<x-backoffice.layout title="Reservasi" :admin="$admin" active="reservations" search-placeholder="Cari reservasi...">
    <section class="page-head">
        <div>
            <h1 class="page-title">Daftar Reservasi</h1>
            <p class="page-subtitle">Kelola dan pantau seluruh aktivitas reservasi penyewaan armada.</p>
        </div>
    </section>

    @if (session('success'))
    <div class="flash-banner">
        <span>{{ session('success') }}</span>
        <button type="button" class="modal-close" data-dismiss-flash aria-label="Tutup notifikasi">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M18 6 6 18" />
                <path d="m6 6 12 12" />
            </svg>
        </button>
    </div>
    @endif

    @if (session('error'))
    <div class="flash-banner"
        style="background: rgba(239, 68, 68, 0.08); border-color: rgba(239, 68, 68, 0.2); color: #b42318;">
        <span>{{ session('error') }}</span>
        <button type="button" class="modal-close" data-dismiss-flash aria-label="Tutup notifikasi"
            style="color: #b42318;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M18 6 6 18" />
                <path d="m6 6 12 12" />
            </svg>
        </button>
    </div>
    @endif

    @if (session('warning'))
    <div class="flash-banner"
        style="background: rgba(245, 158, 11, 0.10); border-color: rgba(245, 158, 11, 0.22); color: #92400e;">
        <span>{{ session('warning') }}</span>
        <button type="button" class="modal-close" data-dismiss-flash aria-label="Tutup notifikasi"
            style="color: #92400e;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M18 6 6 18" />
                <path d="m6 6 12 12" />
            </svg>
        </button>
    </div>
    @endif

    <section
        style="display: grid; grid-template-columns: repeat(5, minmax(0, 1fr)); gap: 18px; margin-top: 18px; margin-bottom: 18px;">
        <a href="{{ route('backoffice.reservations') }}" class="card" style="display: flex; align-items: center; gap: 16px; padding: 22px 24px; text-decoration: none; color: inherit; cursor: pointer; border: 1px solid transparent; transition: all 0.2s;" onmouseover="this.style.borderColor='var(--blue)'; this.style.boxShadow='0 4px 12px rgba(63, 94, 215, 0.05)'" onmouseout="this.style.borderColor='transparent'; this.style.boxShadow='none'">
            <div
                style="width: 54px; height: 54px; border-radius: 16px; display: grid; place-items: center; background: rgba(63, 94, 215, 0.10); color: var(--blue); flex: 0 0 auto;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M4 7h16v10H4z" />
                    <path d="M7 7V5h10v2" />
                    <path d="M8 11h8" />
                </svg>
            </div>
            <div>
                <div class="page-subtitle"
                    style="margin-bottom: 6px; font-weight: 700; text-transform: uppercase; letter-spacing: .04em;">
                    Total</div>
                <div class="page-title" style="font-size: 28px; margin: 0; line-height: 1;">
                    {{ number_format($summary['total'] ?? 0) }}</div>
            </div>
        </a>

        <a href="{{ route('backoffice.reservations', ['status_filter' => 'waiting_review']) }}" class="card" style="display: flex; align-items: center; gap: 16px; padding: 22px 24px; text-decoration: none; color: inherit; cursor: pointer; border: 1px solid transparent; transition: all 0.2s;" onmouseover="this.style.borderColor='var(--red)'; this.style.boxShadow='0 4px 12px rgba(239, 68, 68, 0.05)'" onmouseout="this.style.borderColor='transparent'; this.style.boxShadow='none'">
            <div
                style="width: 54px; height: 54px; border-radius: 16px; display: grid; place-items: center; background: rgba(239, 68, 68, 0.12); color: var(--red); flex: 0 0 auto;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z" />
                    <path d="M12 8v4" />
                    <path d="M12 16h.01" />
                </svg>
            </div>
            <div>
                <div class="page-subtitle"
                    style="margin-bottom: 6px; font-weight: 700; text-transform: uppercase; letter-spacing: .04em;">
                    Butuh Review</div>
                <div class="page-title" style="font-size: 28px; margin: 0; line-height: 1;">
                    {{ number_format($summary['needs_review'] ?? 0) }}</div>
            </div>
        </a>

        <a href="{{ route('backoffice.reservations', ['status_filter' => 'active']) }}" class="card" style="display: flex; align-items: center; gap: 16px; padding: 22px 24px; text-decoration: none; color: inherit; cursor: pointer; border: 1px solid transparent; transition: all 0.2s;" onmouseover="this.style.borderColor='#d97706'; this.style.boxShadow='0 4px 12px rgba(217, 119, 6, 0.05)'" onmouseout="this.style.borderColor='transparent'; this.style.boxShadow='none'">
            <div
                style="width: 54px; height: 54px; border-radius: 16px; display: grid; place-items: center; background: rgba(255, 193, 7, 0.16); color: #d97706; flex: 0 0 auto;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M7 7h10v10H7z" />
                    <path d="M12 8v5l3 2" />
                </svg>
            </div>
            <div>
                <div class="page-subtitle"
                    style="margin-bottom: 6px; font-weight: 700; text-transform: uppercase; letter-spacing: .04em;">
                    Aktif</div>
                <div class="page-title" style="font-size: 28px; margin: 0; line-height: 1;">
                    {{ number_format($summary['active'] ?? 0) }}</div>
            </div>
        </a>

        <a href="{{ route('backoffice.reservations', ['status_filter' => 'returned']) }}" class="card" style="display: flex; align-items: center; gap: 16px; padding: 22px 24px; text-decoration: none; color: inherit; cursor: pointer; border: 1px solid transparent; transition: all 0.2s;" onmouseover="this.style.borderColor='var(--green)'; this.style.boxShadow='0 4px 12px rgba(29, 187, 132, 0.05)'" onmouseout="this.style.borderColor='transparent'; this.style.boxShadow='none'">
            <div
                style="width: 54px; height: 54px; border-radius: 16px; display: grid; place-items: center; background: rgba(29, 187, 132, 0.12); color: var(--green); flex: 0 0 auto;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="8" />
                    <path d="m9.5 12 1.8 1.8L15 10.2" />
                </svg>
            </div>
            <div>
                <div class="page-subtitle"
                    style="margin-bottom: 6px; font-weight: 700; text-transform: uppercase; letter-spacing: .04em;">
                    Selesai</div>
                <div class="page-title" style="font-size: 28px; margin: 0; line-height: 1;">
                    {{ number_format($summary['completed'] ?? 0) }}</div>
            </div>
        </a>

        <a href="{{ route('backoffice.reservations', ['status_filter' => 'overdue']) }}" class="card" style="display: flex; align-items: center; gap: 16px; padding: 22px 24px; text-decoration: none; color: inherit; cursor: pointer; border: 1px solid rgba(245, 158, 11, 0.2); background: rgba(245, 158, 11, 0.04); transition: all 0.2s;" onmouseover="this.style.borderColor='var(--amber)'; this.style.boxShadow='0 4px 12px rgba(245, 158, 11, 0.08)'" onmouseout="this.style.borderColor='rgba(245, 158, 11, 0.2)'; this.style.boxShadow='none'">
            <div
                style="width: 54px; height: 54px; border-radius: 16px; display: grid; place-items: center; background: rgba(245, 158, 11, 0.15); color: #b96e00; flex: 0 0 auto;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10" />
                    <line x1="12" y1="8" x2="12" y2="12" />
                    <line x1="12" y1="16" x2="12.01" y2="16" />
                </svg>
            </div>
            <div>
                <div class="page-subtitle"
                    style="margin-bottom: 6px; font-weight: 700; text-transform: uppercase; letter-spacing: .04em; color: #b96e00;">
                    Terlambat</div>
                <div class="page-title" style="font-size: 28px; margin: 0; line-height: 1; color: #b96e00;">
                    {{ number_format($summary['overdue'] ?? 0) }}</div>
            </div>
        </a>
    </section>

    <section class="card" style="
    margin-top:18px;
    padding:18px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    gap:16px;
    flex-wrap:wrap;
">
        <form method="GET" style="
            display:flex;
            align-items:center;
            gap:12px;
            flex-wrap:wrap;
            flex:1;
          ">

            <select name="status_filter" onchange="this.form.submit()" style="
                min-width:220px;
                padding:12px 14px;
                border:1px solid #e2e8f0;
                border-radius:12px;
                background:#fff;
                font-size:14px;
                color:#334155;
            ">
                
                <option value="">Semua Status</option>
                
                <option value="waiting_review" {{ request('status_filter') === 'waiting_review' ? 'selected' : '' }}>
                    Butuh Review
                </option>
                
                <option value="waiting_pay" {{ request('status_filter') === 'waiting_pay' ? 'selected' : '' }}>
                    Menunggu Pembayaran
                </option>
                
                <option value="upcoming" {{ request('status_filter') === 'upcoming' ? 'selected' : '' }}>
                    Akan Datang
                </option>
                
                <option value="active" {{ request('status_filter') === 'active' ? 'selected' : '' }}>
                    Aktif
                </option>
                
                <option value="overdue" {{ request('status_filter') === 'overdue' ? 'selected' : '' }}>
                    Terlambat
                </option>
                    
                <option value="returned" {{ request('status_filter') === 'returned' ? 'selected' : '' }}>
                    Selesai
                </option>
            </select>
            
            <option value="cancelled_expired"
                {{ request('status_filter') === 'cancelled_expired' ? 'selected' : '' }}>
                Dibatalkan
            </option>
                    <select name="car_type" onchange="this.form.submit()" style="
                min-width:200px;
                padding:12px 14px;
                border:1px solid #e2e8f0;
                border-radius:12px;
                background:#fff;
                font-size:14px;
                color:#334155;
            ">
                        <option value="">Tipe Mobil</option>

                        @foreach($vehicleTypes as $type)
                        <option value="{{ $type->value }}" {{ request('car_type') === $type->value ? 'selected' : '' }}>
                            {{ $type->label() }}
                        </option>
                        @endforeach
                    </select>

                    <input type="date" name="date" value="{{ request('date') }}" onchange="this.form.submit()" style="
                padding:12px 14px;
                border:1px solid #e2e8f0;
                border-radius:12px;
                background:#fff;
                font-size:14px;
                color:#334155;
            ">

                    @if(request()->hasAny(['status_filter','car_type','date']))
                    <a href="{{ route('backoffice.reservations') }}" style="
                    padding:12px 14px;
                    border-radius:12px;
                    background:#f1f5f9;
                    color:#475569;
                    text-decoration:none;
                    font-size:14px;
                    font-weight:600;
               ">
                        Reset
                    </a>
                    @endif

        </form>

        <button type="button" data-open-reservation-modal style="
            border:none;
            background:#0f172a;
            color:white;
            padding:12px 18px;
            border-radius:12px;
            font-weight:700;
            cursor:pointer;
            white-space:nowrap;
        ">
            + Reservasi Baru
        </button>
    </section>

    <section class="card" style="margin-top: 18px;">
        <div style="overflow-x:auto;">
            <table class="table reservations-table" style="width:100%; border-collapse: collapse;">
                <thead>
                    <tr>
                        <th>ID Reservasi</th>
                        <th>Pelanggan</th>
                        <th>Mobil</th>
                        <th>Tanggal Sewa</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rentals as $rental)
                    <tr data-reservation-row="{{ $rental['id'] }}" @if (!empty($highlightRentalId) && (int)
                        $highlightRentalId===(int) ($rental['id'] ?? 0)) style="background: rgba(245, 158, 11, 0.08);"
                        @endif>
                        <td>{{ $rental['booking_id'] ?? $rental['id'] }}</td>
                        <td>{{ $rental['customer_name'] ?? $rental['customer'] ?? '-' }}</td>
                        <td>{{ $rental['car_model'] ?? $rental['car'] ?? '-' }}</td>
                        <td>{{ $rental['start_date'] ?? '-' }} — {{ $rental['end_date'] ?? '-' }}</td>
                        <td>Rp {{ number_format($rental['total_price'] ?? ($rental['total'] ?? 0), 0, ',', '.') }}</td>
                        <td>
                            {{ $rental['status'] ?? '-' }}
                            @if (!empty($rental['is_overdue']))
                                <span class="pill amber" style="margin-left: 6px; font-size: 10px; padding: 2px 6px;">Terlambat</span>
                            @endif
                        </td>
                        <td>
                            <button type="button" class="text-action detail" data-reservation-detail
                                data-reservation='@json($rental)'>Lihat</button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="text-align:center; padding: 24px;">Belum ada reservasi</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    @if ($rentals->hasPages())
    <section class="card table-card" style="margin-top: 16px;">
        <div class="table-footer"
            style="border-top: 0; display:flex; justify-content:space-between; align-items:center;">
            <span>Halaman {{ $rentals->currentPage() }} dari {{ $rentals->lastPage() }}</span>
            <div class="pagination">
                @if ($rentals->onFirstPage())
                <span class="page-link muted">‹</span>
                @else
                <a href="{{ $rentals->previousPageUrl() }}" class="page-link">‹</a>
                @endif

                @foreach ($pagination as $pageItem)
                @if ($pageItem === '...')
                <span class="page-link muted">...</span>
                @elseif ($pageItem === $rentals->currentPage())
                <span class="page-link active">{{ $pageItem }}</span>
                @else
                <a href="{{ $rentals->url($pageItem) }}" class="page-link">{{ $pageItem }}</a>
                @endif
                @endforeach

                @if ($rentals->hasMorePages())
                <a href="{{ $rentals->nextPageUrl() }}" class="page-link">›</a>
                @else
                <span class="page-link muted">›</span>
                @endif
            </div>
        </div>
    </section>
    @endif

    <div class="modal-overlay {{ $errors->any() ? 'is-open' : '' }}" data-reservation-modal>
        <div class="modal-panel" role="dialog" aria-modal="true" aria-labelledby="reservation-form-title">
            <div class="modal-header">
                <div>
                    <h2 class="modal-title" id="reservation-form-title">Reservasi Baru</h2>
                    <p class="modal-subtitle">Buat reservasi baru untuk pelanggan.</p>
                </div>

                <button type="button" class="modal-close" data-close-reservation-modal aria-label="Tutup modal">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M18 6 6 18" />
                        <path d="m6 6 12 12" />
                    </svg>
                </button>
            </div>

            <div class="modal-body">
                <form method="POST" action="{{ route('backoffice.reservations.store') }}" enctype="multipart/form-data"
                    class="reservation-form">
                    @csrf

                    @if ($errors->any())
                    <div
                        style="padding: 14px; border-radius: 12px; background: rgba(239,68,68,0.06); color:#b42318; margin-bottom:12px;">
                        <strong>Periksa input:</strong>
                        <ul style="margin:6px 0 0 18px;">
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <div style="padding: 12px 14px; border-radius: 12px; background: rgba(59, 130, 246, 0.08); color: #1d4ed8; margin-bottom: 12px; font-size: 13px; line-height: 1.5;">
                        Ketersediaan mobil sekarang dihitung berdasarkan tanggal rental + buffer default 2 hari sebelum dan 1 hari sesudah. Status operasional mobil tetap harus tersedia.
                    </div>

                    <div class="form-grid">
                        <div class="form-field">
                            <label class="form-label" for="user_id">Pelanggan</label>
                            <select id="user_id" name="user_id" class="form-select">
                                <option value="">Pilih pelanggan</option>
                                @foreach ($customers as $customer)
                                <option value="{{ $customer['id'] }}" @selected(old('user_id')==$customer['id'])>
                                    {{ $customer['name'] }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-field">
                            <label class="form-label" for="car_id">Mobil</label>
                            <select id="car_id" name="car_id" class="form-select">
                                <option value="">Pilih mobil</option>
                                @foreach ($availableCars as $car)
                                <option value="{{ $car['id'] }}" @selected(old('car_id')==$car['id'])>
                                    {{ $car['brand'] }} {{ $car['model'] ?? $car['name'] }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-field">
                            <label class="form-label" for="start_date">Mulai</label>
                            <input id="start_date" name="start_date" type="date" class="form-input"
                                value="{{ old('start_date') }}">
                        </div>

                        <div class="form-field">
                            <label class="form-label" for="end_date">Selesai</label>
                            <input id="end_date" name="end_date" type="date" class="form-input"
                                value="{{ old('end_date') }}">
                        </div>

                        <div class="form-field">
                            <label class="form-label" for="type">Tipe Rental</label>
                            <select id="type" name="type" class="form-select">
                                <option value="">Pilih tipe</option>
                                @foreach (App\Enums\RentalType::values() as $typeOption)
                                <option value="{{ $typeOption }}" @selected(old('type')==$typeOption)>
                                    {{ str($typeOption)->headline() }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-field">
                            <label class="form-label" for="ktp">KTP (opsional)</label>
                            <input id="ktp" name="ktp" type="file" class="form-input">
                        </div>

                        <div class="form-field">
                            <label class="form-label" for="selfie">Selfie verifikasi (opsional)</label>
                            <input id="selfie" name="selfie" type="file" class="form-input">
                        </div>
                    </div>

                    <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:12px;">
                        <button type="button" class="secondary-button" data-close-reservation-modal>Tutup</button>
                        <button type="submit" class="primary-button">Buat Reservasi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal-overlay" data-reservation-detail-modal hidden>
        <div class="modal-panel" role="dialog" aria-modal="true">
            <div class="modal-header">
                <div>
                    <h2 class="modal-title">Detail Reservasi</h2>
                    <p class="modal-subtitle">Informasi lengkap reservasi</p>
                </div>

                <button type="button" class="modal-close" data-close-reservation-detail-modal aria-label="Tutup modal">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M18 6 6 18" />
                        <path d="m6 6 12 12" />
                    </svg>
                </button>
            </div>

            <div class="modal-body" data-reservation-detail-body>
                <div id="overdue-warning-banner" class="flash-banner"
                    style="display: none; background: rgba(245, 158, 11, 0.10); border-color: rgba(245, 158, 11, 0.22); color: #92400e; margin-bottom: 18px; margin-top: 0; padding: 12px 14px; border-radius: 12px; font-weight: 500;">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink: 0;">
                            <circle cx="12" cy="12" r="10" />
                            <line x1="12" y1="8" x2="12" y2="12" />
                            <line x1="12" y1="16" x2="12.01" y2="16" />
                        </svg>
                        <span>Pengembalian terlambat selama <strong id="overdue-warning-days">0</strong> hari! Segera hubungi customer untuk konfirmasi pengembalian armada.</span>
                    </div>
                    <div id="overdue-next-booking" style="display: none; margin-top: 10px; font-size: 13px; line-height: 1.5; color: #92400e;">
                        Booking berikutnya yang berpotensi terdampak: <strong id="overdue-next-booking-text">-</strong>
                    </div>
                </div>

                <div id="post-buffer-banner" class="flash-banner"
                    style="display: none; background: rgba(59, 130, 246, 0.10); border-color: rgba(59, 130, 246, 0.20); color: #1d4ed8; margin-bottom: 18px; margin-top: 0; padding: 12px 14px; border-radius: 12px; font-weight: 500;">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink: 0;">
                            <path d="M12 6v6l4 2" />
                            <circle cx="12" cy="12" r="9" />
                        </svg>
                        <span>Mobil masih berada dalam masa buffer setelah rental sampai <strong id="post-buffer-end-date">-</strong>.</span>
                    </div>
                </div>

                <dl style="display:grid; gap:8px; grid-template-columns: 140px 1fr;">
                    <dt>ID Reservasi</dt>
                    <dd data-detail-booking>-</dd>
                    <dt>Pelanggan</dt>
                    <dd data-detail-customer>-</dd>
                    <dt>Mobil</dt>
                    <dd data-detail-car>-</dd>
                    <dt>Tanggal Sewa</dt>
                    <dd data-detail-period>-</dd>
                    <dt>Total</dt>
                    <dd data-detail-total>-</dd>
                    <dt>Status</dt>
                    <dd data-detail-status>-</dd>
                </dl>

                <div id="car-details-section"
                    style="margin-top: 18px; padding-top: 16px; border-top: 1px dashed var(--line); display: none;">
                    <h3
                        style="margin-top: 0; margin-bottom: 12px; font-size: 14px; font-weight: 700; color: var(--text);">
                        Detail Mobil</h3>
                    <dl style="display:grid; gap:8px; grid-template-columns: 140px 1fr; margin-bottom: 0;">
                        <dt>Merek & Model</dt>
                        <dd data-detail-car-name>-</dd>
                        <dt>Plat Nomor</dt>
                        <dd data-detail-car-plate>-</dd>
                        <dt>Transmisi</dt>
                        <dd data-detail-car-transmission>-</dd>
                        <dt>Kapasitas</dt>
                        <dd data-detail-car-seats>-</dd>
                        <dt>Tahun</dt>
                        <dd data-detail-car-year>-</dd>
                        <dt>Kapasitas Mesin</dt>
                        <dd data-detail-car-cc>-</dd>
                        <dt>Tipe Kendaraan</dt>
                        <dd data-detail-car-type>-</dd>
                        <dt>Warna</dt>
                        <dd data-detail-car-color>-</dd>
                        <dt>Tarif Harian</dt>
                        <dd data-detail-car-rate>-</dd>
                    </dl>
                </div>

                <div id="verification-section"
                    style="margin-top: 24px; padding-top: 20px; border-top: 1px solid var(--line); display: none;">
                    <h3 style="margin-top: 0; margin-bottom: 16px; font-size: 16px; font-weight: 700;">Dokumen
                        Verifikasi Identitas</h3>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px;">
                        <div id="ktp-container">
                            <div
                                style="font-size: 12px; font-weight: 700; color: var(--muted); margin-bottom: 6px; text-transform: uppercase;">
                                Foto KTP</div>
                            <div
                                style="border: 1px solid var(--line); border-radius: 12px; overflow: hidden; background: #f8fafc; height: 200px; display: grid; place-items: center;">
                                <img id="detail-ktp-img" src="" alt="Foto KTP"
                                    style="max-width: 100%; max-height: 100%; object-fit: contain; cursor: pointer;"
                                    onclick="window.open(this.src)">
                            </div>
                        </div>
                        <div id="selfie-container">
                            <div
                                style="font-size: 12px; font-weight: 700; color: var(--muted); margin-bottom: 6px; text-transform: uppercase;">
                                Foto Selfie</div>
                            <div
                                style="border: 1px solid var(--line); border-radius: 12px; overflow: hidden; background: #f8fafc; height: 200px; display: grid; place-items: center;">
                                <img id="detail-selfie-img" src="" alt="Foto Selfie"
                                    style="max-width: 100%; max-height: 100%; object-fit: contain; cursor: pointer;"
                                    onclick="window.open(this.src)">
                            </div>
                        </div>
                    </div>

                    <form id="verification-action-form" method="POST" action=""
                        style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 16px;">
                        @csrf
                        <input type="hidden" name="action" id="verification-action-input" value="">
                        <button type="button" class="secondary-button" id="btn-reject-verification"
                            style="border-color: var(--red); color: var(--red); background: rgba(239, 68, 68, 0.05);"
                            onclick="submitVerificationAction('reject')">Tolak Verifikasi</button>
                        <button type="button" class="primary-button" id="btn-approve-verification"
                            style="background: var(--blue); box-shadow: 0 14px 34px rgba(63, 94, 215, 0.18);"
                            onclick="submitVerificationAction('approve')">Setujui Verifikasi</button>
                    </form>
                </div>

                <form id="return-action-form" method="POST" action=""
                    style="display: none; justify-content: flex-end; margin-top: 16px;">
                    @csrf
                    <button type="button" class="primary-button"
                        style="background: var(--green); box-shadow: 0 14px 34px rgba(29, 187, 132, 0.18);"
                        onclick="submitReturnAction()">Mobil Sudah DiKembalikan</button>
                </form>

                <form id="cancel-action-form" method="POST" action=""
                    style="display: none; justify-content: flex-end; margin-top: 16px;">
                    @csrf
                    <button type="button" class="secondary-button"
                        style="border-color: var(--red); color: var(--red); background: rgba(239, 68, 68, 0.05);"
                        onclick="submitCancelAction()">Batalkan Reservasi</button>
                </form>

                <form id="release-post-buffer-form" method="POST" action=""
                    style="display: none; justify-content: flex-end; margin-top: 16px;">
                    @csrf
                    <button type="button" class="primary-button"
                        style="background: #1d4ed8; box-shadow: 0 14px 34px rgba(29, 78, 216, 0.18);"
                        onclick="submitReleasePostBufferAction()">Lepas Buffer Setelah Rental</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Custom Confirmation Modal -->
    <div class="modal-overlay" id="confirm-modal-overlay"
        style="z-index: 100; display: none; background: rgba(10, 15, 26, 0.4);" hidden>
        <div class="modal-panel"
            style="width: min(450px, 100%); border-radius: 20px; padding: 24px; text-align: center; transform: scale(0.95); transition: transform 0.2s ease;">
            <div
                style="width: 54px; height: 54px; border-radius: 50%; display: grid; place-items: center; background: rgba(245, 158, 11, 0.12); color: var(--amber); margin: 0 auto 16px;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <path d="M12 9v4" />
                    <path d="m12 17h.01" />
                    <path
                        d="m10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z" />
                </svg>
            </div>
            <h3 style="margin-top: 0; margin-bottom: 8px; font-size: 18px; font-weight: 700; color: var(--text);">
                Konfirmasi Tindakan</h3>
            <p id="confirm-modal-message"
                style="margin-top: 0; margin-bottom: 24px; font-size: 14px; color: var(--muted); line-height: 1.5;"></p>
            <div style="display: flex; gap: 12px; justify-content: center;">
                <button type="button" class="secondary-button" id="confirm-modal-cancel-btn"
                    style="padding: 10px 20px; border-radius: 12px;">Batal</button>
                <button type="button" class="primary-button" id="confirm-modal-ok-btn"
                    style="padding: 10px 20px; border-radius: 12px; background: var(--blue);">Lanjutkan</button>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    (function() {
        const formModal = document.querySelector('[data-reservation-modal]');
        const detailModal = document.querySelector('[data-reservation-detail-modal]');
        if (!formModal && !detailModal) return;
        const highlightRentalId = @json($highlightRentalId ?? null);

        const openButtons = document.querySelectorAll('[data-open-reservation-modal]');
        const closeFormButtons = document.querySelectorAll('[data-close-reservation-modal]');
        const closeDetailButtons = document.querySelectorAll('[data-close-reservation-detail-modal]');
        const formPanel = formModal?.querySelector('.modal-panel');
        const detailPanel = detailModal?.querySelector('.modal-panel');

        const openForm = () => {
            formModal.classList.add('is-open');
            formModal.removeAttribute('hidden');
            document.body.style.overflow = 'hidden';
        };

        const closeForm = () => {
            formModal.classList.remove('is-open');
            document.body.style.overflow = '';
        };

        const openDetail = () => {
            detailModal.classList.add('is-open');
            detailModal.removeAttribute('hidden');
            document.body.style.overflow = 'hidden';
        };

        const closeDetail = () => {
            detailModal.classList.remove('is-open');
            document.body.style.overflow = '';
            detailModal.setAttribute('hidden', '');
        };

        openButtons.forEach((b) => b.addEventListener('click', () => {
            closeDetail();
            openForm();
        }));
        closeFormButtons.forEach((b) => b.addEventListener('click', closeForm));
        closeDetailButtons.forEach((b) => b.addEventListener('click', closeDetail));

        formModal?.addEventListener('click', (e) => {
            if (e.target === formModal) closeForm();
        });
        detailModal?.addEventListener('click', (e) => {
            if (e.target === detailModal) closeDetail();
        });

        formPanel?.addEventListener('click', (e) => e.stopPropagation());
        detailPanel?.addEventListener('click', (e) => e.stopPropagation());

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                closeForm();
                closeDetail();
            }
        });

        window.showCustomConfirm = function(message, onConfirm) {
            const overlay = document.getElementById('confirm-modal-overlay');
            const msgEl = document.getElementById('confirm-modal-message');
            const okBtn = document.getElementById('confirm-modal-ok-btn');
            const cancelBtn = document.getElementById('confirm-modal-cancel-btn');

            if (!overlay || !msgEl || !okBtn || !cancelBtn) return;

            msgEl.textContent = message;
            overlay.style.display = 'grid';
            overlay.removeAttribute('hidden');

            // Force reflow
            overlay.offsetHeight;
            overlay.classList.add('is-open');
            overlay.firstElementChild.style.transform = 'scale(1)';

            const cleanup = () => {
                overlay.classList.remove('is-open');
                overlay.firstElementChild.style.transform = 'scale(0.95)';
                setTimeout(() => {
                    overlay.style.display = 'none';
                    overlay.setAttribute('hidden', '');
                }, 200);
            };

            // Remove old listeners
            const newOkBtn = okBtn.cloneNode(true);
            const newCancelBtn = cancelBtn.cloneNode(true);
            okBtn.parentNode.replaceChild(newOkBtn, okBtn);
            cancelBtn.parentNode.replaceChild(newCancelBtn, cancelBtn);

            newOkBtn.addEventListener('click', () => {
                cleanup();
                onConfirm();
            });

            newCancelBtn.addEventListener('click', cleanup);
            overlay.addEventListener('click', (e) => {
                if (e.target === overlay) cleanup();
            });
        };

        window.submitVerificationAction = function(action) {
            const form = document.getElementById('verification-action-form');
            const input = document.getElementById('verification-action-input');
            if (form && input) {
                window.showCustomConfirm(
                    `Apakah Anda yakin ingin ${action === 'approve' ? 'menyetujui' : 'menolak'} verifikasi ini?`,
                    () => {
                        input.value = action;
                        form.submit();
                    });
            }
        };

        window.submitReturnAction = function() {
            const form = document.getElementById('return-action-form');
            if (form) {
                window.showCustomConfirm(
                    'Apakah Anda yakin mobil ini sudah dikembalikan dan ingin menyelesaikan sewa?', () => {
                        form.submit();
                    });
            }
        };

        window.submitCancelAction = function() {
            const form = document.getElementById('cancel-action-form');
            if (form) {
                window.showCustomConfirm('Apakah Anda yakin ingin membatalkan reservasi ini?', () => {
                    form.submit();
                });
            }
        };

        window.submitReleasePostBufferAction = function() {
            const form = document.getElementById('release-post-buffer-form');
            if (form) {
                window.showCustomConfirm('Lepas buffer setelah rental ini agar mobil bisa dibooking lebih cepat?', () => {
                    form.submit();
                });
            }
        };

        document.querySelectorAll('[data-reservation-detail]').forEach((btn) => {
            btn.addEventListener('click', () => {
                const payload = btn.dataset.reservation || '{}';
                try {
                    const data = JSON.parse(payload);
                    (document.querySelector('[data-detail-booking]') || {}).textContent = data
                        .booking_id || data.id || '-';
                    (document.querySelector('[data-detail-customer]') || {}).textContent = data
                        .customer_name || data.customer || '-';
                    (document.querySelector('[data-detail-car]') || {}).textContent = data
                        .car_model || data.car || '-';
                    (document.querySelector('[data-detail-period]') || {}).textContent = (data
                        .start_date || '-') + ' — ' + (data.end_date || '-');
                    (document.querySelector('[data-detail-total]') || {}).textContent = 'Rp ' + (
                        data.total_price ? new Intl.NumberFormat('id-ID').format(data
                            .total_price) : (data.total ? new Intl.NumberFormat('id-ID').format(
                            data.total) : '0'));
                    (document.querySelector('[data-detail-status]') || {}).textContent = data
                        .status || '-';

                    const overdueBanner = document.getElementById('overdue-warning-banner');
                    const overdueDaysEl = document.getElementById('overdue-warning-days');
                    const overdueNextBooking = document.getElementById('overdue-next-booking');
                    const overdueNextBookingText = document.getElementById('overdue-next-booking-text');
                    const postBufferBanner = document.getElementById('post-buffer-banner');
                    const postBufferEndDate = document.getElementById('post-buffer-end-date');
                    if (overdueBanner && overdueDaysEl) {
                        if (data.is_overdue) {
                            overdueDaysEl.textContent = data.overdue_days || '0';
                            overdueBanner.style.display = 'block';
                            if (overdueNextBooking && overdueNextBookingText) {
                                if (data.next_impacted_booking) {
                                    overdueNextBooking.style.display = 'block';
                                    overdueNextBookingText.textContent =
                                        `#${data.next_impacted_booking.id} ${data.next_impacted_booking.customer_name} (${data.next_impacted_booking.start_date} - ${data.next_impacted_booking.end_date})`;
                                } else {
                                    overdueNextBooking.style.display = 'none';
                                }
                            }
                        } else {
                            overdueBanner.style.display = 'none';
                        }
                    }

                    if (postBufferBanner && postBufferEndDate) {
                        if (data.post_buffer_active) {
                            postBufferEndDate.textContent = data.post_buffer_end_date || '-';
                            postBufferBanner.style.display = 'block';
                        } else {
                            postBufferBanner.style.display = 'none';
                        }
                    }

                    const verSection = document.getElementById('verification-section');
                    const ktpImg = document.getElementById('detail-ktp-img');
                    const selfieImg = document.getElementById('detail-selfie-img');
                    const ktpContainer = document.getElementById('ktp-container');
                    const selfieContainer = document.getElementById('selfie-container');
                    const actionForm = document.getElementById('verification-action-form');

                    if (data.ktp_url || data.selfie_url) {
                        verSection.style.display = 'block';
                        if (data.ktp_url) {
                            ktpImg.src = data.ktp_url;
                            ktpContainer.style.display = 'block';
                        } else {
                            ktpContainer.style.display = 'none';
                        }
                        if (data.selfie_url) {
                            selfieImg.src = data.selfie_url;
                            selfieContainer.style.display = 'block';
                        } else {
                            selfieContainer.style.display = 'none';
                        }

                        // Show action buttons only if status is PENDING_VERIFICATION and needs review
                        if (data.status_raw === 'pending_verification' && data
                            .verification_status === 'needs_review') {
                            actionForm.style.display = 'flex';
                            actionForm.action = '/dashboard/reservations/' + data.id + '/verify';
                        } else {
                            actionForm.style.display = 'none';
                        }
                    } else {
                        verSection.style.display = 'none';
                    }

                    const carSec = document.getElementById('car-details-section');
                    if (data.car_details) {
                        carSec.style.display = 'block';
                        (document.querySelector('[data-detail-car-name]') || {}).textContent = (data
                            .car_details.brand || '') + ' ' + (data.car_details.name || '-');
                        (document.querySelector('[data-detail-car-plate]') || {}).textContent = data
                            .car_details.license_plate || '-';
                        (document.querySelector('[data-detail-car-transmission]') || {}).textContent
                            = data.car_details.transmission || '-';
                        (document.querySelector('[data-detail-car-seats]') || {}).textContent = data
                            .car_details.seat_count || '-';
                        (document.querySelector('[data-detail-car-year]') || {}).textContent = data
                            .car_details.year || '-';
                        (document.querySelector('[data-detail-car-cc]') || {}).textContent = data
                            .car_details.cc || '-';
                        (document.querySelector('[data-detail-car-type]') || {}).textContent = data
                            .car_details.vehicle_type || '-';
                        (document.querySelector('[data-detail-car-color]') || {}).textContent = data
                            .car_details.color || '-';
                        (document.querySelector('[data-detail-car-rate]') || {}).textContent = data
                            .car_details.daily_rate || '-';
                    } else {
                        carSec.style.display = 'none';
                    }

                    const returnForm = document.getElementById('return-action-form');
                    if (data.status_raw === 'ongoing') {
                        returnForm.style.display = 'flex';
                        returnForm.action = '/dashboard/reservations/' + data.id + '/return';
                    } else {
                        returnForm.style.display = 'none';
                    }

                    const cancelForm = document.getElementById('cancel-action-form');
                    if (data.status_raw === 'pending_verification' || data.status_raw ===
                        'prepaid') {
                        cancelForm.style.display = 'flex';
                        cancelForm.action = '/dashboard/reservations/' + data.id + '/cancel';
                    } else {
                        cancelForm.style.display = 'none';
                    }

                    const releasePostBufferForm = document.getElementById('release-post-buffer-form');
                    if (data.status_raw === 'returned' && data.post_buffer_active && !data.post_buffer_released_at) {
                        releasePostBufferForm.style.display = 'flex';
                        releasePostBufferForm.action = data.release_post_buffer_url;
                    } else {
                        releasePostBufferForm.style.display = 'none';
                    }

                    closeForm();
                    openDetail();
                } catch (err) {
                    console.error('Invalid reservation payload', err);
                }
            });
        });

        if (highlightRentalId) {
            const targetButton = document.querySelector(
                `[data-reservation-row="${highlightRentalId}"] [data-reservation-detail]`);
            if (targetButton) {
                targetButton.click();
                const row = targetButton.closest('[data-reservation-row]');
                if (row) {
                    row.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                }
            }
        }

        if (formModal && formModal.classList.contains('is-open')) {
            document.body.style.overflow = 'hidden';
        }
    })();
    </script>
    @endpush

</x-backoffice.layout>
