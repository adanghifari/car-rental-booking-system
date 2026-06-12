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
        $now = now();
        $monthStart = $now->copy()->startOfMonth();
        $months = collect(range(5, 0))->map(fn (int $offset) => $now->copy()->subMonths($offset)->startOfMonth());
        $monthLabels = $months->map(fn (Carbon $date) => $date->translatedFormat('M'));

        $rentalCounts = Rental::query()
            ->where('created_at', '>=', $months->first())
            ->get()
            ->groupBy(fn (Rental $rental) => $rental->created_at->format('Y-m'))
            ->map->count();

        $revenueByMonth = PaymentHistory::query()
            ->where('status', PaymentStatus::PAID)
            ->where('created_at', '>=', $months->first())
            ->get()
            ->groupBy(fn (PaymentHistory $payment) => $payment->created_at->format('Y-m'))
            ->map(fn ($payments) => $payments->sum('amount'));

        $chartRentals = $months->map(fn (Carbon $date) => [
            'label' => $date->translatedFormat('M'),
            'value' => (int) ($rentalCounts[$date->format('Y-m')] ?? 0),
        ]);

        $chartRevenue = $months->map(fn (Carbon $date) => [
            'label' => $date->translatedFormat('M'),
            'value' => (int) ($revenueByMonth[$date->format('Y-m')] ?? 0),
        ]);

        $topCar = Car::query()
            ->withCount('rentals')
            ->withSum('rentals', 'total_price')
            ->orderByDesc('rentals_count')
            ->orderByDesc('rentals_sum_total_price')
            ->first();

        $recentRentals = Rental::query()
            ->with(['user:id,name', 'car:id,name'])
            ->latest()
            ->take(4)
            ->get()
            ->map(function (Rental $rental) {
                $activity = match ($rental->status) {
                    RentalStatus::PREPAID => 'Booking dibuat',
                    RentalStatus::ONGOING => 'Mobil disewa',
                    RentalStatus::RETURNED => 'Mobil dikembalikan',
                    default => 'Aktivitas rental',
                };

                $status = match ($rental->status) {
                    RentalStatus::PREPAID => ['label' => 'Prepaid', 'tone' => 'amber'],
                    RentalStatus::ONGOING => ['label' => 'Berjalan', 'tone' => 'green'],
                    RentalStatus::RETURNED => ['label' => 'Selesai', 'tone' => 'blue'],
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

        return view('backoffice.dashboard', [
            'admin' => request()->user(),
            'stats' => [
                'total_users' => User::count(),
                'total_cars' => Car::count(),
                'available_cars' => Car::where('status', CarStatus::AVAILABLE)->count(),
                'rented_cars' => Car::where('status', CarStatus::UNAVAILABLE)->count(),
                'monthly_revenue' => PaymentHistory::where('status', PaymentStatus::PAID)
                    ->where('created_at', '>=', $monthStart)
                    ->sum('amount'),
                'bookings_today' => Rental::whereDate('created_at', $now->toDateString())->count(),
            ],
            'fleet' => [
                'available' => Car::where('status', CarStatus::AVAILABLE)->count(),
                'rented' => Car::where('status', CarStatus::UNAVAILABLE)->count(),
                'maintenance' => 0,
            ],
            'chartRentals' => $chartRentals,
            'chartRevenue' => $chartRevenue,
            'recentActivities' => $recentRentals,
            'featuredCar' => [
                'name' => trim(($topCar?->brand ?? '').' '.($topCar?->name ?? '')) ?: 'Belum ada armada unggulan',
                'description' => $topCar?->description ?? 'Tambahkan transaksi rental untuk melihat armada dengan performa terbaik.',
                'revenue' => (int) ($topCar?->rentals_sum_total_price ?? 0),
                'rentals_count' => (int) ($topCar?->rentals_count ?? 0),
            ],
            'monthLabels' => $monthLabels,
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
        $activeRentalCarIds = Rental::query()
            ->whereIn('status', [RentalStatus::PREPAID, RentalStatus::ONGOING])
            ->pluck('car_id')
            ->filter()
            ->unique()
            ->values();

        $filters = [
            'search' => trim((string) $request->query('search', '')),
            'status' => (string) $request->query('status', ''),
            'type' => (string) $request->query('type', ''),
            'transmission' => (string) $request->query('transmission', ''),
        ];

        $totalCars = Car::count();
        $availableCars = Car::where('status', CarStatus::AVAILABLE)->count();
        $unavailableCars = Car::where('status', CarStatus::UNAVAILABLE)->count();
        $rentedCars = Car::query()->whereIn('id', $activeRentalCarIds)->count();
        $maintenanceCars = max($unavailableCars - $rentedCars, 0);

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
            ->when($filters['status'] !== '', function ($query) use ($filters, $activeRentalCarIds) {
                if ($filters['status'] === 'available') {
                    $query->where('status', CarStatus::AVAILABLE);
                }

                if ($filters['status'] === 'rented') {
                    $query->whereIn('id', $activeRentalCarIds);
                }

                if ($filters['status'] === 'maintenance') {
                    $query
                        ->where('status', CarStatus::UNAVAILABLE)
                        ->whereNotIn('id', $activeRentalCarIds);
                }
            })
            ->latest();

        $cars = $carsQuery
            ->paginate(6)
            ->withQueryString()
            ->through(function (Car $car) use ($activeRentalCarIds) {
                $status = $this->carStatusMeta($car, $activeRentalCarIds->contains($car->id));

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
            'typeOptions' => VehicleType::values(),
            'transmissionOptions' => TransmissionType::values(),
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
            'self_drive_available' => ['nullable', 'boolean'],
            'driver_available' => ['nullable', 'boolean'],
        ]);

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
            'image' => ['nullable', 'file', 'image', 'max:5120'],
            'remove_image' => ['nullable', 'boolean'],
            'remove_gallery_images' => ['nullable', 'string'],
            'gallery_images' => ['nullable', 'array', 'max:8'],
            'gallery_images.*' => ['file', 'image', 'max:5120'],
            'self_drive_available' => ['nullable', 'boolean'],
            'driver_available' => ['nullable', 'boolean'],
        ]);

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
        $totalReservations = Rental::count();
        $pendingReservations = Rental::where('status', RentalStatus::PREPAID)->count();
        $completedReservations = Rental::where('status', RentalStatus::RETURNED)->count();

        $rentals = Rental::with(['user:id,name', 'car:id,brand,name'])
            ->latest()
            ->paginate(6)
            ->withQueryString()
            ->through(function (Rental $rental) {
                return [
                    'id' => $rental->id,
                    'booking_id' => $rental->id,
                    'customer_name' => $rental->user?->name,
                    'car_model' => trim(($rental->car?->brand ?? '') . ' ' . ($rental->car?->name ?? '')),
                    'start_date' => $rental->start_date?->toDateString(),
                    'end_date' => $rental->end_date?->toDateString(),
                    'total_price' => (int) ($rental->total_price ?? 0),
                    'status' => $rental->status instanceof RentalStatus ? $rental->status->value : (string) $rental->status,
                ];
            });

        $customers = User::query()
            ->where('role', '!=', User::ROLE_ADMIN)
            ->orderBy('name')
            ->get()
            ->map(fn (User $u) => ['id' => $u->id, 'name' => $u->name])
            ->all();

        $availableCars = Car::query()
            ->where('status', CarStatus::AVAILABLE)
            ->get()
            ->map(fn (Car $c) => ['id' => $c->id, 'brand' => $c->brand, 'model' => $c->name ?? $c->model ?? ''])
            ->all();

        return view('backoffice.reservations', [
            'admin' => request()->user(),
            'rentals' => $rentals,
            'pagination' => $this->paginationWindow($rentals->currentPage(), $rentals->lastPage()),
            'customers' => $customers,
            'availableCars' => $availableCars,
            'summary' => [
                'total' => $totalReservations,
                'pending' => $pendingReservations,
                'completed' => $completedReservations,
            ],
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

    private function carStatusMeta(Car $car, bool $isRented): array
    {
        if ($car->status === CarStatus::AVAILABLE) {
            return ['label' => 'TERSEDIA', 'tone' => 'green', 'note' => 'Optimal'];
        }

        if ($isRented) {
            return ['label' => 'DISEWA', 'tone' => 'blue', 'note' => 'Aktif'];
        }

        return ['label' => 'MAINTENANCE', 'tone' => 'red', 'note' => 'Perlu tindakan'];
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
}
