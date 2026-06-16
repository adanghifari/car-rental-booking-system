<!-- Chatbot Floating Widget -->
<div class="fixed bottom-6 right-6 z-50 flex flex-col items-end font-sans">
    
    <!-- Chat Window -->
    <div id="chatbot-window" class="hidden w-96 max-w-[calc(100vw-2rem)] h-[550px] max-h-[85vh] bg-white/95 backdrop-blur-md rounded-2xl shadow-2xl border border-slate-200/60 flex flex-col overflow-hidden transition-all duration-300 transform translate-y-4 opacity-0 scale-95">
        
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-700 via-blue-800 to-indigo-900 text-white px-4 py-4 flex items-center justify-between shadow-md">
            <div class="flex items-center gap-3">
                <div class="relative">
                    <div class="w-10 h-10 bg-white/10 rounded-xl flex items-center justify-center shadow-inner border border-white/20">
                        <svg class="w-5.5 h-5.5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2M5 6h14a3 3 0 013 3v4a3 3 0 01-3 3H5a3 3 0 01-3-3V9a3 3 0 013-3zm4 5h.01M15 11h.01M9 16h6" />
                        </svg>
                    </div>
                    <span class="absolute bottom-0 right-0 w-3 h-3 bg-emerald-500 border-2 border-blue-800 rounded-full"></span>
                </div>
                <div>
                    <h4 class="font-bold text-sm leading-tight">MD Virtual Assistant</h4>
                    <p class="text-[10px] text-blue-200/80 font-medium tracking-wide">Online • Siap membantu</p>
                </div>
            </div>
            
            <button onclick="toggleChatWindow()" class="text-white/80 hover:text-white p-1 rounded-lg hover:bg-white/10 transition-colors cursor-pointer" title="Tutup Chat">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Chat Area -->
        <div id="chatbot-messages" class="flex-grow p-4 overflow-y-auto space-y-4 bg-slate-50/50">
            <!-- Greeting Message -->
            <div class="flex items-start gap-2.5 max-w-[85%]">
                <div class="w-7 h-7 rounded-lg bg-blue-100 flex items-center justify-center shrink-0 border border-blue-200 shadow-sm">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2M5 6h14a3 3 0 013 3v4a3 3 0 01-3 3H5a3 3 0 01-3-3V9a3 3 0 013-3zm4 5h.01M15 11h.01M9 16h6" />
                    </svg>
                </div>
                @guest
                <div class="bg-white border border-slate-100 text-[#1E293B] text-xs px-3.5 py-2.5 rounded-2xl rounded-tl-none shadow-sm leading-relaxed">
                    Halo! Silakan login terlebih dahulu untuk menggunakan fitur chatbot kami dan menikmati layanan MD Car Rental secara penuh.<br><br>
                    <a href="{{ route('login') }}" class="inline-block text-center bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-xl shadow-sm transition text-xs">
                        Login Sekarang
                    </a>
                </div>
                @else
                <div class="bg-white border border-slate-100 text-[#1E293B] text-xs px-3.5 py-2.5 rounded-2xl rounded-tl-none shadow-sm leading-relaxed">
                    Halo, <strong>{{ auth()->user()->name }}</strong>! Saya <strong>Asisten Virtual MD Car Rental</strong>.<br><br>
                    Saya bisa membantu memberikan rekomendasi mobil, info alamat perusahaan, panduan cara sewa, atau membantu Anda memesan mobil secara langsung!
                </div>
                @endguest
            </div>
        </div>

        <!-- Typing Indicator -->
        <div id="chatbot-typing" class="hidden px-4 py-2 flex items-center gap-2 max-w-[85%]">
            <div class="w-7 h-7 rounded-lg bg-blue-100 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2M5 6h14a3 3 0 013 3v4a3 3 0 01-3 3H5a3 3 0 01-3-3V9a3 3 0 013-3zm4 5h.01M15 11h.01M9 16h6" />
                </svg>
            </div>
            <div class="bg-white border border-slate-100 p-3 rounded-2xl rounded-tl-none shadow-sm flex items-center gap-1">
                <span class="w-1.5 h-1.5 bg-blue-500 rounded-full animate-bounce" style="animation-delay: 0ms"></span>
                <span class="w-1.5 h-1.5 bg-blue-500 rounded-full animate-bounce" style="animation-delay: 150ms"></span>
                <span class="w-1.5 h-1.5 bg-blue-500 rounded-full animate-bounce" style="animation-delay: 300ms"></span>
            </div>
        </div>

        <!-- Quick Suggestions / Chips -->
        <div class="px-4 py-2 border-t border-slate-100 bg-white flex flex-wrap gap-2 overflow-x-auto max-h-24 overflow-y-auto shrink-0 select-none">
            <div id="chatbot-suggestions" class="flex flex-wrap gap-2">
                @guest
                <a href="{{ route('login') }}" class="text-[11px] bg-blue-600 hover:bg-blue-700 text-white font-semibold px-3.5 py-1.5 rounded-full shadow-sm hover:shadow transition flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z" />
                    </svg>
                    <span>Login Sekarang</span>
                </a>
                @else
                <button onclick="handleSuggestion('Rekomendasi Mobil')" class="text-[11px] bg-slate-100 hover:bg-blue-50 text-slate-700 hover:text-blue-700 font-semibold px-3 py-1.5 rounded-full border border-slate-200/50 transition cursor-pointer flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124l-.324-5.184a3.375 3.375 0 00-3.37-3.166h-4.83a.75.75 0 01-.52-.22L12.44 4.5h-2.88l-1.24 3.16a.75.75 0 01-.52.22H2.97a3.375 3.375 0 00-3.37 3.166l-.324 5.184c-.04.62.469 1.124 1.09 1.124h1.125m17.25 0h-1.5" />
                    </svg>
                    <span>Rekomendasi Mobil</span>
                </button>
                <button onclick="handleSuggestion('Cara Sewa')" class="text-[11px] bg-slate-100 hover:bg-blue-50 text-slate-700 hover:text-blue-700 font-semibold px-3 py-1.5 rounded-full border border-slate-200/50 transition cursor-pointer flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span>Cara Sewa</span>
                </button>
                <button onclick="handleSuggestion('Tentang Perusahaan')" class="text-[11px] bg-slate-100 hover:bg-blue-50 text-slate-700 hover:text-blue-700 font-semibold px-3 py-1.5 rounded-full border border-slate-200/50 transition cursor-pointer flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-10.5h16.5m-16.5 4.5h16.5m-16.5 4.5h16.5M6.75 5.25h.75m-.75 3h.75M12 5.25h.75m-.75 3h.75m3.75-3h.75m-.75 3h.75M3 21V5.25A2.25 2.25 0 015.25 3h13.5A2.25 2.25 0 0121 5.25V21" />
                    </svg>
                    <span>Tentang Perusahaan</span>
                </button>
                <button onclick="handleSuggestion('Bantu Saya Booking')" class="text-[11px] bg-blue-600 hover:bg-blue-700 text-white font-semibold px-3.5 py-1.5 rounded-full shadow-sm hover:shadow transition cursor-pointer flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" />
                    </svg>
                    <span>Bantu Booking</span>
                </button>
                @endguest
            </div>
        </div>

        <!-- Input Box -->
        <form id="chatbot-form" onsubmit="handleChatSubmit(event)" class="p-3 border-t border-slate-200/60 bg-white flex items-center gap-2 shrink-0">
            <input type="text" id="chatbot-input" placeholder="Ketik pertanyaan atau tanggal..." class="flex-grow text-xs border border-slate-200 rounded-xl px-3.5 py-2.5 focus:outline-none focus:border-blue-600 bg-slate-50/50 focus:bg-white transition" autocomplete="off">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white p-2.5 rounded-xl transition duration-200 hover:shadow-md cursor-pointer shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" />
                </svg>
            </button>
        </form>
    </div>

    <!-- Floating Chat Button -->
    <button id="chatbot-toggle-btn"
        onclick="toggleChatWindow()"
        class="bg-gradient-to-tr from-blue-600 via-indigo-600 to-violet-700 text-white w-14 h-14 rounded-full shadow-lg hover:shadow-blue-500/35 hover:shadow-xl flex items-center justify-center transition-all duration-300 hover:scale-105 active:scale-95 cursor-pointer relative group border border-white/20">

        <!-- Notification Dot -->
        <span class="absolute -top-1 -right-1 w-4.5 h-4.5 bg-rose-500 border-2 border-white rounded-full flex items-center justify-center text-[9px] font-bold text-white shadow">
        1
    </span>

    <!-- Robot Icon -->
    <svg xmlns="http://www.w3.org/2000/svg"
        fill="none"
        viewBox="0 0 24 24"
        stroke-width="1.8"
        stroke="currentColor"
        class="w-7 h-7 transition-transform duration-300 group-hover:rotate-6">

        <!-- Antenna -->
        <path stroke-linecap="round" stroke-linejoin="round"
            d="M12 3v2" />

        <circle cx="12" cy="2" r="1" fill="currentColor" />

        <!-- Head -->
        <rect x="5" y="6"
            width="14"
            height="10"
            rx="3"
            stroke="currentColor" />

        <!-- Eyes -->
        <circle cx="9" cy="11" r="1.2" fill="currentColor" />
        <circle cx="15" cy="11" r="1.2" fill="currentColor" />

        <!-- Mouth -->
        <path stroke-linecap="round"
            d="M9 14h6" />

        <!-- Arms -->
        <path stroke-linecap="round"
            d="M5 10H3" />
        <path stroke-linecap="round"
            d="M21 10h-2" />

        <!-- Body -->
        <path stroke-linecap="round"
            stroke-linejoin="round"
            d="M9 16v3m6-3v3M8 22h8" />

    </svg>

</button>
</div>

<script>
    // Global States
    let isChatOpen = false;
    let bookingState = {
        carId: null,
        startDate: null,
        endDate: null,
        serviceType: null,
        step: null
    };
    let chatHistory = [];

    // Toggle Chat Window visibility
    function toggleChatWindow() {
        const win = document.getElementById('chatbot-window');
        const badge = document.querySelector('#chatbot-toggle-btn span');
        isChatOpen = !isChatOpen;

        if (isChatOpen) {
            win.classList.remove('hidden');
            // Remove notification dot
            if (badge) badge.classList.add('hidden');
            setTimeout(() => {
                win.classList.remove('opacity-0', 'translate-y-4', 'scale-95');
                win.classList.add('opacity-100', 'translate-y-0', 'scale-100');
                scrollToBottom();
            }, 50);
        } else {
            win.classList.remove('opacity-100', 'translate-y-0', 'scale-100');
            win.classList.add('opacity-0', 'translate-y-4', 'scale-95');
            setTimeout(() => {
                win.classList.add('hidden');
            }, 300);
        }
    }

    // Scroll chat area to bottom
    function scrollToBottom() {
        const area = document.getElementById('chatbot-messages');
        area.scrollTop = area.scrollHeight;
    }

    // Send direct text submission (for form submit)
    function handleChatSubmit(e) {
        e.preventDefault();
        const input = document.getElementById('chatbot-input');
        const text = input.value.trim();
        if (!text) return;

        input.value = '';
        sendMessage(text);
    }

    // Send suggestions or click replies
    function handleSuggestion(text) {
        sendMessage(text);
    }

    // Main send message logic
    function sendMessage(text) {
        // Render User Message
        appendMessage('user', text);
        chatHistory.push({ sender: 'user', text: text });
        
        // Show Typing Indicator
        const loader = document.getElementById('chatbot-typing');
        loader.classList.remove('hidden');
        scrollToBottom();

        // Call API
        fetch('{{ route('chatbot.message') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                message: text,
                history: chatHistory,
                bookingState: bookingState
            })
        })
        .then(res => {
            if (!res.ok) throw new Error('Network error');
            return res.json();
        })
        .then(data => {
            // Hide typing indicator
            loader.classList.add('hidden');

            if (data.reply) {
                // Update States
                if (data.bookingState) {
                    bookingState = data.bookingState;
                }
                chatHistory.push({ sender: 'bot', text: data.reply });

                // Render Bot Reply
                appendMessage('bot', data.reply);

                // Render Recommended Cars if any
                if (data.cars && data.cars.length > 0) {
                    renderCarRecommendations(data.cars);
                }

                // Render Booking Link if ready
                if (data.bookingLink) {
                    renderBookingLinkButton(data.bookingLink);
                }

                // Render Suggestions
                if (data.suggestions) {
                    renderSuggestions(data.suggestions);
                }
            }
            scrollToBottom();
        })
        .catch(err => {
            loader.classList.add('hidden');
            appendMessage('bot', '⚠️ Gagal terhubung ke asisten virtual. Coba lagi nanti.');
            scrollToBottom();
        });
    }

    // Append a message bubble to messages list
    function appendMessage(sender, text) {
        const container = document.getElementById('chatbot-messages');
        const wrapper = document.createElement('div');
        wrapper.className = `flex items-start gap-2.5 max-w-[85%] ${sender === 'user' ? 'ml-auto justify-end' : ''}`;

        if (sender === 'user') {
            wrapper.innerHTML = `
                <div class="bg-blue-600 text-white text-xs px-3.5 py-2.5 rounded-2xl rounded-tr-none shadow-sm leading-relaxed">
                    ${escapeHtml(text)}
                </div>
            `;
        } else {
            wrapper.innerHTML = `
                <div class="w-7 h-7 rounded-lg bg-blue-100 flex items-center justify-center shrink-0 border border-blue-200 shadow-sm">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2M5 6h14a3 3 0 013 3v4a3 3 0 01-3 3H5a3 3 0 01-3-3V9a3 3 0 013-3zm4 5h.01M15 11h.01M9 16h6" />
                    </svg>
                </div>
                <div class="bg-white border border-slate-100 text-[#1E293B] text-xs px-3.5 py-2.5 rounded-2xl rounded-tl-none shadow-sm leading-relaxed">
                    ${text}
                </div>
            `;
        }

        container.appendChild(wrapper);
    }

    // Render horizontal suggestions chips
    function renderSuggestions(list) {
        const container = document.getElementById('chatbot-suggestions');
        container.innerHTML = '';
        list.forEach(item => {
            const btn = document.createElement('button');
            btn.className = "text-[11px] bg-slate-100 hover:bg-blue-50 text-slate-700 hover:text-blue-700 font-semibold px-3 py-1.5 rounded-full border border-slate-200/50 transition cursor-pointer";
            btn.innerHTML = item;
            btn.onclick = () => handleSuggestion(item);
            container.appendChild(btn);
        });
    }

    // Select a car from card click
    function selectCar(carId, carName) {
        bookingState.carId = carId;
        sendMessage(`Pilih ${carName}`);
    }

    // Render car recommendations inside the chat log
    function renderCarRecommendations(cars) {
        const container = document.getElementById('chatbot-messages');
        const wrapper = document.createElement('div');
        wrapper.className = "w-full overflow-x-auto py-2 flex gap-3 snap-x no-scrollbar";
        
        cars.forEach(car => {
            const card = document.createElement('div');
            card.className = "w-64 bg-white border border-slate-200/70 rounded-xl overflow-hidden shadow-sm shrink-0 snap-center";
            
            card.innerHTML = `
                <div class="relative h-28 bg-slate-100">
                    <img src="${car.image}" alt="${car.name}" class="w-full h-full object-cover">
                    <span class="absolute top-2 right-2 bg-white/95 text-slate-800 text-[10px] font-bold px-1.5 py-0.5 rounded shadow">
                        ⭐️ ${car.rating} (${car.reviews_count} Ulasan)
                    </span>
                </div>
                <div class="p-3">
                    <h5 class="font-bold text-xs text-slate-900">${car.brand} ${car.name}</h5>
                    <p class="text-[10px] text-slate-500 mt-1">${car.transmission} • ${car.seat_count} Kursi</p>
                    <div class="flex items-center justify-between mt-3 pt-2 border-t border-slate-100">
                        <div>
                            <span class="text-[10px] text-slate-400 block font-medium">Tarif / Hari</span>
                            <span class="font-bold text-xs text-blue-700">Rp ${car.daily_rate}</span>
                        </div>
                        <button onclick="selectCar(${car.id}, '${car.name}')" class="bg-blue-600 hover:bg-blue-700 text-white text-[10px] font-bold px-3 py-1.5 rounded-lg transition hover:shadow cursor-pointer">
                            Pilih Mobil
                        </button>
                    </div>
                </div>
            `;
            wrapper.appendChild(card);
        });

        container.appendChild(wrapper);
    }

    // Render booking link CTA button
    function renderBookingLinkButton(link) {
        const container = document.getElementById('chatbot-messages');
        const wrapper = document.createElement('div');
        wrapper.className = "w-full py-2 flex justify-center";

        wrapper.innerHTML = `
            <a href="${link}" class="w-full text-center bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white font-extrabold py-3 px-4 rounded-xl shadow-md hover:shadow-lg transition-all duration-300 text-xs flex items-center justify-center gap-2 hover:-translate-y-0.5">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5zm6-10.125a1.875 1.875 0 11-3.75 0 1.875 1.875 0 013.75 0zm-1.25 6.125c0-1.38-1.12-2.5-2.5-2.5s-2.5 1.12-2.5 2.5v1.25h5V15.5z" />
                </svg>
                <span>Lanjutkan ke Upload Identitas</span>
                <svg class="w-4 h-4 animate-pulse" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                </svg>
            </a>
        `;

        container.appendChild(wrapper);
    }

    // Helper: Escape HTML strings
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.innerText = text;
        return div.innerHTML;
    }
</script>

<!-- Add styling to hide scrollbar in card list -->
<style>
    .no-scrollbar::-webkit-scrollbar {
        display: none;
    }
    .no-scrollbar {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
</style>
