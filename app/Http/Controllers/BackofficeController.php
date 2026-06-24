<?php

namespace App\Http\Controllers;

use App\Enums\CarStatus;
use App\Enums\PaymentStatus;
use App\Enums\RentalStatus;
use App\Enums\VehicleType;
use App\Enums\RentalType;
use Illuminate\Support\Facades\DB;
use App\Enums\TransmissionType;
use App\Models\Car;
use App\Models\CompanySetting;
use App\Models\PaymentHistory;
use App\Models\Rental;
use App\Models\User;
use App\Services\CloudinaryMediaService;
use Illuminate\Support\Str;
use App\Support\BookingAvailability;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class BackofficeController extends Controller
{
    public function index(): View
    {
        $recentRentals = Rental::query()
            ->with(['user:id,name', 'car:id,name'])
            ->latest()
            ->take(4)
            ->get()
            ->map(function (Rental $rental) {
                $activity = match ($rental->status) {
                    RentalStatus::PENDING_VERIFICATION => ($rental->verification_status === \App\Enums\VerificationStatus::NEEDS_REVIEW) 
                        ? 'Verifikasi butuh review' 
                        : 'Menunggu verifikasi',
                    RentalStatus::PREPAID => 'Menunggu pembayaran',
                    RentalStatus::ONGOING => 'Mobil disewa',
                    RentalStatus::RETURNED => 'Mobil dikembalikan',
                    RentalStatus::CANCELLED => 'Rental dibatalkan',
                    RentalStatus::EXPIRED => 'Waktu rental habis',
                    default => 'Aktivitas rental',
                };

                $status = match ($rental->status) {
                    RentalStatus::PENDING_VERIFICATION => ($rental->verification_status === \App\Enums\VerificationStatus::NEEDS_REVIEW)
                        ? ['label' => 'Butuh Review', 'tone' => 'amber']
                        : ['label' => 'Verifikasi', 'tone' => 'indigo'],
                    RentalStatus::PREPAID => ['label' => 'Prepaid', 'tone' => 'amber'],
                    RentalStatus::ONGOING => ['label' => 'Berjalan', 'tone' => 'green'],
                    RentalStatus::RETURNED => ['label' => 'Selesai', 'tone' => 'blue'],
                    RentalStatus::CANCELLED => ['label' => 'Batal', 'tone' => 'red'],
                    RentalStatus::EXPIRED => ['label' => 'Expired', 'tone' => 'gray'],
                    default => ['label' => 'Update', 'tone' => 'slate'],
                };

                return [
                    'activity' => $activity,
                    'subtitle' => trim(($rental->user?->name ?? 'User').' • '.($rental->car?->name ?? 'Armada')),
                    'entity' => $rental->car?->name ?? '-',
                    'time' => $rental->created_at->diffForHumans(),
                    'status' => $status,
                ];
            });

        $overdueRentalsQuery = Rental::with(['user:id,name', 'car'])
            ->where('status', RentalStatus::ONGOING)
            ->whereNull('returned_at')
            ->whereDate('end_date', '<', now());

        $overdueRentalsCount = (clone $overdueRentalsQuery)->count();

        $overdueRentalsPreview = (clone $overdueRentalsQuery)
            ->orderBy('end_date', 'asc')
            ->limit(3)
            ->get();

        $pendingVerifications = Rental::with(['user:id,name', 'car'])
            ->where('status', RentalStatus::PENDING_VERIFICATION)
            ->where('verification_status', \App\Enums\VerificationStatus::NEEDS_REVIEW)
            ->orderBy('created_at', 'asc')
            ->limit(4)
            ->get();

        $returnsToday = Rental::with(['user:id,name', 'car'])
            ->where('status', RentalStatus::ONGOING)
            ->whereNull('returned_at')
            ->whereDate('end_date', now()->toDateString())
            ->orderBy('end_date', 'asc')
            ->limit(4)
            ->get();

        $fleet = [
            'available' => Car::where('status', CarStatus::AVAILABLE)->count(),
            'rented' => Car::query()
                ->whereHas('rentals', fn ($query) => $query
                    ->where('status', RentalStatus::ONGOING)
                    ->whereNull('returned_at')
                    ->whereDate('start_date', '<=', now()->toDateString()))
                ->count(),
            'maintenance' => Car::query()
                ->where('status', CarStatus::UNAVAILABLE)
                ->count(),
        ];

        return view('backoffice.dashboard', [
            'admin' => request()->user(),
            'overdueRentalsCount' => $overdueRentalsCount,
            'overdueRentalsPreview' => $overdueRentalsPreview,
            'pendingVerifications' => $pendingVerifications,
            'returnsToday' => $returnsToday,
            'fleet' => $fleet,
            'recentActivities' => $recentRentals,
        ]);
    }

    public function users(Request $request): View
    {
        $usersQuery = User::query()
            ->withCount('rentals')
            ->withSum('rentals', 'total_price');

        // Search Filter
        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $usersQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Status Filter
        if ($request->filled('status')) {
            $status = $request->input('status');
            if ($status === 'aktif') {
                $usersQuery->where(function ($q) {
                    $q->where('role', User::ROLE_ADMIN)
                      ->orWhereHas('rentals')
                      ->orWhere('created_at', '>=', now()->subMonths(2));
                });
            } elseif ($status === 'suspend') {
                $usersQuery->where('role', '!=', User::ROLE_ADMIN)
                    ->whereDoesntHave('rentals')
                    ->where('created_at', '<', now()->subMonths(2));
            }
        }

        // Sorting
        $sort = $request->query('sort', 'latest');
        if ($sort === 'oldest') {
            $usersQuery->orderBy('created_at', 'asc');
        } elseif ($sort === 'name_asc') {
            $usersQuery->orderBy('name', 'asc');
        } elseif ($sort === 'name_desc') {
            $usersQuery->orderBy('name', 'desc');
        } elseif ($sort === 'transactions_desc') {
            $usersQuery->orderByRaw('(SELECT COALESCE(SUM(total_price), 0) FROM rentals WHERE rentals.user_id = users.id) DESC');
        } else {
            $usersQuery->orderBy('created_at', 'desc');
        }

        $users = $usersQuery->paginate(10)
            ->withQueryString()
            ->through(function (User $user, int $index) {
                $totalTransactions = (int) ($user->rentals_sum_total_price ?? 0);

                $isSuspended = $user->role !== User::ROLE_ADMIN && $user->rentals_count === 0 && $user->created_at->lt(now()->subMonths(2));
                $status = $isSuspended ? 'SUSPEND' : 'AKTIF';
                $statusTone = $isSuspended ? 'suspend' : 'active';

                $avatarPalette = [
                    'linear-gradient(135deg, #2c4474, #6e95ff)',
                    'linear-gradient(135deg, #23495e, #16b5b5)',
                    'linear-gradient(135deg, #50545f, #9399a8)',
                ];

                return [
                    'id' => $user->id,
                    'role' => $user->role,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'username' => $user->username ?? 'user'.$user->id,
                    'contact' => $user->phone ?: ($user->role === User::ROLE_ADMIN ? 'Admin system' : 'Nomor belum diisi'),
                    'role_label' => $user->role === User::ROLE_ADMIN ? 'Admin system' : 'Customer rental',
                    'total_transactions' => $totalTransactions,
                    'status' => $status,
                    'status_tone' => $statusTone,
                    'registered_day' => $user->created_at->translatedFormat('d M'),
                    'registered_year' => $user->created_at->translatedFormat('Y'),
                    'initials' => strtoupper(substr($user->name, 0, 1)),
                    'avatar_background' => $avatarPalette[$index % count($avatarPalette)],
                ];
            });

        return view('backoffice.users', [
            'admin' => request()->user(),
            'users' => $users,
            'pagination' => $this->paginationWindow($users->currentPage(), $users->lastPage()),
        ]);
    }

    public function deleteUser(User $user): RedirectResponse
    {
        if ($user->role === User::ROLE_ADMIN) {
            return back()->with('error', 'User admin tidak dapat dihapus.');
        }

        $user->delete();

        return redirect()
            ->route('backoffice.users')
            ->with('success', 'User berhasil dihapus.');
    }


    public function cars(Request $request): View
    {
        $filters = [
            'search' => trim((string) $request->query('search', '')),
            'status' => (string) $request->query('status', ''),
            'type' => (string) $request->query('type', ''),
            'transmission' => (string) $request->query('transmission', ''),
        ];

        $totalCars = Car::count();
        $availableCars = Car::where('status', CarStatus::AVAILABLE)->count();
        $rentedCars = Car::query()
            ->whereHas('rentals', fn ($query) => $query
                ->where('status', RentalStatus::ONGOING)
                ->whereNull('returned_at')
                ->whereDate('start_date', '<=', now()->toDateString()))
            ->count();
        $maintenanceCars = Car::query()
            ->where('status', CarStatus::UNAVAILABLE)
            ->count();

        $carsQuery = Car::query()
            ->with(['rentals' => fn ($query) => $query->latest()])
            ->when($filters['search'] !== '', function ($query) use ($filters) {
                $query->where(function ($searchQuery) use ($filters) {
                    $searchQuery
                        ->where('name', 'like', '%'.$filters['search'].'%')
                        ->orWhere('brand', 'like', '%'.$filters['search'].'%')
                        ->orWhere('license_plate', 'like', '%'.$filters['search'].'%')
                        ->orWhere('vehicle_type', 'like', '%'.$filters['search'].'%');
                });
            })
            ->when($filters['type'] !== '', fn ($query) => $query->where('vehicle_type', $filters['type']))
            ->when($filters['transmission'] !== '', fn ($query) => $query->where('transmission', $filters['transmission']))
            ->when($filters['status'] !== '', function ($query) use ($filters) {
                if ($filters['status'] === 'available') {
                    $query->where('status', CarStatus::AVAILABLE);
                }

                if ($filters['status'] === 'rented') {
                    $query->whereHas('rentals', fn ($rentalQuery) => $rentalQuery
                        ->where('status', RentalStatus::ONGOING)
                        ->whereNull('returned_at')
                        ->whereDate('start_date', '<=', now()->toDateString()));
                }

                if ($filters['status'] === 'maintenance') {
                    $query->where('status', CarStatus::UNAVAILABLE);
                }
            })
            ->latest();

        $cars = $carsQuery
            ->paginate(6)
            ->withQueryString()
            ->through(function (Car $car) {
                $currentRental = BookingAvailability::currentOngoingRental($car);
                $upcomingRentals = collect(BookingAvailability::impactedRentalsForOperationalHold($car))
                    ->filter(fn (Rental $rental) => $rental->start_date?->isFuture() || $rental->start_date?->isToday())
                    ->values();
                $nextRental = $upcomingRentals->first();
                $status = $this->carStatusMeta($car, $currentRental, $nextRental);

                // Build upcoming schedules list (current + upcoming)
                $scheduleList = collect();
                if ($currentRental) {
                    $scheduleList->push([
                        'id' => $currentRental->id,
                        'customer' => $currentRental->user?->name ?? 'Unknown',
                        'start_date' => $currentRental->start_date?->toDateString(),
                        'end_date' => $currentRental->end_date?->toDateString(),
                        'status' => $currentRental->status instanceof RentalStatus ? $currentRental->status->value : (string) $currentRental->status,
                        'is_current' => true,
                    ]);
                }
                foreach ($upcomingRentals as $r) {
                    $scheduleList->push([
                        'id' => $r->id,
                        'customer' => $r->user?->name ?? 'Unknown',
                        'start_date' => $r->start_date?->toDateString(),
                        'end_date' => $r->end_date?->toDateString(),
                        'status' => $r->status instanceof RentalStatus ? $r->status->value : (string) $r->status,
                        'is_current' => false,
                    ]);
                }
                $services = array_values(array_filter([
                    $car->self_drive_available ? 'Lepas Kunci' : null,
                    $car->driver_available ? 'Dengan Driver' : null,
                ]));

                return [
                    'id' => $car->id,
                    'brand' => $car->brand ?? '-',
                    'model' => $car->name,
                    'description' => $car->description ?? '-',
                    'color' => $car->color ?? '-',
                    'status_raw' => $car->status instanceof CarStatus ? $car->status->value : (string) $car->status,
                    'price' => (int) $car->daily_rate,
                    'price_label' => number_format((int) $car->daily_rate, 0, ',', '.'),
                    'price_raw' => (int) $car->daily_rate,
                    'rating' => number_format($car->rating ?: 5, 1),
                    'rating_raw' => (float) ($car->rating ?: 5),
                    'status' => $status['label'],
                    'status_tone' => $status['tone'],
                    'status_note' => $status['note'],
                    'operational_status' => $status['operational_status'],
                    'schedule_status' => $status['schedule_status'],
                    'locking_rental_id' => $status['locking_rental_id'],
                    'can_change_status' => $status['can_change_status'],
                    'status_action_label' => $status['action_label'],
                    'status_action_kind' => $status['action_kind'],
                    'status_action_value' => $status['action_value'],
                    'action_class' => $status['action_class'],
                    'transmission' => $car->transmission instanceof TransmissionType ? $car->transmission->label() : (string) $car->transmission,
                    'transmission_raw' => $car->transmission instanceof TransmissionType ? $car->transmission->value : (string) $car->transmission,
                    'seat' => $car->seat_count.' Kursi',
                    'seat_raw' => (int) $car->seat_count,
                    'year' => $car->year ? (string) $car->year : 'Tahun belum diisi',
                    'year_raw' => $car->year,
                    'cc' => $car->cc ? number_format($car->cc, 0, ',', '.').' CC' : 'CC belum diisi',
                    'cc_raw' => (int) $car->cc,
                    'type' => $car->vehicle_type instanceof VehicleType ? $car->vehicle_type->label() : (string) $car->vehicle_type,
                    'type_raw' => $car->vehicle_type instanceof VehicleType ? $car->vehicle_type->value : (string) $car->vehicle_type,
                    'self_drive_available' => (bool) $car->self_drive_available,
                    'driver_available' => (bool) $car->driver_available,
                    'services' => $services,
                    'services_label' => $services !== [] ? implode(', ', $services) : 'Tidak ada',
                    'plate' => strtoupper($car->license_plate),
                    'plate_raw' => $car->license_plate,
                    'image_url' => $this->resolveCarImageUrl($car->image),
                    'gallery_urls' => collect($car->gallery_images ?? [])
                        ->map(fn ($path) => $this->resolveCarImageUrl($path))
                        ->filter()
                        ->values()
                        ->all(),
                    'gallery_paths' => array_values(array_filter(is_array($car->gallery_images) ? $car->gallery_images : [])),
                    'image_raw' => $car->image,
                    'upcoming_rentals' => $scheduleList->values()->all(),
                ];
            });

        return view('backoffice.cars', [
            'admin' => request()->user(),
            'stats' => [
                'total' => $totalCars,
                'available' => $availableCars,
                'rented' => $rentedCars,
                'maintenance' => $maintenanceCars,
                'occupancy_rate' => $totalCars > 0 ? (int) round(($rentedCars / $totalCars) * 100) : 0,
            ],
            'filters' => $filters,
            'cars' => $cars,
            'typeOptions' => collect(VehicleType::cases())
                ->map(fn (VehicleType $type) => [
                    'value' => $type->value,
                    'label' => $type->label(),
                ])
                ->values()
                ->all(),
            'transmissionOptions' => collect(TransmissionType::cases())
                ->map(fn (TransmissionType $type) => [
                    'value' => $type->value,
                    'label' => $type->label(),
                ])
                ->values()
                ->all(),
            'serviceOptions' => [
                ['name' => 'self_drive_available', 'label' => 'Lepas Kunci'],
                ['name' => 'driver_available', 'label' => 'Dengan Driver'],
            ],
            'pagination' => $this->paginationWindow($cars->currentPage(), $cars->lastPage()),
        ]);
    }

    public function storeCar(Request $request): RedirectResponse
    {
        if ($request->has('license_plate')) {
            $normalizedPlate = strtoupper(trim(preg_replace('/\s+/', ' ', $request->input('license_plate'))));
            $request->merge(['license_plate' => $normalizedPlate]);
        }

        $validator = Validator::make($request->all(), [
            'brand' => ['required', 'string', 'max:100'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'transmission' => ['required', Rule::in(TransmissionType::values())],
            'seat_count' => ['required', 'integer', 'min:1', 'max:99'],
            'year' => ['required', 'integer', 'min:1990', 'max:' . (int) now()->addYear()->year],
            'cc' => ['required', 'integer', 'min:1', 'max:99999'],
            'vehicle_type' => ['required', Rule::in(VehicleType::values())],
            'color' => ['required', 'string', 'max:50'],
            'daily_rate' => ['required', 'integer', 'min:0'],
            'license_plate' => ['required', 'string', 'max:30', 'unique:cars,license_plate'],
            'image' => ['required', 'file', 'image', 'max:5120'],
            'gallery_images' => ['nullable', 'array', 'max:8'],
            'gallery_images.*' => ['file', 'image', 'max:5120'],
            'self_drive_available' => ['boolean'],
            'driver_available' => ['boolean'],
        ]);

        $validator->after(function ($validator) use ($request) {
            if (! $request->boolean('self_drive_available') && ! $request->boolean('driver_available')) {
                $validator->errors()->add('service_selection', 'Pilih minimal satu layanan: Lepas Kunci atau Dengan Driver.');
            }
        });

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $validated = $validator->validated();

        $validated['image'] = $this->storeCarMedia($request->file('image'), 'cars/main');
        $validated['gallery_images'] = collect($request->file('gallery_images', []))
            ->map(fn ($file) => $this->storeCarMedia($file, 'cars/gallery'))
            ->values()
            ->all();

        $validated['status'] = CarStatus::AVAILABLE;
        $validated['self_drive_available'] = $request->boolean('self_drive_available');
        $validated['driver_available'] = $request->boolean('driver_available');

        Car::create($validated);

        return redirect()
            ->route('backoffice.cars')
            ->with('success', 'Mobil baru berhasil ditambahkan.');
    }

    public function updateCar(Request $request, Car $car): RedirectResponse
    {
        if ($request->has('license_plate')) {
            $normalizedPlate = strtoupper(trim(preg_replace('/\s+/', ' ', $request->input('license_plate'))));
            $request->merge(['license_plate' => $normalizedPlate]);
        }

        $imageRules = ($request->boolean('remove_image') || blank($car->image))
            ? ['required', 'file', 'image', 'max:5120']
            : ['nullable', 'file', 'image', 'max:5120'];

        $validator = Validator::make($request->all(), [
            'brand' => ['required', 'string', 'max:100'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'transmission' => ['required', Rule::in(TransmissionType::values())],
            'seat_count' => ['required', 'integer', 'min:1', 'max:99'],
            'year' => ['required', 'integer', 'min:1990', 'max:' . (int) now()->addYear()->year],
            'cc' => ['required', 'integer', 'min:1', 'max:99999'],
            'vehicle_type' => ['required', Rule::in(VehicleType::values())],
            'color' => ['required', 'string', 'max:50'],
            'daily_rate' => ['required', 'integer', 'min:0'],
            'license_plate' => ['required', 'string', 'max:30', 'unique:cars,license_plate,' . $car->id],
            'image' => $imageRules,
            'remove_image' => ['nullable', 'boolean'],
            'remove_gallery_images' => ['nullable', 'string'],
            'gallery_images' => ['nullable', 'array', 'max:8'],
            'gallery_images.*' => ['file', 'image', 'max:5120'],
            'self_drive_available' => ['boolean'],
            'driver_available' => ['boolean'],
        ]);

        $validator->after(function ($validator) use ($request) {
            if (! $request->boolean('self_drive_available') && ! $request->boolean('driver_available')) {
                $validator->errors()->add('service_selection', 'Pilih minimal satu layanan: Lepas Kunci atau Dengan Driver.');
            }
        });

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $validated = $validator->validated();

        $removeImage = $request->boolean('remove_image');
        $removedGalleryImages = json_decode((string) $request->input('remove_gallery_images', '[]'), true);
        $removedGalleryImages = is_array($removedGalleryImages) ? array_values(array_filter($removedGalleryImages, 'is_string')) : [];
        $existingGalleryImages = array_values(array_filter(is_array($car->gallery_images) ? $car->gallery_images : []));
        $remainingGalleryImages = array_values(array_diff($existingGalleryImages, $removedGalleryImages));

        if ($request->hasFile('image')) {
            $this->deleteStoredCarMedia($car, false);
            $validated['image'] = $this->storeCarMedia($request->file('image'), 'cars/main');
        } elseif ($removeImage) {
            $this->deleteStoredCarMedia($car, false);
            $validated['image'] = null;
        } else {
            unset($validated['image']);
        }

        if ($request->hasFile('gallery_images')) {
            $this->deleteStoredGalleryMedia($car, false, $removedGalleryImages);
            $newGalleryImages = collect($request->file('gallery_images', []))
                ->map(fn ($file) => $this->storeCarMedia($file, 'cars/gallery'))
                ->values()
                ->all();

            $validated['gallery_images'] = array_values(array_merge($remainingGalleryImages, $newGalleryImages));
        } else {
            if ($removedGalleryImages !== []) {
                $this->deleteStoredGalleryMedia($car, false, $removedGalleryImages);
                $validated['gallery_images'] = $remainingGalleryImages;
            } else {
                unset($validated['gallery_images']);
            }
        }

        $validated['self_drive_available'] = $request->boolean('self_drive_available');
        $validated['driver_available'] = $request->boolean('driver_available');

        $car->fill($validated);
        $car->save();

        return redirect()
            ->route('backoffice.cars')
            ->with('success', 'Mobil berhasil diperbarui.');
    }

    public function updateCarStatus(Request $request, Car $car): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'status' => ['required', Rule::in(CarStatus::values())],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $desiredStatus = CarStatus::tryFrom((string) $validator->validated()['status']);
        if (! $desiredStatus) {
            return back()->with('error', 'Status mobil tidak valid.')->withInput();
        }

        $currentRental = BookingAvailability::currentOngoingRental($car);

        if ($desiredStatus === CarStatus::AVAILABLE) {
            if ($currentRental) {
                return redirect()
                    ->route('backoffice.reservations', ['rental_id' => $currentRental->id])
                    ->with('warning', 'Mobil ini masih sedang dipakai customer hari ini atau belum dikembalikan. Selesaikan rental aktif terlebih dahulu.');
            }

            $car->status = CarStatus::AVAILABLE;
            $car->save();

            return redirect()
                ->route('backoffice.cars')
                ->with('success', 'Mobil berhasil diaktifkan kembali.');
        }

        if ($desiredStatus === CarStatus::UNAVAILABLE) {
            $car->status = CarStatus::UNAVAILABLE;
            $car->save();

            $impactedRentals = BookingAvailability::impactedRentalsForOperationalHold($car, now());
            $warning = null;
            if ($impactedRentals !== []) {
                $formatted = collect($impactedRentals)
                    ->take(3)
                    ->map(fn (Rental $rental) => sprintf(
                        '#%d %s (%s - %s)',
                        $rental->id,
                        $rental->user?->name ?? 'Customer',
                        optional($rental->start_date)->toDateString(),
                        optional($rental->end_date)->toDateString()
                    ))
                    ->implode(', ');

                $warning = 'Ada reservasi aktif/terjadwal yang berpotensi terdampak oleh status maintenance: ' . $formatted . '.';
            }

            $response = redirect()
                ->route('backoffice.cars')
                ->with('success', 'Mobil berhasil diset ke maintenance.');

            if ($warning !== null) {
                $response->with('warning', $warning);
            }

            return $response;
        }

        return back()->with('error', 'Status mobil tidak dapat diubah.')->withInput();
    }

    public function deleteCar(Car $car): RedirectResponse
    {
        $this->deleteStoredCarMedia($car);
        $car->delete();

        return redirect()
            ->route('backoffice.cars')
            ->with('success', 'Mobil berhasil dihapus.');
    }

    public function reservations(Request $request): View
    {
        $filter = $request->query('status_filter');
        $carType = $request->query('car_type');
        $date = $request->query('date');

        $highlightRentalId = (int) $request->query(
            'rental_id',
            $request->query('highlight', 0)
        );

        $query = Rental::query()
            ->with(['user:id,name,phone', 'car']);

        if ($filter === 'waiting_review') {
            $query->where('status', RentalStatus::PENDING_VERIFICATION)
                ->where(
                    'verification_status',
                    \App\Enums\VerificationStatus::NEEDS_REVIEW
                );
        } elseif ($filter === 'upcoming') {
            $query->whereIn('status', BookingAvailability::activeRentalStatuses())
                ->whereDate('start_date', '>', now()->toDateString());
        } elseif ($filter === 'verified_no_pay') {
            $query->where('status', RentalStatus::PENDING_VERIFICATION)
                ->where(
                    'verification_status',
                    \App\Enums\VerificationStatus::VERIFIED
                );
        } elseif ($filter === 'waiting_pay') {
            $query->where('status', RentalStatus::PREPAID);
        } elseif ($filter === 'active') {
            $query->where('status', RentalStatus::ONGOING)
                ->whereDate('start_date', '<=', now()->toDateString());
        } elseif ($filter === 'overdue') {
            $query->where('status', RentalStatus::ONGOING)
                ->whereNull('returned_at')
                ->whereDate('end_date', '<', now());
        } elseif ($filter === 'cancelled_expired') {
            $query->whereIn('status', [
                RentalStatus::CANCELLED,
                RentalStatus::EXPIRED,
            ]);
        } elseif ($filter === 'returned') {
            $query->where('status', RentalStatus::RETURNED);
        }

        if ($carType) {
            $query->whereHas('car', function ($q) use ($carType) {
                $q->where('vehicle_type', $carType);
            });
        }

        if ($date) {
            $query->whereDate('start_date', '<=', $date)
                ->whereDate('end_date', '>=', $date);
        }

        $totalReservations = Rental::count();

        $activeReservations = Rental::where('status', RentalStatus::ONGOING)
            ->count();

        $completedReservations = Rental::where(
            'status',
            RentalStatus::RETURNED
        )->count();

        $needsReviewCount = Rental::where(
            'status',
            RentalStatus::PENDING_VERIFICATION
        )
            ->where(
                'verification_status',
                \App\Enums\VerificationStatus::NEEDS_REVIEW
            )
            ->count();

        $overdueReservations = Rental::where('status', RentalStatus::ONGOING)
            ->whereNull('returned_at')
            ->whereDate('end_date', '<', now())
            ->count();

        $rentals = $query
            ->latest()
            ->paginate(6)
            ->withQueryString()
            ->through(function (Rental $rental) {
                $isUpcoming = $rental->start_date
                    && $rental->start_date->gt(now()->startOfDay())
                    && in_array($rental->status, BookingAvailability::activeRentalStatuses(), true);

                $statusLabel = 'Menunggu';

                if ($isUpcoming) {
                    $statusLabel = 'Akan Datang';
                } elseif ($rental->status === RentalStatus::PENDING_VERIFICATION) {

                    if (
                        $rental->verification_status ===
                        \App\Enums\VerificationStatus::NEEDS_REVIEW
                    ) {
                        $statusLabel = 'Butuh Review Admin';

                    } elseif (
                        $rental->verification_status ===
                        \App\Enums\VerificationStatus::VERIFIED
                    ) {
                        $statusLabel = 'Terverifikasi (Belum Payment)';

                    } else {
                        $statusLabel = 'Menunggu Verifikasi';
                    }

                } elseif ($rental->status === RentalStatus::PREPAID) {

                    $statusLabel = 'Menunggu Pembayaran';

                } elseif ($rental->status === RentalStatus::ONGOING) {

                    $statusLabel = 'Aktif';

                } elseif ($rental->status === RentalStatus::RETURNED) {

                    $statusLabel = 'Selesai';

                } elseif ($rental->status === RentalStatus::CANCELLED) {

                    if (
                        $rental->verification_status ===
                        \App\Enums\VerificationStatus::REJECTED
                    ) {
                        $statusLabel = 'Ditolak';
                    } else {
                        $statusLabel = 'Dibatalkan';
                    }

                } elseif ($rental->status === RentalStatus::EXPIRED) {

                    $statusLabel = 'Expired';
                }

                $isOverdue = $rental->status === RentalStatus::ONGOING 
                    && is_null($rental->returned_at) 
                    && $rental->end_date && $rental->end_date->lt(now()->startOfDay());
                $overdueDays = $isOverdue ? $rental->end_date->diffInDays(now()->startOfDay()) : 0;
                $nextImpactedBooking = null;

                if ($isOverdue && $rental->car_id) {
                    $nextRental = Rental::query()
                        ->where('car_id', $rental->car_id)
                        ->where('id', '!=', $rental->id)
                        ->whereIn('status', BookingAvailability::activeRentalStatuses())
                        ->whereDate('start_date', '>', $rental->end_date->toDateString())
                        ->orderBy('start_date')
                        ->first();

                    if ($nextRental) {
                        $nextImpactedBooking = [
                            'id' => $nextRental->id,
                            'customer_name' => $nextRental->user?->name ?? 'Customer',
                            'start_date' => $nextRental->start_date?->toDateString(),
                            'end_date' => $nextRental->end_date?->toDateString(),
                        ];
                    }
                }

                $postBufferActive = BookingAvailability::hasActivePostBuffer($rental);
                $postBufferEndDate = $rental->status === RentalStatus::RETURNED
                    ? BookingAvailability::returnedPostBufferEndDate($rental)->toDateString()
                    : null;

                return [
                    'id' => $rental->id,

                    'booking_id' => $rental->booking_code ?? $rental->id,

                    'is_overdue' => $isOverdue,
                    'is_upcoming' => $isUpcoming,

                    'overdue_days' => (int) $overdueDays,
                    'next_impacted_booking' => $nextImpactedBooking,

                    'customer_name' => $rental->user?->name,
                    'customer_phone' => $rental->user?->phone,

                    'car_model' => trim(
                        ($rental->car?->brand ?? '') .
                        ' ' .
                        ($rental->car?->name ?? '')
                    ),

                    'start_date' => $rental->start_date?->toDateString(),

                    'end_date' => $rental->end_date?->toDateString(),

                    'total_price' => (int) ($rental->total_price ?? 0),

                    'status' => $statusLabel,

                    'status_raw' => $rental->status->value,

                    'verification_status' => $rental->verification_status
                        ? $rental->verification_status->value
                        : null,
                    'post_buffer_active' => $postBufferActive,
                    'post_buffer_end_date' => $postBufferEndDate,
                    'post_buffer_released_at' => $rental->post_buffer_released_at?->toDateTimeString(),
                    'release_post_buffer_url' => route('backoffice.reservations.release-post-buffer', ['rental' => $rental->id]),

                    'ktp_url' => $rental->ktp_path
                        ? route(
                            'backoffice.rentals.document',
                            [
                                'rental' => $rental->id,
                                'type' => 'ktp',
                            ]
                        )
                        : null,

                    'selfie_url' => $rental->selfie_path
                        ? route(
                            'backoffice.rentals.document',
                            [
                                'rental' => $rental->id,
                                'type' => 'selfie',
                            ]
                        )
                        : null,

                    'car_details' => $rental->car
                        ? [
                            'brand' => $rental->car->brand,
                            'name' => $rental->car->name,
                            'license_plate' => $rental->car->license_plate,
                            'transmission' => str(
                                $rental->car->transmission->value
                            )->headline(),
                            'seat_count' => $rental->car->seat_count . ' Kursi',
                            'year' => $rental->car->year,
                            'cc' => number_format($rental->car->cc) . ' cc',
                            'vehicle_type' => str(
                                $rental->car->vehicle_type->value
                            )->headline(),
                            'color' => $rental->car->color,
                            'daily_rate' => 'Rp ' .
                                number_format(
                                    $rental->car->daily_rate,
                                    0,
                                    ',',
                                    '.'
                                ),
                        ]
                        : null,
                ];
            });

        $customers = User::query()
            ->where('role', '!=', User::ROLE_ADMIN)
            ->orderBy('name')
            ->get()
            ->map(fn (User $u) => [
                'id' => $u->id,
                'name' => $u->name,
                'phone' => $u->phone,
            ])
            ->all();

        $availableCars = Car::query()
            ->where('status', CarStatus::AVAILABLE)
            ->get()
            ->map(fn (Car $c) => [
                'id' => $c->id,
                'brand' => $c->brand,
                'model' => $c->name ?? $c->model ?? '',
            ])
            ->all();

        $vehicleTypes = VehicleType::cases();

        return view('backoffice.reservations', [
            'admin' => request()->user(),

            'rentals' => $rentals,

            'pagination' => $this->paginationWindow(
                $rentals->currentPage(),
                $rentals->lastPage()
            ),

            'customers' => $customers,

            'availableCars' => $availableCars,

            'vehicleTypes' => $vehicleTypes,

            'summary' => [
                'total' => $totalReservations,
                'active' => $activeReservations,
                'completed' => $completedReservations,
                'needs_review' => $needsReviewCount,
                'overdue' => $overdueReservations,
            ],

            'current_filter' => $filter,

            'highlightRentalId' => $highlightRentalId > 0
                ? $highlightRentalId
                : null,
        ]);
    }

    public function storeReservation(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'car_id' => ['required', 'integer', 'exists:cars,id'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'type' => ['nullable', Rule::in(RentalType::values())],
            'ktp' => ['nullable', 'file', 'image', 'max:5120'],
            'selfie' => ['nullable', 'file', 'image', 'max:5120'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $data = $validator->validated();

        try {
            $rental = DB::transaction(function () use ($request, $data) {
                $car = Car::query()->lockForUpdate()->findOrFail($data['car_id']);
                if ($car->status !== CarStatus::AVAILABLE) {
                    throw new \RuntimeException(BookingAvailability::unavailabilityMessage('operational_unavailable'));
                }

                $ktpPath = null;
                $selfiePath = null;
                $cloudinary = app(CloudinaryMediaService::class);
                if ($request->hasFile('ktp')) {
                    $ktpPath = $cloudinary->configured()
                        ? $cloudinary->uploadPrivate($request->file('ktp'), 'rentals/ktp')
                        : Storage::disk('local')->putFile('ktp', $request->file('ktp'));
                }
                if ($request->hasFile('selfie')) {
                    $selfiePath = $cloudinary->configured()
                        ? $cloudinary->uploadPrivate($request->file('selfie'), 'rentals/selfie')
                        : Storage::disk('local')->putFile('selfie', $request->file('selfie'));
                }

                $start = Carbon::parse($data['start_date']);
                $end = Carbon::parse($data['end_date']);
                $availability = BookingAvailability::checkCarAvailability($car, $start, $end);
                if (! $availability['available']) {
                    throw new \RuntimeException(BookingAvailability::unavailabilityMessage($availability['reason'] ?? 'overlap'));
                }

                $days = max(1, $start->diffInDays($end));

                $rentCost = (int) ($car->daily_rate ?? 0) * $days;
                $serviceCost = 100000; // default service fee
                $totalPrice = $rentCost + $serviceCost;

                $rental = Rental::create([
                    'user_id' => $data['user_id'],
                    'car_id' => $car->id,
                    'start_date' => $start,
                    'end_date' => $end,
                    'total_price' => $totalPrice,
                    'status' => RentalStatus::PREPAID,
                    'type' => $data['type'] ?? RentalType::SELF_DRIVE,
                    'prepaid_expires_at' => now()->addDay(),
                    'ktp_path' => $ktpPath,
                    'selfie_path' => $selfiePath,
                    'buffer_before_days' => BookingAvailability::DEFAULT_BUFFER_BEFORE_DAYS,
                    'buffer_after_days' => BookingAvailability::DEFAULT_BUFFER_AFTER_DAYS,
                ]);

                return $rental;
            });

            return redirect()->route('backoffice.reservations')->with('success', 'Reservasi berhasil dibuat.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage() ?: 'Gagal membuat reservasi.')->withInput();
        }
    }

    private function paginationWindow(int $currentPage, int $lastPage): array
    {
        if ($lastPage <= 5) {
            return range(1, max(1, $lastPage));
        }

        return [1, 2, 3, '...', $lastPage];
    }

    private function carStatusMeta(Car $car, ?Rental $currentRental = null, ?Rental $nextRental = null): array
    {
        if ($car->status === CarStatus::UNAVAILABLE) {
            $note = 'Mobil tidak tersedia secara operasional untuk pemeriksaan, perbaikan, atau manual hold.';
            if ($nextRental) {
                $note .= ' Booking terdekat #' . $nextRental->id . ' mulai ' . $nextRental->start_date?->toDateString() . '.';
            }

            return [
                'label' => 'MAINTENANCE',
                'tone' => 'red',
                'note' => $note,
                'operational_status' => 'Tidak tersedia operasional',
                'schedule_status' => $nextRental ? 'Ada booking terjadwal' : 'Tidak ada booking terdekat',
                'locking_rental_id' => $nextRental?->id,
                'can_change_status' => true,
                'action_label' => 'Aktifkan',
                'action_kind' => 'toggle',
                'action_value' => CarStatus::AVAILABLE->value,
                'action_class' => 'status-action status-action-available',
            ];
        }

        if ($currentRental) {
            $isOverdue = $currentRental->end_date && $currentRental->end_date->lt(now()->startOfDay());

            return [
                'label' => $isOverdue ? 'RENTAL TERLAMBAT' : 'DISEWA HARI INI',
                'tone' => 'blue',
                'note' => $isOverdue
                    ? 'Rental berjalan sudah melewati tanggal selesai dan mobil belum dikembalikan.'
                    : 'Status operasional mobil tersedia, tetapi unit sedang dipakai customer pada rental berjalan.',
                'operational_status' => 'Tersedia operasional',
                'schedule_status' => 'Sedang dipakai booking #' . $currentRental->id,
                'locking_rental_id' => $currentRental->id,
                'can_change_status' => false,
                'action_label' => 'Lihat Reservasi',
                'action_kind' => 'view_reservation',
                'action_value' => null,
                'action_class' => 'status-action status-action-rented',
            ];
        }

        $note = 'Mobil siap menerima booking selama tidak bentrok dengan reservasi aktif dan buffer operasional.';
        if ($nextRental) {
            $note = 'Status operasional tersedia. Booking terdekat #' . $nextRental->id . ' mulai ' . $nextRental->start_date?->toDateString() . '.';
        }

        return [
            'label' => 'Tersedia Hari Ini',
            'tone' => 'green',
            'note' => $note,
            'operational_status' => 'Tersedia operasional',
            'schedule_status' => $nextRental ? 'Ada booking terjadwal' : 'Belum ada booking terjadwal',
            'locking_rental_id' => $nextRental?->id,
            'can_change_status' => true,
            'action_label' => 'Ubah ke Maintenance',
            'action_kind' => 'toggle',
            'action_value' => CarStatus::UNAVAILABLE->value,
            'action_class' => 'status-action status-action-maintenance',
        ];
    }

    private function resolveCarImageUrl(?string $image): ?string
    {
        if (! $image) {
            return null;
        }

        $cloudinaryUrl = app(CloudinaryMediaService::class)->url($image);
        if ($cloudinaryUrl) {
            return $cloudinaryUrl;
        }

        if (str_starts_with($image, 'http://') || str_starts_with($image, 'https://') || str_starts_with($image, '/')) {
            return $image;
        }

        if (Storage::disk('public')->exists($image)) {
            return Storage::url($image);
        }

        return asset($image);
    }

    private function storeCarMedia(\Illuminate\Http\UploadedFile $file, string $directory): string
    {
        $cloudinary = app(CloudinaryMediaService::class);

        if ($cloudinary->configured()) {
            return $cloudinary->upload($file, $directory);
        }

        return $file->store($directory, 'public');
    }

    private function deleteStoredCarMedia(Car $car, bool $includeGallery = true): void
    {
        $paths = [$car->image];

        if ($includeGallery) {
            $paths = array_merge($paths, is_array($car->gallery_images) ? $car->gallery_images : []);
        }

        foreach (array_filter($paths) as $path) {
            if (! is_string($path)) {
                continue;
            }

            if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://') || str_starts_with($path, '/')) {
                continue;
            }

            if (app(CloudinaryMediaService::class)->isCloudinaryPath($path)) {
                app(CloudinaryMediaService::class)->delete($path);
                continue;
            }

            Storage::disk('public')->delete($path);
        }
    }

    private function deleteStoredGalleryMedia(Car $car, bool $includeImage = true, array $specificGalleryPaths = []): void
    {
        $galleryPaths = is_array($car->gallery_images) ? $car->gallery_images : [];
        $paths = $specificGalleryPaths !== []
            ? $specificGalleryPaths
            : ($includeImage ? array_merge([$car->image], $galleryPaths) : []);

        foreach (array_filter($paths) as $path) {
            if (! is_string($path)) {
                continue;
            }

            if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://') || str_starts_with($path, '/')) {
                continue;
            }

            if (app(CloudinaryMediaService::class)->isCloudinaryPath($path)) {
                app(CloudinaryMediaService::class)->delete($path);
                continue;
            }

            Storage::disk('public')->delete($path);
        }
    }

    public function reports(Request $request)
    {
        // Validate export parameter if present
        if ($request->has('export')) {
            $request->validate([
                'export' => 'in:csv,pdf',
            ]);
        }
        
        $tab = $request->query('tab', 'overview');
        $reportPeriod = $this->resolveReportFilterPeriod($request);
        $filterMode = $reportPeriod['filterMode'];
        $filterDate = $reportPeriod['filterDate'];
        $filterMonth = $reportPeriod['filterMonth'];
        $filterYear = $reportPeriod['filterYear'];
        $filterStart = $reportPeriod['filterStart'];
        $filterEnd = $reportPeriod['filterEnd'];
        $periodStart = $reportPeriod['periodStart'];
        $periodEnd = $reportPeriod['periodEnd'];
        $previousPeriodStart = $reportPeriod['previousPeriodStart'];
        $previousPeriodEnd = $reportPeriod['previousPeriodEnd'];

        // Prepare Laporan Overview data if tab is overview
        $overviewSummary = [];
        $statusDistribution = collect();
        $serviceTypeDistribution = collect();
        $topCars = collect();
        $fleetOccupancy = [];
        $chartBookingsBreakdown = collect();
        $chartRevenueBreakdown = collect();
        $paidTransactions = 0;

        if ($tab === 'overview') {
            $totalRentalsCurrentQuery = Rental::query();
            $totalRentalsPreviousQuery = Rental::query();
            $revenuePaidCurrentQuery = PaymentHistory::query()->where('status', PaymentStatus::PAID);
            $revenuePaidPreviousQuery = PaymentHistory::query()->where('status', PaymentStatus::PAID);
            $successRentalsCurrentQuery = PaymentHistory::query()->where('status', PaymentStatus::PAID);
            $successRentalsPreviousQuery = PaymentHistory::query()->where('status', PaymentStatus::PAID);
            $failedRentalsCurrentQuery = Rental::query()->whereIn('status', [RentalStatus::CANCELLED, RentalStatus::EXPIRED]);
            $failedRentalsPreviousQuery = Rental::query()->whereIn('status', [RentalStatus::CANCELLED, RentalStatus::EXPIRED]);

            $this->applyReportFilter($totalRentalsCurrentQuery, $periodStart, $periodEnd);
            $this->applyReportFilter($totalRentalsPreviousQuery, $previousPeriodStart, $previousPeriodEnd);
            $this->applyReportFilter($revenuePaidCurrentQuery, $periodStart, $periodEnd);
            $this->applyReportFilter($revenuePaidPreviousQuery, $previousPeriodStart, $previousPeriodEnd);
            $this->applyReportFilter($successRentalsCurrentQuery, $periodStart, $periodEnd);
            $this->applyReportFilter($successRentalsPreviousQuery, $previousPeriodStart, $previousPeriodEnd);
            $this->applyReportFilter($failedRentalsCurrentQuery, $periodStart, $periodEnd);
            $this->applyReportFilter($failedRentalsPreviousQuery, $previousPeriodStart, $previousPeriodEnd);

            $totalRentals = $totalRentalsCurrentQuery->count();
            $previousTotalRentals = $totalRentalsPreviousQuery->count();

            $successRentals = (clone $successRentalsCurrentQuery)
                ->distinct()
                ->count('rental_id');
            $previousSuccessRentals = (clone $successRentalsPreviousQuery)
                ->distinct()
                ->count('rental_id');

            $failedRentals = $failedRentalsCurrentQuery->count();
            $previousFailedRentals = $failedRentalsPreviousQuery->count();

            $revenuePaid = (int) $revenuePaidCurrentQuery->sum('amount');
            $paidTransactions = (clone $revenuePaidCurrentQuery)->count();
            $previousRevenuePaid = (int) $revenuePaidPreviousQuery->sum('amount');

            $successRate = $totalRentals > 0 ? round(($successRentals / $totalRentals) * 100, 1) : 0.0;
            $previousSuccessRate = $previousTotalRentals > 0 ? round(($previousSuccessRentals / $previousTotalRentals) * 100, 1) : 0.0;
            $failedRate = $totalRentals > 0 ? round(($failedRentals / $totalRentals) * 100, 1) : 0.0;
            $previousFailedRate = $previousTotalRentals > 0 ? round(($previousFailedRentals / $previousTotalRentals) * 100, 1) : 0.0;

            $overviewSummary = [
                'total_cars' => Car::count(),
                'total_rentals' => $totalRentals,
                'previous_total_rentals' => $previousTotalRentals,
                'total_users' => User::count(),
                'revenue_paid' => $revenuePaid,
                'paid_transactions' => $paidTransactions,
                'previous_revenue_paid' => $previousRevenuePaid,
                'success_bookings' => $successRentals,
                'previous_success_bookings' => $previousSuccessRentals,
                'failed_bookings' => $failedRentals,
                'previous_failed_bookings' => $previousFailedRentals,
                'success_rate' => $successRate,
                'previous_success_rate' => $previousSuccessRate,
                'failed_rate' => $failedRate,
                'previous_failed_rate' => $previousFailedRate,
                'total_rentals_growth' => $this->buildRelativeGrowthText($totalRentals, $previousTotalRentals),
                'revenue_paid_growth' => $this->buildRelativeGrowthText($revenuePaid, $previousRevenuePaid),
                'success_rate_growth' => $this->buildPointChangeText($successRate, $previousSuccessRate, false),
                'failed_rate_growth' => $this->buildPointChangeText($failedRate, $previousFailedRate, true),
            ];

            $statusDistributionQuery = Rental::query();
            $this->applyReportFilter($statusDistributionQuery, $periodStart, $periodEnd);
            $statusDistribution = $statusDistributionQuery
                ->groupBy('status')
                ->select('status', DB::raw('count(*) as total'))
                ->get()
                ->map(fn($item) => [
                    'label' => match($item->status) {
                        RentalStatus::PENDING_VERIFICATION => 'Verifikasi',
                        RentalStatus::PREPAID => 'Prepaid',
                        RentalStatus::ONGOING => 'Aktif',
                        RentalStatus::RETURNED => 'Selesai',
                        RentalStatus::CANCELLED => 'Batal',
                        RentalStatus::EXPIRED => 'Expired',
                        default => $item->status->value
                    },
                    'value' => $item->total
                ]);

            $serviceTypeDistributionQuery = Rental::query();
            $this->applyReportFilter($serviceTypeDistributionQuery, $periodStart, $periodEnd);
            $serviceTypeDistribution = $serviceTypeDistributionQuery
                ->groupBy('type')
                ->select('type', DB::raw('count(*) as total'))
                ->get()
                ->map(fn($item) => [
                    'label' => match($item->type) {
                        RentalType::SELF_DRIVE => 'Lepas Kunci',
                        RentalType::WITH_DRIVER => 'Dengan Driver',
                        default => $item->type->value
                    },
                    'value' => $item->total
                ]);

            $topCarsQuery = Car::query()
                ->withCount(['rentals' => function ($q) use ($periodStart, $periodEnd) {
                    $this->applyReportFilter($q, $periodStart, $periodEnd);
                }])
                ->withSum(['rentals' => function ($q) use ($periodStart, $periodEnd) {
                    $this->applyReportFilter($q, $periodStart, $periodEnd);
                }], 'total_price')
                ->orderByDesc('rentals_count')
                ->limit(5);

            $topCars = $topCarsQuery
                ->get()
                ->map(fn($car) => [
                    'name' => trim($car->brand . ' ' . $car->name),
                    'count' => $car->rentals_count,
                    'revenue' => (int) ($car->rentals_sum_total_price ?? 0),
                ]);

            $fleetOccupancy = [
                'total' => Car::count(),
                'available' => Car::where('status', CarStatus::AVAILABLE)->count(),
                'unavailable' => Car::where('status', CarStatus::UNAVAILABLE)->count(),
            ];
        }

        // Prepare Laporan Armada data if tab is fleet OR we are exporting fleet
        $carStats = collect();
        if ($tab === 'fleet') {
            $cars = Car::all();
            $paymentsQuery = PaymentHistory::query()
                ->with('rental')
                ->where('status', PaymentStatus::PAID);
            $this->applyReportFilter($paymentsQuery, $periodStart, $periodEnd);
            $payments = $paymentsQuery->get();

            $rentalsQuery = Rental::query();
            $this->applyReportFilter($rentalsQuery, $periodStart, $periodEnd);
            $rentals = $rentalsQuery
                ->get();

            $carStats = $cars->map(function ($car) use ($rentals, $payments) {
                $carRentals = $rentals->where('car_id', $car->id);
                $carPayments = $payments->where('rental.car_id', $car->id);
                $latestRental = $carRentals->sortByDesc('created_at')->first();

                return [
                    'id' => $car->id,
                    'brand' => $car->brand,
                    'name' => $car->name,
                    'license_plate' => $car->license_plate,
                    'vehicle_type' => $car->vehicle_type ? $car->vehicle_type->value : '-',
                    'transmission' => $car->transmission ? $car->transmission->value : '-',
                    'status' => $car->status ? $car->status->value : '-',
                    'rentals_count' => $carRentals->count(),
                    'total_revenue' => (int) $carPayments->sum('amount'),
                    'last_rented' => $latestRental ? $latestRental->created_at->toDateString() : '-',
                ];
            });
        }

        // Initialize variables for view rendering
        $summary = [];
        $data = null;
        $exportRows = collect();

        if ($tab === 'revenue') {
            $baseQuery = PaymentHistory::query()
                ->with(['rental.user', 'rental.car'])
                ->where('status', PaymentStatus::PAID);
            $this->applyReportFilter($baseQuery, $periodStart, $periodEnd);

            $totalRevenue = (int) $baseQuery->sum('amount');
            $totalTransactions = $baseQuery->count();
            $avgTransaction = $totalTransactions > 0 ? (int) ($totalRevenue / $totalTransactions) : 0;

            $summary = [
                'total_revenue' => $totalRevenue,
                'total_transactions' => $totalTransactions,
                'avg_transaction' => $avgTransaction,
            ];

            $data = $baseQuery->latest()->paginate(10)->withQueryString();
            $exportRows = (clone $baseQuery)->latest()->get();

        } elseif ($tab === 'reservation') {
            $baseQuery = Rental::query()
                ->with(['user', 'car']);
            $this->applyReportFilter($baseQuery, $periodStart, $periodEnd);

            $totalReservations = $baseQuery->count();
            $pending = (clone $baseQuery)->where('status', RentalStatus::PENDING_VERIFICATION)->count();
            $prepaid = (clone $baseQuery)->where('status', RentalStatus::PREPAID)->count();
            $ongoing = (clone $baseQuery)->where('status', RentalStatus::ONGOING)->count();
            $returned = (clone $baseQuery)->where('status', RentalStatus::RETURNED)->count();
            $cancelled = (clone $baseQuery)->where('status', RentalStatus::CANCELLED)->count();
            $expired = (clone $baseQuery)->where('status', RentalStatus::EXPIRED)->count();

            $cancellationRate = $totalReservations > 0 ? round(($cancelled / $totalReservations) * 100, 1) : 0;
            $avgDuration = (clone $baseQuery)
                ->whereNotNull('start_date')
                ->whereNotNull('end_date')
                ->get()
                ->avg(fn ($r) => Carbon::parse($r->start_date)->diffInDays(Carbon::parse($r->end_date)) + 1) ?? 0;

            $summary = [
                'total_reservations' => $totalReservations,
                'pending' => $pending,
                'prepaid' => $prepaid,
                'ongoing' => $ongoing,
                'returned' => $returned,
                'cancelled' => $cancelled,
                'expired' => $expired,
                'cancellation_rate' => $cancellationRate,
                'avg_duration' => round($avgDuration, 1),
            ];

            $data = $baseQuery->latest()->paginate(10)->withQueryString();
            $exportRows = (clone $baseQuery)->latest()->get();

        } elseif ($tab === 'fleet') {
            $totalFleet = Car::count();
            $available = Car::where('status', CarStatus::AVAILABLE)->count();
            $unavailable = Car::where('status', CarStatus::UNAVAILABLE)->count();

            $topRented = $carStats->sortByDesc('rentals_count')->first();
            $topRentedName = ($topRented && $topRented['rentals_count'] > 0) 
                ? trim($topRented['brand'] . ' ' . $topRented['name']) . ' (' . $topRented['rentals_count'] . 'x)' 
                : '-';

            $topRevenue = $carStats->sortByDesc('total_revenue')->first();
            $topRevenueName = ($topRevenue && $topRevenue['total_revenue'] > 0) 
                ? trim($topRevenue['brand'] . ' ' . $topRevenue['name']) . ' (Rp ' . number_format($topRevenue['total_revenue'], 0, ',', '.') . ')' 
                : '-';

            $summary = [
                'total_fleet' => $totalFleet,
                'available' => $available,
                'unavailable' => $unavailable,
                'top_rented' => $topRentedName,
                'top_revenue' => $topRevenueName,
            ];

            // Manual pagination for computed collection
            $perPage = 10;
            $currentPage = \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPage();
            $currentItems = $carStats->slice(($currentPage - 1) * $perPage, $perPage)->values();
            $data = new \Illuminate\Pagination\LengthAwarePaginator(
                $currentItems,
                $carStats->count(),
                $perPage,
                $currentPage,
                ['path' => \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPath()]
            );
            $data->withQueryString();
            $exportRows = $carStats->values();
        }

        // Prepare chart and featured fleet data for visual analytics
        $chartStart = $periodStart ? $periodStart->copy() : Carbon::now()->subMonthsNoOverflow(5)->startOfMonth();
        $chartEnd = $periodEnd ? $periodEnd->copy() : Carbon::now()->endOfDay();
        $chartMode = $filterMode === 'day'
            ? 'hour'
            : ((max(1, $chartStart->copy()->startOfDay()->diffInDays($chartEnd->copy()->startOfDay()) + 1) <= 31) ? 'day' : 'month');
        if ($chartMode === 'hour') {
            $chartBuckets = collect(range(0, 23))->map(fn (int $hour) => $chartStart->copy()->startOfDay()->addHours($hour));
            $periodRentals = Rental::query()
                ->whereBetween('created_at', [$chartStart->copy()->startOfDay(), $chartEnd->copy()->endOfDay()])
                ->get();
            $periodPayments = PaymentHistory::query()
                ->where('status', PaymentStatus::PAID)
                ->whereBetween('created_at', [$chartStart->copy()->startOfDay(), $chartEnd->copy()->endOfDay()])
                ->get();

            $rentalCounts = $periodRentals
                ->groupBy(fn (Rental $rental) => $rental->created_at->format('H'))
                ->map->count();

            $revenueByPeriod = $periodPayments
                ->groupBy(fn (PaymentHistory $payment) => $payment->created_at->format('H'))
                ->map(fn ($payments) => $payments->sum('amount'));
            $successCounts = $periodPayments
                ->groupBy(fn (PaymentHistory $payment) => $payment->created_at->format('H'))
                ->map->count();
            $failedCounts = $periodRentals
                ->filter(fn (Rental $rental) => in_array($rental->status, [RentalStatus::CANCELLED, RentalStatus::EXPIRED], true))
                ->groupBy(fn (Rental $rental) => $rental->created_at->format('H'))
                ->map->count();

            $chartRentals = $chartBuckets->map(fn (Carbon $date) => [
                'label' => $date->format('H:00'),
                'value' => (int) ($rentalCounts[$date->format('H')] ?? 0),
            ]);

            $chartRevenue = $chartBuckets->map(fn (Carbon $date) => [
                'label' => $date->format('H:00'),
                'value' => (int) ($revenueByPeriod[$date->format('H')] ?? 0),
            ]);
            $chartBookingsBreakdown = $chartBuckets->map(fn (Carbon $date) => [
                'label' => $date->format('H:00'),
                'total' => (int) ($rentalCounts[$date->format('H')] ?? 0),
                'success' => (int) ($successCounts[$date->format('H')] ?? 0),
                'failed' => (int) ($failedCounts[$date->format('H')] ?? 0),
            ]);
            $chartRevenueBreakdown = $chartBuckets->map(fn (Carbon $date) => [
                'label' => $date->format('H:00'),
                'revenue' => (int) ($revenueByPeriod[$date->format('H')] ?? 0),
                'transactions' => (int) ($successCounts[$date->format('H')] ?? 0),
            ]);
        } elseif ($chartMode === 'day') {
            $chartBuckets = collect();
            $cursor = $chartStart->copy()->startOfDay();
            while ($cursor->lte($chartEnd->copy()->endOfDay())) {
                $chartBuckets->push($cursor->copy());
                $cursor->addDay();
            }
            $periodRentals = Rental::query()
                ->whereBetween('created_at', [$chartStart->copy()->startOfDay(), $chartEnd->copy()->endOfDay()])
                ->get();
            $periodPayments = PaymentHistory::query()
                ->where('status', PaymentStatus::PAID)
                ->whereBetween('created_at', [$chartStart->copy()->startOfDay(), $chartEnd->copy()->endOfDay()])
                ->get();

            $rentalCounts = $periodRentals
                ->groupBy(fn (Rental $rental) => $rental->created_at->format('Y-m-d'))
                ->map->count();

            $revenueByPeriod = $periodPayments
                ->groupBy(fn (PaymentHistory $payment) => $payment->created_at->format('Y-m-d'))
                ->map(fn ($payments) => $payments->sum('amount'));
            $successCounts = $periodPayments
                ->groupBy(fn (PaymentHistory $payment) => $payment->created_at->format('Y-m-d'))
                ->map->count();
            $failedCounts = $periodRentals
                ->filter(fn (Rental $rental) => in_array($rental->status, [RentalStatus::CANCELLED, RentalStatus::EXPIRED], true))
                ->groupBy(fn (Rental $rental) => $rental->created_at->format('Y-m-d'))
                ->map->count();

            $chartRentals = $chartBuckets->map(fn (Carbon $date) => [
                'label' => $date->translatedFormat('d M'),
                'value' => (int) ($rentalCounts[$date->format('Y-m-d')] ?? 0),
            ]);

            $chartRevenue = $chartBuckets->map(fn (Carbon $date) => [
                'label' => $date->translatedFormat('d M'),
                'value' => (int) ($revenueByPeriod[$date->format('Y-m-d')] ?? 0),
            ]);
            $chartBookingsBreakdown = $chartBuckets->map(fn (Carbon $date) => [
                'label' => $date->translatedFormat('d M'),
                'total' => (int) ($rentalCounts[$date->format('Y-m-d')] ?? 0),
                'success' => (int) ($successCounts[$date->format('Y-m-d')] ?? 0),
                'failed' => (int) ($failedCounts[$date->format('Y-m-d')] ?? 0),
            ]);
            $chartRevenueBreakdown = $chartBuckets->map(fn (Carbon $date) => [
                'label' => $date->translatedFormat('d M'),
                'revenue' => (int) ($revenueByPeriod[$date->format('Y-m-d')] ?? 0),
                'transactions' => (int) ($successCounts[$date->format('Y-m-d')] ?? 0),
            ]);
        } else {
            $chartStartMonth = $chartStart->copy()->startOfMonth();
            $chartEndMonth = $chartEnd->copy()->startOfMonth();
            $chartBuckets = collect();
            $cursor = $chartStartMonth->copy();
            while ($cursor->lte($chartEndMonth)) {
                $chartBuckets->push($cursor->copy());
                $cursor->addMonthNoOverflow();
            }
            $periodRentals = Rental::query()
                ->whereBetween('created_at', [$chartStart->copy()->startOfDay(), $chartEnd->copy()->endOfDay()])
                ->get();
            $periodPayments = PaymentHistory::query()
                ->where('status', PaymentStatus::PAID)
                ->whereBetween('created_at', [$chartStart->copy()->startOfDay(), $chartEnd->copy()->endOfDay()])
                ->get();

            $rentalCounts = $periodRentals
                ->groupBy(fn (Rental $rental) => $rental->created_at->format('Y-m'))
                ->map->count();

            $revenueByPeriod = $periodPayments
                ->groupBy(fn (PaymentHistory $payment) => $payment->created_at->format('Y-m'))
                ->map(fn ($payments) => $payments->sum('amount'));
            $successCounts = $periodPayments
                ->groupBy(fn (PaymentHistory $payment) => $payment->created_at->format('Y-m'))
                ->map->count();
            $failedCounts = $periodRentals
                ->filter(fn (Rental $rental) => in_array($rental->status, [RentalStatus::CANCELLED, RentalStatus::EXPIRED], true))
                ->groupBy(fn (Rental $rental) => $rental->created_at->format('Y-m'))
                ->map->count();

            $chartRentals = $chartBuckets->map(fn (Carbon $date) => [
                'label' => $date->translatedFormat('M Y'),
                'value' => (int) ($rentalCounts[$date->format('Y-m')] ?? 0),
            ]);

            $chartRevenue = $chartBuckets->map(fn (Carbon $date) => [
                'label' => $date->translatedFormat('M Y'),
                'value' => (int) ($revenueByPeriod[$date->format('Y-m')] ?? 0),
            ]);
            $chartBookingsBreakdown = $chartBuckets->map(fn (Carbon $date) => [
                'label' => $date->translatedFormat('M Y'),
                'total' => (int) ($rentalCounts[$date->format('Y-m')] ?? 0),
                'success' => (int) ($successCounts[$date->format('Y-m')] ?? 0),
                'failed' => (int) ($failedCounts[$date->format('Y-m')] ?? 0),
            ]);
            $chartRevenueBreakdown = $chartBuckets->map(fn (Carbon $date) => [
                'label' => $date->translatedFormat('M Y'),
                'revenue' => (int) ($revenueByPeriod[$date->format('Y-m')] ?? 0),
                'transactions' => (int) ($successCounts[$date->format('Y-m')] ?? 0),
            ]);
        }

        $topCar = Car::query()
            ->withCount(['rentals' => function ($q) use ($periodStart, $periodEnd) {
                $this->applyReportFilter($q, $periodStart, $periodEnd);
            }])
            ->withSum(['rentals' => function ($q) use ($periodStart, $periodEnd) {
                $this->applyReportFilter($q, $periodStart, $periodEnd);
            }], 'total_price')
            ->orderByDesc('rentals_count')
            ->orderByDesc('rentals_sum_total_price')
            ->first();

        $featuredCar = [
            'name' => trim(($topCar?->brand ?? '').' '.($topCar?->name ?? '')) ?: 'Belum ada armada unggulan',
            'description' => $topCar?->description ?? 'Tambahkan transaksi rental untuk melihat armada dengan performa terbaik.',
            'revenue' => (int) ($topCar?->rentals_sum_total_price ?? 0),
            'rentals_count' => (int) ($topCar?->rentals_count ?? 0),
            'image_url' => $this->resolveCarImageUrl($topCar?->image),
        ];

        $reportTabLabels = $this->reportTabLabels();
        $reportTitle = $reportTabLabels[$tab] ?? 'Overview';
        $reportPeriodLabel = $this->formatReportPeriodLabel($filterMode, $periodStart, $periodEnd);
        $pdfCharts = $this->buildReportPdfCharts(
            $tab,
            $chartRentals,
            $chartRevenue,
            $statusDistribution,
            $serviceTypeDistribution,
            $carStats,
            $fleetOccupancy,
            $summary
        );

        if ($request->query('export') === 'csv') {
            // Validate that there is data to export
            if ($exportRows->isEmpty()) {
                return back()->with('warning', 'Tidak ada data untuk di-export pada periode ini.');
            }
            
            $filename = 'laporan_' . str($reportTitle)->slug('_') . '_' . $periodStart->format('Ymd') . '_' . $periodEnd->format('Ymd') . '.csv';
            $headers = [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            ];

            return response()->stream(function () use (
                $tab,
                $reportTitle,
                $reportPeriodLabel,
                $overviewSummary,
                $statusDistribution,
                $serviceTypeDistribution,
                $topCars,
                $fleetOccupancy,
                $chartRentals,
                $chartRevenue,
                $exportRows
            ) {
                $handle = fopen('php://output', 'w');
                fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

                fputcsv($handle, [$reportTitle]);
                fputcsv($handle, ['Periode', $reportPeriodLabel]);
                fputcsv($handle, []);

                if ($tab === 'overview') {
                    fputcsv($handle, ['KPI Ringkas']);
                    fputcsv($handle, ['Total Reservasi', (int) ($overviewSummary['total_rentals'] ?? 0)]);
                    fputcsv($handle, ['Pendapatan Masuk', (int) ($overviewSummary['revenue_paid'] ?? 0)]);
                    fputcsv($handle, ['Booking Berhasil', (int) ($overviewSummary['success_bookings'] ?? 0)]);
                    fputcsv($handle, ['Booking Gagal', (int) ($overviewSummary['failed_bookings'] ?? 0)]);
                    fputcsv($handle, []);

                    fputcsv($handle, ['Distribusi Status Rental']);
                    fputcsv($handle, ['Status', 'Jumlah']);
                    foreach ($statusDistribution as $item) {
                        fputcsv($handle, [$item['label'], $item['value']]);
                    }
                    fputcsv($handle, []);

                    fputcsv($handle, ['Reservasi Berdasarkan Tipe Layanan']);
                    fputcsv($handle, ['Layanan', 'Jumlah']);
                    foreach ($serviceTypeDistribution as $item) {
                        fputcsv($handle, [$item['label'], $item['value']]);
                    }
                    fputcsv($handle, []);

                    fputcsv($handle, ['Top Armada Terpopuler']);
                    fputcsv($handle, ['Armada', 'Jumlah Reservasi', 'Pendapatan']);
                    foreach ($topCars as $car) {
                        fputcsv($handle, [$car['name'], $car['count'], $car['revenue']]);
                    }
                    fputcsv($handle, []);

                    fputcsv($handle, ['Status Ketersediaan Armada']);
                    fputcsv($handle, ['Total Armada', (int) ($fleetOccupancy['total'] ?? 0)]);
                    fputcsv($handle, ['Tersedia', (int) ($fleetOccupancy['available'] ?? 0)]);
                    fputcsv($handle, ['Sibuk', (int) ($fleetOccupancy['unavailable'] ?? 0)]);
                    fputcsv($handle, []);

                    fputcsv($handle, ['Tren Reservasi']);
                    fputcsv($handle, ['Periode', 'Reservasi']);
                    foreach ($chartRentals as $point) {
                        fputcsv($handle, [$point['label'], $point['value']]);
                    }
                    fputcsv($handle, []);

                    fputcsv($handle, ['Tren Pendapatan']);
                    fputcsv($handle, ['Periode', 'Pendapatan']);
                    foreach ($chartRevenue as $point) {
                        fputcsv($handle, [$point['label'], $point['value']]);
                    }
                } elseif ($tab === 'revenue') {
                    fputcsv($handle, ['Tanggal Pembayaran', 'Customer', 'Mobil', 'Plat Nomor', 'Tipe Rental', 'Provider Pembayaran', 'Status Pembayaran', 'Amount']);
                    foreach ($exportRows as $history) {
                        fputcsv($handle, [
                            $history->created_at->toDateString(),
                            $history->rental?->user?->name ?? '-',
                            trim(($history->rental?->car?->brand ?? '') . ' ' . ($history->rental?->car?->name ?? '')),
                            $history->rental?->car?->license_plate ?? '-',
                            $history->rental?->type === RentalType::SELF_DRIVE ? 'Self Drive' : 'With Driver',
                            strtoupper((string) ($history->provider ?? '-')),
                            $history->status->value,
                            number_format((float) $history->amount, 0, ',', ''),
                        ]);
                    }
                } elseif ($tab === 'reservation') {
                    fputcsv($handle, ['Tanggal Booking', 'Customer', 'Mobil', 'Plat Nomor', 'Start Date', 'End Date', 'Returned At', 'Type', 'Verification Status', 'Status Rental', 'Total Price']);
                    foreach ($exportRows as $rental) {
                        fputcsv($handle, [
                            $rental->created_at->toDateString(),
                            $rental->user?->name ?? '-',
                            trim(($rental->car?->brand ?? '') . ' ' . ($rental->car?->name ?? '')),
                            $rental->car?->license_plate ?? '-',
                            $rental->start_date?->toDateString() ?? '-',
                            $rental->end_date?->toDateString() ?? '-',
                            $rental->returned_at?->toDateString() ?? '-',
                            $rental->type?->value ?? '-',
                            $rental->verification_status?->value ?? '-',
                            $rental->status?->value ?? '-',
                            number_format((float) $rental->total_price, 0, ',', ''),
                        ]);
                    }
                } elseif ($tab === 'fleet') {
                    fputcsv($handle, ['Brand', 'Nama Mobil', 'Plat Nomor', 'Tipe Kendaraan', 'Transmisi', 'Status Mobil', 'Jumlah Disewa', 'Total Pendapatan', 'Terakhir Disewa']);
                    foreach ($exportRows as $car) {
                        fputcsv($handle, [
                            $car['brand'],
                            $car['name'],
                            $car['license_plate'],
                            str($car['vehicle_type'])->headline(),
                            str($car['transmission'])->headline(),
                            str($car['status'])->headline(),
                            $car['rentals_count'],
                            $car['total_revenue'],
                            $car['last_rented'],
                        ]);
                    }
                }

                fclose($handle);
            }, 200, $headers);
        }

        if ($request->query('export') === 'pdf') {
            return response()->view('backoffice.reports-pdf', [
                'tab' => $tab,
                'reportTitle' => $reportTitle,
                'reportPeriodLabel' => $reportPeriodLabel,
                'filterMode' => $filterMode,
                'summary' => $summary,
                'overviewSummary' => $overviewSummary,
                'statusDistribution' => $statusDistribution,
                'serviceTypeDistribution' => $serviceTypeDistribution,
                'topCars' => $topCars,
                'fleetOccupancy' => $fleetOccupancy,
                'featuredCar' => $featuredCar,
                'exportRows' => $exportRows,
                'pdfCharts' => $pdfCharts,
                'generatedAt' => now(),
            ]);
        }

        return view('backoffice.reports', [
            'admin' => $request->user(),
            'active' => 'reports',
            'tab' => $tab,
            'filterMode' => $filterMode,
            'filterDate' => $filterDate,
            'filterMonth' => $filterMonth,
            'filterYear' => $filterYear,
            'filterStart' => $filterStart,
            'filterEnd' => $filterEnd,
            'summary' => $summary,
            'data' => $data,
            'chartRentals' => $chartRentals,
            'chartRevenue' => $chartRevenue,
            'featuredCar' => $featuredCar,
            'overviewSummary' => $overviewSummary,
            'statusDistribution' => $statusDistribution,
            'serviceTypeDistribution' => $serviceTypeDistribution,
            'topCars' => $topCars,
            'fleetOccupancy' => $fleetOccupancy,
            'chartMode' => $chartMode,
            'chartBookingsBreakdown' => $chartBookingsBreakdown,
            'chartRevenueBreakdown' => $chartRevenueBreakdown,
            'paidTransactions' => $paidTransactions ?? 0,
        ]);
    }

    private function resolveReportFilterPeriod(Request $request): array
    {
        $now = Carbon::now();
        $filterMode = (string) $request->query('filter_mode', 'none');

        $defaultStart = $now->copy()->startOfMonth()->startOfDay();
        $defaultEnd = $now->copy()->endOfDay();

        $filterDate = (string) $request->query('filter_date', $defaultEnd->toDateString());
        $filterMonth = (string) $request->query('filter_month', $defaultEnd->format('m'));
        $filterYear = (string) $request->query('filter_year', $defaultEnd->year);
        $filterStart = (string) $request->query('filter_start', $defaultStart->toDateString());
        $filterEnd = (string) $request->query('filter_end', $defaultEnd->toDateString());

        [$periodStart, $periodEnd] = match ($filterMode) {
            'day' => [
                Carbon::parse($filterDate)->startOfDay(),
                Carbon::parse($filterDate)->endOfDay(),
            ],
            'month' => [
                Carbon::createFromDate((int) $filterYear, (int) $filterMonth, 1)->startOfMonth(),
                Carbon::createFromDate((int) $filterYear, (int) $filterMonth, 1)->endOfMonth(),
            ],
            'year' => [
                Carbon::createFromDate((int) $filterYear, 1, 1)->startOfYear(),
                Carbon::createFromDate((int) $filterYear, 1, 1)->endOfYear(),
            ],
            'range' => [
                Carbon::parse($filterStart)->startOfDay(),
                Carbon::parse($filterEnd)->endOfDay(),
            ],
            default => [
                $defaultStart->copy(),
                $defaultEnd->copy(),
            ],
        };

        if ($periodEnd->lessThan($periodStart)) {
            [$periodStart, $periodEnd] = [$periodEnd->copy()->startOfDay(), $periodStart->copy()->endOfDay()];
        }

        $durationDays = max(1, $periodStart->copy()->startOfDay()->diffInDays($periodEnd->copy()->startOfDay()) + 1);
        $previousPeriodEnd = $periodStart->copy()->subDay()->endOfDay();
        $previousPeriodStart = $previousPeriodEnd->copy()->subDays($durationDays - 1)->startOfDay();

        return [
            'filterMode' => $filterMode,
            'filterDate' => $filterDate,
            'filterMonth' => $filterMonth,
            'filterYear' => $filterYear,
            'filterStart' => $filterStart,
            'filterEnd' => $filterEnd,
            'periodStart' => $periodStart,
            'periodEnd' => $periodEnd,
            'previousPeriodStart' => $previousPeriodStart,
            'previousPeriodEnd' => $previousPeriodEnd,
        ];
    }

    private function applyReportFilter(\Illuminate\Database\Eloquent\Builder $query, ?Carbon $start, ?Carbon $end): \Illuminate\Database\Eloquent\Builder
    {
        if ($start && $end) {
            $query->whereBetween('created_at', [$start, $end]);
        }

        return $query;
    }

    private function buildRelativeGrowthText(int|float $current, int|float $previous): array
    {
        if ($previous <= 0) {
            if ($current > 0) {
                return [
                    'direction' => 'up',
                    'value' => '100,0%',
                    'suffix' => 'vs periode sebelumnya',
                    'tone' => 'positive',
                ];
            }

            return [
                'direction' => 'flat',
                'value' => '0,0%',
                'suffix' => 'vs periode sebelumnya',
                'tone' => 'neutral',
            ];
        }

        $growth = round((($current - $previous) / $previous) * 100, 1);

        if ($growth > 0) {
            return [
                'direction' => 'up',
                'value' => number_format($growth, 1, ',', '.') . '%',
                'suffix' => 'vs periode sebelumnya',
                'tone' => 'positive',
            ];
        }

        if ($growth < 0) {
            return [
                'direction' => 'down',
                'value' => number_format(abs($growth), 1, ',', '.') . '%',
                'suffix' => 'vs periode sebelumnya',
                'tone' => 'negative',
            ];
        }

        return [
            'direction' => 'flat',
            'value' => '0,0%',
            'suffix' => 'vs periode sebelumnya',
            'tone' => 'neutral',
        ];
    }

    private function buildPointChangeText(float $currentRate, float $previousRate, bool $lowerIsBetter = false): array
    {
        $delta = round($currentRate - $previousRate, 1);

        if ($delta === 0.0) {
            return [
                'direction' => 'flat',
                'value' => '0,0%',
                'suffix' => 'vs periode sebelumnya',
                'tone' => 'neutral',
            ];
        }

        $arrow = $delta > 0 ? '▲' : '▼';
        $formatted = number_format(abs($delta), 1, ',', '.');
        $tone = $lowerIsBetter
            ? ($delta < 0 ? 'positive' : 'negative')
            : ($delta > 0 ? 'positive' : 'negative');

        return [
            'direction' => $delta > 0 ? 'up' : 'down',
            'value' => "{$formatted}%",
            'suffix' => 'vs periode sebelumnya',
            'tone' => $tone,
        ];
    }

    private function reportTabLabels(): array
    {
        return [
            'overview' => 'Overview',
            'revenue' => 'Laporan Pendapatan',
            'reservation' => 'Laporan Reservasi',
            'fleet' => 'Laporan Armada',
        ];
    }

    private function formatReportPeriodLabel(string $filterMode, Carbon $periodStart, Carbon $periodEnd): string
    {
        return match ($filterMode) {
            'day' => $periodStart->translatedFormat('d F Y'),
            'month' => $periodStart->translatedFormat('F Y'),
            'year' => $periodStart->translatedFormat('Y'),
            'range' => $periodStart->translatedFormat('d M Y') . ' - ' . $periodEnd->translatedFormat('d M Y'),
            default => $periodStart->translatedFormat('d M Y') . ' - ' . $periodEnd->translatedFormat('d M Y'),
        };
    }

    private function buildReportPdfCharts(
        string $tab,
        \Illuminate\Support\Collection $chartRentals,
        \Illuminate\Support\Collection $chartRevenue,
        \Illuminate\Support\Collection $statusDistribution,
        \Illuminate\Support\Collection $serviceTypeDistribution,
        \Illuminate\Support\Collection $carStats,
        array $fleetOccupancy,
        array $summary
    ): array {
        $charts = [];

        if ($tab === 'overview') {
            $charts['bookings'] = $this->renderLineChartSvg($chartRentals, '#3f5ed7', 'Tren Periode Aktif');
            $charts['revenue'] = $this->renderBarChartSvg($chartRevenue, '#1dbb84', 'Pendapatan Masuk', true);
            $charts['status'] = $this->renderDonutChartSvg($statusDistribution, ['#818cf8', '#f59e0b', '#3b82f6', '#1dbb84', '#ef4444', '#94a3b8'], 'Total Reservasi');
            $charts['service'] = $this->renderDonutChartSvg($serviceTypeDistribution, ['#3f5ed7', '#1dbb84', '#f59e0b', '#94a3b8'], 'Total Reservasi');
        } elseif ($tab === 'revenue') {
            $charts['revenue'] = $this->renderBarChartSvg($chartRevenue, '#1dbb84', 'Pendapatan', true);
        } elseif ($tab === 'reservation') {
            $charts['reservations'] = $this->renderLineChartSvg($chartRentals, '#3f5ed7', 'Reservasi');
            $reservationStatus = collect([
                ['label' => 'Pending', 'value' => (int) ($summary['pending'] ?? 0)],
                ['label' => 'Prepaid', 'value' => (int) ($summary['prepaid'] ?? 0)],
                ['label' => 'Aktif', 'value' => (int) ($summary['ongoing'] ?? 0)],
                ['label' => 'Selesai', 'value' => (int) ($summary['returned'] ?? 0)],
                ['label' => 'Batal', 'value' => (int) ($summary['cancelled'] ?? 0)],
                ['label' => 'Expired', 'value' => (int) ($summary['expired'] ?? 0)],
            ])->filter(fn (array $item) => $item['value'] > 0)->values();
            $charts['status'] = $this->renderDonutChartSvg($reservationStatus, ['#94a3b8', '#f59e0b', '#1dbb84', '#818cf8', '#ef4444', '#3b82f6'], 'Total Booking');
        } elseif ($tab === 'fleet') {
            $fleetPerformance = $carStats
                ->sortByDesc('rentals_count')
                ->take(5)
                ->map(fn (array $item) => [
                    'label' => str(trim(($item['brand'] ?? '') . ' ' . ($item['name'] ?? '')))->limit(16, '…')->toString(),
                    'value' => (int) ($item['rentals_count'] ?? 0),
                ])
                ->values();
            $fleetStatus = collect([
                ['label' => 'Tersedia', 'value' => (int) ($fleetOccupancy['available'] ?? 0)],
                ['label' => 'Sibuk', 'value' => (int) ($fleetOccupancy['unavailable'] ?? 0)],
            ]);
            $charts['fleet_performance'] = $this->renderBarChartSvg($fleetPerformance, '#3f5ed7', 'Top Armada', false);
            $charts['fleet_status'] = $this->renderDonutChartSvg($fleetStatus, ['#1dbb84', '#ef4444'], 'Status Armada');
        }

        return $charts;
    }

    private function renderBarChartSvg(\Illuminate\Support\Collection $points, string $barColor, string $seriesLabel, bool $currency = false): string
    {
        $points = $points->map(fn ($point) => [
            'label' => (string) ($point['label'] ?? ''),
            'value' => (float) ($point['value'] ?? 0),
        ])->values();

        $width = 720;
        $height = 280;
        $left = 52;
        $right = 18;
        $top = 18;
        $bottom = 42;
        $plotWidth = $width - $left - $right;
        $plotHeight = $height - $top - $bottom;
        $maxValue = max(1, (float) $points->max('value'));
        $step = $points->count() > 0 ? $plotWidth / max($points->count(), 1) : $plotWidth;
        $barWidth = max(16, $step * 0.56);
        $svg = [];

        $svg[] = "<svg viewBox=\"0 0 {$width} {$height}\" role=\"img\" aria-label=\"" . e($seriesLabel) . "\" xmlns=\"http://www.w3.org/2000/svg\">";
        $svg[] = "<rect width=\"{$width}\" height=\"{$height}\" rx=\"18\" fill=\"#ffffff\"/>";

        for ($i = 0; $i < 4; $i++) {
            $y = $top + ($plotHeight / 3) * $i;
            $svg[] = "<line x1=\"{$left}\" y1=\"{$y}\" x2=\"" . ($width - $right) . "\" y2=\"{$y}\" stroke=\"rgba(203,213,225,0.9)\" stroke-width=\"1\" />";
        }

        if ($points->isEmpty()) {
            $svg[] = "<text x=\"" . ($width / 2) . "\" y=\"" . ($height / 2) . "\" text-anchor=\"middle\" fill=\"#64748b\" font-size=\"16\" font-family=\"Arial, sans-serif\">Belum ada data</text>";
            $svg[] = '</svg>';
            return implode('', $svg);
        }

        foreach ($points as $index => $point) {
            $value = (float) $point['value'];
            $x = $left + ($step * $index) + (($step - $barWidth) / 2);
            $barHeight = $maxValue > 0 ? ($value / $maxValue) * ($plotHeight * 0.9) : 0;
            $y = $top + $plotHeight - $barHeight;
            $label = e($point['label']);
            $valueLabel = $currency
                ? 'Rp ' . number_format((int) $value, 0, ',', '.')
                : number_format((int) $value, 0, ',', '.');

            $svg[] = "<rect x=\"{$x}\" y=\"{$y}\" width=\"{$barWidth}\" height=\"{$barHeight}\" rx=\"8\" fill=\"{$barColor}\" fill-opacity=\"0.82\" />";
            $svg[] = "<text x=\"" . ($x + ($barWidth / 2)) . "\" y=\"" . max(14, $y - 6) . "\" text-anchor=\"middle\" fill=\"#334155\" font-size=\"10\" font-family=\"Arial, sans-serif\">{$valueLabel}</text>";
            $svg[] = "<text x=\"" . ($x + ($barWidth / 2)) . "\" y=\"" . ($height - 16) . "\" text-anchor=\"middle\" fill=\"#64748b\" font-size=\"10\" font-family=\"Arial, sans-serif\">{$label}</text>";
        }

        $svg[] = '</svg>';

        return implode('', $svg);
    }

    private function renderLineChartSvg(\Illuminate\Support\Collection $points, string $strokeColor, string $seriesLabel): string
    {
        $points = $points->map(fn ($point) => [
            'label' => (string) ($point['label'] ?? ''),
            'value' => (float) ($point['value'] ?? 0),
        ])->values();

        $width = 720;
        $height = 280;
        $left = 42;
        $right = 18;
        $top = 18;
        $bottom = 42;
        $plotWidth = $width - $left - $right;
        $plotHeight = $height - $top - $bottom;
        $maxValue = max(1, (float) $points->max('value'));
        $count = max($points->count(), 1);
        $svg = [];

        $svg[] = "<svg viewBox=\"0 0 {$width} {$height}\" role=\"img\" aria-label=\"" . e($seriesLabel) . "\" xmlns=\"http://www.w3.org/2000/svg\">";
        $svg[] = "<defs><linearGradient id=\"lineFill\" x1=\"0\" x2=\"0\" y1=\"0\" y2=\"1\"><stop offset=\"0%\" stop-color=\"{$strokeColor}\" stop-opacity=\"0.18\"/><stop offset=\"100%\" stop-color=\"{$strokeColor}\" stop-opacity=\"0.02\"/></linearGradient></defs>";
        $svg[] = "<rect width=\"{$width}\" height=\"{$height}\" rx=\"18\" fill=\"#ffffff\"/>";

        for ($i = 0; $i < 4; $i++) {
            $y = $top + ($plotHeight / 3) * $i;
            $svg[] = "<line x1=\"{$left}\" y1=\"{$y}\" x2=\"" . ($width - $right) . "\" y2=\"{$y}\" stroke=\"rgba(203,213,225,0.9)\" stroke-width=\"1\" />";
        }

        if ($points->isEmpty()) {
            $svg[] = "<text x=\"" . ($width / 2) . "\" y=\"" . ($height / 2) . "\" text-anchor=\"middle\" fill=\"#64748b\" font-size=\"16\" font-family=\"Arial, sans-serif\">Belum ada data</text>";
            $svg[] = '</svg>';
            return implode('', $svg);
        }

        $coordinates = [];
        foreach ($points as $index => $point) {
            $x = $left + ($plotWidth * ($count === 1 ? 0.5 : $index / ($count - 1)));
            $y = $top + $plotHeight - (($point['value'] / $maxValue) * ($plotHeight * 0.9));
            $coordinates[] = ['x' => round($x, 2), 'y' => round($y, 2), 'label' => $point['label'], 'value' => $point['value']];
        }

        $polyline = collect($coordinates)->map(fn ($point) => $point['x'] . ',' . $point['y'])->implode(' ');
        $area = $polyline . ' ' . ($left + $plotWidth) . ',' . ($top + $plotHeight) . ' ' . $left . ',' . ($top + $plotHeight);
        $svg[] = "<polygon points=\"{$area}\" fill=\"url(#lineFill)\" />";
        $svg[] = "<polyline points=\"{$polyline}\" fill=\"none\" stroke=\"{$strokeColor}\" stroke-width=\"4\" stroke-linecap=\"round\" stroke-linejoin=\"round\" />";

        foreach ($coordinates as $point) {
            $svg[] = "<circle cx=\"{$point['x']}\" cy=\"{$point['y']}\" r=\"4.5\" fill=\"{$strokeColor}\" />";
            $svg[] = "<text x=\"{$point['x']}\" y=\"" . ($height - 16) . "\" text-anchor=\"middle\" fill=\"#64748b\" font-size=\"10\" font-family=\"Arial, sans-serif\">" . e($point['label']) . "</text>";
        }

        $svg[] = '</svg>';

        return implode('', $svg);
    }

    private function renderDonutChartSvg(\Illuminate\Support\Collection $segments, array $colors, string $centerLabel): string
    {
        $segments = $segments->map(fn ($segment) => [
            'label' => (string) ($segment['label'] ?? ''),
            'value' => (float) ($segment['value'] ?? 0),
        ])->filter(fn (array $segment) => $segment['value'] > 0)->values();

        $size = 300;
        $radius = 78;
        $stroke = 34;
        $circumference = 2 * pi() * $radius;
        $total = max(0, (float) $segments->sum('value'));
        $svg = [];

        $svg[] = "<svg viewBox=\"0 0 {$size} {$size}\" role=\"img\" aria-label=\"" . e($centerLabel) . "\" xmlns=\"http://www.w3.org/2000/svg\">";
        $svg[] = "<rect width=\"{$size}\" height=\"{$size}\" rx=\"24\" fill=\"#ffffff\"/>";

        if ($total <= 0) {
            $svg[] = "<text x=\"150\" y=\"150\" text-anchor=\"middle\" fill=\"#64748b\" font-size=\"16\" font-family=\"Arial, sans-serif\">Belum ada data</text>";
            $svg[] = '</svg>';
            return implode('', $svg);
        }

        $offset = 0.0;
        foreach ($segments as $index => $segment) {
            $ratio = $segment['value'] / $total;
            $arc = $circumference * $ratio;
            $color = $colors[$index % count($colors)];
            $svg[] = "<circle cx=\"150\" cy=\"150\" r=\"{$radius}\" fill=\"none\" stroke=\"{$color}\" stroke-width=\"{$stroke}\" stroke-linecap=\"butt\" stroke-dasharray=\"{$arc} " . ($circumference - $arc) . "\" stroke-dashoffset=\"-" . ($offset * $circumference) . "\" transform=\"rotate(-90 150 150)\" />";
            $offset += $ratio;
        }

        $svg[] = "<circle cx=\"150\" cy=\"150\" r=\"50\" fill=\"#ffffff\" />";
        $svg[] = "<text x=\"150\" y=\"145\" text-anchor=\"middle\" fill=\"#111827\" font-size=\"28\" font-weight=\"700\" font-family=\"Arial, sans-serif\">" . number_format((int) $total, 0, ',', '.') . "</text>";
        $svg[] = "<text x=\"150\" y=\"170\" text-anchor=\"middle\" fill=\"#64748b\" font-size=\"12\" font-weight=\"700\" font-family=\"Arial, sans-serif\">" . e($centerLabel) . "</text>";

        foreach ($segments as $index => $segment) {
            $y = 236 + ($index * 18);
            $color = $colors[$index % count($colors)];
            $text = e($segment['label'] . ' • ' . number_format((int) $segment['value'], 0, ',', '.'));
            $svg[] = "<rect x=\"26\" y=\"" . ($y - 9) . "\" width=\"10\" height=\"10\" rx=\"3\" fill=\"{$color}\" />";
            $svg[] = "<text x=\"44\" y=\"{$y}\" fill=\"#475569\" font-size=\"11\" font-family=\"Arial, sans-serif\">{$text}</text>";
        }

        $svg[] = '</svg>';

        return implode('', $svg);
    }

    public function settings()
    {
        $admin = Auth::user();
        $companySetting = CompanySetting::current();

        return view('backoffice.settings', [
            'admin' => $admin,
            'companySetting' => $companySetting,
            'active' => 'settings'
        ]);
    }

    public function updateProfile(Request $request)
    {
        $admin = Auth::user();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => [
                'required',
                'string',
                'min:3',
                'max:50',
                'alpha_dash',
                Rule::unique('users', 'username')->ignore($admin->id),
                static function (string $attribute, mixed $value, \Closure $fail): void {
                    if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
                        $fail('Username tidak boleh menggunakan format email.');
                    }
                },
            ],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users', 'email')->ignore($admin->id)],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah digunakan oleh akun lain.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan oleh akun lain.',
        ]);

        $admin->name = $request->input('name');
        $admin->username = Str::lower($request->input('username'));
        $admin->email = $request->input('email');
        $admin->save();

        return redirect()->route('backoffice.settings')
            ->with('success', 'Profil admin berhasil diperbarui.');
    }

    public function updateCompanySettings(Request $request)
    {
        $companySetting = CompanySetting::current();

        $validated = $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'company_email' => ['required', 'string', 'lowercase', 'email', 'max:255'],
            'company_description' => ['required', 'string'],
            'address' => ['required', 'string'],
            'maps_directions_url' => ['required', 'url'],
        ], [
            'company_name.required' => 'Nama perusahaan wajib diisi.',
            'company_email.required' => 'Email perusahaan wajib diisi.',
            'company_email.email' => 'Email perusahaan harus berupa alamat email yang valid.',
            'company_description.required' => 'Deskripsi perusahaan wajib diisi.',
            'address.required' => 'Alamat perusahaan wajib diisi.',
            'maps_directions_url.required' => 'Link Google Maps wajib diisi.',
            'maps_directions_url.url' => 'Link Google Maps harus berupa URL yang valid.',
        ]);

        $validated['maps_embed_url'] = 'https://maps.google.com/maps?q='
            . rawurlencode($validated['address'])
            . '&t=&z=16&ie=UTF8&iwloc=&output=embed';

        $companySetting->fill($validated);
        $companySetting->save();

        return redirect()->route('backoffice.settings')
            ->with('success', 'Pengaturan perusahaan berhasil diperbarui.');
    }
}
