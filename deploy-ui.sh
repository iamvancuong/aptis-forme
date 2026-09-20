#!/usr/bin/env bash
#
# Deploy nhanh phần FRONTEND (chỉ asset đã build) lên production qua SSH.
# Dùng khi chỉ sửa giao diện (.vue/.css) — KHÔNG đụng code PHP/DB.
#
# Cách dùng:  bash deploy-ui.sh
# Lần đầu: copy .deploy.env.example -> .deploy.env rồi điền thông tin SSH.
#
set -euo pipefail
cd "$(dirname "$0")"

# Nạp cấu hình SSH (file .deploy.env KHÔNG commit lên git)
if [ -f .deploy.env ]; then
    # shellcheck disable=SC1091
    source .deploy.env
else
    echo "❌ Chưa có .deploy.env. Hãy: cp .deploy.env.example .deploy.env  rồi điền thông tin." >&2
    exit 1
fi

: "${SSH_HOST:?Chưa đặt SSH_HOST trong .deploy.env}"
: "${SSH_USER:?Chưa đặt SSH_USER trong .deploy.env}"
: "${SSH_PORT:=22}"
: "${REMOTE_APP:?Chưa đặt REMOTE_APP trong .deploy.env (thư mục chứa public/ trên server)}"

TARGET="$SSH_USER@$SSH_HOST"

# ⚠️ Script này chạy TRÊN MÁY DEV (có Node/npm), KHÔNG chạy trên server.
if ! command -v npm >/dev/null 2>&1; then
    echo "❌ Không tìm thấy 'npm'. Script này phải chạy trên MÁY TÍNH của bạn (máy dev có Node)," >&2
    echo "   KHÔNG chạy trên server. Nó sẽ tự build rồi đẩy lên server qua SSH." >&2
    exit 1
fi

echo "==> [1/4] Build asset (npm run build)..."
npm run build

echo "==> [2/4] Xoá build cũ trên server (tránh file hash cũ tồn đọng)..."
ssh -p "$SSH_PORT" "$TARGET" "rm -rf '$REMOTE_APP/public/build'"

echo "==> [3/4] Đẩy public/build lên $TARGET:$REMOTE_APP/public/ ..."
scp -P "$SSH_PORT" -r public/build "$TARGET:$REMOTE_APP/public/"

echo "==> [4/4] Xoá cache view Laravel (để chắc chắn)..."
ssh -p "$SSH_PORT" "$TARGET" "cd '$REMOTE_APP' && php artisan view:clear" || \
    echo "   (bỏ qua — view:clear lỗi/không cần)"

echo ""
echo "✅ Deploy UI xong. Vào site và hard-refresh trình duyệt (Ctrl+Shift+R)."
