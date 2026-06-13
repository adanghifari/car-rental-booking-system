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
use App\Models\PaymentHistory;
use App\Models\Rental;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class BackofficeController extends Controller
{
    public function index(): View
    {
        $lockingStatuses = $this->lockingRentalStatuses();

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
                ->whereHas('rentals', fn ($query) => $query->whereIn('status', $lockingStatuses))
                ->count(),
            'maintenance' => Car::query()
                ->where('status', CarStatus::UNAVAILABLE)
                ->whereDoesntHave('rentals', fn ($query) => $query->whereIn('status', $lockingStatuses))
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
                  ->orWhere('username', 'like', "%{$search}%");
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

        // Membership Filter
        if ($request->filled('membership')) {
            $membership = $request->input('membership');
            $usersQuery->where(function ($q) use ($membership) {
                $sumQuery = "(SELECT COALESCE(SUM(total_price), 0) FROM rentals WHERE rentals.user_id = users.id)";

                if ($membership === 'platinum') {
                    $q->whereRaw("$sumQuery >= 40000000");
                } elseif ($membership === 'gold') {
                    $q->whereRaw("$sumQuery >= 20000000")
                      ->whereRaw("$sumQuery < 40000000");
                } elseif ($membership === 'silver') {
                    $q->whereRaw("$sumQuery < 20000000");
                }
            });
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

                [$membership, $membershipTone] = match (true) {
                    $totalTransactions >= 40000000 => ['PLATINUM', 'platinum'],
                    $totalTransactions >= 20000000 => ['GOLD', 'gold'],
                    default => ['SILVER', 'silver'],
                };

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
                    'username' => $user->username ?? 'user'.$user->id,
                    'contact' => $user->role === User::ROLE_ADMIN ? 'Admin system' : 'Customer rental',
                    'membership' => $membership,
                    'membership_tone' => $membershipTone,
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
        $lockingStatuses = $this->lockingRentalStatuses();

        $filters = [
            'search' => trim((string) $request->query('search', '')),
            'status' => (string) $request->query('status', ''),
            'type' => (string) $request->query('type', ''),
            'transmission' => (string) $request->query('transmission', ''),
        ];

        $totalCars = Car::count();
        $availableCars = Car::where('status', CarStatus::AVAILABLE)->count();
        $rentedCars = Car::query()
            ->whereHas('rentals', fn ($query) => $query->whereIn('status', $lockingStatuses))
            ->count();
        $maintenanceCars = Car::query()
            ->where('status', CarStatus::UNAVAILABLE)
            ->whereDoesntHave('rentals', fn ($query) => $query->whereIn('status', $lockingStatuses))
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
            ->when($filters['status'] !== '', function ($query) use ($filters, $lockingStatuses) {
                if ($filters['status'] === 'available') {
                    $query->where('status', CarStatus::AVAILABLE);
                }

                if ($filters['status'] === 'rented') {
                    $query->whereHas('rentals', fn ($rentalQuery) => $rentalQuery->whereIn('status', $lockingStatuses));
                }

                if ($filters['status'] === 'maintenance') {
                    $query
                        ->where('status', CarStatus::UNAVAILABLE)
                        ->whereDoesntHave('rentals', fn ($rentalQuery) => $rentalQuery->whereIn('status', $lockingStatuses));
                }
            })
            ->latest();

        $cars = $carsQuery
            ->paginate(6)
            ->withQueryString()
            ->through(function (Car $car) use ($lockingStatuses) {
                $lockingRental = $car->rentals
                    ->first(fn (Rental $rental) => in_array($rental->status, $lockingStatuses, true));
                $status = $this->carStatusMeta($car, $lockingRental);
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
                    'locking_rental_id' => $lockingRental?->id,
                    'locking_rental_status' => $lockingRental?->status?->value,
                    'locking_rental_verification_status' => $lockingRental?->verification_status?->value,
                    'can_change_status' => $status['can_change_status'],
                    'status_action_label' => $status['action_label'],
                    'status_action_kind' => $status['action_kind'],
                    'status_action_value' => $status['action_value'],
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

        $validated['image'] = $request->file('image')->store('cars/main', 'public');
        $validated['gallery_images'] = collect($request->file('gallery_images', []))
            ->map(fn ($file) => $file->store('cars/gallery', 'public'))
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
            $validated['image'] = $request->file('image')->store('cars/main', 'public');
        } elseif ($removeImage) {
            $this->deleteStoredCarMedia($car, false);
            $validated['image'] = null;
        } else {
            unset($validated['image']);
        }

        if ($request->hasFile('gallery_images')) {
            $this->deleteStoredGalleryMedia($car, false, $removedGalleryImages);
            $validated['gallery_images'] = collect($request->file('gallery_images', []))
                ->map(fn ($file) => $file->store('cars/gallery', 'public'))
                ->values()
                ->pipe(fn ($items) => array_values(array_merge($remainingGalleryImages, $items->all())));
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

        $lockingStatuses = $this->lockingRentalStatuses();
        $lockingRental = $car->rentals()
            ->whereIn('status', $lockingStatuses)
            ->latest('created_at')
            ->first();

        if ($desiredStatus === CarStatus::AVAILABLE) {
            if ($lockingRental) {
                return redirect()
                    ->route('backoffice.reservations', ['rental_id' => $lockingRental->id])
                    ->with('warning', $this->lockingRentalWarningMessage($lockingRental));
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

            return redirect()
                ->route('backoffice.cars')
                ->with('success', 'Mobil berhasil diset ke maintenance.');
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
            ->with(['user:id,name', 'car']);

        if ($filter === 'waiting_review') {
            $query->where('status', RentalStatus::PENDING_VERIFICATION)
                ->where(
                    'verification_status',
                    \App\Enums\VerificationStatus::NEEDS_REVIEW
                );
        } elseif ($filter === 'verified_no_pay') {
            $query->where('status', RentalStatus::PENDING_VERIFICATION)
                ->where(
                    'verification_status',
                    \App\Enums\VerificationStatus::VERIFIED
                );
        } elseif ($filter === 'waiting_pay') {
            $query->where('status', RentalStatus::PREPAID);
        } elseif ($filter === 'active') {
            $query->where('status', RentalStatus::ONGOING);
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

        $pendingReservations = Rental::whereIn('status', [
            RentalStatus::PENDING_VERIFICATION,
            RentalStatus::PREPAID,
        ])->count();

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

                $statusLabel = 'Menunggu';

                if ($rental->status === RentalStatus::PENDING_VERIFICATION) {

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

                return [
                    'id' => $rental->id,

                    'booking_id' => $rental->id,

                    'is_overdue' => $isOverdue,

                    'overdue_days' => (int) $overdueDays,

                    'customer_name' => $rental->user?->name,

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
                'pending' => $pendingReservations,
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
                    throw new \RuntimeException('Mobil tidak tersedia.');
                }

                $ktpPath = null;
                $selfiePath = null;
                if ($request->hasFile('ktp')) {
                    $ktpPath = Storage::disk('local')->putFile('ktp', $request->file('ktp'));
                }
                if ($request->hasFile('selfie')) {
                    $selfiePath = Storage::disk('local')->putFile('selfie', $request->file('selfie'));
                }

                $start = Carbon::parse($data['start_date']);
                $end = Carbon::parse($data['end_date']);
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
                ]);

                $car->status = CarStatus::UNAVAILABLE;
                $car->save();

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

    private function carStatusMeta(Car $car, ?Rental $lockingRental = null): array
    {
        if ($car->status === CarStatus::AVAILABLE) {
            return [
                'label' => 'TERSEDIA',
                'tone' => 'green',
                'note' => 'Mobil siap disewa dan tidak sedang digunakan.',
                'can_change_status' => true,
                'action_label' => 'Set Maintenance',
                'action_kind' => 'toggle',
                'action_value' => CarStatus::UNAVAILABLE->value,
                'action_class' => 'status-action status-action-maintenance',
            ];
        }

        if ($lockingRental?->status === RentalStatus::PENDING_VERIFICATION) {
            $needsReview = $lockingRental->verification_status === \App\Enums\VerificationStatus::NEEDS_REVIEW;

            return [
                'label' => $needsReview ? 'BUTUH REVIEW' : 'MENUNGGU VERIFIKASI',
                'tone' => $needsReview ? 'amber' : 'indigo',
                'note' => 'Mobil sedang ditahan oleh proses verifikasi customer.',
                'can_change_status' => false,
                'action_label' => 'Lihat Reservasi',
                'action_kind' => 'view_reservation',
                'action_value' => null,
                'action_class' => 'status-action status-action-verification',
            ];
        }

        if ($lockingRental?->status === RentalStatus::PREPAID) {
            return [
                'label' => 'MENUNGGU PEMBAYARAN',
                'tone' => 'amber',
                'note' => 'Mobil sedang menunggu pembayaran customer.',
                'can_change_status' => false,
                'action_label' => 'Lihat Reservasi',
                'action_kind' => 'view_reservation',
                'action_value' => null,
                'action_class' => 'status-action status-action-payment',
            ];
        }

        if ($lockingRental?->status === RentalStatus::ONGOING) {
            return [
                'label' => 'DISEWA',
                'tone' => 'blue',
                'note' => 'Mobil sedang dipakai pelanggan pada periode rental aktif.',
                'can_change_status' => false,
                'action_label' => 'Lihat Reservasi',
                'action_kind' => 'view_reservation',
                'action_value' => null,
                'action_class' => 'status-action status-action-rented',
            ];
        }

        return [
            'label' => 'MAINTENANCE',
            'tone' => 'red',
            'note' => 'Mobil tidak tersedia secara manual untuk pemeriksaan atau perbaikan.',
            'can_change_status' => true,
            'action_label' => 'Aktifkan',
            'action_kind' => 'toggle',
            'action_value' => CarStatus::AVAILABLE->value,
            'action_class' => 'status-action status-action-available',
        ];
    }

    private function lockingRentalStatuses(): array
    {
        return [
            RentalStatus::PENDING_VERIFICATION,
            RentalStatus::PREPAID,
            RentalStatus::ONGOING,
        ];
    }

    private function lockingRentalWarningMessage(Rental $rental): string
    {
        return match ($rental->status) {
            RentalStatus::ONGOING => 'Mobil ini sedang disewa customer dan tidak dapat dibuat tersedia sebelum rental selesai.',
            RentalStatus::PREPAID => 'Mobil ini sedang menunggu pembayaran customer. Silakan cek detail reservasi.',
            RentalStatus::PENDING_VERIFICATION => 'Mobil ini sedang ditahan untuk proses verifikasi customer. Silakan cek detail reservasi.',
            default => 'Mobil ini masih terkait dengan reservasi aktif, sehingga tidak dapat diubah menjadi tersedia secara manual.',
        };
    }

    private function resolveCarImageUrl(?string $image): ?string
    {
        if (! $image) {
            return null;
        }

        if (str_starts_with($image, 'http://') || str_starts_with($image, 'https://') || str_starts_with($image, '/')) {
            return $image;
        }

        if (Storage::disk('public')->exists($image)) {
            return Storage::url($image);
        }

        return asset($image);
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

            Storage::disk('public')->delete($path);
        }
    }

    public function reports(Request $request)
    {
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

        // Handle CSV Export
        if ($request->query('export') === 'csv') {
            $filename = "laporan_{$tab}_{$filterMode}.csv";
            $headers = [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            ];

            return response()->stream(function () use ($tab, $periodStart, $periodEnd, $carStats) {
                $handle = fopen('php://output', 'w');
                // Add UTF-8 BOM
                fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

                if ($tab === 'revenue') {
                    fputcsv($handle, ['Tanggal Pembayaran', 'Customer', 'Mobil', 'Plat Nomor', 'Tipe Rental', 'Provider Pembayaran', 'Status Pembayaran', 'Amount']);
                    PaymentHistory::query()
                        ->with(['rental.user', 'rental.car'])
                        ->where('status', PaymentStatus::PAID)
                        ->when($periodStart && $periodEnd, fn ($query) => $query->whereBetween('created_at', [$periodStart, $periodEnd]))
                        ->latest()
                        ->chunk(100, function ($histories) use ($handle) {
                            foreach ($histories as $history) {
                                fputcsv($handle, [
                                    $history->created_at->toDateTimeString(),
                                    $history->rental?->user?->name ?? '-',
                                    trim(($history->rental?->car?->brand ?? '') . ' ' . ($history->rental?->car?->name ?? '')),
                                    $history->rental?->car?->license_plate ?? '-',
                                    $history->rental?->type === RentalType::SELF_DRIVE ? 'Self Drive' : 'With Driver',
                                    $history->provider ?? '-',
                                    $history->status->value,
                                    $history->amount,
                                ]);
                            }
                        });
                } elseif ($tab === 'reservation') {
                    fputcsv($handle, ['Tanggal Booking', 'Customer', 'Mobil', 'Plat Nomor', 'Start Date', 'End Date', 'Returned At', 'Type', 'Verification Status', 'Status Rental', 'Total Price']);
                    Rental::query()
                        ->with(['user', 'car'])
                        ->when($periodStart && $periodEnd, fn ($query) => $query->whereBetween('created_at', [$periodStart, $periodEnd]))
                        ->latest()
                        ->chunk(100, function ($rentals) use ($handle) {
                            foreach ($rentals as $rental) {
                                fputcsv($handle, [
                                    $rental->created_at->toDateTimeString(),
                                    $rental->user?->name ?? '-',
                                    trim(($rental->car?->brand ?? '') . ' ' . ($rental->car?->name ?? '')),
                                    $rental->car?->license_plate ?? '-',
                                    $rental->start_date?->toDateString() ?? '-',
                                    $rental->end_date?->toDateString() ?? '-',
                                    $rental->returned_at?->toDateTimeString() ?? '-',
                                    $rental->type?->value ?? '-',
                                    $rental->verification_status?->value ?? '-',
                                    $rental->status?->value ?? '-',
                                    $rental->total_price,
                                ]);
                            }
                        });
                } elseif ($tab === 'fleet') {
                    fputcsv($handle, ['Brand', 'Nama Mobil', 'Plat Nomor', 'Tipe Kendaraan', 'Transmisi', 'Status Mobil', 'Jumlah Disewa', 'Total Pendapatan', 'Terakhir Disewa']);
                    foreach ($carStats as $car) {
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

        // Initialize variables for view rendering
        $summary = [];
        $data = null;

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
        ];

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
}
