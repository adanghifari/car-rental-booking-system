<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class BackofficeNotificationController extends Controller
{
    public function open(Request $request, string $notification): RedirectResponse
    {
        $admin = $request->user();

        if (! $admin) {
            return redirect()->route('login');
        }

        $item = $this->findAdminNotification($admin, $notification);

        if (is_null($item->read_at)) {
            $item->markAsRead();
        }

        return redirect()->to($item->data['url'] ?? route('backoffice.reservations'));
    }

    public function markRead(Request $request, string $notification): RedirectResponse
    {
        $admin = $request->user();

        if (! $admin) {
            return redirect()->route('login');
        }

        $item = $this->findAdminNotification($admin, $notification);

        $item->markAsRead();

        return back()->with('success', 'Notifikasi admin ditandai sudah dibaca.');
    }

    public function markAllRead(Request $request): RedirectResponse
    {
        $admin = $request->user();

        if (! $admin) {
            return redirect()->route('login');
        }

        $notificationIds = $admin->unreadNotifications()
            ->get()
            ->filter(function ($notification) {
                return ($notification->data['audience'] ?? null) === 'admin';
            })
            ->pluck('id');

        if ($notificationIds->isNotEmpty()) {
            $admin->notifications()
                ->whereIn('id', $notificationIds)
                ->update(['read_at' => now()]);
        }

        return back()->with('success', 'Semua notifikasi admin telah ditandai sudah dibaca.');
    }

    private function findAdminNotification($admin, string $notification)
    {
        return $admin->notifications()
            ->whereKey($notification)
            ->get()
            ->first(function ($item) {
                return ($item->data['audience'] ?? null) === 'admin';
            }) ?? abort(404);
    }
}
