<?php

namespace App\Support;

/**
 * Truy vấn danh sách sale giới thiệu (định nghĩa ở config/sales.php).
 * Mã chuẩn hoá về CHỮ HOA để so khớp không phân biệt hoa/thường.
 */
class Sales
{
    public static function normalize(?string $code): string
    {
        return strtoupper(trim((string) $code));
    }

    public static function isValid(?string $code): bool
    {
        $rep = config('sales.reps.' . self::normalize($code));

        return is_array($rep) && ($rep['active'] ?? false) === true;
    }

    public static function resolve(?string $code): ?string
    {
        return self::isValid($code) ? self::normalize($code) : null;
    }

    public static function name(?string $code): string
    {
        $code = self::normalize($code);

        return config('sales.reps.' . $code . '.name', $code);
    }

    public static function active(): array
    {
        return array_filter(
            config('sales.reps', []),
            fn ($rep) => ($rep['active'] ?? false) === true,
        );
    }
}
