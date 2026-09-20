{{-- Bảng điểm tiêu chí (demo). Tham số: $title, $unit, $rows [[label, percent], ...], $note --}}
<div class="rounded-2xl ring-1 ring-slate-200 p-5">
    <div class="mb-3 inline-block rounded-md bg-slate-100 px-2.5 py-1 text-xs font-bold uppercase tracking-wide text-slate-600">{{ $title }}</div>
    <div class="space-y-3">
        @foreach ($rows as [$label, $pct])
            <div>
                <div class="flex items-center justify-between text-sm">
                    <span class="font-medium text-slate-600">{{ $label }}</span>
                    <span class="font-bold text-slate-900">{{ $unit === '/10' ? number_format($pct / 10, 1) . $unit : $pct }}</span>
                </div>
                <div class="mt-1 h-2 w-full overflow-hidden rounded-full bg-slate-100">
                    <div class="h-full rounded-full bg-brand-600" style="width: {{ $pct }}%"></div>
                </div>
            </div>
        @endforeach
    </div>
    @if (! empty($note))
        <div class="mt-4 rounded-xl bg-slate-50 p-3 text-sm text-slate-600 ring-1 ring-slate-200">
            <span class="font-semibold text-slate-700">Nhận xét: </span>{{ $note }}
        </div>
    @endif
</div>
