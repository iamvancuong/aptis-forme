@extends('layouts.marketing')

@section('title', 'Luyện thi APTIS 4 kỹ năng: Reading, Listening, Writing, Speaking | nhaiaptis')
@section('meta_description', 'Luyện thi APTIS đủ 4 kỹ năng Reading, Listening, Writing, Speaking: luyện theo part, thi thử chấm điểm và AI chấm bài Viết/Nói. Học thử miễn phí tại nhaiaptis.')

@section('content')
<section class="mx-auto max-w-3xl px-6 py-12">
    <h1 class="text-3xl font-bold text-slate-900">Luyện thi APTIS online</h1>
    <div class="prose prose-slate mt-6 max-w-none">
        <p>Kỳ thi APTIS gồm 4 kỹ năng chính. Nền tảng của chúng tôi bao phủ đầy đủ:</p>
        <ul>
            <li><strong>Reading</strong> — 4 part: điền từ, sắp câu, ghép đoạn, ghép tiêu đề.</li>
            <li><strong>Listening</strong> — 4 part: chọn đáp án, ghép người nói, hội thoại, độc thoại.</li>
            <li><strong>Grammar & Vocabulary</strong> — trắc nghiệm và ghép từ.</li>
            <li><strong>Writing & Speaking</strong> — chấm bằng AI, phản hồi chi tiết.</li>
        </ul>
        <p>Luyện tập từng part hoặc thi thử full đề có tính giờ, xem lại đáp án và lời giải,
        theo dõi tiến bộ qua lịch sử làm bài và bảng xếp hạng.</p>
    </div>
    <a href="{{ route('register') }}" class="mt-8 inline-block rounded-xl bg-indigo-600 px-6 py-3 text-sm font-semibold text-white hover:bg-indigo-700">
        Đăng ký luyện thi
    </a>
</section>
@endsection
