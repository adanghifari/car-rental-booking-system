<?php

namespace App\Http\Controllers;

use App\Enums\PaymentStatus;
use App\Enums\RentalStatus;
use App\Enums\VerificationStatus;
use App\Http\Responses\ApiResponse;
use App\Models\PaymentHistory;
use App\Models\Rental;
use App\Services\MidtransService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    public function create(Request $request, MidtransService $midtrans): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'rental_id' => ['required', 'integer', 'exists:rentals,id'],
        ]);

        if ($validator->fails()) {
            return ApiResponse::validation($validator->errors()->toArray());
        }

        $user = $request->user();

        if (! $user) {
            return ApiResponse::unauthorized('Unauthenticated.');
        }

        $rental = Rental::with(['car', 'user'])->find($validator->validated()['rental_id']);

        if (! $rental || $rental->user_id !== $user->id) {
            return ApiResponse::notFound('Rental not found.');
        }

        if ($rental->status !== RentalStatus::PREPAID) {
            return ApiResponse::error('Rental is not in prepaid status.', 409);
        }

        if ($rental->prepaid_expires_at && $rental->prepaid_expires_at->isPast()) {
            return ApiResponse::error('Rental has expired.', 409);
        }

        if ($rental->verification_status !== \App\Enums\VerificationStatus::VERIFIED) {
            return ApiResponse::error('Verification is required before payment.', 409);
        }

        $orderId = 'rental-'.$rental->id.'-'.now()->format('YmdHis');
        $midtransResponse = $midtrans->createTransaction($rental, $orderId);

        $payment = PaymentHistory::create([
            'rental_id' => $rental->id,
            'amount' => $rental->total_price,
            'status' => PaymentStatus::PENDING,
            'provider' => 'midtrans',
            'provider_order_id' => $orderId,
            'snap_token' => $midtransResponse['token'] ?? null,
            'redirect_url' => $midtransResponse['redirect_url'] ?? null,
            'payload' => $midtransResponse,
        ]);

        app(\App\Services\CustomerNotificationService::class)->notifyPaymentAvailable($rental);

        return ApiResponse::created([
            'payment' => $payment,
            'snap_token' => $payment->snap_token,
            'redirect_url' => $payment->redirect_url,
        ], 'Payment initialized.');
    }

    public function changePaymentMethod(Request $request, Rental $rental, MidtransService $midtrans): RedirectResponse
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        if ($rental->user_id !== $user->id) {
            abort(403);
        }

        if ($rental->status === RentalStatus::EXPIRED) {
            return redirect()
                ->route('booking.detail', ['rental' => $rental->id])
                ->with('error', 'Waktu pembayaran telah habis. Booking dibatalkan.');
        }

        if ($rental->status !== RentalStatus::PREPAID) {
            return back()->with('error', 'Metode pembayaran hanya dapat diganti saat status pembayaran masih menunggu.');
        }

        if ($rental->verification_status !== VerificationStatus::VERIFIED) {
            return back()->with('error', 'Verifikasi data penyewa belum disetujui.');
        }

        if ($rental->prepaid_expires_at && $rental->prepaid_expires_at->isPast()) {
            return $this->expirePrepaidRental($rental);
        }

        $latestPayment = $rental->paymentHistories()
            ->where('status', PaymentStatus::PENDING)
            ->latest('id')
            ->first();

        if (! $latestPayment || (! $latestPayment->snap_token && ! $latestPayment->redirect_url)) {
            return back()->with('error', 'Tidak ada sesi pembayaran yang dapat diganti.');
        }

        $orderId = $this->generateProviderOrderId($rental);
        $midtransResponse = $midtrans->createTransaction($rental->loadMissing(['user', 'car']), $orderId);

        $payment = DB::transaction(function () use ($rental, $latestPayment, $midtransResponse, $orderId) {
            $rental->paymentHistories()
                ->where('status', PaymentStatus::PENDING)
                ->update(['status' => PaymentStatus::CANCELLED->value]);

            return PaymentHistory::create([
                'rental_id' => $rental->id,
                'amount' => $rental->total_price,
                'status' => PaymentStatus::PENDING,
                'provider' => 'midtrans',
                'provider_order_id' => $orderId,
                'snap_token' => $midtransResponse['token'] ?? null,
                'redirect_url' => $midtransResponse['redirect_url'] ?? null,
                'payload' => array_merge($midtransResponse, [
                    'replaced_payment_id' => $latestPayment->id,
                ]),
            ]);
        });

        app(\App\Services\CustomerNotificationService::class)->notifyPaymentAvailable($rental);

        $redirectUrl = $payment->redirect_url ?: route('booking.detail', ['rental' => $rental->id]);

        return redirect()->away($redirectUrl);
    }

    public function webhook(Request $request, MidtransService $midtrans): JsonResponse
    {
        $payload = $request->all();

        if (! $midtrans->verifySignature($payload)) {
            return ApiResponse::unauthorized('Invalid signature.');
        }

        $orderId = $payload['order_id'] ?? null;
        $transactionStatus = $payload['transaction_status'] ?? null;

        if (! $orderId || ! $transactionStatus) {
            return ApiResponse::error('Invalid payload.', 422);
        }

        $payment = PaymentHistory::where('provider_order_id', $orderId)->first();

        if (! $payment) {
            return ApiResponse::notFound('Payment not found.');
        }

        if (in_array($payment->status, [PaymentStatus::CANCELLED, PaymentStatus::EXPIRED], true)) {
            return ApiResponse::success(null, 'Payment ignored.');
        }

        $latestActivePayment = PaymentHistory::query()
            ->where('rental_id', $payment->rental_id)
            ->where('status', PaymentStatus::PENDING)
            ->latest('id')
            ->first();

        if (! $latestActivePayment || $latestActivePayment->id !== $payment->id) {
            return ApiResponse::success(null, 'Stale payment ignored.');
        }

        return DB::transaction(function () use ($payment, $transactionStatus, $payload) {
            $paymentStatus = match ($transactionStatus) {
                'settlement', 'capture' => PaymentStatus::PAID,
                'pending' => PaymentStatus::PENDING,
                'expire' => PaymentStatus::EXPIRED,
                'deny', 'cancel', 'failure' => PaymentStatus::CANCELLED,
                default => PaymentStatus::PENDING,
            };

            $payment->status = $paymentStatus;
            $payment->payload = $payload;
            $payment->save();

            $rental = $payment->rental;

            if ($rental) {
                if ($paymentStatus === PaymentStatus::PAID) {
                    $rental->status = RentalStatus::ONGOING;
                    $rental->save();

                    app(\App\Services\CustomerNotificationService::class)->notifyPaymentPaid($rental);
                } elseif ($paymentStatus === PaymentStatus::EXPIRED) {
                    $this->expireRental($rental);
                    app(\App\Services\CustomerNotificationService::class)->notifyPaymentExpired($rental);
                } elseif ($paymentStatus === PaymentStatus::CANCELLED) {
                    $this->cancelRental($rental);
                    app(\App\Services\CustomerNotificationService::class)->notifyPaymentCancelled($rental);
                }
            }

            return ApiResponse::success(null, 'Webhook processed.');
        });
    }

    private function expirePrepaidRental(Rental $rental): RedirectResponse
    {
        DB::transaction(function () use ($rental) {
            $rental->paymentHistories()
                ->where('status', PaymentStatus::PENDING)
                ->update(['status' => PaymentStatus::EXPIRED->value]);

            $this->expireRental($rental);
        });

        app(\App\Services\CustomerNotificationService::class)->notifyPaymentExpired($rental);

        return redirect()
            ->route('booking.detail', ['rental' => $rental->id])
            ->with('error', 'Waktu pembayaran telah habis. Booking dibatalkan.');
    }

    private function expireRental(Rental $rental): void
    {
        $rental->status = RentalStatus::EXPIRED;
        $rental->prepaid_expires_at = null;
        $this->releaseIdentityFiles($rental);
        $rental->save();
    }

    private function cancelRental(Rental $rental): void
    {
        $rental->status = RentalStatus::CANCELLED;
        $rental->prepaid_expires_at = null;
        $this->releaseIdentityFiles($rental);
        $rental->save();
    }

    private function releaseIdentityFiles(Rental $rental): void
    {
        if ($rental->ktp_path) {
            Storage::disk('local')->delete($rental->ktp_path);
        }

        if ($rental->selfie_path) {
            Storage::disk('local')->delete($rental->selfie_path);
        }

        $rental->ktp_path = '';
        $rental->selfie_path = '';
    }

    private function generateProviderOrderId(Rental $rental): string
    {
        return sprintf(
            'RENTAL-%d-PAY-%s',
            $rental->id,
            Str::upper((string) Str::ulid())
        );
    }

    public static function syncUserPendingRentals(\App\Models\User $user): void
    {
        $midtrans = app(MidtransService::class);
        $prepaidRentals = Rental::where('user_id', $user->id)
            ->where('status', RentalStatus::PREPAID)
            ->get();

        if ($prepaidRentals->isEmpty()) {
            return;
        }

        $instance = new self();

        foreach ($prepaidRentals as $rental) {
            $latestPayment = $rental->paymentHistories()->latest()->first();
            if ($latestPayment && $latestPayment->status === PaymentStatus::PENDING && $latestPayment->provider_order_id) {
                try {
                    $status = $midtrans->getTransactionStatus($latestPayment->provider_order_id);
                    if ($status === 'settlement' || $status === 'capture') {
                        DB::transaction(function () use ($rental, $latestPayment) {
                            $latestPayment->status = PaymentStatus::PAID;
                            $latestPayment->save();

                            $rental->status = RentalStatus::ONGOING;
                            $rental->save();
                        });
                        app(\App\Services\CustomerNotificationService::class)->notifyPaymentPaid($rental);
                    } elseif ($status === 'expire') {
                        DB::transaction(function () use ($rental, $latestPayment, $instance) {
                            $latestPayment->status = PaymentStatus::EXPIRED;
                            $latestPayment->save();

                            $instance->expireRental($rental);
                        });
                        app(\App\Services\CustomerNotificationService::class)->notifyPaymentExpired($rental);
                    } elseif (in_array($status, ['deny', 'cancel', 'failure'])) {
                        DB::transaction(function () use ($rental, $latestPayment, $instance) {
                            $latestPayment->status = PaymentStatus::CANCELLED;
                            $latestPayment->save();

                            $instance->cancelRental($rental);
                        });
                        app(\App\Services\CustomerNotificationService::class)->notifyPaymentCancelled($rental);
                    }
                } catch (\Exception $e) {
                    // Ignore API failures
                }
            }
        }
    }

    public function index(Request $request)
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        // Sync pending rentals first to ensure accuracy
        self::syncUserPendingRentals($user);

        // Build the query
        $query = PaymentHistory::whereHas('rental', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })->with(['rental.car']);

        // Apply search query
        if ($request->filled('q')) {
            $search = $request->input('q');
            $query->where(function ($q) use ($search) {
                $q->where('provider_order_id', 'like', "%{$search}%")
                  ->orWhereHas('rental.car', function ($carQuery) use ($search) {
                      $carQuery->where('name', 'like', "%{$search}%")
                               ->orWhere('brand', 'like', "%{$search}%");
                  });
            });
        }

        // Apply status filter
        if ($request->filled('status')) {
            $status = $request->input('status');
            if (in_array($status, ['pending', 'paid', 'cancelled', 'expired'])) {
                $query->where('status', $status);
            }
        }

        // Compute statistics for the user
        $statsQuery = PaymentHistory::whereHas('rental', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        });

        $totalSpend = (clone $statsQuery)->where('status', PaymentStatus::PAID)->sum('amount');
        $pendingCount = (clone $statsQuery)->where('status', PaymentStatus::PENDING)->count();
        $totalCount = (clone $statsQuery)->count();

        // Paginate results (9 per page for a 3x3 layout)
        $payments = $query->orderBy('created_at', 'desc')->paginate(9)->withQueryString();

        return view('frontliner.pages.pembayaran', [
            'payments' => $payments,
            'totalSpend' => $totalSpend,
            'pendingCount' => $pendingCount,
            'totalCount' => $totalCount,
        ]);
    }
}
