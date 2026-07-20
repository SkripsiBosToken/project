<?php

namespace App\Support;

/**
 * Status pesanan yang tersimpan di kolom `orders.status`.
 *
 * Nilainya tetap string bahasa Indonesia agar kompatibel dengan data
 * dan view yang sudah ada, tapi dikumpulkan di satu tempat supaya tidak
 * ada lagi typo string yang tersebar di controller dan blade.
 */
final class OrderStatus
{
    public const UNPAID = 'Belum Dibayar';
    public const WAITING_CONFIRMATION = 'Menunggu Konfirmasi';
    public const PROCESSING = 'Diproses';
    public const SHIPPED = 'Dikirim';
    public const COMPLETED = 'Berhasil';
    public const FAILED = 'Gagal';
    public const REFUNDED = 'Pengembalian Dana';

    /** Status yang dianggap pesanan masih berjalan. */
    public const IN_PROGRESS = [
        self::WAITING_CONFIRMATION,
        self::PROCESSING,
        self::SHIPPED,
    ];

    /** Status yang dianggap pesanan berhasil (untuk laporan & dashboard). */
    public const SUCCESSFUL = [
        self::WAITING_CONFIRMATION,
        self::PROCESSING,
        self::SHIPPED,
        self::COMPLETED,
    ];

    /** Status akhir; stok tidak boleh dikembalikan dua kali dari sini. */
    public const FINAL_FAILED = [
        self::FAILED,
        self::REFUNDED,
    ];

    public static function all(): array
    {
        return [
            self::UNPAID,
            self::WAITING_CONFIRMATION,
            self::PROCESSING,
            self::SHIPPED,
            self::COMPLETED,
            self::FAILED,
            self::REFUNDED,
        ];
    }

    /** Apakah pesanan sudah dibayar (stok tidak boleh dikembalikan otomatis). */
    public static function isPaid(string $status): bool
    {
        return in_array($status, self::SUCCESSFUL, true);
    }

    /** Apakah pesanan sudah berada di status akhir yang gagal. */
    public static function isFailed(string $status): bool
    {
        return in_array($status, self::FINAL_FAILED, true);
    }
}
