{{-- Demo giao diện luyện thi (nội dung mẫu cố định) cho khách chưa mua. --}}
@php
    $bars = [30,55,40,70,50,85,45,65,35,75,55,90,60,45,80,50,70,40,60,35,75,50,65,45];
@endphp
<section class="mx-auto max-w-4xl px-6 py-16">
    <div class="mb-8 text-center">
        <span class="inline-flex items-center gap-2 rounded-full bg-brand-50 px-4 py-1.5 text-sm font-semibold text-brand-700 ring-1 ring-brand-100">✨ Trải nghiệm thử</span>
        <h2 class="mt-3 text-3xl font-bold text-slate-900 sm:text-4xl">Giao diện luyện thi &amp; AI chấm điểm</h2>
        <p class="mx-auto mt-2 max-w-xl text-slate-500">Xem trước cách bạn luyện từng kỹ năng và nhận nhận xét chi tiết từ AI.</p>
    </div>

    <div class="rounded-3xl bg-white p-4 shadow-sm ring-1 ring-slate-200 sm:p-6">
        {{-- Tabs --}}
        <div id="demoTabs" class="mb-6 flex flex-wrap gap-2">
            @foreach (['speaking' => 'Speaking', 'writing' => 'Writing', 'reading' => 'Reading', 'listening' => 'Listening', 'feedback' => 'AI Feedback'] as $key => $label)
                <button type="button" data-tab="{{ $key }}" class="demo-tab rounded-xl px-4 py-2 text-sm font-semibold transition">{{ $label }}</button>
            @endforeach
        </div>

        {{-- ══════ SPEAKING ══════ --}}
        <div class="demo-panel space-y-4" data-panel="speaking">
            <div class="rounded-2xl ring-1 ring-slate-200 p-5">
                <div class="rounded-lg bg-blue-50 px-3 py-1.5 text-xs font-bold uppercase tracking-wide text-blue-700">Speaking · Part 1</div>
                <p class="mt-3 text-lg font-bold text-slate-900">Tell me about your favorite place to relax.</p>
                <div class="mt-5 flex h-14 items-end justify-center gap-1">
                    @foreach ($bars as $h)
                        <span class="w-1.5 rounded-full bg-blue-400" style="height: {{ $h }}%"></span>
                    @endforeach
                </div>
                <p class="mt-3 text-center text-sm text-slate-400">🎙️ 00:45</p>
                <div class="mt-4 text-center">
                    <a href="{{ route('register') }}" class="inline-block rounded-xl bg-blue-600 px-6 py-3 text-sm font-semibold text-white hover:bg-blue-700">Bắt đầu ghi âm</a>
                    <p class="mt-2 text-xs text-slate-400">Câu trả lời sẽ được AI phân tích.</p>
                </div>
            </div>
            @include('partials.demo-scores', ['title' => 'AI Feedback', 'unit' => '/10', 'rows' => [['Pronunciation', 75], ['Fluency', 68], ['Vocabulary', 72], ['Grammar', 70]], 'note' => 'Bạn đang nói khá tự nhiên. Hãy cải thiện ngữ điệu ở cuối câu và dùng thêm linking words.'])
        </div>

        {{-- ══════ WRITING ══════ --}}
        <div class="demo-panel hidden space-y-4" data-panel="writing">
            <div class="rounded-2xl ring-1 ring-slate-200 p-5">
                <div class="rounded-lg bg-slate-100 px-3 py-1.5 text-xs font-bold uppercase tracking-wide text-slate-700">Writing · Part 2</div>
                <p class="mt-3 font-bold text-slate-900">Write an email (50–60 words) to a friend about a place you visited recently.</p>
                <div class="mt-2 flex items-center justify-between text-sm text-slate-400"><span>124/150 từ</span><span>⏱️ 12:45</span></div>
                <div class="mt-2 rounded-xl bg-slate-50 p-4 text-sm leading-relaxed text-slate-700 ring-1 ring-slate-200">
                    I think it is a good place to visit. The weather was nice and the food was great. I think you will like it too.
                </div>
                <div class="mt-3 text-right">
                    <a href="{{ route('register') }}" class="inline-block rounded-xl bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white hover:bg-slate-800">Nộp bài</a>
                </div>
            </div>
            @include('partials.demo-scores', ['title' => 'AI Feedback', 'unit' => '', 'rows' => [['Grammar', 72], ['Vocabulary', 78], ['Task achievement', 81], ['Organization', 69]], 'note' => null])
            <div class="rounded-xl bg-rose-50 p-4 text-sm ring-1 ring-rose-100">
                <p class="font-semibold text-rose-700">Lỗi phát hiện</p>
                <p class="mt-1 text-rose-600">Lặp cụm “I think” 2 lần — thử thay bằng “From my perspective”.</p>
            </div>
            <div class="rounded-xl bg-emerald-50 p-4 text-sm ring-1 ring-emerald-100">
                <p class="font-semibold text-emerald-700">Gợi ý sửa</p>
                <p class="mt-1 text-emerald-600">“The weather was nice” → “The weather was pleasantly mild”.</p>
            </div>
        </div>

        {{-- ══════ READING ══════ --}}
        <div class="demo-panel hidden space-y-4" data-panel="reading">
            <div class="rounded-2xl ring-1 ring-slate-200 p-5">
                <div class="rounded-lg bg-emerald-50 px-3 py-1.5 text-xs font-bold uppercase tracking-wide text-emerald-700">Reading · Part 2</div>
                <p class="mt-2 text-sm text-slate-400">Điền từ phù hợp vào chỗ trống.</p>
                <div class="mt-3 space-y-3 text-slate-700">
                    <p>Many cities are investing in public transport to <span class="font-semibold text-emerald-600 underline underline-offset-4">reduce</span> traffic congestion.</p>
                    <p>Commuters are encouraged to switch from private cars to <span class="font-semibold text-emerald-600 underline underline-offset-4">buses</span> and trains.</p>
                    <p>This change helps lower emissions and improves <span class="font-semibold text-emerald-600 underline underline-offset-4">air quality</span> in urban areas.</p>
                </div>
            </div>
            <div class="rounded-2xl ring-1 ring-slate-200 p-5">
                <div class="flex items-center justify-between text-sm"><span class="font-semibold text-slate-700">Tiến độ</span><span class="font-bold text-emerald-600">3/3 câu</span></div>
                <div class="mt-2 h-2 w-full overflow-hidden rounded-full bg-slate-100"><div class="h-full rounded-full bg-emerald-500" style="width:100%"></div></div>
                <div class="mt-3 text-right">
                    <a href="{{ route('register') }}" class="inline-block rounded-xl bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-emerald-700">Nộp bài</a>
                </div>
            </div>
        </div>

        {{-- ══════ LISTENING ══════ --}}
        <div class="demo-panel hidden space-y-4" data-panel="listening">
            <div class="rounded-2xl ring-1 ring-slate-200 p-5">
                <div class="rounded-lg bg-cyan-50 px-3 py-1.5 text-xs font-bold uppercase tracking-wide text-cyan-700">Listening · Part 3</div>
                <p class="mt-2 text-sm text-slate-400">Nghe đoạn audio và chọn đáp án đúng.</p>
                <div class="mt-3 flex items-center gap-3 rounded-xl bg-slate-50 p-3 ring-1 ring-slate-200">
                    <span class="grid h-9 w-9 place-items-center rounded-full bg-cyan-500 text-white">▶</span>
                    <div class="flex h-8 flex-1 items-end gap-0.5">
                        @foreach ($bars as $h)<span class="w-1 rounded bg-cyan-300" style="height: {{ $h }}%"></span>@endforeach
                    </div>
                    <span class="text-xs text-slate-400">00:32</span>
                </div>
                <p class="mt-4 font-bold text-slate-900">What does the speaker say about the new schedule?</p>
                <div class="mt-3 grid gap-2 sm:grid-cols-2">
                    <div class="rounded-xl px-4 py-3 text-sm text-slate-600 ring-1 ring-slate-200">A. It starts earlier than before.</div>
                    <div class="rounded-xl bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800 ring-1 ring-emerald-200">✓ It has been extended by two weeks.</div>
                    <div class="rounded-xl px-4 py-3 text-sm text-slate-600 ring-1 ring-slate-200">C. It will be announced next month.</div>
                    <div class="rounded-xl px-4 py-3 text-sm text-slate-600 ring-1 ring-slate-200">D. It only affects weekend classes.</div>
                </div>
                <div class="mt-4 text-right">
                    <a href="{{ route('register') }}" class="inline-block rounded-xl bg-cyan-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-cyan-700">Luyện Listening</a>
                </div>
            </div>
        </div>

        {{-- ══════ AI FEEDBACK ══════ --}}
        <div class="demo-panel hidden space-y-4" data-panel="feedback">
            <div class="rounded-2xl bg-slate-900 p-6 text-white">
                <p class="text-xs font-bold uppercase tracking-wide text-slate-400">Tổng điểm</p>
                <p class="mt-1 text-5xl font-extrabold">72<span class="text-2xl font-bold text-slate-500">/100</span></p>
                <div class="mt-3 h-2 w-full overflow-hidden rounded-full bg-slate-700"><div class="h-full rounded-full bg-gradient-to-r from-brand-500 to-violet-500" style="width:72%"></div></div>
                <p class="mt-2 text-sm text-slate-400">Mức khá — còn cải thiện để đạt B2.</p>
            </div>
            @include('partials.demo-scores', ['title' => 'Phân tích theo tiêu chí', 'unit' => '', 'rows' => [['Grammar', 72], ['Vocabulary', 78], ['Fluency', 68], ['Task achievement', 81]], 'note' => null])
            <div class="rounded-xl bg-amber-50 p-4 text-sm ring-1 ring-amber-100">
                <p class="font-semibold text-amber-700">Điểm cần cải thiện</p>
                <ol class="mt-2 list-decimal space-y-1 pl-5 text-amber-800">
                    <li>Dùng thêm linking words (however, in addition, as a result).</li>
                    <li>Tránh lặp lại cụm “I think” quá nhiều lần.</li>
                    <li>Phát triển câu trả lời dài hơn ở Part 2 — thêm lý do và ví dụ.</li>
                </ol>
            </div>
            <a href="{{ route('register') }}" class="block rounded-xl bg-brand-600 py-3 text-center text-sm font-semibold text-white hover:bg-brand-700">Đăng ký để nhận phân tích chi tiết →</a>
        </div>
    </div>
</section>

<script>
    (function () {
        const root = document.getElementById('demoTabs');
        if (!root) return;
        const tabs = root.querySelectorAll('.demo-tab');
        const panels = document.querySelectorAll('.demo-panel');
        const on = ['bg-slate-900', 'text-white'];
        const off = ['bg-slate-100', 'text-slate-600', 'hover:bg-slate-200'];
        function activate(name) {
            tabs.forEach((t) => {
                const active = t.dataset.tab === name;
                t.classList.remove(...(active ? off : on));
                t.classList.add(...(active ? on : off));
            });
            panels.forEach((p) => p.classList.toggle('hidden', p.dataset.panel !== name));
        }
        tabs.forEach((t) => t.addEventListener('click', () => activate(t.dataset.tab)));
        activate('speaking');
    })();
</script>
