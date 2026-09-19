import { onBeforeUnmount, onMounted } from 'vue';

/**
 * Chặn sao chép/bôi đen/chuột phải nội dung cho tài khoản KHÔNG phải admin.
 * Bảo vệ nội dung bài học khỏi bị copy hàng loạt. Admin không bị chặn để tiện
 * kiểm tra/biên tập. Chỉ là lớp chặn phía client (chống copy thông thường).
 *
 * @param {() => boolean} isAdmin  hàm trả về true nếu là admin
 */
export function useAntiCopy(isAdmin) {
    const block = (e) => {
        if (!isAdmin()) e.preventDefault();
    };
    const events = ['copy', 'cut', 'contextmenu', 'selectstart', 'dragstart'];

    onMounted(() => {
        events.forEach((ev) => document.addEventListener(ev, block));
        if (!isAdmin()) document.body.classList.add('select-none');
    });

    onBeforeUnmount(() => {
        events.forEach((ev) => document.removeEventListener(ev, block));
        document.body.classList.remove('select-none');
    });
}
