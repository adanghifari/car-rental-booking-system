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
        $timeout = (int) config('services.face_verify.timeout', 120);
        $useMock = (bool) config('services.face_verify.mock', false);

        try {
            Log::info('Starting face verification', [
                'ktp_size' => $ktpFile->getSize(),
                'selfie_size' => $selfieFile->getSize(),
            ]);

            $response = Http::timeout($timeout)
                ->attach('ktp', fopen($ktpFile->getRealPath(), 'r'), $ktpFile->getClientOriginalName())
                ->attach('selfie', fopen($selfieFile->getRealPath(), 'r'), $selfieFile->getClientOriginalName())
                ->post($baseUrl.'/verify');

            if ($response->successful()) {
                $data = $response->json();
                
                Log::info('Face verification successful', [
                    'verified' => $data['verified'] ?? false,
                    'nik_extracted' => !empty($data['nik']),
                    'distance' => $data['distance'] ?? null,
                ]);
                
                return [
                    'verified' => (bool) \Illuminate\Support\Arr::get($data, 'verified', false),
                    'nik' => \Illuminate\Support\Arr::get($data, 'nik'),
                    'payload' => $data,
                ];
            }

            Log::warning('Face verification returned non-success status', [
                'status' => $response->status(),
                'response' => $response->json(),
            ]);

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::warning('Face verification service connection failed', [
                'error' => $e->getMessage(),
            ]);
            
            if ($useMock) {
                Log::info('Falling back to mock response due to connection failure');
                return $this->mockResponse();
            }
            
            throw new RuntimeException('Face verification service unavailable (connection failed).');
        
        } catch (\Illuminate\Http\Client\RequestException $e) {
            Log::warning('Face verification request failed', [
                'error' => $e->getMessage(),
                'response' => $e->response?->json(),
            ]);
            
            if ($useMock) {
                Log::info('Falling back to mock response due to request failure');
                return $this->mockResponse();
            }
            
            throw new RuntimeException('Face verification request failed.');
        
        } catch (\Exception $e) {
            Log::error('Unexpected face verification error', [
                'error' => $e->getMessage(),
                'class' => get_class($e),
            ]);
            
            if ($useMock) {
                Log::info('Falling back to mock response due to unexpected error');
                return $this->mockResponse();
            }
            
            throw $e;
        }

        if ($useMock) {
            Log::info('Falling back to mock response');
            return $this->mockResponse();
        }

        throw new RuntimeException('Face verification service unavailable.');
    }
}
