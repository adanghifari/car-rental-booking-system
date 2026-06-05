<x-backoffice.layout title="Manajemen User" :admin="$admin" active="users" search-placeholder="Cari nama atau email...">
    <section class="page-head">
        <div>
            <h1 class="page-title">Manajemen User</h1>
            <p class="page-subtitle">Kelola seluruh data customer rental mobil premium Anda.</p>
        </div>

        <a href="#" class="primary-button">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                <circle cx="9" cy="7" r="4"/>
                <path d="M19 8v6"/>
                <path d="M16 11h6"/>
            </svg>
            <span>Tambah User Baru</span>
        </a>
    </section>

    <section class="card filter-bar">
        <div class="filter-box">
            <span class="filter-label">Status:</span>
            <span class="filter-value">Semua Status</span>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#7b869b" stroke-width="2">
                <path d="m6 9 6 6 6-6"/>
            </svg>
        </div>

        <div class="filter-box">
            <span class="filter-label">Membership:</span>
            <span class="filter-value">Semua Tingkat</span>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#7b869b" stroke-width="2">
                <path d="m6 9 6 6 6-6"/>
            </svg>
        </div>

        <div class="filter-box">
            <span class="filter-label">Urutkan:</span>
            <span class="filter-value">Paling Baru</span>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#7b869b" stroke-width="2">
                <path d="m6 9 6 6 6-6"/>
            </svg>
        </div>
    </section>

    <section class="card table-card">
        <table class="user-table">
            <thead>
                <tr>
                    <th>Customer</th>
                    <th>Kontak</th>
                    <th>Membership</th>
                    <th style="text-align: right;">Total Transaksi</th>
                    <th>Status</th>
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
                        <td>
                            <span class="table-pill {{ strtolower($userItem['membership']) }}">{{ $userItem['membership'] }}</span>
                        </td>
                        <td>
                            <div class="money">
                                <div>Rp</div>
                                <div>{{ number_format($userItem['total_transactions'], 0, ',', '.') }}</div>
                            </div>
                        </td>
                        <td>
                            <span class="table-pill {{ $userItem['status_tone'] }}">{{ $userItem['status'] }}</span>
                        </td>
                        <td>
                            <div class="muted-stack" style="color: #4a586f;">
                                <div>{{ $userItem['registered_day'] }}</div>
                                <div>{{ $userItem['registered_year'] }}</div>
                            </div>
                        </td>
                        <td>
                            <div class="action-list">
                                <svg class="action-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12Z"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>
                                <svg class="action-icon edit" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M12 20h9"/>
                                    <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5Z"/>
                                </svg>
                                @if ($userItem['status_tone'] === 'suspend')
                                    <svg class="action-icon approve" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <circle cx="12" cy="12" r="10"/>
                                        <path d="m9 12 2 2 4-4"/>
                                    </svg>
                                @else
                                    <svg class="action-icon block" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <circle cx="12" cy="12" r="10"/>
                                        <path d="m4.9 4.9 14.2 14.2"/>
                                    </svg>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">
                            <div style="padding: 24px 0; color: #7b869b;">Belum ada user untuk ditampilkan.</div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="table-footer">
            <span>Menampilkan {{ $users->firstItem() ?? 0 }}-{{ $users->lastItem() ?? 0 }} dari {{ $users->total() }} user</span>

            <div class="pagination">
                <span class="page-link muted">‹</span>
                @foreach ($pagination as $pageItem)
                    @if ($pageItem === '...')
                        <span class="page-link muted">...</span>
                    @else
                        <span class="page-link {{ $pageItem === $users->currentPage() ? 'active' : '' }}">{{ $pageItem }}</span>
                    @endif
                @endforeach
                <span class="page-link">›</span>
            </div>
        </div>
    </section>
</x-backoffice.layout>
