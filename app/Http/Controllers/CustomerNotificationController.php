<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
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
}
