# DEPLOY — APTIS V2 lên cPanel

> v2 chạy **cùng cPanel, cùng MySQL server** với v1. `db1` = kho nội dung (chỉ đọc),
> `db2` = dữ liệu riêng của v2. Domain mới hoàn toàn.

## 0. Yêu cầu
- PHP 8.2+, Composer, Node (để build asset — có thể build ở máy rồi upload thư mục `public/build`).
- Quyền tạo database trong cPanel.
- Tài khoản **PayOS mới** (Client ID / API Key / Checksum Key).

## 1. Tạo database db2
Trong cPanel → **MySQL Databases**:
1. Tạo database mới, ví dụ `ujxmchhx_aptis_v2`.
2. Tạo user DB (hoặc dùng user sẵn có) và **gán quyền** cho user đó trên **cả** `db2` **và** `db1`
   (v2 cần đọc db1). Không cấp quyền ghi lên db1 nếu muốn chắc chắn (code đã tự chặn ghi).

## 2. Upload mã nguồn
- Đưa toàn bộ repo v2 vào thư mục của domain mới (ví dụ `~/aptis-v2/`), document root trỏ vào `public/`.
- `composer install --no-dev --optimize-autoloader`
- Build asset: chạy `npm ci && npm run build` (ở máy dev cũng được, rồi upload `public/build`).

## 3. Cấu hình `.env` (production)
```dotenv
APP_NAME="APTIS V2"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://<domain-moi>

# db2 — dữ liệu v2
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ujxmchhx_aptis_v2
DB_USERNAME=<user>
DB_PASSWORD=<pass>

# db1 — LEGACY (chỉ đọc), NỘI BỘ vì cùng MySQL server
DB_LEGACY_HOST=127.0.0.1
DB_LEGACY_PORT=3306
DB_LEGACY_DATABASE=ujxmchhx_aptis_test_2026
DB_LEGACY_USERNAME=<user-co-quyen-db1>
DB_LEGACY_PASSWORD=<pass>

# PayOS THẬT (tài khoản mới) — TẮT giả lập
PAYOS_FAKE=false
PAYOS_VERIFY_SSL=true
PAYOS_CLIENT_ID=...
PAYOS_API_KEY=...
PAYOS_CHECKSUM_KEY=...

# Giá (chốt số thật ở đây, ghi đè config)
PRICE_WEEK=...
PRICE_MONTH=...

# Mail (gửi thông tin tài khoản sau thanh toán)
MAIL_MAILER=smtp
MAIL_HOST=...
MAIL_PORT=587
MAIL_USERNAME=...
MAIL_PASSWORD=...
MAIL_FROM_ADDRESS="no-reply@<domain-moi>"
MAIL_FROM_NAME="APTIS V2"

FILESYSTEM_DISK=local
```
Rồi: `php artisan key:generate`

## 4. Media dùng chung (symlink storage v1)
Audio/ảnh nằm ở `storage/app/public` của **v1**. Cho v2 đọc chung:
```bash
# Xoá thư mục public rỗng của v2 rồi trỏ sang v1 (đường dẫn tuyệt đối tới v1)
rm -rf ~/aptis-v2/storage/app/public
ln -s ~/<duong-dan-v1>/storage/app/public ~/aptis-v2/storage/app/public

# Link public/storage → storage/app/public như thường lệ
php artisan storage:link
```
> Kết quả: MediaController của v2 đọc trúng file audio của v1. Ký URL bằng `APP_KEY`
> riêng của v2 nên link không dùng lại được ở domain khác.

## 5. Migrate + tạo admin
```bash
php artisan migrate --force        # CHỈ chạy trên db2 (legacy không có migration)
php artisan db:seed --force        # tạo admin@aptis.local — ĐỔI MẬT KHẨU ngay sau đó
```
> ⚠️ Đổi email/mật khẩu admin mặc định trước khi mở bán. Có thể sửa seeder hoặc đổi trong DB.

## 6. Webhook PayOS
Trong dashboard PayOS (tài khoản mới), đặt Webhook URL:
```
https://<domain-moi>/webhooks/payos
```

## 7. Tối ưu production
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```
> Nếu đổi `.env` sau khi cache → chạy lại `php artisan config:cache` (giống lưu ý ở v1).

## 8. Kiểm tra sau deploy (smoke test)
- [ ] `/` hiển thị landing, `/sitemap.xml`, `/robots.txt` OK
- [ ] Đăng ký → PayOS thật tạo link/QR → thanh toán → nhận email → đăng nhập → buộc đổi mật khẩu
- [ ] Practice một bộ đề → **audio listening phát được** (symlink OK) → chấm điểm
- [ ] Admin `/admin` xem được user + đơn + doanh thu
- [ ] Sửa nội dung bài học ở **admin v1** → v2 thấy ngay (đọc realtime)

## Ghi chú
- Không bao giờ chạy `migrate` với connection `legacy`.
- Nếu v1 đổi cấu trúc bảng nội dung (quizzes/sets/questions…), cập nhật model `App\Models\Content\*` cho khớp.
- Pha 4 (chấm AI Writing/Speaking) cần thêm `OPENAI_API_KEY` khi triển khai.
