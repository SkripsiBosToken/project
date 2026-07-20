<?php

namespace App\Support;

use App\Actions\SystemAction;
use RuntimeException;

/**
 * Menghitung ongkos kirim berdasarkan jarak haversine antara alamat kantor
 * (tersimpan di tabel `system`) dan koordinat alamat pengiriman.
 */
class ShippingCalculator
{
    public function __construct(private SystemAction $system_action)
    {
    }

    /**
     * @throws RuntimeException bila koordinat kantor belum diatur.
     */
    public function cost(float $latitude, float $longitude): int
    {
        $distance = $this->distanceFromOffice($latitude, $longitude);

        $cost = $distance * (int) config('shipping.rate_per_km');
        $rounding = max(1, (int) config('shipping.rounding'));

        $cost = (int) (ceil($cost / $rounding) * $rounding);

        return max($cost, (int) config('shipping.minimum_cost'));
    }

    public function distanceFromOffice(float $latitude, float $longitude): float
    {
        $office = json_decode((string) $this->system_action->get()['office_address'], true);

        if (! isset($office['latitude'], $office['longitude'])) {
            throw new RuntimeException('Koordinat alamat kantor belum diatur di pengaturan sistem.');
        }

        return $this->haversine(
            $latitude,
            $longitude,
            (float) $office['latitude'],
            (float) $office['longitude']
        );
    }

    public function isWithinServiceArea(float $latitude, float $longitude): bool
    {
        return $this->distanceFromOffice($latitude, $longitude) <= (float) config('shipping.max_distance_km');
    }

    /**
     * Mengambil koordinat dari kolom `users.address` yang berisi JSON.
     *
     * @return array{address: string, latitude: float, longitude: float}
     *
     * @throws RuntimeException bila alamat pelanggan belum lengkap.
     */
    public function resolveUserAddress($user): array
    {
        $address = json_decode((string) $user['address'], true);

        if (! isset($address['latitude'], $address['longitude'])
            || $address['latitude'] === '' || $address['longitude'] === '') {
            throw new RuntimeException('Alamat pengiriman belum lengkap. Silakan lengkapi alamat di halaman profil.');
        }

        return [
            'address' => (string) ($address['address'] ?? ''),
            'latitude' => (float) $address['latitude'],
            'longitude' => (float) $address['longitude'],
        ];
    }

    private function haversine(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = (int) config('shipping.earth_radius_km');

        $lat1 = deg2rad($lat1);
        $lng1 = deg2rad($lng1);
        $lat2 = deg2rad($lat2);
        $lng2 = deg2rad($lng2);

        $dLat = $lat2 - $lat1;
        $dLng = $lng2 - $lng1;

        $a = sin($dLat / 2) ** 2 + cos($lat1) * cos($lat2) * sin($dLng / 2) ** 2;

        return 2 * atan2(sqrt($a), sqrt(1 - $a)) * $earthRadius;
    }
}
