<x-backoffice.layout title="Reservasi" :admin="$admin" active="reservations" search-placeholder="Cari reservasi...">
	<section class="page-head">
		<div>
			<h1 class="page-title">Daftar Reservasi</h1>
			<p class="page-subtitle">Kelola dan pantau seluruh aktivitas reservasi penyewaan armada.</p>
		</div>

		<button type="button" class="primary-button" data-open-reservation-modal>
			<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
				<path d="M12 5v14"/>
				<path d="M5 12h14"/>
			</svg>
			<span>Reservasi Baru</span>
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

	<section style="display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 18px; margin-top: 18px; margin-bottom: 18px;">
		<div class="card" style="display: flex; align-items: center; gap: 16px; padding: 22px 24px;">
			<div style="width: 54px; height: 54px; border-radius: 16px; display: grid; place-items: center; background: rgba(63, 94, 215, 0.10); color: var(--blue); flex: 0 0 auto;">
				<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
					<path d="M4 7h16v10H4z" />
					<path d="M7 7V5h10v2" />
					<path d="M8 11h8" />
				</svg>
			</div>
			<div>
				<div class="page-subtitle" style="margin-bottom: 6px; font-weight: 700; text-transform: uppercase; letter-spacing: .04em;">Total</div>
				<div class="page-title" style="font-size: 28px; margin: 0; line-height: 1;">{{ number_format($summary['total'] ?? 0) }}</div>
			</div>
		</div>

		<div class="card" style="display: flex; align-items: center; gap: 16px; padding: 22px 24px;">
			<div style="width: 54px; height: 54px; border-radius: 16px; display: grid; place-items: center; background: rgba(255, 193, 7, 0.16); color: #d97706; flex: 0 0 auto;">
				<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
					<path d="M7 7h10v10H7z" />
					<path d="M12 8v5l3 2" />
				</svg>
			</div>
			<div>
				<div class="page-subtitle" style="margin-bottom: 6px; font-weight: 700; text-transform: uppercase; letter-spacing: .04em;">Pending</div>
				<div class="page-title" style="font-size: 28px; margin: 0; line-height: 1;">{{ number_format($summary['pending'] ?? 0) }}</div>
			</div>
		</div>

		<div class="card" style="display: flex; align-items: center; gap: 16px; padding: 22px 24px;">
			<div style="width: 54px; height: 54px; border-radius: 16px; display: grid; place-items: center; background: rgba(29, 187, 132, 0.12); color: var(--green); flex: 0 0 auto;">
				<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
					<circle cx="12" cy="12" r="8" />
					<path d="m9.5 12 1.8 1.8L15 10.2" />
				</svg>
			</div>
			<div>
				<div class="page-subtitle" style="margin-bottom: 6px; font-weight: 700; text-transform: uppercase; letter-spacing: .04em;">Selesai</div>
				<div class="page-title" style="font-size: 28px; margin: 0; line-height: 1;">{{ number_format($summary['completed'] ?? 0) }}</div>
			</div>
		</div>
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
						<tr>
							<td>{{ $rental['booking_id'] ?? $rental['id'] }}</td>
							<td>{{ $rental['customer_name'] ?? $rental['customer'] ?? '-' }}</td>
							<td>{{ $rental['car_model'] ?? $rental['car'] ?? '-' }}</td>
							<td>{{ $rental['start_date'] ?? '-' }} — {{ $rental['end_date'] ?? '-' }}</td>
							<td>Rp {{ number_format($rental['total_price'] ?? ($rental['total'] ?? 0), 0, ',', '.') }}</td>
							<td>{{ $rental['status'] ?? '-' }}</td>
							<td>
								<button type="button" class="text-action detail" data-reservation-detail data-reservation='@json($rental)'>Lihat</button>
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
			<div class="table-footer" style="border-top: 0; display:flex; justify-content:space-between; align-items:center;">
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
						<path d="M18 6 6 18"/>
						<path d="m6 6 12 12"/>
					</svg>
				</button>
			</div>

			<div class="modal-body">
				<form method="POST" action="{{ route('backoffice.reservations.store') }}" enctype="multipart/form-data" class="reservation-form">
					@csrf

					@if ($errors->any())
						<div style="padding: 14px; border-radius: 12px; background: rgba(239,68,68,0.06); color:#b42318; margin-bottom:12px;">
							<strong>Periksa input:</strong>
							<ul style="margin:6px 0 0 18px;">
								@foreach ($errors->all() as $error)
									<li>{{ $error }}</li>
								@endforeach
							</ul>
						</div>
					@endif

					<div class="form-grid">
						<div class="form-field">
							<label class="form-label" for="user_id">Pelanggan</label>
							<select id="user_id" name="user_id" class="form-select">
								<option value="">Pilih pelanggan</option>
								@foreach ($customers as $customer)
									<option value="{{ $customer['id'] }}" @selected(old('user_id') == $customer['id'])>{{ $customer['name'] }}</option>
								@endforeach
							</select>
						</div>

						<div class="form-field">
							<label class="form-label" for="car_id">Mobil</label>
							<select id="car_id" name="car_id" class="form-select">
								<option value="">Pilih mobil</option>
								@foreach ($availableCars as $car)
									<option value="{{ $car['id'] }}" @selected(old('car_id') == $car['id'])>{{ $car['brand'] }} {{ $car['model'] ?? $car['name'] }}</option>
								@endforeach
							</select>
						</div>

						<div class="form-field">
							<label class="form-label" for="start_date">Mulai</label>
							<input id="start_date" name="start_date" type="date" class="form-input" value="{{ old('start_date') }}">
						</div>

						<div class="form-field">
							<label class="form-label" for="end_date">Selesai</label>
							<input id="end_date" name="end_date" type="date" class="form-input" value="{{ old('end_date') }}">
						</div>

						<div class="form-field">
							<label class="form-label" for="type">Tipe Rental</label>
							<select id="type" name="type" class="form-select">
								<option value="">Pilih tipe</option>
								@foreach (App\Enums\RentalType::values() as $typeOption)
									<option value="{{ $typeOption }}" @selected(old('type') == $typeOption)>{{ str($typeOption)->headline() }}</option>
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
						<path d="M18 6 6 18"/>
						<path d="m6 6 12 12"/>
					</svg>
				</button>
			</div>

			<div class="modal-body" data-reservation-detail-body>
				<dl style="display:grid; gap:8px; grid-template-columns: 140px 1fr;">
					<dt>ID Reservasi</dt><dd data-detail-booking>-</dd>
					<dt>Pelanggan</dt><dd data-detail-customer>-</dd>
					<dt>Mobil</dt><dd data-detail-car>-</dd>
					<dt>Tanggal Sewa</dt><dd data-detail-period>-</dd>
					<dt>Total</dt><dd data-detail-total>-</dd>
					<dt>Status</dt><dd data-detail-status>-</dd>
				</dl>
			</div>
		</div>
	</div>

	@push('scripts')
		<script>
			(function () {
				const formModal = document.querySelector('[data-reservation-modal]');
				const detailModal = document.querySelector('[data-reservation-detail-modal]');
				if (!formModal && !detailModal) return;

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

				openButtons.forEach((b) => b.addEventListener('click', () => { closeDetail(); openForm(); }));
				closeFormButtons.forEach((b) => b.addEventListener('click', closeForm));
				closeDetailButtons.forEach((b) => b.addEventListener('click', closeDetail));

				formModal?.addEventListener('click', (e) => { if (e.target === formModal) closeForm(); });
				detailModal?.addEventListener('click', (e) => { if (e.target === detailModal) closeDetail(); });

				formPanel?.addEventListener('click', (e) => e.stopPropagation());
				detailPanel?.addEventListener('click', (e) => e.stopPropagation());

				document.addEventListener('keydown', (e) => { if (e.key === 'Escape') { closeForm(); closeDetail(); } });

				document.querySelectorAll('[data-reservation-detail]').forEach((btn) => {
					btn.addEventListener('click', () => {
						const payload = btn.dataset.reservation || '{}';
						try {
							const data = JSON.parse(payload);
							(document.querySelector('[data-detail-booking]') || {}).textContent = data.booking_id || data.id || '-';
							(document.querySelector('[data-detail-customer]') || {}).textContent = data.customer_name || data.customer || '-';
							(document.querySelector('[data-detail-car]') || {}).textContent = data.car_model || data.car || '-';
							(document.querySelector('[data-detail-period]') || {}).textContent = (data.start_date || '-') + ' — ' + (data.end_date || '-');
							(document.querySelector('[data-detail-total]') || {}).textContent = 'Rp ' + (data.total_price ? new Intl.NumberFormat('id-ID').format(data.total_price) : (data.total ? new Intl.NumberFormat('id-ID').format(data.total) : '0'));
							(document.querySelector('[data-detail-status]') || {}).textContent = data.status || '-';
							closeForm();
							openDetail();
						} catch (err) {
							console.error('Invalid reservation payload', err);
						}
					});
				});

				if (formModal && formModal.classList.contains('is-open')) {
					document.body.style.overflow = 'hidden';
				}
			})();
		</script>
	@endpush

</x-backoffice.layout>

