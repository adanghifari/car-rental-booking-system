<?php

namespace App\Http\Controllers;

use App\Enums\CarStatus;
use App\Models\Car;
use App\Support\BookingAvailability;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatbotController extends Controller
{
    /**
     * Handle incoming chatbot message.
     */
    public function handle(Request $request)
    {
        // Check if user is authenticated. If not, return greeting requesting login.
        if (!auth()->check()) {
            return response()->json([
                'reply' => "Halo! Silakan login terlebih dahulu untuk menggunakan fitur chatbot kami dan menikmati layanan MD Car Rental secara penuh.<br><br><a href=\"" . route('login') . "\" class=\"inline-block text-center bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 px-4 rounded-xl shadow-sm transition text-xs\">Login Sekarang</a>",
                'bookingState' => [
                    'carId' => null,
                    'startDate' => null,
                    'endDate' => null,
                    'serviceType' => null,
                    'step' => null,
                    'duration' => null,
                    'ambiguousCarIds' => null,
                ],
                'suggestions' => [],
                'cars' => [],
            ]);
        }

        $message = $request->input('message', '');
        $history = $request->input('history', []);
        $bookingState = $request->input('bookingState', [
            'carId' => null,
            'startDate' => null,
            'endDate' => null,
            'serviceType' => null,
            'step' => null,
            'duration' => null,
            'ambiguousCarIds' => null,
        ]);
        $bookingState['duration'] = $bookingState['duration'] ?? null;
        $bookingState['ambiguousCarIds'] = $bookingState['ambiguousCarIds'] ?? null;

        $cleanMessage = trim($message);
        $lowerMsg = strtolower($cleanMessage);

        // Try to extract duration if mentioned in user message (e.g. "5 hari")
        if (preg_match('/(\d+)\s*(?:hari|day|days)\b/i', $lowerMsg, $durationMatches)) {
            $bookingState['duration'] = (int) $durationMatches[1];
        }

        // Fetch available cars from DB
        $availableCars = Car::where('status', CarStatus::AVAILABLE)
            ->withReviewMetrics()
            ->get();

        // 1. If in active booking step, always run local step-by-step logic
        if (!empty($bookingState['step'])) {
            $data = $this->processLocalBookingStep($lowerMsg, $cleanMessage, $bookingState, $availableCars);
            return $this->formatCarsListInResponse($data);
        }

        // 2. Rule-based check FIRST (similar flow to foodOrder ChatbotService)
        $ruleResponse = $this->checkRuleBasedIntents($lowerMsg, $cleanMessage, $bookingState, $availableCars);
        if ($ruleResponse) {
            return $this->formatCarsListInResponse($ruleResponse);
        }

        // 3. Fallback to Gemini NLU if rule-based didn't match
        $geminiApiKey = trim((string) config('services.gemini.api_key', ''));
        if ($geminiApiKey !== '') {
            try {
                $data = $this->handleWithGemini($cleanMessage, $history, $bookingState, $availableCars, $geminiApiKey);
                if ($data) {
                    return $this->formatCarsListInResponse($data);
                }
            } catch (\Exception $e) {
                Log::error('Gemini Fallback failed: ' . $e->getMessage());
            }
        }

        // 4. Default fallback if Gemini is offline/not configured or both fail
        $defaultFallback = [
            'reply' => "Maaf, saya kurang memahami pertanyaan Anda. Anda dapat bertanya mengenai cara sewa, rekomendasi mobil, atau mengeklik salah satu menu di bawah ini.",
            'bookingState' => $bookingState,
            'suggestions' => ["Rekomendasi Mobil", "Cara Sewa", "Tentang Perusahaan", "Bantu Saya Booking"],
            'cars' => [],
        ];
        return $this->formatCarsListInResponse($defaultFallback);
    }

    /**
     * Helper to find a matching car from a user message.
     */
    private function findCarInMessage($lowerMsg, $availableCars)
    {
        foreach ($availableCars as $car) {
            $carNameLower = strtolower($car->name);
            $carBrandLower = strtolower($car->brand);

            if (str_contains($lowerMsg, $carBrandLower) || str_contains($lowerMsg, $carNameLower)) {
                return $car;
            }

            // Check if any word in the car name (min 3 chars) is in the message
            $nameWords = explode(' ', $carNameLower);
            foreach ($nameWords as $word) {
                $word = trim($word);
                if (strlen($word) >= 3 && str_contains($lowerMsg, $word)) {
                    return $car;
                }
            }
        }
        return null;
    }

    /**
     * Check rule-based intents locally before calling Gemini.
     */
    private function checkRuleBasedIntents($lowerMsg, $originalMsg, $bookingState, $availableCars)
    {
        $recommendedCarIds = [];

        // Greetings - only trigger on very short greeting-only messages
        if (preg_match('/^(halo|hi|hai|hello|pagi|siang|sore|malam|selamat|p|assalamualaikum)( ya)?$/i', $lowerMsg) || (str_word_count($lowerMsg) <= 2 && preg_match('/\b(halo|hi|hai|hello|pagi|siang|sore|malam|selamat)\b/', $lowerMsg))) {
            return [
                'reply' => "Halo! Saya Asisten Virtual MD Car Rental. Ada yang bisa saya bantu hari ini?<br><br>Anda bisa mencari rekomendasi mobil, bertanya tentang cara sewa, atau langsung melakukan pemesanan kendaraan.",
                'bookingState' => $bookingState,
                'suggestions' => ["Rekomendasi Mobil", "Cara Sewa", "Tentang Perusahaan", "Bantu Saya Booking"],
                'cars' => [],
            ];
        }

        // Reviews and ratings inquiry
        if (preg_match('/\b(ulasan|review|komentar|rating|testimoni)\b/', $lowerMsg)) {
            $matchedCar = $this->findCarInMessage($lowerMsg, $availableCars);

            if ($matchedCar) {
                $reviews = \App\Models\Review::where('car_id', $matchedCar->id)
                    ->with('user')
                    ->latest()
                    ->limit(3)
                    ->get();

                $avg = $matchedCar->average_rating;
                $count = $matchedCar->total_reviews;

                $reply = "<span class=\"inline-flex items-center align-middle mr-1.5 text-blue-600\"><svg class=\"w-4.5 h-4.5\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" viewBox=\"0 0 24 24\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124l-.324-5.184a3.375 3.375 0 00-3.37-3.166h-4.83a.75.75 0 01-.52-.22L12.44 4.5h-2.88l-1.24 3.16a.75.75 0 01-.52.22H2.97a3.375 3.375 0 00-3.37 3.166l-.324 5.184c-.04.62.469 1.124 1.09 1.124h1.125m17.25 0h-1.5\" /></svg></span><strong>MD Review Info - {$matchedCar->brand} {$matchedCar->name}</strong><br>";
                $reply .= "<span class=\"inline-flex items-center align-middle mr-1 text-amber-500\"><svg class=\"w-3.5 h-3.5\" fill=\"currentColor\" viewBox=\"0 0 20 20\"><path d=\"M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z\" /></svg></span> Rating: <strong>{$avg} / 5</strong> ({$count} Ulasan)<br><br>";

                if ($reviews->isEmpty()) {
                    $reply .= "Belum ada ulasan tertulis untuk mobil ini. Namun, mobil ini siap disewa dalam kondisi prima!";
                } else {
                    $reply .= "<strong>Ulasan terbaru dari pelanggan kami:</strong><br>";
                    foreach ($reviews as $rev) {
                        $userName = $rev->user ? $rev->user->name : 'Pelanggan';
                        $stars = str_repeat('<span class="inline-flex items-center align-middle text-amber-500"><svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" /></svg></span>', $rev->rating);
                        $date = \Carbon\Carbon::parse($rev->created_at)->setTimezone('Asia/Jakarta')->isoFormat('D MMMM YYYY');
                        $comment = e($rev->comment);
                        $reply .= "• {$stars} oleh <strong>{$userName}</strong> ({$date}):<br>   <i>\"{$comment}\"</i><br>";
                    }
                }

                return [
                    'reply' => $reply,
                    'bookingState' => $bookingState,
                    'suggestions' => ["Bantu Saya Booking " . $matchedCar->name, "Rekomendasi Mobil", "Cara Sewa"],
                    'cars' => [$matchedCar->id],
                ];
            } else {
                // Return null to let Gemini NLU handle general/complex rating/review queries
                return null;
            }
        }

        $isBookingIntent = preg_match('/\b(booking|pesan|sewa|mau rental|mau sewa|pilih)\b/i', $lowerMsg) &&
            !preg_match('/\b(berapa|harga|tarif|biaya|rate|price|gimana|bagaimana|apakah|ready|ada|syarat|cara|tanya|info)\b/i', $lowerMsg) &&
            !str_contains($lowerMsg, '?');

        // Recommendations
        if (!$isBookingIntent && preg_match('/\b(rekomendasi|cari|mobil|daftar|armada|pilihan|jenis|tipe|ready|matic|manual|murah|mewah|keluarga|toyota|honda|daihatsu|mitsubishi|suzuki|nissan|warna|putih|hitam|silver|abu|merah|biru|tahun|cc|lepas|kunci|sopir|driver)\b/i', $lowerMsg)) {
            // Opinion/comparative/semantic query override -> fallback to Gemini NLU
            if (preg_match('/\b(terlaris|laris|populer|favorit|terbagus|bagus|paling|terbaik|cocok|nyaman|bagusan|bandingkan|mana yang)\b/i', $lowerMsg)) {
                return null;
            }
            $type = null;
            $transmission = null;
            $seatsMin = null;
            $seatsMax = null;
            $maxPrice = null;
            $minPrice = null;
            $brand = null;
            $color = null;
            $year = null;
            $yearOperator = '=';
            $cc = null;
            $ccOperator = '=';
            $selfDrive = null;
            $withDriver = null;

            // 1. Detect Transmission
            if (preg_match('/\b(matic|automatic|otomatis)\b/', $lowerMsg)) {
                $transmission = 'automatic';
            } elseif (preg_match('/\b(manual|biasa)\b/', $lowerMsg)) {
                $transmission = 'manual';
            }

            // 2. Detect Category/Vehicle Type
            if (str_contains($lowerMsg, 'suv')) {
                $type = 'suv';
            } elseif (str_contains($lowerMsg, 'mpv')) {
                $type = 'mpv';
            } elseif (str_contains($lowerMsg, 'sedan')) {
                $type = 'sedan';
            } elseif (str_contains($lowerMsg, 'hatchback') || str_contains($lowerMsg, 'ringkas')) {
                $type = 'hatchback';
            } elseif (str_contains($lowerMsg, 'luxury') || str_contains($lowerMsg, 'mewah') || str_contains($lowerMsg, 'premium')) {
                $type = 'luxury';
            } elseif (str_contains($lowerMsg, 'lcgc')) {
                $type = 'lcgc';
            } elseif (str_contains($lowerMsg, 'city car') || str_contains($lowerMsg, 'citycar') || str_contains($lowerMsg, 'mobil kota')) {
                $type = 'city_car';
            }

            // 3. Detect Brand
            if (str_contains($lowerMsg, 'toyota')) {
                $brand = 'Toyota';
            } elseif (str_contains($lowerMsg, 'honda')) {
                $brand = 'Honda';
            } elseif (str_contains($lowerMsg, 'daihatsu')) {
                $brand = 'Daihatsu';
            } elseif (str_contains($lowerMsg, 'mitsubishi')) {
                $brand = 'Mitsubishi';
            } elseif (str_contains($lowerMsg, 'suzuki')) {
                $brand = 'Suzuki';
            } elseif (str_contains($lowerMsg, 'nissan')) {
                $brand = 'Nissan';
            }

            // 4. Detect Seats / Capacity
            if (preg_match('/(\d+)\s*(?:orang|kursi|seat|seater|penumpang)/', $lowerMsg, $matches)) {
                $count = (int) $matches[1];
                if ($count >= 6) {
                    $seatsMin = $count;
                } else {
                    $seatsMax = 5;
                }
            } elseif (str_contains($lowerMsg, 'keluarga') || str_contains($lowerMsg, 'mudik') || str_contains($lowerMsg, 'rombongan') || str_contains($lowerMsg, 'ramai') || str_contains($lowerMsg, 'banyak orang')) {
                $seatsMin = 7;
            } elseif (str_contains($lowerMsg, 'kecil') || str_contains($lowerMsg, 'berdua') || str_contains($lowerMsg, 'pasangan') || str_contains($lowerMsg, 'ringkas')) {
                $seatsMax = 5;
            }

            // 5. Detect Budget / Price
            if (preg_match('/(?:di bawah|dibawah|kurang dari|maksimal|max|budget|harga|tarif|diatas|di atas|lebih dari)\s*(\d{1,3}(?:[\s\.,]\d{3})*|\d+)\s*(rb|ribu|k)?/', $lowerMsg, $priceMatches)) {
                $val = strtolower($priceMatches[1]);
                $val = preg_replace('/[^\d]/', '', $val);
                $numericVal = (int) $val;
                $suffix = strtolower($priceMatches[2] ?? '');
                
                if ($suffix === 'rb' || $suffix === 'ribu' || $suffix === 'k') {
                    $numericVal *= 1000;
                } elseif ($numericVal < 1000) {
                    $numericVal *= 1000;
                }

                if (str_contains($priceMatches[0], 'di atas') || str_contains($priceMatches[0], 'lebih dari') || str_contains($priceMatches[0], 'diatas')) {
                    $minPrice = $numericVal;
                } else {
                    $maxPrice = $numericVal;
                }
            } elseif (str_contains($lowerMsg, 'murah') || str_contains($lowerMsg, 'hemat') || str_contains($lowerMsg, 'terjangkau')) {
                $maxPrice = 400000;
            } elseif (str_contains($lowerMsg, 'mewah') || str_contains($lowerMsg, 'mahal') || str_contains($lowerMsg, 'premium')) {
                $minPrice = 800000;
            }

            // 6. Detect Color
            if (str_contains($lowerMsg, 'putih')) {
                $color = 'putih';
            } elseif (str_contains($lowerMsg, 'hitam')) {
                $color = 'hitam';
            } elseif (str_contains($lowerMsg, 'silver')) {
                $color = 'silver';
            } elseif (str_contains($lowerMsg, 'abu-abu') || str_contains($lowerMsg, 'abu')) {
                $color = 'abu-abu';
            } elseif (str_contains($lowerMsg, 'merah')) {
                $color = 'merah';
            } elseif (str_contains($lowerMsg, 'biru')) {
                $color = 'biru';
            }

            // 7. Detect Manufacturing Year
            if (preg_match('/(?:tahun|keluaran|buatan)\s*(\d{4})/', $lowerMsg, $yearMatches)) {
                $year = (int) $yearMatches[1];
                if (preg_match('/(?:ke atas|di atas|sejak|mulai|terbaru|>)/', $lowerMsg)) {
                    $yearOperator = '>=';
                } elseif (preg_match('/(?:ke bawah|di bawah|terlama|<)/', $lowerMsg)) {
                    $yearOperator = '<=';
                }
            } elseif (preg_match('/\b(20\d{2})\b/', $lowerMsg, $yearMatches)) {
                $year = (int) $yearMatches[1];
                if (preg_match('/(?:ke atas|di atas|sejak|mulai|>)/', $lowerMsg)) {
                    $yearOperator = '>=';
                } elseif (preg_match('/(?:ke bawah|di bawah|<)/', $lowerMsg)) {
                    $yearOperator = '<=';
                }
            } elseif (str_contains($lowerMsg, 'baru') || str_contains($lowerMsg, 'terbaru')) {
                $year = 2021;
                $yearOperator = '>=';
            }

            // 8. Detect Engine CC
            if (preg_match('/(\d{3,4})\s*cc/i', $lowerMsg, $ccMatches)) {
                $cc = (int) $ccMatches[1];
            } elseif (preg_match('/cc\s*(\d{3,4})/i', $lowerMsg, $ccMatches)) {
                $cc = (int) $ccMatches[1];
            } elseif (str_contains($lowerMsg, 'cc besar') || str_contains($lowerMsg, 'mesin besar')) {
                $cc = 2000;
                $ccOperator = '>=';
            } elseif (str_contains($lowerMsg, 'cc kecil') || str_contains($lowerMsg, 'mesin kecil') || str_contains($lowerMsg, 'irit')) {
                $cc = 1300;
                $ccOperator = '<=';
            }

            // 9. Detect Availability
            if (str_contains($lowerMsg, 'lepas kunci') || str_contains($lowerMsg, 'tanpa sopir') || str_contains($lowerMsg, 'self drive') || str_contains($lowerMsg, 'sewa sendiri')) {
                $selfDrive = true;
            } elseif (str_contains($lowerMsg, 'dengan sopir') || str_contains($lowerMsg, 'pakai sopir') || str_contains($lowerMsg, 'pakai driver') || str_contains($lowerMsg, 'with driver')) {
                $withDriver = true;
            }

            // Build dynamic criteria labels first so we can check if it is a generic query
            $criteriaTexts = [];
            if ($brand) $criteriaTexts[] = "merek <strong>{$brand}</strong>";
            if ($type) $criteriaTexts[] = "tipe <strong>" . strtoupper($type) . "</strong>";
            if ($transmission) $criteriaTexts[] = "transmisi <strong>" . ($transmission === 'automatic' ? 'Automatic/Matic' : 'Manual') . "</strong>";
            if ($seatsMin !== null) $criteriaTexts[] = "kapasitas minimal <strong>{$seatsMin} kursi</strong>";
            if ($seatsMax !== null) $criteriaTexts[] = "kapasitas maksimal <strong>{$seatsMax} kursi</strong>";
            if ($maxPrice !== null) $criteriaTexts[] = "tarif maksimal <strong>Rp " . number_format($maxPrice, 0, ',', '.') . "/hari</strong>";
            if ($minPrice !== null) $criteriaTexts[] = "tarif minimal <strong>Rp " . number_format($minPrice, 0, ',', '.') . "/hari</strong>";
            if ($color) $criteriaTexts[] = "warna <strong>" . ucfirst($color) . "</strong>";
            if ($year !== null) {
                $opLabel = ($yearOperator === '>=') ? 'tahun ke atas' : (($yearOperator === '<=') ? 'tahun ke bawah' : 'tahun');
                $criteriaTexts[] = "{$opLabel} <strong>{$year}</strong>";
            }
            if ($cc !== null) {
                $opLabel = ($ccOperator === '>=') ? 'kapasitas mesin minimal' : (($ccOperator === '<=') ? 'kapasitas mesin maksimal' : 'kapasitas mesin');
                $criteriaTexts[] = "{$opLabel} <strong>{$cc} cc</strong>";
            }
            if ($selfDrive !== null) $criteriaTexts[] = "layanan <strong>Lepas Kunci</strong> tersedia";
            if ($withDriver !== null) $criteriaTexts[] = "layanan <strong>Dengan Sopir</strong> tersedia";

            $criteriaString = implode(', ', $criteriaTexts);
            $fallbackUsed = false;

            if ($criteriaString === '') {
                // Generic recommendation
                $reply = "Mau mobil yang bagaimana? Jika Anda masih bingung, berikut adalah beberapa armada unggulan kami:";
                
                // Fetch available cars, loaded with review metrics and rentals count
                $cars = Car::where('status', CarStatus::AVAILABLE)
                    ->withReviewMetrics()
                    ->withCount('rentals')
                    ->get();
                
                // Sort by average rating descending, then rentals count descending
                $cars = $cars->sort(function ($a, $b) {
                    $avgA = $a->average_rating;
                    $avgB = $b->average_rating;
                    if ($avgA != $avgB) {
                        return $avgB <=> $avgA;
                    }
                    return $b->rentals_count <=> $a->rentals_count;
                })->take(3)->values();
            } else {
                // Filtered recommendation
                $query = Car::where('status', CarStatus::AVAILABLE);
                if ($type) {
                    $query->where('vehicle_type', $type);
                }
                if ($transmission) {
                    $query->where('transmission', $transmission);
                }
                if ($brand) {
                    $query->whereRaw('LOWER(brand) LIKE ?', ['%' . strtolower($brand) . '%']);
                }
                if ($seatsMin !== null) {
                    $query->where('seat_count', '>=', $seatsMin);
                }
                if ($seatsMax !== null) {
                    $query->where('seat_count', '<=', $seatsMax);
                }
                if ($maxPrice !== null) {
                    $query->where('daily_rate', '<=', $maxPrice);
                }
                if ($minPrice !== null) {
                    $query->where('daily_rate', '>=', $minPrice);
                }
                if ($color) {
                    $query->whereRaw('LOWER(color) LIKE ?', ['%' . strtolower($color) . '%']);
                }
                if ($year !== null) {
                    $query->where('year', $yearOperator, $year);
                }
                if ($cc !== null) {
                    $query->where('cc', $ccOperator, $cc);
                }
                if ($selfDrive !== null) {
                    $query->where('self_drive_available', true);
                }
                if ($withDriver !== null) {
                    $query->where('driver_available', true);
                }

                $cars = $query->withReviewMetrics()->limit(3)->get();

                if ($cars->count() < 3) {
                    $fallbackUsed = ($cars->count() === 0);
                    $matchedIds = $cars->pluck('id')->all();

                    $allAvailableCars = Car::where('status', CarStatus::AVAILABLE)
                        ->whereNotIn('id', $matchedIds)
                        ->withReviewMetrics()
                        ->withCount('rentals')
                        ->get();

                    $scored = $allAvailableCars->map(function ($car) use (
                        $brand, $type, $transmission, $seatsMin, $seatsMax,
                        $maxPrice, $minPrice, $color, $year, $yearOperator,
                        $cc, $ccOperator, $selfDrive, $withDriver
                    ) {
                        $score = 0;

                        // 1. Color match (Highest priority)
                        if ($color && str_contains(strtolower($car->color), strtolower($color))) {
                            $score += 10;
                        }

                        // 2. Seating match (Second priority)
                        if ($seatsMin !== null && $car->seat_count >= $seatsMin) {
                            $score += 4;
                        }
                        if ($seatsMax !== null && $car->seat_count <= $seatsMax) {
                            $score += 4;
                        }

                        // 3. Brand match
                        if ($brand && str_contains(strtolower($car->brand), strtolower($brand))) {
                            $score += 3;
                        }

                        // 4. Type match
                        if ($type && $car->vehicle_type?->value === $type) {
                            $score += 3;
                        }

                        // 5. Transmission match
                        if ($transmission && $car->transmission?->value === $transmission) {
                            $score += 2;
                        }

                        // 6. CC match
                        if ($cc !== null) {
                            $ccMatch = false;
                            if ($ccOperator === '>=') $ccMatch = ($car->cc >= $cc);
                            elseif ($ccOperator === '<=') $ccMatch = ($car->cc <= $cc);
                            else $ccMatch = ($car->cc == $cc);

                            if ($ccMatch) $score += 1;
                        }

                        // 7. Year match
                        if ($year !== null) {
                            $yearMatch = false;
                            if ($yearOperator === '>=') $yearMatch = ($car->year >= $year);
                            elseif ($yearOperator === '<=') $yearMatch = ($car->year <= $year);
                            else $yearMatch = ($car->year == $year);

                            if ($yearMatch) $score += 1;
                        }

                        // 8. Price match
                        if ($maxPrice !== null && $car->daily_rate <= $maxPrice) {
                            $score += 1;
                        }
                        if ($minPrice !== null && $car->daily_rate >= $minPrice) {
                            $score += 1;
                        }

                        // 9. Availability matches
                        if ($selfDrive !== null && $car->self_drive_available) {
                            $score += 1;
                        }
                        if ($withDriver !== null && $car->driver_available) {
                            $score += 1;
                        }

                        return [
                            'car' => $car,
                            'score' => $score
                        ];
                    });

                    // Sort candidates by match score desc, then average rating desc, then rentals count desc
                    $sortedCandidates = $scored->sort(function ($a, $b) {
                        if ($a['score'] != $b['score']) {
                            return $b['score'] <=> $a['score'];
                        }

                        $avgA = $a['car']->average_rating;
                        $avgB = $b['car']->average_rating;
                        if ($avgA != $avgB) {
                            return $avgB <=> $avgA;
                        }

                        return $b['car']->rentals_count <=> $a['car']->rentals_count;
                    });

                    $needed = 3 - $cars->count();
                    $extraCars = $sortedCandidates->take($needed)->pluck('car');
                    $cars = $cars->concat($extraCars);
                }

                if ($fallbackUsed) {
                    $reply = "Maaf, saat ini armada mobil dengan kriteria {$criteriaString} sedang tidak tersedia. Namun, berikut adalah beberapa mobil terbaik kami yang siap Anda sewa:";
                } else {
                    $reply = "Berikut adalah beberapa mobil dengan kriteria {$criteriaString} yang kami rekomendasikan untuk Anda:";
                }
            }

            foreach ($cars as $car) {
                $recommendedCarIds[] = $car->id;
            }

            return [
                'reply' => $reply,
                'bookingState' => $bookingState,
                'suggestions' => ["Bantu Saya Booking", "Cara Sewa"],
                'cars' => $recommendedCarIds,
            ];
        }

        // Company info - refined to avoid false positive matches on generic terms
        if (preg_match('/\b(alamat kantor|lokasi kantor|alamat md car|lokasi md car|kantor dimana|sejarah md car|profil md car|tentang perusahaan)\b/', $lowerMsg)) {
            return [
                'reply' => "<strong>MD Car Rental</strong> adalah penyedia layanan sewa mobil terpercaya di Jakarta.<br><br><span class=\"inline-flex items-center align-middle mr-1.5 text-rose-500\"><svg class=\"w-4 h-4\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" viewBox=\"0 0 24 24\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"M15 10.5a3 3 0 11-6 0 3 3 0 016 0z\" /><path stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z\" /></svg></span><strong>Alamat Kantor:</strong> Jl. Jend. Sudirman Kav. 1, Jakarta Pusat, DKI Jakarta 10220.<br>Kami berkomitmen memberikan armada bersih, prima, dan layanan terbaik baik lepas kunci maupun dengan sopir.",
                'bookingState' => $bookingState,
                'suggestions' => ["Rekomendasi Mobil", "Cara Sewa", "Bantu Saya Booking"],
                'cars' => [],
            ];
        }

        // Rental guidelines - refined to avoid false positive matches on generic terms
        if (preg_match('/\b(cara sewa|syarat sewa|syarat rental|cara rental|alur rental|prosedur sewa|ketentuan rental|bagaimana menyewa)\b/', $lowerMsg)) {
            return [
                'reply' => "<strong>Cara Sewa Mobil di MD Car Rental:</strong><br>" .
                    "1. Pilih mobil yang Anda inginkan (misal lewat menu rekomendasi).<br>" .
                    "2. Tentukan tanggal sewa (mulai dan selesai).<br>" .
                    "3. Chatbot akan memberikan tombol konfirmasi untuk langsung ke halaman <strong>Upload Identitas</strong>.<br>" .
                    "4. Unggah foto KTP dan Selfie Anda.<br>" .
                    "5. Lakukan pembayaran via Midtrans. Sangat cepat dan mudah!",
                'bookingState' => $bookingState,
                'suggestions' => ["Rekomendasi Mobil", "Bantu Saya Booking", "Tentang Perusahaan"],
                'cars' => [],
            ];
        }

        // Direct booking trigger
        if (preg_match('/\b(booking|pesan|sewa|mau rental|mau sewa|pilih)\b/', $lowerMsg)) {
            // Exclude informational / questioning intents so they fall back to Gemini NLU
            if (preg_match('/\b(berapa|harga|tarif|biaya|rate|price|gimana|bagaimana|apakah|ready|ada|syarat|cara|tanya|info)\b/i', $lowerMsg) || str_contains($lowerMsg, '?')) {
                return null;
            }

            // Parse dates and duration from the single message first
            $parsedDates = $this->parseDatesFromSingleMessage($lowerMsg);
            if ($parsedDates['startDate']) {
                $bookingState['startDate'] = $parsedDates['startDate'];
            }
            if ($parsedDates['duration']) {
                $bookingState['duration'] = $parsedDates['duration'];
            }
            if ($parsedDates['endDate']) {
                $bookingState['endDate'] = $parsedDates['endDate'];
            }

            // Parse service type
            if (preg_match('/\b(lepas kunci|tanpa sopir|self drive|self_drive)\b/i', $lowerMsg)) {
                $bookingState['serviceType'] = 'self_drive';
            } elseif (preg_match('/\b(dengan sopir|pakai sopir|dengan supir|pakai supir|driver|with driver|with_driver)\b/i', $lowerMsg)) {
                $bookingState['serviceType'] = 'with_driver';
            }

            // Check if they already mentioned a car name/brand in this message
            $matchingCars = $this->getMatchingCars($lowerMsg, $availableCars);

            if (count($matchingCars) > 1) {
                return $this->handleCarAmbiguity($matchingCars, $bookingState);
            }

            $matchedCar = count($matchingCars) === 1 ? $matchingCars[0] : null;

            if ($matchedCar) {
                $bookingState['carId'] = $matchedCar->id;
                
                if ($bookingState['startDate'] && $bookingState['endDate']) {
                    if ($bookingState['serviceType']) {
                        $bookingState['step'] = 'confirm';
                        $serviceLabel = $bookingState['serviceType'] === 'with_driver' ? 'Dengan Sopir' : 'Lepas Kunci';
                        $days = Carbon::parse($bookingState['startDate'])->diffInDays(Carbon::parse($bookingState['endDate']));
                        $reply = "Baik, data pesanan Anda sudah lengkap:<br>" .
                                 "- Mobil: <strong>" . $matchedCar->brand . " " . $matchedCar->name . "</strong><br>" .
                                 "- Mulai: <strong>" . Carbon::parse($bookingState['startDate'])->isoFormat('D MMMM YYYY') . "</strong><br>" .
                                 "- Selesai: <strong>" . Carbon::parse($bookingState['endDate'])->isoFormat('D MMMM YYYY') . "</strong> ({$days} Hari)<br>" .
                                 "- Layanan: <strong>" . $serviceLabel . "</strong><br><br>" .
                                 "Apakah data di atas sudah benar? Silakan klik <strong>Konfirmasi Pemesanan</strong> untuk melanjutkan.";
                        return [
                            'reply' => $reply,
                            'bookingState' => $bookingState,
                            'suggestions' => ["Konfirmasi Pemesanan", "Ganti Mobil", "Batal"],
                            'cars' => [$matchedCar->id],
                        ];
                    } else {
                        $bookingState['step'] = 'ask_service_type';
                        $reply = "Mobil <strong>" . $matchedCar->brand . " " . $matchedCar->name . "</strong> telah dipilih.<br>" .
                                 "Mulai sewa: <strong>" . Carbon::parse($bookingState['startDate'])->isoFormat('D MMMM YYYY') . "</strong>.<br>" .
                                 "Selesai sewa: <strong>" . Carbon::parse($bookingState['endDate'])->isoFormat('D MMMM YYYY') . "</strong>.<br><br>" .
                                 "Apakah Anda ingin menyewa <strong>Lepas Kunci</strong> (tanpa sopir) atau <strong>Dengan Sopir</strong> (+ Rp 150.000/hari)?";
                        return [
                            'reply' => $reply,
                            'bookingState' => $bookingState,
                            'suggestions' => ["Lepas Kunci", "Dengan Sopir"],
                            'cars' => [$matchedCar->id],
                        ];
                    }
                } elseif ($bookingState['startDate']) {
                    $bookingState['step'] = 'ask_end_date';
                    $reply = "Mobil <strong>" . $matchedCar->brand . " " . $matchedCar->name . "</strong> telah dipilih.<br>" .
                             "Mulai sewa: <strong>" . Carbon::parse($bookingState['startDate'])->isoFormat('D MMMM YYYY') . "</strong>.<br><br>" .
                             "Sampai tanggal berapa sewa selesai? (Format: YYYY-MM-DD, contoh: " . Carbon::parse($bookingState['startDate'])->addDays(2)->toDateString() . ")";
                    
                    $requestedDuration = $bookingState['duration'] ?? null;
                    $suggestions = [];
                    if ($requestedDuration) {
                        $suggestions[] = Carbon::parse($bookingState['startDate'])->addDays($requestedDuration)->toDateString() . " ({$requestedDuration} Hari)";
                    }
                    $suggestions[] = Carbon::parse($bookingState['startDate'])->addDay()->toDateString() . " (1 Hari)";
                    $suggestions[] = Carbon::parse($bookingState['startDate'])->addDays(2)->toDateString() . " (2 Hari)";
                    $suggestions[] = Carbon::parse($bookingState['startDate'])->addDays(3)->toDateString() . " (3 Hari)";
                    $suggestions = array_values(array_unique($suggestions));

                    return [
                        'reply' => $reply,
                        'bookingState' => $bookingState,
                        'suggestions' => $suggestions,
                        'cars' => [$matchedCar->id],
                    ];
                }

                $bookingState['step'] = 'ask_start_date';
                return [
                    'reply' => "Baik, pemesanan mobil <strong>" . $matchedCar->brand . " " . $matchedCar->name . "</strong> akan segera kami proses.<br><br>Kapan Anda ingin mulai sewa? (Format: YYYY-MM-DD, contoh: " . Carbon::today('Asia/Jakarta')->toDateString() . " atau ketik 'besok')",
                    'bookingState' => $bookingState,
                    'suggestions' => ["Hari Ini", "Besok", "Lusa"],
                    'cars' => [$matchedCar->id],
                ];
            }

            // No matched car in message, ask them which car they want
            $bookingState['step'] = 'ask_car';
            $cars = Car::where('status', CarStatus::AVAILABLE)->limit(4)->get();

            return [
                'reply' => "Tentu! Saya akan membantu memproses sewa mobil Anda dengan cepat.<br><br><strong>Mobil apa yang ingin Anda sewa?</strong><br>Silakan ketik nama mobil pilihan Anda dengan format:<br><strong>[Nama Mobil]</strong> atau <strong>Pesan [Nama Mobil]</strong> (Contoh: <i>Avanza</i> atau <i>Pesan Innova</i>).",
                'bookingState' => $bookingState,
                'suggestions' => $cars->pluck('name')->all(),
                'cars' => [],
            ];
        }

        return null;
    }

    /**
     * Handle chatbot message using Google Gemini API with fallback models.
     */
    private function handleWithGemini($message, $history, $bookingState, $availableCars, $apiKey)
    {
        $todayStr = Carbon::now('Asia/Jakarta')->toDateString();
        
        // Prepare list of cars for Gemini context
        $carsData = $availableCars->map(function ($car) {
            return [
                'id' => $car->id,
                'brand' => $car->brand,
                'name' => $car->name,
                'type' => $car->vehicle_type?->value,
                'transmission' => $car->transmission?->value,
                'seat_count' => $car->seat_count,
                'year' => $car->year,
                'cc' => $car->cc,
                'color' => $car->color,
                'daily_rate' => $car->daily_rate,
                'self_drive_available' => $car->self_drive_available,
                'driver_available' => $car->driver_available,
                'rating' => $car->average_rating,
                'reviews_count' => $car->total_reviews,
                'description' => $car->description,
            ];
        });

        // Format history for context
        $formattedHistory = "";
        foreach (array_slice($history, -6) as $chat) {
            $sender = $chat['sender'] === 'user' ? 'User' : 'Chatbot';
            $formattedHistory .= "{$sender}: {$chat['text']}\n";
        }

        $systemInstruction = "Anda adalah Asisten Virtual MD Car Rental.
Tugas Anda:
1. Membantu merekomendasikan mobil berdasarkan berbagai preferensi pengguna (seperti merek brand, tipe bodi SUV/MPV/sedan/hatchback/LCGC, transmisi manual/automatic, kapasitas mesin CC, tahun buatan, warna cat mobil, harga sewa harian, serta ketersediaan opsi lepas kunci / self drive maupun dengan sopir / driver) dan memberikan informasi tentang perusahaan serta tata cara sewa.
2. Membantu proses booking mobil secara interaktif dan menyaring/menyimpan parameter: carId, startDate, endDate, serviceType ke dalam bookingState.

Informasi Perusahaan:
- Nama: MD Car Rental
- Alamat: Jl. Jend. Sudirman Kav. 1, Jakarta Pusat, DKI Jakarta 10220
- Cara Sewa:
  1. Cari mobil yang Anda inginkan melalui asisten ini atau halaman Armada.
  2. Tentukan tanggal mulai dan selesai sewa.
  3. Konfirmasi pilihan Anda, asisten akan memberikan link khusus.
  4. Unggah foto KTP dan Selfie untuk verifikasi identitas (keamanan).
  5. Selesaikan pembayaran menggunakan Midtrans.

Daftar Mobil Tersedia (Gunakan ID dari daftar ini untuk merekomendasikan/booking):
" . json_encode($carsData, JSON_PRETTY_PRINT) . "

Panduan Booking:
- Jika user menyebutkan durasi sewa (misal: \"sewa 5 hari\"), simpan angka durasi tersebut ke dalam field \"duration\" di bookingState.
- Jika user ingin memesan mobil tetapi belum memilih mobil, tanyakan mobil mana yang diinginkan.
- Jika mobil sudah dipilih, tanyakan tanggal mulai sewa (format YYYY-MM-DD).
- Jika tanggal mulai sudah ada, tanyakan tanggal selesai sewa (format YYYY-MM-DD).
- Jika tanggal mulai & selesai sudah ada, tanyakan layanan: Lepas Kunci (self_drive) atau Dengan Sopir (with_driver).
- Jika semua data lengkap, setel step menjadi 'confirm'.
- HARI INI adalah tanggal: {$todayStr}. Konversi tanggal relatif (seperti 'besok', 'lusa', 'minggu depan') ke format YYYY-MM-DD.

Wajib kembalikan format JSON persis seperti berikut (jangan sertakan markdown block ```json):
{
  \"reply\": \"Balasan ramah dalam bahasa Indonesia (bisa pakai HTML minimal seperti bold, list)...\",
  \"bookingState\": {
    \"carId\": integer_atau_null,
    \"startDate\": \"YYYY-MM-DD_atau_null\",
    \"endDate\": \"YYYY-MM-DD_atau_null\",
    \"serviceType\": \"self_drive\"_atau_\"with_driver\"_atau_null,
    \"step\": \"ask_car\"_atau_\"ask_start_date\"_atau_\"ask_end_date\"_atau_\"ask_service_type\"_atau_\"confirm\"_atau_null,
    \"duration\": integer_atau_null
  },
  \"suggestions\": [\"Opsi 1\", \"Opsi 2\", ...],
  \"cars\": [id_mobil_rekomendasi_1, id_mobil_rekomendasi_2, ...]
}";

        $prompt = "SYSTEM INSTRUCTION:\n{$systemInstruction}\n\nCURRENT BOOKING STATE:\n" . json_encode($bookingState) . "\n\nCHAT HISTORY:\n{$formattedHistory}\n\nUSER MESSAGE:\n{$message}";

        $primaryModel = trim((string) config('services.gemini.model', 'gemini-3.1-flash-lite'));
        $fallbackModel = trim((string) config('services.gemini.fallback_model', 'gemini-3.5-flash'));
        $models = array_values(array_filter([$primaryModel, $fallbackModel], fn ($m) => trim((string) $m) !== ''));

        $lastError = null;

        foreach ($models as $index => $model) {
            try {
                $response = Http::timeout(8)->withHeaders([
                    'Content-Type' => 'application/json',
                ])->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}", [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $prompt]
                            ]
                        ]
                    ],
                    'generationConfig' => [
                        'temperature' => 0.1,
                        'responseMimeType' => 'application/json'
                    ]
                ]);

                if ($response->successful()) {
                    $result = $response->json();
                    $text = $result['candidates'][0]['content']['parts'][0]['text'] ?? '';
                    $data = json_decode(trim($text), true);

                    if ($data && isset($data['reply'])) {
                        $this->validateAndEnrichState($data, $bookingState);
                        $data['nlu_model_used'] = (string) $model;
                        return $data;
                    }
                } else {
                    $lastError = 'API failed with status code ' . $response->status();
                }
            } catch (\Throwable $e) {
                Log::warning("Gemini model {$model} failed: " . $e->getMessage());
                $lastError = $e->getMessage();
            }
        }

        throw new \Exception('Failed to get structured response from Gemini models: ' . $lastError);
    }

    /**
     * Process step-by-step local booking logic.
     */
    private function processLocalBookingStep($lowerMsg, $originalMsg, $bookingState, $availableCars)
    {
        $reply = "";
        $suggestions = [];
        $recommendedCarIds = [];

        $step = $bookingState['step'];

        // User typed cancel/reset during booking flow
        if (preg_match('/\b(batal|cancel|reset|mulai awal|mulai dari awal|ulang)\b/', $lowerMsg)) {
            return [
                'reply' => "Proses booking telah dibatalkan. Ada yang bisa saya bantu lainnya?",
                'bookingState' => [
                    'carId' => null,
                    'startDate' => null,
                    'endDate' => null,
                    'serviceType' => null,
                    'step' => null,
                    'duration' => null,
                ],
                'suggestions' => ["Rekomendasi Mobil", "Cara Sewa", "Bantu Saya Booking"],
                'cars' => [],
            ];
        }

        // User typed or clicked "Ganti Mobil" during booking flow
        if (preg_match('/\b(ganti mobil|ubah mobil|pilih mobil lain)\b/', $lowerMsg)) {
            $bookingState['carId'] = null;
            $bookingState['startDate'] = null;
            $bookingState['endDate'] = null;
            $bookingState['serviceType'] = null;
            $bookingState['step'] = 'ask_car';
            $bookingState['duration'] = null;

            $cars = Car::where('status', CarStatus::AVAILABLE)->limit(3)->get();
            $recommendedCarIds = [];
            foreach ($cars as $car) {
                $recommendedCarIds[] = $car->id;
            }

            return [
                'reply' => "Silakan pilih mobil lain dari daftar di bawah ini dengan mengetik namanya:",
                'bookingState' => $bookingState,
                'suggestions' => $cars->pluck('name')->all(),
                'cars' => $recommendedCarIds,
            ];
        }

        // User typed a greeting during booking flow -> reset booking state and greet them
        if (preg_match('/^(halo|hi|hai|hello|pagi|siang|sore|malam|selamat|p|assalamualaikum)( ya)?$/i', $lowerMsg) || (str_word_count($lowerMsg) <= 2 && preg_match('/\b(halo|hi|hai|hello|pagi|siang|sore|malam|selamat)\b/', $lowerMsg))) {
            return [
                'reply' => "Halo! Saya Asisten Virtual MD Car Rental. Ada yang bisa saya bantu hari ini?<br><br>Anda bisa mencari rekomendasi mobil, bertanya tentang cara sewa, atau langsung melakukan pemesanan kendaraan.",
                'bookingState' => [
                    'carId' => null,
                    'startDate' => null,
                    'endDate' => null,
                    'serviceType' => null,
                    'step' => null,
                    'duration' => null,
                ],
                'suggestions' => ["Rekomendasi Mobil", "Cara Sewa", "Tentang Perusahaan", "Bantu Saya Booking"],
            ];
        }

        // STEP: Resolve Car Ambiguity
        if ($step === 'resolve_car_ambiguity') {
            $ambiguousCarIds = $bookingState['ambiguousCarIds'] ?? [];
            unset($bookingState['ambiguousCarIds']); // Clear it

            // Try to match a number first (e.g. "1", "pilihan 2", etc.)
            $chosenIndex = null;
            if (preg_match('/\b(\d+)\b/', $lowerMsg, $numMatches)) {
                $idx = (int)$numMatches[1] - 1;
                if ($idx >= 0 && $idx < count($ambiguousCarIds)) {
                    $chosenIndex = $idx;
                }
            }

            // If number match failed, try matching by name of the cars in ambiguous list
            if ($chosenIndex === null) {
                foreach ($ambiguousCarIds as $index => $id) {
                    $car = Car::find($id);
                    if ($car) {
                        $carNameLower = strtolower($car->name);
                        if (str_contains($lowerMsg, $carNameLower)) {
                            $chosenIndex = $index;
                            break;
                        }
                    }
                }
            }

            if ($chosenIndex !== null) {
                $carId = $ambiguousCarIds[$chosenIndex];
                $matchedCar = Car::find($carId);
                
                $bookingState['carId'] = $carId;
                
                // Route to next step
                if ($bookingState['startDate'] && $bookingState['endDate']) {
                    if ($bookingState['serviceType']) {
                        $bookingState['step'] = 'confirm';
                        $serviceLabel = $bookingState['serviceType'] === 'with_driver' ? 'Dengan Sopir' : 'Lepas Kunci';
                        $days = Carbon::parse($bookingState['startDate'])->diffInDays(Carbon::parse($bookingState['endDate']));
                        $reply = "Mobil <strong>" . $matchedCar->brand . " " . $matchedCar->name . "</strong> telah dipilih.<br><br>" .
                                 "Data pesanan Anda:<br>" .
                                 "- Mulai: <strong>" . Carbon::parse($bookingState['startDate'])->isoFormat('D MMMM YYYY') . "</strong><br>" .
                                 "- Selesai: <strong>" . Carbon::parse($bookingState['endDate'])->isoFormat('D MMMM YYYY') . "</strong> ({$days} Hari)<br>" .
                                 "- Layanan: <strong>" . $serviceLabel . "</strong><br><br>" .
                                 "Apakah data di atas sudah benar? Silakan klik <strong>Konfirmasi Pemesanan</strong> untuk melanjutkan.";
                        $suggestions = ["Konfirmasi Pemesanan", "Ganti Mobil", "Batal"];
                    } else {
                        $bookingState['step'] = 'ask_service_type';
                        $reply = "Mobil <strong>" . $matchedCar->brand . " " . $matchedCar->name . "</strong> telah dipilih.<br><br>" .
                                 "Mulai sewa: <strong>" . Carbon::parse($bookingState['startDate'])->isoFormat('D MMMM YYYY') . "</strong>.<br>" .
                                 "Selesai sewa: <strong>" . Carbon::parse($bookingState['endDate'])->isoFormat('D MMMM YYYY') . "</strong>.<br><br>" .
                                 "Apakah Anda ingin menyewa <strong>Lepas Kunci</strong> (tanpa sopir) atau <strong>Dengan Sopir</strong> (+ Rp 150.000/hari)?";
                        $suggestions = ["Lepas Kunci", "Dengan Sopir"];
                    }
                } elseif ($bookingState['startDate']) {
                    $bookingState['step'] = 'ask_end_date';
                    $reply = "Mobil <strong>" . $matchedCar->brand . " " . $matchedCar->name . "</strong> telah dipilih.<br><br>" .
                             "Mulai sewa: <strong>" . Carbon::parse($bookingState['startDate'])->isoFormat('D MMMM YYYY') . "</strong>.<br><br>" .
                             "Sampai tanggal berapa sewa selesai? (Format: YYYY-MM-DD, contoh: " . Carbon::parse($bookingState['startDate'])->addDays(2)->toDateString() . ")";
                    
                    $requestedDuration = $bookingState['duration'] ?? null;
                    $suggestions = [];
                    if ($requestedDuration) {
                        $suggestions[] = Carbon::parse($bookingState['startDate'])->addDays($requestedDuration)->toDateString() . " ({$requestedDuration} Hari)";
                    }
                    $suggestions[] = Carbon::parse($bookingState['startDate'])->addDay()->toDateString() . " (1 Hari)";
                    $suggestions[] = Carbon::parse($bookingState['startDate'])->addDays(2)->toDateString() . " (2 Hari)";
                    $suggestions[] = Carbon::parse($bookingState['startDate'])->addDays(3)->toDateString() . " (3 Hari)";
                    $suggestions = array_values(array_unique($suggestions));
                } else {
                    $bookingState['step'] = 'ask_start_date';
                    $reply = "Mobil <strong>" . $matchedCar->brand . " " . $matchedCar->name . "</strong> telah dipilih.<br><br>Kapan Anda ingin mulai sewa? (Format: YYYY-MM-DD, contoh: " . Carbon::today('Asia/Jakarta')->toDateString() . " atau ketik 'besok')";
                    $suggestions = ["Hari Ini", "Besok", "Lusa"];
                }
                
                return [
                    'reply' => $reply,
                    'bookingState' => $bookingState,
                    'suggestions' => $suggestions,
                    'cars' => [$carId],
                ];
            } else {
                // Restore ambiguous list to try again
                $bookingState['ambiguousCarIds'] = $ambiguousCarIds;
                $bookingState['step'] = 'resolve_car_ambiguity';
                
                $matchingCars = Car::whereIn('id', $ambiguousCarIds)->get();
                $tempState = $bookingState;
                $res = $this->handleCarAmbiguity($matchingCars, $tempState);
                $res['reply'] = "Pilihan tidak valid. " . $res['reply'];
                return $res;
            }
        }

        // STEP 1: Ask/Select Car
        if ($step === 'ask_car') {
            $matchingCars = $this->getMatchingCars($lowerMsg, $availableCars);

            if (count($matchingCars) > 1) {
                return $this->handleCarAmbiguity($matchingCars, $bookingState);
            }
            
            $matchedCar = count($matchingCars) === 1 ? $matchingCars[0] : null;

            if ($matchedCar) {
                $bookingState['carId'] = $matchedCar->id;
                
                if ($bookingState['startDate'] && $bookingState['endDate']) {
                    if ($bookingState['serviceType']) {
                        $bookingState['step'] = 'confirm';
                        $serviceLabel = $bookingState['serviceType'] === 'with_driver' ? 'Dengan Sopir' : 'Lepas Kunci';
                        $days = Carbon::parse($bookingState['startDate'])->diffInDays(Carbon::parse($bookingState['endDate']));
                        $reply = "Mobil <strong>" . $matchedCar->brand . " " . $matchedCar->name . "</strong> telah dipilih.<br><br>" .
                                 "Data pesanan Anda:<br>" .
                                 "- Mulai: <strong>" . Carbon::parse($bookingState['startDate'])->isoFormat('D MMMM YYYY') . "</strong><br>" .
                                 "- Selesai: <strong>" . Carbon::parse($bookingState['endDate'])->isoFormat('D MMMM YYYY') . "</strong> ({$days} Hari)<br>" .
                                 "- Layanan: <strong>" . $serviceLabel . "</strong><br><br>" .
                                 "Apakah data di atas sudah benar? Silakan klik <strong>Konfirmasi Pemesanan</strong> untuk melanjutkan.";
                        $suggestions = ["Konfirmasi Pemesanan", "Ganti Mobil", "Batal"];
                    } else {
                        $bookingState['step'] = 'ask_service_type';
                        $reply = "Mobil <strong>" . $matchedCar->brand . " " . $matchedCar->name . "</strong> telah dipilih.<br><br>" .
                                 "Mulai sewa: <strong>" . Carbon::parse($bookingState['startDate'])->isoFormat('D MMMM YYYY') . "</strong>.<br>" .
                                 "Selesai sewa: <strong>" . Carbon::parse($bookingState['endDate'])->isoFormat('D MMMM YYYY') . "</strong>.<br><br>" .
                                 "Apakah Anda ingin menyewa <strong>Lepas Kunci</strong> (tanpa sopir) atau <strong>Dengan Sopir</strong> (+ Rp 150.000/hari)?";
                        $suggestions = ["Lepas Kunci", "Dengan Sopir"];
                    }
                } elseif ($bookingState['startDate']) {
                    $bookingState['step'] = 'ask_end_date';
                    $reply = "Mobil <strong>" . $matchedCar->brand . " " . $matchedCar->name . "</strong> telah dipilih.<br><br>" .
                             "Mulai sewa: <strong>" . Carbon::parse($bookingState['startDate'])->isoFormat('D MMMM YYYY') . "</strong>.<br><br>" .
                             "Sampai tanggal berapa sewa selesai? (Format: YYYY-MM-DD, contoh: " . Carbon::parse($bookingState['startDate'])->addDays(2)->toDateString() . ")";
                    
                    $requestedDuration = $bookingState['duration'] ?? null;
                    $suggestions = [];
                    if ($requestedDuration) {
                        $suggestions[] = Carbon::parse($bookingState['startDate'])->addDays($requestedDuration)->toDateString() . " ({$requestedDuration} Hari)";
                    }
                    $suggestions[] = Carbon::parse($bookingState['startDate'])->addDay()->toDateString() . " (1 Hari)";
                    $suggestions[] = Carbon::parse($bookingState['startDate'])->addDays(2)->toDateString() . " (2 Hari)";
                    $suggestions[] = Carbon::parse($bookingState['startDate'])->addDays(3)->toDateString() . " (3 Hari)";
                    $suggestions = array_values(array_unique($suggestions));
                } else {
                    $bookingState['step'] = 'ask_start_date';
                    $reply = "Mobil <strong>" . $matchedCar->brand . " " . $matchedCar->name . "</strong> telah dipilih.<br><br>Kapan Anda ingin mulai sewa? (Format: YYYY-MM-DD, contoh: " . Carbon::today('Asia/Jakarta')->toDateString() . " atau ketik 'besok')";
                    $suggestions = ["Hari Ini", "Besok", "Lusa"];
                }
            } else {
                // Check if they want to filter by category
                $type = null;
                if (str_contains($lowerMsg, 'suv')) $type = 'suv';
                elseif (str_contains($lowerMsg, 'mpv')) $type = 'mpv';
                elseif (str_contains($lowerMsg, 'sedan')) $type = 'sedan';
                elseif (str_contains($lowerMsg, 'hatchback') || str_contains($lowerMsg, 'kecil') || str_contains($lowerMsg, 'ringkas')) $type = 'hatchback';
                elseif (str_contains($lowerMsg, 'luxury') || str_contains($lowerMsg, 'mewah')) $type = 'luxury';
                elseif (str_contains($lowerMsg, 'murah') || str_contains($lowerMsg, 'lcgc')) $type = 'lcgc';

                if ($type) {
                    $cars = Car::where('status', CarStatus::AVAILABLE)->where('vehicle_type', $type)->limit(3)->get();
                    if (!$cars->isEmpty()) {
                        $reply = "Berikut adalah beberapa mobil tipe <strong>" . strtoupper($type) . "</strong> yang tersedia. Silakan ketik nama mobil pilihan Anda:";
                        $suggestions = $cars->pluck('name')->all();
                        foreach ($cars as $car) {
                            $recommendedCarIds[] = $car->id;
                        }
                        return [
                            'reply' => $reply,
                            'bookingState' => $bookingState,
                            'suggestions' => $suggestions,
                            'cars' => $recommendedCarIds,
                        ];
                    }
                }

                $reply = "Mobil tidak ditemukan atau sedang tidak tersedia. Silakan ketik nama mobil dari daftar berikut:";
                $cars = Car::where('status', CarStatus::AVAILABLE)->limit(3)->get();
                foreach ($cars as $car) {
                    $recommendedCarIds[] = $car->id;
                }
                $suggestions = $cars->pluck('name')->all();
            }
        }
        // STEP 2: Ask Start Date
        elseif ($step === 'ask_start_date') {
            $parsedDates = $this->parseDatesFromSingleMessage($lowerMsg);
            $startDate = $parsedDates['startDate'];
            
            if ($startDate) {
                $bookingState['startDate'] = $startDate;
                if ($parsedDates['duration']) {
                    $bookingState['duration'] = $parsedDates['duration'];
                }
                if ($parsedDates['endDate']) {
                    $bookingState['endDate'] = $parsedDates['endDate'];
                }

                // Check if they also specified service type in this message
                if (preg_match('/\b(lepas kunci|tanpa sopir|self drive|self_drive)\b/i', $lowerMsg)) {
                    $bookingState['serviceType'] = 'self_drive';
                } elseif (preg_match('/\b(dengan sopir|pakai sopir|dengan supir|pakai supir|driver|with driver|with_driver)\b/i', $lowerMsg)) {
                    $bookingState['serviceType'] = 'with_driver';
                }

                if ($bookingState['endDate']) {
                    if ($bookingState['serviceType']) {
                        $bookingState['step'] = 'confirm';
                        $serviceLabel = $bookingState['serviceType'] === 'with_driver' ? 'Dengan Sopir' : 'Lepas Kunci';
                        $days = Carbon::parse($bookingState['startDate'])->diffInDays(Carbon::parse($bookingState['endDate']));
                        $reply = "Tanggal sewa diset: <strong>" . Carbon::parse($bookingState['startDate'])->isoFormat('D MMMM YYYY') . "</strong> s/d <strong>" . Carbon::parse($bookingState['endDate'])->isoFormat('D MMMM YYYY') . "</strong> ({$days} Hari).<br>" .
                                 "Layanan: <strong>" . $serviceLabel . "</strong>.<br><br>" .
                                 "Apakah data di atas sudah benar? Silakan klik <strong>Konfirmasi Pemesanan</strong> untuk melanjutkan.";
                        $suggestions = ["Konfirmasi Pemesanan", "Ganti Mobil", "Batal"];
                    } else {
                        $bookingState['step'] = 'ask_service_type';
                        $reply = "Tanggal sewa diset: <strong>" . Carbon::parse($bookingState['startDate'])->isoFormat('D MMMM YYYY') . "</strong> s/d <strong>" . Carbon::parse($bookingState['endDate'])->isoFormat('D MMMM YYYY') . "</strong>.<br><br>" .
                                 "Apakah Anda ingin menyewa <strong>Lepas Kunci</strong> (tanpa sopir) atau <strong>Dengan Sopir</strong> (+ Rp 150.000/hari)?";
                        $suggestions = ["Lepas Kunci", "Dengan Sopir"];
                    }
                } else {
                    $bookingState['step'] = 'ask_end_date';
                    $reply = "Tanggal mulai sewa diset: <strong>" . Carbon::parse($startDate)->isoFormat('D MMMM YYYY') . "</strong>.<br><br>Sampai tanggal berapa sewa selesai? (Format: YYYY-MM-DD, contoh: " . Carbon::parse($startDate)->addDays(2)->toDateString() . ")";
                    
                    $requestedDuration = $bookingState['duration'] ?? null;
                    $suggestions = [];
                    if ($requestedDuration) {
                        $suggestions[] = Carbon::parse($startDate)->addDays($requestedDuration)->toDateString() . " ({$requestedDuration} Hari)";
                    }
                    $suggestions[] = Carbon::parse($startDate)->addDay()->toDateString() . " (1 Hari)";
                    $suggestions[] = Carbon::parse($startDate)->addDays(2)->toDateString() . " (2 Hari)";
                    $suggestions[] = Carbon::parse($startDate)->addDays(3)->toDateString() . " (3 Hari)";
                    $suggestions = array_values(array_unique($suggestions));
                }
            } else {
                $reply = "Format tanggal tidak dikenali. Silakan masukkan tanggal mulai sewa dengan format YYYY-MM-DD (Contoh: " . Carbon::today('Asia/Jakarta')->toDateString() . ") atau ketik 'besok'.";
                $suggestions = ["Hari Ini", "Besok", "Lusa"];
            }
        }
        // STEP 3: Ask End Date
        elseif ($step === 'ask_end_date') {
            $endDate = $this->parseRelativeDate($lowerMsg, $bookingState['startDate']);
            if ($endDate) {
                $startDate = $bookingState['startDate'];
                if (Carbon::parse($endDate)->gte(Carbon::parse($startDate))) {
                    $bookingState['endDate'] = $endDate;
                    
                    // Parse service type if present in end date response
                    if (preg_match('/\b(lepas kunci|tanpa sopir|self drive|self_drive)\b/i', $lowerMsg)) {
                        $bookingState['serviceType'] = 'self_drive';
                    } elseif (preg_match('/\b(dengan sopir|pakai sopir|dengan supir|pakai supir|driver|with driver|with_driver)\b/i', $lowerMsg)) {
                        $bookingState['serviceType'] = 'with_driver';
                    }

                    if ($bookingState['serviceType']) {
                        $bookingState['step'] = 'confirm';
                        $serviceLabel = $bookingState['serviceType'] === 'with_driver' ? 'Dengan Sopir' : 'Lepas Kunci';
                        $days = Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate));
                        $reply = "Tanggal selesai sewa diset: <strong>" . Carbon::parse($endDate)->isoFormat('D MMMM YYYY') . "</strong> ({$days} Hari).<br>" .
                                 "Layanan: <strong>" . $serviceLabel . "</strong>.<br><br>" .
                                 "Apakah data di atas sudah benar? Silakan klik <strong>Konfirmasi Pemesanan</strong> untuk melanjutkan.";
                        $suggestions = ["Konfirmasi Pemesanan", "Ganti Mobil", "Batal"];
                    } else {
                        $bookingState['step'] = 'ask_service_type';
                        $reply = "Tanggal selesai sewa diset: <strong>" . Carbon::parse($endDate)->isoFormat('D MMMM YYYY') . "</strong>.<br><br>Apakah Anda ingin menyewa <strong>Lepas Kunci</strong> (tanpa sopir) atau <strong>Dengan Sopir</strong> (+ Rp 150.000/hari)?";
                        $suggestions = ["Lepas Kunci", "Dengan Sopir"];
                    }
                } else {
                    $reply = "Tanggal selesai sewa tidak boleh sebelum tanggal mulai (" . Carbon::parse($startDate)->isoFormat('D MMMM YYYY') . "). Silakan ketik tanggal selesai yang valid:";
                    $suggestions = [
                        Carbon::parse($startDate)->addDay()->toDateString() . " (1 Hari)",
                        Carbon::parse($startDate)->addDays(2)->toDateString() . " (2 Hari)"
                    ];
                }
            }
        }
        // STEP 4: Ask Service Type
        elseif ($step === 'ask_service_type') {
            $serviceType = null;
            if (str_contains($lowerMsg, 'sopir') || str_contains($lowerMsg, 'driver') || str_contains($lowerMsg, 'dengan')) {
                $serviceType = 'with_driver';
            } elseif (str_contains($lowerMsg, 'lepas') || str_contains($lowerMsg, 'kunci') || str_contains($lowerMsg, 'sendiri') || str_contains($lowerMsg, 'tanpa')) {
                $serviceType = 'self_drive';
            }

            if ($serviceType) {
                $bookingState['serviceType'] = $serviceType;
                
                $data = [
                    'bookingState' => $bookingState,
                    'reply' => '',
                    'suggestions' => [],
                ];
                $this->validateAndEnrichState($data, $bookingState);
                return $data;
            } else {
                $reply = "Pilihan tidak valid. Silakan pilih layanan: <strong>Lepas Kunci</strong> atau <strong>Dengan Sopir</strong>.";
                $suggestions = ["Lepas Kunci", "Dengan Sopir"];
            }
        }

        return [
            'reply' => $reply,
            'bookingState' => $bookingState,
            'suggestions' => $suggestions,
            'cars' => $recommendedCarIds,
        ];
    }

    /**
     * Intercept and format the recommended cars into full details.
     */
    private function formatCarsListInResponse($data)
    {
        if (isset($data['cars']) && is_array($data['cars'])) {
            $formattedCars = [];
            foreach ($data['cars'] as $carId) {
                $id = is_array($carId) ? ($carId['id'] ?? null) : $carId;
                if (!$id) continue;

                $car = Car::withReviewMetrics()->find($id);
                if ($car && $car->status === CarStatus::AVAILABLE) {
                    $formattedCars[] = [
                        'id' => $car->id,
                        'brand' => $car->brand,
                        'name' => $car->name,
                        'transmission' => $car->transmission?->label(),
                        'seat_count' => $car->seat_count,
                        'daily_rate' => number_format($car->daily_rate, 0, ',', '.'),
                        'rating' => $car->average_rating,
                        'reviews_count' => $car->total_reviews,
                        'image' => $car->image ? asset('storage/' . $car->image) : 'https://images.unsplash.com/photo-1617814076367-b759c7d7e738?auto=format&fit=crop&w=500&q=80',
                    ];
                }
            }
            $data['cars'] = $formattedCars;
        }

        return response()->json($data);
    }

    /**
     * Validate dates, check availability, and enrich response with pricing & direct links.
     */
    private function validateAndEnrichState(&$data, $oldState)
    {
        $state = $data['bookingState'] ?? $oldState;
        
        // Ensure all components are set
        if (!empty($state['carId']) && !empty($state['startDate']) && !empty($state['endDate']) && !empty($state['serviceType'])) {
            $car = Car::find($state['carId']);
            if (!$car) {
                $data['reply'] = "Maaf, mobil yang dipilih tidak ditemukan. Mari mulai sewa lagi.";
                $data['bookingState'] = [
                    'carId' => null,
                    'startDate' => null,
                    'endDate' => null,
                    'serviceType' => null,
                    'step' => 'ask_car',
                    'duration' => null,
                ];
                $data['suggestions'] = ["Cari Mobil"];
                return;
            }

            // Check availability
            $availability = BookingAvailability::checkCarAvailability($car, $state['startDate'], $state['endDate']);
            if (!$availability['available']) {
                $reasonMsg = BookingAvailability::unavailabilityMessage($availability['reason'] ?? 'overlap');
                $data['reply'] = "<span class=\"inline-flex items-center align-middle mr-1.5 text-amber-500\"><svg class=\"w-4 h-4\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" viewBox=\"0 0 24 24\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z\" /></svg></span><strong>Mobil Tidak Tersedia:</strong> Mobil <strong>{$car->brand} {$car->name}</strong> tidak tersedia pada tanggal " . 
                    Carbon::parse($state['startDate'])->isoFormat('D MMM') . " s/d " . Carbon::parse($state['endDate'])->isoFormat('D MMM YYYY') . 
                    ".<br><i>Alasan: {$reasonMsg}</i><br><br>Silakan pilih tanggal mulai sewa yang lain:";
                
                // Reset dates, keep car
                $state['startDate'] = null;
                $state['endDate'] = null;
                $state['step'] = 'ask_start_date';
                
                $data['bookingState'] = $state;
                $data['suggestions'] = ["Hari Ini", "Besok", "Ganti Mobil"];
                return;
            }

            // Calculate price
            $start = Carbon::parse($state['startDate']);
            $end = Carbon::parse($state['endDate']);
            $days = max(1, $start->diffInDays($end));

            $rentCost = $car->daily_rate * $days;
            $driverCost = ($state['serviceType'] === 'with_driver') ? 150000 * $days : 0;
            $serviceCost = 100000 + $driverCost;
            $totalPrice = $rentCost + $serviceCost;

            $serviceLabel = $state['serviceType'] === 'with_driver' ? 'Dengan Sopir (+Rp150k/hari)' : 'Lepas Kunci';

            $data['reply'] = "<span class=\"inline-flex items-center align-middle mr-1.5 text-emerald-500\"><svg class=\"w-4 h-4\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" viewBox=\"0 0 24 24\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"M9.813 15.904L9 21l-1.81-5.096L2.096 14.1 7.19 12.29 9 7.19l1.81 5.096 5.096 1.81-5.096 1.81zM19.071 4.929l-.707 1.986-1.986.707 1.986.707.707 1.986.707-1.986 1.986-.707-1.986-.707-.707-1.986zM14 10l-.353.99-.99.353.99.353.353.99.353-.99.99-.353-.99-.353-.353-.99z\" /></svg></span><strong>Mobil Tersedia!</strong> Berikut rincian pemesanan Anda:<br><br>" .
                "• <strong>Mobil:</strong> {$car->brand} {$car->name} ({$car->transmission?->label()})<br>" .
                "• <strong>Durasi:</strong> " . $start->isoFormat('D MMMM YYYY') . " s/d " . $end->isoFormat('D MMMM YYYY') . " ({$days} hari)<br>" .
                "• <strong>Layanan:</strong> {$serviceLabel}<br>" .
                "• <strong>Estimasi Biaya:</strong><br>" .
                "  - Tarif Mobil: Rp " . number_format($rentCost, 0, ',', '.') . "<br>" .
                "  - Biaya Layanan & Sopir: Rp " . number_format($serviceCost, 0, ',', '.') . "<br>" .
                "  - <strong>Total Harga: Rp " . number_format($totalPrice, 0, ',', '.') . "</strong><br><br>" .
                "Klik tombol di bawah untuk langsung menuju halaman <strong>Upload Identitas (KTP & Selfie)</strong> untuk menyelesaikan booking Anda!";
            
            $state['step'] = 'confirm';
            $data['bookingState'] = $state;
            
            // Generate direct link
            $data['bookingLink'] = route('booking.identity') . "?" . http_build_query([
                'car_id' => $car->id,
                'start_date' => $state['startDate'],
                'end_date' => $state['endDate'],
                'service_type' => $state['serviceType']
            ]);
            $data['suggestions'] = ["Mulai Dari Awal", "Ganti Mobil"];
        }
    }

    /**
     * Parse relative Indonesian date strings.
     */
    private function parseRelativeDate($str, $startDate = null)
    {
        $str = trim($str);

        // 1. Extract YYYY-MM-DD or YYYY/MM/DD pattern from anywhere in the string
        if (preg_match('/\b(\d{4})[\-\/](\d{2})[\-\/](\d{2})\b/', $str, $matches)) {
            try {
                return Carbon::createFromDate((int)$matches[1], (int)$matches[2], (int)$matches[3])->toDateString();
            } catch (\Exception $e) {
                // Ignore exception and continue
            }
        }

        // 2. Extract DD-MM-YYYY or DD/MM/YYYY pattern from anywhere in the string
        if (preg_match('/\b(\d{1,2})[\-\/](\d{1,2})[\-\/](\d{2,4})\b/', $str, $matches)) {
            try {
                $year = (int)$matches[3];
                if ($year < 100) {
                    $year += 2000;
                }
                return Carbon::createFromDate($year, (int)$matches[2], (int)$matches[1])->toDateString();
            } catch (\Exception $e) {
                // Ignore exception and continue
            }
        }

        // 3. Parse relative duration like "5 hari" or "6 days" if startDate is provided
        if ($startDate && preg_match('/(\d+)\s*(?:hari|day|days)/i', $str, $matches)) {
            $days = (int) $matches[1];
            return Carbon::parse($startDate)->addDays($days)->toDateString();
        }

        $lowerStr = strtolower($str);
        if (str_contains($lowerStr, 'hari ini')) {
            return Carbon::today('Asia/Jakarta')->toDateString();
        }
        if (str_contains($lowerStr, 'besok')) {
            return Carbon::tomorrow('Asia/Jakarta')->toDateString();
        }
        if (str_contains($lowerStr, 'lusa')) {
            return Carbon::today('Asia/Jakarta')->addDays(2)->toDateString();
        }

        // Custom parser for Indonesian months (e.g. "20 juni 2026")
        $months = [
            'jan' => 1, 'feb' => 2, 'mar' => 3, 'apr' => 4, 'mei' => 5, 'jun' => 6,
            'jul' => 7, 'agu' => 8, 'sep' => 9, 'okt' => 10, 'nov' => 11, 'des' => 12,
            'januari' => 1, 'februari' => 2, 'maret' => 3, 'april' => 4, 'juni' => 6, 'juli' => 7,
            'agustus' => 8, 'september' => 9, 'oktober' => 10, 'november' => 11, 'desember' => 12
        ];

        // Format: "20 Juni 2026" or "20 Juni" (not anchored to start of string)
        if (preg_match('/\b(\d{1,2})\s+([a-zA-Z]+)(?:\s+(\d{2,4}))?\b/i', $str, $matches)) {
            $day = (int) $matches[1];
            $monthName = strtolower($matches[2]);
            $year = isset($matches[3]) ? (int) $matches[3] : Carbon::today('Asia/Jakarta')->year;
            if ($year < 100) {
                $year += 2000;
            }

            if (isset($months[$monthName])) {
                $month = $months[$monthName];
                try {
                    return Carbon::createFromDate($year, $month, $day)->toDateString();
                } catch (\Exception $e) {
                    return null;
                }
            }
        }

        return null;
    }

    /**
     * Helper to parse start date, end date, and duration from a single message.
     */
    private function parseDatesFromSingleMessage($lowerMsg)
    {
        $startDate = null;
        $endDate = null;
        $duration = null;

        // Try to split message by range indicators
        $splitRegex = '/\b(sampai|hingga|ke|sd|s\/d|selesai|selama|durasi|untuk|for|to|until)\b/i';
        if (preg_match($splitRegex, $lowerMsg, $matches, PREG_OFFSET_CAPTURE)) {
            $offset = $matches[0][1];
            $part1 = substr($lowerMsg, 0, $offset);
            $part2 = substr($lowerMsg, $offset);

            // Parse start date from part 1
            $startDate = $this->parseRelativeDate($part1);
            
            // If start date is found, parse end date/duration from part 2
            if ($startDate) {
                // Check for duration first in part 2
                if (preg_match('/(\d+)\s*(?:hari|day|days)/i', $part2, $durationMatches)) {
                    $duration = (int) $durationMatches[1];
                    $endDate = Carbon::parse($startDate)->addDays($duration)->toDateString();
                } else {
                    $endDate = $this->parseRelativeDate($part2, $startDate);
                }
            }
        } else {
            // No split word. Let's see if we can find a duration anyway
            if (preg_match('/(\d+)\s*(?:hari|day|days)/i', $lowerMsg, $durationMatches)) {
                $duration = (int) $durationMatches[1];
            }
            // Try parsing start date from the whole message
            $startDate = $this->parseRelativeDate($lowerMsg);
            if ($startDate && $duration) {
                $endDate = Carbon::parse($startDate)->addDays($duration)->toDateString();
            }
        }

        return [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'duration' => $duration
        ];
    }

    /**
     * Get all available cars matching the brand or name inside user message.
     */
    private function getMatchingCars($lowerMsg, $availableCars)
    {
        $matches = [];
        foreach ($availableCars as $car) {
            $carNameLower = strtolower($car->name);
            $carBrandLower = strtolower($car->brand);

            // Exact brand + name matches, or full match
            if (str_contains($lowerMsg, $carBrandLower . ' ' . $carNameLower) || 
                str_contains($lowerMsg, $carNameLower)) {
                $matches[] = $car;
                continue;
            }

            // Word-by-word match
            $nameWords = explode(' ', $carNameLower);
            $matchedWord = false;
            foreach ($nameWords as $word) {
                $word = trim($word);
                if (strlen($word) >= 3 && str_contains($lowerMsg, $word)) {
                    $matchedWord = true;
                    break;
                }
            }
            if ($matchedWord) {
                $matches[] = $car;
            }
        }
        // Unique the matches by ID
        $uniqueMatches = [];
        $seenIds = [];
        foreach ($matches as $car) {
            if (!in_array($car->id, $seenIds)) {
                $seenIds[] = $car->id;
                $uniqueMatches[] = $car;
            }
        }
        return $uniqueMatches;
    }

    /**
     * Handle state adjustment when multiple cars match user intent.
     */
    private function handleCarAmbiguity($matchingCars, &$bookingState)
    {
        $carIds = [];
        $suggestions = [];
        $reply = "Ditemukan beberapa pilihan mobil yang cocok. Silakan pilih salah satu dengan mengetik nomor pilihannya:<br><br>";
        
        foreach ($matchingCars as $index => $car) {
            $num = $index + 1;
            $carIds[] = $car->id;
            $suggestions[] = (string) $num;
            
            $transmissionLabel = $car->transmission ? $car->transmission->label() : '';
            $colorLabel = $car->color ? "Warna " . $car->color : '';
            $rateLabel = "Rp " . number_format($car->daily_rate, 0, ',', '.') . "/hari";
            
            $reply .= "<strong>{$num}. {$car->brand} {$car->name}</strong><br>";
            $reply .= "• Transmisi: {$transmissionLabel}<br>";
            if ($colorLabel) {
                $reply .= "• {$colorLabel}<br>";
            }
            $reply .= "• Tarif Harian: {$rateLabel}<br><br>";
        }
        
        $reply .= "Ketik angka pilihan Anda (contoh: 1 atau 2).";
        $suggestions[] = "Batal";

        $bookingState['step'] = 'resolve_car_ambiguity';
        $bookingState['ambiguousCarIds'] = $carIds;

        return [
            'reply' => $reply,
            'bookingState' => $bookingState,
            'suggestions' => $suggestions,
            'cars' => $carIds,
        ];
    }
}
