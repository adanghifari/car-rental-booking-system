<x-backoffice.layout title="Pengaturan Akun - MD CAR RENTAL" :admin="$admin" active="settings" search-placeholder="Cari pengaturan...">
    
    <div class="max-w-4xl mx-auto mt-6 space-y-8">
        
        @if (session('success'))
            <div class="p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded-r-2xl text-xs font-semibold shadow-sm flex items-center gap-2">
                <span>✓</span>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6 sm:p-8 space-y-6">
            <div>
                <h2 class="text-xl font-bold text-gray-900">Detail Profil</h2>
                <p class="text-xs text-gray-500 mt-1">Perbarui informasi dasar akun administrator Anda di sini.</p>
            </div>

            <form action="{{ route('backoffice.profile.update') }}" method="POST" class="space-y-5">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div class="space-y-1.5">
                        <label for="name" class="block text-xs font-bold text-gray-400 uppercase tracking-wider">Nama Lengkap</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $admin->name) }}" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm font-medium text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500/30 transition">
                        @error('name')
                            <p class="text-red-500 text-xs font-semibold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-1.5">
                        <label for="username" class="block text-xs font-bold text-gray-400 uppercase tracking-wider">Username</label>
                        <input type="text" name="username" id="username" value="{{ old('username', $admin->username) }}" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm font-medium text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500/30 transition">
                        @error('username')
                            <p class="text-red-500 text-xs font-semibold mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label for="email" class="block text-xs font-bold text-gray-400 uppercase tracking-wider">Alamat Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $admin->email) }}" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm font-medium text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500/30 transition">
                    @error('email')
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

</x-backoffice.layout>