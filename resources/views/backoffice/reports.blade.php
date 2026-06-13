<x-backoffice.layout title="Laporan Perusahaan" :admin="$admin" active="reports">
    <section class="page-head">
        <div>
            <h1 class="page-title">{{ $tab === 'overview' ? 'Overview' : 'Laporan Perusahaan' }}</h1>
            <p class="page-subtitle">{{ $tab === 'overview' ? 'Ringkasan performa bisnis secara visual' : 'Rekap data pendapatan, reservasi, dan armada perusahaan.' }}</p>
        </div>
    </section>

    <section class="card" style="margin-bottom: 20px; padding: 12px 14px; border-radius: 16px; border: 1px solid rgba(219, 227, 239, 0.85); background: rgba(255, 255, 255, 0.88); display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 12px;">
        <div style="display: flex; flex-wrap: wrap; gap: 8px; align-items: center;">
            <a href="{{ route('backoffice.reports', ['tab' => 'overview', 'start_date' => $start_date, 'end_date' => $end_date]) }}"
               style="text-decoration: none; cursor: pointer; padding: 10px 20px; border-radius: 12px; font-weight: 700; font-size: 13px; transition: all 0.2s; background: {{ $tab === 'overview' ? 'var(--blue)' : 'transparent' }}; color: {{ $tab === 'overview' ? '#fff' : '#6a748a' }};">
                Overview
            </a>
            <a href="{{ route('backoffice.reports', ['tab' => 'revenue', 'start_date' => $start_date, 'end_date' => $end_date]) }}"
               style="text-decoration: none; cursor: pointer; padding: 10px 20px; border-radius: 12px; font-weight: 700; font-size: 13px; transition: all 0.2s; background: {{ $tab === 'revenue' ? 'var(--blue)' : 'transparent' }}; color: {{ $tab === 'revenue' ? '#fff' : '#6a748a' }};">
                Laporan Pendapatan
            </a>
            <a href="{{ route('backoffice.reports', ['tab' => 'reservation', 'start_date' => $start_date, 'end_date' => $end_date]) }}"
               style="text-decoration: none; cursor: pointer; padding: 10px 20px; border-radius: 12px; font-weight: 700; font-size: 13px; transition: all 0.2s; background: {{ $tab === 'reservation' ? 'var(--blue)' : 'transparent' }}; color: {{ $tab === 'reservation' ? '#fff' : '#6a748a' }};">
                Laporan Reservasi
            </a>
            <a href="{{ route('backoffice.reports', ['tab' => 'fleet', 'start_date' => $start_date, 'end_date' => $end_date]) }}"
               style="text-decoration: none; cursor: pointer; padding: 10px 20px; border-radius: 12px; font-weight: 700; font-size: 13px; transition: all 0.2s; background: {{ $tab === 'fleet' ? 'var(--blue)' : 'transparent' }}; color: {{ $tab === 'fleet' ? '#fff' : '#6a748a' }};">
                Laporan Armada
            </a>
        </div>

        <form method="GET" action="{{ route('backoffice.reports') }}" style="display: flex; flex-wrap: wrap; gap: 10px; align-items: flex-end; justify-content: flex-end; margin-left: auto;">
            <input type="hidden" name="tab" value="{{ $tab }}">
            <div style="display: flex; flex-wrap: wrap; gap: 10px; align-items: flex-end;">
                <div style="display: flex; flex-direction: column; gap: 4px;">
                    <label style="font-size: 10px; font-weight: 800; color: var(--muted); text-transform: uppercase; letter-spacing: 0.08em;">Dari Tanggal</label>
                    <input type="date" name="start_date" value="{{ $start_date }}" style="padding: 9px 12px; border: 1px solid #e2e8f0; border-radius: 10px; font-size: 13px; color: var(--text); background: #fff; outline: none;">
                </div>
                <div style="display: flex; flex-direction: column; gap: 4px;">
                    <label style="font-size: 10px; font-weight: 800; color: var(--muted); text-transform: uppercase; letter-spacing: 0.08em;">Sampai Tanggal</label>
                    <input type="date" name="end_date" value="{{ $end_date }}" style="padding: 9px 12px; border: 1px solid #e2e8f0; border-radius: 10px; font-size: 13px; color: var(--text); background: #fff; outline: none;">
                </div>
                <div style="display: flex; gap: 8px; align-items: flex-end;">
                    <button type="submit" style="background: var(--blue); padding: 10px 16px; border-radius: 10px; border: 0; color: white; font-weight: 700; cursor: pointer; font-size: 13px; transition: opacity 0.2s;">
                        Terapkan Filter
                    </button>
                    <a href="{{ route('backoffice.reports', ['tab' => $tab]) }}" style="display: inline-flex; align-items: center; justify-content: center; text-decoration: none; background: #f1f5f9; padding: 10px 16px; border-radius: 10px; border: 0; color: #475569; font-weight: 700; cursor: pointer; font-size: 13px; transition: background 0.2s;" onmouseover="this.style.background='#e2e8f0'" onmouseout="this.style.background='#f1f5f9'">
                        Reset
                    </a>
                </div>
            </div>

            @if ($tab !== 'overview')
                <a href="{{ route('backoffice.reports', ['tab' => $tab, 'start_date' => $start_date, 'end_date' => $end_date, 'export' => 'csv']) }}" style="background: var(--green); padding: 10px 16px; border-radius: 10px; border: 0; color: white; font-weight: 700; cursor: pointer; font-size: 13px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; transition: opacity 0.2s; white-space: nowrap;" onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink: 0;">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                        <polyline points="7 10 12 15 17 10" />
                        <line x1="12" y1="15" x2="12" y2="3" />
                    </svg>
                    Export CSV
                </a>
            @endif
        </form>
    </section>

    <!-- Summary Metrics & Visual Analytics Charts -->
    @if ($tab === 'overview')
        <style>
            .overview-layout {
                display: grid;
                gap: 14px;
            }

            .overview-kpi-row {
                display: grid;
            }

            .overview-charts {
                display: grid;
                grid-template-columns: minmax(0, 1.35fr) minmax(0, 1fr);
                gap: 14px;
                align-items: stretch;
            }

            .chart-column {
                display: grid;
                gap: 14px;
                align-content: start;
            }

            .overview-card {
                padding: 16px;
                border-radius: 22px;
                height: 100%;
            }

            .kpi-grid {
                display: grid;
                grid-template-columns: repeat(4, minmax(0, 1fr));
                gap: 12px;
            }

            .kpi-card {
                display: flex;
                align-items: stretch;
                justify-content: space-between;
                gap: 12px;
                min-height: 74px;
                padding: 12px 14px;
                border-radius: 16px;
                background: #fff;
                border: 1px solid rgba(226, 232, 240, 0.95);
                box-shadow: 0 8px 22px rgba(15, 23, 42, 0.04);
            }

            .kpi-card.total {
                background: linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(247, 249, 252, 0.98));
            }

            .kpi-card.revenue {
                background: linear-gradient(180deg, rgba(240, 253, 249, 0.98), rgba(236, 253, 245, 0.96));
                border-color: rgba(167, 243, 208, 0.9);
            }

            .kpi-card.avg {
                background: linear-gradient(180deg, rgba(239, 246, 255, 0.98), rgba(243, 248, 255, 0.96));
                border-color: rgba(191, 219, 254, 0.92);
            }

            .kpi-card.success {
                background: linear-gradient(180deg, rgba(255, 251, 235, 0.98), rgba(255, 247, 224, 0.96));
                border-color: rgba(253, 230, 138, 0.92);
            }

            .kpi-copy {
                min-width: 0;
            }

            .kpi-label {
                margin: 0 0 6px;
                font-size: 11px;
                font-weight: 800;
                text-transform: uppercase;
                letter-spacing: 0.08em;
                color: #64748b;
            }

            .kpi-card.revenue .kpi-label {
                color: #047857;
            }

            .kpi-card.avg .kpi-label {
                color: #1d4ed8;
            }

            .kpi-card.success .kpi-label {
                color: #b45309;
            }

            .kpi-value {
                margin: 0;
                font-size: 22px;
                line-height: 1;
                font-weight: 800;
                letter-spacing: -0.04em;
                color: var(--text);
                white-space: nowrap;
            }

            .kpi-graphic {
                width: 64px;
                flex: 0 0 64px;
                display: flex;
                align-items: center;
                justify-content: flex-end;
                opacity: 0.9;
            }

            .kpi-spark {
                width: 64px;
                height: 40px;
            }

            .kpi-expand-grid {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 10px;
            }

            .metric-mini {
                padding: 12px 14px;
                border-radius: 16px;
                background: #fff;
                border: 1px solid rgba(226, 232, 240, 0.9);
            }

            .metric-mini.blue {
                background: rgba(239, 246, 255, 0.98);
                border-color: rgba(191, 219, 254, 0.95);
            }

            .metric-mini.green {
                background: rgba(236, 253, 245, 0.98);
                border-color: rgba(167, 243, 208, 0.95);
            }

            .metric-mini.amber {
                background: rgba(255, 251, 235, 0.98);
                border-color: rgba(253, 230, 138, 0.95);
            }

            .metric-mini .kpi-label {
                margin: 0 0 4px;
                font-size: 10px;
                font-weight: 800;
                text-transform: uppercase;
                letter-spacing: 0.08em;
                color: #64748b;
            }

            .metric-mini .kpi-value {
                margin: 0;
                font-size: 18px;
                font-weight: 800;
                line-height: 1.1;
                color: var(--text);
            }

            .chart-box {
                position: relative;
                width: 100%;
            }

            .chart-box.trend {
                height: 250px;
            }

            .chart-box.status {
                height: 220px;
            }

            .chart-box.service-type {
                height: 220px;
            }

            .chart-box.revenue {
                height: 190px;
            }

            .secondary-stack {
                display: grid;
                gap: 14px;
                align-content: start;
            }

            .topcars-list {
                display: grid;
                gap: 8px;
            }

            .topcars-item {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 10px;
                padding: 10px 12px;
                border-radius: 14px;
                background: rgba(243, 246, 251, 0.9);
            }

            .topcars-name {
                margin: 0;
                font-size: 13px;
                font-weight: 700;
                color: var(--text);
                min-width: 0;
            }

            .topcars-empty {
                margin: 0;
                font-size: 13px;
                color: var(--muted);
            }

            @media (max-width: 1280px) {
                .overview-charts {
                    grid-template-columns: 1fr;
                }

                .kpi-grid {
                    grid-template-columns: repeat(2, minmax(0, 1fr));
                }

                .chart-box.trend {
                    height: 230px;
                }
            }

            @media (max-width: 768px) {
                .kpi-grid {
                    grid-template-columns: 1fr;
                }

                .chart-box.trend,
                .chart-box.status,
                .chart-box.service-type,
                .chart-box.revenue {
                    height: 210px;
                }
            }
        </style>

        <section class="overview-layout">
            <section class="overview-kpi-row">
                <article class="card overview-card">
                    <div class="section-head" style="margin-bottom: 12px;">
                        <h2 class="section-title">KPI Ringkas</h2>
                        <span class="chip" style="border-radius: 999px; padding: 5px 10px; font-size: 10px; font-weight: 700; background: #f0f3f8; color: #6a748a;">4 KPI utama</span>
                    </div>

                    <div class="kpi-grid">
                        <div class="kpi-card total">
                            <div class="kpi-copy">
                                <p class="kpi-label">Total Booking</p>
                                <p class="kpi-value">{{ (int) $overviewSummary['total_rentals'] }}</p>
                            </div>
                            <div class="kpi-graphic" aria-hidden="true">
                                <svg class="kpi-spark" viewBox="0 0 72 48" fill="none">
                                    <path d="M8 38V18M18 38V30M28 38V22M38 38V14M48 38V28M58 38V10" stroke="#cbd5e1" stroke-width="4" stroke-linecap="round"/>
                                    <path d="M58 38V16" stroke="#3f5ed7" stroke-width="4" stroke-linecap="round"/>
                                </svg>
                            </div>
                        </div>

                        <div class="kpi-card revenue">
                            <div class="kpi-copy">
                                <p class="kpi-label">Revenue Paid</p>
                                <p class="kpi-value">Rp {{ number_format((float) $overviewSummary['revenue_paid'], 0, ',', '.') }}</p>
                            </div>
                            <div class="kpi-graphic" aria-hidden="true">
                                <svg class="kpi-spark" viewBox="0 0 72 48" fill="none">
                                    <path d="M6 34C12 24 16 20 22 22C28 24 30 34 36 30C42 26 44 12 50 12C56 12 60 22 66 18" stroke="#94a3b8" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                        </div>

                        <div class="kpi-card avg">
                            <div class="kpi-copy">
                                <p class="kpi-label">Booking Berhasil</p>
                                <p class="kpi-value">{{ (int) $overviewSummary['success_bookings'] }}</p>
                            </div>
                            <div class="kpi-graphic" aria-hidden="true">
                                <svg class="kpi-spark" viewBox="0 0 72 48" fill="none">
                                    <path d="M10 30L22 18L32 24L42 14L54 20L62 10" stroke="#cbd5e1" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                        </div>

                        <div class="kpi-card success">
                            <div class="kpi-copy">
                                <p class="kpi-label">Booking Gagal</p>
                                <p class="kpi-value">{{ (int) $overviewSummary['failed_bookings'] }}</p>
                            </div>
                            <div class="kpi-graphic" aria-hidden="true">
                                <svg class="kpi-spark" viewBox="0 0 72 48" fill="none">
                                    <path d="M10 12V38M22 20V38M34 8V38M46 24V38M58 16V38" stroke="#f5c56b" stroke-width="5" stroke-linecap="round"/>
                                    <path d="M58 16V30" stroke="#d97706" stroke-width="5" stroke-linecap="round"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </article>
            </section>

            <section class="overview-charts">
                <div class="chart-column">
                    <article class="card overview-card">
                    <div class="section-head" style="margin-bottom: 12px;">
                        <h2 class="section-title">Tren 6 Bulan Terakhir</h2>
                        <span class="chip" style="border-radius: 999px; padding: 5px 10px; font-size: 10px; font-weight: 700; background: #f0f3f8; color: #6a748a;">Grafik utama</span>
                    </div>
                    <div class="chart-box trend">
                        <canvas id="overview-bookings-chart"></canvas>
                    </div>
                    </article>

                    <article class="card overview-card">
                        <div class="section-head" style="margin-bottom: 12px;">
                            <h2 class="section-title">Revenue Paid</h2>
                        </div>
                        <div class="chart-box revenue">
                            <canvas id="overview-revenue-chart"></canvas>
                        </div>
                    </article>
                </div>

                <div class="chart-column">
                    <article class="card overview-card">
                        <div class="section-head" style="margin-bottom: 12px;">
                            <h2 class="section-title">Distribusi Status Rental</h2>
                        </div>
                        <div class="chart-box status">
                            <canvas id="overview-status-chart"></canvas>
                        </div>
                    </article>

                    <article class="card overview-card">
                        <div class="section-head" style="margin-bottom: 12px;">
                            <h2 class="section-title">Booking Berdasarkan Tipe Layanan</h2>
                        </div>
                        <div class="chart-box service-type">
                            <canvas id="overview-service-type-chart"></canvas>
                        </div>
                    </article>
                </div>
            </section>

            <section class="overview-charts">
                <article class="card overview-card">
                    <div class="section-head" style="margin-bottom: 12px;">
                        <h2 class="section-title">Top Armada Terpopuler</h2>
                    </div>
                    <div class="topcars-list">
                        @forelse ($topCars as $car)
                            <div class="topcars-item">
                                <p class="topcars-name">{{ $car['name'] }}</p>
                                <span class="text-xs font-bold rounded-full px-2 py-1 bg-slate-200 text-slate-700" style="white-space: nowrap;">{{ $car['count'] }}x</span>
                            </div>
                        @empty
                            <p class="topcars-empty">Belum ada transaksi dalam periode ini.</p>
                        @endforelse
                    </div>
                </article>

                <article class="card overview-card">
                    <div class="section-head" style="margin-bottom: 12px;">
                        <h2 class="section-title">Status Ketersediaan Armada</h2>
                    </div>
                    <div class="kpi-expand-grid" style="grid-template-columns: repeat(3, minmax(0, 1fr));">
                        <div class="metric-mini">
                            <p class="kpi-label">Total</p>
                            <p class="kpi-value">{{ (int) $fleetOccupancy['total'] }}</p>
                        </div>
                        <div class="metric-mini green">
                            <p class="kpi-label">Tersedia</p>
                            <p class="kpi-value">{{ (int) $fleetOccupancy['available'] }}</p>
                        </div>
                        <div class="metric-mini" style="background: rgba(254, 242, 242, 0.98); border-color: rgba(254, 202, 202, 0.95);">
                            <p class="kpi-label" style="color: #b91c1c;">Sibuk</p>
                            <p class="kpi-value" style="color: #b91c1c;">{{ (int) $fleetOccupancy['unavailable'] }}</p>
                        </div>
                    </div>
                </article>
            </section>
        </section>
    @elseif ($tab === 'revenue')
        <section style="display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 18px; margin-bottom: 24px;">
            <div class="card" style="display: flex; align-items: center; gap: 16px; padding: 22px 24px; border: 1px solid rgba(219, 227, 239, 0.85); background: rgba(255, 255, 255, 0.88);">
                <div style="width: 54px; height: 54px; border-radius: 16px; display: grid; place-items: center; background: rgba(29, 187, 132, 0.12); color: var(--green); flex: 0 0 auto;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="12" y1="1" x2="12" y2="23"></line>
                        <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                    </svg>
                </div>
                <div>
                    <div class="page-subtitle" style="margin-bottom: 6px; font-weight: 700; text-transform: uppercase; letter-spacing: .04em;">Total Pendapatan</div>
                    <div class="page-title" style="font-size: 28px; margin: 0; line-height: 1;">Rp {{ number_format($summary['total_revenue'] ?? 0, 0, ',', '.') }}</div>
                </div>
            </div>

            <div class="card" style="display: flex; align-items: center; gap: 16px; padding: 22px 24px; border: 1px solid rgba(219, 227, 239, 0.85); background: rgba(255, 255, 255, 0.88);">
                <div style="width: 54px; height: 54px; border-radius: 16px; display: grid; place-items: center; background: rgba(63, 94, 215, 0.12); color: var(--blue); flex: 0 0 auto;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                        <line x1="16" y1="13" x2="8" y2="13"></line>
                        <line x1="16" y1="17" x2="8" y2="17"></line>
                        <polyline points="10 9 9 9 8 9"></polyline>
                    </svg>
                </div>
                <div>
                    <div class="page-subtitle" style="margin-bottom: 6px; font-weight: 700; text-transform: uppercase; letter-spacing: .04em;">Transaksi Lunas</div>
                    <div class="page-title" style="font-size: 28px; margin: 0; line-height: 1;">{{ number_format($summary['total_transactions'] ?? 0) }}</div>
                </div>
            </div>

            <div class="card" style="display: flex; align-items: center; gap: 16px; padding: 22px 24px; border: 1px solid rgba(219, 227, 239, 0.85); background: rgba(255, 255, 255, 0.88);">
                <div style="width: 54px; height: 54px; border-radius: 16px; display: grid; place-items: center; background: rgba(245, 158, 11, 0.12); color: var(--amber); flex: 0 0 auto;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                    </svg>
                </div>
                <div>
                    <div class="page-subtitle" style="margin-bottom: 6px; font-weight: 700; text-transform: uppercase; letter-spacing: .04em;">Rata-rata Nilai Transaksi</div>
                    <div class="page-title" style="font-size: 28px; margin: 0; line-height: 1;">Rp {{ number_format($summary['avg_transaction'] ?? 0, 0, ',', '.') }}</div>
                </div>
            </div>
        </section>

        <!-- Revenue Trend Bar Chart (Chart.js) -->
        <section class="card" style="margin-bottom: 24px; padding: 22px 24px;">
            <div class="section-head" style="margin-bottom: 16px;">
                <h2 class="section-title">Pendapatan Bulanan (Tren Histori)</h2>
                <span class="chip" style="border-radius: 999px; padding: 6px 12px; font-size: 11px; font-weight: 700; background: #f0f3f8; color: #6a748a;">6 Bulan Terakhir</span>
            </div>

            <div style="height: 280px; position: relative; width: 100%;">
                <canvas id="revenue-chart"></canvas>
            </div>
        </section>

    @elseif ($tab === 'reservation')
        <section style="display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 18px; margin-bottom: 24px;">
            <div class="card" style="display: flex; align-items: center; gap: 16px; padding: 22px 24px; border: 1px solid rgba(219, 227, 239, 0.85); background: rgba(255, 255, 255, 0.88);">
                <div style="width: 54px; height: 54px; border-radius: 16px; display: grid; place-items: center; background: rgba(63, 94, 215, 0.10); color: var(--blue); flex: 0 0 auto;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M4 7h16v10H4z" />
                        <path d="M7 7V5h10v2" />
                    </svg>
                </div>
                <div>
                    <div class="page-subtitle" style="margin-bottom: 6px; font-weight: 700; text-transform: uppercase; letter-spacing: .04em;">Total Booking</div>
                    <div class="page-title" style="font-size: 28px; margin: 0; line-height: 1;">{{ number_format($summary['total_reservations'] ?? 0) }}</div>
                </div>
            </div>

            <div class="card" style="display: flex; align-items: center; gap: 16px; padding: 22px 24px; border: 1px solid rgba(219, 227, 239, 0.85); background: rgba(255, 255, 255, 0.88);">
                <div style="width: 54px; height: 54px; border-radius: 16px; display: grid; place-items: center; background: rgba(29, 187, 132, 0.12); color: var(--green); flex: 0 0 auto;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="8" />
                        <path d="m9.5 12 1.8 1.8L15 10.2" />
                    </svg>
                </div>
                <div>
                    <div class="page-subtitle" style="margin-bottom: 6px; font-weight: 700; text-transform: uppercase; letter-spacing: .04em;">Selesai / Aktif</div>
                    <div class="page-title" style="font-size: 28px; margin: 0; line-height: 1;">{{ number_format($summary['returned'] + $summary['ongoing']) }}</div>
                </div>
            </div>

            <div class="card" style="display: flex; align-items: center; gap: 16px; padding: 22px 24px; border: 1px solid rgba(219, 227, 239, 0.85); background: rgba(255, 255, 255, 0.88);">
                <div style="width: 54px; height: 54px; border-radius: 16px; display: grid; place-items: center; background: rgba(239, 68, 68, 0.12); color: var(--red); flex: 0 0 auto;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z" />
                        <path d="M15 9l-6 6M9 9l6 6" />
                    </svg>
                </div>
                <div>
                    <div class="page-subtitle" style="margin-bottom: 6px; font-weight: 700; text-transform: uppercase; letter-spacing: .04em;">Batal / Expired</div>
                    <div class="page-title" style="font-size: 28px; margin: 0; line-height: 1;">{{ number_format($summary['cancelled'] + $summary['expired']) }} ({{ $summary['cancellation_rate'] }}%)</div>
                </div>
            </div>

            <div class="card" style="display: flex; align-items: center; gap: 16px; padding: 22px 24px; border: 1px solid rgba(219, 227, 239, 0.85); background: rgba(255, 255, 255, 0.88);">
                <div style="width: 54px; height: 54px; border-radius: 16px; display: grid; place-items: center; background: rgba(245, 158, 11, 0.12); color: var(--amber); flex: 0 0 auto;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                </div>
                <div>
                    <div class="page-subtitle" style="margin-bottom: 6px; font-weight: 700; text-transform: uppercase; letter-spacing: .04em;">Rata-rata Durasi</div>
                    <div class="page-title" style="font-size: 28px; margin: 0; line-height: 1;">{{ $summary['avg_duration'] }} Hari</div>
                </div>
            </div>
        </section>

        <!-- Booking Trend Line Chart (Chart.js) -->
        <section class="card" style="margin-bottom: 24px; padding: 22px 24px;">
            <div class="section-head" style="margin-bottom: 16px;">
                <h2 class="section-title">Penyewaan per Bulan (Tren Histori)</h2>
                <span class="chip" style="border-radius: 999px; padding: 6px 12px; font-size: 11px; font-weight: 700; background: #f0f3f8; color: #6a748a;">6 Bulan Terakhir</span>
            </div>

            <div style="height: 280px; position: relative; width: 100%;">
                <canvas id="bookings-chart"></canvas>
            </div>
        </section>

    @elseif ($tab === 'fleet')
        <section style="display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 18px; margin-bottom: 24px;">
            <div class="card" style="display: flex; align-items: center; gap: 16px; padding: 22px 24px; border: 1px solid rgba(219, 227, 239, 0.85); background: rgba(255, 255, 255, 0.88);">
                <div style="width: 54px; height: 54px; border-radius: 16px; display: grid; place-items: center; background: rgba(63, 94, 215, 0.10); color: var(--blue); flex: 0 0 auto;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 16H9m10 0h2m-7 0h1m-9 0h1m0 0a2 2 0 1 0 4 0m-4 0a2 2 0 1 1 4 0m8 0a2 2 0 1 0 4 0m-4 0a2 2 0 1 1 4 0M3 12l2-5h13l3 5"/>
                    </svg>
                </div>
                <div>
                    <div class="page-subtitle" style="margin-bottom: 6px; font-weight: 700; text-transform: uppercase; letter-spacing: .04em;">Total Armada</div>
                    <div class="page-title" style="font-size: 28px; margin: 0; line-height: 1;">{{ number_format($summary['total_fleet'] ?? 0) }}</div>
                </div>
            </div>

            <div class="card" style="display: flex; align-items: center; gap: 16px; padding: 22px 24px; border: 1px solid rgba(219, 227, 239, 0.85); background: rgba(255, 255, 255, 0.88);">
                <div style="width: 54px; height: 54px; border-radius: 16px; display: grid; place-items: center; background: rgba(29, 187, 132, 0.12); color: var(--green); flex: 0 0 auto;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10" />
                        <path d="m9 12 2 2 4-4" />
                    </svg>
                </div>
                <div>
                    <div class="page-subtitle" style="margin-bottom: 6px; font-weight: 700; text-transform: uppercase; letter-spacing: .04em;">Mobil Tersedia / Sibuk</div>
                    <div class="page-title" style="font-size: 28px; margin: 0; line-height: 1;">{{ $summary['available'] }} / {{ $summary['unavailable'] }}</div>
                </div>
            </div>

            <div class="card" style="display: flex; align-items: center; gap: 16px; padding: 22px 24px; border: 1px solid rgba(219, 227, 239, 0.85); background: rgba(255, 255, 255, 0.88); grid-column: span 2;">
                <div style="width: 54px; height: 54px; border-radius: 16px; display: grid; place-items: center; background: rgba(245, 158, 11, 0.12); color: var(--amber); flex: 0 0 auto;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                    </svg>
                </div>
                <div>
                    <div class="page-subtitle" style="margin-bottom: 6px; font-weight: 700; text-transform: uppercase; letter-spacing: .04em;">Performa Tertinggi</div>
                    <div style="font-size: 13px; color: var(--text); line-height: 1.4;">
                        <strong>Sewa Terbanyak:</strong> {{ $summary['top_rented'] }}<br>
                        <strong>Pendapatan Terbesar:</strong> {{ $summary['top_revenue'] }}
                    </div>
                </div>
            </div>
        </section>

        <!-- Most Popular Fleet Card -->
        <section class="card featured-card" style="margin-bottom: 24px;">
            <div class="featured-layout">
                <div>
                    <span class="eyebrow">Most Popular Fleet</span>
                    <h2 class="featured-title">{{ $featuredCar['name'] }}</h2>
                    <p class="featured-text">{{ $featuredCar['description'] }}</p>

                    <div class="featured-metrics">
                        <div>
                            <div class="featured-label">Revenue</div>
                            <strong>Rp {{ number_format($featuredCar['revenue'], 0, ',', '.') }}</strong>
                        </div>
                        <div>
                            <div class="featured-label">Rental Count</div>
                            <strong>{{ $featuredCar['rentals_count'] }} Kali</strong>
                        </div>
                    </div>
                </div>

                <div class="vehicle-stage">
                    <svg viewBox="0 0 560 320" aria-hidden="true">
                        <defs>
                            <linearGradient id="carPaint" x1="0" y1="0" x2="1" y2="1">
                                <stop offset="0%" stop-color="#121922" />
                                <stop offset="55%" stop-color="#3b495a" />
                                <stop offset="100%" stop-color="#111827" />
                            </linearGradient>
                        </defs>
                        <rect x="0" y="0" width="560" height="320" fill="transparent" />
                        <ellipse cx="280" cy="220" rx="200" ry="28" fill="rgba(0, 0, 0, 0.18)" />
                        <g transform="translate(72 118)">
                            <path
                                d="M35 88c0-16 11-29 26-32l41-9c18-33 53-54 94-54h74c29 0 57 10 79 27l41 31h41c19 0 35 16 35 35v37c0 9-7 16-16 16h-18c-3-25-24-44-50-44-25 0-46 19-49 44H143c-3-25-24-44-49-44-26 0-47 19-50 44H35Z"
                                fill="url(#carPaint)" />
                            <path d="M125 45c17-24 40-36 70-36h65c20 0 39 7 54 19l38 29H111Z"
                                fill="rgba(228, 236, 249, 0.82)" />
                            <path d="M154 48h58v-27h-33c-11 0-21 9-25 27Z" fill="rgba(175, 192, 220, 0.7)" />
                            <path d="M227 21h42c14 0 28 5 39 13l20 14h-101Z" fill="rgba(175, 192, 220, 0.7)" />
                            <circle cx="93" cy="129" r="35" fill="#111827" />
                            <circle cx="93" cy="129" r="21" fill="#cbd5e1" />
                            <circle cx="388" cy="129" r="35" fill="#111827" />
                            <circle cx="388" cy="129" r="21" fill="#cbd5e1" />
                            <circle cx="93" cy="129" r="8" fill="#94a3b8" />
                            <circle cx="388" cy="129" r="8" fill="#94a3b8" />
                            <rect x="455" y="78" width="20" height="10" rx="5" fill="#f8fafc" />
                            <rect x="26" y="83" width="14" height="10" rx="4" fill="#f59e0b" />
                        </g>
                        <rect x="26" y="250" width="508" height="44" rx="14" fill="rgba(255, 255, 255, 0.9)" />
                        <text x="48" y="276" fill="#111827" font-size="22" font-weight="700">Premium Fleet</text>
                        <text x="454" y="276" fill="#111827" font-size="18" font-weight="700"
                            text-anchor="end">{{ $summary['total_fleet'] ?? 0 }} Units</text>
                    </svg>
                </div>
            </div>
        </section>
    @endif

    <!-- Data Table Card (Omitted for Overview tab) -->
    @if ($tab !== 'overview')
        <section class="card" style="border: 1px solid rgba(219, 227, 239, 0.85); background: rgba(255, 255, 255, 0.88); border-radius: 16px; padding: 20px;">
            <div style="overflow-x: auto;">
                @if($data->isEmpty())
                    <div style="text-align: center; padding: 48px 24px; color: var(--muted);">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="margin-bottom: 16px; opacity: 0.5;">
                            <circle cx="12" cy="12" r="10" />
                            <line x1="12" y1="8" x2="12" y2="12" />
                            <line x1="12" y1="16" x2="12.01" y2="16" />
                        </svg>
                        <p style="margin: 0; font-size: 16px; font-weight: 700; color: var(--text);">Data Tidak Ditemukan</p>
                        <p style="margin: 4px 0 0 0; font-size: 13px;">Tidak ada transaksi atau aktivitas dalam rentang tanggal yang dipilih.</p>
                    </div>
                @else
                    <table class="table" style="width: 100%; border-collapse: collapse;">
                        <thead>
                            @if ($tab === 'revenue')
                                <tr>
                                    <th>Tanggal Pembayaran</th>
                                    <th>Customer</th>
                                    <th>Mobil</th>
                                    <th>Plat Nomor</th>
                                    <th>Tipe Rental</th>
                                    <th>Provider</th>
                                    <th>Status</th>
                                    <th style="text-align: right;">Amount</th>
                                </tr>
                            @elseif ($tab === 'reservation')
                                <tr>
                                    <th>Tanggal Booking</th>
                                    <th>Customer</th>
                                    <th>Mobil</th>
                                    <th>Plat Nomor</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Returned At</th>
                                    <th>Tipe</th>
                                    <th>Verifikasi</th>
                                    <th>Status</th>
                                    <th style="text-align: right;">Total Price</th>
                                </tr>
                            @elseif ($tab === 'fleet')
                                <tr>
                                    <th>Brand & Model</th>
                                    <th>Plat Nomor</th>
                                    <th>Tipe</th>
                                    <th>Transmisi</th>
                                    <th>Status Mobil</th>
                                    <th style="text-align: center;">Jumlah Sewa</th>
                                    <th style="text-align: right;">Total Pendapatan</th>
                                    <th>Terakhir Disewa</th>
                                </tr>
                            @endif
                        </thead>
                        <tbody>
                            @if ($tab === 'revenue')
                                @foreach ($data as $history)
                                    <tr>
                                        <td>{{ $history->created_at->format('d M Y H:i') }}</td>
                                        <td style="font-weight: 600;">{{ $history->rental?->user?->name ?? '-' }}</td>
                                        <td>{{ trim(($history->rental?->car?->brand ?? '') . ' ' . ($history->rental?->car?->name ?? '')) }}</td>
                                        <td><code>{{ $history->rental?->car?->license_plate ?? '-' }}</code></td>
                                        <td>{{ $history->rental?->type === \App\Enums\RentalType::SELF_DRIVE ? 'Self Drive' : 'With Driver' }}</td>
                                        <td>{{ strtoupper($history->provider ?? '-') }}</td>
                                        <td><span class="pill green">Lunas</span></td>
                                        <td style="text-align: right; font-weight: 700;">Rp {{ number_format($history->amount, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            @elseif ($tab === 'reservation')
                                @foreach ($data as $rental)
                                    <tr>
                                        <td>{{ $rental->created_at->format('d M Y H:i') }}</td>
                                        <td style="font-weight: 600;">{{ $rental->user?->name ?? '-' }}</td>
                                        <td>{{ trim(($rental->car?->brand ?? '') . ' ' . ($rental->car?->name ?? '')) }}</td>
                                        <td><code>{{ $rental->car?->license_plate ?? '-' }}</code></td>
                                        <td>{{ $rental->start_date?->format('d M Y') ?? '-' }}</td>
                                        <td>{{ $rental->end_date?->format('d M Y') ?? '-' }}</td>
                                        <td>{{ $rental->returned_at ? $rental->returned_at->format('d M Y H:i') : '-' }}</td>
                                        <td>{{ $rental->type === \App\Enums\RentalType::SELF_DRIVE ? 'Self Drive' : 'With Driver' }}</td>
                                        <td>
                                            @if ($rental->verification_status === \App\Enums\VerificationStatus::VERIFIED)
                                                <span class="pill green" style="padding: 2px 6px; font-size: 10px;">Lolos</span>
                                            @elseif ($rental->verification_status === \App\Enums\VerificationStatus::NEEDS_REVIEW)
                                                <span class="pill amber" style="padding: 2px 6px; font-size: 10px;">Review</span>
                                            @elseif ($rental->verification_status === \App\Enums\VerificationStatus::REJECTED)
                                                <span class="pill red" style="background: rgba(239, 68, 68, 0.12); color: var(--red); padding: 2px 6px; font-size: 10px;">Ditolak</span>
                                            @else
                                                <span class="pill" style="background: rgba(226, 232, 240, 0.6); color: #64748b; padding: 2px 6px; font-size: 10px;">Belum</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($rental->status === \App\Enums\RentalStatus::RETURNED)
                                                <span class="pill blue" style="padding: 2px 6px; font-size: 10px;">Selesai</span>
                                            @elseif ($rental->status === \App\Enums\RentalStatus::ONGOING)
                                                <span class="pill green" style="padding: 2px 6px; font-size: 10px;">Aktif</span>
                                            @elseif ($rental->status === \App\Enums\RentalStatus::CANCELLED)
                                                <span class="pill" style="background: rgba(226, 232, 240, 0.6); color: #64748b; padding: 2px 6px; font-size: 10px;">Batal</span>
                                            @else
                                                <span class="pill amber" style="padding: 2px 6px; font-size: 10px;">Pending</span>
                                            @endif
                                        </td>
                                        <td style="text-align: right; font-weight: 700;">Rp {{ number_format($rental->total_price, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            @elseif ($tab === 'fleet')
                                @foreach ($data as $car)
                                    <tr>
                                        <td style="font-weight: 600;">{{ trim($car['brand'] . ' ' . $car['name']) }}</td>
                                        <td><code>{{ $car['license_plate'] }}</code></td>
                                        <td>{{ str($car['vehicle_type'])->headline() }}</td>
                                        <td>{{ str($car['transmission'])->headline() }}</td>
                                        <td>
                                            @if ($car['status'] === 'available')
                                                <span class="pill green" style="padding: 2px 6px; font-size: 10px;">Tersedia</span>
                                            @else
                                                <span class="pill" style="background: rgba(226, 232, 240, 0.6); color: #64748b; padding: 2px 6px; font-size: 10px;">Sibuk</span>
                                            @endif
                                        </td>
                                        <td style="text-align: center; font-weight: 700;">{{ $car['rentals_count'] }}x</td>
                                        <td style="text-align: right; font-weight: 700; color: var(--green);">Rp {{ number_format($car['total_revenue'], 0, ',', '.') }}</td>
                                        <td>{{ $car['last_rented'] }}</td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                @endif
            </div>
        </section>

        <!-- Pagination -->
        @if ($data && $data->hasPages())
            <section class="card table-card" style="margin-top: 16px; border: 1px solid rgba(219, 227, 239, 0.85); background: rgba(255, 255, 255, 0.88); border-radius: 16px; padding: 12px 20px;">
                <div class="table-footer" style="border-top: 0; display:flex; justify-content:space-between; align-items:center;">
                    <span style="font-size: 13px; color: var(--muted);">Menampilkan {{ $data->firstItem() }} - {{ $data->lastItem() }} dari {{ $data->total() }} data</span>
                    <div class="pagination" style="display: flex; gap: 4px;">
                        @if ($data->onFirstPage())
                            <span class="page-link muted" style="padding: 6px 12px; border-radius: 8px; font-weight: 700; color: #b5bfce;">‹</span>
                        @else
                            <a href="{{ $data->previousPageUrl() }}" class="page-link" style="padding: 6px 12px; border-radius: 8px; font-weight: 700; text-decoration: none; color: var(--blue);">‹</a>
                        @endif

                        @foreach ($data->links()->elements[0] ?? [] as $page => $url)
                            @if ($page == $data->currentPage())
                                <span class="page-link active" style="padding: 6px 12px; border-radius: 8px; font-weight: 700; background: var(--blue); color: #fff;">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" class="page-link" style="padding: 6px 12px; border-radius: 8px; font-weight: 700; text-decoration: none; color: var(--blue);">{{ $page }}</a>
                            @endif
                        @endforeach

                        @if ($data->hasMorePages())
                            <a href="{{ $data->nextPageUrl() }}" class="page-link" style="padding: 6px 12px; border-radius: 8px; font-weight: 700; text-decoration: none; color: var(--blue);">›</a>
                        @else
                            <span class="page-link muted" style="padding: 6px 12px; border-radius: 8px; font-weight: 700; color: #b5bfce;">›</span>
                        @endif
                    </div>
                </div>
            </section>
        @endif
    @endif

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if ($tab === 'overview')
                // 1. Overview Bookings Line Chart
                const overviewBookingsCtx = document.getElementById('overview-bookings-chart');
                if (overviewBookingsCtx) {
                    new Chart(overviewBookingsCtx, {
                        type: 'line',
                        data: {
                            labels: @json($chartRentals->pluck('label')),
                            datasets: [{
                                label: 'Booking',
                                data: @json($chartRentals->pluck('value')),
                                borderColor: '#3f5ed7',
                                backgroundColor: 'rgba(63, 94, 215, 0.08)',
                                fill: true,
                                tension: 0.35,
                                pointRadius: 4,
                                pointBackgroundColor: '#3f5ed7',
                                borderWidth: 3,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: { legend: { display: false } },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    grid: { color: 'rgba(226, 232, 240, 0.6)', drawBorder: false },
                                    ticks: { color: '#64748b', font: { size: 10 }, stepSize: 1 }
                                },
                                x: {
                                    grid: { display: false },
                                    ticks: { color: '#64748b', font: { size: 10 } }
                                }
                            }
                        }
                    });
                }

                // 2. Overview Revenue Bar Chart
                const overviewRevenueCtx = document.getElementById('overview-revenue-chart');
                if (overviewRevenueCtx) {
                    new Chart(overviewRevenueCtx, {
                        type: 'bar',
                        data: {
                            labels: @json($chartRevenue->pluck('label')),
                            datasets: [{
                                label: 'Pendapatan',
                                data: @json($chartRevenue->pluck('value')),
                                backgroundColor: 'rgba(29, 187, 132, 0.75)',
                                borderRadius: 6,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: { legend: { display: false } },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    grid: { color: 'rgba(226, 232, 240, 0.6)', drawBorder: false },
                                    ticks: {
                                        color: '#64748b',
                                        font: { size: 10 },
                                        callback: function(value) {
                                            return 'Rp' + value.toLocaleString('id-ID');
                                        }
                                    }
                                },
                                x: {
                                    grid: { display: false },
                                    ticks: { color: '#64748b', font: { size: 10 } }
                                }
                            }
                        }
                    });
                }

                // 3. Overview Status Distribution Doughnut Chart
                const overviewStatusCtx = document.getElementById('overview-status-chart');
                if (overviewStatusCtx) {
                    new Chart(overviewStatusCtx, {
                        type: 'doughnut',
                        data: {
                            labels: @json($statusDistribution->pluck('label')),
                            datasets: [{
                                data: @json($statusDistribution->pluck('value')),
                                backgroundColor: ['#818cf8', '#f59e0b', '#3b82f6', '#10b981', '#ef4444', '#94a3b8'],
                                borderWidth: 0,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        boxWidth: 8,
                                        boxHeight: 8,
                                        font: { size: 10, family: "'Instrument Sans', sans-serif" },
                                        color: '#64748b'
                                    }
                                }
                            }
                        }
                    });
                }

                // 4. Overview Service Type Distribution Doughnut Chart
                const overviewServiceTypeCtx = document.getElementById('overview-service-type-chart');
                if (overviewServiceTypeCtx) {
                    new Chart(overviewServiceTypeCtx, {
                        type: 'doughnut',
                        data: {
                            labels: @json($serviceTypeDistribution->pluck('label')),
                            datasets: [{
                                data: @json($serviceTypeDistribution->pluck('value')),
                                backgroundColor: ['#3f5ed7', '#1dbb84'],
                                borderWidth: 0,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        boxWidth: 8,
                                        boxHeight: 8,
                                        font: { size: 10, family: "'Instrument Sans', sans-serif" },
                                        color: '#64748b'
                                    }
                                }
                            }
                        }
                    });
                }
            @elseif ($tab === 'revenue')
                const revenueCtx = document.getElementById('revenue-chart');
                if (revenueCtx) {
                    new Chart(revenueCtx, {
                        type: 'bar',
                        data: {
                            labels: @json($chartRevenue->pluck('label')),
                            datasets: [{
                                label: 'Pendapatan',
                                data: @json($chartRevenue->pluck('value')),
                                backgroundColor: 'rgba(29, 187, 132, 0.75)',
                                borderColor: 'rgba(29, 187, 132, 1)',
                                borderWidth: 0,
                                borderRadius: 8,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    grid: {
                                        color: 'rgba(226, 232, 240, 0.6)',
                                        drawBorder: false
                                    },
                                    ticks: {
                                        color: '#64748b',
                                        font: {
                                            family: "'Instrument Sans', sans-serif",
                                            size: 11
                                        },
                                        callback: function(value) {
                                            return 'Rp ' + value.toLocaleString('id-ID');
                                        }
                                    }
                                },
                                x: {
                                    grid: {
                                        display: false
                                    },
                                    ticks: {
                                        color: '#64748b',
                                        font: {
                                            family: "'Instrument Sans', sans-serif",
                                            size: 11
                                        }
                                    }
                                }
                            }
                        }
                    });
                }
            @elseif ($tab === 'reservation')
                const bookingsCtx = document.getElementById('bookings-chart');
                if (bookingsCtx) {
                    new Chart(bookingsCtx, {
                        type: 'line',
                        data: {
                            labels: @json($chartRentals->pluck('label')),
                            datasets: [{
                                label: 'Booking',
                                data: @json($chartRentals->pluck('value')),
                                borderColor: '#3f5ed7',
                                backgroundColor: 'rgba(63, 94, 215, 0.08)',
                                fill: true,
                                tension: 0.35,
                                pointRadius: 4,
                                pointBackgroundColor: '#3f5ed7',
                                borderWidth: 3,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    grid: {
                                        color: 'rgba(226, 232, 240, 0.6)',
                                        drawBorder: false
                                    },
                                    ticks: {
                                        color: '#64748b',
                                        font: {
                                            family: "'Instrument Sans', sans-serif",
                                            size: 11
                                        },
                                        stepSize: 1
                                    }
                                },
                                x: {
                                    grid: {
                                        display: false
                                    },
                                    ticks: {
                                        color: '#64748b',
                                        font: {
                                            family: "'Instrument Sans', sans-serif",
                                            size: 11
                                        }
                                    }
                                }
                            }
                        }
                    });
                }
            @endif
        });
    </script>
</x-backoffice.layout>
