<x-backoffice.layout title="Manajemen Mobil" :admin="$admin" active="cars" search-placeholder="Cari mobil...">
    <section class="page-head">
        <div>
            <h1 class="page-title">Manajemen Mobil</h1>
            <p class="page-subtitle">Kelola armada mobil rental premium Anda secara efisien.</p>
        </div>

        <button type="button" class="primary-button" data-open-car-modal>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M12 5v14"/>
                <path d="M5 12h14"/>
            </svg>
            <span>Tambah Mobil Baru</span>
        </button>
    </section>

    @if (session('success'))
        <div class="flash-banner">
            <span>{{ session('success') }}</span>
            <button type="button" class="modal-close" data-dismiss-flash aria-label="Tutup notifikasi">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M18 6 6 18"/>
                    <path d="m6 6 12 12"/>
                </svg>
            </button>
        </div>
    @endif

    <section class="fleet-stats-grid">
        <div class="card fleet-stat-card">
            <div class="stat-top">
                <div class="stat-icon" style="background: rgba(32, 38, 54, 0.08); color: #202636;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 16H9m10 0h2m-7 0h1m-9 0h1m0 0a2 2 0 1 0 4 0m-4 0a2 2 0 1 1 4 0m8 0a2 2 0 1 0 4 0m-4 0a2 2 0 1 1 4 0M3 12l2-5h13l3 5"/>
                    </svg>
                </div>
                <span class="delta up">+12% Bulan Ini</span>
            </div>
            <div class="stat-label">Total Mobil</div>
            <div class="stat-value">{{ number_format($stats['total']) }}</div>
        </div>

        <div class="card fleet-stat-card">
            <div class="stat-top">
                <div class="stat-icon" style="background: rgba(29, 187, 132, 0.12); color: var(--green);">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="9"/>
                        <path d="m9 12 2 2 4-4"/>
                    </svg>
                </div>
                <span class="delta up">Optimal</span>
            </div>
            <div class="stat-label">Tersedia</div>
            <div class="stat-value" style="color: var(--green);">{{ number_format($stats['available']) }}</div>
        </div>

        <div class="card fleet-stat-card">
            <div class="stat-top">
                <div class="stat-icon" style="background: rgba(63, 94, 215, 0.12); color: var(--blue);">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M7 7h10l2 5H5l2-5Z"/>
                        <path d="M5 12v5a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-5"/>
                    </svg>
                </div>
                <span class="delta" style="color: var(--blue);">{{ $stats['occupancy_rate'] }}% Occupancy</span>
            </div>
            <div class="stat-label">Disewa</div>
            <div class="stat-value" style="color: var(--blue);">{{ number_format($stats['rented']) }}</div>
        </div>

        <div class="card fleet-stat-card">
            <div class="stat-top">
                <div class="stat-icon" style="background: rgba(239, 68, 68, 0.12); color: var(--red);">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="m10 13 5-5"/>
                        <path d="m15 13-5-5"/>
                        <path d="M14 3h7v7"/>
                        <path d="M21 3 10 14"/>
                        <path d="M5 3H3v2"/>
                        <path d="M3 8v13h13"/>
                    </svg>
                </div>
                <span class="delta" style="color: var(--red);">Perlu Tindakan</span>
            </div>
            <div class="stat-label">Maintenance</div>
            <div class="stat-value" style="color: var(--red);">{{ number_format($stats['maintenance']) }}</div>
        </div>
    </section>

    @php
        $hasActiveFilters = filled($filters['search']) || filled($filters['status']) || filled($filters['type']) || filled($filters['transmission']);
    @endphp

    <details class="card" style="margin-bottom: 24px;" @if ($hasActiveFilters) open @endif>
        <summary style="list-style: none; cursor: pointer;">
            <div style="display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap;">
                <div>
                    <h2 class="section-title">Cari dengan filter</h2>
                    <p class="page-subtitle" style="margin-top: 6px;">
                        Menampilkan {{ $cars->firstItem() ?? 0 }}-{{ $cars->lastItem() ?? 0 }} dari {{ $cars->total() }} mobil
                        @if ($hasActiveFilters)
                            • filter aktif
                        @endif
                    </p>
                </div>

                <span class="secondary-button" style="pointer-events: none;">
                    {{ $hasActiveFilters ? 'Sembunyikan Filter' : 'Tampilkan Filter' }}
                </span>
            </div>
        </summary>

        <form method="GET" action="{{ route('backoffice.cars') }}" style="margin-top: 18px;">
            <div class="filter-bar" style="margin-bottom: 18px;">
                <div class="form-field" style="margin: 0;">
                    <label class="form-label" for="search">Cari mobil</label>
                    <input
                        id="search"
                        name="search"
                        type="text"
                        class="form-input"
                        value="{{ $filters['search'] }}"
                        placeholder="Nama, merk, plat nomor, atau tipe"
                    >
                </div>

                <div class="form-field" style="margin: 0;">
                    <label class="form-label" for="status">Status</label>
                    <select id="status" name="status" class="form-select">
                        <option value="">Semua status</option>
                        <option value="available" @selected($filters['status'] === 'available')>Tersedia</option>
                        <option value="rented" @selected($filters['status'] === 'rented')>Disewa</option>
                        <option value="maintenance" @selected($filters['status'] === 'maintenance')>Maintenance</option>
                    </select>
                </div>

                <div class="form-field" style="margin: 0;">
                    <label class="form-label" for="type_filter">Tipe mobil</label>
                    <select id="type_filter" name="type" class="form-select">
                        <option value="">Semua tipe</option>
                        @foreach ($typeOptions as $typeOption)
                            <option value="{{ $typeOption }}" @selected($filters['type'] === $typeOption)>{{ str($typeOption)->headline() }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-field" style="margin: 0;">
                    <label class="form-label" for="transmission_filter">Transmisi</label>
                    <select id="transmission_filter" name="transmission" class="form-select">
                        <option value="">Semua transmisi</option>
                        @foreach ($transmissionOptions as $transmissionOption)
                            <option value="{{ $transmissionOption }}" @selected($filters['transmission'] === $transmissionOption)>{{ str($transmissionOption)->headline() }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 12px; flex-wrap: wrap;">
                <a href="{{ route('backoffice.cars') }}" class="secondary-button">Reset</a>
                <button type="submit" class="primary-button">
                    <span>Terapkan Filter</span>
                </button>
            </div>
        </form>
    </details>

    <section class="fleet-grid">
        @forelse ($cars as $car)
            <article class="card fleet-card">
                <div class="fleet-media">
                    <span class="fleet-status-badge {{ strtolower($car['status_tone']) }}">{{ $car['status'] }}</span>

                    @if ($car['image_url'])
                        <img src="{{ $car['image_url'] }}" alt="{{ $car['model'] }}">
                    @else
                        <div class="fleet-image-fallback">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                                <path d="M14 16H9m10 0h2m-7 0h1m-9 0h1m0 0a2 2 0 1 0 4 0m-4 0a2 2 0 1 1 4 0m8 0a2 2 0 1 0 4 0m-4 0a2 2 0 1 1 4 0M3 12l2-5h13l3 5"/>
                                <path d="M5 12v4m14-4v4M7 7V5h10v2"/>
                            </svg>
                        </div>
                    @endif
                </div>

                <div class="fleet-body">
                    <div class="fleet-brand-row">
                        <span class="fleet-brand">{{ $car['brand'] }}</span>
                        <span class="fleet-rating">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="#fbbf24">
                                <path d="m12 2.5 2.94 5.96 6.58.96-4.76 4.64 1.12 6.56L12 17.52 6.12 20.62l1.12-6.56L2.48 9.42l6.58-.96L12 2.5Z"/>
                            </svg>
                            {{ $car['rating'] }}
                        </span>
                    </div>

                    <div class="fleet-title-row">
                        <h2 class="fleet-name" style="margin-bottom: 0;">{{ $car['model'] }}</h2>

                        <div class="fleet-title-actions" data-car-menu-root>
                            <button type="button" class="fleet-menu-button" data-car-menu-toggle aria-label="Opsi mobil" aria-expanded="false">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="5" r="1.6"/>
                                    <circle cx="12" cy="12" r="1.6"/>
                                    <circle cx="12" cy="19" r="1.6"/>
                                </svg>
                            </button>

                            <div class="fleet-menu" data-car-menu hidden>
                                <form method="POST" action="{{ route('backoffice.cars.destroy', ['car' => $car['id']]) }}" onsubmit="return confirm('Hapus mobil ini?');" style="margin: 0;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="fleet-menu-item">Hapus</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="fleet-specs">
                        <div class="fleet-spec-item">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="3"/>
                                <path d="M12 2v4m0 12v4m10-10h-4M6 12H2m17.07-7.07-2.83 2.83M7.76 16.24l-2.83 2.83m0-14.14 2.83 2.83m8.48 8.48 2.83 2.83"/>
                            </svg>
                            <span>{{ $car['transmission'] }}</span>
                        </div>
                        <div class="fleet-spec-item">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                                <circle cx="9" cy="7" r="4"/>
                            </svg>
                            <span>{{ $car['seat'] }}</span>
                        </div>
                        <div class="fleet-spec-item">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M5 12h14"/>
                                <path d="M8 8h8l1 4H7l1-4Z"/>
                                <path d="M7 16h10"/>
                            </svg>
                            <span>{{ $car['cc'] }}</span>
                        </div>
                        <div class="fleet-spec-item">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M7 7h10l2 5H5l2-5Z"/>
                                <path d="M5 12v5a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-5"/>
                            </svg>
                            <span>{{ $car['type'] }}</span>
                        </div>
                    </div>

                    <div class="fleet-price-row" style="margin-top: 14px;">
                        <div>
                            <div class="fleet-price-meta">{{ $car['plate'] }}</div>
                            <div class="fleet-price">Rp {{ $car['price_label'] }}<span>/hari</span></div>
                        </div>

                        <div class="fleet-card-actions">
                            <button
                                type="button"
                                class="text-action edit"
                                title="Edit"
                                data-car-edit
                                data-update-url="{{ route('backoffice.cars.update', ['car' => $car['id']]) }}"
                                data-car='@json($car)'
                            >
                                Edit
                            </button>
                            <button
                                type="button"
                                class="text-action detail"
                                title="Lihat Detail"
                                data-car-detail
                                data-car='@json($car)'
                            >
                                Lihat Detail
                            </button>
                        </div>
                    </div>
                </div>
            </article>
        @empty
            <article class="card" style="display: grid; place-items: center; min-height: 260px; text-align: center;">
                <div>
                    <h2 class="section-title" style="margin-bottom: 8px;">Mobil tidak ditemukan</h2>
                    <p class="page-subtitle">Coba ubah kata kunci pencarian atau filter yang dipakai.</p>
                </div>
            </article>
        @endforelse
    </section>

    @if ($cars->hasPages())
        <section class="card table-card" style="margin-top: 24px;">
            <div class="table-footer" style="border-top: 0;">
                <span>Halaman {{ $cars->currentPage() }} dari {{ $cars->lastPage() }}</span>

                <div class="pagination">
                    @if ($cars->onFirstPage())
                        <span class="page-link muted">‹</span>
                    @else
                        <a href="{{ $cars->previousPageUrl() }}" class="page-link">‹</a>
                    @endif

                    @foreach ($pagination as $pageItem)
                        @if ($pageItem === '...')
                            <span class="page-link muted">...</span>
                        @elseif ($pageItem === $cars->currentPage())
                            <span class="page-link active">{{ $pageItem }}</span>
                        @else
                            <a href="{{ $cars->url($pageItem) }}" class="page-link">{{ $pageItem }}</a>
                        @endif
                    @endforeach

                    @if ($cars->hasMorePages())
                        <a href="{{ $cars->nextPageUrl() }}" class="page-link">›</a>
                    @else
                        <span class="page-link muted">›</span>
                    @endif
                </div>
            </div>
        </section>
    @endif

    <div class="modal-overlay {{ $errors->any() ? 'is-open' : '' }}" data-car-form-modal>
        <div class="modal-panel" role="dialog" aria-modal="true" aria-labelledby="car-form-modal-title">
            <div class="modal-header">
                <div>
                    <h2 class="modal-title" id="car-form-modal-title" data-car-form-title>Tambah Mobil Baru</h2>
                    <p class="modal-subtitle" data-car-form-subtitle>
                        Isi data unit baru, foto utama, dan galeri pendukung sebelum mobil dipublikasikan ke katalog.
                    </p>
                </div>

                <button type="button" class="modal-close" data-close-car-form-modal aria-label="Tutup modal">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M18 6 6 18"/>
                        <path d="m6 6 12 12"/>
                    </svg>
                </button>
            </div>

            <div class="modal-body">
                <form
                    method="POST"
                    action="{{ route('backoffice.cars.store') }}"
                    enctype="multipart/form-data"
                    class="car-form"
                    data-car-form
                    data-old-update-url="{{ old('car_id') ? route('backoffice.cars.update', ['car' => old('car_id')]) : '' }}"
                >
                    @csrf
                    <input type="hidden" name="car_id" value="{{ old('car_id') }}" data-car-form-id>
                    <input type="hidden" name="_method" value="PUT" disabled data-car-form-method>
                    <input type="hidden" name="remove_image" value="{{ old('remove_image', 0) }}" data-remove-image-flag>
                    <input type="hidden" name="remove_gallery_images" value="{{ old('remove_gallery_images', '[]') }}" data-remove-gallery-flag>

                    @if ($errors->any())
                        <div style="padding: 14px 16px; border-radius: 16px; background: rgba(239, 68, 68, 0.08); border: 1px solid rgba(239, 68, 68, 0.16); color: #b42318; font-size: 13px;">
                            <strong style="display: block; margin-bottom: 4px;">Periksa kembali input berikut:</strong>
                            <ul style="margin: 0; padding-left: 18px;">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <section class="modal-card">
                        <div class="section-head">
                            <div>
                                <h3 class="section-title">Informasi Mobil</h3>
                                <p class="modal-subtitle">Data inti untuk katalog, pencarian, dan harga sewa.</p>
                            </div>
                        </div>

                        <div class="form-grid">
                            <div class="form-field">
                                <label class="form-label" for="brand">Merk Mobil</label>
                                <input id="brand" name="brand" type="text" class="form-input" value="{{ old('brand') }}" placeholder="Toyota" data-car-field="brand">
                                @error('brand')
                                    <div class="error-text">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-field">
                                <label class="form-label" for="name">Nama Mobil</label>
                                <input id="name" name="name" type="text" class="form-input" value="{{ old('name') }}" placeholder="Alphard" data-car-field="name">
                                @error('name')
                                    <div class="error-text">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-field">
                                <label class="form-label" for="license_plate">Plat Nomor</label>
                                <input id="license_plate" name="license_plate" type="text" class="form-input" value="{{ old('license_plate') }}" placeholder="B 1234 XYZ" data-car-field="license_plate">
                                @error('license_plate')
                                    <div class="error-text">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-field">
                                <label class="form-label" for="transmission">Transmisi</label>
                                <select id="transmission" name="transmission" class="form-select" data-car-field="transmission">
                                    <option value="">Pilih transmisi</option>
                                    @foreach (['Automatic', 'Manual', 'CVT'] as $transmission)
                                        <option value="{{ $transmission }}" @selected(old('transmission') === $transmission)>{{ $transmission }}</option>
                                    @endforeach
                                </select>
                                @error('transmission')
                                    <div class="error-text">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-field">
                                <label class="form-label" for="seat">Jumlah Kursi</label>
                                <input id="seat" name="seat" type="number" min="1" max="99" class="form-input" value="{{ old('seat') }}" placeholder="7" data-car-field="seat">
                                @error('seat')
                                    <div class="error-text">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-field">
                                <label class="form-label" for="year">Tahun</label>
                                <input id="year" name="year" type="number" min="1990" max="{{ now()->addYear()->year }}" class="form-input" value="{{ old('year') }}" placeholder="{{ now()->year }}" data-car-field="year">
                                @error('year')
                                    <div class="error-text">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-field">
                                <label class="form-label" for="cc">Kapasitas Mesin (CC)</label>
                                <input id="cc" name="cc" type="number" min="1" max="99999" class="form-input" value="{{ old('cc') }}" placeholder="2000" data-car-field="cc">
                                @error('cc')
                                    <div class="error-text">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-field">
                                <label class="form-label" for="type">Tipe Mobil</label>
                                <select id="type" name="type" class="form-select" data-car-field="type">
                                    <option value="">Pilih tipe</option>
                                    @foreach (['SUV', 'MPV', 'Sedan', 'Hatchback', 'City Car', 'Van', 'LCGC'] as $type)
                                        <option value="{{ $type }}" @selected(old('type') === $type)>{{ $type }}</option>
                                    @endforeach
                                </select>
                                @error('type')
                                    <div class="error-text">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-field">
                                <label class="form-label" for="color">Warna</label>
                                <input id="color" name="color" type="text" class="form-input" value="{{ old('color') }}" placeholder="Hitam" data-car-field="color">
                                @error('color')
                                    <div class="error-text">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-field">
                                <label class="form-label" for="rental_fee">Tarif Sewa per Hari</label>
                                <input id="rental_fee" name="rental_fee" type="number" min="0" class="form-input" value="{{ old('rental_fee') }}" placeholder="500000" data-car-field="rental_fee">
                                @error('rental_fee')
                                    <div class="error-text">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-field full">
                                <label class="form-label" for="description">Deskripsi</label>
                                <textarea id="description" name="description" class="form-textarea" placeholder="Tulis kondisi unit, fitur unggulan, dan catatan armada" data-car-field="description">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="error-text">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </section>

                    <section class="modal-card">
                        <div class="section-head">
                            <div>
                                <h3 class="section-title">Foto Mobil</h3>
                                <p class="modal-subtitle">Pilih satu foto utama dan beberapa foto pendukung untuk galeri unit.</p>
                            </div>
                        </div>

                        <div class="upload-stack">
                            <label class="upload-box" data-main-image-drop>
                                <input id="image" name="image" type="file" accept="image/*" class="upload-input" data-main-image-input data-car-field="image">
                                <div class="upload-preview upload-preview-main" data-main-image-preview>
                                    <div class="upload-placeholder">
                                        <div class="upload-badge">Foto utama</div>
                                        <div class="upload-icon">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                                <path d="M4 7h4l2-3h4l2 3h4v13H4V7Z"/>
                                                <circle cx="12" cy="13" r="3.5"/>
                                            </svg>
                                        </div>
                                        <div class="upload-title">Tarik atau klik untuk memilih foto utama</div>
                                        <div class="upload-text">Cover image ini akan dipakai di katalog mobil.</div>
                                    </div>
                                </div>
                            </label>
                            @error('image')
                                <div class="error-text">{{ $message }}</div>
                            @enderror

                            <label class="upload-box" data-gallery-drop>
                                <div class="upload-head">
                                    <div>
                                        <div class="form-label" style="margin-bottom: 0;">Galeri Pendukung</div>
                                        <div class="form-hint">Tambahkan hingga 8 foto tambahan untuk interior, eksterior, dan detail mobil.</div>
                                    </div>
                                    <span class="gallery-count">Maks 8</span>
                                </div>
                                <input id="gallery_images" name="gallery_images[]" type="file" accept="image/*" class="upload-input" multiple data-gallery-input data-car-field="gallery_images">
                                <div class="upload-gallery" data-gallery-preview>
                                    <div class="gallery-empty">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                            <rect x="3" y="4" width="18" height="16" rx="2"/>
                                            <path d="m8 14 2.5-3 3 3 2-2 2.5 3"/>
                                            <circle cx="9" cy="9" r="1.5"/>
                                        </svg>
                                        <p>Tambahkan foto pendukung di sini.</p>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </section>

                    <div class="form-actions">
                        <button type="button" class="secondary-button" data-close-car-form-modal>Batal</button>
                        <button type="submit" class="primary-button" data-car-submit-button>
                            <span>Simpan Mobil</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal-overlay" data-car-detail-modal>
        <div class="modal-panel" role="dialog" aria-modal="true" aria-labelledby="car-detail-modal-title">
            <div class="modal-header">
                <div>
                    <h2 class="modal-title" id="car-detail-modal-title">Detail Mobil</h2>
                    <p class="modal-subtitle">Seluruh informasi unit ditampilkan dalam kartu ringkas.</p>
                </div>

                <button type="button" class="modal-close" data-close-car-detail-modal aria-label="Tutup detail">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M18 6 6 18"/>
                        <path d="m6 6 12 12"/>
                    </svg>
                </button>
            </div>

            <div class="modal-body">
                <section class="modal-card">
                    <div class="section-head">
                        <div>
                            <h3 class="section-title" data-detail-title>Nama Mobil</h3>
                            <p class="modal-subtitle" data-detail-subtitle>Informasi umum kendaraan.</p>
                        </div>
                    </div>

                    <div class="detail-grid">
                        <div class="detail-item">
                            <div class="detail-label">Merk</div>
                            <div class="detail-value" data-detail-brand>-</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Plat Nomor</div>
                            <div class="detail-value" data-detail-plate>-</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Status</div>
                            <div class="detail-value" data-detail-status>-</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Rating</div>
                            <div class="detail-value" data-detail-rating>-</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Transmisi</div>
                            <div class="detail-value" data-detail-transmission>-</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Tipe</div>
                            <div class="detail-value" data-detail-type>-</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Tahun</div>
                            <div class="detail-value" data-detail-year>-</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Warna</div>
                            <div class="detail-value" data-detail-color>-</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Kursi</div>
                            <div class="detail-value" data-detail-seat>-</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">CC</div>
                            <div class="detail-value" data-detail-cc>-</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Tarif</div>
                            <div class="detail-value" data-detail-price>-</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Keterangan Status</div>
                            <div class="detail-value" data-detail-status-note>-</div>
                        </div>
                    </div>

                    <div style="margin-top: 18px;">
                        <div class="detail-label" style="margin-bottom: 10px;">Deskripsi</div>
                        <div class="detail-item">
                            <div class="detail-value" data-detail-description>-</div>
                        </div>
                    </div>
                </section>

                <section class="modal-card">
                    <div class="section-head">
                        <div>
                            <h3 class="section-title">Foto Mobil</h3>
                            <p class="modal-subtitle">Foto utama dan galeri pendukung.</p>
                        </div>
                    </div>

                    <div class="detail-image-shell">
                        <img src="" alt="Foto mobil" data-detail-main-image>
                    </div>

                    <div style="margin-top: 16px;">
                        <div class="detail-label" style="margin-bottom: 10px;">Galeri</div>
                        <div class="detail-gallery" data-detail-gallery></div>
                    </div>
                </section>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            (function () {
                const formModal = document.querySelector('[data-car-form-modal]');
                const detailModal = document.querySelector('[data-car-detail-modal]');
                if (!formModal || !detailModal) {
                    return;
                }

                const openButtons = document.querySelectorAll('[data-open-car-modal]');
                const closeFormButtons = document.querySelectorAll('[data-close-car-form-modal]');
                const closeDetailButtons = document.querySelectorAll('[data-close-car-detail-modal]');
                const flashButtons = document.querySelectorAll('[data-dismiss-flash]');
                const formPanel = formModal.querySelector('.modal-panel');
                const detailPanel = detailModal.querySelector('.modal-panel');
                const formElement = formModal.querySelector('[data-car-form]');
                const formTitle = formModal.querySelector('[data-car-form-title]');
                const formSubtitle = formModal.querySelector('[data-car-form-subtitle]');
                const submitButton = formModal.querySelector('[data-car-submit-button]');
                const methodInput = formModal.querySelector('[data-car-form-method]');
                const carIdInput = formModal.querySelector('[data-car-form-id]');
                const removeImageFlag = formModal.querySelector('[data-remove-image-flag]');
                const removeGalleryFlag = formModal.querySelector('[data-remove-gallery-flag]');
                const mainImageInput = formModal.querySelector('[data-main-image-input]');
                const mainImagePreview = formModal.querySelector('[data-main-image-preview]');
                const galleryInput = formModal.querySelector('[data-gallery-input]');
                const galleryPreview = formModal.querySelector('[data-gallery-preview]');
                const carMenuRoots = document.querySelectorAll('[data-car-menu-root]');
                const editButtons = document.querySelectorAll('[data-car-edit]');
                const detailButtons = document.querySelectorAll('[data-car-detail]');
                const detailFields = {
                    title: detailModal.querySelector('[data-detail-title]'),
                    subtitle: detailModal.querySelector('[data-detail-subtitle]'),
                    brand: detailModal.querySelector('[data-detail-brand]'),
                    plate: detailModal.querySelector('[data-detail-plate]'),
                    status: detailModal.querySelector('[data-detail-status]'),
                    rating: detailModal.querySelector('[data-detail-rating]'),
                    transmission: detailModal.querySelector('[data-detail-transmission]'),
                    type: detailModal.querySelector('[data-detail-type]'),
                    year: detailModal.querySelector('[data-detail-year]'),
                    color: detailModal.querySelector('[data-detail-color]'),
                    seat: detailModal.querySelector('[data-detail-seat]'),
                    cc: detailModal.querySelector('[data-detail-cc]'),
                    price: detailModal.querySelector('[data-detail-price]'),
                    statusNote: detailModal.querySelector('[data-detail-status-note]'),
                    description: detailModal.querySelector('[data-detail-description]'),
                    gallery: detailModal.querySelector('[data-detail-gallery]'),
                };
                const detailImageShell = detailModal.querySelector('.detail-image-shell');
                let existingGalleryItems = [];
                let selectedGalleryFiles = [];
                let mainImageLocked = false;

                const openModal = () => {
                    formModal.classList.add('is-open');
                    document.body.style.overflow = 'hidden';
                };

                const openDetailModal = () => {
                    detailModal.classList.add('is-open');
                    document.body.style.overflow = 'hidden';
                };

                const closeFormModal = () => {
                    formModal.classList.remove('is-open');
                    document.body.style.overflow = '';
                };

                const closeDetailModal = () => {
                    detailModal.classList.remove('is-open');
                    document.body.style.overflow = '';
                };

                const resetFormMode = () => {
                    formElement.action = @json(route('backoffice.cars.store'));
                    methodInput.disabled = true;
                    carIdInput.value = '';
                    removeImageFlag.value = '0';
                    mainImageLocked = false;
                    mainImageInput.disabled = false;
                    existingGalleryItems = [];
                    selectedGalleryFiles = [];
                    formTitle.textContent = 'Tambah Mobil Baru';
                    formSubtitle.textContent = 'Isi data unit baru, foto utama, dan galeri pendukung sebelum mobil dipublikasikan ke katalog.';
                    submitButton.querySelector('span').textContent = 'Simpan Mobil';
                };

                const fillCarForm = (car, updateUrl) => {
                    carIdInput.value = car.id ?? '';
                    formElement.action = updateUrl;
                    methodInput.disabled = false;
                    methodInput.value = 'PUT';
                    formTitle.textContent = 'Edit Mobil';
                    formSubtitle.textContent = 'Perbarui informasi unit, foto utama, dan galeri pendukung.';
                    submitButton.querySelector('span').textContent = 'Simpan Perubahan';

                    const fieldMap = {
                        brand: car.brand ?? '',
                        name: car.model ?? '',
                        license_plate: car.plate_raw ?? car.plate ?? '',
                        transmission: car.transmission_raw ?? '',
                        seat: car.seat_raw ?? '',
                        year: car.year_raw ?? '',
                        cc: car.cc_raw ?? '',
                        type: car.type_raw ?? '',
                        color: car.color ?? '',
                        rental_fee: car.price_raw ?? '',
                        description: car.description ?? '',
                    };

                    Object.entries(fieldMap).forEach(([key, value]) => {
                        const element = formModal.querySelector(`[data-car-field="${key}"]`);
                        if (!element) {
                            return;
                        }

                        if (element.type === 'file') {
                            return;
                        }

                        element.value = value;
                    });

                    const mainFileInput = formModal.querySelector('[data-main-image-input]');
                    const galleryFileInput = formModal.querySelector('[data-gallery-input]');
                    if (mainFileInput) {
                        mainFileInput.value = '';
                    }
                    if (galleryFileInput) {
                        galleryFileInput.value = '';
                    }
                    removeImageFlag.value = '0';
                    setRemovedGalleryImages([]);
                    existingGalleryItems = [];
                    selectedGalleryFiles = [];

                    if (car.image_url) {
                        mainImageLocked = true;
                        mainImageInput.disabled = true;
                        mainImagePreview.classList.add('has-image');
                        mainImagePreview.innerHTML = `
                            <button type="button" class="upload-remove-image" data-remove-main-image aria-label="Hapus foto utama">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M18 6 6 18"/>
                                    <path d="m6 6 12 12"/>
                                </svg>
                            </button>
                            <img src="${car.image_url}" alt="Preview foto utama">
                            <div class="upload-image-label">
                                <span>Foto utama saat ini</span>
                                <strong>${car.model ?? 'Mobil'}</strong>
                            </div>
                        `;
                    } else {
                        mainImageLocked = false;
                        mainImageInput.disabled = false;
                        renderMainImagePreview();
                    }

                    const gallery = Array.isArray(car.gallery_urls) ? car.gallery_urls : [];
                    existingGalleryItems = gallery.map((url, index) => ({
                        path: car.gallery_paths?.[index] ?? '',
                        url,
                    }));
                    renderGalleryPreview();

                    openModal();
                };

                const buildDetailHtml = (value) => value || '-';

                const fillDetailModal = (car) => {
                    detailFields.title.textContent = car.model ?? 'Detail Mobil';
                    detailFields.subtitle.textContent = `${car.brand ?? '-'} • ${car.plate_raw ?? car.plate ?? '-'}`;
                    detailFields.brand.textContent = car.brand ?? '-';
                    detailFields.plate.textContent = car.plate_raw ?? car.plate ?? '-';
                    detailFields.status.textContent = `${car.status ?? '-'} (${car.status_note ?? '-'})`;
                    detailFields.rating.textContent = car.rating ?? '-';
                    detailFields.transmission.textContent = car.transmission ?? '-';
                    detailFields.type.textContent = car.type ?? '-';
                    detailFields.year.textContent = car.year ?? '-';
                    detailFields.color.textContent = car.color ?? '-';
                    detailFields.seat.textContent = car.seat ?? '-';
                    detailFields.cc.textContent = car.cc ?? '-';
                    detailFields.price.textContent = `Rp ${car.price_label ?? '-'} / hari`;
                    detailFields.statusNote.textContent = car.status_note ?? '-';
                    detailFields.description.textContent = buildDetailHtml(car.description);

                    if (detailImageShell) {
                        if (car.image_url) {
                            detailImageShell.innerHTML = `
                                <img src="${car.image_url}" alt="${car.model ?? 'Foto mobil'}">
                            `;
                        } else {
                            detailImageShell.innerHTML = `
                                <div style="min-height: 320px; display: grid; place-items: center; text-align: center; padding: 24px; color: #67758b;">
                                    <div>
                                        <div class="upload-badge" style="margin-bottom: 10px;">Foto utama</div>
                                        <div class="upload-title" style="font-size: 16px;">Foto utama belum tersedia</div>
                                        <div class="upload-text" style="margin-top: 6px;">Mobil ini belum memiliki foto utama tersimpan.</div>
                                    </div>
                                </div>
                            `;
                        }
                    }

                    if (detailFields.gallery) {
                        const gallery = Array.isArray(car.gallery_urls) ? car.gallery_urls : [];

                        if (gallery.length === 0) {
                            detailFields.gallery.innerHTML = '<div class="page-subtitle">Tidak ada foto galeri.</div>';
                        } else {
                            const galleryFragment = document.createDocumentFragment();

                            gallery.forEach((url) => {
                                const galleryItem = document.createElement('div');
                                galleryItem.className = 'detail-gallery-item';

                                const image = document.createElement('img');
                                image.src = url;
                                image.alt = 'Galeri mobil';

                                galleryItem.appendChild(image);
                                galleryFragment.appendChild(galleryItem);
                            });

                            detailFields.gallery.replaceChildren(galleryFragment);
                        }
                    }
                };

                const createImageUrl = (file) => URL.createObjectURL(file);

                const createGalleryItemNode = ({
                    src,
                    alt,
                    order,
                    kind,
                    path = '',
                    index = null,
                }) => {
                    const item = document.createElement('div');
                    item.className = 'gallery-item';

                    if (kind === 'existing') {
                        item.dataset.existingGalleryItem = '';
                        item.dataset.galleryPath = path;
                    } else {
                        item.dataset.newGalleryItem = '';
                        if (index !== null) {
                            item.dataset.galleryIndex = String(index);
                        }
                    }

                    const removeButton = document.createElement('button');
                    removeButton.type = 'button';
                    removeButton.className = 'upload-remove-image';
                    removeButton.setAttribute('aria-label', 'Hapus foto pendukung');
                    removeButton.dataset[kind === 'existing' ? 'removeGalleryImage' : 'removeNewGalleryImage'] = '';
                    removeButton.innerHTML = `
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M18 6 6 18"/>
                            <path d="m6 6 12 12"/>
                        </svg>
                    `;

                    const image = document.createElement('img');
                    image.src = src;
                    image.alt = alt;

                    const orderBadge = document.createElement('span');
                    orderBadge.className = 'gallery-order';
                    orderBadge.textContent = String(order);

                    item.append(removeButton, image, orderBadge);
                    return item;
                };

                const renderMainImagePreview = () => {
                    const file = mainImageInput.files?.[0];

                    if (!file) {
                        if (mainImageLocked) {
                            return;
                        }

                        mainImagePreview.classList.remove('has-image');
                        mainImagePreview.innerHTML = `
                            <div class="upload-placeholder">
                                <div class="upload-badge">Foto utama</div>
                                <div class="upload-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <path d="M4 7h4l2-3h4l2 3h4v13H4V7Z"/>
                                        <circle cx="12" cy="13" r="3.5"/>
                                    </svg>
                                </div>
                                <div class="upload-title">Tarik atau klik untuk memilih foto utama</div>
                                <div class="upload-text">Cover image ini akan dipakai di katalog mobil.</div>
                            </div>
                        `;
                        return;
                    }

                    const url = createImageUrl(file);
                    mainImagePreview.classList.add('has-image');
                    mainImagePreview.innerHTML = `
                        <button type="button" class="upload-remove-image" data-remove-main-image aria-label="Hapus foto utama">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M18 6 6 18"/>
                                <path d="m6 6 12 12"/>
                            </svg>
                        </button>
                        <img src="${url}" alt="Preview foto utama">
                        <div class="upload-image-label">
                            <span>Foto utama</span>
                            <strong>${file.name}</strong>
                        </div>
                    `;
                };

                const renderGalleryPreview = () => {
                    const removedGalleryImages = getRemovedGalleryImages();
                    const visibleExisting = existingGalleryItems.filter((item) => item.path && !removedGalleryImages.includes(item.path));
                    const files = selectedGalleryFiles;

                    if (visibleExisting.length === 0 && files.length === 0) {
                        galleryPreview.innerHTML = `
                            <div class="gallery-empty">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <rect x="3" y="4" width="18" height="16" rx="2"/>
                                    <path d="m8 14 2.5-3 3 3 2-2 2.5 3"/>
                                    <circle cx="9" cy="9" r="1.5"/>
                                </svg>
                                <p>Tambahkan foto pendukung di sini.</p>
                            </div>
                        `;
                        return;
                    }

                    const galleryGrid = document.createElement('div');
                    galleryGrid.className = 'gallery-grid';

                    visibleExisting.forEach((item, index) => {
                        galleryGrid.appendChild(createGalleryItemNode({
                            src: item.url,
                            alt: `Galeri ${index + 1}`,
                            order: index + 1,
                            kind: 'existing',
                            path: item.path,
                        }));
                    });

                    files.forEach((file, index) => {
                        galleryGrid.appendChild(createGalleryItemNode({
                            src: createImageUrl(file),
                            alt: `Galeri ${visibleExisting.length + index + 1}`,
                            order: visibleExisting.length + index + 1,
                            kind: 'new',
                            index,
                        }));
                    });

                    galleryPreview.replaceChildren(galleryGrid);
                };

                const syncGalleryInput = () => {
                    const dataTransfer = new DataTransfer();
                    selectedGalleryFiles.forEach((file) => dataTransfer.items.add(file));
                    galleryInput.files = dataTransfer.files;
                };

                const getRemovedGalleryImages = () => {
                    try {
                        const parsed = JSON.parse(removeGalleryFlag.value || '[]');
                        return Array.isArray(parsed) ? parsed : [];
                    } catch {
                        return [];
                    }
                };

                const setRemovedGalleryImages = (paths) => {
                    removeGalleryFlag.value = JSON.stringify(paths);
                };

                const closeAllCarMenus = () => {
                    carMenuRoots.forEach((root) => {
                        const menu = root.querySelector('[data-car-menu]');
                        const toggle = root.querySelector('[data-car-menu-toggle]');

                        if (menu) {
                            menu.hidden = true;
                        }

                        if (toggle) {
                            toggle.setAttribute('aria-expanded', 'false');
                        }
                    });
                };

                carMenuRoots.forEach((root) => {
                    const toggle = root.querySelector('[data-car-menu-toggle]');
                    const menu = root.querySelector('[data-car-menu]');

                    if (!toggle || !menu) {
                        return;
                    }

                    toggle.addEventListener('click', (event) => {
                        event.stopPropagation();

                        const isOpen = !menu.hidden;
                        closeAllCarMenus();
                        menu.hidden = isOpen;
                        toggle.setAttribute('aria-expanded', String(!isOpen));
                    });

                    menu.addEventListener('click', (event) => {
                        event.stopPropagation();
                    });
                });

                const restoreCreateMode = () => {
                    formModal.querySelectorAll('[data-car-field]').forEach((element) => {
                        if (element.type === 'file') {
                            element.value = '';
                            return;
                        }

                        element.value = '';
                    });
                    removeImageFlag.value = '0';
                    setRemovedGalleryImages([]);
                    existingGalleryItems = [];
                    selectedGalleryFiles = [];

                    resetFormMode();
                    renderMainImagePreview();
                    renderGalleryPreview();
                };

                const setFormModeFromErrors = () => {
                    const updateUrl = formElement.dataset.oldUpdateUrl;

                    if (!updateUrl) {
                        resetFormMode();
                        return;
                    }

                    methodInput.disabled = false;
                    methodInput.value = 'PUT';
                    formElement.action = updateUrl;
                    formTitle.textContent = 'Edit Mobil';
                    formSubtitle.textContent = 'Perbarui informasi unit, foto utama, dan galeri pendukung.';
                    submitButton.querySelector('span').textContent = 'Simpan Perubahan';
                };

                openButtons.forEach((button) => {
                    button.addEventListener('click', () => {
                        closeDetailModal();
                        restoreCreateMode();
                        openModal();
                    });
                });

                const openTargets = document.querySelectorAll('[data-open-car-modal]');
                openTargets.forEach((target) => {
                    target.addEventListener('keydown', (event) => {
                        if (event.key === 'Enter' || event.key === ' ') {
                            event.preventDefault();
                            closeDetailModal();
                            restoreCreateMode();
                            openModal();
                        }
                    });
                });

                editButtons.forEach((button) => {
                    button.addEventListener('click', () => {
                        closeDetailModal();
                        const car = JSON.parse(button.dataset.car || '{}');
                        fillCarForm(car, button.dataset.updateUrl || formElement.action);
                    });
                });

                detailButtons.forEach((button) => {
                    button.addEventListener('click', () => {
                        closeFormModal();
                        const car = JSON.parse(button.dataset.car || '{}');
                        fillDetailModal(car);
                        openDetailModal();
                    });
                });

                closeFormButtons.forEach((button) => {
                    button.addEventListener('click', closeFormModal);
                });

                closeDetailButtons.forEach((button) => {
                    button.addEventListener('click', closeDetailModal);
                });

                flashButtons.forEach((button) => {
                    button.addEventListener('click', () => {
                        const flash = button.closest('.flash-banner');
                        if (flash) {
                            flash.remove();
                        }
                    });
                });

                formModal.addEventListener('click', (event) => {
                    if (event.target === formModal) {
                        closeFormModal();
                    }
                });

                detailModal.addEventListener('click', (event) => {
                    if (event.target === detailModal) {
                        closeDetailModal();
                    }
                });

                formPanel.addEventListener('click', (event) => {
                    event.stopPropagation();
                });

                detailPanel.addEventListener('click', (event) => {
                    event.stopPropagation();
                });

                mainImagePreview?.addEventListener('click', (event) => {
                    const removeButton = event.target instanceof Element ? event.target.closest('[data-remove-main-image]') : null;

                    if (!removeButton) {
                        return;
                    }

                    event.preventDefault();
                    event.stopPropagation();
                    mainImageInput.value = '';
                    removeImageFlag.value = '1';
                    mainImageLocked = false;
                    mainImageInput.disabled = false;

                    if (detailModal.classList.contains('is-open')) {
                        return;
                    }

                    renderMainImagePreview();
                });

                mainImageInput?.addEventListener('change', () => {
                    removeImageFlag.value = '0';
                    mainImageLocked = false;
                    renderMainImagePreview();
                });

                galleryInput?.addEventListener('change', () => {
                    const nextFiles = Array.from(galleryInput.files ?? []);
                    selectedGalleryFiles = [...selectedGalleryFiles, ...nextFiles];
                    syncGalleryInput();
                    renderGalleryPreview();
                });

                galleryPreview?.addEventListener('click', (event) => {
                    const removeExistingButton = event.target instanceof Element ? event.target.closest('[data-remove-gallery-image]') : null;
                    const removeNewButton = event.target instanceof Element ? event.target.closest('[data-remove-new-gallery-image]') : null;

                    if (removeExistingButton) {
                        event.preventDefault();
                        event.stopPropagation();

                        const item = removeExistingButton.closest('[data-gallery-path]');
                        const path = item?.getAttribute('data-gallery-path');
                        if (!path) {
                            return;
                        }

                        const removed = getRemovedGalleryImages();
                        if (!removed.includes(path)) {
                            removed.push(path);
                            setRemovedGalleryImages(removed);
                        }

                        item.remove();

                        if (galleryPreview.querySelectorAll('[data-existing-gallery-item], [data-new-gallery-item]').length === 0) {
                            renderGalleryPreview();
                        }
                        return;
                    }

                    if (removeNewButton) {
                        event.preventDefault();
                        event.stopPropagation();

                        const item = removeNewButton.closest('[data-gallery-index]');
                        const index = Number(item?.getAttribute('data-gallery-index'));
                        if (Number.isNaN(index)) {
                            return;
                        }

                        selectedGalleryFiles.splice(index, 1);
                        syncGalleryInput();
                        renderGalleryPreview();
                    }
                });

                document.addEventListener('click', (event) => {
                    if (!(event.target instanceof Element) || !event.target.closest('[data-car-menu-root]')) {
                        closeAllCarMenus();
                    }
                });

                document.addEventListener('keydown', (event) => {
                    if (event.key === 'Escape') {
                        closeFormModal();
                        closeDetailModal();
                        closeAllCarMenus();
                    }
                });

                setFormModeFromErrors();

                if (formModal.classList.contains('is-open')) {
                    document.body.style.overflow = 'hidden';
                }

                if (!formElement.dataset.oldUpdateUrl) {
                    renderMainImagePreview();
                    renderGalleryPreview();
                }
            })();
        </script>
    @endpush
</x-backoffice.layout>
