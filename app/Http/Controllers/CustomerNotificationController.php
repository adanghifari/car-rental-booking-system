<?php

namespace App\Http\Controllers;

use App\Models\Rental;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\View\View;

class CustomerNotificationController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        if (! $user->hasNotificationsTable()) {
            return view('frontliner.pages.notifications', [
                'notifications' => new LengthAwarePaginator([], 0, 12),
                'unreadCount' => 0,
            ]);
        }

        $notifications = $user->notifications()
            ->latest()
            ->paginate(12);

        return view('frontliner.pages.notifications', [
            'notifications' => $notifications,
            'unreadCount' => $user->unreadNotificationCount(),
        ]);
    }

    public function markRead(Request $request, string $notification): RedirectResponse
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        if (! $user->hasNotificationsTable()) {
            return back()->with('success', 'Notifikasi belum tersedia di database.');
        }

        $item = $user->notifications()->whereKey($notification)->firstOrFail();
        $item->markAsRead();

        return back()->with('success', 'Notifikasi ditandai sudah dibaca.');
    }

    public function open(Request $request, string $notification): RedirectResponse
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        if (! $user->hasNotificationsTable()) {
            return redirect()->route('notifications.index');
        }

        $item = $user->notifications()->whereKey($notification)->firstOrFail();

        if (is_null($item->read_at)) {
            $item->markAsRead();
        }

        return redirect()->to($this->resolveNotificationUrl($item, $user));
    }

    public function markAllRead(Request $request): RedirectResponse
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        if (! $user->hasNotificationsTable()) {
            return back()->with('success', 'Notifikasi belum tersedia di database.');
        }

        $user->unreadNotifications()->update(['read_at' => now()]);

        return back()->with('success', 'Semua notifikasi telah ditandai sudah dibaca.');
    }

    private function resolveNotificationUrl(DatabaseNotification $notification, User $user): string
    {
        $data = $notification->data ?? [];
        $rentalId = (int) ($data['rental_id'] ?? 0);

        if ($rentalId <= 0) {
            return $data['url'] ?? route('notifications.index');
        }

        $rental = Rental::with(['paymentHistories' => fn ($query) => $query->latest(), 'review'])
            ->whereKey($rentalId)
            ->where('user_id', $user->id)
            ->first();

        if (! $rental) {
            return route('notifications.index');
        }

        $dedupeKey = (string) ($data['dedupe_key'] ?? '');

        return match ($dedupeKey) {
            'payment-paid' => route('pembayaran.index', ['status' => 'paid']),
            'payment-cancelled' => route('pembayaran.index', ['status' => 'cancelled']),
            'payment-expired' => route('pembayaran.index', ['status' => 'expired']),
            'verification-rejected', 'booking-cancelled' => route('pesanan-saya', ['status' => 'dibatalkan']),
            'rental-returned', 'review-request' => $rental->review
                ? route('booking.detail', ['rental' => $rental->id])
                : route('booking.review', ['rental' => $rental->id]),
            default => $data['url'] ?? route('booking.detail', ['rental' => $rental->id]),
        };
    }
}
