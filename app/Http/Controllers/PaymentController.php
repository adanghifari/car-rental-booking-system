<?php

namespace App\Http\Controllers;

use App\Enums\CarStatus;
use App\Enums\PaymentStatus;
use App\Enums\RentalStatus;
use App\Http\Responses\ApiResponse;
use App\Models\PaymentHistory;
use App\Models\Rental;
use App\Services\MidtransService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

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

        if (! $rental->verification_passed) {
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

        return ApiResponse::created([
            'payment' => $payment,
            'snap_token' => $payment->snap_token,
            'redirect_url' => $payment->redirect_url,
        ], 'Payment initialized.');
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

        return DB::transaction(function () use ($payment, $transactionStatus, $payload) {
            $paymentStatus = match ($transactionStatus) {
                'settlement', 'capture' => PaymentStatus::PAID,
                'pending' => PaymentStatus::PENDING,
                'deny', 'cancel', 'expire', 'failure' => PaymentStatus::CANCELLED,
                default => PaymentStatus::PENDING,
            };

            $payment->status = $paymentStatus;
            $payment->payload = $payload;
            $payment->save();

            $rental = $payment->rental;

            if ($rental && $paymentStatus === PaymentStatus::PAID) {
                $rental->status = RentalStatus::ONGOING;
                $rental->save();

                $car = $rental->car;
                if ($car) {
                    $car->status = CarStatus::UNAVAILABLE;
                    $car->save();
                }
            }

            return ApiResponse::success(null, 'Webhook processed.');
        });
    }
}
