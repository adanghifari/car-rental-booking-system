@php
    $companySetting = \App\Models\CompanySetting::current();
@endphp

<footer class="bg-[#11161B] text-gray-400 text-sm pt-16 pb-8 border-t border-gray-800">
    <div class="max-w-7xl mx-auto px-4 md:px-6">
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-10 md:gap-8 pb-12">
            
            <!-- KOLOM KIRI: Identitas Perusahaan -->
            <div class="space-y-4">
                <div class="flex items-center space-x-3">
                    <img src="{{ asset('images/logo-footer.png') }}" alt="{{ $companySetting->company_name }}" class="h-auto" style="height: 120px;">
                    <h3 class="text-white font-bold text-lg tracking-tight">{{ $companySetting->company_name }}</h3>
                </div>
                <p class="text-xs text-gray-400 leading-relaxed max-w-sm">
                    {{ $companySetting->company_description }}
                </p>
                <p class="text-xs text-gray-400 leading-relaxed max-w-sm">
                    Email:
                    <a href="mailto:{{ $companySetting->company_email }}" class="font-medium text-gray-300 hover:text-white transition">
                        {{ $companySetting->company_email }}
                    </a>
                </p>
            </div>

            <!-- KOLOM TENGAH: Menu Navigasi & Bantuan -->
            <div class="space-y-4">
                <h4 class="text-white font-bold tracking-wider uppercase text-xs">Informasi</h4>
                <ul class="space-y-2.5 text-xs font-medium">
                    <li><a href="https://wa.me/6282155402629" class="hover:text-white transition">Kontak Bantuan</a></li>
                    <li><a href="#" class="hover:text-white transition">Cara Sewa</a></li>
                    <li><a href="#" class="hover:text-white transition">FAQ</a></li>
                    <li><a href="{{ route('terms.show') }}" class="hover:text-white transition">Ketentuan Layanan</a></li>
                    <li><a href="{{ route('terms.show') }}" class="hover:text-white transition">Syarat & Ketentuan</a></li>
                    <li><a href="{{ route('privacy.show') }}" class="hover:text-white transition">Kebijakan Privasi</a></li>
                </ul>
            </div>

            <div class="space-y-4">
                <h4 class="text-white font-bold tracking-wider uppercase text-xs">Lokasi Utama</h4>
                
                <a href="{{ $companySetting->effective_directions_url }}" target="_blank" rel="noopener noreferrer" class="relative block w-full max-w-sm overflow-hidden bg-[#1E2329] border border-gray-700/60 rounded-xl p-0 hover:border-gray-500 transition group">
                    <div class="relative h-[112px] overflow-hidden">
                        <iframe
                            src="{{ $companySetting->effective_embed_url }}"
                            class="absolute inset-0 w-full h-full pointer-events-none grayscale-[0.15] contrast-110"
                            loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"
                            aria-hidden="true"></iframe>
                        <div class="absolute inset-0 bg-slate-950/35 group-hover:bg-slate-950/20 transition"></div>
                        <div class="absolute inset-x-0 bottom-3 flex flex-col items-center justify-end text-center space-y-1 px-4">
                            <div class="w-10 h-10 rounded-full bg-white/14 backdrop-blur-sm border border-white/20 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-white">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                                </svg>
                            </div>
                            <span class="text-[10px] font-medium text-white/95">Buka Peta</span>
                        </div>
                    </div>
                </a>

                <div class="flex items-start space-x-2 text-xs text-gray-400 max-w-sm pt-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mt-0.5 shrink-0 text-gray-400">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                    </svg>
                    <p class="leading-relaxed font-medium">
                        {!! nl2br(e(str_replace(', ', ",\n", $companySetting->address))) !!}
                    </p>
                </div>
            </div>

        </div>

        <div class="border-t border-gray-800/80 pt-6 mt-4 flex flex-col sm:flex-row justify-between items-center text-[11px] font-medium tracking-wide space-y-3 sm:space-y-0 text-gray-500">
            <div>
                &copy; {{ date('Y') }} Rental Mobil. All rights reserved.
            </div>
            <div class="flex space-x-6">
                <a href="{{ route('terms.show') }}" class="hover:text-gray-300 transition">Syarat & Ketentuan</a>
                <a href="{{ route('privacy.show') }}" class="hover:text-gray-300 transition">Kebijakan Privasi</a>
                <a href="#" class="hover:text-gray-300 transition">Cookies</a>
            </div>
        </div>

    </div>
</footer>

<div class="fixed bottom-6 right-6 z-50 flex flex-col gap-3">
    <x-frontliner.contact />
    <x-frontliner.chatbot />
</div>
