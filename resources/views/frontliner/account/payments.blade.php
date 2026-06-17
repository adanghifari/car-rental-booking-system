<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pembayaran - MD CAR RENTAL</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-[#F8F9FC] text-[#1E293B] antialiased min-h-screen flex flex-col justify-between">

    <x-frontliner.navbar />

    <!-- Main Content -->
    <main class="flex-grow max-w-7xl mx-auto px-4 lg:px-8 py-10 w-full">
        
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <!-- Left Sidebar Navigation -->
            <aside class="bg-white p-5 rounded-3xl border border-gray-100 shadow-sm space-y-2 h-fit lg:sticky lg:top-24">
                <a href="{{ route('customer.profile') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200 text-slate-600 hover:bg-slate-50 hover:text-blue-600">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                    </svg>
                    <span>Profil Saya</span>
                </a>
                <a href="{{ route('customer.settings') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200 text-slate-600 hover:bg-slate-50 hover:text-blue-600">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                    </svg>
                    <span>Pengaturan</span>
                </a>
                <a href="{{ route('customer.payments') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200 bg-[#0B3C9B] text-white shadow-md shadow-blue-500/10">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z" />
                    </svg>
                    <span>Pembayaran</span>
                </a>
            </aside>

            <!-- Right Content Area -->
            <div class="lg:col-span-3 space-y-6">
                <div>
                    <h2 class="text-xl font-bold text-gray-900">Riwayat Transaksi</h2>
                    <p class="text-xs text-gray-500 mt-1">Daftar seluruh pembayaran reservasi sewa armada Anda.</p>
                </div>

                @forelse($payments as $payment)
                    @php
                        $status = $payment->status;
                        $badgeBg = 'bg-slate-50 text-slate-600 border-slate-200';
                        $statusText = 'Tidak Diketahui';

                        if ($status === \App\Enums\PaymentStatus::PAID) {
                            $badgeBg = 'bg-green-50 text-green-700 border-green-200';
                            $statusText = 'Berhasil';
                        } elseif ($status === \App\Enums\PaymentStatus::PENDING) {
                            $badgeBg = 'bg-yellow-50 text-yellow-700 border-yellow-200';
                            $statusText = 'Menunggu';
                        } elseif ($status === \App\Enums\PaymentStatus::CANCELLED) {
                            $badgeBg = 'bg-rose-50 text-rose-700 border-rose-200';
                            $statusText = 'Dibatalkan';
                        } elseif ($status === \App\Enums\PaymentStatus::EXPIRED) {
                            $badgeBg = 'bg-rose-50 text-rose-700 border-rose-200';
                            $statusText = 'Kedaluwarsa';
                        }
                    @endphp

                    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 hover:-translate-y-0.5 hover:shadow-md transition-all duration-200">
                        <div class="flex items-center gap-4">
                            <!-- Icon/Illustration -->
                            <div class="w-12 h-12 rounded-2xl bg-blue-50 flex items-center justify-center shrink-0">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z" />
                                </svg>
                            </div>
                            <div class="space-y-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h3 class="text-sm font-bold text-gray-900">
                                        {{ $payment->rental->car->brand ?? 'Mobil' }} {{ $payment->rental->car->name ?? '' }}
                                    </h3>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold border {{ $badgeBg }}">
                                        {{ $statusText }}
                                    </span>
                                </div>
                                <p class="text-xs text-gray-400 font-medium">Order ID: {{ $payment->provider_order_id }}</p>
                                <p class="text-xs text-gray-400 font-medium">{{ $payment->created_at->locale('id')->format('d M Y, H:i') }} WIB</p>
                            </div>
                        </div>

                        <div class="flex flex-col md:items-end gap-2 w-full md:w-auto border-t md:border-t-0 pt-3 md:pt-0 border-gray-50">
                            <span class="text-sm font-extrabold text-slate-800">
                                Rp {{ number_format($payment->amount, 0, ',', '.') }}
                            </span>
                            <a href="{{ route('booking.detail', $payment->rental_id) }}" class="text-xs font-bold text-[#0B3C9B] hover:text-[#082D76] inline-flex items-center gap-1 transition-colors duration-200">
                                <span>Lihat Rincian</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                                </svg>
                            </a>
                        </div>
                    </div>
                @empty
                    <!-- Empty State -->
                    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-12 text-center space-y-4">
                        <div class="w-16 h-16 mx-auto rounded-3xl bg-slate-50 flex items-center justify-center text-slate-400">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z" />
                            </svg>
                        </div>
                        <div class="space-y-1">
                            <h3 class="text-base font-bold text-gray-900">Belum Ada Transaksi</h3>
                            <p class="text-xs text-gray-500 max-w-sm mx-auto">Anda belum memiliki riwayat transaksi pembayaran sewa mobil di platform kami.</p>
                        </div>
                        <div>
                            <a href="{{ route('armada') }}" class="inline-flex items-center justify-center bg-[#0B3C9B] hover:bg-[#082D76] text-white font-bold py-3 px-6 rounded-xl text-xs transition-all duration-200 uppercase tracking-wider">
                                Sewa Armada Sekarang
                            </a>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>

    </main>

    <footer class="bg-gray-900 text-gray-400 py-6 border-t border-gray-800">
        <div class="max-w-7xl mx-auto px-4 lg:px-8 text-center text-xs">
            <p>&copy; 2026 MD CAR RENTAL. All rights reserved.</p>
        </div>
    </footer>

</body>
</html>
