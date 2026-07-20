<?php

namespace App\Support;

use App\Models\Product;
use App\Models\System;
use Illuminate\Support\Str;

/**
 * Membangun data terstruktur (JSON-LD) schema.org.
 *
 * Dipakai mesin pencari untuk menampilkan rich result: kartu bisnis lokal,
 * harga & ketersediaan produk, rating bintang, dan breadcrumb di hasil
 * pencarian. Semua data diambil dari pengaturan sistem sehingga ikut
 * berubah saat admin memperbarui profil toko.
 */
class Seo
{
    /**
     * Profil bisnis. `Caterer` adalah subtipe LocalBusiness yang paling
     * spesifik untuk usaha katering, sehingga lebih mudah dikenali Google
     * untuk pencarian lokal seperti "catering malang".
     */
    public static function localBusiness(?System $setting): array
    {
        if (! $setting) {
            return [];
        }

        $office = json_decode($setting->office_address ?? '{}', true) ?: [];
        $socials = json_decode($setting->social_media ?? '[]', true) ?: [];
        $coverage = json_decode($setting->our_coverage ?? '[]', true) ?: [];

        // Google menolak URL relatif pada properti image/logo.
        $logo = self::absoluteUrl($setting->logo);

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Caterer',
            '@id' => url('/') . '#business',
            'name' => $setting->name ?: 'Kusuka Catering',
            'url' => url('/'),
            'image' => $logo,
            'logo' => $logo,
            'description' => 'Layanan catering di Malang untuk acara pernikahan, perusahaan, snack box, hampers, dan nasi tumpeng.',
            'priceRange' => 'Rp',
            'currenciesAccepted' => 'IDR',
            'paymentAccepted' => 'Transfer Bank, Virtual Account, QRIS',
            'servesCuisine' => 'Indonesian',
        ];

        if ($setting->phone_number) {
            // Nomor lokal dinormalisasi ke format internasional (+62).
            $schema['telephone'] = preg_replace('/^0/', '+62', $setting->phone_number);
        }

        if (! empty($office['address'])) {
            $schema['address'] = array_filter([
                '@type' => 'PostalAddress',
                'streetAddress' => $office['address'],
                'addressLocality' => 'Malang',
                'addressRegion' => 'Jawa Timur',
                'postalCode' => $office['postal_code'] ?? null,
                'addressCountry' => 'ID',
            ]);
        }

        if (isset($office['latitude'], $office['longitude']) && $office['latitude'] !== '') {
            $schema['geo'] = [
                '@type' => 'GeoCoordinates',
                'latitude' => (float) $office['latitude'],
                'longitude' => (float) $office['longitude'],
            ];
        }

        // Wilayah layanan membantu pencarian "catering dekat saya".
        $schema['areaServed'] = self::areaServed($coverage);

        // sameAs menautkan situs ke profil media sosial resmi.
        $sameAs = array_values(array_filter(array_map(
            fn ($item) => $item['href'] ?? null,
            $socials
        )));

        if ($sameAs) {
            $schema['sameAs'] = $sameAs;
        }

        return array_filter($schema, fn ($value) => $value !== null && $value !== []);
    }

    /**
     * Menyusun areaServed dari kolom `our_coverage`.
     *
     * Kolom itu menyimpan poligon area antar (daftar pasangan [lat, lng]),
     * yang dipetakan ke GeoShape schema.org sehingga cakupan layanan yang
     * sebenarnya ikut terbaca mesin pencari. Bila formatnya berupa daftar
     * nama wilayah, dipakai sebagai Place. Kota Malang selalu disertakan
     * sebagai penanda area utama.
     */
    private static function areaServed(array $coverage): array
    {
        $areas = [['@type' => 'City', 'name' => 'Malang']];

        if (! $coverage) {
            return $areas;
        }

        // Bentuk poligon: [[lat, lng], [lat, lng], ...]
        $isPolygon = isset($coverage[0]) && is_array($coverage[0])
            && count($coverage[0]) === 2
            && is_numeric($coverage[0][0] ?? null);

        if ($isPolygon) {
            $points = [];
            foreach ($coverage as $point) {
                if (is_numeric($point[0] ?? null) && is_numeric($point[1] ?? null)) {
                    $points[] = $point[0] . ' ' . $point[1];
                }
            }

            // GeoShape.polygon harus tertutup: titik akhir sama dengan awal.
            if (count($points) >= 3) {
                if ($points[0] !== end($points)) {
                    $points[] = $points[0];
                }

                $areas[] = [
                    '@type' => 'GeoShape',
                    'polygon' => implode(' ', $points),
                ];
            }

            return $areas;
        }

        // Bentuk daftar nama wilayah.
        foreach ($coverage as $area) {
            $name = is_array($area) ? ($area['label'] ?? $area['name'] ?? null) : $area;

            if (is_string($name) && trim($name) !== '') {
                $areas[] = ['@type' => 'Place', 'name' => trim($name)];
            }
        }

        return $areas;
    }

    /**
     * Mengubah path relatif menjadi URL absolut. Mengembalikan null bila
     * nilainya kosong atau jelas bukan path/URL gambar yang sah.
     */
    private static function absoluteUrl(?string $value): ?string
    {
        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        if (Str::startsWith($value, ['http://', 'https://'])) {
            return $value;
        }

        // Nilai tanpa garis miring dan tanpa ekstensi (mis. data dummy
        // "test") bukan path gambar — lebih baik dihilangkan daripada
        // mengirim URL rusak ke mesin pencari.
        if (! Str::startsWith($value, '/') && ! Str::contains($value, '.')) {
            return null;
        }

        return url($value);
    }

    /**
     * Schema situs + kotak pencarian, agar Google bisa menampilkan
     * sitelinks search box.
     */
    public static function website(?System $setting): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            '@id' => url('/') . '#website',
            'name' => $setting->name ?? 'Kusuka Catering',
            'url' => url('/'),
            'inLanguage' => 'id-ID',
            'publisher' => ['@id' => url('/') . '#business'],
        ];
    }

    /**
     * Schema produk: harga, ketersediaan, dan rating.
     *
     * @param  \Illuminate\Support\Collection  $variants  varian aktif produk
     */
    public static function product(Product $product, $variants, ?float $ratingValue = null, int $ratingCount = 0): array
    {
        $prices = $variants->pluck('price')->filter();
        $inStock = $variants->sum('stock') > 0;
        $photos = json_decode($variants->first()->photo ?? '[]', true) ?: [];

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => $product->name,
            'description' => Str::limit(strip_tags($variants->first()->description ?? $product->name), 300),
            'category' => $product->category->name ?? null,
            'brand' => [
                '@type' => 'Brand',
                'name' => 'Kusuka Catering',
            ],
            'offers' => [
                '@type' => 'AggregateOffer',
                'priceCurrency' => 'IDR',
                'lowPrice' => (int) $prices->min(),
                'highPrice' => (int) $prices->max(),
                'offerCount' => $variants->count(),
                'availability' => $inStock
                    ? 'https://schema.org/InStock'
                    : 'https://schema.org/OutOfStock',
                'seller' => ['@id' => url('/') . '#business'],
            ],
        ];

        if ($photos) {
            // URL absolut: Google menolak path relatif pada properti image.
            $schema['image'] = array_map(fn ($photo) => Str::startsWith($photo, 'http') ? $photo : url($photo), $photos);
        }

        // Rating hanya disertakan bila benar-benar ada ulasan — menyertakan
        // aggregateRating palsu melanggar pedoman rich result Google.
        if ($ratingCount > 0 && $ratingValue > 0) {
            $schema['aggregateRating'] = [
                '@type' => 'AggregateRating',
                'ratingValue' => round($ratingValue, 1),
                'reviewCount' => $ratingCount,
                'bestRating' => 5,
                'worstRating' => 1,
            ];
        }

        return array_filter($schema, fn ($value) => $value !== null);
    }

    /**
     * Breadcrumb agar hasil pencarian menampilkan jalur navigasi,
     * bukan URL mentah.
     *
     * @param  array<string, string>  $items  [label => url]
     */
    public static function breadcrumbs(array $items): array
    {
        $position = 1;

        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => array_map(function ($label, $url) use (&$position) {
                return [
                    '@type' => 'ListItem',
                    'position' => $position++,
                    'name' => $label,
                    'item' => $url,
                ];
            }, array_keys($items), array_values($items)),
        ];
    }

    /**
     * Daftar produk pada halaman katalog.
     */
    public static function itemList($products): array
    {
        $position = 1;

        return [
            '@context' => 'https://schema.org',
            '@type' => 'ItemList',
            'itemListElement' => $products->map(function ($product) use (&$position) {
                return [
                    '@type' => 'ListItem',
                    'position' => $position++,
                    'name' => $product->name,
                    'url' => route('catalogue-detail', [
                        'id' => $product->id,
                        'slug' => Str::slug($product->name),
                    ]),
                ];
            })->values()->all(),
        ];
    }
}
