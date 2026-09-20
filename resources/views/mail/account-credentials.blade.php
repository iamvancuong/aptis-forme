<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <title>nhaiaptis</title>
</head>
<body style="margin:0; padding:0; background-color:#ffffff; font-family:'Segoe UI',Roboto,Helvetica,Arial,sans-serif; -webkit-font-smoothing:antialiased;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#ffffff; padding:40px 16px;">
        <tr>
            <td align="center">
                <table role="presentation" width="480" cellpadding="0" cellspacing="0" style="max-width:480px; width:100%;">

                    <!-- Brand -->
                    <tr>
                        <td style="padding-bottom:36px;">
                            <span style="font-size:18px; font-weight:800; letter-spacing:-0.4px; color:#0f172a;">nhai<span style="color:#94a3b8;">aptis</span></span>
                        </td>
                    </tr>

                    <!-- Heading -->
                    <tr>
                        <td>
                            @if ($isNew)
                                <h1 style="margin:0; font-size:28px; line-height:1.25; font-weight:800; letter-spacing:-0.5px; color:#0f172a;">
                                    Tài khoản của bạn<br>đã sẵn sàng.
                                </h1>
                                <p style="margin:16px 0 0; font-size:15px; line-height:1.65; color:#64748b;">
                                    Cảm ơn bạn đã tham gia. Dùng thông tin bên dưới để đăng nhập và bắt đầu luyện thi APTIS.
                                </p>
                            @else
                                <h1 style="margin:0; font-size:28px; line-height:1.25; font-weight:800; letter-spacing:-0.5px; color:#0f172a;">
                                    Đã gia hạn<br>thành công.
                                </h1>
                                <p style="margin:16px 0 0; font-size:15px; line-height:1.65; color:#64748b;">
                                    Tài khoản <b style="color:#0f172a;">{{ $email }}</b> đã được gia hạn. Chúc bạn ôn luyện hiệu quả!
                                </p>
                            @endif
                        </td>
                    </tr>

                    @if ($isNew)
                    <!-- Credentials -->
                    <tr>
                        <td style="padding-top:32px;">
                            <div style="font-size:12px; font-weight:600; text-transform:uppercase; letter-spacing:1px; color:#94a3b8;">Email đăng nhập</div>
                            <div style="margin-top:6px; font-size:16px; font-weight:600; color:#0f172a;">{{ $email }}</div>

                            <div style="margin-top:24px; font-size:12px; font-weight:600; text-transform:uppercase; letter-spacing:1px; color:#94a3b8;">Mật khẩu</div>
                            <div style="margin-top:8px; font-family:'SFMono-Regular',Consolas,'Courier New',monospace; font-size:26px; font-weight:700; letter-spacing:4px; color:#0f172a; background-color:#f1f5f9; border:1px solid #e2e8f0; border-radius:12px; padding:18px 20px; text-align:center;">
                                {{ $password }}
                            </div>
                            <div style="margin-top:10px; font-size:13px; color:#94a3b8;">Hãy đổi mật khẩu ngay ở lần đăng nhập đầu tiên.</div>
                        </td>
                    </tr>
                    @endif

                    @if ($expiresAt)
                    <tr>
                        <td style="padding-top:24px;">
                            <div style="border-top:1px solid #f1f5f9; padding-top:16px; font-size:14px; color:#64748b;">
                                Hạn sử dụng: <b style="color:#0f172a;">{{ $expiresAt->format('d/m/Y') }}</b>
                            </div>
                        </td>
                    </tr>
                    @endif

                    <!-- CTA -->
                    <tr>
                        <td style="padding-top:32px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td align="center" style="border-radius:12px; background-color:#0f172a;">
                                        <a href="{{ $loginUrl }}" target="_blank" style="display:block; padding:16px; font-size:15px; font-weight:700; color:#ffffff; text-decoration:none; border-radius:12px;">
                                            Đăng nhập ngay
                                        </a>
                                    </td>
                                </tr>
                            </table>
                            <div style="margin-top:12px; font-size:12px; color:#cbd5e1; text-align:center;">
                                {{ $loginUrl }}
                            </div>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="padding-top:40px;">
                            <div style="border-top:1px solid #f1f5f9; padding-top:20px; font-size:12px; line-height:1.6; color:#cbd5e1;">
                                © {{ date('Y') }} nhaiaptis — nền tảng luyện thi APTIS độc lập, không liên kết chính thức với British Council.<br>
                                Email tự động, vui lòng không trả lời.
                            </div>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
