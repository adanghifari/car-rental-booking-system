<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Midtrans Sandbox Simulator - MD CAR RENTAL</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-[#F3F4F6] text-[#1E293B] antialiased min-h-screen flex items-center justify-center p-4">

    <!-- Simulator Container -->
    <div class="bg-white rounded-3xl shadow-2xl border border-gray-100 max-w-md w-full overflow-hidden">
        
        <!-- Header -->
        <div class="bg-[#1C2C54] text-white p-6 text-center relative">
            <span class="text-xs bg-amber-500/20 text-amber-400 font-extrabold px-3 py-1 rounded-full uppercase tracking-wider mb-2 inline-block border border-amber-500/30">
                🛠️ Mode Simulasi Lokal
            </span>
            <h1 class="text-xl font-extrabold tracking-tight">HD PAYMENTS GATEWAY</h1>
            <p class="text-[10px] text-gray-300 mt-1 uppercase tracking-widest font-semibold">Midtrans Snap Sandbox Mockup</p>
        </div>

        <div class="p-6 space-y-6">
            
            <!-- Rental Info Summary -->
            <div class="bg-[#F8F9FC] border border-gray-100 rounded-2xl p-4 space-y-3">
                <div class="flex justify-between items-center text-xs">
                    <span class="text-gray-400">Order ID:</span>
                    <span class="font-bold text-gray-800">{{ $rental->booking_code ?? ('BOOK-'.($rental->created_at?->format('Ymd') ?? now()->format('Ymd')).'-'.str_pad($rental->id, 4, '0', STR_PAD_LEFT)) }}</span>
                </div>
                <div class="flex justify-between items-center text-xs">
                    <span class="text-gray-400">Mobil:</span>
                    <span class="font-bold text-gray-800">{{ $rental->car->brand }} {{ $rental->car->name }}</span>
                </div>
                <div class="flex justify-between items-center text-xs">
                    <span class="text-gray-400">Penyewa:</span>
                    <span class="font-bold text-gray-800">{{ $rental->user->name }}</span>
                </div>
                <div class="pt-3 border-t border-dashed border-gray-200 flex justify-between items-center">
                    <span class="text-xs font-bold text-gray-400 uppercase">Total Tagihan</span>
                    <span class="text-xl font-extrabold text-[#0B3C9B]">Rp {{ number_format($rental->total_price, 0, ',', '.') }}</span>
                </div>
            </div>

            <!-- Simulated QRIS / Card Panel -->
            <div class="border border-gray-200 rounded-2xl p-6 text-center space-y-4">
                <p class="text-xs font-bold text-gray-500 uppercase tracking-wide">Pindai QRIS atau Konfirmasi Simulasi</p>
                
                <!-- Dummy QR Code -->
                <div class="w-36 h-36 bg-white border-4 border-gray-900 mx-auto flex items-center justify-center p-2 rounded-xl relative group shadow-sm">
                    <div class="grid grid-cols-6 grid-rows-6 gap-1 w-full h-full opacity-80">
                        @for($i = 0; $i < 36; $i++)
                            <div class="rounded-sm {{ (rand(0, 10) > 4 || $i < 6 || $i % 6 == 0 || $i > 30) ? 'bg-black' : 'bg-transparent' }}"></div>
                        @endfor
                    </div>
                    <span class="absolute inset-0 m-auto w-8 h-8 bg-[#0B3C9B] rounded-lg text-white font-extrabold flex items-center justify-center text-[10px] shadow-md border border-white">
                        HD
                    </span>
                </div>
                <p class="text-[10px] text-gray-400 italic">Pindai kode QR di atas dengan aplikasi e-wallet simulator Anda</p>
            </div>

            <!-- Action Buttons -->
            <div class="space-y-3">
                <form action="{{ route('booking.simulate-payment.submit') }}" method="POST">
                    @csrf
                    <input type="hidden" name="rental_id" value="{{ $rental->id }}">
                    <button type="submit" class="w-full bg-[#10B981] hover:bg-[#0D9668] active:scale-[0.98] text-white font-bold py-3.5 rounded-xl text-xs transition duration-200 shadow-lg shadow-emerald-100 uppercase tracking-wider">
                        ✓ Selesaikan Pembayaran (Simulasi Sukses)
                    </button>
                </form>

                <a href="{{ route('frontliner') }}" class="block text-center w-full bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold py-3.5 rounded-xl text-xs transition duration-200 uppercase tracking-wider">
                    ✗ Batalkan / Bayar Nanti
                </a>
            </div>
            
        </div>

        <!-- Simulator Footer Info -->
        <div class="bg-gray-50 px-6 py-4 text-center border-t border-gray-100">
            <p class="text-[9px] text-gray-400">Copyright &copy; Midtrans Simulator. Halaman ini hanya tampil dalam mode `local` development.</p>
        </div>

    </div>

</body>
</html>
