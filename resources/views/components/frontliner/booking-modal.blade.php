{{-- Booking Modal Component --}}
{{-- Usage: <x-frontliner.booking-modal /> --}}
{{-- Then call openBookingModal(carData) from JavaScript --}}

<!-- Booking Modal Overlay -->
<div id="booking-modal-overlay"
    class="fixed inset-0 z-[999] flex items-center justify-center p-4 transition-all duration-300 opacity-0 pointer-events-none"
    style="backdrop-filter: blur(0px); -webkit-backdrop-filter: blur(0px); background: rgba(15, 23, 42, 0);">

    <!-- Modal Content -->
    <div id="booking-modal-content"
        class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md max-h-[90vh] overflow-y-auto transform scale-95 transition-all duration-300"
        style="scrollbar-width: thin; scrollbar-color: #CBD5E1 transparent;">

        <!-- Close Button -->
        <button onclick="closeBookingModal()"
            class="absolute top-4 right-4 z-10 w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 flex items-center justify-center transition-colors duration-200">
            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>

        <!-- Car Image -->
        <div class="rounded-t-2xl overflow-hidden h-48 bg-gray-100">
            <img id="modal-car-image" src="" alt="Car" class="w-full h-full object-cover">
        </div>

        <div class="p-6 space-y-5">
            <!-- Tarif Sewa & Status -->
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Tarif Sewa</p>
                    <p class="text-xl font-bold text-[#0B3C9B]">
                        <span id="modal-daily-rate">Rp 0</span>
                        <span class="text-xs font-normal text-gray-400">/hari</span>
                    </p>
                </div>
                <span id="modal-status-badge" class="bg-emerald-50 text-emerald-600 text-[10px] font-bold px-2.5 py-1 rounded-md uppercase">Tersedia</span>
            </div>

            <form id="booking-modal-form" method="GET" class="space-y-4">
                <input type="hidden" name="car_id" id="modal-car-id" value="">
                <input type="hidden" name="service_type" id="modal-service-type" value="self_drive">

                <!-- Date Pickers -->
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Mulai Sewa</label>
                        <input type="date" name="start_date" id="modal-start-date" onchange="modalCalculatePrice()"
                            class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2.5 text-xs font-semibold text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#0B3C9B]/20 focus:border-[#0B3C9B]/30 transition">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Selesai Sewa</label>
                        <input type="date" name="end_date" id="modal-end-date" onchange="modalCalculatePrice()"
                            class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2.5 text-xs font-semibold text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#0B3C9B]/20 focus:border-[#0B3C9B]/30 transition">
                    </div>
                </div>

                <!-- Layanan Picker -->
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Pilihan Layanan</label>
                    <div class="grid grid-cols-2 gap-3">
                        <button type="button" id="modal-btn-self-drive" onclick="modalSelectService('self_drive')"
                            class="border bg-gray-50 text-gray-500 rounded-xl py-3 text-xs font-medium flex flex-col items-center justify-center space-y-1 transition-all duration-200">
                            <span class="text-base">🔑</span>
                            <span>Lepas Kunci</span>
                        </button>
                        <button type="button" id="modal-btn-with-driver" onclick="modalSelectService('with_driver')"
                            class="border bg-gray-50 text-gray-500 rounded-xl py-3 text-xs font-medium flex flex-col items-center justify-center space-y-1 transition-all duration-200">
                            <span class="text-base">👤</span>
                            <span>Dengan Sopir</span>
                        </button>
                    </div>
                </div>

                <!-- Price Breakdown -->
                <div class="border-t border-gray-100 pt-4 space-y-2.5 text-xs">
                    <div class="flex justify-between text-gray-500">
                        <span id="modal-display-days">Sewa 0 Hari</span>
                        <span id="modal-display-rent-cost" class="font-semibold text-gray-800">Rp -</span>
                    </div>
                    <div class="flex justify-between text-gray-500">
                        <span>Biaya Layanan & Asuransi</span>
                        <span id="modal-display-service-cost" class="font-semibold text-gray-800">Rp -</span>
                    </div>
                    <div class="flex justify-between items-center pt-2.5 border-t border-dashed border-gray-200 text-sm font-bold text-gray-900">
                        <span>Total Harga</span>
                        <span id="modal-display-total-cost" class="text-[#0B3C9B] text-base">Rp -</span>
                    </div>
                </div>

                <!-- Booking Button -->
                <button type="submit" id="modal-booking-btn"
                    class="w-full bg-[#0B3C9B] hover:bg-[#082D76] active:scale-[0.98] text-white font-bold py-3.5 rounded-xl text-xs transition-all duration-200 shadow-lg shadow-blue-200 tracking-wider uppercase">
                    Booking Sekarang
                </button>

                <p class="text-[9px] text-center text-gray-400 italic">Pembatalan gratis hingga 24 jam sebelum pengambilan</p>
            </form>
        </div>
    </div>
</div>

<style>
    #booking-modal-content::-webkit-scrollbar {
        width: 4px;
    }
    #booking-modal-content::-webkit-scrollbar-track {
        background: transparent;
    }
    #booking-modal-content::-webkit-scrollbar-thumb {
        background: #CBD5E1;
        border-radius: 999px;
    }
    .booking-modal-open {
        overflow: hidden;
    }
</style>

<script>
    // Modal state
    let modalDailyRate = 0;
    let modalSelectedService = 'self_drive';
    let modalSelfDriveAvail = true;
    let modalDriverAvail = true;

    /**
     * Open the booking modal with car data.
     * @param {Object} carData - { id, name, image, dailyRate, status, selfDriveAvailable, driverAvailable }
     */
    function openBookingModal(carData) {
        // Auth check: if not logged in, redirect to login
        @auth
            // User is authenticated, proceed
        @else
            window.location.href = "{{ route('login') }}?redirect=" + encodeURIComponent(window.location.pathname + window.location.search);
            return;
        @endauth

        const overlay = document.getElementById('booking-modal-overlay');
        const content = document.getElementById('booking-modal-content');

        // Populate data
        modalDailyRate = carData.dailyRate || 0;
        modalSelfDriveAvail = carData.selfDriveAvailable !== false;
        modalDriverAvail = carData.driverAvailable !== false;

        document.getElementById('modal-car-id').value = carData.id;
        document.getElementById('modal-car-image').src = carData.image || '';
        document.getElementById('modal-car-image').alt = carData.name || 'Car';
        document.getElementById('modal-daily-rate').textContent = 'Rp ' + modalDailyRate.toLocaleString('id-ID');

        // Status badge
        const badge = document.getElementById('modal-status-badge');
        if (carData.status === 'available') {
            badge.textContent = 'TERSEDIA';
            badge.className = 'bg-emerald-50 text-emerald-600 text-[10px] font-bold px-2.5 py-1 rounded-md uppercase';
        } else {
            badge.textContent = 'DISEWA';
            badge.className = 'bg-amber-50 text-amber-600 text-[10px] font-bold px-2.5 py-1 rounded-md uppercase';
        }

        // Service availability
        const selfBtn = document.getElementById('modal-btn-self-drive');
        const driverBtn = document.getElementById('modal-btn-with-driver');
        selfBtn.classList.toggle('opacity-40', !modalSelfDriveAvail);
        selfBtn.classList.toggle('cursor-not-allowed', !modalSelfDriveAvail);
        driverBtn.classList.toggle('opacity-40', !modalDriverAvail);
        driverBtn.classList.toggle('cursor-not-allowed', !modalDriverAvail);

        // Set form action
        document.getElementById('booking-modal-form').action = "{{ route('booking.start') }}";

        // Set default dates
        const today = new Date();
        const tomorrow = new Date(today);
        tomorrow.setDate(tomorrow.getDate() + 1);
        const threeDaysLater = new Date(tomorrow);
        threeDaysLater.setDate(threeDaysLater.getDate() + 3);
        const formatDate = (d) => d.toISOString().split('T')[0];

        const startEl = document.getElementById('modal-start-date');
        const endEl = document.getElementById('modal-end-date');
        startEl.value = formatDate(tomorrow);
        startEl.min = formatDate(tomorrow);
        endEl.value = formatDate(threeDaysLater);
        endEl.min = formatDate(tomorrow);

        // Select default service
        modalSelectedService = modalSelfDriveAvail ? 'self_drive' : (modalDriverAvail ? 'with_driver' : 'self_drive');
        modalSelectService(modalSelectedService);

        // Show modal with animation
        document.body.classList.add('booking-modal-open');
        overlay.classList.remove('pointer-events-none');

        requestAnimationFrame(() => {
            overlay.style.backdropFilter = 'blur(8px)';
            overlay.style.webkitBackdropFilter = 'blur(8px)';
            overlay.style.background = 'rgba(15, 23, 42, 0.4)';
            overlay.classList.remove('opacity-0');
            overlay.classList.add('opacity-100');
            content.classList.remove('scale-95');
            content.classList.add('scale-100');
        });
    }

    function closeBookingModal() {
        const overlay = document.getElementById('booking-modal-overlay');
        const content = document.getElementById('booking-modal-content');

        overlay.style.backdropFilter = 'blur(0px)';
        overlay.style.webkitBackdropFilter = 'blur(0px)';
        overlay.style.background = 'rgba(15, 23, 42, 0)';
        overlay.classList.remove('opacity-100');
        overlay.classList.add('opacity-0');
        content.classList.remove('scale-100');
        content.classList.add('scale-95');

        setTimeout(() => {
            overlay.classList.add('pointer-events-none');
            document.body.classList.remove('booking-modal-open');
        }, 300);
    }

    // Close on overlay click (not on modal content)
    document.getElementById('booking-modal-overlay').addEventListener('click', function(e) {
        if (e.target === this) closeBookingModal();
    });

    // Close on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeBookingModal();
    });

    function modalSelectService(type) {
        if (type === 'self_drive' && !modalSelfDriveAvail) return;
        if (type === 'with_driver' && !modalDriverAvail) return;

        modalSelectedService = type;
        document.getElementById('modal-service-type').value = type;

        const selfBtn = document.getElementById('modal-btn-self-drive');
        const driverBtn = document.getElementById('modal-btn-with-driver');

        const activeClasses = ['border-[#0B3C9B]', 'bg-white', 'text-[#0B3C9B]', 'font-bold', 'border-2', 'shadow-sm'];
        const inactiveClasses = ['border-gray-200', 'bg-gray-50', 'text-gray-500', 'font-medium'];

        if (type === 'self_drive') {
            selfBtn.classList.remove(...inactiveClasses);
            selfBtn.classList.add(...activeClasses);
            driverBtn.classList.remove(...activeClasses);
            driverBtn.classList.add(...inactiveClasses);
        } else {
            driverBtn.classList.remove(...inactiveClasses);
            driverBtn.classList.add(...activeClasses);
            selfBtn.classList.remove(...activeClasses);
            selfBtn.classList.add(...inactiveClasses);
        }

        modalCalculatePrice();
    }

    function modalCalculatePrice() {
        const startVal = document.getElementById('modal-start-date').value;
        const endVal = document.getElementById('modal-end-date').value;

        if (!startVal || !endVal) return;

        const start = new Date(startVal);
        const end = new Date(endVal);

        let diffTime = end - start;
        let days = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        if (days < 1) days = 1;

        const rentCost = modalDailyRate * days;
        let driverCost = 0;
        if (modalSelectedService === 'with_driver') {
            driverCost = 150000 * days;
        }
        const serviceCost = 100000 + driverCost;
        const totalCost = rentCost + serviceCost;

        const fmt = (n) => 'Rp ' + n.toLocaleString('id-ID');

        document.getElementById('modal-display-days').textContent = `Sewa ${days} Hari`;
        document.getElementById('modal-display-rent-cost').textContent = fmt(rentCost);
        document.getElementById('modal-display-service-cost').textContent = fmt(serviceCost);
        document.getElementById('modal-display-total-cost').textContent = fmt(totalCost);
    }
</script>
