@component('mail::message')
# {{ $isNew ? 'Chào mừng bạn đến với ' . config('app.name') . '!' : 'Tài khoản đã được gia hạn' }}

@if ($isNew)
Tài khoản luyện thi APTIS của bạn đã được tạo. Thông tin đăng nhập:

- **Email:** {{ $email }}
- **Mật khẩu:** {{ $password }}

> Vui lòng đổi mật khẩu ngay ở lần đăng nhập đầu tiên.
@else
Tài khoản **{{ $email }}** của bạn đã được gia hạn thành công.
@endif

@if ($expiresAt)
**Hạn sử dụng:** {{ $expiresAt->format('d/m/Y') }}
@endif

@component('mail::button', ['url' => $loginUrl])
Đăng nhập ngay
@endcomponent

Chúc bạn học tốt,<br>
{{ config('app.name') }}
@endcomponent
