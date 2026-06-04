<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class FaceVerificationService
{
    public function verify(UploadedFile $ktpFile, UploadedFile $selfieFile): array
    {
        $baseUrl = rtrim((string) config('services.face_verify.base_url'), '/');
        $timeout = (int) config('services.face_verify.timeout', 60);

        $response = Http::timeout($timeout)
            ->attach('ktp', fopen($ktpFile->getRealPath(), 'r'), $ktpFile->getClientOriginalName())
            ->attach('selfie', fopen($selfieFile->getRealPath(), 'r'), $selfieFile->getClientOriginalName())
            ->post($baseUrl.'/verify');

        if (! $response->successful()) {
            Log::warning('Face verification failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            throw new RuntimeException('Face verification service unavailable.');
        }

        $data = $response->json();

        return [
            'verified' => (bool) Arr::get($data, 'verified', false),
            'nik' => Arr::get($data, 'nik'),
            'payload' => $data,
        ];
    }
}
