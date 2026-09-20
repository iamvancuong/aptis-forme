<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Lưới an toàn: đối soát đơn pending với PayOS (phòng khi webhook không tới)
// → tự tạo tài khoản + gửi mail. Cần 1 cron `schedule:run` mỗi phút trên cPanel.
Schedule::command('payos:reconcile')->everyTwoMinutes()->withoutOverlapping();
