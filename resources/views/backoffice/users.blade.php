<x-backoffice.layout title="Manajemen User" :admin="$admin" active="users" search-placeholder="Cari nama atau email...">
    <section class="page-head">
        <div>
            <h1 class="page-title">Manajemen User</h1>
            <p class="page-subtitle">Kelola seluruh data customer rental mobil premium Anda.</p>
        </div>


    </section>

    <form id="filter-form" method="GET" action="{{ route('backoffice.users') }}" class="card filter-bar">
        @if (request('search'))
            <input type="hidden" name="search" value="{{ request('search') }}">
        @endif
        <input type="hidden" name="status" id="hidden-status" value="{{ request('status') }}">
        <input type="hidden" name="sort" id="hidden-sort" value="{{ request('sort') }}">

        <div class="filter-box custom-dropdown" id="dropdown-status" tabindex="0">
            <div class="dropdown-trigger">
                <span class="filter-label">Status:</span>
                <span class="filter-value">
                    @if (request('status') === 'aktif')
                        Aktif
                    @elseif (request('status') === 'suspend')
                        Suspend
                    @else
                        Semua Status
                    @endif
                </span>
                <svg class="dropdown-chevron" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#7b869b" stroke-width="2">
                    <path d="m6 9 6 6 6-6"/>
                </svg>
            </div>
            <div class="dropdown-menu">
                <div class="dropdown-item @if(!request('status')) active @endif" data-value="">
                    <span>Semua Status</span>
                    @if(!request('status'))
                        <svg class="check-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M20 6 9 17l-5-5"/></svg>
                    @endif
                </div>
                <div class="dropdown-item @if(request('status') === 'aktif') active @endif" data-value="aktif">
                    <span>Aktif</span>
                    @if(request('status') === 'aktif')
                        <svg class="check-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M20 6 9 17l-5-5"/></svg>
                    @endif
                </div>
                <div class="dropdown-item @if(request('status') === 'suspend') active @endif" data-value="suspend">
                    <span>Suspend</span>
                    @if(request('status') === 'suspend')
                        <svg class="check-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M20 6 9 17l-5-5"/></svg>
                    @endif
                </div>
            </div>
        </div>


        <div class="filter-box custom-dropdown" id="dropdown-sort" tabindex="0">
            <div class="dropdown-trigger">
                <span class="filter-label">Urutkan:</span>
                <span class="filter-value">
                    @if (request('sort') === 'oldest')
                        Paling Lama
                    @elseif (request('sort') === 'name_asc')
                        Nama (A-Z)
                    @elseif (request('sort') === 'name_desc')
                        Nama (Z-A)
                    @elseif (request('sort') === 'transactions_desc')
                        Transaksi Terbanyak
                    @else
                        Paling Baru
                    @endif
                </span>
                <svg class="dropdown-chevron" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#7b869b" stroke-width="2">
                    <path d="m6 9 6 6 6-6"/>
                </svg>
            </div>
            <div class="dropdown-menu">
                <div class="dropdown-item @if(!request('sort') || request('sort') === 'latest') active @endif" data-value="latest">
                    <span>Paling Baru</span>
                    @if(!request('sort') || request('sort') === 'latest')
                        <svg class="check-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M20 6 9 17l-5-5"/></svg>
                    @endif
                </div>
                <div class="dropdown-item @if(request('sort') === 'oldest') active @endif" data-value="oldest">
                    <span>Paling Lama</span>
                    @if(request('sort') === 'oldest')
                        <svg class="check-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M20 6 9 17l-5-5"/></svg>
                    @endif
                </div>
                <div class="dropdown-item @if(request('sort') === 'name_asc') active @endif" data-value="name_asc">
                    <span>Nama (A-Z)</span>
                    @if(request('sort') === 'name_asc')
                        <svg class="check-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M20 6 9 17l-5-5"/></svg>
                    @endif
                </div>
                <div class="dropdown-item @if(request('sort') === 'name_desc') active @endif" data-value="name_desc">
                    <span>Nama (Z-A)</span>
                    @if(request('sort') === 'name_desc')
                        <svg class="check-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M20 6 9 17l-5-5"/></svg>
                    @endif
                </div>
                <div class="dropdown-item @if(request('sort') === 'transactions_desc') active @endif" data-value="transactions_desc">
                    <span>Transaksi Terbanyak</span>
                    @if(request('sort') === 'transactions_desc')
                        <svg class="check-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M20 6 9 17l-5-5"/></svg>
                    @endif
                </div>
            </div>
        </div>
    </form>

    <section class="card table-card">
        <table class="user-table">
            <thead>
                <tr>
                    <th>Customer</th>
                    <th>Kontak</th>
                    <th style="text-align: right; padding-right: 200px;">Total Transaksi</th>
                    <th style="padding-right: 100px;">Status</th>
                    <th>Daftar Pada</th>
                    <th style="text-align: right;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $userItem)
                    <tr>
                        <td>
                            <div class="user-cell">
                                <div class="user-avatar" style="background: {{ $userItem['avatar_background'] }};">
                                    {{ $userItem['initials'] }}
                                </div>
                                <div>
                                    <div class="user-name">{{ $userItem['name'] }}</div>
                                    <div class="user-meta">{{ $userItem['email'] }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="muted-stack">
                                <div>{{ '@'.$userItem['username'] }}</div>
                                <div>{{ $userItem['contact'] }}</div>
                            </div>
                        </td>
                        <td style="padding-right: 200px;">
                            <div class="money">
                                Rp {{ number_format($userItem['total_transactions'], 0, ',', '.') }}
                            </div>
                        </td>
                        <td style="padding-right: 20px;">
                            <span class="table-pill {{ $userItem['status_tone'] }}">{{ $userItem['status'] }}</span>
                        </td>
                        <td>
                            <div class="muted-stack" style="color: #4a586f;">
                                {{ $userItem['registered_day'] }} {{ $userItem['registered_year'] }}
                            </div>
                        </td>
                        <td>
                            <div class="action-list">
                                <button type="button" class="action-button-raw" data-user-detail='@json($userItem)' style="border: none; background: none; padding: 0; cursor: pointer; display: flex; align-items: center;" title="Lihat Detail">
                                    <svg class="action-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12Z"/>
                                        <circle cx="12" cy="12" r="3"/>
                                    </svg>
                                </button>
                                @if ($userItem['role'] !== \App\Models\User::ROLE_ADMIN)
                                    <button type="button" class="action-button-raw" data-delete-user-id="{{ $userItem['id'] }}" data-delete-user-name="{{ $userItem['name'] }}" data-delete-url="{{ route('backoffice.users.destroy', ['user' => $userItem['id']]) }}" style="border: none; background: none; padding: 0; margin: 0; cursor: pointer; display: flex; align-items: center;" title="Hapus User">
                                        <svg class="action-icon block" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M3 6h18"/>
                                            <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/>
                                            <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/>
                                        </svg>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">
                            <div style="padding: 24px 0; color: #7b869b;">Belum ada user untuk ditampilkan.</div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="table-footer">
            <span>Menampilkan {{ $users->firstItem() ?? 0 }}-{{ $users->lastItem() ?? 0 }} dari {{ $users->total() }} user</span>

            @if ($users->hasPages())
                <div class="pagination">
                    @if ($users->onFirstPage())
                        <span class="page-link muted">‹</span>
                    @else
                        <a href="{{ $users->previousPageUrl() }}" class="page-link">‹</a>
                    @endif

                    @foreach ($pagination as $pageItem)
                        @if ($pageItem === '...')
                            <span class="page-link muted">...</span>
                        @elseif ($pageItem === $users->currentPage())
                            <span class="page-link active">{{ $pageItem }}</span>
                        @else
                            <a href="{{ $users->url($pageItem) }}" class="page-link">{{ $pageItem }}</a>
                        @endif
                    @endforeach

                    @if ($users->hasMorePages())
                        <a href="{{ $users->nextPageUrl() }}" class="page-link">›</a>
                    @else
                        <span class="page-link muted">›</span>
                    @endif
                </div>
            @endif
        </div>
    </section>
    <style>
        .filter-bar {
            position: relative;
            z-index: 10;
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .filter-box.custom-dropdown {
            position: relative;
            cursor: pointer;
            user-select: none;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .filter-box.custom-dropdown:focus-within,
        .filter-box.custom-dropdown.is-active {
            border-color: rgba(63, 94, 215, 0.4);
            box-shadow: 0 0 0 3px rgba(63, 94, 215, 0.1);
        }

        .dropdown-trigger {
            flex: 1;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }

        .filter-value {
            font-size: 14px;
            color: var(--text, #202636);
            font-weight: 600;
            flex: 1;
            text-align: right;
            margin-right: 4px;
        }

        .dropdown-chevron {
            transition: transform 0.2s ease;
            flex-shrink: 0;
        }

        .filter-box.custom-dropdown.is-active .dropdown-chevron {
            transform: rotate(180deg);
        }

        .dropdown-menu {
            position: absolute;
            top: calc(100% + 8px);
            left: 0;
            right: 0;
            background: var(--card, #ffffff);
            border: 1px solid rgba(219, 227, 239, 0.95);
            border-radius: 16px;
            box-shadow: 0 12px 30px rgba(15, 29, 51, 0.12);
            z-index: 100;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-8px);
            transition: opacity 0.2s ease, transform 0.2s ease, visibility 0.2s;
            overflow: hidden;
            padding: 6px;
        }

        .filter-box.custom-dropdown.is-active .dropdown-menu {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .dropdown-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 14px;
            font-size: 14px;
            font-weight: 500;
            color: #4a586f;
            border-radius: 10px;
            cursor: pointer;
            transition: background-color 0.15s, color 0.15s;
        }

        .dropdown-item:hover {
            background-color: var(--blue-soft, #eef2ff);
            color: var(--blue, #3f5ed7);
        }

        .dropdown-item.active {
            background-color: var(--blue-soft, #eef2ff);
            color: var(--blue, #3f5ed7);
            font-weight: 600;
        }

        .check-icon {
            width: 14px;
            height: 14px;
            color: var(--blue, #3f5ed7);
            flex-shrink: 0;
        }

        .action-button-raw {
            border: none;
            background: none;
            padding: 0;
            margin: 0;
            cursor: pointer;
            display: flex;
            align-items: center;
        }
        
        .action-button-raw:hover .action-icon {
            opacity: 0.8;
        }
    </style>

    <!-- Modal Detail User -->
    <div class="modal-overlay" data-user-detail-modal>
        <div class="modal-panel" role="dialog" aria-modal="true" aria-labelledby="user-detail-modal-title" style="width: min(600px, 100%);">
            <div class="modal-header">
                <div>
                    <h2 class="modal-title" id="user-detail-modal-title">Detail User</h2>
                    <p class="modal-subtitle">Informasi lengkap profil customer.</p>
                </div>

                <button type="button" class="modal-close" data-close-user-detail-modal aria-label="Tutup detail">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M18 6 6 18"/>
                        <path d="m6 6 12 12"/>
                    </svg>
                </button>
            </div>

            <div class="modal-body">
                <section class="modal-card">
                    <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 24px; padding-bottom: 16px; border-bottom: 1px solid rgba(219, 227, 239, 0.6);">
                        <div class="user-avatar" id="detail-avatar" style="width: 56px; height: 56px; font-size: 18px;">U</div>
                        <div>
                           <h3 class="section-title" id="detail-name" style="margin-bottom: 4px; font-size: 18px; font-weight: 700;">Nama User</h3>
                           <p class="modal-subtitle" id="detail-email" style="font-size: 13px;">email@example.com</p>
                        </div>
                    </div>

                    <div class="detail-grid" style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 18px;">
                        <div class="detail-item">
                            <div class="detail-label" style="font-size: 11px; text-transform: uppercase; color: #7b869b; font-weight: 700; margin-bottom: 4px; letter-spacing: 0.08em;">Username</div>
                            <div class="detail-value" id="detail-username" style="font-size: 14px; font-weight: 600; color: #202636;">-</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label" style="font-size: 11px; text-transform: uppercase; color: #7b869b; font-weight: 700; margin-bottom: 4px; letter-spacing: 0.08em;">Kontak / Peran</div>
                            <div class="detail-value" id="detail-contact" style="font-size: 14px; font-weight: 600; color: #202636;">-</div>
                        </div>

                        <div class="detail-item">
                            <div class="detail-label" style="font-size: 11px; text-transform: uppercase; color: #7b869b; font-weight: 700; margin-bottom: 4px; letter-spacing: 0.08em;">Total Transaksi</div>
                            <div class="detail-value" id="detail-transactions" style="font-size: 14px; font-weight: 600; color: #202636;">-</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label" style="font-size: 11px; text-transform: uppercase; color: #7b869b; font-weight: 700; margin-bottom: 4px; letter-spacing: 0.08em;">Status</div>
                            <div class="detail-value" id="detail-status" style="font-size: 14px; font-weight: 600; color: #202636;">-</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label" style="font-size: 11px; text-transform: uppercase; color: #7b869b; font-weight: 700; margin-bottom: 4px; letter-spacing: 0.08em;">Daftar Pada</div>
                            <div class="detail-value" id="detail-registered" style="font-size: 14px; font-weight: 600; color: #202636;">-</div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>

    <!-- Modal Hapus User -->
    <div class="modal-overlay" data-delete-user-modal>
        <div class="modal-panel" role="dialog" aria-modal="true" aria-labelledby="delete-user-modal-title" style="width: min(420px, 100%); border-radius: 24px; background: rgba(255, 255, 255, 0.98); border: 1px solid rgba(219, 227, 239, 0.95); box-shadow: 0 28px 90px rgba(15, 29, 51, 0.22); overflow: hidden;">
            <div class="modal-body" style="padding: 32px 24px; text-align: center;">
                <div style="width: 56px; height: 56px; background: #fee2e2; color: #ef4444; border-radius: 50%; display: grid; place-items: center; margin: 0 auto 16px;">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 6h18"/>
                        <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/>
                        <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/>
                    </svg>
                </div>
                <h2 class="modal-title" id="delete-user-modal-title" style="font-size: 20px; font-weight: 700; margin-bottom: 8px; font-family: 'Space Grotesk', sans-serif; letter-spacing: -0.04em;">Hapus User</h2>
                <p class="modal-subtitle" style="font-size: 14px; line-height: 1.5; color: #57657c; margin-bottom: 24px;">
                    Apakah Anda yakin ingin menghapus <strong id="delete-user-name" style="color: #202636; font-weight: 700;">-</strong>? Tindakan ini tidak dapat dibatalkan.
                </p>

                <div style="display: flex; gap: 12px; justify-content: center;">
                    <button type="button" class="page-link" data-close-delete-user-modal style="padding: 10px 20px; font-size: 14px; font-weight: 600; cursor: pointer; flex: 1; border-radius: 12px; height: 42px; display: inline-flex; align-items: center; justify-content: center; background: #f0f3f8; border: 1px solid rgba(219, 227, 239, 0.95); color: #202636;">
                        Batal
                    </button>
                    <form id="delete-user-form" method="POST" action="" style="margin: 0; flex: 1;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="primary-button" style="padding: 10px 20px; font-size: 14px; font-weight: 600; background: #ef4444; color: #fff; border: none; cursor: pointer; border-radius: 12px; height: 42px; width: 100%; display: inline-flex; align-items: center; justify-content: center; box-shadow: 0 10px 20px rgba(239, 68, 68, 0.18);">
                            Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Dropdown Filter logic
                const dropdowns = document.querySelectorAll('.custom-dropdown');
                dropdowns.forEach(dropdown => {
                    const trigger = dropdown.querySelector('.dropdown-trigger');
                    const items = dropdown.querySelectorAll('.dropdown-item');
                    const hiddenInputId = dropdown.id.replace('dropdown-', 'hidden-');
                    const hiddenInput = document.getElementById(hiddenInputId);

                    dropdown.addEventListener('click', function(e) {
                        e.stopPropagation();
                        // Close other dropdowns
                        dropdowns.forEach(other => {
                            if (other !== dropdown) {
                                other.classList.remove('is-active');
                            }
                        });
                        dropdown.classList.toggle('is-active');
                    });

                    const menu = dropdown.querySelector('.dropdown-menu');
                    if (menu) {
                        menu.addEventListener('click', function(e) {
                            e.stopPropagation();
                        });
                    }

                    items.forEach(item => {
                        item.addEventListener('click', function(e) {
                            e.stopPropagation();
                            const val = this.getAttribute('data-value');
                            if (hiddenInput) {
                                hiddenInput.value = val;
                            }
                            dropdown.classList.remove('is-active');
                            document.getElementById('filter-form').submit();
                        });
                    });
                });

                // Close on click outside
                document.addEventListener('click', function() {
                    dropdowns.forEach(dropdown => {
                        dropdown.classList.remove('is-active');
                    });
                });

                // Modal Detail User logic
                const modal = document.querySelector('[data-user-detail-modal]');
                if (modal) {
                    const closeBtn = modal.querySelector('[data-close-user-detail-modal]');
                    const detailAvatar = document.getElementById('detail-avatar');
                    const detailName = document.getElementById('detail-name');
                    const detailEmail = document.getElementById('detail-email');
                    const detailUsername = document.getElementById('detail-username');
                    const detailContact = document.getElementById('detail-contact');
                    const detailTransactions = document.getElementById('detail-transactions');
                    const detailStatus = document.getElementById('detail-status');
                    const detailRegistered = document.getElementById('detail-registered');

                    document.querySelectorAll('[data-user-detail]').forEach(button => {
                        button.addEventListener('click', function() {
                            const user = JSON.parse(this.getAttribute('data-user-detail'));
                            
                            // Populate details
                            if (detailAvatar) {
                                detailAvatar.textContent = user.initials;
                                detailAvatar.style.background = user.avatar_background;
                            }
                            if (detailName) detailName.textContent = user.name;
                            if (detailEmail) detailEmail.textContent = user.email;
                            if (detailUsername) detailUsername.textContent = '@' + user.username;
                            if (detailContact) detailContact.textContent = user.contact;
                            

                            if (detailTransactions) {
                                const formatted = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(user.total_transactions);
                                detailTransactions.textContent = formatted;
                            }
                            if (detailStatus) {
                                detailStatus.innerHTML = `<span class="table-pill ${user.status_tone}">${user.status}</span>`;
                            }
                            if (detailRegistered) {
                                detailRegistered.textContent = `${user.registered_day} ${user.registered_year}`;
                            }

                            // Open modal
                            modal.classList.add('is-open');
                            document.body.style.overflow = 'hidden';
                        });
                    });

                    const closeModal = () => {
                        modal.classList.remove('is-open');
                        document.body.style.overflow = '';
                    };

                    if (closeBtn) {
                        closeBtn.addEventListener('click', closeModal);
                    }

                    modal.addEventListener('click', function(e) {
                        if (e.target === modal) {
                            closeModal();
                        }
                    });
                }

                // Modal Delete User logic
                const deleteModal = document.querySelector('[data-delete-user-modal]');
                if (deleteModal) {
                    const deleteCloseBtn = deleteModal.querySelector('[data-close-delete-user-modal]');
                    const deleteForm = document.getElementById('delete-user-form');
                    const deleteNameSpan = document.getElementById('delete-user-name');

                    document.querySelectorAll('[data-delete-user-id]').forEach(button => {
                        button.addEventListener('click', function() {
                            const name = this.getAttribute('data-delete-user-name');
                            const url = this.getAttribute('data-delete-url');

                            if (deleteNameSpan) deleteNameSpan.textContent = name;
                            if (deleteForm) deleteForm.setAttribute('action', url);

                            deleteModal.classList.add('is-open');
                            document.body.style.overflow = 'hidden';
                        });
                    });

                    const closeDeleteModal = () => {
                        deleteModal.classList.remove('is-open');
                        document.body.style.overflow = '';
                    };

                    if (deleteCloseBtn) {
                        deleteCloseBtn.addEventListener('click', closeDeleteModal);
                    }

                    deleteModal.addEventListener('click', function(e) {
                        if (e.target === deleteModal) {
                            closeDeleteModal();
                        }
                    });
                }
            });
        </script>
    @endpush
</x-backoffice.layout>
