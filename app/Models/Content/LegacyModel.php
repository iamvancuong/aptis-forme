<?php

namespace App\Models\Content;

use Illuminate\Database\Eloquent\Model;
use RuntimeException;

/**
 * Base cho mọi model đọc từ DB v1 (db1) qua connection `legacy`.
 *
 * NGUYÊN TẮC: db1 là kho nội dung dùng chung, v2 CHỈ ĐỌC. Mọi thao tác ghi
 * (create/update/delete) đều bị chặn ngay ở tầng model để không bao giờ có
 * đường vô tình sửa dữ liệu của v1. Soạn/sửa bài học làm ở admin v1.
 */
abstract class LegacyModel extends Model
{
    protected $connection = 'legacy';

    protected static function booted(): void
    {
        $block = function (): void {
            throw new RuntimeException(
                'DB v1 (legacy) là CHỈ ĐỌC — v2 không được ghi vào kho nội dung.'
            );
        };

        static::saving($block);
        static::deleting($block);
    }
}
