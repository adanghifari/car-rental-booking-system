{{-- Confirm Popup Notification Component --}}
{{-- Usage: <x-frontliner.confirm-popup /> --}}

<script>
    /**
     * Show a premium confirmation popup notification.
     * @param {string} message - The text to display.
     * @param {function} onConfirm - Callback when user clicks confirm.
     */
    window.showConfirmPopup = function(message, onConfirm) {
        if (document.getElementById('confirm-popup-overlay')) return;

        // Create overlay element
        const overlay = document.createElement('div');
        overlay.id = 'confirm-popup-overlay';
        overlay.className = 'fixed inset-0 z-[1000] flex items-center justify-center p-4 transition-all duration-300 opacity-0 pointer-events-none';
        overlay.style.backdropFilter = 'blur(4px)';
        overlay.style.webkitBackdropFilter = 'blur(4px)';
        overlay.style.background = 'rgba(15, 23, 42, 0.3)';

        // Create popup card
        const container = document.createElement('div');
        container.className = 'bg-white rounded-2xl shadow-xl w-full max-w-sm p-6 flex flex-col items-center text-center transform scale-95 transition-all duration-300 border border-slate-100';

        // Yellow warning badge
        const iconContainer = document.createElement('div');
        iconContainer.className = 'w-14 h-14 bg-amber-50 rounded-full flex items-center justify-center text-amber-500 mb-4 shadow-sm';
        iconContainer.innerHTML = `
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
            </svg>
        `;

        const title = document.createElement('h3');
        title.className = 'text-base font-bold text-slate-800 mb-1';
        title.textContent = 'Konfirmasi';

        const msg = document.createElement('p');
        msg.className = 'text-slate-500 text-xs mb-5 leading-relaxed';
        msg.textContent = message;

        // Buttons container
        const btnContainer = document.createElement('div');
        btnContainer.className = 'w-full flex gap-3';

        // Cancel button
        const cancelBtn = document.createElement('button');
        cancelBtn.type = 'button';
        cancelBtn.className = 'flex-1 border border-slate-200 hover:bg-slate-50 text-slate-500 font-bold py-2.5 rounded-xl text-xs transition duration-200 uppercase tracking-wider cursor-pointer';
        cancelBtn.textContent = 'Batal';

        // Confirm button
        const confirmBtn = document.createElement('button');
        confirmBtn.type = 'button';
        confirmBtn.className = 'flex-1 bg-red-500 hover:bg-red-600 text-white font-bold py-2.5 rounded-xl text-xs transition duration-200 uppercase tracking-wider shadow-sm cursor-pointer';
        confirmBtn.textContent = 'Ya, Batal';

        // Assemble elements
        btnContainer.appendChild(cancelBtn);
        btnContainer.appendChild(confirmBtn);
        container.appendChild(iconContainer);
        container.appendChild(title);
        container.appendChild(msg);
        container.appendChild(btnContainer);
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
            overlay.classList.remove('opacity-100');
            overlay.classList.add('opacity-0');
            container.classList.remove('scale-100');
            container.classList.add('scale-95');
            setTimeout(() => {
                overlay.remove();
            }, 300);
        };

        // Close events
        cancelBtn.addEventListener('click', closePopup);
        confirmBtn.addEventListener('click', () => {
            closePopup();
            onConfirm();
        });
        
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
    };
</script>
