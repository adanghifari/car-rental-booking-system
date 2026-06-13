<?php

namespace App\Services;

use App\Models\Rental;
use App\Models\User;
use App\Notifications\RentalCustomerNotification;

class CustomerNotificationService
{
    public function notifyBookingVerificationStarted(Rental $rental): void
    {
        $this->send(
            $rental,
            'Booking Masuk Verifikasi',
            'Mobil telah diamankan sementara untuk Anda. Silakan lengkapi verifikasi data penyewa.',
            'VERIFICATION',
            'verification',
            route('booking.detail', ['rental' => $rental->id])
        );

        app(AdminNotificationService::class)->notifyReservationSubmitted($rental);
    }

    public function notifyVerificationSubmitted(Rental $rental): void
    {
        $this->send(
            $rental,
            'Data Penyewa Dikirim',
            'Data penyewa Anda sedang diproses untuk verifikasi identitas.',
            'VERIFICATION',
            'verification-submitted',
            route('booking.detail', ['rental' => $rental->id])
        );
    }

    public function notifyVerificationApproved(Rental $rental): void
    {
        $this->send(
            $rental,
            'Verifikasi Disetujui',
            'Admin telah menyetujui verifikasi Anda. Silakan lanjutkan pembayaran sebelum batas waktu berakhir.',
            'VERIFICATION',
            'verification-approved',
            route('booking.detail', ['rental' => $rental->id])
        );
    }

    public function notifyVerificationNeedsReview(Rental $rental): void
    {
        $this->send(
            $rental,
            'Menunggu Review Admin',
            'Verifikasi otomatis belum berhasil. Data Anda sedang ditinjau oleh admin.',
            'VERIFICATION',
            'verification-needs-review',
            route('booking.detail', ['rental' => $rental->id])
        );

        app(AdminNotificationService::class)->notifyReservationNeedsReview($rental);
    }

    public function notifyVerificationRejected(Rental $rental): void
    {
        $this->send(
            $rental,
            'Verifikasi Ditolak',
            'Verifikasi data penyewa belum dapat disetujui. Booking dibatalkan dan mobil kembali tersedia.',
            'CANCELLATION',
            'verification-rejected',
            route('booking.detail', ['rental' => $rental->id])
        );
    }

    public function notifyPaymentAvailable(Rental $rental): void
    {
        $this->send(
            $rental,
            'Pembayaran Tersedia',
            'Pembayaran untuk booking Anda sudah tersedia. Selesaikan pembayaran sebelum batas waktu berakhir.',
            'PAYMENT',
            'payment-available',
            route('booking.detail', ['rental' => $rental->id])
        );
    }

    public function notifyPaymentPaid(Rental $rental): void
    {
        $this->send(
            $rental,
            'Pembayaran Berhasil',
            'Pembayaran berhasil. Rental Anda sekarang aktif.',
            'PAYMENT',
            'payment-paid',
            route('booking.detail', ['rental' => $rental->id])
        );

        app(AdminNotificationService::class)->notifyPaymentPaid($rental);
    }

    public function notifyPaymentCancelled(Rental $rental): void
    {
        $this->send(
            $rental,
            'Pembayaran Dibatalkan',
            'Pembayaran belum berhasil atau dibatalkan. Booking Anda telah dibatalkan dan mobil kembali tersedia.',
            'PAYMENT',
            'payment-cancelled',
            route('booking.detail', ['rental' => $rental->id])
        );

        app(AdminNotificationService::class)->notifyPaymentFailed($rental, \App\Enums\PaymentStatus::CANCELLED);
    }

    public function notifyPaymentExpired(Rental $rental): void
    {
        $this->send(
            $rental,
            'Waktu Pembayaran Habis',
            'Batas waktu pembayaran telah habis. Booking dibatalkan dan mobil kembali tersedia.',
            'CANCELLATION',
            'payment-expired',
            route('booking.detail', ['rental' => $rental->id])
        );

        app(AdminNotificationService::class)->notifyPaymentFailed($rental, \App\Enums\PaymentStatus::EXPIRED);
    }

    public function notifyRentalCancelled(Rental $rental): void
    {
        $this->send(
            $rental,
            'Booking Dibatalkan',
            'Booking Anda telah dibatalkan. Mobil kembali tersedia dan data verifikasi dihapus sesuai kebijakan sistem.',
            'CANCELLATION',
            'booking-cancelled',
            route('booking.detail', ['rental' => $rental->id])
        );
    }

    public function notifyRentalReturned(Rental $rental): void
    {
        $this->send(
            $rental,
            'Rental Selesai',
            'Rental Anda telah selesai. Terima kasih sudah menggunakan MD Car Rental.',
            'RENTAL',
            'rental-returned',
            route('booking.detail', ['rental' => $rental->id])
        );
    }

    private function send(Rental $rental, string $title, string $message, string $type, string $dedupeKey, ?string $url = null): void
    {
        $user = $rental->user;

        if (! $user instanceof User || ! $user->hasNotificationsTable()) {
            return;
        }

        if ($this->alreadyNotified($user, $dedupeKey, $rental->id)) {
            return;
        }

        $user->notify(new RentalCustomerNotification([
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'rental_id' => $rental->id,
            'url' => $url,
            'dedupe_key' => $dedupeKey,
        ]));
    }

    private function alreadyNotified(User $user, string $dedupeKey, int $rentalId): bool
    {
        if (! $user->hasNotificationsTable()) {
            return false;
        }

        return $user->notifications()
            ->latest()
            ->take(200)
            ->get()
            ->contains(function ($notification) use ($dedupeKey, $rentalId) {
                $data = $notification->data ?? [];

                return ($data['dedupe_key'] ?? null) === $dedupeKey
                    && (int) ($data['rental_id'] ?? 0) === $rentalId;
            });
    }
}
