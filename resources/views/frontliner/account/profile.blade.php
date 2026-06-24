<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Saya - MD CAR RENTAL</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-[#F8F9FC] text-[#1E293B] antialiased min-h-screen flex flex-col justify-between">

    <x-frontliner.navbar />

    <main class="flex-grow max-w-7xl mx-auto px-4 lg:px-8 py-10 w-full">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <aside class="bg-white p-5 rounded-3xl border border-gray-100 shadow-sm space-y-2 h-fit lg:sticky lg:top-24">
                <a href="{{ route('customer.profile') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200 bg-[#0B3C9B] text-white shadow-md shadow-blue-500/10">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                    </svg>
                    <span>Profil Saya</span>
                </a>
                <a href="{{ route('customer.settings') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200 text-slate-600 hover:bg-slate-50 hover:text-blue-600">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                    </svg>
                    <span>Ganti Password</span>
                </a>
                <a href="{{ route('customer.payments') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200 text-slate-600 hover:bg-slate-50 hover:text-blue-600">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z" />
                    </svg>
                    <span>Pembayaran</span>
                </a>
            </aside>

            <div class="lg:col-span-3 space-y-6">
                @if (session('success'))
                    <div class="p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded-r-2xl text-xs font-semibold shadow-sm flex items-center gap-2">
                        <span>✓</span>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6 sm:p-8 space-y-6">
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">Detail Profil</h2>
                        <p class="text-xs text-gray-500 mt-1">Perbarui informasi dasar akun Anda di sini.</p>
                    </div>

                    <form action="{{ route('customer.profile.update') }}" method="POST" class="space-y-5">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div class="space-y-1.5">
                                <label for="name" class="block text-xs font-bold text-gray-400 uppercase tracking-wider">Nama Lengkap</label>
                                <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm font-medium text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500/30 transition">
                                @error('name')
                                    <p class="text-red-500 text-xs font-semibold mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="space-y-1.5">
                                <label for="username" class="block text-xs font-bold text-gray-400 uppercase tracking-wider">Username</label>
                                <input type="text" name="username" id="username" value="{{ old('username', $user->username) }}" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm font-medium text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500/30 transition">
                                @error('username')
                                    <p class="text-red-500 text-xs font-semibold mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="space-y-1.5">
                            <label for="email" class="block text-xs font-bold text-gray-400 uppercase tracking-wider">Alamat Email</label>
                            <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm font-medium text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500/30 transition">
                            @error('email')
                                <p class="text-red-500 text-xs font-semibold mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-1.5">
                            <label for="phone" class="block text-xs font-bold text-gray-400 uppercase tracking-wider">Nomor Telepon</label>
                            <input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone) }}" maxlength="15" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm font-medium text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500/30 transition">
                            @error('phone')
                                <p class="text-red-500 text-xs font-semibold mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="pt-2 flex justify-end">
                            <button type="submit" class="bg-[#0B3C9B] hover:bg-[#082D76] active:scale-[0.98] text-white font-bold py-3.5 px-8 rounded-xl text-xs transition-all duration-200 shadow-md shadow-blue-200 uppercase tracking-wider">
                                Simpan Perubahan Profil
                            </button>
                        </div>
                    </form>
                </div>
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
