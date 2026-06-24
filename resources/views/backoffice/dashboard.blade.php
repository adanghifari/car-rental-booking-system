<x-backoffice.layout title="Backoffice Dashboard" :admin="$admin" active="dashboard">
    @php
        $fleetTotal = max($fleet['available'] + $fleet['rented'] + $fleet['maintenance'], 1);
        $occupancyRate = (int) round(($fleet['rented'] / $fleetTotal) * 100);
        $radius = 58;
        $circumference = 2 * pi() * $radius;
        $availableStroke = $circumference * ($fleet['available'] / $fleetTotal);
        $rentedStroke = $circumference * ($fleet['rented'] / $fleetTotal);
        $pendingCount = $pendingVerifications->count();
        $returnsTodayCount = $returnsToday->count();
        $statusBanner = $overdueRentalsCount > 0 ? 'Perlu perhatian' : 'Operasional stabil';
        $statusTone = $overdueRentalsCount > 0 ? 'red' : 'green';
    @endphp

    <style>
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: 14px;
            margin-bottom: 20px;
        }

        .summary-card {
            padding: 16px 18px;
            min-height: 108px;
            display: flex;
            align-items: center;
            gap: 12px;
            background: rgba(255, 255, 255, 0.92);
            border: 1px solid rgba(219, 227, 239, 0.85);
            box-shadow: 0 18px 45px rgba(15, 29, 51, 0.04);
        }

        .summary-label {
            margin: 0;
            font-size: 12px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #64748b;
        }

        .summary-value {
            margin: 2px 0 0;
            font-size: 28px;
            line-height: 1;
            font-weight: 800;
            letter-spacing: -0.04em;
            color: #202636;
        }

        .summary-icon {
            width: 52px;
            height: 52px;
            border-radius: 16px;
            display: grid;
            place-items: center;
            flex: 0 0 auto;
        }

        .summary-copy {
            min-width: 0;
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.15fr) minmax(0, 0.85fr);
            gap: 20px;
        }

        .dashboard-column {
            display: flex;
            flex-direction: column;
            gap: 20px;
            min-width: 0;
        }

        .status-visual {
            display: flex;
            align-items: center;
            gap: 20px;
            justify-content: space-between;
            flex-wrap: wrap;
        }

        .donut-wrap {
            width: 132px;
            height: 132px;
            display: grid;
            place-items: center;
            flex: 0 0 auto;
        }

        .status-summary {
            display: grid;
            gap: 10px;
            flex: 1 1 160px;
            min-width: 160px;
        }

        .top-summary {
            text-align: center;
            padding: 28px 12px;
            color: var(--text);
        }

        .top-summary strong {
            font-size: 15px;
            font-weight: 700;
            display: block;
            margin-bottom: 4px;
        }

        .top-summary p {
            margin: 0;
            color: var(--muted);
            font-size: 13px;
            line-height: 1.45;
        }

        .timeline-item {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 14px 0;
            border-bottom: 1px solid rgba(219, 227, 239, 0.55);
        }

        .timeline-item:last-child {
            border-bottom: 0;
        }

        .timeline-dot {
            width: 9px;
            height: 9px;
            border-radius: 50%;
            margin-top: 8px;
            flex-shrink: 0;
        }

        .activity-title {
            margin: 0;
            font-size: 14px;
            font-weight: 700;
            color: var(--text);
        }

        .activity-subtitle {
            margin: 3px 0 0;
            font-size: 12px;
            color: var(--muted);
        }

        .activity-time {
            margin: 4px 0 0;
            font-size: 12px;
            color: var(--muted);
        }

        @media (max-width: 1180px) {
            .summary-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .dashboard-grid {
                grid-template-columns: 1fr;
            }
        }

    @media (max-width: 640px) {
            .summary-grid {
                grid-template-columns: 1fr;
            }
        }

        .dashboard-hero {
            margin-bottom: 18px;
        }

        .dashboard-hero-title {
            margin: 0 0 8px;
            font-size: 34px;
            font-weight: 800;
            letter-spacing: -0.05em;
            line-height: 1;
            color: #202636;
        }

        .dashboard-hero-subtitle {
            margin: 0;
            font-size: 14px;
            color: #667085;
            line-height: 1.45;
            max-width: 760px;
        }

        .dashboard-welcome {
            animation: dashboardWelcomeIn 860ms cubic-bezier(0.22, 1, 0.36, 1) both;
        }

        .dashboard-welcome-sub {
            animation: dashboardWelcomeIn 980ms cubic-bezier(0.22, 1, 0.36, 1) both;
            animation-delay: 90ms;
        }

        .summary-grid .summary-card,
        .dashboard-grid .card {
            animation: dashboardPanelIn 760ms cubic-bezier(0.22, 1, 0.36, 1) both;
            animation-delay: var(--panel-delay, 0ms);
        }

        @keyframes dashboardWelcomeIn {
            0% {
                opacity: 0;
                transform: translateY(18px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes dashboardPanelIn {
            0% {
                opacity: 0;
                transform: translateY(20px) scale(0.985);
            }
            100% {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }
    </style>

    <section class="dashboard-hero">
        <h1 class="dashboard-hero-title dashboard-welcome">Pantau Operasional</h1>
        <p class="dashboard-hero-subtitle dashboard-welcome-sub">Kelola dan pantau kondisi armada, verifikasi, serta aktivitas operasional rental secara ringkas dalam satu layar.</p>
    </section>

    <section class="summary-grid">
        <article class="card summary-card" style="--panel-delay: 120ms">
            <div class="summary-icon" style="background: rgba(99, 102, 241, 0.12); color: #4f46e5;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M4 7h16v10H4z" />
                    <path d="M7 7V5h10v2" />
                    <path d="M8 12h8" />
                </svg>
            </div>
            <div class="summary-copy">
                <p class="summary-label">Total Armada</p>
                <p class="summary-value">{{ $fleetTotal }}</p>
            </div>
        </article>

        <article class="card summary-card" style="--panel-delay: 190ms">
            <div class="summary-icon" style="background: rgba(251, 191, 36, 0.16); color: #d97706;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 8v4l3 2" />
                    <circle cx="12" cy="12" r="8" />
                </svg>
            </div>
            <div class="summary-copy">
                <p class="summary-label">Butuh Review</p>
                <p class="summary-value">{{ $pendingCount }}</p>
            </div>
        </article>

        <article class="card summary-card" style="--panel-delay: 260ms">
            <div class="summary-icon" style="background: rgba(147, 197, 253, 0.16); color: #2563eb;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 6v6l4 2" />
                    <circle cx="12" cy="12" r="8" />
                </svg>
            </div>
            <div class="summary-copy">
                <p class="summary-label">Pengembalian Hari Ini</p>
                <p class="summary-value">{{ $returnsTodayCount }}</p>
            </div>
        </article>

        <article class="card summary-card" style="--panel-delay: 330ms">
            <div class="summary-icon" style="background: rgba(110, 231, 183, 0.18); color: #059669;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M4 17h16M6 17a2 2 0 1 0 4 0m4 0a2 2 0 1 0 4 0M3 17v-4l2-4h14l2 4v4" />
                </svg>
            </div>
            <div class="summary-copy">
                <p class="summary-label">Armada Disewa</p>
                <p class="summary-value">{{ $fleet['rented'] }}</p>
            </div>
        </article>

        <article class="card summary-card" style="--panel-delay: 400ms">
            <div class="summary-icon" style="background: rgba(248, 180, 130, 0.20); color: #c2410c;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M10 14h4" />
                    <path d="M12 7v7" />
                    <circle cx="12" cy="12" r="8" />
                </svg>
            </div>
            <div class="summary-copy">
                <p class="summary-label">Armada Maintenance</p>
                <p class="summary-value">{{ $fleet['maintenance'] }}</p>
            </div>
        </article>
    </section>

    <section class="dashboard-grid">
        <div class="dashboard-column">
            <section class="card" style="--panel-delay: 220ms">
                <div class="section-head" style="margin-bottom: 16px;">
                    <h2 class="section-title">Pengembalian Terlambat</h2>
                    @if ($overdueRentalsCount > 0)
                        <span class="pill red" style="background: rgba(239, 68, 68, 0.12); color: var(--red); font-weight: 700;">{{ $overdueRentalsCount }} Terlambat</span>
                    @else
                        <span class="pill" style="background: rgba(226, 232, 240, 0.65); color: #64748b; font-weight: 700;">Aman</span>
                    @endif
                </div>

                @if ($overdueRentalsCount === 0)
                    <div class="top-summary">
                        <strong>Tidak ada armada terlambat.</strong>
                        <p>Semua rental aktif berjalan tepat waktu.</p>
                    </div>
                @else
                    <div style="display: grid; gap: 12px;">
                        @foreach ($overdueRentalsPreview as $rental)
                            @php
                                $overdueDays = $rental->end_date ? $rental->end_date->diffInDays(now()->startOfDay()) : 0;
                            @endphp
                            <div class="timeline-item" style="padding: 0; border-bottom: 0;">
                                <div class="timeline-dot" style="background: var(--red);"></div>
                                <div style="display: flex; flex-direction: column; flex-grow: 1; min-width: 0;">
                                    <p class="activity-title">Terlambat {{ $overdueDays }} hari</p>
                                    <p class="activity-subtitle">{{ trim(($rental->user?->name ?? 'User') . ' • ' . ($rental->car?->name ?? 'Armada')) }}</p>
                                    <p class="activity-time">{{ $rental->car?->license_plate ?? '-' }}</p>
                                </div>
                                <span class="pill red" style="font-size: 10px; padding: 2px 6px; align-self: flex-start;">Overdue</span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </section>

            <section class="card" style="--panel-delay: 320ms">
                <div class="section-head" style="margin-bottom: 16px;">
                    <h2 class="section-title">Menunggu Verifikasi</h2>
                    <span class="pill amber" style="background: rgba(245, 158, 11, 0.14); color: #b96e00; font-weight: 700;">Butuh Review</span>
                </div>

                @if ($pendingVerifications->isEmpty())
                    <div class="top-summary">
                        <strong>Tidak ada verifikasi identitas baru.</strong>
                        <p>Semua identitas pelanggan telah diproses.</p>
                    </div>
                @else
                    <div style="display: grid; gap: 12px;">
                        @foreach ($pendingVerifications as $rental)
                            <div class="timeline-item" style="padding: 0; border-bottom: 0;">
                                <div class="timeline-dot" style="background: var(--amber);"></div>
                                <div style="display: flex; flex-direction: column; flex-grow: 1; min-width: 0;">
                                    <p class="activity-title">{{ $rental->user?->name ?? 'User' }}</p>
                                    <p class="activity-subtitle">{{ trim(($rental->car?->brand ?? '') . ' ' . ($rental->car?->name ?? '')) }}</p>
                                    <p class="activity-time">{{ $rental->car?->license_plate ?? '-' }}</p>
                                </div>
                                <span class="pill amber" style="font-size: 10px; padding: 2px 6px; align-self: flex-start;">Review</span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </section>

            <section class="card" style="--panel-delay: 420ms">
                <div class="section-head" style="margin-bottom: 16px;">
                    <h2 class="section-title">Pengembalian Hari Ini</h2>
                    <span class="pill blue" style="background: rgba(63, 94, 215, 0.12); color: var(--blue); font-weight: 700;">Hari Ini</span>
                </div>

                @if ($returnsToday->isEmpty())
                    <div class="top-summary">
                        <strong>Tidak ada pengembalian hari ini.</strong>
                        <p>Tidak ada jadwal pengembalian armada untuk hari ini.</p>
                    </div>
                @else
                    <div style="display: grid; gap: 12px;">
                        @foreach ($returnsToday as $rental)
                            <div class="timeline-item" style="padding: 0; border-bottom: 0;">
                                <div class="timeline-dot" style="background: var(--blue);"></div>
                                <div style="display: flex; flex-direction: column; flex-grow: 1; min-width: 0;">
                                    <p class="activity-title">{{ trim(($rental->car?->brand ?? '') . ' ' . ($rental->car?->name ?? '')) }}</p>
                                    <p class="activity-subtitle">Pelanggan: {{ $rental->user?->name ?? 'User' }}</p>
                                    <p class="activity-time">{{ $rental->car?->license_plate ?? '-' }}</p>
                                </div>
                                <span class="pill blue" style="font-size: 10px; padding: 2px 6px; align-self: flex-start;">Hari Ini</span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </section>
        </div>

        <div class="dashboard-column">
            <section class="card" style="--panel-delay: 280ms">
                <div class="section-head" style="margin-bottom: 16px;">
                    <h2 class="section-title">Status Armada</h2>
                </div>

                <div class="status-visual">
                    <div class="donut-wrap">
                        <svg width="132" height="132" viewBox="0 0 180 180" aria-hidden="true">
                            <circle cx="90" cy="90" r="{{ $radius }}" fill="none" stroke="#e7edf6" stroke-width="14"></circle>
                            <circle cx="90" cy="90" r="{{ $radius }}" fill="none" stroke="#1dbb84" stroke-width="14"
                                stroke-linecap="round" stroke-dasharray="{{ $availableStroke }} {{ $circumference }}"
                                transform="rotate(-90 90 90)"></circle>
                            <circle cx="90" cy="90" r="{{ $radius }}" fill="none" stroke="#3f5ed7" stroke-width="14"
                                stroke-linecap="round" stroke-dasharray="{{ $rentedStroke }} {{ $circumference }}"
                                stroke-dashoffset="-{{ $availableStroke + 10 }}" transform="rotate(-90 90 90)"></circle>
                            <circle cx="90" cy="90" r="42" fill="#fff"></circle>
                            <text x="90" y="88" text-anchor="middle" font-size="28" font-weight="700" fill="#202636">{{ $fleetTotal }}</text>
                            <text x="90" y="106" text-anchor="middle" font-size="12" fill="#7b869b">Total</text>
                        </svg>
                    </div>

                    <div class="status-summary">
                        <div class="status-row" style="display: flex; align-items: center; justify-content: space-between; gap: 10px;">
                            <div class="status-label"><span class="dot" style="background: var(--green)"></span>Tersedia</div>
                            <strong>{{ $fleet['available'] }}</strong>
                        </div>
                        <div class="status-row" style="display: flex; align-items: center; justify-content: space-between; gap: 10px;">
                            <div class="status-label"><span class="dot" style="background: var(--blue)"></span>Disewa</div>
                            <strong>{{ $fleet['rented'] }}</strong>
                        </div>
                        <div class="status-row" style="display: flex; align-items: center; justify-content: space-between; gap: 10px;">
                            <div class="status-label"><span class="dot" style="background: #cbd5e1"></span>Maintenance</div>
                            <strong>{{ $fleet['maintenance'] }}</strong>
                        </div>
                    </div>
                </div>
            </section>

            <section class="card" style="--panel-delay: 380ms">
                <div class="section-head" style="margin-bottom: 12px;">
                    <h2 class="section-title">Aktivitas Terbaru</h2>
                    <a href="{{ route('backoffice.reservations') }}" style="color: var(--blue); font-size: 13px; font-weight: 700; text-decoration: none;">Semua</a>
                </div>

                <div>
                    @if ($recentActivities->isEmpty())
                        <p class="empty-text" style="text-align: center; padding: 24px 0;">Belum ada aktivitas baru.</p>
                    @else
                        @foreach ($recentActivities as $activity)
                            <div class="timeline-item">
                                <div class="timeline-dot" style="background: var(--{{ $activity['status']['tone'] }});"></div>
                                <div style="display: flex; flex-direction: column; flex-grow: 1; min-width: 0;">
                                    <span class="activity-title">{{ $activity['activity'] }}</span>
                                    <span class="activity-subtitle">{{ $activity['subtitle'] }}</span>
                                    <span class="activity-time">{{ $activity['time'] }}</span>
                                </div>
                                <span class="pill {{ $activity['status']['tone'] }}" style="font-size: 10px; padding: 2px 6px; align-self: flex-start;">{{ $activity['status']['label'] }}</span>
                            </div>
                        @endforeach
                    @endif
                </div>
            </section>
        </div>
    </section>
</x-backoffice.layout>
