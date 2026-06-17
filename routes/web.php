<?php

use App\Http\Controllers\BackofficeController;
use App\Http\Controllers\BackofficeNotificationController;
use App\Http\Controllers\CustomerNotificationController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\CustomerAccountController;
use App\Http\Controllers\PaymentController;
use Illuminate\Http\RedirectResponse;
use App\Models\User;
use App\Models\Car;
use App\Models\Rental;
use App\Enums\CarStatus;
use App\Enums\RentalStatus;
use App\Enums\VehicleType;
use App\Support\BookingAvailability;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ViewErrorBag;
use Barryvdh\DomPDF\Facade\Pdf;

if (! function_exists('booking_active_rental_statuses')) {
    function booking_active_rental_statuses(): array
    {
        return BookingAvailability::activeRentalStatuses();
    }
}

if (! function_exists('booking_rental_has_overlap')) {
    function booking_rental_has_overlap(int $carId, string $startDate, string $endDate, ?int $ignoreRentalId = null): bool
    {
        $car = Car::query()->find($carId);

        if (! $car) {
            return false;
        }

        $result = BookingAvailability::checkCarAvailability($car, $startDate, $endDate, $ignoreRentalId);

        return ! $result['available'] && in_array($result['reason'], ['overlap', 'post_buffer'], true);
    }
}

if (! function_exists('booking_car_availability_result')) {
    function booking_car_availability_result(Car $car, string $startDate, string $endDate, ?int $ignoreRentalId = null): array
    {
        return BookingAvailability::checkCarAvailability($car, $startDate, $endDate, $ignoreRentalId);
    }
}

if (! function_exists('booking_car_is_listable_for_date')) {
    function booking_car_is_listable_for_date(Car $car, string $date): bool
    {
        return BookingAvailability::checkCarAvailability($car, $date, $date)['available'];
    }
}

if (! function_exists('booking_unavailability_message')) {
    function booking_unavailability_message(string $reason): string
    {
        return BookingAvailability::unavailabilityMessage($reason);
    }
}

if (! function_exists('booking_release_identity_files')) {
    function booking_release_identity_files(Rental $rental): void
    {
        if ($rental->ktp_path) {
            \Illuminate\Support\Facades\Storage::disk('local')->delete($rental->ktp_path);
        }

        if ($rental->selfie_path) {
            \Illuminate\Support\Facades\Storage::disk('local')->delete($rental->selfie_path);
        }

        $rental->ktp_path = '';
        $rental->selfie_path = '';
    }
}

Route::get('/', function (Request $request) {
    $user = $request->user();

    if ($user?->role === User::ROLE_ADMIN) {
        return redirect()->route('dashboard');
    }

    if ($user) {
        return redirect()->route('frontliner');
    }

    $query = Car::query()->withReviewMetrics()->where('status', CarStatus::AVAILABLE);
    $startDate = $request->input('start_date');

    if ($request->filled('max_price')) {
        $maxPrice = (int) $request->input('max_price');
        if ($maxPrice > 0) {
            $query->where('daily_rate', '<=', $maxPrice);
        }
    }

    $cars = $query->get()
        ->when($request->filled('start_date'), fn ($cars) => $cars->filter(
            fn (Car $car) => booking_car_is_listable_for_date($car, $startDate)
        ))
        ->take(3)
        ->values();
    $featuredCars = Car::query()
        ->withReviewMetrics()
        ->withCount('rentals')
        ->withCount([
            'rentals as active_rentals_count' => fn ($rentalQuery) => $rentalQuery
                ->whereIn('status', booking_active_rental_statuses()),
        ])
        ->orderByDesc('rentals_count')
        ->latest()
        ->limit(3)
        ->get();
    $reviews = \App\Models\Review::with(['user', 'car'])
        ->latest()
        ->orderByDesc('rating')
        ->limit(3)
        ->get();

    return view('frontliner.pages.beranda-non-login', [
        'cars' => $cars,
        'featuredCars' => $featuredCars,
        'reviews' => $reviews,
    ]);
})->middleware('token.cookie')->name('home');

Route::get('/beranda', function (Request $request) {
    $user = $request->user();

    if ($user?->role === User::ROLE_ADMIN) {
        return redirect()->route('dashboard');
    }

    if ($user) {
        return redirect()->route('frontliner');
    }

    $query = Car::query()->withReviewMetrics()->where('status', CarStatus::AVAILABLE);
    $startDate = $request->input('start_date');

    if ($request->filled('max_price')) {
        $maxPrice = (int) $request->input('max_price');
        if ($maxPrice > 0) {
            $query->where('daily_rate', '<=', $maxPrice);
        }
    }

    $cars = $query->get()
        ->when($request->filled('start_date'), fn ($cars) => $cars->filter(
            fn (Car $car) => booking_car_is_listable_for_date($car, $startDate)
        ))
        ->take(3)
        ->values();
    $featuredCars = Car::query()
        ->withReviewMetrics()
        ->withCount('rentals')
        ->withCount([
            'rentals as active_rentals_count' => fn ($rentalQuery) => $rentalQuery
                ->whereIn('status', booking_active_rental_statuses()),
        ])
        ->orderByDesc('rentals_count')
        ->latest()
        ->limit(3)
        ->get();
    $reviews = \App\Models\Review::with(['user', 'car'])
        ->latest()
        ->orderByDesc('rating')
        ->limit(3)
        ->get();

    return view('frontliner.pages.beranda-non-login', [
        'cars' => $cars,
        'featuredCars' => $featuredCars,
        'reviews' => $reviews,
    ]);
})->middleware('token.cookie')->name('beranda');

Route::get('/frontliner', function (Request $request) {
    $user = $request->user();

    if ($user?->role === User::ROLE_ADMIN) {
        return redirect()->route('dashboard');
    }

    $query = Car::query()->withReviewMetrics()->where('status', CarStatus::AVAILABLE);
    $startDate = $request->input('start_date');

    if ($request->filled('max_price')) {
        $maxPrice = (int) $request->input('max_price');
        if ($maxPrice > 0) {
            $query->where('daily_rate', '<=', $maxPrice);
        }
    }

    $cars = $query->get()
        ->when($request->filled('start_date'), fn ($cars) => $cars->filter(
            fn (Car $car) => booking_car_is_listable_for_date($car, $startDate)
        ))
        ->take(3)
        ->values();

    $rentals = Rental::with(['car'])
        ->where('user_id', $user->id)
        ->orderBy('created_at', 'desc')
        ->get();

    // Real-time Midtrans status sync on frontliner page load
    $midtrans = app(\App\Services\MidtransService::class);
    foreach ($rentals as $rental) {
        if ($rental->status === RentalStatus::PREPAID) {
            $latestPayment = $rental->paymentHistories()->latest()->first();
            if ($latestPayment && $latestPayment->provider_order_id) {
                try {
                    $status = $midtrans->getTransactionStatus($latestPayment->provider_order_id);
                    if ($status === 'settlement' || $status === 'capture') {
                        DB::transaction(function () use ($rental, $latestPayment) {
                            $latestPayment->status = \App\Enums\PaymentStatus::PAID;
                            $latestPayment->save();

                            $rental->status = RentalStatus::ONGOING;
                            $rental->save();
                        });
                        app(\App\Services\CustomerNotificationService::class)->notifyPaymentPaid($rental);
                        $rental->refresh();
                    } elseif ($status === 'expire') {
                        DB::transaction(function () use ($rental, $latestPayment) {
                            $latestPayment->status = \App\Enums\PaymentStatus::EXPIRED;
                            $latestPayment->save();

                            $rental->status = RentalStatus::EXPIRED;
                            $rental->save();
                        });
                        app(\App\Services\CustomerNotificationService::class)->notifyPaymentExpired($rental);
                        $rental->refresh();
                    } elseif (in_array($status, ['deny', 'cancel', 'failure'])) {
                        DB::transaction(function () use ($rental, $latestPayment) {
                            $latestPayment->status = \App\Enums\PaymentStatus::CANCELLED;
                            $latestPayment->save();

                            $rental->status = RentalStatus::CANCELLED;
                            $rental->save();
                        });
                        app(\App\Services\CustomerNotificationService::class)->notifyPaymentCancelled($rental);
                        $rental->refresh();
                    }
                } catch (\Exception $e) {
                    // Ignore API failures and proceed with stored DB status
                }
            }
        }
    }

    $reviews = \App\Models\Review::with(['user', 'car'])->latest()->limit(3)->get();

    return view('frontliner.pages.beranda-login', [
        'user' => $user,
        'cars' => $cars,
        'rentals' => $rentals,
        'reviews' => $reviews,
    ]);
})->middleware('auth')->name('frontliner');


Route::get('/booking/start', function (Request $request) {
    $user = $request->user();

    if (! $user) {
        return redirect()->route('login', [
            'redirect' => $request->getRequestUri(),
        ]);
    }

    if ($user->role === User::ROLE_ADMIN) {
        return redirect()->route('dashboard');
    }

    $carId = $request->input('car_id');
    $startDate = $request->input('start_date');
    $endDate = $request->input('end_date');
    $serviceType = $request->input('service_type', 'self_drive');

    if (! $carId || ! $startDate || ! $endDate) {
        return redirect()->route('frontliner')->with('error', 'Detail pemesanan tidak lengkap.');
    }

    $validator = Validator::make($request->all(), [
        'car_id' => 'required|integer|exists:cars,id',
        'start_date' => 'required|date',
        'end_date' => 'required|date|after_or_equal:start_date',
        'service_type' => 'required|string',
    ]);

    if ($validator->fails()) {
        return redirect()
            ->route('car-detail', $carId)
            ->withInput()
            ->with('error', 'Tanggal sewa tidak valid. Tanggal selesai harus sama atau setelah tanggal mulai.');
    }

    $car = Car::findOrFail($carId);

    $availability = booking_car_availability_result($car, $startDate, $endDate);
    if (! $availability['available']) {
        return redirect()->route('car-detail', $carId)
            ->with('error', booking_unavailability_message($availability['reason'] ?? 'overlap'));
    }

    // Calculate dates & price
    $start = \Carbon\Carbon::parse($startDate);
    $end = \Carbon\Carbon::parse($endDate);
    $days = max(1, $start->diffInDays($end));

    $rentCost = $car->daily_rate * $days;
    $driverCost = ($serviceType === 'with_driver') ? 150000 * $days : 0;
    $serviceCost = 100000 + $driverCost;
    $totalPrice = $rentCost + $serviceCost;

    return view('frontliner.pages.booking-confirm', [
        'car' => $car,
        'start_date' => $startDate,
        'end_date' => $endDate,
        'service_type' => $serviceType,
        'days' => $days,
        'rentCost' => $rentCost,
        'driverCost' => $driverCost,
        'serviceCost' => $serviceCost,
        'totalPrice' => $totalPrice,
    ]);
})->middleware('token.cookie')->name('booking.start');

Route::post('/booking/identity', function (Request $request) {
    $request->validate([
        'car_id' => 'required|integer|exists:cars,id',
        'start_date' => 'required|date',
        'end_date' => 'required|date|after_or_equal:start_date',
        'service_type' => 'required|string',
    ]);

    $carId = $request->input('car_id');
    $startDate = $request->input('start_date');
    $endDate = $request->input('end_date');
    $serviceType = $request->input('service_type');
    $car = Car::findOrFail($carId);

    $availability = booking_car_availability_result($car, $startDate, $endDate);
    if (! $availability['available']) {
        return redirect()->route('car-detail', $carId)
            ->with('error', booking_unavailability_message($availability['reason'] ?? 'overlap'));
    }

    $start = \Carbon\Carbon::parse($startDate);
    $end = \Carbon\Carbon::parse($endDate);
    $days = max(1, $start->diffInDays($end));

    $rentCost = $car->daily_rate * $days;
    $driverCost = ($serviceType === 'with_driver') ? 150000 * $days : 0;
    $serviceCost = 100000 + $driverCost;
    $totalPrice = $rentCost + $serviceCost;

    return view('frontliner.pages.booking-identity', [
        'car' => $car,
        'start_date' => $startDate,
        'end_date' => $endDate,
        'service_type' => $serviceType,
        'days' => $days,
        'rentCost' => $rentCost,
        'driverCost' => $driverCost,
        'serviceCost' => $serviceCost,
        'totalPrice' => $totalPrice,
    ]);
})->middleware(['token.cookie', 'auth'])->name('booking.identity');

Route::get('/booking/identity', function (Request $request) {
    if ($request->has(['car_id', 'start_date', 'end_date', 'service_type'])) {
        $validator = Validator::make($request->all(), [
            'car_id' => 'required|integer|exists:cars,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'service_type' => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()->route('frontliner')->with('error', 'Parameter booking tidak valid.');
        }

        $carId = $request->input('car_id');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $serviceType = $request->input('service_type');
        $car = Car::findOrFail($carId);

        $availability = booking_car_availability_result($car, $startDate, $endDate);
        if (! $availability['available']) {
            return redirect()->route('car-detail', $carId)
                ->with('error', booking_unavailability_message($availability['reason'] ?? 'overlap'));
        }

        $start = \Carbon\Carbon::parse($startDate);
        $end = \Carbon\Carbon::parse($endDate);
        $days = max(1, $start->diffInDays($end));

        $rentCost = $car->daily_rate * $days;
        $driverCost = ($serviceType === 'with_driver') ? 150000 * $days : 0;
        $serviceCost = 100000 + $driverCost;
        $totalPrice = $rentCost + $serviceCost;

        return view('frontliner.pages.booking-identity', [
            'car' => $car,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'service_type' => $serviceType,
            'days' => $days,
            'rentCost' => $rentCost,
            'driverCost' => $driverCost,
            'serviceCost' => $serviceCost,
            'totalPrice' => $totalPrice,
        ]);
    }

    return redirect()->route('frontliner');
})->middleware(['token.cookie', 'auth']);

Route::get('/booking/availability', function (Request $request) {
    $validator = Validator::make($request->all(), [
        'car_id' => 'required|integer|exists:cars,id',
        'start_date' => 'required|date',
        'end_date' => 'required|date|after_or_equal:start_date',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'available' => false,
            'reason' => 'invalid_dates',
            'label' => 'Tanggal tidak valid',
            'message' => 'Tanggal selesai harus sama atau setelah tanggal mulai.',
            'tone' => 'rose',
        ], 422);
    }

    $data = $validator->validated();
    $car = Car::findOrFail($data['car_id']);
    $availability = BookingAvailability::checkCarAvailability($car, $data['start_date'], $data['end_date']);

    if ($availability['available']) {
        return response()->json([
            'available' => true,
            'reason' => null,
            'label' => 'Tersedia pada tanggal ini',
            'message' => 'Mobil siap dipesan untuk rentang tanggal yang dipilih.',
            'tone' => 'emerald',
        ]);
    }

    $reason = $availability['reason'] ?? 'overlap';

    return response()->json([
        'available' => false,
        'reason' => $reason,
        'label' => match ($reason) {
            'operational_unavailable' => 'Tidak tersedia operasional',
            'post_buffer' => 'Masih dalam masa buffer',
            default => 'Tidak tersedia pada tanggal ini',
        },
        'message' => booking_unavailability_message($reason),
        'tone' => $reason === 'operational_unavailable' ? 'amber' : 'rose',
    ], 409);
})->middleware('token.cookie')->name('booking.availability');

Route::post('/booking/submit', function (Request $request, \App\Services\FaceVerificationService $faceVerify, \App\Services\MidtransService $midtrans) {
    $startDate = $request->input('start_date');
    $endDate = $request->input('end_date');
    $serviceType = $request->input('service_type');
    $carId = $request->input('car_id');
    $car = Car::findOrFail($carId);

    $validator = Validator::make($request->all(), [
        'car_id' => 'required|integer|exists:cars,id',
        'start_date' => 'required|date',
        'end_date' => 'required|date|after_or_equal:start_date',
        'service_type' => 'required|string',
        'ktp' => 'required|file|image|min:1|max:5120',
        'selfie' => 'required|file|image|min:1|max:5120',
    ]);

    $start = \Carbon\Carbon::parse($startDate);
    $end = \Carbon\Carbon::parse($endDate);
    $days = max(1, $start->diffInDays($end));
    $rentCost = $car->daily_rate * $days;
    $driverCost = ($serviceType === 'with_driver') ? 150000 * $days : 0;
    $serviceCost = 100000 + $driverCost;
    $totalPrice = $rentCost + $serviceCost;
    $type = ($serviceType === 'with_driver')
        ? \App\Enums\RentalType::WITH_DRIVER
        : \App\Enums\RentalType::SELF_DRIVE;

    $renderIdentityPage = function (array $extraData = [], ?ViewErrorBag $errorBag = null) use ($car, $startDate, $endDate, $serviceType, $days, $rentCost, $driverCost, $serviceCost, $totalPrice) {
        return response()->view('frontliner.pages.booking-identity', array_merge([
            'car' => $car,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'service_type' => $serviceType,
            'days' => $days,
            'rentCost' => $rentCost,
            'driverCost' => $driverCost,
            'serviceCost' => $serviceCost,
            'totalPrice' => $totalPrice,
            'errors' => $errorBag ?? new ViewErrorBag(),
        ], $extraData), 422);
    };

    if ($validator->fails()) {
        $errorBag = new ViewErrorBag();
        $errorBag->put('default', $validator->errors());

        return $renderIdentityPage([], $errorBag);
    }

    $autoVerifyPassed = false;
    try {
        $verification = $faceVerify->verify($request->file('ktp'), $request->file('selfie'));
        $autoVerifyPassed = (bool) ($verification['verified'] ?? false);
    } catch (\Exception $e) {
        \Illuminate\Support\Facades\Log::warning('Face verification service failure: ' . $e->getMessage());
    }

    try {
        $rental = DB::transaction(function () use ($request, $carId, $start, $end, $type, $totalPrice) {
            $car = Car::query()->lockForUpdate()->findOrFail($carId);

            if ($car->status !== CarStatus::AVAILABLE) {
                throw new \RuntimeException(booking_unavailability_message('operational_unavailable'));
            }

            $availability = BookingAvailability::checkCarAvailability(
                $car,
                $start->format('Y-m-d'),
                $end->format('Y-m-d')
            );
            if (! $availability['available']) {
                throw new \RuntimeException(booking_unavailability_message($availability['reason'] ?? 'overlap'));
            }

            $ktpPermanentPath = \Illuminate\Support\Facades\Storage::disk('local')->putFile('ktp', $request->file('ktp'));
            $selfiePermanentPath = \Illuminate\Support\Facades\Storage::disk('local')->putFile('selfie', $request->file('selfie'));

            $rental = Rental::create([
                'user_id' => auth()->id(),
                'car_id' => $car->id,
                'start_date' => $start,
                'end_date' => $end,
                'total_price' => $totalPrice,
                'status' => RentalStatus::PENDING_VERIFICATION,
                'type' => $type,
                'prepaid_expires_at' => null,
                'ktp_path' => $ktpPermanentPath,
                'selfie_path' => $selfiePermanentPath,
                'verification_passed' => false,
                'verified_at' => null,
                'verification_status' => \App\Enums\VerificationStatus::PENDING,
                'buffer_before_days' => BookingAvailability::DEFAULT_BUFFER_BEFORE_DAYS,
                'buffer_after_days' => BookingAvailability::DEFAULT_BUFFER_AFTER_DAYS,
            ]);

            app(\App\Services\CustomerNotificationService::class)->notifyBookingVerificationStarted($rental);

            return $rental;
        });
    } catch (\Throwable $e) {
        return $renderIdentityPage([
            'error_message' => 'Gagal memproses verifikasi: ' . $e->getMessage(),
        ]);
    }

    app(\App\Services\CustomerNotificationService::class)->notifyVerificationSubmitted($rental);

    if ($autoVerifyPassed) {
        DB::transaction(function () use ($rental) {
            $rental->status = RentalStatus::PREPAID;
            $rental->verification_status = \App\Enums\VerificationStatus::VERIFIED;
            $rental->verification_passed = true;
            $rental->verified_at = now();
            $rental->prepaid_expires_at = now()->addHours(4);
            $rental->save();
        });

        app(\App\Services\CustomerNotificationService::class)->notifyVerificationApproved($rental);

        try {
            $orderId = 'rental-' . $rental->id . '-' . now()->format('YmdHis');
            $midtransResponse = $midtrans->createTransaction($rental, $orderId);

            \App\Models\PaymentHistory::create([
                'rental_id' => $rental->id,
                'amount' => $rental->total_price,
                'status' => \App\Enums\PaymentStatus::PENDING,
                'provider' => 'midtrans',
                'provider_order_id' => $orderId,
                'snap_token' => $midtransResponse['token'] ?? null,
                'redirect_url' => $midtransResponse['redirect_url'] ?? null,
                'payload' => $midtransResponse,
            ]);

            app(\App\Services\CustomerNotificationService::class)->notifyPaymentAvailable($rental);
        } catch (\Throwable $e) {
            return redirect()->route('booking.detail', ['rental' => $rental->id])
                ->with('error', 'Verifikasi selesai, tetapi inisialisasi pembayaran gagal: ' . $e->getMessage());
        }
    } else {
        DB::transaction(function () use ($rental) {
            $rental->verification_status = \App\Enums\VerificationStatus::NEEDS_REVIEW;
            $rental->prepaid_expires_at = null;
            $rental->save();
        });

        app(\App\Services\CustomerNotificationService::class)->notifyVerificationNeedsReview($rental);
    }

    return redirect()->route('booking.detail', ['rental' => $rental->id]);
})->middleware(['token.cookie', 'auth'])->name('booking.submit');

Route::post('/booking/detail/{rental}/pay', function (Rental $rental, \App\Services\MidtransService $midtrans) {
    $user = auth()->user();
    if (!$user || $rental->user_id !== $user->id) {
        abort(403);
    }

    if ($rental->verification_status !== \App\Enums\VerificationStatus::VERIFIED) {
        return back()->with('error', 'Verifikasi data penyewa belum disetujui.');
    }

    if ($rental->status !== RentalStatus::PENDING_VERIFICATION) {
        return back()->with('error', 'Status reservasi tidak sesuai.');
    }

    // Check if 4 hours has passed since verified_at
    if ($rental->verified_at && $rental->verified_at->addHours(4)->isPast()) {
        DB::transaction(function () use ($rental) {
            $rental->status = RentalStatus::EXPIRED;
            $rental->prepaid_expires_at = null;
            booking_release_identity_files($rental);
            $rental->save();
        });
        return back()->with('error', 'Batas waktu pembayaran telah habis.');
    }

    try {
        $orderId = 'rental-' . $rental->id . '-' . now()->format('YmdHis');
        $midtransResponse = $midtrans->createTransaction($rental, $orderId);

        DB::transaction(function () use ($rental, $orderId, $midtransResponse) {
            $rental->status = RentalStatus::PREPAID;
            // Limit countdown to exactly remaining of verified_at + 4 hours
            $rental->prepaid_expires_at = $rental->verified_at->addHours(4);
            $rental->save();

            \App\Models\PaymentHistory::create([
                'rental_id' => $rental->id,
                'amount' => $rental->total_price,
                'status' => \App\Enums\PaymentStatus::PENDING,
                'provider' => 'midtrans',
                'provider_order_id' => $orderId,
                'snap_token' => $midtransResponse['token'] ?? null,
                'redirect_url' => $midtransResponse['redirect_url'] ?? null,
                'payload' => $midtransResponse,
            ]);
        });

        app(\App\Services\CustomerNotificationService::class)->notifyPaymentAvailable($rental);

        return redirect()->route('booking.detail', ['rental' => $rental->id]);
    } catch (\Exception $e) {
        return back()->with('error', 'Gagal memulai gerbang pembayaran: ' . $e->getMessage());
    }
})->middleware(['token.cookie', 'auth'])->name('booking.pay');

Route::post('/booking/detail/{rental}/cancel', function (Rental $rental) {
    $user = auth()->user();
    if (!$user || $rental->user_id !== $user->id) {
        abort(403);
    }

    if ($rental->status !== RentalStatus::PENDING_VERIFICATION && $rental->status !== RentalStatus::PREPAID) {
        return back()->with('error', 'Pemesanan tidak dapat dibatalkan pada tahap ini.');
    }

    DB::transaction(function () use ($rental) {
        $rental->status = RentalStatus::CANCELLED;
        $rental->verification_status = \App\Enums\VerificationStatus::CANCELLED;
        $rental->prepaid_expires_at = null;
        booking_release_identity_files($rental);
        $rental->save();

        // Delete uploaded files
        if ($rental->ktp_path) {
            \Illuminate\Support\Facades\Storage::disk('local')->delete($rental->ktp_path);
        }
        if ($rental->selfie_path) {
            \Illuminate\Support\Facades\Storage::disk('local')->delete($rental->selfie_path);
        }

        // Cancel payments
        $latestPayment = $rental->paymentHistories()->latest()->first();
        if ($latestPayment) {
            $latestPayment->status = \App\Enums\PaymentStatus::CANCELLED;
            $latestPayment->save();
        }
    });

    app(\App\Services\CustomerNotificationService::class)->notifyRentalCancelled($rental);

    return redirect()->route('pesanan-saya')->with('success', 'Pemesanan Anda berhasil dibatalkan.');
})->middleware(['token.cookie', 'auth'])->name('booking.cancel');

Route::post('/booking/detail/{rental}/change-payment-method', [PaymentController::class, 'changePaymentMethod'])
    ->middleware(['token.cookie', 'auth'])
    ->name('booking.change-payment-method');

Route::post('/dashboard/reservations/{rental}/verify', function (Request $request, Rental $rental) {
    $user = auth()->user();
    if (!$user || $user->role !== User::ROLE_ADMIN) {
        abort(403);
    }

    $action = $request->input('action');

    if ($rental->status !== RentalStatus::PENDING_VERIFICATION || $rental->verification_status !== \App\Enums\VerificationStatus::NEEDS_REVIEW) {
        return back()->with('error', 'Reservasi tidak dalam status membutuhkan verifikasi.');
    }

    if ($action === 'approve') {
        DB::transaction(function () use ($rental) {
            $rental->verification_status = \App\Enums\VerificationStatus::VERIFIED;
            $rental->verified_at = now();
            $rental->verification_passed = true;
            $rental->save();
        });

        app(\App\Services\CustomerNotificationService::class)->notifyVerificationApproved($rental);

        return back()->with('success', 'Verifikasi identitas disetujui. Customer dipersilakan melanjutkan pembayaran.');
    } elseif ($action === 'reject') {
        DB::transaction(function () use ($rental) {
            $rental->verification_status = \App\Enums\VerificationStatus::REJECTED;
            $rental->status = RentalStatus::CANCELLED;
            $rental->prepaid_expires_at = null;
            booking_release_identity_files($rental);
            $rental->save();

            if ($rental->ktp_path) {
                \Illuminate\Support\Facades\Storage::disk('local')->delete($rental->ktp_path);
            }
            if ($rental->selfie_path) {
                \Illuminate\Support\Facades\Storage::disk('local')->delete($rental->selfie_path);
            }

            $latestPayment = $rental->paymentHistories()->latest()->first();
        if ($latestPayment) {
            $latestPayment->status = \App\Enums\PaymentStatus::CANCELLED;
            $latestPayment->save();
        }
    });

    app(\App\Services\CustomerNotificationService::class)->notifyVerificationRejected($rental);

        return back()->with('success', 'Verifikasi identitas ditolak. Pemesanan dibatalkan.');
    }

    return back()->with('error', 'Aksi tidak dikenal.');
})->middleware(['auth', 'admin'])->name('backoffice.reservations.verify');

Route::post('/dashboard/reservations/{rental}/return', function (Rental $rental) {
    $user = auth()->user();
    if (!$user || $user->role !== \App\Models\User::ROLE_ADMIN) {
        abort(403);
    }

    if ($rental->status !== RentalStatus::ONGOING) {
        return back()->with('error', 'Reservasi tidak sedang aktif.');
    }

    DB::transaction(function () use ($rental) {
        $rental->status = RentalStatus::RETURNED;
        $rental->returned_at = now();
        $rental->post_buffer_released_at = null;
        $rental->post_buffer_released_by = null;
        $rental->save();
    });

    app(\App\Services\CustomerNotificationService::class)->notifyRentalReturned($rental);

    return back()->with('success', 'Mobil berhasil dikembalikan. Pemesanan selesai.');
})->middleware(['auth', 'admin'])->name('backoffice.reservations.return');

Route::post('/dashboard/reservations/{rental}/release-post-buffer', function (Rental $rental) {
    $user = auth()->user();
    if (! $user || $user->role !== User::ROLE_ADMIN) {
        abort(403);
    }

    if ($rental->status !== RentalStatus::RETURNED) {
        return back()->with('error', 'Buffer hanya dapat dilepas untuk rental yang sudah selesai.');
    }

    if (! BookingAvailability::hasActivePostBuffer($rental)) {
        return back()->with('warning', 'Masa buffer setelah rental sudah tidak aktif.');
    }

    DB::transaction(function () use ($rental, $user) {
        $rental->post_buffer_released_at = now();
        $rental->post_buffer_released_by = $user->id;
        $rental->save();
    });

    return back()->with('success', 'Buffer setelah rental berhasil dilepas.');
})->middleware(['auth', 'admin'])->name('backoffice.reservations.release-post-buffer');

Route::post('/dashboard/reservations/{rental}/cancel', function (Rental $rental) {
    $user = auth()->user();
    if (!$user || $user->role !== User::ROLE_ADMIN) {
        abort(403);
    }

    if ($rental->status === RentalStatus::RETURNED || $rental->status === RentalStatus::ONGOING || $rental->status === RentalStatus::CANCELLED || $rental->status === RentalStatus::EXPIRED) {
        return back()->with('error', 'Reservasi tidak dapat dibatalkan pada tahap ini.');
    }

    DB::transaction(function () use ($rental) {
        $rental->status = RentalStatus::CANCELLED;
        $rental->verification_status = \App\Enums\VerificationStatus::CANCELLED;
        $rental->prepaid_expires_at = null;
        booking_release_identity_files($rental);
        $rental->save();

        // Delete uploaded files
        if ($rental->ktp_path) {
            \Illuminate\Support\Facades\Storage::disk('local')->delete($rental->ktp_path);
        }
        if ($rental->selfie_path) {
            \Illuminate\Support\Facades\Storage::disk('local')->delete($rental->selfie_path);
        }

        // Cancel payments
        $latestPayment = $rental->paymentHistories()->latest()->first();
        if ($latestPayment) {
            $latestPayment->status = \App\Enums\PaymentStatus::CANCELLED;
            $latestPayment->save();
        }
    });

    return back()->with('success', 'Reservasi berhasil dibatalkan oleh Admin.');
})->middleware(['auth', 'admin'])->name('backoffice.reservations.cancel');

Route::get('/booking/detail/{rental}', function (Rental $rental, \App\Services\MidtransService $midtrans) {
    $user = auth()->user();
    if (!$user || ($rental->user_id !== $user->id && $user->role !== User::ROLE_ADMIN)) {
        abort(403);
    }

    $latestPayment = $rental->paymentHistories()->latest()->first();

    // Direct Midtrans status check if still prepaid and we have a transaction
    if ($rental->status === RentalStatus::PREPAID && $latestPayment && $latestPayment->status === \App\Enums\PaymentStatus::PENDING && $latestPayment->provider_order_id) {
        try {
            $details = $midtrans->getTransactionDetails($latestPayment->provider_order_id);
            if ($details) {
                // Update local payload in DB so we know chosen payment method details
                $latestPayment->payload = array_merge($latestPayment->payload ?? [], $details);
                $latestPayment->save();

                $status = $details['transaction_status'] ?? null;
                if ($status === 'settlement' || $status === 'capture') {
                    DB::transaction(function () use ($rental, $latestPayment) {
                        $latestPayment->status = \App\Enums\PaymentStatus::PAID;
                        $latestPayment->save();

                        $rental->status = RentalStatus::ONGOING;
                        $rental->save();
                    });

                    app(\App\Services\CustomerNotificationService::class)->notifyPaymentPaid($rental);

                    // Reload relations
                    $rental->refresh();
                    $latestPayment = $rental->paymentHistories()->latest()->first();
                } elseif ($status === 'expire') {
                    DB::transaction(function () use ($rental, $latestPayment) {
                        $latestPayment->status = \App\Enums\PaymentStatus::EXPIRED;
                        $latestPayment->save();

                        $rental->status = RentalStatus::EXPIRED;
                        $rental->prepaid_expires_at = null;
                        booking_release_identity_files($rental);
                        $rental->save();
                    });

                    app(\App\Services\CustomerNotificationService::class)->notifyPaymentExpired($rental);

                    // Reload relations
                    $rental->refresh();
                    $latestPayment = $rental->paymentHistories()->latest()->first();
                } elseif (in_array($status, ['deny', 'cancel', 'failure'])) {
                    DB::transaction(function () use ($rental, $latestPayment) {
                        $latestPayment->status = \App\Enums\PaymentStatus::CANCELLED;
                        $latestPayment->save();

                        $rental->status = RentalStatus::CANCELLED;
                        $rental->prepaid_expires_at = null;
                        booking_release_identity_files($rental);
                        $rental->save();
                    });

                    app(\App\Services\CustomerNotificationService::class)->notifyPaymentCancelled($rental);

                    // Reload relations
                    $rental->refresh();
                    $latestPayment = $rental->paymentHistories()->latest()->first();
                }
            }
        } catch (\Exception $e) {
            // Ignore API failures and proceed with stored DB status
        }
    }

    return view('frontliner.pages.booking-detail', [
        'rental' => $rental,
        'car' => $rental->car,
        'payment' => $latestPayment,
    ]);
})->middleware(['token.cookie', 'auth'])->name('booking.detail');

Route::get('/rentals/{rental}/review', [ReviewController::class, 'create'])->middleware(['token.cookie', 'auth'])->name('booking.review');
Route::post('/rentals/{rental}/review', [ReviewController::class, 'store'])->middleware(['token.cookie', 'auth'])->name('booking.review.store');

Route::get('/profile', [CustomerAccountController::class, 'profile'])->middleware(['token.cookie', 'auth'])->name('customer.profile');
Route::put('/profile', [CustomerAccountController::class, 'updateProfile'])->middleware(['token.cookie', 'auth'])->name('customer.profile.update');
Route::get('/settings', [CustomerAccountController::class, 'settings'])->middleware(['token.cookie', 'auth'])->name('customer.settings');
Route::put('/settings/password', [CustomerAccountController::class, 'updatePassword'])->middleware(['token.cookie', 'auth'])->name('customer.settings.password');
Route::get('/payments', [CustomerAccountController::class, 'payments'])->middleware(['token.cookie', 'auth'])->name('customer.payments');

Route::get('/pesanan-saya', function (Request $request) {
    $user = auth()->user();
    if (!$user) {
        return redirect()->route('login');
    }

    // Run Midtrans sync for prepaid rentals of this user first, to be consistent!
    \App\Http\Controllers\PaymentController::syncUserPendingRentals($user);

    // Build the query
    $query = Rental::with(['car', 'paymentHistories', 'review'])
        ->where('user_id', $user->id);

    // Apply search filter (car name, brand, or license plate)
    if ($request->filled('q')) {
        $search = $request->input('q');
        $query->whereHas('car', function ($q) use ($search) {
            $q->where(function ($sq) use ($search) {
                $sq->where('name', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%")
                  ->orWhere('license_plate', 'like', "%{$search}%");
            });
        });
    }

    // Apply status filter
    // Options: 'aktif', 'selesai', 'pending', 'dibatalkan'
    if ($request->filled('status')) {
        $status = $request->input('status');
        if ($status === 'aktif') {
            $query->where('status', RentalStatus::ONGOING);
        } elseif ($status === 'selesai') {
            $query->where('status', RentalStatus::RETURNED);
        } elseif ($status === 'pending') {
            $query->whereIn('status', [RentalStatus::PENDING_VERIFICATION, RentalStatus::PREPAID]);
        } elseif ($status === 'dibatalkan') {
            $query->whereIn('status', [RentalStatus::CANCELLED, RentalStatus::EXPIRED]);
        }
    }

    // Apply vehicle type filter
    if ($request->filled('type')) {
        $type = $request->input('type');
        $query->whereHas('car', function ($q) use ($type) {
            $q->where('vehicle_type', $type);
        });
    }

    // Apply service type filter
    if ($request->filled('service')) {
        $service = $request->input('service');
        $query->where('type', $service);
    }

    // Apply date filter
    if ($request->filled('date')) {
        $date = $request->input('date');
        $query->whereDate('start_date', '<=', $date)
              ->whereDate('end_date', '>=', $date);
    }

    $rentals = $query->orderBy('created_at', 'desc')->paginate(6)->withQueryString();

    return view('frontliner.pages.pesanan-saya', [
        'rentals' => $rentals,
    ]);
})->middleware(['token.cookie', 'auth'])->name('pesanan-saya');

Route::get('/pembayaran', [PaymentController::class, 'index'])
    ->middleware(['token.cookie', 'auth'])
    ->name('pembayaran.index');


Route::get('/notifications', [CustomerNotificationController::class, 'index'])
    ->middleware('auth')
    ->name('notifications.index');

Route::get('/notifications/{notification}/open', [CustomerNotificationController::class, 'open'])
    ->middleware('auth')
    ->name('notifications.open');

Route::post('/notifications/{notification}/read', [CustomerNotificationController::class, 'markRead'])
    ->middleware('auth')
    ->name('notifications.read');

Route::post('/notifications/read-all', [CustomerNotificationController::class, 'markAllRead'])
    ->middleware('auth')
    ->name('notifications.read-all');

Route::get('/dashboard/notifications/{notification}/open', [BackofficeNotificationController::class, 'open'])
    ->middleware(['auth', 'admin'])
    ->name('backoffice.notifications.open');

Route::post('/dashboard/notifications/{notification}/read', [BackofficeNotificationController::class, 'markRead'])
    ->middleware(['auth', 'admin'])
    ->name('backoffice.notifications.read');

Route::post('/dashboard/notifications/read-all', [BackofficeNotificationController::class, 'markAllRead'])
    ->middleware(['auth', 'admin'])
    ->name('backoffice.notifications.read-all');

Route::get('/booking/simulate-payment', function (Request $request) {
    $rentalId = $request->query('rental_id');
    $rental = Rental::with(['car', 'user'])->findOrFail($rentalId);

    return view('frontliner.pages.simulate-payment', [
        'rental' => $rental,
    ]);
})->name('booking.simulate-payment');

Route::post('/booking/simulate-payment', function (Request $request) {
    $rentalId = $request->input('rental_id');
    
    $rental = Rental::findOrFail($rentalId);
    $payment = \App\Models\PaymentHistory::where('rental_id', $rentalId)
        ->where('status', \App\Enums\PaymentStatus::PENDING)
        ->firstOrFail();

    DB::transaction(function () use ($rental, $payment) {
        $payment->status = \App\Enums\PaymentStatus::PAID;
        $payment->save();

        $rental->status = RentalStatus::ONGOING;
        $rental->save();
    });

    app(\App\Services\CustomerNotificationService::class)->notifyPaymentPaid($rental);

    return redirect()->route('booking.detail', ['rental' => $rental->id])->with('success', 'Pembayaran Berhasil! Rental Anda telah aktif.');
})->name('booking.simulate-payment.submit');

Route::get('/beranda-login', function () {
    return redirect()->route('frontliner');
})->name('beranda.login');

Route::get('/dashboard', [BackofficeController::class, 'index'])
    ->middleware(['auth', 'admin'])
    ->name('dashboard');

Route::get('/dashboard/users', [BackofficeController::class, 'users'])
    ->middleware(['auth', 'admin'])
    ->name('backoffice.users');

Route::delete('/dashboard/users/{user}', [BackofficeController::class, 'deleteUser'])
    ->middleware(['auth', 'admin'])
    ->name('backoffice.users.destroy');

Route::get('/dashboard/cars', [BackofficeController::class, 'cars'])
    ->middleware(['auth', 'admin'])
    ->name('backoffice.cars');

Route::post('/dashboard/cars', [BackofficeController::class, 'storeCar'])
    ->middleware(['auth', 'admin'])
    ->name('backoffice.cars.store');

Route::patch('/dashboard/cars/{car}/status', [BackofficeController::class, 'updateCarStatus'])
    ->middleware(['auth', 'admin'])
    ->name('backoffice.cars.update-status');

Route::get('/dashboard/reservations', [BackofficeController::class, 'reservations'])
    ->middleware(['auth', 'admin'])
    ->name('backoffice.reservations');

Route::get('/dashboard/reports', [BackofficeController::class, 'reports'])
    ->middleware(['auth', 'admin'])
    ->name('backoffice.reports');

Route::post('/dashboard/reservations', [BackofficeController::class, 'storeReservation'])
    ->middleware(['auth', 'admin'])
    ->name('backoffice.reservations.store');

Route::get('/dashboard/settings', [BackofficeController::class, 'settings'])
    ->middleware(['auth', 'admin'])
    ->name('backoffice.settings');

Route::put('/dashboard/settings/profile', [BackofficeController::class, 'updateProfile'])
    ->middleware(['auth', 'admin'])
    ->name('backoffice.profile.update');

Route::put('/dashboard/settings/company', [BackofficeController::class, 'updateCompanySettings'])
    ->middleware(['auth', 'admin'])
    ->name('backoffice.company-settings.update');

Route::get('/dashboard/rentals/{rental}/document/{type}', function (Rental $rental, string $type) {
    $user = auth()->user();
    if (!$user || $user->role !== User::ROLE_ADMIN) {
        abort(403);
    }

    $path = ($type === 'selfie') ? $rental->selfie_path : $rental->ktp_path;

    if (!$path || !\Illuminate\Support\Facades\Storage::disk('local')->exists($path)) {
        abort(404);
    }

    return response()->file(\Illuminate\Support\Facades\Storage::disk('local')->path($path));
})->middleware(['auth', 'admin'])->name('backoffice.rentals.document');

Route::put('/dashboard/cars/{car}', [BackofficeController::class, 'updateCar'])
    ->middleware(['auth', 'admin'])
    ->name('backoffice.cars.update');

Route::delete('/dashboard/cars/{car}', [BackofficeController::class, 'deleteCar'])
    ->middleware(['auth', 'admin'])
    ->name('backoffice.cars.destroy');

Route::post('/logout', function (Request $request): RedirectResponse {
    $user = $request->user();

    if ($user?->currentAccessToken()) {
        $user->currentAccessToken()->delete();
    }

    auth()->guard('web')->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('login')->withCookie(cookie()->forget('access_token'));
})->middleware('auth')->name('logout');

Route::get('/beranda-non-login', function () {
    return view('frontliner.pages.beranda-non-login');
})->name('beranda.non-login');

Route::view('/welcome', 'welcome');
Route::get('/login', function (Request $request) {
    $user = $request->user();
    $redirect = $request->query('redirect');
    $cars = Car::count();

    if ($user?->role === User::ROLE_ADMIN) {
        return redirect()->route('dashboard');
    }

    if ($user) {
        if (is_string($redirect) && str_starts_with($redirect, '/') && ! str_starts_with($redirect, '//')) {
            return redirect($redirect);
        }

        return redirect()->route('frontliner');
    }

    return view('frontliner.auth.login',['cars' => $cars]);
})->name('login');

Route::get('/register', function (Request $request) {
    $user = $request->user();
    $redirect = $request->query('redirect');

    if ($user?->role === User::ROLE_ADMIN) {
        return redirect()->route('dashboard');
    }

    if ($user) {
        if (is_string($redirect) && str_starts_with($redirect, '/') && ! str_starts_with($redirect, '//')) {
            return redirect($redirect);
        }

        return redirect()->route('frontliner');
    }

    return view('frontliner.auth.register');
})->name('register');

Route::get('/forgot-password', function (Request $request) {
    if ($request->user()) {
        return redirect()->route('frontliner');
    }
    return view('frontliner.auth.forgot-password');
})->name('password.request');

Route::get('/reset-password/{token}', function (Request $request, $token) {
    if ($request->user()) {
        return redirect()->route('frontliner');
    }
    return view('frontliner.auth.reset-password', [
        'token' => $token,
        'email' => $request->query('email')
    ]);
})->name('password.reset');

Route::get('/syarat-ketentuan', function () {
    return view('frontliner.pages.terms');
})->name('terms.show');

Route::get('/kebijakan-privasi', function () {
    return view('frontliner.pages.privacy');
})->name('privacy.show');

Route::get('/search-result', function (Request $request) {
    $query = Car::query()->withReviewMetrics()->where('status', CarStatus::AVAILABLE);
    $startDate = $request->input('start_date');
    $endDate = $request->input('end_date');
    $hasActiveFilters = $request->filled('start_date')
        || $request->filled('end_date')
        || $request->filled('max_price')
        || $request->filled('types')
        || $request->filled('capacity')
        || $request->filled('service_types');

    if ($request->filled('start_date') || $request->filled('end_date')) {
        $validator = Validator::make($request->all(), [
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date|after_or_equal:today',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('armada')
                ->withInput()
                ->with('error', 'Tanggal pencarian tidak valid. Tanggal tidak boleh sebelum hari ini dan tanggal selesai harus sama atau setelah tanggal mulai.');
        }
    }

    if ($request->filled('max_price')) {
        $maxPrice = (int) $request->input('max_price');
        if ($maxPrice > 0) {
            $query->where('daily_rate', '<=', $maxPrice);
        }
    }

    if ($request->filled('types')) {
        $types = (array) $request->input('types');
        $query->whereIn('vehicle_type', $types);
    }

    if ($request->filled('capacity')) {
        $capacity = $request->input('capacity');
        if ($capacity === '2') {
            $query->where('seat_count', 2);
        } elseif ($capacity === '4-5') {
            $query->whereBetween('seat_count', [4, 5]);
        } elseif ($capacity === '7') {
            $query->where('seat_count', 7);
        } elseif ($capacity === 'other') {
            $query->whereNotIn('seat_count', [2, 4, 5, 7]);
        }
    }

    if ($request->filled('service_types')) {
        $serviceTypes = (array) $request->input('service_types');
        $query->where(function ($q) use ($serviceTypes) {
            if (in_array('self_drive', $serviceTypes)) {
                $q->orWhere('self_drive_available', true);
            }
            if (in_array('with_driver', $serviceTypes)) {
                $q->orWhere('driver_available', true);
            }
        });
    }

    $cars = $query->get()
        ->when($request->filled('start_date') && $request->filled('end_date'), fn ($cars) => $cars->filter(
            fn (Car $car) => booking_car_availability_result($car, $startDate, $endDate)['available']
        ))
        ->values();

    $recommendedCars = $hasActiveFilters
        ? $cars->take(2)->values()
        : Car::query()
            ->withReviewMetrics()
            ->withCount('rentals')
            ->where('status', CarStatus::AVAILABLE)
            ->orderByDesc('rentals_count')
            ->orderByDesc('reviews_avg_rating')
            ->orderByDesc('rating')
            ->orderByDesc('created_at')
            ->take(2)
            ->get()
            ->values();

    return view('frontliner.pages.search-result', [
        'cars' => $cars,
        'recommendedCars' => $recommendedCars,
        'hasActiveFilters' => $hasActiveFilters,
    ]);
})->middleware('token.cookie')->name('search-result');
Route::get('/armada', function (Request $request) {
    $search = trim($request->string('q')->toString());

    $cars = Car::query()
        ->withReviewMetrics()
        ->when($search !== '', function ($query) use ($search) {

            $search = strtolower($search);

            $query->where(function ($innerQuery) use ($search) {

                $innerQuery
                    ->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(brand) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(license_plate) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(vehicle_type) LIKE ?', ["%{$search}%"]);
            });
        })
        ->orderByDesc('created_at')
        ->paginate(6)
        ->withQueryString();

    return view('frontliner.pages.armada', [
        'cars' => $cars,
        'search' => $search,
    ]);
})->middleware('token.cookie')->name('armada');

Route::get('/armada/export', function () {

    $cars = Car::query()
        ->orderBy('brand')
        ->orderBy('name')
        ->get()
        ->map(function ($car) {

            $mainImage = null;

            if (
                $car->image &&
                Storage::disk('public')->exists($car->image)
            ) {

                $path = Storage::disk('public')->path($car->image);

                $mainImage =
                    'data:image/' .
                    pathinfo($path, PATHINFO_EXTENSION) .
                    ';base64,' .
                    base64_encode(file_get_contents($path));
            }

            $galleryUrls = [];

            $galleryImages = [];

            if ($car->gallery_images) {

                if (is_array($car->gallery_images)) {
                    $galleryImages = $car->gallery_images;
                } else {
                    $galleryImages = json_decode(
                        $car->gallery_images,
                        true
                    ) ?? [];
                }
            }

            foreach ($galleryImages as $image) {

                if (Storage::disk('public')->exists($image)) {

                    $path = Storage::disk('public')->path($image);

                    $galleryUrls[] =
                        'data:image/' .
                        pathinfo($path, PATHINFO_EXTENSION) .
                        ';base64,' .
                        base64_encode(file_get_contents($path));
                }
            }

            return [
                'name' => $car->name,
                'brand' => $car->brand,
                'description' => $car->description,
                'license_plate' => $car->license_plate,
                'year' => $car->year,
                'cc' => $car->cc,
                'seat_count' => $car->seat_count,
                'daily_rate' => $car->daily_rate,
                'transmission' => $car->transmission->label(),
                'main_image' => $mainImage,
                'gallery_urls' => $galleryUrls,
            ];
        });

    $pdf = Pdf::loadView(
        'vendor.armada-pdf',
        compact('cars')
    )->setPaper('a4', 'portrait');

    return $pdf->download('armada.pdf');

})->middleware('token.cookie')->name('armada.export');

Route::get('/car-detail/{car}', function (Car $car) {
    $similarCars = Car::query()
        ->where('status', CarStatus::AVAILABLE)
        ->where('id', '!=', $car->id)
        ->where('vehicle_type', $car->vehicle_type)
        ->limit(3)
        ->get();

    if ($similarCars->count() < 3) {
        $additionalCars = Car::query()
            ->where('status', CarStatus::AVAILABLE)
            ->where('id', '!=', $car->id)
            ->whereNotIn('id', $similarCars->pluck('id'))
            ->limit(3 - $similarCars->count())
            ->get();
        $similarCars = $similarCars->concat($additionalCars);
    }

    $car->load(['reviews.user']);

    return view('frontliner.pages.car-detail', [
        'car' => $car,
        'similarCars' => $similarCars,
    ]);
})->middleware('token.cookie')->name('car-detail');

Route::get('/favorite', function (Request $request) {
    $cars = \App\Models\Car::query()
        ->withReviewMetrics()
        ->get();
    return view('frontliner.pages.favorite', [
        'cars' => $cars,
    ]);
})->middleware(['token.cookie', 'auth'])->name('favorite');

Route::get('/testimoni', function (Request $request) {
    $sort = $request->string('sort', 'latest')->toString();
    $selectedVehicleType = $request->string('vehicle_type')->toString();
    $selectedMinimumRating = (int) $request->integer('min_rating', 0);

    $reviews = \App\Models\Review::with(['user', 'car'])
        ->when($selectedVehicleType !== '', function ($query) use ($selectedVehicleType) {
            $query->whereHas('car', function ($carQuery) use ($selectedVehicleType) {
                $carQuery->where('vehicle_type', $selectedVehicleType);
            });
        })
        ->when($selectedMinimumRating > 0, function ($query) use ($selectedMinimumRating) {
            $query->where('rating', '>=', $selectedMinimumRating);
        })
        ->when($sort === 'oldest', fn ($query) => $query->oldest())
        ->when($sort === 'highest_rating', fn ($query) => $query->orderByDesc('rating')->latest())
        ->when($sort === 'lowest_rating', fn ($query) => $query->orderBy('rating')->latest())
        ->when(! in_array($sort, ['oldest', 'highest_rating', 'lowest_rating'], true), fn ($query) => $query->latest())
        ->paginate(6)
        ->withQueryString();

    return view('frontliner.pages.testimoni', [
        'reviews' => $reviews,
        'vehicleTypes' => VehicleType::cases(),
        'selectedVehicleType' => $selectedVehicleType,
        'selectedMinimumRating' => $selectedMinimumRating,
        'selectedSort' => $sort,
    ]);
})->middleware('token.cookie')->name('testimoni');

Route::post('/chatbot/message', [\App\Http\Controllers\ChatbotController::class, 'handle'])
    ->middleware('token.cookie')
    ->name('chatbot.message');

    
