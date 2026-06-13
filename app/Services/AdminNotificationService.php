<?php

namespace App\Services;

use App\Enums\PaymentStatus;
use App\Models\Rental;
use App\Models\User;
use App\Notifications\RentalCustomerNotification;

class AdminNotificationService
{
    public function notifyReservationSubmitted(Rental $rental): void
    {
        $this->sendToAdmins(
            $rental,
            'Reservasi Masuk',
            trim(($rental->user?->name ?? 'Customer').' mengajukan '.$rental->car?->name.' untuk reservasi baru.'),
            'reservations',
            'admin-reservation-submitted',
            route('backoffice.reservations', ['rental_id' => $rental->id])
        );
    }

    public function notifyReservationNeedsReview(Rental $rental): void
    {
        $this->sendToAdmins(
            $rental,
            'Butuh Review',
            trim(($rental->user?->name ?? 'Customer').' membutuhkan verifikasi manual untuk '.$rental->car?->name.'.'),
            'reviews',
            'admin-reservation-needs-review',
            route('backoffice.reservations', [
                'status_filter' => 'waiting_review',
                'rental_id' => $rental->id,
            ])
        );
    }

    public function notifyPaymentPaid(Rental $rental): void
    {
        $latestPayment = $rental->paymentHistories()->latest()->first();

        $this->sendToAdmins(
            $rental,
            'Pembayaran Masuk',
            trim(($rental->user?->name ?? 'Customer').' menyelesaikan pembayaran untuk '.$rental->car?->name.'.'),
            'payments',
            'admin-payment-paid',
            route('backoffice.reservations', ['rental_id' => $rental->id]),
            $latestPayment?->provider_order_id
        );
    }

    public function notifyPaymentFailed(Rental $rental, PaymentStatus $status): void
    {
        $latestPayment = $rental->paymentHistories()->latest()->first();
        $title = $status === PaymentStatus::EXPIRED ? 'Pembayaran Expired' : 'Pembayaran Gagal';

        $this->sendToAdmins(
            $rental,
            $title,
            trim(($rental->user?->name ?? 'Customer').' belum menyelesaikan pembayaran untuk '.$rental->car?->name.'.'),
            'failed',
            'admin-payment-failed-'.$status->value,
            route('backoffice.reservations', [
                'status_filter' => 'cancelled_expired',
                'rental_id' => $rental->id,
            ]),
            $latestPayment?->provider_order_id
        );
    }

    private function sendToAdmins(
        Rental $rental,
        string $title,
        string $message,
        string $category,
        string $dedupeKey,
        string $url,
        ?string $meta = null
    ): void {
        $admins = User::query()
            ->where('role', User::ROLE_ADMIN)
            ->get();

        foreach ($admins as $admin) {
            if (! $admin->hasNotificationsTable()) {
                continue;
            }

            if ($this->alreadyNotified($admin, $dedupeKey, $rental->id)) {
                continue;
            }

            $admin->notify(new RentalCustomerNotification([
                'audience' => 'admin',
                'category' => $category,
                'title' => $title,
                'message' => $message,
                'rental_id' => $rental->id,
                'url' => $url,
                'meta' => $meta ?: 'Booking #'.$rental->id,
                'dedupe_key' => $dedupeKey,
            ]));
        }
    }

    private function alreadyNotified(User $admin, string $dedupeKey, int $rentalId): bool
    {
        return $admin->notifications()
            ->latest()
            ->take(200)
            ->get()
            ->contains(function ($notification) use ($dedupeKey, $rentalId) {
                $data = $notification->data ?? [];

                return ($data['audience'] ?? null) === 'admin'
                    && ($data['dedupe_key'] ?? null) === $dedupeKey
                    && (int) ($data['rental_id'] ?? 0) === $rentalId;
            });
    }
}
