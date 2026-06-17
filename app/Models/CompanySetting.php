<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanySetting extends Model
{
    protected $fillable = [
        'company_name',
        'company_email',
        'company_description',
        'address',
        'maps_directions_url',
        'maps_embed_url',
    ];

    public static function defaults(): array
    {
        return [
            'company_name' => 'MD Car Rental',
            'company_email' => 'mdrentalcarr@gmail.com',
            'company_description' => 'MD Car Rental adalah penyedia layanan sewa mobil terpercaya, aman, dan nyaman untuk berbagai kebutuhan perjalanan.',
            'address' => 'Jl. Gatot Subroto No.5, Ujung Pandang Baru, Kec. Tallo, Kota Makassar, Sulawesi Selatan 90212',
            'maps_directions_url' => 'https://www.google.com/maps/dir/?api=1&destination=Jl.+Gatot+Subroto+No.5,+Ujung+Pandang+Baru,+Kec.+Tallo,+Kota+Makassar,+Sulawesi+Selatan+90212',
            'maps_embed_url' => 'https://maps.google.com/maps?q=Jl.%20Gatot%20Subroto%20No.5%2C%20Ujung%20Pandang%20Baru%2C%20Kec.%20Tallo%2C%20Kota%20Makassar%2C%20Sulawesi%20Selatan%2090212&t=&z=16&ie=UTF8&iwloc=&output=embed',
        ];
    }

    public static function current(): self
    {
        return static::query()->firstOrCreate([], static::defaults());
    }

    public function getEffectiveDirectionsUrlAttribute(): string
    {
        if (filled($this->maps_directions_url)) {
            return (string) $this->maps_directions_url;
        }

        return 'https://www.google.com/maps/dir/?api=1&destination='.rawurlencode((string) $this->address);
    }

    public function getEffectiveEmbedUrlAttribute(): string
    {
        $embedUrl = trim((string) ($this->maps_embed_url ?? ''));

        if ($embedUrl !== '' && $this->looksEmbeddableMapUrl($embedUrl)) {
            return $embedUrl;
        }

        return 'https://maps.google.com/maps?q='.rawurlencode((string) $this->address).'&t=&z=16&ie=UTF8&iwloc=&output=embed';
    }

    private function looksEmbeddableMapUrl(string $url): bool
    {
        return str_contains($url, 'output=embed') || str_contains($url, '/maps/embed');
    }
}
