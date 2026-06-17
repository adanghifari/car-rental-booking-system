<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Berikan Ulasan - {{ $car->name }} - MD CAR RENTAL</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .star-btn {
            font-size: 2.5rem;
            cursor: pointer;
            transition: all 0.2s ease-in-out;
        }
    </style>
</head>
<body class="bg-[#F8F9FC] text-[#1E293B] antialiased min-h-screen flex flex-col justify-between">

    <x-frontliner.navbar />

    <!-- Main Content -->
    <main class="flex-grow max-w-2xl mx-auto px-4 py-12 w-full">
        
        <div class="bg-white rounded-3xl border border-gray-100 shadow-md p-8 space-y-8">
            <!-- Header -->
            <div class="text-center space-y-2">
                <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight">Bagikan Pengalaman Anda</h1>
                <p class="text-sm text-gray-500">Ulasan Anda sangat berharga bagi kami dan pelanggan lainnya.</p>
            </div>

            <!-- Car Card Mini -->
            <div class="flex items-center gap-5 p-4 bg-gray-50 rounded-2xl border border-gray-100">
                <div class="w-24 h-16 rounded-xl overflow-hidden bg-gray-200 flex-shrink-0">
                    <img src="{{ $car->image ? asset('storage/' . $car->image) : 'https://images.unsplash.com/photo-1614162692292-7ac56d7f7f1e?auto=format&fit=crop&w=300&q=80' }}" alt="{{ $car->name }}" class="w-full h-full object-cover">
                </div>
                <div>
                    <span class="text-[9px] font-bold text-gray-400 uppercase tracking-wider block">{{ $car->brand }}</span>
                    <h3 class="text-base font-bold text-gray-900">{{ $car->name }}</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Sewa selesai pada {{ \Carbon\Carbon::parse($rental->returned_at)->translatedFormat('d M Y') }}</p>
                </div>
            </div>

            <!-- Review Form -->
            <form action="{{ route('booking.review.store', $rental->id) }}" method="POST" class="space-y-6">
                @csrf

                <!-- Rating Stars -->
                <div class="space-y-3 text-center">
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider">Rating Anda</label>
                    <div class="flex justify-center items-center space-x-2" id="star-rating-container">
                        @for($i = 1; $i <= 5; $i++)
                            <button type="button" data-rating="{{ $i }}" class="star-btn text-gray-300 hover:text-amber-400 focus:outline-none">★</button>
                        @endfor
                    </div>
                    <input type="hidden" name="rating" id="rating-input" value="{{ old('rating') }}">
                    @error('rating')
                        <p class="text-red-500 text-xs font-semibold mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Comment Textarea -->
                <div class="space-y-2">
                    <label for="comment" class="block text-xs font-bold text-gray-400 uppercase tracking-wider">Tulis Ulasan / Testimoni</label>
                    <textarea name="comment" id="comment" rows="5" placeholder="Bagaimana kondisi mobil? Apakah layanannya memuaskan? Bagikan detailnya di sini..." class="w-full bg-gray-50 border border-gray-200 rounded-2xl px-4 py-3.5 text-sm font-medium text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500/30 transition resize-none">{{ old('comment') }}</textarea>
                    @error('comment')
                        <p class="text-red-500 text-xs font-semibold mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit and Cancel Buttons -->
                <div class="flex flex-col sm:flex-row gap-3 pt-2">
                    <button type="submit" class="w-full sm:flex-1 bg-[#0B3C9B] hover:bg-[#082D76] active:scale-[0.98] text-white font-bold py-4 rounded-2xl text-xs transition-all duration-200 shadow-md shadow-blue-200 uppercase tracking-wider">
                        Kirim Ulasan
                    </button>
                    <a href="{{ route('booking.detail', $rental->id) }}" class="w-full sm:w-auto text-center border border-gray-200 hover:bg-gray-50 text-gray-500 font-bold py-4 px-6 rounded-2xl text-xs transition uppercase tracking-wider">
                        Batal
                    </a>
                </div>
            </form>
        </div>

    </main>

    <footer class="bg-gray-900 text-gray-400 py-6 border-t border-gray-800">
        <div class="max-w-7xl mx-auto px-4 lg:px-8 text-center text-xs">
            <p>&copy; 2026 MD CAR RENTAL. All rights reserved.</p>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const stars = document.querySelectorAll('.star-btn');
            const ratingInput = document.getElementById('rating-input');

            // Handle default old rating if present
            const oldRating = ratingInput.value;
            if (oldRating) {
                updateStars(oldRating);
            }

            stars.forEach(star => {
                star.addEventListener('click', function() {
                    const ratingValue = this.getAttribute('data-rating');
                    ratingInput.value = ratingValue;
                    updateStars(ratingValue);
                });

                star.addEventListener('mouseover', function() {
                    const ratingValue = this.getAttribute('data-rating');
                    highlightStars(ratingValue);
                });

                star.addEventListener('mouseout', function() {
                    const currentRating = ratingInput.value || 0;
                    updateStars(currentRating);
                });
            });

            function highlightStars(val) {
                stars.forEach(star => {
                    const starValue = star.getAttribute('data-rating');
                    if (starValue <= val) {
                        star.classList.remove('text-gray-300');
                        star.classList.add('text-amber-400', 'scale-110');
                    } else {
                        star.classList.remove('text-amber-400', 'scale-110');
                        star.classList.add('text-gray-300');
                    }
                });
            }

            function updateStars(val) {
                stars.forEach(star => {
                    const starValue = star.getAttribute('data-rating');
                    if (starValue <= val) {
                        star.classList.remove('text-gray-300');
                        star.classList.add('text-amber-400');
                        star.classList.remove('scale-110');
                    } else {
                        star.classList.remove('text-amber-400', 'scale-110');
                        star.classList.add('text-gray-300');
                    }
                });
            }
        });
    </script>
</body>
</html>
