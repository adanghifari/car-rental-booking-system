<?php

use App\Http\Controllers\BackofficeController;
use Illuminate\Http\RedirectResponse;
use App\Models\User;
use App\Models\Car;
use App\Enums\CarStatus;
use App\Enums\RentalStatus;
use App\Enums\VehicleType;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function (Request $request) {
    $user = $request->user();

    if ($user?->role === User::ROLE_ADMIN) {
        return redirect()->route('dashboard');
    }

    if ($user) {
        return redirect()->route('frontliner');
    }

    $query = Car::query()->where('status', CarStatus::AVAILABLE);

    if ($request->filled('start_date')) {
        $startDate = $request->input('start_date');
        $query->whereNotExists(function ($q) use ($startDate) {
            $q->select(DB::raw(1))
                ->from('rentals')
                ->whereColumn('rentals.car_id', 'cars.id')
                ->whereIn('rentals.status', [RentalStatus::PREPAID, RentalStatus::ONGOING])
                ->whereDate('rentals.start_date', '<=', $startDate)
                ->whereDate('rentals.end_date', '>=', $startDate);
        });
    }

    if ($request->filled('max_price')) {
        $maxPrice = (int) $request->input('max_price');
        if ($maxPrice > 0) {
            $query->where('daily_rate', '<=', $maxPrice);
        }
    }

    $cars = $query->get();

    return view('frontliner.pages.beranda-non-login', [
        'cars' => $cars,
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

    $query = Car::query()->where('status', CarStatus::AVAILABLE);

    if ($request->filled('start_date')) {
        $startDate = $request->input('start_date');
        $query->whereNotExists(function ($q) use ($startDate) {
            $q->select(DB::raw(1))
                ->from('rentals')
                ->whereColumn('rentals.car_id', 'cars.id')
                ->whereIn('rentals.status', [RentalStatus::PREPAID, RentalStatus::ONGOING])
                ->whereDate('rentals.start_date', '<=', $startDate)
                ->whereDate('rentals.end_date', '>=', $startDate);
        });
    }

    if ($request->filled('max_price')) {
        $maxPrice = (int) $request->input('max_price');
        if ($maxPrice > 0) {
            $query->where('daily_rate', '<=', $maxPrice);
        }
    }

    $cars = $query->get();

    return view('frontliner.pages.beranda-non-login', [
        'cars' => $cars,
    ]);
})->middleware('token.cookie')->name('beranda');

Route::get('/frontliner', function (Request $request) {
    $user = $request->user();

    if ($user?->role === User::ROLE_ADMIN) {
        return redirect()->route('dashboard');
    }

    $query = Car::query()->where('status', CarStatus::AVAILABLE);

    if ($request->filled('start_date')) {
        $startDate = $request->input('start_date');
        $query->whereNotExists(function ($q) use ($startDate) {
            $q->select(DB::raw(1))
                ->from('rentals')
                ->whereColumn('rentals.car_id', 'cars.id')
                ->whereIn('rentals.status', [RentalStatus::PREPAID, RentalStatus::ONGOING])
                ->whereDate('rentals.start_date', '<=', $startDate)
                ->whereDate('rentals.end_date', '>=', $startDate);
        });
    }

    if ($request->filled('max_price')) {
        $maxPrice = (int) $request->input('max_price');
        if ($maxPrice > 0) {
            $query->where('daily_rate', '<=', $maxPrice);
        }
    }

    $cars = $query->get();

    return view('frontliner.pages.beranda-login', [
        'user' => $user,
        'cars' => $cars,
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

    $car = Car::findOrFail($carId);

    $start = \Carbon\Carbon::parse($startDate);
    $end = \Carbon\Carbon::parse($endDate);
    $days = max(1, $start->diffInDays($end));

    return view('frontliner.pages.booking-confirm', [
        'car' => $car,
        'start_date' => $startDate,
        'end_date' => $endDate,
        'service_type' => $serviceType,
        'days' => $days,
    ]);
})->middleware('token.cookie')->name('booking.start');

Route::post('/booking/confirm', function (Request $request) {
    $request->validate([
        'car_id' => 'required|integer|exists:cars,id',
        'start_date' => 'required|date',
        'end_date' => 'required|date|after_or_equal:start_date',
        'service_type' => 'required|string',
        'ktp' => 'required|file|image|max:5120',
    ]);

    $ktpPath = $request->file('ktp')->store('temp', 'public');

    session()->put('booking', [
        'car_id' => $request->input('car_id'),
        'start_date' => $request->input('start_date'),
        'end_date' => $request->input('end_date'),
        'service_type' => $request->input('service_type'),
        'ktp_path' => $ktpPath,
    ]);

    return redirect()->route('booking.summary');
})->middleware(['token.cookie', 'auth'])->name('booking.confirm');

Route::get('/booking/summary', function (Request $request) {
    $booking = session()->get('booking');

    if (! $booking) {
        return redirect()->route('frontliner')->with('error', 'Silakan isi detail pemesanan terlebih dahulu.');
    }

    $car = Car::findOrFail($booking['car_id']);

    // Calculate dates & price
    $start = \Carbon\Carbon::parse($booking['start_date']);
    $end = \Carbon\Carbon::parse($booking['end_date']);
    $days = max(1, $start->diffInDays($end));

    $rentCost = $car->daily_rate * $days;
    $driverCost = ($booking['service_type'] === 'with_driver') ? 150000 * $days : 0;
    $serviceCost = 100000 + $driverCost;
    $totalPrice = $rentCost + $serviceCost;

    return view('frontliner.pages.booking-summary', [
        'booking' => $booking,
        'car' => $car,
        'days' => $days,
        'rentCost' => $rentCost,
        'driverCost' => $driverCost,
        'serviceCost' => $serviceCost,
        'totalPrice' => $totalPrice,
    ]);
})->middleware(['token.cookie', 'auth'])->name('booking.summary');

Route::post('/booking/submit', function (Request $request, \App\Services\FaceVerificationService $faceVerify, \App\Services\MidtransService $midtrans) {
    $request->validate([
        'selfie' => 'required|file|image|max:5120',
    ]);

    $booking = session()->get('booking');

    if (! $booking) {
        return redirect()->route('frontliner')->with('error', 'Detail pemesanan kedaluwarsa.');
    }

    $car = Car::findOrFail($booking['car_id']);

    if ($car->status !== CarStatus::AVAILABLE) {
        return redirect()->route('frontliner')->with('error', 'Mobil ini sudah tidak tersedia untuk disewa.');
    }

    // 1. Prepare files for face verification
    $ktpRelativePath = 'public/' . $booking['ktp_path'];
    $ktpFullPath = storage_path('app/' . $ktpRelativePath);

    if (! file_exists($ktpFullPath)) {
        return redirect()->route('booking.start', [
            'car_id' => $booking['car_id'],
            'start_date' => $booking['start_date'],
            'end_date' => $booking['end_date'],
            'service_type' => $booking['service_type'],
        ])->with('error', 'Berkas KTP tidak ditemukan. Silakan unggah kembali.');
    }

    $selfieFile = $request->file('selfie');
    
    // Construct UploadedFile for KTP
    $ktpUploadedFile = new \Illuminate\Http\UploadedFile(
        $ktpFullPath,
        basename($ktpFullPath),
        mime_content_type($ktpFullPath),
        null,
        true // test mode to bypass validation of moves
    );

    // 2. Perform Face Verification
    try {
        $verification = $faceVerify->verify($ktpUploadedFile, $selfieFile);
    } catch (\Exception $e) {
        return back()->with('error', 'Layanan verifikasi wajah gagal dihubungi. Silakan coba sesaat lagi.');
    }

    if (! $verification['verified']) {
        return back()->with('error', 'Verifikasi wajah gagal. Pastikan foto selfie Anda cocok dengan foto KTP.');
    }

    // 3. Create Rental & Payment within Transaction
    try {
        $rental = DB::transaction(function () use ($booking, $car, $selfieFile, $request, $ktpFullPath) {
            // Re-verify availability with lock
            $car = Car::query()->lockForUpdate()->find($car->id);
            if ($car->status !== CarStatus::AVAILABLE) {
                throw new \RuntimeException('Car is no longer available.');
            }

            // Move files to permanent storage
            $ktpPermanentPath = Storage::disk('local')->putFile('ktp', new \Illuminate\Http\File($ktpFullPath));
            $selfiePermanentPath = Storage::disk('local')->putFile('selfie', $selfieFile);

            // Calculate price
            $start = \Carbon\Carbon::parse($booking['start_date']);
            $end = \Carbon\Carbon::parse($booking['end_date']);
            $days = max(1, $start->diffInDays($end));

            $rentCost = $car->daily_rate * $days;
            $driverCost = ($booking['service_type'] === 'with_driver') ? 150000 * $days : 0;
            $serviceCost = 100000 + $driverCost;
            $totalPrice = $rentCost + $serviceCost;

            // Map UI service type to RentalType enum value
            $type = ($booking['service_type'] === 'with_driver') 
                ? \App\Enums\RentalType::WITH_DRIVER 
                : \App\Enums\RentalType::SELF_DRIVE;

            $rental = Rental::create([
                'user_id' => auth()->id(),
                'car_id' => $car->id,
                'start_date' => $start,
                'end_date' => $end,
                'total_price' => $totalPrice,
                'status' => RentalStatus::PREPAID,
                'type' => $type,
                'prepaid_expires_at' => now()->addDay(),
                'ktp_path' => $ktpPermanentPath,
                'selfie_path' => $selfiePermanentPath,
                'verification_passed' => true,
                'verified_at' => now(),
            ]);

            // Mark car as UNAVAILABLE
            $car->status = CarStatus::UNAVAILABLE;
            $car->save();

            // Delete temporary KTP file
            @unlink($ktpFullPath);

            return $rental;
        });
    } catch (\Exception $e) {
        return redirect()->route('frontliner')->with('error', $e->getMessage() ?: 'Gagal memproses pemesanan.');
    }

    // Initialize Midtrans Payment
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

        // Clear session
        session()->forget('booking');

        // Redirect to payment redirect url
        return redirect($midtransResponse['redirect_url']);
    } catch (\Exception $e) {
        // Rollback car status if payment initialization fails completely
        $rental->status = RentalStatus::RETURNED; // cancel it
        $rental->save();
        $car->status = CarStatus::AVAILABLE;
        $car->save();
        return redirect()->route('frontliner')->with('error', 'Gagal memulai gerbang pembayaran: ' . $e->getMessage());
    }
})->middleware(['token.cookie', 'auth'])->name('booking.submit');

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

        $car = $rental->car;
        if ($car) {
            $car->status = CarStatus::UNAVAILABLE;
            $car->save();
        }
    });

    return redirect()->route('frontliner')->with('success', 'Pembayaran Berhasil! Rental Anda telah aktif.');
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

Route::get('/dashboard/cars', [BackofficeController::class, 'cars'])
    ->middleware(['auth', 'admin'])
    ->name('backoffice.cars');

Route::post('/dashboard/cars', [BackofficeController::class, 'storeCar'])
    ->middleware(['auth', 'admin'])
    ->name('backoffice.cars.store');

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

    if ($user?->role === User::ROLE_ADMIN) {
        return redirect()->route('dashboard');
    }

    if ($user) {
        if (is_string($redirect) && str_starts_with($redirect, '/') && ! str_starts_with($redirect, '//')) {
            return redirect($redirect);
        }

        return redirect()->route('frontliner');
    }

    return view('frontliner.auth.login');
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
Route::get('/search-result', function (Request $request) {
    $query = Car::query()->where('status', CarStatus::AVAILABLE);

    if ($request->filled('start_date')) {
        $startDate = $request->input('start_date');
        $query->whereNotExists(function ($q) use ($startDate) {
            $q->select(DB::raw(1))
                ->from('rentals')
                ->whereColumn('rentals.car_id', 'cars.id')
                ->whereIn('rentals.status', [RentalStatus::PREPAID, RentalStatus::ONGOING])
                ->whereDate('rentals.start_date', '<=', $startDate)
                ->whereDate('rentals.end_date', '>=', $startDate);
        });
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

    $cars = $query->get();

    return view('frontliner.pages.search-result', [
        'cars' => $cars,
    ]);
})->middleware('token.cookie')->name('search-result');
Route::get('/armada', function (Request $request) {
    $cars = Car::query()->where('status', CarStatus::AVAILABLE)->get();

    return view('frontliner.pages.armada', [
        'cars' => $cars,
    ]);
})->middleware('token.cookie')->name('armada');
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

    return view('frontliner.pages.car-detail', [
        'car' => $car,
        'similarCars' => $similarCars,
    ]);
})->middleware('token.cookie')->name('car-detail');
