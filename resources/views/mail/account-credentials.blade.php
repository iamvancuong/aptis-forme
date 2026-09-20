<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <title>nhaiaptis</title>
</head>
<body style="margin:0; padding:0; background-color:#f1f5f9; font-family:'Segoe UI',Roboto,Helvetica,Arial,sans-serif; -webkit-font-smoothing:antialiased;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f1f5f9; padding:24px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="max-width:600px; width:100%; background-color:#ffffff; border-radius:16px; overflow:hidden; box-shadow:0 1px 3px rgba(0,0,0,0.06);">

                    <!-- Header gradient -->
                    <tr>
                        <td style="background:linear-gradient(135deg,#4f46e5,#7c3aed); padding:28px 32px; text-align:center;">
                            <div style="font-size:24px; font-weight:800; letter-spacing:-0.5px; color:#ffffff;">
                                🐦 nhaiaptis
                            </div>
                            <div style="margin-top:4px; font-size:13px; color:#e0e7ff;">Luyện thi APTIS online</div>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding:32px;">
                            @if ($isNew)
                                <h1 style="margin:0 0 8px; font-size:20px; color:#0f172a;">Chào mừng bạn! 🎉</h1>
                                <p style="margin:0 0 20px; font-size:15px; line-height:1.6; color:#475569;">
                                    Tài khoản luyện thi APTIS của bạn đã được tạo. Dưới đây là thông tin đăng nhập:
                                </p>

                                <!-- Credentials card -->
                                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f8fafc; border:1px solid #e2e8f0; border-radius:12px;">
                                    <tr>
                                        <td style="padding:16px 20px;">
                                            <div style="font-size:12px; color:#94a3b8; text-transform:uppercase; letter-spacing:0.5px;">Email</div>
                                            <div style="margin-top:2px; font-size:15px; font-weight:600; color:#0f172a;">{{ $email }}</div>
                                        </td>
                                    </tr>
                                    <tr><td style="border-top:1px solid #e2e8f0;"></td></tr>
                                    <tr>
                                        <td style="padding:16px 20px;">
                                            <div style="font-size:12px; color:#94a3b8; text-transform:uppercase; letter-spacing:0.5px;">Mật khẩu</div>
                                            <div style="margin-top:4px; display:inline-block; font-family:'Courier New',monospace; font-size:18px; font-weight:700; letter-spacing:1px; color:#4f46e5; background-color:#eef2ff; padding:6px 14px; border-radius:8px;">{{ $password }}</div>
                                        </td>
                                    </tr>
                                </table>

                                <p style="margin:16px 0 0; font-size:13px; line-height:1.6; color:#b45309; background-color:#fffbeb; border:1px solid #fde68a; border-radius:8px; padding:12px 14px;">
                                    ⚠️ Vì lý do bảo mật, vui lòng <b>đổi mật khẩu ngay</b> ở lần đăng nhập đầu tiên.
                                </p>
                            @else
                                <h1 style="margin:0 0 8px; font-size:20px; color:#0f172a;">Tài khoản đã được gia hạn ✅</h1>
                                <p style="margin:0 0 8px; font-size:15px; line-height:1.6; color:#475569;">
                                    Tài khoản <b style="color:#0f172a;">{{ $email }}</b> của bạn đã được gia hạn thành công. Chúc bạn ôn luyện hiệu quả!
                                </p>
                            @endif

                            @if ($expiresAt)
                                <p style="margin:20px 0 0; font-size:15px; color:#475569;">
                                    <span style="color:#94a3b8;">Hạn sử dụng:</span>
                                    <b style="color:#0f172a;">{{ $expiresAt->format('d/m/Y') }}</b>
                                </p>
                            @endif

                            <!-- CTA button -->
                            <table role="presentation" cellpadding="0" cellspacing="0" style="margin:28px 0 8px;">
                                <tr>
                                    <td align="center" style="border-radius:12px; background:linear-gradient(135deg,#4f46e5,#7c3aed);">
                                        <a href="{{ $loginUrl }}" target="_blank"
                                           style="display:inline-block; padding:14px 32px; font-size:15px; font-weight:700; color:#ffffff; text-decoration:none; border-radius:12px;">
                                            Đăng nhập ngay →
                                        </a>
                                    </td>
                                </tr>
                            </table>
                            <p style="margin:8px 0 0; font-size:12px; color:#94a3b8;">
                                Hoặc mở: <a href="{{ $loginUrl }}" style="color:#6366f1;">{{ $loginUrl }}</a>
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="padding:20px 32px; background-color:#f8fafc; border-top:1px solid #e2e8f0; text-align:center;">
                            <p style="margin:0; font-size:13px; color:#64748b;">Chúc bạn học tốt,<br><b style="color:#334155;">Đội ngũ nhaiaptis</b></p>
                            <p style="margin:12px 0 0; font-size:11px; line-height:1.5; color:#94a3b8;">
                                © {{ date('Y') }} nhaiaptis · Nền tảng luyện thi APTIS độc lập, không liên kết chính thức với British Council.<br>
                                Email này được gửi tự động, vui lòng không trả lời.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
