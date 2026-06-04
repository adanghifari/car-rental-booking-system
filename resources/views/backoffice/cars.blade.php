<x-backoffice.layout title="Manajemen Mobil" :admin="$admin" active="cars" search-placeholder="Cari mobil...">
    <section class="page-head">
        <div>
            <h1 class="page-title">Manajemen Mobil</h1>
            <p class="page-subtitle">Kelola armada mobil rental premium Anda secara efisien.</p>
        </div>

        <a href="#" class="primary-button">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M12 5v14"/>
                <path d="M5 12h14"/>
            </svg>
            <span>Tambah Mobil Baru</span>
        </a>
    </section>

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

    <section class="fleet-grid">
        @foreach ($cars as $car)
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

                    <h2 class="fleet-name">{{ $car['model'] }}</h2>

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
                            <span class="mini-action" title="Edit">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M12 20h9"/>
                                    <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5Z"/>
                                </svg>
                            </span>
                            <span class="mini-action" title="Lihat">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12Z"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>
                            </span>
                        </div>
                    </div>
                </div>
            </article>
        @endforeach

        <article class="card fleet-add-card">
            <div>
                <div class="plus">+</div>
                <strong>Tambah Unit Baru</strong>
                <p class="page-subtitle" style="margin-top: 8px;">Perluas koleksi mobil premium Anda.</p>
            </div>
        </article>
    </section>

    <section class="card maintenance-card">
        <div class="section-head">
            <h2 class="section-title">Ringkasan Maintenance</h2>
            <a href="#" style="color: var(--blue); font-size: 13px; font-weight: 700; text-decoration: none;">Lihat Semua</a>
        </div>

        <div class="maintenance-table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Unit Mobil</th>
                        <th>Status</th>
                        <th>Jadwal Terakhir</th>
                        <th>Kilometer</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($maintenanceRows as $row)
                        <tr>
                            <td>
                                <div class="activity-title">{{ $row['name'] }}</div>
                                <div class="activity-subtitle">{{ $row['plate'] }}</div>
                            </td>
                            <td><span class="pill {{ $row['status_tone'] }}">{{ $row['status'] }}</span></td>
                            <td>{{ $row['last_service'] }}</td>
                            <td>{{ $row['mileage'] }}</td>
                            <td>
                                <span class="mini-action" title="Lihat detail">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12Z"/>
                                        <circle cx="12" cy="12" r="3"/>
                                    </svg>
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <p class="empty-text">Belum ada data armada untuk diringkas.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</x-backoffice.layout>
