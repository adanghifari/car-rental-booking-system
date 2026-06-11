<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class FaceVerificationService
{
    private function mockResponse(): array
    {
        return [
            'verified' => true,
            'nik' => '3273123456789001',
            'payload' => ['mock' => true, 'verified' => true, 'nik' => '3273123456789001', 'confidence' => 0.95],
        ];
    }

    public function verify(UploadedFile $ktpFile, UploadedFile $selfieFile): array
    {
        $baseUrl = rtrim((string) config('services.face_verify.base_url'), '/');
        $timeout = (int) config('services.face_verify.timeout', 60);
        $useMock = (bool) config('services.face_verify.mock', false);

        try {
            $response = Http::timeout($timeout)
                ->attach('ktp', fopen($ktpFile->getRealPath(), 'r'), $ktpFile->getClientOriginalName())
                ->attach('selfie', fopen($selfieFile->getRealPath(), 'r'), $selfieFile->getClientOriginalName())
                ->post($baseUrl.'/verify');

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'verified' => (bool) \Illuminate\Support\Arr::get($data, 'verified', false),
                    'nik' => \Illuminate\Support\Arr::get($data, 'nik'),
                    'payload' => $data,
                ];
            }
        } catch (\Exception $e) {
            if ($useMock) {
                return $this->mockResponse();
            }
            throw $e;
        }

        if ($useMock) {
            return $this->mockResponse();
        }

        throw new RuntimeException('Face verification service unavailable.');
    }
}
