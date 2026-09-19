# APTIS V2 — Ghi chú dự án (đọc trước khi làm)

> File này để bất kỳ ai (hoặc Claude ở chat mới) hiểu ngay dự án. Đọc kèm `DEPLOY.md`.

## 1. Dự án là gì
Website luyện thi **APTIS** phiên bản 2 — **bán khóa + cho học viên làm bài**.
- **v1** (milaedu.com, database `ujxmchhx_aptis_v2`) đang chạy production, là **kho nội dung bài học** và là nơi admin soạn/sửa bài. v2 KHÔNG sửa v1.
- **v2** (repo này, domain **nhaiaptis.com**) = code MỚI hoàn toàn: UI hiện đại, giá rẻ hơn, **đọc nội dung bài học trực tiếp từ DB v1 (chỉ đọc)**, còn user/đơn hàng/lịch sử làm bài lưu ở **DB riêng của v2**.
- Thư mục tham chiếu code v1: `C:\Cuong\01_coding\aptis-v2` (chỉ để tham khảo logic, KHÔNG sửa).

## 2. Kiến trúc dữ liệu (QUAN TRỌNG NHẤT)
Hai kết nối DB, **cùng 1 MySQL server** trên cPanel:
- **`legacy`** → DB v1 (`ujxmchhx_aptis_v2`). **CHỈ ĐỌC**. Model nội dung ở `app/Models/Content/*`
  (Quiz, Set, Question, Instruction, Feedback, HighScore) — kế thừa `LegacyModel` (chặn mọi thao tác ghi).
- **`mysql`** (mặc định) → DB v2 (database MỚI, vd `ujxmchhx_aptis_forme`). Users, orders, attempts,
  mock_tests, ai_usages, login_sessions, security_flags. **Mọi migration chỉ chạy ở đây.**
- Không đặt FK chéo 2 DB. Bảng nội dung đọc từ v1, không copy.
- ⚠️ `DB_DATABASE` (db2) **PHẢI KHÁC** `ujxmchhx_aptis_v2` (db1) — nếu trùng rồi migrate sẽ đè lên db v1.

## 3. Tech stack
Laravel 12 · PHP 8.2 · **Inertia + Vue 3** (khu app) · **Blade** (marketing/SEO: landing, đăng ký,
thanh toán) · Tailwind v4 (font Be Vietnam Pro, brand indigo→violet) · PayOS · OpenAI (gpt-4o-mini + whisper-1).

## 4. Tính năng (ĐÃ XONG)
- **Marketing**: landing hiện đại (`resources/views/welcome.blade.php`), SEO pages, sitemap, robots.
- **Học thử không cần đăng nhập** (`TrialController`, `/hoc-thu/{skill}`): mỗi kỹ năng 1 lượt/session,
  lấy ĐỀ THẬT từ db1, có instant check; vượt lượt → chuyển đăng ký. Nút hero vào thẳng Reading.
- **Đăng ký → PayOS → tạo tài khoản tự động** (mật khẩu mặc định `12345678`, buộc đổi lần đầu) + email.
- **Practice**: từng câu một (next/prev), panel "Danh sách câu" tìm kiếm, **instant check** (hiện đáp án
  ngay sau mỗi câu), chấm mọi dạng objective (reading/listening/grammar). `QuestionCard.vue` render theo type.
- **Writing/Speaking hiển thị THEO BỘ** (mỗi "Đề" = 1 kịch bản đủ 4 part). Writing nhập text; **Speaking
  tự động**: TTS đọc đề → beep → tự ghi âm → tự chuyển câu → tự nộp (`SpeakingRecorder.vue`).
- **Chấm AI**: Writing (verified thật) + Speaking (Whisper phiên âm → GPT chấm CEFR). `AiService`,
  `AiController`, prompt ở `resources/views/prompts/*`. Có giới hạn lượt (config `services.openai`).
- **Mock Test** (thi thử reading/listening): đồng hồ, bốc đề ngẫu nhiên, chấm từng part. (Writing/Speaking
  KHÔNG cần mock riêng vì mỗi "Đề" đã là full 4 part.)
- **Lịch sử** (review + lọc câu sai + nút chấm AI), **Leaderboard**, **Hướng dẫn**.
- **Admin** (`/admin`, chỉ role admin — có nút Admin trên header): CHỈ quản lý **Học viên** (chi tiết,
  gia hạn, khóa, **thêm/reset lượt AI**, xem lịch sử/đơn) và **Thanh toán/Doanh thu**. KHÔNG CRUD nội dung.
- **Chặn copy text** cho tài khoản không phải admin (`composables/antiCopy.js`).
- Giới hạn thiết bị (chống share tài khoản) — middleware `SessionLimit`.

## 5. Còn lại / CHƯA làm
- **Deploy production** lên nhaiaptis.com (xem `DEPLOY.md`) — bước tiếp theo chính.
- Verify trên production: **audio listening + ảnh đề** (cần symlink storage v1), **ghi âm Speaking** (mic thật),
  kết nối db1 nội bộ, PayOS thật.
- Tùy chọn chưa làm: phát hiện DevTools nâng cao, quên mật khẩu, brand cho vài trang admin.
- Giá đang là **giá tạm** (config `pricing.php` / env `PRICE_*`) — chốt số thật khi go-live.

## 6. Chạy local
```bash
composer install && npm install
cp .env.example .env && php artisan key:generate
# Local dev: có thể để DB_CONNECTION=sqlite cho db2; DB_LEGACY_* trỏ vào bản sao/thẳng db1
php artisan migrate --seed        # tạo admin + học viên demo
npm run build   # hoặc npm run dev
php artisan serve
```
**Tài khoản demo** (seeder): `admin@aptis.local`/`admin1234` · `hocvien@aptis.local`/`hocvien1234`.
Audio/ảnh KHÔNG có ở local (file nằm trên storage v1) — đúng như thiết kế.

## 7. Deploy
Xem `DEPLOY.md` (đầy đủ). Tóm tắt: tạo db2 MỚI → `.env` production (điền secret) → symlink storage v1
→ migrate/seed → đổi mật khẩu admin → cache. `.env.example` đã điền sẵn mọi thứ trừ secret.

## 8. Lưu ý khi sửa code
- Model nội dung (db1) là READ-ONLY — không thêm thao tác ghi.
- Nếu v1 đổi schema bảng nội dung → cập nhật `app/Models/Content/*`.
- Không commit `.env` (có key OpenAI + mật khẩu DB).
- Repo git: `https://github.com/iamvancuong/aptis-forme.git` (branch `main`).
