<x-backoffice.layout title="Profil Perusahaan - MD CAR RENTAL" :admin="$admin" active="settings" search-placeholder="Cari pengaturan...">
    
    <div class="max-w-4xl mx-auto mt-6 space-y-8">
        
        @if (session('success'))
            <div class="p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded-r-2xl text-xs font-semibold shadow-sm flex items-center gap-2">
                <span>✓</span>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6 sm:p-8 space-y-8">
            <div>
                <h2 class="text-xl font-bold text-gray-900">Profil Perusahaan</h2>
                <p class="text-xs text-gray-500 mt-1">Kelola data perusahaan untuk footer serta chatbot di sini.</p>
            </div>

            <form action="{{ route('backoffice.company-settings.update') }}" method="POST" class="space-y-5">
                @csrf
                @method('PUT')

                <div class="space-y-1.5">
                    <label for="company_name" class="block text-xs font-bold text-gray-400 uppercase tracking-wider">Nama Perusahaan</label>
                    <input type="text" name="company_name" id="company_name" value="{{ old('company_name', $companySetting->company_name) }}" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm font-medium text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500/30 transition">
                    @error('company_name')
                        <p class="text-red-500 text-xs font-semibold mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-1.5">
                    <label for="company_email" class="block text-xs font-bold text-gray-400 uppercase tracking-wider">Alamat Email</label>
                    <input type="email" name="company_email" id="company_email" value="{{ old('company_email', $companySetting->company_email) }}" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm font-medium text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500/30 transition">
                    @error('company_email')
                        <p class="text-red-500 text-xs font-semibold mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-1.5">
                    <label for="company_description" class="block text-xs font-bold text-gray-400 uppercase tracking-wider">Deskripsi Perusahaan</label>
                    <textarea name="company_description" id="company_description" rows="4" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm font-medium text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500/30 transition">{{ old('company_description', $companySetting->company_description) }}</textarea>
                    @error('company_description')
                        <p class="text-red-500 text-xs font-semibold mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-1.5">
                    <label for="address" class="block text-xs font-bold text-gray-400 uppercase tracking-wider">Alamat</label>
                    <textarea name="address" id="address" rows="4" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm font-medium text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500/30 transition">{{ old('address', $companySetting->address) }}</textarea>
                    @error('address')
                        <p class="text-red-500 text-xs font-semibold mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-1.5">
                    <label for="maps_directions_url" class="block text-xs font-bold text-gray-400 uppercase tracking-wider">Link Google Maps</label>
                    <input type="url" name="maps_directions_url" id="maps_directions_url" value="{{ old('maps_directions_url', $companySetting->maps_directions_url) }}" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm font-medium text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500/30 transition">
                    @error('maps_directions_url')
                        <p class="text-red-500 text-xs font-semibold mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="pt-2 flex justify-end">
                    <button type="submit" class="bg-[#0B3C9B] hover:bg-[#082D76] active:scale-[0.98] text-white font-bold py-3.5 px-8 rounded-xl text-xs transition-all duration-200 shadow-md shadow-blue-200 uppercase tracking-wider">
                        Simpan Pengaturan Perusahaan
                    </button>
                </div>
            </form>
        </div>
    </div>

</x-backoffice.layout>
