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

    if ($user?->role === User::ROLE_ADMIN) {
        return redirect()->route('dashboard');
    }

    if ($user) {
        return redirect()->route('frontliner');
    }

    return redirect()->route('login', [
        'redirect' => '/frontliner',
    ]);
})->middleware('token.cookie')->name('booking.start');

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
