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

Route::post('/booking/submit', function (Request $request, \App\Services\FaceVerificationService $faceVerify, \App\Services\MidtransService $midtrans) {
    $request->validate([
        'car_id' => 'required|integer|exists:cars,id',
        'start_date' => 'required|date',
        'end_date' => 'required|date|after_or_equal:start_date',
        'service_type' => 'required|string',
        'ktp' => 'nullable|file|image|max:5120',
        'selfie' => 'nullable|file|image|max:5120',
    ]);

    $car = Car::findOrFail($request->input('car_id'));

    if ($car->status !== CarStatus::AVAILABLE) {
        return redirect()->route('frontliner')->with('error', 'Mobil ini sudah tidak tersedia untuk disewa.');
    }

    // 1. Prepare files for face verification
    if ($request->hasFile('ktp')) {
        $ktpFile = $request->file('ktp');
    } else {
        $ktpPath = 'temp/mock_ktp.png';
        $ktpFullPath = storage_path('app/public/' . $ktpPath);
        if (! file_exists($ktpFullPath)) {
            @mkdir(dirname($ktpFullPath), 0755, true);
            $dummyPng = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==');
            @file_put_contents($ktpFullPath, $dummyPng);
        }
        $ktpFile = new \Illuminate\Http\UploadedFile(
            $ktpFullPath,
            basename($ktpFullPath),
            mime_content_type($ktpFullPath),
            null,
            true
        );
    }

    if ($request->hasFile('selfie')) {
        $selfieFile = $request->file('selfie');
    } else {
        $selfiePath = 'temp/mock_selfie.png';
        $selfieFullPath = storage_path('app/public/' . $selfiePath);
        if (! file_exists($selfieFullPath)) {
            @mkdir(dirname($selfieFullPath), 0755, true);
            $dummyPng = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==');
            @file_put_contents($selfieFullPath, $dummyPng);
        }
        $selfieFile = new \Illuminate\Http\UploadedFile(
            $selfieFullPath,
            basename($selfieFullPath),
            mime_content_type($selfieFullPath),
            null,
            true
        );
    }

    // 2. Perform Face Verification
    try {
        $verification = $faceVerify->verify($ktpFile, $selfieFile);
    } catch (\Exception $e) {
        return back()->with('error', 'Layanan verifikasi wajah gagal dihubungi. Silakan coba sesaat lagi.');
    }

    if (! $verification['verified']) {
        return back()->with('error', 'Verifikasi wajah gagal. Pastikan foto selfie Anda cocok dengan foto KTP.');
    }

    // 3. Create Rental & Payment within Transaction
    try {
        $rental = DB::transaction(function () use ($request, $car) {
            // Re-verify availability with lock
            $car = Car::query()->lockForUpdate()->find($car->id);
            if ($car->status !== CarStatus::AVAILABLE) {
                throw new \RuntimeException('Car is no longer available.');
            }

            // Move files to permanent storage
            if ($request->hasFile('ktp')) {
                $ktpPermanentPath = Storage::disk('local')->putFile('ktp', $request->file('ktp'));
            } else {
                $ktpPermanentPath = 'ktp/mock_ktp.png';
                if (! Storage::disk('local')->exists($ktpPermanentPath)) {
                    Storage::disk('local')->put($ktpPermanentPath, base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg=='));
                }
            }

            if ($request->hasFile('selfie')) {
                $selfiePermanentPath = Storage::disk('local')->putFile('selfie', $request->file('selfie'));
            } else {
                $selfiePermanentPath = 'selfie/mock_selfie.png';
                if (! Storage::disk('local')->exists($selfiePermanentPath)) {
                    Storage::disk('local')->put($selfiePermanentPath, base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg=='));
                }
            }

            // Calculate price
            $start = \Carbon\Carbon::parse($request->input('start_date'));
            $end = \Carbon\Carbon::parse($request->input('end_date'));
            $days = max(1, $start->diffInDays($end));

            $rentCost = $car->daily_rate * $days;
            $driverCost = ($request->input('service_type') === 'with_driver') ? 150000 * $days : 0;
            $serviceCost = 100000 + $driverCost;
            $totalPrice = $rentCost + $serviceCost;

            // Map UI service type to RentalType enum value
            $type = ($request->input('service_type') === 'with_driver') 
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
