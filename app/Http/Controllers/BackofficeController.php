<?php

namespace App\Http\Controllers;

use App\Enums\CarStatus;
use App\Enums\PaymentStatus;
use App\Enums\RentalStatus;
use App\Models\Car;
use App\Models\PaymentHistory;
use App\Models\Rental;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Storage;

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
                'name' => $topCar?->name ?? 'Belum ada armada unggulan',
                'description' => $topCar?->description ?? 'Tambahkan transaksi rental untuk melihat armada dengan performa terbaik.',
                'revenue' => (int) ($topCar?->rentals_sum_total_price ?? 0),
                'rentals_count' => (int) ($topCar?->rentals_count ?? 0),
            ],
            'monthLabels' => $monthLabels,
        ]);
    }

    public function users(): View
    {
        $users = User::query()
            ->withCount('rentals')
            ->withSum('rentals', 'total_price')
            ->latest()
            ->paginate(10)
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

    public function cars(): View
    {
        $activeRentalCarIds = Rental::query()
            ->whereIn('status', [RentalStatus::PREPAID, RentalStatus::ONGOING])
            ->pluck('car_id')
            ->filter()
            ->unique()
            ->values();

        $totalCars = Car::count();
        $availableCars = Car::where('status', CarStatus::AVAILABLE)->count();
        $unavailableCars = Car::where('status', CarStatus::UNAVAILABLE)->count();
        $rentedCars = Car::query()->whereIn('id', $activeRentalCarIds)->count();
        $maintenanceCars = max($unavailableCars - $rentedCars, 0);

        $cars = Car::query()
            ->with(['rentals' => fn ($query) => $query->latest()])
            ->latest()
            ->take(4)
            ->get()
            ->map(function (Car $car) use ($activeRentalCarIds) {
                [$brand, $model] = $this->splitCarName($car->name);
                $status = $this->carStatusMeta($car, $activeRentalCarIds->contains($car->id));

                return [
                    'brand' => $brand,
                    'model' => $model,
                    'price' => (int) $car->rental_fee,
                    'price_label' => number_format((int) $car->rental_fee, 0, ',', '.'),
                    'rating' => number_format($car->rating ?: 5, 1),
                    'status' => $status['label'],
                    'status_tone' => $status['tone'],
                    'status_note' => $status['note'],
                    'transmission' => str($car->transmission)->headline()->value(),
                    'seat' => $car->seat.' Kursi',
                    'type' => str($car->type)->headline()->value(),
                    'plate' => strtoupper($car->license_plate),
                    'image_url' => $this->resolveCarImageUrl($car->image),
                ];
            });

        $maintenanceRows = Car::query()
            ->with(['rentals' => fn ($query) => $query->latest()])
            ->latest()
            ->take(3)
            ->get()
            ->map(function (Car $car) use ($activeRentalCarIds) {
                $isRented = $activeRentalCarIds->contains($car->id);
                $isMaintenance = $car->status === CarStatus::UNAVAILABLE && ! $isRented;
                $latestRental = $car->rentals->first();

                return [
                    'name' => $car->name,
                    'plate' => strtoupper($car->license_plate),
                    'status' => $isMaintenance ? 'MENUNGGU' : ($isRented ? 'TERJADWAL' : 'SELESAI'),
                    'status_tone' => $isMaintenance ? 'amber' : ($isRented ? 'blue' : 'green'),
                    'last_service' => optional($latestRental?->created_at ?? $car->updated_at)->translatedFormat('d M Y'),
                    'mileage' => '—',
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
            'cars' => $cars,
            'maintenanceRows' => $maintenanceRows,
        ]);
    }

    private function paginationWindow(int $currentPage, int $lastPage): array
    {
        if ($lastPage <= 5) {
            return range(1, max(1, $lastPage));
        }

        return [1, 2, 3, '...', $lastPage];
    }

    private function splitCarName(string $name): array
    {
        $segments = preg_split('/\s+/', trim($name), 2) ?: [];

        return [
            strtoupper($segments[0] ?? $name),
            $segments[1] ?? $name,
        ];
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
}
