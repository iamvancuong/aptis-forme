<?php

// Cấu hình giới hạn thiết bị đăng nhập đồng thời (dùng bởi middleware SessionLimit).
return [
    // Trần thiết bị mặc định (user.max_devices ghi đè nếu có).
    'max_devices' => (int) env('DEVICE_MAX', 2),

    // Chỉ tính là "đang hoạt động" nếu phát sinh request trong khoảng giờ này.
    'activity_window_hours' => (int) env('DEVICE_ACTIVITY_WINDOW_HOURS', 12),

    // Đủ số lần vi phạm thì khoá tài khoản.
    'block_after_violations' => (int) env('DEVICE_BLOCK_AFTER_VIOLATIONS', 3),

    // Vi phạm cũ hơn số ngày này thì bỏ qua, đếm lại từ đầu.
    'violation_reset_days' => (int) env('DEVICE_VIOLATION_RESET_DAYS', 30),
];
