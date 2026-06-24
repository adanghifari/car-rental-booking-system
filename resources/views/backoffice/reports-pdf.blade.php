<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $reportTitle }} - {{ $reportPeriodLabel }}</title>
    <style>
        :root {
            --blue: #3f5ed7;
            --green: #1dbb84;
            --text: #202636;
            --muted: #64748b;
            --border: #dbe3ef;
            --bg: #f6f8fc;
        }

        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            color: var(--text);
            background: var(--bg);
        }

        .page {
            width: 1120px;
            margin: 0 auto;
            padding: 28px;
        }

        .sheet {
            background: #fff;
            border-radius: 24px;
            padding: 28px;
            box-shadow: 0 24px 48px rgba(15, 23, 42, 0.08);
        }

        .header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 24px;
            padding-bottom: 18px;
            border-bottom: 1px solid var(--border);
        }

        .eyebrow {
            margin: 0 0 8px;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: .12em;
            text-transform: uppercase;
            color: var(--blue);
        }

        h1 {
            margin: 0;
            font-size: 34px;
            line-height: 1.05;
        }

        .subtitle {
            margin: 10px 0 0;
            font-size: 14px;
            color: var(--muted);
        }

        .meta {
            display: grid;
            gap: 6px;
            justify-items: end;
            text-align: right;
        }

        .meta strong {
            font-size: 13px;
        }

        .meta span {
            font-size: 12px;
            color: var(--muted);
        }

        .summary-grid {
            display: grid;
            gap: 14px;
            margin-bottom: 24px;
        }

        .summary-grid.cols-4 { grid-template-columns: repeat(4, minmax(0, 1fr)); }
        .summary-grid.cols-3 { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        .summary-grid.cols-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .summary-grid.cols-5 { grid-template-columns: repeat(5, minmax(0, 1fr)); }

        .summary-card {
            border: 1px solid var(--border);
            border-radius: 18px;
            padding: 18px;
            background: #fff;
        }

        .summary-card .label {
            margin: 0 0 8px;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: var(--muted);
        }

        .summary-card .value {
            margin: 0;
            font-size: 28px;
            font-weight: 800;
            line-height: 1;
        }

        .summary-card .note {
            margin: 10px 0 0;
            font-size: 12px;
            color: var(--muted);
            line-height: 1.45;
        }

        .insight-banner {
            display: grid;
            gap: 14px;
            padding: 18px 20px;
            margin-bottom: 24px;
            border-radius: 20px;
            border: 1px solid #d8e2f2;
            background: linear-gradient(135deg, rgba(63, 94, 215, 0.06), rgba(29, 187, 132, 0.04), rgba(255, 255, 255, 0.95));
        }

        .insight-banner-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }

        .insight-banner-title {
            margin: 0;
            font-size: 12px;
            font-weight: 800;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: var(--blue);
        }

        .insight-banner-text {
            margin: 0;
            font-size: 14px;
            line-height: 1.6;
            color: #334155;
        }

        .mini-facts {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 12px;
        }

        .mini-fact {
            padding: 12px 14px;
            border-radius: 14px;
            background: rgba(255, 255, 255, 0.88);
            border: 1px solid #e6edf6;
        }

        .mini-fact span {
            display: block;
            margin-bottom: 6px;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: var(--muted);
        }

        .mini-fact strong {
            font-size: 15px;
            line-height: 1.35;
        }

        .section {
            margin-bottom: 24px;
        }

        .section-header {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 12px;
        }

        .section-header h2 {
            margin: 0;
            font-size: 18px;
        }

        .section-header p {
            margin: 4px 0 0;
            font-size: 12px;
            color: var(--muted);
        }

        .chart-grid {
            display: grid;
            gap: 16px;
        }

        .chart-grid.cols-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }

        .chart-card {
            border: 1px solid var(--border);
            border-radius: 18px;
            padding: 16px;
            background: #fff;
        }

        .chart-card h3 {
            margin: 0 0 10px;
            font-size: 15px;
        }

        .chart-svg {
            width: 100%;
            overflow: hidden;
            border-radius: 16px;
            background: #fff;
        }

        .list-card {
            border: 1px solid var(--border);
            border-radius: 18px;
            padding: 16px 18px;
            background: #fff;
        }

        .rank-list {
            display: grid;
            gap: 10px;
        }

        .rank-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            padding: 12px 14px;
            border-radius: 14px;
            background: #f8fafc;
        }

        .rank-item strong {
            display: block;
            margin-bottom: 2px;
            font-size: 14px;
        }

        .rank-item span {
            font-size: 12px;
            color: var(--muted);
        }

        .pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 6px 10px;
            border-radius: 999px;
            background: #e2e8f0;
            color: #334155;
            font-size: 11px;
            font-weight: 800;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 18px;
            overflow: hidden;
        }

        thead th {
            background: #f8fafc;
            color: #334155;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: .04em;
            text-transform: uppercase;
        }

        th, td {
            padding: 12px 10px;
            border-bottom: 1px solid #e9eef5;
            text-align: left;
            vertical-align: top;
        }

        tbody tr:last-child td {
            border-bottom: 0;
        }

        .empty {
            border: 1px dashed var(--border);
            border-radius: 18px;
            padding: 24px;
            text-align: center;
            color: var(--muted);
            font-size: 13px;
            background: #fff;
        }

        .featured-box {
            display: grid;
            gap: 12px;
            border: 1px solid var(--border);
            border-radius: 18px;
            padding: 18px;
            background: linear-gradient(135deg, #f8fbff, #ffffff);
        }

        .featured-box h3 {
            margin: 0;
            font-size: 22px;
        }

        .featured-image-wrap {
            overflow: hidden;
            border-radius: 18px;
            border: 1px solid #e7edf5;
            background: linear-gradient(135deg, #eef4ff, #f8fbff);
        }

        .featured-image {
            width: 100%;
            height: 280px;
            object-fit: cover;
            object-position: center;
            display: block;
        }

        .featured-box p {
            margin: 0;
            font-size: 13px;
            line-height: 1.5;
            color: var(--muted);
        }

        .featured-metrics {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
        }

        .featured-metrics .metric {
            border-radius: 14px;
            background: rgba(255, 255, 255, 0.96);
            padding: 12px 14px;
            border: 1px solid #e7edf5;
        }

        .featured-metrics .metric span {
            display: block;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: .06em;
            text-transform: uppercase;
            color: var(--muted);
            margin-bottom: 6px;
        }

        .featured-metrics .metric strong {
            font-size: 18px;
        }

        @media print {
            body { background: #fff; }
            .page { width: auto; margin: 0; padding: 0; }
            .sheet { box-shadow: none; border-radius: 0; }
        }
    </style>
</head>
<body>
    <div class="page">
        <div class="sheet">
            <header class="header">
                <div>
                    <p class="eyebrow">Export Laporan</p>
                    <h1>{{ $reportTitle }}</h1>
                    <p class="subtitle">Periode: {{ $reportPeriodLabel }}</p>
                </div>
                <div class="meta">
                    <strong>Car Rental Fleet Management</strong>
                    <span>Dibuat: {{ $generatedAt->translatedFormat('d F Y H:i') }}</span>
                    <span>Mode filter: {{ strtoupper($filterMode) }}</span>
                </div>
            </header>

            @php
                $filterModeLabel = match ($filterMode) {
                    'day' => 'Spesifik Hari',
                    'month' => 'Spesifik Bulan',
                    'year' => 'Spesifik Tahun',
                    'range' => 'Rentang Tanggal',
                    default => 'Default Bulan Berjalan',
                };

                $insightText = match ($tab) {
                    'overview' => 'Dokumen ini merangkum performa bisnis utama pada periode aktif, termasuk volume reservasi, pendapatan masuk, distribusi status rental, pilihan layanan, dan performa armada terpopuler.',
                    'revenue' => 'Dokumen ini menyoroti performa pendapatan pada periode aktif, dengan fokus pada total revenue, jumlah transaksi lunas, rata-rata nilai transaksi, serta rincian pembayaran yang tercatat.',
                    'reservation' => 'Dokumen ini merangkum aktivitas reservasi pada periode aktif, termasuk volume booking, status penyelesaian, rasio pembatalan, durasi rata-rata rental, dan detail transaksi reservasi.',
                    'fleet' => 'Dokumen ini merangkum performa armada pada periode aktif, mencakup ketersediaan unit, armada paling produktif, status operasional, dan kontribusi masing-masing kendaraan.',
                    default => 'Dokumen laporan periode aktif.',
                };
            @endphp

            <section class="insight-banner">
                <div class="insight-banner-head">
                    <p class="insight-banner-title">Ringkasan Export</p>
                    <span class="pill">{{ $filterModeLabel }}</span>
                </div>
                <p class="insight-banner-text">{{ $insightText }}</p>
                <div class="mini-facts">
                    <div class="mini-fact">
                        <span>Periode Aktif</span>
                        <strong>{{ $reportPeriodLabel }}</strong>
                    </div>
                    <div class="mini-fact">
                        <span>Tab Laporan</span>
                        <strong>{{ $reportTitle }}</strong>
                    </div>
                    <div class="mini-fact">
                        <span>Cakupan Export</span>
                        <strong>Summary, grafik, dan data detail mengikuti filter aktif</strong>
                    </div>
                </div>
            </section>

            @if ($tab === 'overview')
                <section class="summary-grid cols-4">
                    <div class="summary-card">
                        <p class="label">Total Reservasi</p>
                        <p class="value">{{ number_format((int) ($overviewSummary['total_rentals'] ?? 0), 0, ',', '.') }}</p>
                    </div>
                    <div class="summary-card">
                        <p class="label">Pendapatan Masuk</p>
                        <p class="value">Rp {{ number_format((int) ($overviewSummary['revenue_paid'] ?? 0), 0, ',', '.') }}</p>
                    </div>
                    <div class="summary-card">
                        <p class="label">Booking Berhasil</p>
                        <p class="value">{{ number_format((int) ($overviewSummary['success_bookings'] ?? 0), 0, ',', '.') }}</p>
                    </div>
                    <div class="summary-card">
                        <p class="label">Booking Gagal</p>
                        <p class="value">{{ number_format((int) ($overviewSummary['failed_bookings'] ?? 0), 0, ',', '.') }}</p>
                    </div>
                </section>

                <section class="chart-grid cols-2 section">
                    <div class="chart-card">
                        <h3>Tren Periode Aktif</h3>
                        <div class="chart-svg">{!! $pdfCharts['bookings'] ?? '' !!}</div>
                    </div>
                    <div class="chart-card">
                        <h3>Distribusi Status Rental</h3>
                        <div class="chart-svg">{!! $pdfCharts['status'] ?? '' !!}</div>
                    </div>
                    <div class="chart-card">
                        <h3>Pendapatan Masuk</h3>
                        <div class="chart-svg">{!! $pdfCharts['revenue'] ?? '' !!}</div>
                    </div>
                    <div class="chart-card">
                        <h3>Reservasi Berdasarkan Tipe Layanan</h3>
                        <div class="chart-svg">{!! $pdfCharts['service'] ?? '' !!}</div>
                    </div>
                </section>

                <section class="chart-grid cols-2 section">
                    <div class="list-card">
                        <div class="section-header">
                            <div>
                                <h2>Top Armada Terpopuler</h2>
                                <p>Armada terlaris pada periode ini.</p>
                            </div>
                        </div>
                        @if ($topCars->isEmpty())
                            <div class="empty">Belum ada data armada populer pada periode ini.</div>
                        @else
                            <div class="rank-list">
                                @foreach ($topCars as $car)
                                    <div class="rank-item">
                                        <div>
                                            <strong>{{ $car['name'] }}</strong>
                                            <span>Rp {{ number_format((int) ($car['revenue'] ?? 0), 0, ',', '.') }}</span>
                                        </div>
                                        <span class="pill">{{ $car['count'] }}x</span>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <div class="list-card">
                        <div class="section-header">
                            <div>
                                <h2>Status Ketersediaan Armada</h2>
                                <p>Komposisi armada pada saat export dibuat.</p>
                            </div>
                        </div>
                        <section class="summary-grid cols-3" style="margin-bottom:0;">
                            <div class="summary-card">
                                <p class="label">Total</p>
                                <p class="value">{{ (int) ($fleetOccupancy['total'] ?? 0) }}</p>
                            </div>
                            <div class="summary-card">
                                <p class="label">Tersedia</p>
                                <p class="value">{{ (int) ($fleetOccupancy['available'] ?? 0) }}</p>
                            </div>
                            <div class="summary-card">
                                <p class="label">Sibuk</p>
                                <p class="value">{{ (int) ($fleetOccupancy['unavailable'] ?? 0) }}</p>
                            </div>
                        </section>
                    </div>
                </section>

                <section class="section">
                    <div class="section-header">
                        <div>
                            <h2>Catatan Kinerja Periode</h2>
                            <p>Interpretasi singkat atas metrik utama pada periode aktif.</p>
                        </div>
                    </div>
                    <section class="summary-grid cols-3" style="margin-bottom:0;">
                        <div class="summary-card">
                            <p class="label">Komposisi Booking</p>
                            <p class="note">Booking berhasil mencapai {{ number_format((int) ($overviewSummary['success_bookings'] ?? 0), 0, ',', '.') }} transaksi, sementara booking gagal tercatat {{ number_format((int) ($overviewSummary['failed_bookings'] ?? 0), 0, ',', '.') }} transaksi pada periode ini.</p>
                        </div>
                        <div class="summary-card">
                            <p class="label">Arah Pendapatan</p>
                            <p class="note">Pendapatan masuk terkumpul sebesar Rp {{ number_format((int) ($overviewSummary['revenue_paid'] ?? 0), 0, ',', '.') }} dari {{ number_format((int) ($overviewSummary['paid_transactions'] ?? 0), 0, ',', '.') }} transaksi lunas.</p>
                        </div>
                        <div class="summary-card">
                            <p class="label">Kondisi Armada</p>
                            <p class="note">{{ (int) ($fleetOccupancy['available'] ?? 0) }} armada tersedia dari total {{ (int) ($fleetOccupancy['total'] ?? 0) }} unit, sehingga snapshot ini juga menggambarkan kapasitas layanan yang masih dapat digunakan.</p>
                        </div>
                    </section>
                </section>
            @elseif ($tab === 'revenue')
                <section class="summary-grid cols-3">
                    <div class="summary-card">
                        <p class="label">Total Pendapatan</p>
                        <p class="value">Rp {{ number_format((int) ($summary['total_revenue'] ?? 0), 0, ',', '.') }}</p>
                    </div>
                    <div class="summary-card">
                        <p class="label">Transaksi Lunas</p>
                        <p class="value">{{ number_format((int) ($summary['total_transactions'] ?? 0), 0, ',', '.') }}</p>
                    </div>
                    <div class="summary-card">
                        <p class="label">Rata-rata Transaksi</p>
                        <p class="value">Rp {{ number_format((int) ($summary['avg_transaction'] ?? 0), 0, ',', '.') }}</p>
                    </div>
                </section>

                <section class="section">
                    <div class="section-header">
                        <div>
                            <h2>Tren Pendapatan</h2>
                            <p>Visual pendapatan sesuai filter aktif.</p>
                        </div>
                    </div>
                    <div class="chart-card">
                        <div class="chart-svg">{!! $pdfCharts['revenue'] ?? '' !!}</div>
                    </div>
                </section>

                <section class="summary-grid cols-3">
                    <div class="summary-card">
                        <p class="label">Nilai per Transaksi</p>
                        <p class="note">Nilai rata-rata transaksi pada periode ini berada di kisaran Rp {{ number_format((int) ($summary['avg_transaction'] ?? 0), 0, ',', '.') }}, memberikan indikasi kualitas monetisasi per booking.</p>
                    </div>
                    <div class="summary-card">
                        <p class="label">Volume Pembayaran</p>
                        <p class="note">Tercatat {{ number_format((int) ($summary['total_transactions'] ?? 0), 0, ',', '.') }} transaksi lunas yang berkontribusi langsung terhadap pendapatan periode aktif.</p>
                    </div>
                    <div class="summary-card">
                        <p class="label">Fokus Analisis</p>
                        <p class="note">Gunakan tabel detail untuk menelusuri provider pembayaran, tipe rental, dan pelanggan yang paling berkontribusi pada revenue.</p>
                    </div>
                </section>
            @elseif ($tab === 'reservation')
                <section class="summary-grid cols-4">
                    <div class="summary-card">
                        <p class="label">Total Booking</p>
                        <p class="value">{{ number_format((int) ($summary['total_reservations'] ?? 0), 0, ',', '.') }}</p>
                    </div>
                    <div class="summary-card">
                        <p class="label">Selesai / Aktif</p>
                        <p class="value">{{ number_format((int) (($summary['returned'] ?? 0) + ($summary['ongoing'] ?? 0)), 0, ',', '.') }}</p>
                    </div>
                    <div class="summary-card">
                        <p class="label">Batal / Expired</p>
                        <p class="value">{{ number_format((int) (($summary['cancelled'] ?? 0) + ($summary['expired'] ?? 0)), 0, ',', '.') }}</p>
                        <p class="note">Rate: {{ $summary['cancellation_rate'] ?? 0 }}%</p>
                    </div>
                    <div class="summary-card">
                        <p class="label">Rata-rata Durasi</p>
                        <p class="value">{{ $summary['avg_duration'] ?? 0 }} Hari</p>
                    </div>
                </section>

                <section class="chart-grid cols-2 section">
                    <div class="chart-card">
                        <h3>Tren Reservasi</h3>
                        <div class="chart-svg">{!! $pdfCharts['reservations'] ?? '' !!}</div>
                    </div>
                    <div class="chart-card">
                        <h3>Distribusi Status Booking</h3>
                        <div class="chart-svg">{!! $pdfCharts['status'] ?? '' !!}</div>
                    </div>
                </section>

                <section class="summary-grid cols-3">
                    <div class="summary-card">
                        <p class="label">Kualitas Penyelesaian</p>
                        <p class="note">Gabungan booking selesai dan aktif mencapai {{ number_format((int) (($summary['returned'] ?? 0) + ($summary['ongoing'] ?? 0)), 0, ',', '.') }} transaksi, menunjukkan tingkat penyelesaian operasional pada periode aktif.</p>
                    </div>
                    <div class="summary-card">
                        <p class="label">Risiko Pembatalan</p>
                        <p class="note">Batal dan expired tercatat {{ number_format((int) (($summary['cancelled'] ?? 0) + ($summary['expired'] ?? 0)), 0, ',', '.') }} transaksi dengan rasio {{ $summary['cancellation_rate'] ?? 0 }}% terhadap total booking.</p>
                    </div>
                    <div class="summary-card">
                        <p class="label">Durasi Layanan</p>
                        <p class="note">Rata-rata durasi rental adalah {{ $summary['avg_duration'] ?? 0 }} hari, yang dapat dipakai untuk membaca pola pemakaian armada pada periode aktif.</p>
                    </div>
                </section>
            @elseif ($tab === 'fleet')
                <section class="summary-grid cols-3">
                    <div class="summary-card">
                        <p class="label">Total Armada</p>
                        <p class="value">{{ number_format((int) ($summary['total_fleet'] ?? 0), 0, ',', '.') }}</p>
                    </div>
                    <div class="summary-card">
                        <p class="label">Tersedia / Sibuk</p>
                        <p class="value">{{ (int) ($summary['available'] ?? 0) }} / {{ (int) ($summary['unavailable'] ?? 0) }}</p>
                    </div>
                    <div class="summary-card">
                        <p class="label">Performa Tertinggi</p>
                        <p class="note"><strong>Sewa:</strong> {{ $summary['top_rented'] ?? '-' }}<br><strong>Pendapatan:</strong> {{ $summary['top_revenue'] ?? '-' }}</p>
                    </div>
                </section>

                <section class="chart-grid cols-2 section">
                    <div class="chart-card">
                        <h3>Top Armada</h3>
                        <div class="chart-svg">{!! $pdfCharts['fleet_performance'] ?? '' !!}</div>
                    </div>
                    <div class="chart-card">
                        <h3>Status Armada</h3>
                        <div class="chart-svg">{!! $pdfCharts['fleet_status'] ?? '' !!}</div>
                    </div>
                </section>

                <section class="featured-box section">
                    <p class="eyebrow" style="margin-bottom:0;">Most Popular Fleet</p>
                    @if (!empty($featuredCar['image_url']))
                        <div class="featured-image-wrap">
                            <img class="featured-image" src="{{ $featuredCar['image_url'] }}" alt="{{ $featuredCar['name'] }}">
                        </div>
                    @endif
                    <h3>{{ $featuredCar['name'] }}</h3>
                    <p>{{ $featuredCar['description'] }}</p>
                    <div class="featured-metrics">
                        <div class="metric">
                            <span>Revenue</span>
                            <strong>Rp {{ number_format((int) ($featuredCar['revenue'] ?? 0), 0, ',', '.') }}</strong>
                        </div>
                        <div class="metric">
                            <span>Rental Count</span>
                            <strong>{{ (int) ($featuredCar['rentals_count'] ?? 0) }} Kali</strong>
                        </div>
                    </div>
                </section>

                <section class="summary-grid cols-3">
                    <div class="summary-card">
                        <p class="label">Kesiapan Armada</p>
                        <p class="note">Komposisi {{ (int) ($summary['available'] ?? 0) }} unit tersedia dan {{ (int) ($summary['unavailable'] ?? 0) }} unit sibuk memberi gambaran langsung atas kapasitas operasional saat periode laporan dievaluasi.</p>
                    </div>
                    <div class="summary-card">
                        <p class="label">Unit Unggulan</p>
                        <p class="note">Bagian featured menyoroti armada dengan kombinasi performa booking dan pendapatan terbaik, sehingga hasil export terasa seperti laporan evaluasi, bukan tampilan ulang dashboard.</p>
                    </div>
                    <div class="summary-card">
                        <p class="label">Arah Analisis</p>
                        <p class="note">Gunakan tabel detail untuk membandingkan status mobil, frekuensi sewa, dan kontribusi revenue antar armada secara lebih granular.</p>
                    </div>
                </section>
            @endif

            @if ($tab !== 'overview')
                <section class="section">
                    <div class="section-header">
                        <div>
                            <h2>Data Detail</h2>
                            <p>Seluruh data mengikuti filter yang aktif saat export.</p>
                        </div>
                    </div>

                    @if ($exportRows->isEmpty())
                        <div class="empty">Tidak ada data dalam periode yang dipilih.</div>
                    @else
                        <table>
                            <thead>
                                @if ($tab === 'revenue')
                                    <tr>
                                        <th>Tanggal Pembayaran</th>
                                        <th>Customer</th>
                                        <th>Mobil</th>
                                        <th>Plat</th>
                                        <th>Tipe</th>
                                        <th>Provider</th>
                                        <th>Status</th>
                                        <th>Amount</th>
                                    </tr>
                                @elseif ($tab === 'reservation')
                                    <tr>
                                        <th>Tanggal Booking</th>
                                        <th>Customer</th>
                                        <th>Mobil</th>
                                        <th>Plat</th>
                                        <th>Start</th>
                                        <th>End</th>
                                        <th>Returned At</th>
                                        <th>Tipe</th>
                                        <th>Verifikasi</th>
                                        <th>Status</th>
                                        <th>Total</th>
                                    </tr>
                                @elseif ($tab === 'fleet')
                                    <tr>
                                        <th>Brand & Model</th>
                                        <th>Plat</th>
                                        <th>Tipe</th>
                                        <th>Transmisi</th>
                                        <th>Status</th>
                                        <th>Jumlah Sewa</th>
                                        <th>Total Pendapatan</th>
                                        <th>Terakhir Disewa</th>
                                    </tr>
                                @endif
                            </thead>
                            <tbody>
                                @if ($tab === 'revenue')
                                    @foreach ($exportRows as $history)
                                        <tr>
                                            <td>{{ $history->created_at->format('d M Y H:i') }}</td>
                                            <td>{{ $history->rental?->user?->name ?? '-' }}</td>
                                            <td>{{ trim(($history->rental?->car?->brand ?? '') . ' ' . ($history->rental?->car?->name ?? '')) }}</td>
                                            <td>{{ $history->rental?->car?->license_plate ?? '-' }}</td>
                                            <td>{{ $history->rental?->type === \App\Enums\RentalType::SELF_DRIVE ? 'Self Drive' : 'With Driver' }}</td>
                                            <td>{{ strtoupper((string) ($history->provider ?? '-')) }}</td>
                                            <td>Lunas</td>
                                            <td>Rp {{ number_format((int) $history->amount, 0, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                @elseif ($tab === 'reservation')
                                    @foreach ($exportRows as $rental)
                                        <tr>
                                            <td>{{ $rental->created_at->format('d M Y H:i') }}</td>
                                            <td>{{ $rental->user?->name ?? '-' }}</td>
                                            <td>{{ trim(($rental->car?->brand ?? '') . ' ' . ($rental->car?->name ?? '')) }}</td>
                                            <td>{{ $rental->car?->license_plate ?? '-' }}</td>
                                            <td>{{ $rental->start_date?->format('d M Y') ?? '-' }}</td>
                                            <td>{{ $rental->end_date?->format('d M Y') ?? '-' }}</td>
                                            <td>{{ $rental->returned_at?->format('d M Y H:i') ?? '-' }}</td>
                                            <td>{{ $rental->type === \App\Enums\RentalType::SELF_DRIVE ? 'Self Drive' : 'With Driver' }}</td>
                                            <td>{{ $rental->verification_status?->value ?? '-' }}</td>
                                            <td>{{ $rental->status?->value ?? '-' }}</td>
                                            <td>Rp {{ number_format((int) $rental->total_price, 0, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                @elseif ($tab === 'fleet')
                                    @foreach ($exportRows as $car)
                                        <tr>
                                            <td>{{ trim(($car['brand'] ?? '') . ' ' . ($car['name'] ?? '')) }}</td>
                                            <td>{{ $car['license_plate'] ?? '-' }}</td>
                                            <td>{{ str($car['vehicle_type'] ?? '-')->headline() }}</td>
                                            <td>{{ str($car['transmission'] ?? '-')->headline() }}</td>
                                            <td>{{ str($car['status'] ?? '-')->headline() }}</td>
                                            <td>{{ $car['rentals_count'] ?? 0 }}x</td>
                                            <td>Rp {{ number_format((int) ($car['total_revenue'] ?? 0), 0, ',', '.') }}</td>
                                            <td>{{ $car['last_rented'] ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    @endif
                </section>
            @endif
        </div>
    </div>

    <script>
        window.addEventListener('load', function () {
            window.setTimeout(function () {
                window.print();
            }, 300);
        });
    </script>
</body>
</html>
