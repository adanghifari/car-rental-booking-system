<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifikasi - MD CAR RENTAL</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
    <body class="bg-[#F5F7FB] text-[#1E293B] antialiased min-h-screen flex flex-col justify-between">

    <x-frontliner.navbar />
    @php
        $summaryUnread = $unreadCount ?? 0;
        $summaryTotal = $notifications->total();
        $summaryRead = max($summaryTotal - $summaryUnread, 0);
    @endphp

    <main class="flex-grow max-w-6xl mx-auto px-4 lg:px-8 py-10 w-full space-y-8">
        <section class="relative overflow-hidden rounded-[2rem] bg-gradient-to-br from-[#123C7A] via-[#1E4E9A] to-[#2C6DD5] text-white p-8 shadow-2xl shadow-blue-200/40">
            <div class="absolute inset-0 opacity-30 bg-[radial-gradient(circle_at_top_right,_rgba(255,255,255,0.24),_transparent_35%),radial-gradient(circle_at_bottom_left,_rgba(191,219,254,0.18),_transparent_32%)]"></div>
            <div class="relative flex flex-col gap-6 md:flex-row md:items-end md:justify-between">
                <div class="max-w-2xl space-y-3">
                    <span class="inline-flex items-center px-3 py-1 rounded-full bg-white/10 border border-white/10 text-[11px] font-bold uppercase tracking-[0.2em] text-blue-100">Inbox Rental</span>
                    <div>
                        <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight">Notifikasi Booking</h1>
                        <p class="mt-2 text-sm md:text-base text-slate-200 leading-7">
                            Semua update penting untuk verifikasi, pembayaran, dan status rental Anda dikumpulkan di satu inbox yang lebih mudah dipindai.
                        </p>
                    </div>
                </div>

                <div class="flex flex-wrap items-stretch gap-3">
                    <div class="min-w-[9rem] rounded-2xl bg-white/10 border border-white/10 px-4 py-3 backdrop-blur">
                        <p class="text-[11px] uppercase tracking-[0.18em] text-blue-100">Belum dibaca</p>
                        <p class="text-2xl font-extrabold mt-1">{{ $summaryUnread }}</p>
                    </div>
                    <div class="min-w-[9rem] rounded-2xl bg-white/10 border border-white/10 px-4 py-3 backdrop-blur">
                        <p class="text-[11px] uppercase tracking-[0.18em] text-blue-100">Total Notifikasi</p>
                        <p class="text-2xl font-extrabold mt-1">{{ $summaryTotal }}</p>
                    </div>
                    <div class="min-w-[9rem] rounded-2xl bg-white/10 border border-white/10 px-4 py-3 backdrop-blur">
                        <p class="text-[11px] uppercase tracking-[0.18em] text-blue-100">Sudah dibaca</p>
                        <p class="text-2xl font-extrabold mt-1">{{ $summaryRead }}</p>
                    </div>
                    @if($summaryUnread > 0)
                        <form method="POST" action="{{ route('notifications.read-all') }}">
                            @csrf
                            <button type="submit" class="inline-flex items-center gap-2 px-4 py-3 rounded-2xl bg-white text-slate-900 font-bold text-sm shadow-lg shadow-black/10 hover:-translate-y-0.5 transition">
                                <span>✓</span>
                                Tandai Semua Dibaca
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </section>

        <section class="space-y-4">
            @forelse($notifications as $notification)
                @php
                    $data = $notification->data ?? [];
                    $type = strtoupper((string) ($data['type'] ?? 'SYSTEM'));
                    $meta = match ($type) {
                        'VERIFICATION' => ['label' => 'Verifikasi', 'tone' => 'blue', 'icon' => 'M9 12l2 2 4-4m6-2.25A11.96 11.96 0 0112 3.75a11.96 11.96 0 01-9 3.75v4.5c0 4.98 3.44 9.36 9 10.5 5.56-1.14 9-5.52 9-10.5v-4.5z'],
                        'PAYMENT' => ['label' => 'Pembayaran', 'tone' => 'amber', 'icon' => 'M21 12.75V19.5A2.25 2.25 0 0118.75 21h-13.5A2.25 2.25 0 013 18.75v-13.5A2.25 2.25 0 015.25 3H12M16.5 3l4.5 4.5M21 3l-7.5 7.5'],
                        'RENTAL' => ['label' => 'Rental', 'tone' => 'emerald', 'icon' => 'M3 10.5h18M6 6.75h12M6 14.25h12M6 18h8.25'],
                        'REVIEW_REQUEST' => ['label' => 'Permintaan Review', 'tone' => 'purple', 'icon' => 'M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345h5.561a.563.563 0 01.330 1.004l-4.508 3.286a.563.563 0 00-.183.607l2.125 5.111a.562.562 0 01-.856.666l-4.508-3.286a.562.562 0 00-.663 0l-4.508 3.286a.562.562 0 01-.856-.666l2.125-5.111a.563.563 0 00-.183-.607l-4.508-3.286a.563.563 0 01.330-1.004h5.561a.563.563 0 00.475-.345L11.48 3.5z'],
                        'CANCELLATION' => ['label' => 'Pembatalan', 'tone' => 'rose', 'icon' => 'M6.75 6.75l10.5 10.5M17.25 6.75l-10.5 10.5'],
                        default => ['label' => 'Sistem', 'tone' => 'slate', 'icon' => 'M12 9v4.5m0 3.75h.008M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                    };
                    $isUnread = is_null($notification->read_at);
                    $createdAt = $notification->created_at ? $notification->created_at->locale('id')->diffForHumans() : '';
                    $title = (string) ($data['title'] ?? 'Notifikasi');
                    $message = (string) ($data['message'] ?? '');
                    $url = route('notifications.open', $notification->id);
                    $toneClasses = match ($meta['tone']) {
                        'blue' => ['bg-blue-50 text-blue-600 border-blue-100', 'bg-blue-600/10 text-blue-700'],
                        'amber' => ['bg-amber-50 text-amber-600 border-amber-100', 'bg-amber-600/10 text-amber-700'],
                        'emerald' => ['bg-emerald-50 text-emerald-600 border-emerald-100', 'bg-emerald-600/10 text-emerald-700'],
                        'rose' => ['bg-rose-50 text-rose-600 border-rose-100', 'bg-rose-600/10 text-rose-700'],
                        'purple' => ['bg-purple-50 text-purple-600 border-purple-100', 'bg-purple-600/10 text-purple-700'],
                        default => ['bg-slate-50 text-slate-500 border-slate-100', 'bg-slate-600/10 text-slate-700'],
                    };
                @endphp

                <article class="group rounded-[1.75rem] border {{ $isUnread ? 'border-blue-200 bg-white shadow-lg shadow-blue-100/40' : 'border-slate-200 bg-white/95' }} overflow-hidden transition hover:-translate-y-0.5 hover:border-blue-100 hover:shadow-xl hover:shadow-slate-200/60">
                    <div class="p-5 md:p-6 flex flex-col md:flex-row gap-4 md:items-start md:justify-between">
                        <div class="flex gap-4 min-w-0">
                            <div class="relative shrink-0">
                                <div class="w-12 h-12 rounded-2xl border {{ $toneClasses[0] }} flex items-center justify-center">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $meta['icon'] }}"></path>
                                    </svg>
                                </div>
                                @if($isUnread)
                                    <span class="absolute -right-1 -top-1 inline-flex h-3.5 w-3.5 rounded-full bg-blue-500 ring-4 ring-white"></span>
                                @endif
                            </div>

                            <div class="min-w-0 space-y-3">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold tracking-wide {{ $toneClasses[1] }}">
                                        {{ $meta['label'] }}
                                    </span>
                                    @if($isUnread)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold tracking-wide bg-blue-600 text-white">
                                            Baru
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold tracking-wide bg-slate-100 text-slate-500">
                                            Dibaca
                                        </span>
                                    @endif
                                </div>

                                <div>
                                    <h2 class="text-base md:text-lg font-bold text-slate-900 group-hover:text-blue-700 transition">{{ $title }}</h2>
                                    <p class="mt-2 text-sm text-slate-600 leading-6 max-w-3xl">
                                        {{ $message }}
                                    </p>
                                </div>

                                <div class="flex flex-wrap items-center gap-3 text-xs text-slate-400">
                                    <span>{{ $createdAt }}</span>
                                    @if(! empty($data['rental_id']))
                                        <span class="text-slate-300">•</span>
                                        <span>Booking #{{ $data['rental_id'] }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-col sm:flex-row md:flex-col lg:flex-row gap-3 md:items-end">
                            <a href="{{ $url }}" class="inline-flex items-center justify-center px-4 py-2.5 rounded-xl bg-slate-900 text-white font-semibold text-sm hover:bg-blue-700 transition">
                                Buka Detail
                            </a>
                            @if($isUnread)
                                <form method="POST" action="{{ route('notifications.read', $notification->id) }}">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center justify-center px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-700 font-semibold text-sm hover:bg-slate-50 transition">
                                        Tandai Dibaca
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </article>
            @empty
                <div class="rounded-[1.75rem] border border-slate-200 bg-white p-10 text-center shadow-sm">
                    <div class="w-14 h-14 mx-auto rounded-2xl bg-slate-50 text-slate-400 flex items-center justify-center">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.182 15.182a4.5 4.5 0 11-6.364-6.364 4.5 4.5 0 016.364 6.364z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 22a8.5 8.5 0 118-4.4"></path>
                        </svg>
                    </div>
                    <h2 class="mt-4 text-lg font-bold text-slate-900">Belum ada notifikasi</h2>
                    <p class="mt-2 text-sm text-slate-500">
                        Update booking, pembayaran, dan status rental akan muncul di sini.
                    </p>
                </div>
            @endforelse
        </section>

        @if($notifications->hasPages())
            <div class="flex items-center justify-between gap-4 flex-col sm:flex-row bg-white border border-slate-200 rounded-2xl px-4 py-4 shadow-sm">
                <div class="text-sm text-slate-500">
                    Menampilkan {{ $notifications->firstItem() ?? 0 }} - {{ $notifications->lastItem() ?? 0 }} dari {{ $notifications->total() }}
                </div>
                <div class="flex items-center gap-1.5">
                    @if ($notifications->onFirstPage())
                        <span class="px-3 py-1.5 rounded-lg border border-slate-200 text-slate-300 text-xs cursor-not-allowed select-none">&lt;</span>
                    @else
                        <a href="{{ $notifications->previousPageUrl() }}" class="px-3 py-1.5 rounded-lg border border-slate-200 text-slate-500 hover:bg-blue-50 text-xs transition">&lt;</a>
                    @endif

                    @foreach ($notifications->getUrlRange(1, $notifications->lastPage()) as $page => $url)
                        @if ($page == $notifications->currentPage())
                            <span class="px-3 py-1.5 rounded-lg bg-blue-600 text-white font-bold text-xs select-none">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="px-3 py-1.5 rounded-lg border border-slate-200 text-slate-500 hover:bg-blue-50 text-xs transition">{{ $page }}</a>
                        @endif
                    @endforeach

                    @if ($notifications->hasMorePages())
                        <a href="{{ $notifications->nextPageUrl() }}" class="px-3 py-1.5 rounded-lg border border-slate-200 text-slate-500 hover:bg-blue-50 text-xs transition">&gt;</a>
                    @else
                        <span class="px-3 py-1.5 rounded-lg border border-slate-200 text-slate-300 text-xs cursor-not-allowed select-none">&gt;</span>
                    @endif
                </div>
            </div>
        @endif
    </main>

    <footer class="bg-gray-900 text-gray-400 py-6 border-t border-gray-800 mt-12">
        <div class="max-w-7xl mx-auto px-4 lg:px-8 text-center text-xs">
            <p>&copy; 2026 MD CAR RENTAL. All rights reserved.</p>
        </div>
    </footer>

</body>
</html>
