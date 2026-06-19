<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class CloudinaryMediaService
{
    private const STORAGE_PREFIX = 'cloudinary://';

    public function configured(): bool
    {
        return filled($this->cloudName()) && filled($this->apiKey()) && filled($this->apiSecret());
    }

    public function upload(UploadedFile $file, string $folder): string
    {
        if (! $this->configured()) {
            throw new RuntimeException('Cloudinary credentials are not configured.');
        }

        $params = [
            'folder' => trim($this->folder().'/'.trim($folder, '/'), '/'),
            'timestamp' => (string) time(),
            'unique_filename' => 'true',
            'use_filename' => 'true',
        ];
        $params['signature'] = $this->signature($params);
        $params['api_key'] = $this->apiKey();

        $stream = fopen($file->getRealPath(), 'r');

        try {
            $response = Http::asMultipart()
                ->attach('file', $stream, $file->getClientOriginalName())
                ->post($this->apiUrl('upload'), $params);
        } finally {
            if (is_resource($stream)) {
                fclose($stream);
            }
        }

        if (! $response->successful()) {
            throw new RuntimeException('Cloudinary upload failed: '.$response->body());
        }

        $publicId = $response->json('public_id');

        if (! is_string($publicId) || $publicId === '') {
            throw new RuntimeException('Cloudinary upload did not return a public_id.');
        }

        return self::STORAGE_PREFIX.$publicId;
    }

    public function delete(string $path): void
    {
        $publicId = $this->publicId($path);

        if (! $this->configured() || $publicId === null) {
            return;
        }

        $params = [
            'invalidate' => 'true',
            'public_id' => $publicId,
            'timestamp' => (string) time(),
        ];
        $params['signature'] = $this->signature($params);
        $params['api_key'] = $this->apiKey();

        Http::asForm()->post($this->apiUrl('destroy'), $params);
    }

    public function url(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://') || str_starts_with($path, '/')) {
            return $path;
        }

        $publicId = $this->publicId($path);

        if ($publicId === null || blank($this->cloudName())) {
            return null;
        }

        return sprintf(
            'https://res.cloudinary.com/%s/image/upload/%s',
            rawurlencode($this->cloudName()),
            collect(explode('/', $publicId))->map(fn (string $segment) => rawurlencode($segment))->implode('/')
        );
    }

    public function isCloudinaryPath(?string $path): bool
    {
        return is_string($path) && str_starts_with($path, self::STORAGE_PREFIX);
    }

    private function publicId(string $path): ?string
    {
        if (! $this->isCloudinaryPath($path)) {
            return null;
        }

        return substr($path, strlen(self::STORAGE_PREFIX)) ?: null;
    }

    private function apiUrl(string $action): string
    {
        return sprintf('https://api.cloudinary.com/v1_1/%s/image/%s', rawurlencode($this->cloudName()), $action);
    }

    private function signature(array $params): string
    {
        ksort($params);

        $payload = collect($params)
            ->reject(fn ($value, string $key) => in_array($key, ['api_key', 'file', 'resource_type'], true) || blank($value))
            ->map(fn ($value, string $key) => $key.'='.$value)
            ->implode('&');

        return sha1($payload.$this->apiSecret());
    }

    private function cloudName(): string
    {
        return (string) config('services.cloudinary.cloud_name', '');
    }

    private function apiKey(): string
    {
        return (string) config('services.cloudinary.api_key', '');
    }

    private function apiSecret(): string
    {
        return (string) config('services.cloudinary.api_secret', '');
    }

    private function folder(): string
    {
        return trim((string) config('services.cloudinary.folder', 'car-rental-booking-system'), '/');
    }
}
