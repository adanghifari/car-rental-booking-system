<x-backoffice.layout title="Backoffice Dashboard" :admin="$admin" active="dashboard">
    @php
        $maxRentalValue = max(collect($chartRentals)->max('value') ?: 0, 1);
        $maxRevenueValue = max(collect($chartRevenue)->max('value') ?: 0, 1);
        $fleetTotal = max($fleet['available'] + $fleet['rented'] + $fleet['maintenance'], 1);
        $radius = 58;
        $circumference = 2 * pi() * $radius;
        $availableStroke = $circumference * ($fleet['available'] / $fleetTotal);
        $rentedStroke = $circumference * ($fleet['rented'] / $fleetTotal);
    @endphp

    <section class="stats-grid">
        <div class="card stat-card">
            <div class="stat-top">
                <div class="stat-icon" style="background: rgba(63, 94, 215, 0.12); color: var(--blue);">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                </div>
                <span class="delta up">+12%</span>
            </div>
            <div class="stat-label">Total User</div>
            <div class="stat-value">{{ number_format($stats['total_users']) }}</div>
        </div>

        <div class="card stat-card">
            <div class="stat-top">
                <div class="stat-icon" style="background: rgba(32, 38, 54, 0.08); color: #202636;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 16H9m10 0h2m-7 0h1m-9 0h1m0 0a2 2 0 1 0 4 0m-4 0a2 2 0 1 1 4 0m8 0a2 2 0 1 0 4 0m-4 0a2 2 0 1 1 4 0M3 12l2-5h13l3 5"/>
                    </svg>
                </div>
            </div>
            <div class="stat-label">Total Mobil</div>
            <div class="stat-value">{{ number_format($stats['total_cars']) }}</div>
        </div>

        <div class="card stat-card">
            <div class="stat-top">
                <div class="stat-icon" style="background: rgba(29, 187, 132, 0.12); color: var(--green);">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="9"/>
                        <path d="m9 12 2 2 4-4"/>
                    </svg>
                </div>
            </div>
            <div class="stat-label">Mobil Tersedia</div>
            <div class="stat-value" style="color: var(--green);">{{ number_format($stats['available_cars']) }}</div>
        </div>

        <div class="card stat-card">
            <div class="stat-top">
                <div class="stat-icon" style="background: rgba(245, 158, 11, 0.12); color: var(--amber);">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M7 7h10l2 5H5l2-5Z"/>
                        <path d="M5 12v5a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-5"/>
                    </svg>
                </div>
            </div>
            <div class="stat-label">Mobil Disewa</div>
            <div class="stat-value" style="color: var(--amber);">{{ number_format($stats['rented_cars']) }}</div>
        </div>

        <div class="card stat-card">
            <div class="stat-top">
                <div class="stat-icon" style="background: rgba(63, 94, 215, 0.12); color: var(--blue);">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="2" y="7" width="20" height="14" rx="2"/>
                        <path d="M16 3v4M8 3v4"/>
                        <path d="M2 11h20"/>
                    </svg>
                </div>
                <span class="delta up">+8%</span>
            </div>
            <div class="stat-label">Pendapatan Bulan Ini</div>
            <div class="stat-value">${{ number_format($stats['monthly_revenue'] / 1000, 0) }}k</div>
        </div>

        <div class="card stat-card">
            <div class="stat-top">
                <div class="stat-icon" style="background: rgba(63, 94, 215, 0.12); color: var(--blue);">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M8 2v4m8-4v4M3 10h18"/>
                        <rect x="3" y="4" width="18" height="18" rx="2"/>
                    </svg>
                </div>
            </div>
            <div class="stat-label">Booking Hari Ini</div>
            <div class="stat-value">{{ number_format($stats['bookings_today']) }}</div>
        </div>
    </section>

    <section class="content-grid">
        <div class="card">
            <div class="section-head">
                <h2 class="section-title">Penyewaan per Bulan</h2>
                <div class="chip-group">
                    <span class="chip">6 Bulan Terakhir</span>
                    <span class="chip active">{{ now()->year }}</span>
                </div>
            </div>

            <div class="chart-shell">
                <svg class="chart-svg" viewBox="0 0 680 280" preserveAspectRatio="none" aria-hidden="true">
                    <defs>
                        <linearGradient id="lineFill" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="#4c68dc" stop-opacity="0.22"/>
                            <stop offset="100%" stop-color="#4c68dc" stop-opacity="0.02"/>
                        </linearGradient>
                    </defs>
                    @for ($i = 0; $i < 5; $i++)
                        <line x1="28" y1="{{ 36 + ($i * 46) }}" x2="652" y2="{{ 36 + ($i * 46) }}" stroke="#e5ebf4" stroke-dasharray="6 8"/>
                    @endfor

                    @php
                        $rentalPoints = collect($chartRentals)->values()->map(function ($point, $index) use ($maxRentalValue) {
                            $x = 44 + ($index * 118);
                            $y = 224 - (($point['value'] / $maxRentalValue) * 140);
                            return ['x' => round($x, 2), 'y' => round($y, 2), 'label' => $point['label']];
                        });
                        $polyline = $rentalPoints->map(fn ($point) => $point['x'].','.$point['y'])->implode(' ');
                        $areaPoints = '44,232 '.$polyline.' 634,232';
                    @endphp
                    <polygon points="{{ $areaPoints }}" fill="url(#lineFill)"></polygon>
                    <polyline points="{{ $polyline }}" fill="none" stroke="#3f5ed7" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"></polyline>

                    @foreach ($rentalPoints as $point)
                        <circle cx="{{ $point['x'] }}" cy="{{ $point['y'] }}" r="4" fill="#3f5ed7"></circle>
                        <text x="{{ $point['x'] }}" y="258" fill="#7b869b" font-size="12" text-anchor="middle">{{ $point['label'] }}</text>
                    @endforeach
                </svg>
            </div>
        </div>

        <div class="card status-card">
            <div class="section-head">
                <h2 class="section-title">Status Armada</h2>
            </div>

            <div class="donut-wrap">
                <svg width="220" height="220" viewBox="0 0 180 180" aria-hidden="true">
                    <circle cx="90" cy="90" r="{{ $radius }}" fill="none" stroke="#e7edf6" stroke-width="14"></circle>
                    <circle cx="90" cy="90" r="{{ $radius }}" fill="none" stroke="#1dbb84" stroke-width="14" stroke-linecap="round" stroke-dasharray="{{ $availableStroke }} {{ $circumference }}" transform="rotate(-90 90 90)"></circle>
                    <circle cx="90" cy="90" r="{{ $radius }}" fill="none" stroke="#3f5ed7" stroke-width="14" stroke-linecap="round" stroke-dasharray="{{ $rentedStroke }} {{ $circumference }}" stroke-dashoffset="-{{ $availableStroke + 10 }}" transform="rotate(-90 90 90)"></circle>
                    <circle cx="90" cy="90" r="42" fill="#fff"></circle>
                    <text x="90" y="88" text-anchor="middle" font-size="28" font-weight="700" fill="#202636">{{ $stats['total_cars'] }}</text>
                    <text x="90" y="106" text-anchor="middle" font-size="12" fill="#7b869b">Total</text>
                </svg>
            </div>

            <div class="status-list">
                <div class="status-row">
                    <div class="status-label"><span class="dot" style="background: var(--green)"></span>Tersedia</div>
                    <strong>{{ $fleet['available'] }}</strong>
                </div>
                <div class="status-row">
                    <div class="status-label"><span class="dot" style="background: var(--blue)"></span>Disewa</div>
                    <strong>{{ $fleet['rented'] }}</strong>
                </div>
                <div class="status-row">
                    <div class="status-label"><span class="dot" style="background: #cbd5e1"></span>Maintenance</div>
                    <strong>{{ $fleet['maintenance'] }}</strong>
                </div>
            </div>
        </div>
    </section>

    <section class="bottom-grid">
        <div class="card">
            <div class="section-head">
                <h2 class="section-title">Pendapatan Bulanan</h2>
                <svg width="16" height="16" viewBox="0 0 24 24" fill="#7b869b">
                    <circle cx="12" cy="5" r="2"></circle>
                    <circle cx="12" cy="12" r="2"></circle>
                    <circle cx="12" cy="19" r="2"></circle>
                </svg>
            </div>

            <div class="chart-shell" style="min-height: 260px;">
                <svg class="chart-svg" viewBox="0 0 420 250" preserveAspectRatio="none" aria-hidden="true">
                    @foreach (collect($chartRevenue)->values() as $index => $point)
                        @php
                            $barHeight = max(26, ($point['value'] / $maxRevenueValue) * 140);
                            $x = 34 + ($index * 62);
                            $y = 190 - $barHeight;
                            $isActive = $index === collect($chartRevenue)->search(fn ($item) => $item['value'] === collect($chartRevenue)->max('value'));
                        @endphp
                        <rect x="{{ $x }}" y="{{ $y }}" width="38" height="{{ $barHeight }}" rx="10" fill="{{ $isActive ? '#3f5ed7' : '#e8eef8' }}"></rect>
                        <text x="{{ $x + 19 }}" y="222" fill="#7b869b" font-size="12" text-anchor="middle">{{ $point['label'] }}</text>
                    @endforeach
                </svg>
            </div>
        </div>

        <div class="card">
            <div class="section-head">
                <h2 class="section-title">Aktivitas Terbaru</h2>
                <a href="#" style="color: var(--blue); font-size: 13px; font-weight: 700; text-decoration: none;">Lihat Semua</a>
            </div>

            @if ($recentActivities->isEmpty())
                <p class="empty-text">Belum ada aktivitas rental yang bisa ditampilkan.</p>
            @else
                <table class="table">
                    <thead>
                        <tr>
                            <th>Aktivitas</th>
                            <th>Entitas</th>
                            <th>Waktu</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($recentActivities as $activity)
                            <tr>
                                <td>
                                    <div class="activity-title">{{ $activity['activity'] }}</div>
                                    <div class="activity-subtitle">{{ $activity['subtitle'] }}</div>
                                </td>
                                <td>{{ $activity['entity'] }}</td>
                                <td>{{ $activity['time'] }}</td>
                                <td>
                                    <span class="pill {{ $activity['status']['tone'] }}">{{ $activity['status']['label'] }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </section>

    <section class="card featured-card">
        <div class="featured-layout">
            <div>
                <span class="eyebrow">Most Popular Fleet</span>
                <h2 class="featured-title">{{ $featuredCar['name'] }}</h2>
                <p class="featured-text">{{ $featuredCar['description'] }}</p>

                <div class="featured-metrics">
                    <div>
                        <div class="featured-label">Revenue</div>
                        <strong>${{ number_format($featuredCar['revenue'] / 1000, 1) }}k</strong>
                    </div>
                    <div>
                        <div class="featured-label">Rental Count</div>
                        <strong>{{ $featuredCar['rentals_count'] }} Times</strong>
                    </div>
                </div>
            </div>

            <div class="vehicle-stage">
                <svg viewBox="0 0 560 320" aria-hidden="true">
                    <defs>
                        <linearGradient id="carPaint" x1="0" y1="0" x2="1" y2="1">
                            <stop offset="0%" stop-color="#121922"/>
                            <stop offset="55%" stop-color="#3b495a"/>
                            <stop offset="100%" stop-color="#111827"/>
                        </linearGradient>
                    </defs>
                    <rect x="0" y="0" width="560" height="320" fill="transparent"/>
                    <ellipse cx="280" cy="220" rx="200" ry="28" fill="rgba(0, 0, 0, 0.18)"/>
                    <g transform="translate(72 118)">
                        <path d="M35 88c0-16 11-29 26-32l41-9c18-33 53-54 94-54h74c29 0 57 10 79 27l41 31h41c19 0 35 16 35 35v37c0 9-7 16-16 16h-18c-3-25-24-44-50-44-25 0-46 19-49 44H143c-3-25-24-44-49-44-26 0-47 19-50 44H35Z" fill="url(#carPaint)"/>
                        <path d="M125 45c17-24 40-36 70-36h65c20 0 39 7 54 19l38 29H111Z" fill="rgba(228, 236, 249, 0.82)"/>
                        <path d="M154 48h58v-27h-33c-11 0-21 9-25 27Z" fill="rgba(175, 192, 220, 0.7)"/>
                        <path d="M227 21h42c14 0 28 5 39 13l20 14h-101Z" fill="rgba(175, 192, 220, 0.7)"/>
                        <circle cx="93" cy="129" r="35" fill="#111827"/>
                        <circle cx="93" cy="129" r="21" fill="#cbd5e1"/>
                        <circle cx="388" cy="129" r="35" fill="#111827"/>
                        <circle cx="388" cy="129" r="21" fill="#cbd5e1"/>
                        <circle cx="93" cy="129" r="8" fill="#94a3b8"/>
                        <circle cx="388" cy="129" r="8" fill="#94a3b8"/>
                        <rect x="455" y="78" width="20" height="10" rx="5" fill="#f8fafc"/>
                        <rect x="26" y="83" width="14" height="10" rx="4" fill="#f59e0b"/>
                    </g>
                    <rect x="26" y="250" width="508" height="44" rx="14" fill="rgba(255, 255, 255, 0.9)"/>
                    <text x="48" y="276" fill="#111827" font-size="22" font-weight="700">Premium Fleet</text>
                    <text x="454" y="276" fill="#111827" font-size="18" font-weight="700" text-anchor="end">{{ $stats['total_cars'] }} Units</text>
                </svg>
            </div>
        </div>
    </section>
</x-backoffice.layout>
