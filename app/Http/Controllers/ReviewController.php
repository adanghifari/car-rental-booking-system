<?php

namespace App\Http\Controllers;

use App\Enums\RentalStatus;
use App\Models\Rental;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReviewController extends Controller
{
    /**
     * Show the form for creating a review.
     */
    public function create(Rental $rental)
    {
        $user = auth()->user();

        // 1. Ensure the user is authenticated and is the owner of this rental
        if (!$user || $rental->user_id !== $user->id) {
            abort(403, 'Akses ditolak.');
        }

        // 2. Ensure the rental status is returned / completed
        if ($rental->status !== RentalStatus::RETURNED) {
            return redirect()->route('booking.detail', $rental->id)
                ->with('error', 'Anda hanya dapat memberikan ulasan setelah status sewa selesai (mobil dikembalikan).');
        }

        // 3. Ensure a review does not already exist
        if ($rental->review()->exists()) {
            return redirect()->route('booking.detail', $rental->id)
                ->with('error', 'Anda sudah memberikan ulasan untuk penyewaan ini.');
        }

        return view('frontliner.pages.rate', [
            'rental' => $rental,
            'car' => $rental->car,
        ]);
    }

    /**
     * Store a newly created review in storage.
     */
    public function store(Request $request, Rental $rental)
    {
        $user = auth()->user();

        // 1. Ensure the user is authenticated and is the owner of this rental
        if (!$user || $rental->user_id !== $user->id) {
            abort(403, 'Akses ditolak.');
        }

        // 2. Ensure the rental status is returned / completed
        if ($rental->status !== RentalStatus::RETURNED) {
            return redirect()->route('booking.detail', $rental->id)
                ->with('error', 'Anda hanya dapat memberikan ulasan setelah status sewa selesai (mobil dikembalikan).');
        }

        // 3. Ensure a review does not already exist
        if ($rental->review()->exists()) {
            return redirect()->route('booking.detail', $rental->id)
                ->with('error', 'Anda sudah memberikan ulasan untuk penyewaan ini.');
        }

        // 4. Validate input
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ], [
            'rating.required' => 'Rating bintang wajib dipilih.',
            'rating.integer' => 'Rating bintang tidak valid.',
            'rating.min' => 'Rating bintang minimal 1.',
            'rating.max' => 'Rating bintang maksimal 5.',
        ]);

        // 5. Create Review
        Review::create([
            'rental_id' => $rental->id,
            'user_id' => $user->id,
            'car_id' => $rental->car_id,
            'rating' => $request->input('rating'),
            'comment' => $request->input('comment'),
        ]);

        return redirect()->route('booking.detail', $rental->id)
            ->with('success', 'Terima kasih! Ulasan dan rating Anda berhasil disimpan.');
    }
}
