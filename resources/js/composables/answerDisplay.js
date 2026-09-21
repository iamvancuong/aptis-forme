// Đổi đáp án (lưu dạng chỉ số lựa chọn) thành nội dung dễ đọc.
// DB v1 lưu correct_answers = [1, 5, 3…] là INDEX của lựa chọn → phải tra ngược
// vào metadata của câu hỏi để hiện chữ thật (tiêu đề, đáp án…).
//
// buildAnswerRows(question, yourAnswer, answerKey) → [{label, yours, correct, ok}] | null
// (null = dạng không có đáp án chọn, vd Writing/Speaking → để UI tự xử lý)

const letter = (i) => String.fromCharCode(65 + Number(i));

function isEmpty(v) {
    return v === null || v === undefined || v === '';
}

// Tra lựa chọn theo index; index lạ / không có → trả nguyên giá trị.
function pick(list, idx, fmt = (t) => t) {
    if (isEmpty(idx)) return '';
    const i = Number(idx);
    if (Array.isArray(list) && Number.isInteger(i) && list[i] !== undefined) return fmt(list[i], i);
    return String(idx);
}

function entries(v) {
    if (Array.isArray(v)) return v.map((x, i) => [i, x]);
    if (v && typeof v === 'object') return Object.entries(v);
    return [];
}

function get(obj, k) {
    if (!obj || typeof obj !== 'object') return undefined;
    return obj[k] ?? obj[String(k)];
}

function same(a, b) {
    return !isEmpty(a) && String(a) === String(b);
}

// Câu có nhiều ô: mỗi ô correct_answers[k] là index trong `choicesFor(k)`.
function perItem(correctAnswers, yours, labelFor, choicesFor, fmt) {
    return entries(correctAnswers).map(([k, c]) => {
        const y = get(yours, k);
        const choices = choicesFor(k);
        return {
            label: labelFor(k),
            yours: pick(choices, y, fmt),
            correct: pick(choices, c, fmt),
            ok: same(y, c),
        };
    });
}

export function buildAnswerRows(question, yourAnswer, key) {
    if (!question || !key) return null;
    const m = question.metadata || {};
    const ca = key.correct_answers;
    const type = question.type;

    // Reading Part 2: sắp xếp câu
    if (type === 'sentence_ordering' || (question.skill === 'reading' && Number(question.part) === 2 && key.sentences)) {
        const expected = (key.sentences || []).slice(1);
        const yours = Array.isArray(yourAnswer) ? yourAnswer : [];
        return expected.map((s, i) => {
            const y = yours[i] && typeof yours[i] === 'object' ? yours[i].text : yours[i];
            return { label: `Vị trí ${i + 2}`, yours: y ?? '', correct: s, ok: !isEmpty(y) && String(y).trim() === String(s).trim() };
        });
    }

    // Grammar Part 1: options [{id, text}], correct_option = id
    if (type === 'mcq3' || (Array.isArray(m.options) && key.correct_option !== undefined && m.options[0]?.id !== undefined)) {
        const fmt = (id) => {
            if (isEmpty(id)) return '';
            const o = (m.options || []).find((x) => String(x.id) === String(id));
            return o ? `${o.id}. ${o.text}` : String(id);
        };
        return [{ label: '', yours: fmt(yourAnswer), correct: fmt(key.correct_option), ok: same(yourAnswer, key.correct_option) }];
    }

    // Listening Part 1: choices[], correct_answer = index
    if (key.correct_answer !== undefined && Array.isArray(m.choices)) {
        return [{ label: '', yours: pick(m.choices, yourAnswer), correct: pick(m.choices, key.correct_answer), ok: same(yourAnswer, key.correct_answer) }];
    }

    if (ca === undefined || ca === null) return null;

    // Reading Part 1: đoạn có [BLANK], choices[bi][oi]
    if (type === 'fill_in_blanks_mc' && Array.isArray(m.paragraphs)) {
        return perItem(ca, yourAnswer,
            (k) => String(m.paragraphs[k] ?? `Ô ${Number(k) + 1}`).replace('[BLANK]', '_____'),
            (k) => (m.choices || [])[k]);
    }

    // Reading Part 3: câu hỏi → đoạn A/B/C/D
    if (type === 'text_question_match') {
        return perItem(ca, yourAnswer,
            (k) => `${Number(k) + 1}. ${(m.questions || [])[k] ?? ''}`,
            () => m.options,
            (_, i) => `Đoạn ${letter(i)}`);
    }

    // Reading Part 4: đoạn → tiêu đề
    if (type === 'matching_headings' || (m.headings && m.paragraphs)) {
        return perItem(ca, yourAnswer,
            (k) => `Đoạn ${Number(k) + 1}`,
            () => m.headings,
            (t, i) => `${i + 1}. ${t}`);
    }

    // Grammar Part 2 (vocab): {pairId: word} — đáp án là chữ, không phải index
    if (m.pairs && m.dropdown_pool) {
        return entries(ca).map(([k, c]) => {
            const p = m.pairs.find((x) => String(x.id) === String(k));
            const y = get(yourAnswer, k);
            return { label: p?.prompt ?? k, yours: y ?? '', correct: c, ok: same(y, c) };
        });
    }

    // Listening Part 2: items + choices
    if (m.items && m.choices) {
        return perItem(ca, yourAnswer, (k) => m.items[k] ?? `Mục ${Number(k) + 1}`, () => m.choices);
    }

    // Listening Part 3: statements + shared_choices
    if (m.statements && m.shared_choices) {
        return perItem(ca, yourAnswer, (k) => `${Number(k) + 1}. ${m.statements[k] ?? ''}`, () => m.shared_choices);
    }

    // Listening Part 4: nhiều câu con, mỗi câu có choices riêng
    if (Array.isArray(m.questions) && m.questions[0] && typeof m.questions[0] === 'object' && m.questions[0].choices) {
        return perItem(ca, yourAnswer,
            (k) => `${Number(k) + 1}. ${m.questions[k]?.question ?? ''}`,
            (k) => m.questions[k]?.choices);
    }

    return null;
}
