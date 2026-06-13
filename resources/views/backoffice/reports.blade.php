<x-backoffice.layout title="Laporan Perusahaan" :admin="$admin" active="reports">
    @php
        $reportTabLabels = [
            'overview' => 'Overview',
            'revenue' => 'Laporan Pendapatan',
            'reservation' => 'Laporan Reservasi',
            'fleet' => 'Laporan Armada',
        ];

        $reportMonths = [
            '01' => 'Januari',
            '02' => 'Februari',
            '03' => 'Maret',
            '04' => 'April',
            '05' => 'Mei',
            '06' => 'Juni',
            '07' => 'Juli',
            '08' => 'Agustus',
            '09' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember',
        ];

        $reportYears = range((int) now()->format('Y'), (int) now()->format('Y') - 5);
    @endphp

    <style>
        .report-switcher-trigger-line {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding-bottom: 2px;
            border-bottom: 1px solid transparent;
            transition: border-color 0.18s ease, color 0.18s ease;
        }

        .report-switcher-trigger-main {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 2px 6px 3px;
            border-radius: 12px;
            border-bottom: 1px solid transparent;
            transition: border-color 0.18s ease, color 0.18s ease, background 0.18s ease;
        }

        .report-switcher-trigger-icon-wrap {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 26px;
            height: 26px;
            border-radius: 9px;
            background: #0b1b4d;
            flex: 0 0 26px;
            box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.06);
        }

        .report-switcher-mode {
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: #0b1b4d;
        }

        .report-switcher-trigger:hover .report-switcher-trigger-main {
            border-bottom-color: rgba(11, 27, 77, 0.48);
        }

        .report-switcher-trigger:hover .report-switcher-trigger-icon {
            transform: translateY(1px);
        }

        .report-switcher-summary:hover {
            cursor: pointer;
        }

        .report-filter-panel {
            min-width: min(100%, 760px);
            display: flex;
            justify-content: flex-end;
        }

        .report-filter-row {
            display: flex;
            align-items: flex-end;
            justify-content: flex-end;
            gap: 8px;
            flex-wrap: wrap;
            width: fit-content;
            margin-left: auto;
        }

        .report-filter-group {
            display: grid;
            gap: 4px;
            align-content: start;
            min-width: 0;
        }

        .report-filter-mode {
            width: 260px;
            position: relative;
        }

        .report-filter-detail {
            display: flex;
            align-items: flex-end;
            gap: 8px;
            flex-wrap: wrap;
            justify-content: flex-end;
            width: fit-content;
        }

        .report-filter-detail.is-hidden {
            display: none;
        }

        .report-filter-actions {
            display: flex;
            align-items: flex-end;
            justify-content: flex-end;
            gap: 8px;
            flex-wrap: wrap;
        }

        .report-filter-group {
            display: grid;
            gap: 4px;
        }

        .report-filter-label {
            font-size: 10px;
            font-weight: 800;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .report-filter-control {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #dbe3ef;
            border-radius: 12px;
            background: #fff;
            color: var(--text);
            font-size: 13px;
            outline: none;
        }

        .report-filter-field {
            display: none;
        }

        .report-filter-field.is-visible {
            display: grid;
        }

        .report-filter-actions {
            display: flex;
            justify-content: flex-end;
            gap: 8px;
            flex-wrap: wrap;
        }

        .report-filter-hint {
            margin: 0;
            font-size: 12px;
            color: #6a748a;
        }

        .report-filter-range {
            min-width: 162px;
        }

        .report-filter-mode .report-filter-hint {
            position: absolute;
            left: 0;
            top: calc(100% + 2px);
            white-space: nowrap;
        }

        @media (max-width: 1280px) {
            .report-filter-row {
                justify-content: flex-start;
                width: 100%;
                margin-left: 0;
            }

            .report-filter-mode {
                width: 240px;
            }
        }

        @media (max-width: 768px) {
            .report-filter-row {
                justify-content: flex-start;
                width: 100%;
                margin-left: 0;
            }

            .report-filter-mode {
                width: 100%;
            }

            .report-filter-detail,
            .report-filter-actions {
                width: 100%;
            }

            .report-filter-actions {
                margin-left: 0;
            }
        }
    </style>

    <section class="page-head" style="display: flex; align-items: flex-start; justify-content: space-between; gap: 16px; flex-wrap: wrap;">
        <div class="page-top-reveal opacity-0 -translate-y-4 transition-all duration-700 ease-out">
            <details style="position: relative; display: inline-block;">
                <summary class="report-switcher-trigger report-switcher-summary" style="list-style: none; display: inline-flex; align-items: center; gap: 10px; user-select: none; padding: 2px 0;">
                    <span style="display: grid; gap: 4px;">
                        <span class="report-switcher-mode">Mode Laporan</span>
                        <span class="report-switcher-trigger-line">
                            <span class="report-switcher-trigger-main">
                                <h1 class="page-title" style="margin: 0;">{{ $reportTabLabels[$tab] ?? 'Overview' }}</h1>
                                <span class="report-switcher-trigger-icon-wrap" aria-hidden="true">
                                    <svg class="report-switcher-trigger-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" style="opacity: 1; flex: 0 0 auto; transition: transform 0.18s ease;">
                                        <polyline points="6 9 12 15 18 9"></polyline>
                                    </svg>
                                </span>
                            </span>
                        </span>
                    </span>
                </summary>

                <div style="position: absolute; left: 0; top: calc(100% + 10px); min-width: 240px; z-index: 20; padding: 8px; border-radius: 16px; border: 1px solid rgba(219, 227, 239, 0.95); background: rgba(255, 255, 255, 0.98); box-shadow: 0 20px 40px rgba(15, 23, 42, 0.12); display: grid; gap: 4px;">
                    @foreach ($reportTabLabels as $key => $label)
                        <a href="{{ route('backoffice.reports', ['tab' => $key, 'filter_mode' => $filterMode, 'filter_date' => $filterDate, 'filter_month' => $filterMonth, 'filter_year' => $filterYear, 'filter_start' => $filterStart, 'filter_end' => $filterEnd]) }}"
                           style="text-decoration: none; display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 10px 12px; border-radius: 12px; font-size: 13px; font-weight: 700; color: {{ $tab === $key ? '#fff' : '#52607a' }}; background: {{ $tab === $key ? 'var(--blue)' : 'transparent' }};">
                            <span>{{ $label }}</span>
                            @if ($tab === $key)
                                <span style="font-size: 10px; font-weight: 800; opacity: 0.8;">Aktif</span>
                            @endif
                        </a>
                    @endforeach
                </div>
            </details>
            <p class="page-subtitle">{{ $tab === 'overview' ? 'Ringkasan performa bisnis secara visual' : 'Rekap data pendapatan, reservasi, dan armada perusahaan.' }}</p>
        </div>

        <form method="GET" action="{{ route('backoffice.reports') }}" class="report-filter-panel page-top-reveal opacity-0 -translate-y-4 transition-all duration-700 ease-out">
            <input type="hidden" name="tab" value="{{ $tab }}">
            <div class="report-filter-row">
                <div class="report-filter-group report-filter-mode">
                    <label class="report-filter-label">Mode Filter</label>
                    <select name="filter_mode" id="filter-mode" class="report-filter-control">
                        <option value="none" @selected($filterMode === 'none')>Default</option>
                        <option value="range" @selected($filterMode === 'range')>Rentang Tanggal</option>
                        <option value="day" @selected($filterMode === 'day')>Spesifik Hari</option>
                        <option value="month" @selected($filterMode === 'month')>Spesifik Bulan</option>
                        <option value="year" @selected($filterMode === 'year')>Spesifik Tahun</option>
                    </select>
                    <p class="report-filter-hint" style="margin-top: 2px;">Default otomatis menampilkan 4 bulan terakhir.</p>
                </div>

                <div class="report-filter-detail" data-filter-detail>
                    <div class="report-filter-group report-filter-field report-filter-range" data-filter-field="range">
                        <label class="report-filter-label">Dari Tanggal</label>
                        <input type="date" name="filter_start" value="{{ $filterStart }}" class="report-filter-control">
                    </div>

                    <div class="report-filter-group report-filter-field report-filter-range" data-filter-field="range">
                        <label class="report-filter-label">Sampai Tanggal</label>
                        <input type="date" name="filter_end" value="{{ $filterEnd }}" class="report-filter-control">
                    </div>

                    <div class="report-filter-group report-filter-field" data-filter-field="day">
                        <label class="report-filter-label">Tanggal</label>
                        <input type="date" name="filter_date" value="{{ $filterDate }}" class="report-filter-control">
                    </div>

                    <div class="report-filter-group report-filter-field" data-filter-field="month">
                        <label class="report-filter-label">Bulan</label>
                        <select name="filter_month" class="report-filter-control">
                            @foreach ($reportMonths as $monthValue => $monthLabel)
                                <option value="{{ $monthValue }}" @selected($filterMonth === $monthValue)>{{ $monthLabel }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="report-filter-group report-filter-field" data-filter-field="month year">
                        <label class="report-filter-label">Tahun</label>
                        <select name="filter_year" class="report-filter-control">
                            @foreach ($reportYears as $yearValue)
                                <option value="{{ $yearValue }}" @selected((int) $filterYear === (int) $yearValue)>{{ $yearValue }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="report-filter-actions" data-filter-actions>
                    <button type="submit" style="background: var(--blue); padding: 10px 16px; border-radius: 10px; border: 0; color: white; font-weight: 700; cursor: pointer; font-size: 13px; transition: opacity 0.2s;">
                        Terapkan Filter
                    </button>
                    <a href="{{ route('backoffice.reports', ['tab' => $tab]) }}" style="display: inline-flex; align-items: center; justify-content: center; text-decoration: none; background: #f1f5f9; padding: 10px 16px; border-radius: 10px; border: 0; color: #475569; font-weight: 700; cursor: pointer; font-size: 13px; transition: background 0.2s;" onmouseover="this.style.background='#e2e8f0'" onmouseout="this.style.background='#f1f5f9'">
                        Reset
                    </a>
                </div>
            </div>
        </form>
    </section>

    @if ($tab !== 'overview')
        <section class="card" style="margin-bottom: 20px; padding: 12px 14px; border-radius: 16px; border: 1px solid rgba(219, 227, 239, 0.85); background: rgba(255, 255, 255, 0.88); display: flex; align-items: center; justify-content: flex-end; gap: 12px;">
            <a href="{{ route('backoffice.reports', ['tab' => $tab, 'filter_mode' => $filterMode, 'filter_date' => $filterDate, 'filter_month' => $filterMonth, 'filter_year' => $filterYear, 'filter_start' => $filterStart, 'filter_end' => $filterEnd, 'export' => 'csv']) }}" style="background: var(--green); padding: 10px 16px; border-radius: 10px; border: 0; color: white; font-weight: 700; cursor: pointer; font-size: 13px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; transition: opacity 0.2s; white-space: nowrap;" onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink: 0;">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                    <polyline points="7 10 12 15 17 10" />
                    <line x1="12" y1="15" x2="12" y2="3" />
                </svg>
                Export CSV
            </a>
        </section>
    @endif

    <!-- Summary Metrics & Visual Analytics Charts -->
        @if ($tab === 'overview')
        <style>
            .overview-layout {
                display: grid;
                gap: 10px;
            }

            .overview-kpi-row {
                display: grid;
            }

            .overview-charts {
                display: grid;
                grid-template-columns: minmax(0, 1.35fr) minmax(0, 1fr);
                gap: 10px;
                align-items: stretch;
            }

            .chart-column {
                display: grid;
                gap: 10px;
                align-content: start;
            }

            .overview-card {
                padding: 10px;
                border-radius: 18px;
                height: 100%;
            }

            .chart-header-meta {
                margin: 2px 0 0;
                font-size: 11px;
                line-height: 1.35;
                color: #64748b;
                font-weight: 600;
            }

            .chart-insight-top {
                margin: 0;
                font-size: 11px;
                line-height: 1.35;
                color: #64748b;
                font-weight: 600;
                text-align: right;
                max-width: 260px;
            }

            .kpi-grid {
                display: grid;
                grid-template-columns: repeat(4, minmax(0, 1fr));
                gap: 10px;
            }

            .kpi-card {
                display: flex;
                align-items: stretch;
                justify-content: space-between;
                gap: 10px;
                min-height: 60px;
                padding: 9px 11px;
                border-radius: 13px;
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
                margin: 0 0 3px;
                font-size: 10px;
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

            .kpi-card.avg .kpi-value,
            .kpi-card.success .kpi-value {
                font-size: 28px;
            }

            .kpi-value {
                margin: 0;
                font-size: 28px;
                line-height: 1;
                font-weight: 800;
                letter-spacing: -0.04em;
                color: var(--text);
                white-space: nowrap;
            }

            .kpi-support {
                display: grid;
                gap: 4px;
                margin-top: 8px;
            }

            .kpi-inline-metric {
                display: flex;
                align-items: baseline;
                gap: 6px;
                flex-wrap: wrap;
            }

            .kpi-secondary {
                margin: 0;
                font-size: 12px;
                line-height: 1.3;
                color: #64748b;
                font-weight: 600;
            }

            .kpi-trend {
                display: inline-flex;
                align-items: center;
                gap: 5px;
                margin: 0;
                font-size: 11px;
                line-height: 1.2;
                font-weight: 800;
                flex-wrap: wrap;
            }

            .kpi-trend.positive {
                color: var(--green);
            }

            .kpi-trend.negative {
                color: var(--red);
            }

            .kpi-trend.neutral {
                color: #64748b;
            }

            .kpi-trend-value {
                font-size: 13px;
                line-height: 1;
                font-weight: 900;
                letter-spacing: -0.02em;
            }

            .kpi-trend-icon {
                width: 15px;
                height: 15px;
                flex: 0 0 15px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
            }

            .kpi-trend-icon svg {
                width: 15px;
                height: 15px;
                display: block;
            }

            .kpi-trend-icon.is-up {
                transform: rotate(180deg);
            }

            .kpi-trend-suffix {
                font-size: 10px;
                line-height: 1.1;
                font-weight: 700;
                opacity: 0.85;
            }

            .kpi-graphic {
                width: 60px;
                flex: 0 0 60px;
                display: flex;
                align-items: center;
                justify-content: flex-end;
                opacity: 1;
            }

            .kpi-spark {
                width: 60px;
                height: 38px;
                overflow: visible;
            }

            .kpi-spark path,
            .kpi-spark polyline,
            .kpi-spark line,
            .kpi-spark circle {
                stroke-width: 5 !important;
            }

            .kpi-card.revenue .kpi-spark path {
                stroke-width: 4.5 !important;
            }

            .kpi-expand-grid {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 10px;
            }

            .metric-mini {
                padding: 10px 12px;
                border-radius: 14px;
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
                font-size: 16px;
                font-weight: 800;
                line-height: 1.1;
                color: var(--text);
            }

            .chart-box {
                position: relative;
                width: 100%;
            }

            .chart-stage {
                position: relative;
                width: 100%;
            }

            .chart-box.trend {
                height: 235px;
            }

            .chart-box.status {
                height: 200px;
            }

            .chart-box.service-type {
                height: 200px;
            }

            .chart-box.revenue {
                height: 180px;
            }

            .chart-insight {
                margin: 8px 0 0;
                font-size: 11px;
                line-height: 1.4;
                color: #64748b;
                font-weight: 600;
            }

            .chart-insight strong {
                color: var(--text);
                font-weight: 800;
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
                    height: 220px;
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
                    height: 200px;
                }
            }
        </style>

        <section class="overview-layout">
            <section class="overview-kpi-row">
                @php
                    $totalReservationsTrend = $overviewSummary['total_rentals_growth'] ?? ['value' => '0,0%', 'suffix' => 'vs periode sebelumnya', 'tone' => 'neutral'];
                    $revenueTrend = $overviewSummary['revenue_paid_growth'] ?? ['value' => '0,0%', 'suffix' => 'vs periode sebelumnya', 'tone' => 'neutral'];
                    $successTrend = $overviewSummary['success_rate_growth'] ?? ['value' => '0,0%', 'suffix' => 'vs periode sebelumnya', 'tone' => 'neutral'];
                    $failedTrend = $overviewSummary['failed_rate_growth'] ?? ['value' => '0,0%', 'suffix' => 'vs periode sebelumnya', 'tone' => 'neutral'];
                    $overviewBookingsPeak = $chartRentals->sortByDesc('value')->first();
                    $overviewRevenuePeak = $chartRevenue->sortByDesc('value')->first();
                    $overviewStatusPeak = $statusDistribution->sortByDesc('value')->first();
                    $overviewServicePeak = $serviceTypeDistribution->sortByDesc('value')->first();
                    $overviewTopCarPeak = $topCars->sortByDesc('count')->first();
                    $overviewTotalStatuses = $statusDistribution->sum('value');
                    $overviewTotalServices = $serviceTypeDistribution->sum('value');
                    $overviewTotalTopCars = $topCars->sum('count');
                    $overviewBookingsHeader = 'Total ' . number_format((int) ($overviewSummary['total_rentals'] ?? 0)) . ' reservasi';
                    $overviewRevenueHeader = 'Total Rp' . number_format((int) ($overviewSummary['revenue_paid'] ?? 0), 0, ',', '.') . ' • ' . number_format((int) ($overviewSummary['paid_transactions'] ?? 0)) . ' transaksi paid';
                    $overviewStatusHeader = number_format((int) $overviewTotalStatuses) . ' total reservasi';
                    $overviewServiceHeader = number_format((int) $overviewTotalServices) . ' total reservasi';
                    $overviewTopCarHeader = $overviewTopCarPeak ? number_format((int) ($overviewTopCarPeak['count'] ?? 0)) . ' reservasi paling sering dipesan' : 'Belum ada data pada periode ini';
                    $overviewFleetHeader = number_format((int) ($fleetOccupancy['total'] ?? 0)) . ' total armada';
                @endphp

                <article class="card overview-card dashboard-reveal opacity-0 translate-y-4 transition-all duration-700 ease-out">
                    <div class="section-head" style="margin-bottom: 8px;">
                        <h2 class="section-title">KPI Ringkas</h2>
                        <span class="chip" style="border-radius: 999px; padding: 5px 10px; font-size: 10px; font-weight: 700; background: #f0f3f8; color: #6a748a;">4 KPI utama</span>
                    </div>

                    <div class="kpi-grid">
                        <div class="kpi-card total dashboard-reveal opacity-0 translate-y-4 transition-all duration-700 ease-out">
                            <div class="kpi-copy">
                                <p class="kpi-label">Total Reservasi</p>
                                <p class="kpi-value" style="font-family: 'Space Grotesk', sans-serif;" data-countup-target="{{ (int) ($overviewSummary['total_rentals'] ?? 0) }}" data-countup-type="number">{{ number_format((int) ($overviewSummary['total_rentals'] ?? 0)) }}</p>
                                <div class="kpi-support">
                                <p class="kpi-trend {{ $totalReservationsTrend['tone'] ?? 'neutral' }}">
                                        @if (($totalReservationsTrend['direction'] ?? 'flat') !== 'flat')
                                            <span class="kpi-trend-icon {{ ($totalReservationsTrend['direction'] ?? 'flat') === 'up' ? 'is-up' : '' }}" aria-hidden="true">
                                                <svg viewBox="0 0 12 12" fill="currentColor" aria-hidden="true">
                                                    <path d="M2.2 5.2H4.6V0h2.8v5.2H9.8L6 12 2.2 5.2Z" />
                                                </svg>
                                            </span>
                                        @endif
                                        <span class="kpi-trend-value">{{ $totalReservationsTrend['value'] ?? '0,0%' }}</span>
                                        <span class="kpi-trend-suffix">{{ $totalReservationsTrend['suffix'] ?? 'vs periode sebelumnya' }}</span>
                                    </p>
                                </div>
                            </div>
                            <div class="kpi-graphic" aria-hidden="true">
                                <svg class="kpi-spark" viewBox="0 0 72 48" fill="none">
                                    <path d="M8 38V18M18 38V30M28 38V22M38 38V14M48 38V28M58 38V10" stroke="#cbd5e1" stroke-width="4" stroke-linecap="round"/>
                                    <path d="M58 38V16" stroke="#3f5ed7" stroke-width="4" stroke-linecap="round"/>
                                </svg>
                            </div>
                        </div>

                        <div class="kpi-card revenue dashboard-reveal opacity-0 translate-y-4 transition-all duration-700 ease-out">
                            <div class="kpi-copy">
                                <p class="kpi-label">Pendapatan Masuk</p>
                                <p class="kpi-value" style="font-family: 'Space Grotesk', sans-serif;" data-countup-target="{{ (int) ($overviewSummary['revenue_paid'] ?? 0) }}" data-countup-type="currency">Rp {{ number_format((float) ($overviewSummary['revenue_paid'] ?? 0), 0, ',', '.') }}</p>
                                <div class="kpi-support">
                                <p class="kpi-trend {{ $revenueTrend['tone'] ?? 'neutral' }}">
                                        @if (($revenueTrend['direction'] ?? 'flat') !== 'flat')
                                            <span class="kpi-trend-icon {{ ($revenueTrend['direction'] ?? 'flat') === 'up' ? 'is-up' : '' }}" aria-hidden="true">
                                                <svg viewBox="0 0 12 12" fill="currentColor" aria-hidden="true">
                                                    <path d="M2.2 5.2H4.6V0h2.8v5.2H9.8L6 12 2.2 5.2Z" />
                                                </svg>
                                            </span>
                                        @endif
                                        <span class="kpi-trend-value">{{ $revenueTrend['value'] ?? '0,0%' }}</span>
                                        <span class="kpi-trend-suffix">{{ $revenueTrend['suffix'] ?? 'vs periode sebelumnya' }}</span>
                                    </p>
                                </div>
                            </div>
                            <div class="kpi-graphic" aria-hidden="true">
                                <svg class="kpi-spark" viewBox="0 0 72 48" fill="none">
                                    <path d="M6 34C12 24 16 20 22 22C28 24 30 34 36 30C42 26 44 12 50 12C56 12 60 22 66 18" stroke="#94a3b8" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                        </div>

                        <div class="kpi-card avg dashboard-reveal opacity-0 translate-y-4 transition-all duration-700 ease-out">
                            <div class="kpi-copy">
                                <p class="kpi-label">Booking Berhasil</p>
                                <div class="kpi-inline-metric">
                                    <p class="kpi-value" style="font-family: 'Space Grotesk', sans-serif;" data-countup-target="{{ (int) ($overviewSummary['success_bookings'] ?? 0) }}" data-countup-type="number">{{ number_format((int) ($overviewSummary['success_bookings'] ?? 0)) }}</p>
                                    <p class="kpi-secondary">dari {{ number_format((int) ($overviewSummary['total_rentals'] ?? 0)) }} reservasi</p>
                                </div>
                                <div class="kpi-support">
                                <p class="kpi-trend {{ $successTrend['tone'] ?? 'neutral' }}">
                                        @if (($successTrend['direction'] ?? 'flat') !== 'flat')
                                            <span class="kpi-trend-icon {{ ($successTrend['direction'] ?? 'flat') === 'up' ? 'is-up' : '' }}" aria-hidden="true">
                                                <svg viewBox="0 0 12 12" fill="currentColor" aria-hidden="true">
                                                    <path d="M2.2 5.2H4.6V0h2.8v5.2H9.8L6 12 2.2 5.2Z" />
                                                </svg>
                                            </span>
                                        @endif
                                        <span class="kpi-trend-value">{{ $successTrend['value'] ?? '0,0%' }}</span>
                                        <span class="kpi-trend-suffix">{{ $successTrend['suffix'] ?? 'vs periode sebelumnya' }}</span>
                                    </p>
                                </div>
                            </div>
                            <div class="kpi-graphic" aria-hidden="true">
                                <svg class="kpi-spark" viewBox="0 0 72 48" fill="none">
                                    <path d="M10 30L22 18L32 24L42 14L54 20L62 10" stroke="#cbd5e1" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                        </div>

                        <div class="kpi-card success dashboard-reveal opacity-0 translate-y-4 transition-all duration-700 ease-out">
                            <div class="kpi-copy">
                                <p class="kpi-label">Booking Gagal</p>
                                <div class="kpi-inline-metric">
                                    <p class="kpi-value" style="font-family: 'Space Grotesk', sans-serif;" data-countup-target="{{ (int) ($overviewSummary['failed_bookings'] ?? 0) }}" data-countup-type="number">{{ number_format((int) ($overviewSummary['failed_bookings'] ?? 0)) }}</p>
                                    <p class="kpi-secondary">dari {{ number_format((int) ($overviewSummary['total_rentals'] ?? 0)) }} reservasi</p>
                                </div>
                                <div class="kpi-support">
                                <p class="kpi-trend {{ $failedTrend['tone'] ?? 'neutral' }}">
                                        @if (($failedTrend['direction'] ?? 'flat') !== 'flat')
                                            <span class="kpi-trend-icon {{ ($failedTrend['direction'] ?? 'flat') === 'up' ? 'is-up' : '' }}" aria-hidden="true">
                                                <svg viewBox="0 0 12 12" fill="currentColor" aria-hidden="true">
                                                    <path d="M2.2 5.2H4.6V0h2.8v5.2H9.8L6 12 2.2 5.2Z" />
                                                </svg>
                                            </span>
                                        @endif
                                        <span class="kpi-trend-value">{{ $failedTrend['value'] ?? '0,0%' }}</span>
                                        <span class="kpi-trend-suffix">{{ $failedTrend['suffix'] ?? 'vs periode sebelumnya' }}</span>
                                    </p>
                                </div>
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
                    <article class="card overview-card dashboard-reveal opacity-0 translate-y-4 transition-all duration-700 ease-out" data-dashboard-group="0">
                        <div class="section-head" style="margin-bottom: 4px; display: flex; align-items: flex-start; justify-content: space-between; gap: 12px;">
                            <div>
                                <h2 class="section-title" style="margin-bottom: 2px;">Tren Periode Aktif</h2>
                                <p class="chart-header-meta">{{ $overviewBookingsHeader }}</p>
                            </div>
                            <div style="display: grid; justify-items: end; gap: 4px;">
                                <span class="chip" style="border-radius: 999px; padding: 5px 10px; font-size: 10px; font-weight: 700; background: #f0f3f8; color: #6a748a;">{{ $chartMode === 'hour' ? 'Per Jam' : ($chartMode === 'day' ? 'Harian' : 'Bulanan') }}</span>
                                <p class="chart-insight-top">
                                    @if ($overviewBookingsPeak)
                                        Reservasi tertinggi pada {{ $overviewBookingsPeak['label'] }}.
                                    @else
                                        Belum ada data pada periode ini.
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="chart-box trend chart-stage dashboard-reveal opacity-0 translate-y-4 transition-all duration-700 ease-out">
                            <canvas id="overview-bookings-chart" data-chart-group="0"></canvas>
                        </div>
                    </article>

                    <article class="card overview-card dashboard-reveal opacity-0 translate-y-4 transition-all duration-700 ease-out" data-dashboard-group="1">
                        <div class="section-head" style="margin-bottom: 4px; display: flex; align-items: flex-start; justify-content: space-between; gap: 12px;">
                            <div>
                                <h2 class="section-title" style="margin-bottom: 2px;">Pendapatan Masuk</h2>
                                <p class="chart-header-meta">{{ $overviewRevenueHeader }}</p>
                            </div>
                            <p class="chart-insight-top">
                                @if ($overviewRevenuePeak)
                                    Pendapatan terbesar pada {{ $overviewRevenuePeak['label'] }}.
                                @else
                                    Belum ada data pada periode ini.
                                @endif
                            </p>
                        </div>
                        <div class="chart-box revenue chart-stage dashboard-reveal opacity-0 translate-y-4 transition-all duration-700 ease-out">
                            <canvas id="overview-revenue-chart" data-chart-group="1"></canvas>
                        </div>
                    </article>
                </div>

                <div class="chart-column">
                    <article class="card overview-card dashboard-reveal opacity-0 translate-y-4 transition-all duration-700 ease-out" data-dashboard-group="0">
                        <div class="section-head" style="margin-bottom: 4px; display: flex; align-items: flex-start; justify-content: space-between; gap: 12px;">
                            <div>
                                <h2 class="section-title" style="margin-bottom: 2px;">Distribusi Status Rental</h2>
                                <p class="chart-header-meta">{{ $overviewStatusHeader }}</p>
                            </div>
                            <p class="chart-insight-top">
                                @if ($overviewStatusPeak)
                                    Status terbanyak {{ $overviewStatusPeak['label'] }}.
                                @else
                                    Belum ada data pada periode ini.
                                @endif
                            </p>
                        </div>
                        <div class="chart-box status chart-stage dashboard-reveal opacity-0 translate-y-4 transition-all duration-700 ease-out">
                            <canvas id="overview-status-chart" data-chart-group="0"></canvas>
                        </div>
                    </article>

                    <article class="card overview-card dashboard-reveal opacity-0 translate-y-4 transition-all duration-700 ease-out" data-dashboard-group="1">
                        <div class="section-head" style="margin-bottom: 4px; display: flex; align-items: flex-start; justify-content: space-between; gap: 12px;">
                            <div>
                                <h2 class="section-title" style="margin-bottom: 2px;">Reservasi Berdasarkan Tipe Layanan</h2>
                                <p class="chart-header-meta">{{ $overviewServiceHeader }}</p>
                            </div>
                            <p class="chart-insight-top">
                                @if ($overviewServicePeak)
                                    {{ $overviewServicePeak['label'] }} paling banyak dipilih.
                                @else
                                    Belum ada data pada periode ini.
                                @endif
                            </p>
                        </div>
                        <div class="chart-box service-type chart-stage dashboard-reveal opacity-0 translate-y-4 transition-all duration-700 ease-out">
                            <canvas id="overview-service-type-chart" data-chart-group="1"></canvas>
                        </div>
                    </article>
                </div>
            </section>

            <section class="overview-charts">
                <article class="card overview-card dashboard-reveal opacity-0 translate-y-4 transition-all duration-700 ease-out" data-dashboard-group="2">
                    <div class="section-head" style="margin-bottom: 4px; display: flex; align-items: flex-start; justify-content: space-between; gap: 12px;">
                        <div>
                            <h2 class="section-title" style="margin-bottom: 2px;">Top Armada Terpopuler</h2>
                            <p class="chart-header-meta">{{ $overviewTopCarHeader }}</p>
                        </div>
                        <p class="chart-insight-top">
                            @if ($overviewTopCarPeak)
                                {{ $overviewTopCarPeak['name'] }} paling sering dipesan.
                            @else
                                Belum ada data pada periode ini.
                            @endif
                        </p>
                    </div>
                    <div class="topcars-list">
                        @forelse ($topCars as $car)
                            <div class="topcars-item dashboard-reveal opacity-0 translate-y-4 transition-all duration-700 ease-out">
                                <div style="min-width: 0;">
                                    <p class="topcars-name">{{ $car['name'] }}</p>
                                    <p class="chart-header-meta" style="margin-top: 2px;">Rp {{ number_format((int) ($car['revenue'] ?? 0), 0, ',', '.') }}</p>
                                </div>
                                <span class="text-xs font-bold rounded-full px-2 py-1 bg-slate-200 text-slate-700" style="white-space: nowrap;">{{ $car['count'] }}x</span>
                            </div>
                        @empty
                            <p class="topcars-empty">Belum ada transaksi dalam periode ini.</p>
                        @endforelse
                    </div>
                </article>

                <article class="card overview-card dashboard-reveal opacity-0 translate-y-4 transition-all duration-700 ease-out" data-dashboard-group="2">
                    <div class="section-head" style="margin-bottom: 4px; display: flex; align-items: flex-start; justify-content: space-between; gap: 12px;">
                        <div>
                            <h2 class="section-title" style="margin-bottom: 2px;">Status Ketersediaan Armada</h2>
                            <p class="chart-header-meta">{{ $overviewFleetHeader }}</p>
                        </div>
                        <p class="chart-insight-top">
                            @if (($fleetOccupancy['total'] ?? 0) > 0)
                                {{ (int) ($fleetOccupancy['available'] ?? 0) }} armada masih tersedia.
                            @else
                                Belum ada data pada periode ini.
                            @endif
                        </p>
                    </div>
                    <div class="kpi-expand-grid" style="grid-template-columns: repeat(3, minmax(0, 1fr));">
                        <div class="metric-mini dashboard-reveal opacity-0 translate-y-4 transition-all duration-700 ease-out">
                            <p class="kpi-label">Total</p>
                            <p class="kpi-value">{{ (int) $fleetOccupancy['total'] }}</p>
                        </div>
                        <div class="metric-mini green dashboard-reveal opacity-0 translate-y-4 transition-all duration-700 ease-out">
                            <p class="kpi-label">Tersedia</p>
                            <p class="kpi-value">{{ (int) $fleetOccupancy['available'] }}</p>
                        </div>
                        <div class="metric-mini dashboard-reveal opacity-0 translate-y-4 transition-all duration-700 ease-out" style="background: rgba(254, 242, 242, 0.98); border-color: rgba(254, 202, 202, 0.95);">
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
            const filterMode = document.getElementById('filter-mode');
            const filterFields = document.querySelectorAll('[data-filter-field]');
            const filterDetail = document.querySelector('[data-filter-detail]');

            function syncFilterFields() {
                const activeMode = filterMode ? filterMode.value : 'none';
                filterFields.forEach((field) => {
                    const modes = (field.dataset.filterField || '').split(/\s+/).filter(Boolean);
                    field.classList.toggle('is-visible', modes.includes(activeMode));
                });

                if (filterDetail) {
                    const hasDetail = activeMode !== 'none';
                    filterDetail.classList.toggle('is-hidden', !hasDetail);
                }
            }

            if (filterMode) {
                filterMode.addEventListener('change', syncFilterFields);
                syncFilterFields();
            }

            @if ($tab === 'overview')
                const overviewBookingsBreakdown = @json($chartBookingsBreakdown->values());
                const overviewRevenueBreakdown = @json($chartRevenueBreakdown->values());

                const getCssVar = (name, fallback) => {
                    const value = getComputedStyle(document.documentElement).getPropertyValue(name).trim();
                    return value || fallback;
                };

                const blue = getCssVar('--blue', '#3f5ed7');
                const green = getCssVar('--green', '#1dbb84');
                const textColor = getCssVar('--text', '#202636');
                const dashboardBaseDelay = 220;
                const dashboardGroupGap = 180;
                const revealDuration = 700;
                const formatInteger = (value) => new Intl.NumberFormat('id-ID').format(Number(value) || 0);
                const formatCurrency = (value) => 'Rp ' + new Intl.NumberFormat('id-ID').format(Number(value) || 0);
                const formatCompactCurrency = (value) => {
                    const amount = Number(value) || 0;
                    if (amount >= 1000000) {
                        return 'Rp' + (amount / 1000000).toLocaleString('id-ID', { maximumFractionDigits: 1 }) + ' jt';
                    }

                    if (amount >= 1000) {
                        return 'Rp' + (amount / 1000).toLocaleString('id-ID', { maximumFractionDigits: 1 }) + ' rb';
                    }

                    return 'Rp' + formatInteger(amount);
                };
                const formatPercent = (value, total) => {
                    if (!total) return '0,0%';
                    return (Number(value) / Number(total) * 100).toLocaleString('id-ID', { minimumFractionDigits: 1, maximumFractionDigits: 1 }) + '%';
                };
                const sumValues = (items) => items.reduce((sum, item) => sum + (Number(item) || 0), 0);

                document.querySelectorAll('.page-top-reveal').forEach((element, index) => {
                    window.setTimeout(() => {
                        element.classList.remove('opacity-0', '-translate-y-4');
                    }, index * 120);
                });

                document.querySelectorAll('.dashboard-reveal[data-dashboard-group]').forEach((element) => {
                    const group = Number(element.dataset.dashboardGroup || 0);
                    window.setTimeout(() => {
                        element.classList.remove('opacity-0', 'translate-y-4');
                    }, 220 + (group * 180));
                });

                document.querySelectorAll('.dashboard-reveal:not([data-dashboard-group])').forEach((element, index) => {
                    window.setTimeout(() => {
                        element.classList.remove('opacity-0', 'translate-y-4');
                    }, 820 + (index * 90));
                });

                document.querySelectorAll('[data-countup-target]').forEach((element) => {
                    const target = Number(element.dataset.countupTarget || 0);
                    const type = element.dataset.countupType || 'number';
                    const duration = 1400;

                    const render = (value) => {
                        element.textContent = type === 'currency'
                            ? formatCurrency(value)
                            : formatInteger(value);
                    };

                    const animateCount = () => {
                        const startTime = performance.now();

                        const animate = (currentTime) => {
                            const progress = Math.min((currentTime - startTime) / duration, 1);
                            const eased = 1 - Math.pow(1 - progress, 4);
                            render(Math.round(target * eased));

                            if (progress < 1) {
                                requestAnimationFrame(animate);
                            }
                        };

                        requestAnimationFrame(animate);
                    };

                    const revealCard = element.closest('[data-dashboard-group]');
                    const revealGroup = Number(revealCard?.dataset.dashboardGroup || 0);
                    const revealDelay = dashboardBaseDelay + (revealGroup * dashboardGroupGap) + revealDuration;

                    render(0);
                    window.setTimeout(() => {
                        animateCount();
                    }, revealDelay);
                });

                const scheduleChartRender = (element, renderChart) => {
                    if (!element) {
                        return;
                    }

                    const group = Number(element.dataset.chartGroup || 0);
                    const startDelay = dashboardBaseDelay + (group * dashboardGroupGap) + revealDuration;

                    window.setTimeout(() => {
                        renderChart();
                    }, startDelay);
                };

                // Line chart point-by-point draw animation
                const makeLineAnimation = (pointCount) => {
                    const delayBetweenPoints = pointCount > 0 ? 2000 / pointCount : 250;
                    return {
                        x: {
                            type: 'number',
                            easing: 'easeInOutQuad',
                            duration: delayBetweenPoints,
                            from: NaN,
                            delay(ctx) {
                                if (ctx.type !== 'data' || ctx.xStarted) return 0;
                                ctx.xStarted = true;
                                return ctx.index * delayBetweenPoints;
                            }
                        },
                        y: {
                            type: 'number',
                            easing: 'easeInOutQuad',
                            duration: delayBetweenPoints,
                            from: NaN,
                            delay(ctx) {
                                if (ctx.type !== 'data' || ctx.yStarted) return 0;
                                ctx.yStarted = true;
                                return ctx.index * delayBetweenPoints;
                            }
                        }
                    };
                };

                const valueLabelsPlugin = {
                    id: 'valueLabelsPlugin',
                    afterDatasetsDraw(chart, args, options) {
                        const cfg = options || {};
                        if (!cfg.enabled) {
                            return;
                        }

                        const dataset = chart.data.datasets[0] || {};
                        const values = (dataset.data || []).map((value) => Number(value) || 0);
                        if (!values.length) {
                            return;
                        }

                        const maxValue = Math.max(...values);
                        const showAll = values.length <= (cfg.showAllThreshold ?? 12);
                        const ctx = chart.ctx;
                        ctx.save();
                        ctx.fillStyle = cfg.color || '#475569';
                        ctx.font = `${cfg.fontWeight || 700} ${cfg.fontSize || 10}px ${cfg.fontFamily || "'Instrument Sans', sans-serif"}`;
                        ctx.textAlign = 'center';
                        ctx.textBaseline = 'bottom';

                        chart.getDatasetMeta(0).data.forEach((element, index) => {
                            const value = values[index];
                            if (value <= 0) {
                                return;
                            }
                            if (!showAll && index !== values.length - 1 && value !== maxValue) {
                                return;
                            }

                            const label = typeof cfg.formatter === 'function'
                                ? cfg.formatter(value, index, values, chart)
                                : String(value);
                            if (!label) {
                                return;
                            }

                            const position = element.tooltipPosition();
                            ctx.fillText(label, position.x, position.y - (cfg.offset || 8));
                        });

                        ctx.restore();
                    }
                };

                const centerTextPlugin = {
                    id: 'centerTextPlugin',
                    beforeDraw(chart, args, options) {
                        const cfg = options || {};
                        if (!cfg.enabled || chart.config.type !== 'doughnut') {
                            return;
                        }

                        const meta = chart.getDatasetMeta(0);
                        if (!meta || !meta.data || !meta.data.length) {
                            return;
                        }

                        const { ctx, chartArea } = chart;
                        const total = Number(cfg.total ?? sumValues(chart.data.datasets[0]?.data || []));
                        const progress = Math.max(0, Math.min(1, Number(chart.$centerTextProgress ?? 1)));
                        const animatedTotal = Math.round(total * progress);
                        const centerX = (chartArea.left + chartArea.right) / 2;
                        const centerY = (chartArea.top + chartArea.bottom) / 2;
                        ctx.save();
                        ctx.textAlign = 'center';
                        ctx.textBaseline = 'middle';
                        ctx.fillStyle = cfg.color || textColor;
                        ctx.font = `${cfg.totalFontWeight || 800} ${cfg.totalFontSize || 20}px ${cfg.fontFamily || "'Space Grotesk', sans-serif"}`;
                        ctx.fillText(formatInteger(animatedTotal), centerX, centerY - 8);
                        ctx.fillStyle = cfg.labelColor || '#64748b';
                        ctx.font = `${cfg.labelFontWeight || 700} ${cfg.labelFontSize || 10}px ${cfg.fontFamily || "'Instrument Sans', sans-serif"}`;
                        ctx.fillText(cfg.label || 'Total', centerX, centerY + 12);
                        ctx.restore();
                    }
                };

                Chart.register(valueLabelsPlugin, centerTextPlugin);

                // 1. Overview Bookings Line Chart
                const overviewBookingsCtx = document.getElementById('overview-bookings-chart');
                scheduleChartRender(overviewBookingsCtx, () => {
                    new Chart(overviewBookingsCtx, {
                        type: 'line',
                        data: {
                            labels: @json($chartRentals->pluck('label')),
                            datasets: [{
                                label: '{{ $chartMode === "hour" ? "Reservasi per Jam" : ($chartMode === "day" ? "Reservasi Harian" : "Reservasi Bulanan") }}',
                                data: @json($chartRentals->pluck('value')),
                                borderColor: blue,
                                backgroundColor: 'rgba(63, 94, 215, 0.08)',
                                fill: true,
                                tension: 0.35,
                                pointRadius: 4,
                                pointBackgroundColor: blue,
                                borderWidth: 3,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            animation: makeLineAnimation(@json($chartRentals->count())),
                            plugins: {
                                legend: { display: false },
                                valueLabelsPlugin: {
                                    enabled: true,
                                    showAllThreshold: 12,
                                    formatter: (value) => String(value)
                                },
                                tooltip: {
                                    callbacks: {
                                        title: (items) => overviewBookingsBreakdown[items[0].dataIndex]?.label || items[0].label,
                                        label: (ctx) => `Reservasi: ${formatInteger(overviewBookingsBreakdown[ctx.dataIndex]?.total ?? ctx.parsed.y)}`,
                                        afterBody: (items) => {
                                            const detail = overviewBookingsBreakdown[items[0].dataIndex] || {};
                                            return [
                                                `Booking berhasil: ${formatInteger(detail.success ?? 0)}`,
                                                `Booking gagal: ${formatInteger(detail.failed ?? 0)}`
                                            ];
                                        }
                                    }
                                }
                            },
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
                });

                // 2. Overview Revenue Bar Chart
                const overviewRevenueCtx = document.getElementById('overview-revenue-chart');
                scheduleChartRender(overviewRevenueCtx, () => {
                    new Chart(overviewRevenueCtx, {
                        type: 'bar',
                        data: {
                            labels: @json($chartRevenue->pluck('label')),
                            datasets: [{
                                label: '{{ $chartMode === "hour" ? "Pendapatan per Jam" : ($chartMode === "day" ? "Pendapatan Harian" : "Pendapatan Bulanan") }}',
                                data: @json($chartRevenue->pluck('value')),
                                backgroundColor: 'rgba(29, 187, 132, 0.75)',
                                borderRadius: 6,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            animation: {
                                duration: 1500,
                                easing: 'easeOutQuart',
                                delay(ctx) {
                                    return ctx.type === 'data' ? ctx.dataIndex * 90 : 0;
                                }
                            },
                            animations: {
                                y: {
                                    from(ctx) {
                                        return ctx.chart.scales.y.getPixelForValue(0);
                                    }
                                }
                            },
                            plugins: {
                                legend: { display: false },
                                valueLabelsPlugin: {
                                    enabled: true,
                                    showAllThreshold: 12,
                                    formatter: (value) => formatCompactCurrency(value)
                                },
                                tooltip: {
                                    callbacks: {
                                        title: (items) => overviewRevenueBreakdown[items[0].dataIndex]?.label || items[0].label,
                                        label: (ctx) => `Pendapatan masuk: ${formatCurrency(overviewRevenueBreakdown[ctx.dataIndex]?.revenue ?? ctx.parsed.y)}`,
                                        afterBody: (items) => {
                                            const detail = overviewRevenueBreakdown[items[0].dataIndex] || {};
                                            return [`Transaksi paid: ${formatInteger(detail.transactions ?? 0)}`];
                                        }
                                    }
                                }
                            },
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
                });

                const statusValues = @json($statusDistribution->pluck('value'));
                const statusTotal = sumValues(statusValues);

                // 3. Overview Status Distribution Doughnut Chart
                const overviewStatusCtx = document.getElementById('overview-status-chart');
                scheduleChartRender(overviewStatusCtx, () => {
                    new Chart(overviewStatusCtx, {
                        type: 'doughnut',
                        data: {
                            labels: @json($statusDistribution->pluck('label')),
                            datasets: [{
                                data: statusValues,
                                backgroundColor: ['#818cf8', '#f59e0b', '#3b82f6', green, '#ef4444', '#94a3b8'],
                                borderWidth: 0,
                            }]
                        },
                        options: {
                            cutout: '68%',
                            responsive: true,
                            maintainAspectRatio: false,
                            rotation: -90,
                            circumference: 360,
                            animation: {
                                duration: 1500,
                                easing: 'easeOutQuart',
                                animateRotate: true,
                                animateScale: false,
                                onProgress(context) {
                                    context.chart.$centerTextProgress = context.initial ? 0 : context.currentStep / context.numSteps;
                                },
                                onComplete(context) {
                                    context.chart.$centerTextProgress = 1;
                                }
                            },
                            plugins: {
                                centerTextPlugin: {
                                    enabled: true,
                                    total: statusTotal,
                                    label: 'Total Reservasi'
                                },
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        boxWidth: 8,
                                        boxHeight: 8,
                                        font: { size: 10, family: "'Instrument Sans', sans-serif" },
                                        color: '#64748b',
                                        generateLabels: (chart) => {
                                            const dataset = chart.data.datasets[0] || {};
                                            const data = dataset.data || [];
                                            const total = sumValues(data);
                                            return chart.data.labels.map((label, index) => {
                                                const value = Number(data[index]) || 0;
                                                return {
                                                    text: `${label} ${formatInteger(value)} / ${formatPercent(value, total)}`,
                                                    fillStyle: ['#818cf8', '#f59e0b', '#3b82f6', green, '#ef4444', '#94a3b8'][index % 6],
                                                    strokeStyle: ['#818cf8', '#f59e0b', '#3b82f6', green, '#ef4444', '#94a3b8'][index % 6],
                                                    lineWidth: 0,
                                                    hidden: false,
                                                    index
                                                };
                                            });
                                        }
                                    }
                                },
                                tooltip: {
                                    callbacks: {
                                        title: (items) => items[0].label,
                                        label: (ctx) => {
                                            const value = Number(ctx.parsed) || 0;
                                            return [
                                                `${formatInteger(value)} reservasi`,
                                                `${formatPercent(value, statusTotal)} dari total`
                                            ];
                                        }
                                    }
                                }
                            }
                        }
                    });
                });

                const serviceValues = @json($serviceTypeDistribution->pluck('value'));
                const serviceTotal = sumValues(serviceValues);

                // 4. Overview Service Type Distribution Doughnut Chart
                const overviewServiceTypeCtx = document.getElementById('overview-service-type-chart');
                scheduleChartRender(overviewServiceTypeCtx, () => {
                    new Chart(overviewServiceTypeCtx, {
                        type: 'doughnut',
                        data: {
                            labels: @json($serviceTypeDistribution->pluck('label')),
                            datasets: [{
                                data: serviceValues,
                                backgroundColor: [blue, green],
                                borderWidth: 0,
                            }]
                        },
                        options: {
                            cutout: '68%',
                            responsive: true,
                            maintainAspectRatio: false,
                            rotation: -90,
                            circumference: 360,
                            animation: {
                                duration: 1500,
                                easing: 'easeOutQuart',
                                animateRotate: true,
                                animateScale: false,
                                onProgress(context) {
                                    context.chart.$centerTextProgress = context.initial ? 0 : context.currentStep / context.numSteps;
                                },
                                onComplete(context) {
                                    context.chart.$centerTextProgress = 1;
                                }
                            },
                            plugins: {
                                centerTextPlugin: {
                                    enabled: true,
                                    total: serviceTotal,
                                    label: 'Total Reservasi'
                                },
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        boxWidth: 8,
                                        boxHeight: 8,
                                        font: { size: 10, family: "'Instrument Sans', sans-serif" },
                                        color: '#64748b',
                                        generateLabels: (chart) => {
                                            const dataset = chart.data.datasets[0] || {};
                                            const data = dataset.data || [];
                                            const total = sumValues(data);
                                            return chart.data.labels.map((label, index) => {
                                                const value = Number(data[index]) || 0;
                                                return {
                                                    text: `${label} ${formatInteger(value)} / ${formatPercent(value, total)}`,
                                                    fillStyle: [blue, green][index % 2],
                                                    strokeStyle: [blue, green][index % 2],
                                                    lineWidth: 0,
                                                    hidden: false,
                                                    index
                                                };
                                            });
                                        }
                                    }
                                },
                                tooltip: {
                                    callbacks: {
                                        title: (items) => items[0].label,
                                        label: (ctx) => {
                                            const value = Number(ctx.parsed) || 0;
                                            return [
                                                `${formatInteger(value)} reservasi`,
                                                `${formatPercent(value, serviceTotal)} dari total`
                                            ];
                                        }
                                    }
                                }
                            }
                        }
                    });
                });
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
