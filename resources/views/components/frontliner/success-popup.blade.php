{{-- Success Popup Notification Component --}}
{{-- Usage: <x-frontliner.success-popup /> --}}

<script>
    /**
     * Show a premium success popup notification with a 3-second countdown.
     * @param {string} message - The success text to display.
     */
    window.showSuccessPopup = function(message) {
        if (document.getElementById('favorite-popup-overlay')) return;

        // Create overlay element
        const overlay = document.createElement('div');
        overlay.id = 'favorite-popup-overlay';
        overlay.className = 'fixed inset-0 z-[1000] flex items-center justify-center p-4 transition-all duration-300 opacity-0 pointer-events-none';
        overlay.style.backdropFilter = 'blur(4px)';
        overlay.style.webkitBackdropFilter = 'blur(4px)';
        overlay.style.background = 'rgba(15, 23, 42, 0.3)';

        // Create popup card
        const container = document.createElement('div');
        container.className = 'bg-white rounded-2xl shadow-xl w-full max-w-xs p-6 flex flex-col items-center text-center transform scale-95 transition-all duration-300 border border-slate-100';

        // Green checkmark badge
        const iconContainer = document.createElement('div');
        iconContainer.className = 'w-14 h-14 bg-emerald-50 rounded-full flex items-center justify-center text-emerald-500 mb-4 shadow-sm';
        iconContainer.innerHTML = `
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
            </svg>
        `;

        const title = document.createElement('h3');
        title.className = 'text-base font-bold text-slate-800 mb-1';
        title.textContent = 'Berhasil!';

        const msg = document.createElement('p');
        msg.className = 'text-slate-500 text-xs mb-5 leading-relaxed';
        msg.textContent = message;

        // Countdown button
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'w-full bg-[#0B3C9B] hover:bg-[#082D76] text-white font-bold py-2.5 rounded-xl text-xs transition duration-200 uppercase tracking-wider shadow-sm cursor-pointer';
        
        let countdown = 3;
        btn.textContent = `Oke (${countdown})`;

        // Assemble elements
        container.appendChild(iconContainer);
        container.appendChild(title);
        container.appendChild(msg);
        container.appendChild(btn);
        overlay.appendChild(container);
        document.body.appendChild(overlay);

        // Animate modal entry
        requestAnimationFrame(() => {
            overlay.classList.remove('pointer-events-none', 'opacity-0');
            overlay.classList.add('opacity-100');
            container.classList.remove('scale-95');
            container.classList.add('scale-100');
        });

        // Close function
        const closePopup = () => {
            clearInterval(timer);
            overlay.classList.remove('opacity-100');
            overlay.classList.add('opacity-0');
            container.classList.remove('scale-100');
            container.classList.add('scale-95');
            setTimeout(() => {
                overlay.remove();
            }, 300);
        };

        // Close events
        btn.addEventListener('click', closePopup);
        overlay.addEventListener('click', (e) => {
            if (e.target === overlay) closePopup();
        });

        const escHandler = (e) => {
            if (e.key === 'Escape') {
                closePopup();
                document.removeEventListener('keydown', escHandler);
            }
        };
        document.addEventListener('keydown', escHandler);

        // Countdown logic
        const timer = setInterval(() => {
            countdown--;
            if (countdown <= 0) {
                closePopup();
            } else {
                btn.textContent = `Oke (${countdown})`;
            }
        }, 1000);
    };
</script>
