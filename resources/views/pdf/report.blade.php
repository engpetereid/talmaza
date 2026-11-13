<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: dejavusans; font-size: 11pt; color: #1f2937; direction: rtl; }
        .header { border-bottom: 3px solid #4f46e5; padding-bottom: 8px; margin-bottom: 14px; }
        .header td { vertical-align: middle; }
        .title { font-size: 18pt; font-weight: bold; color: #312e81; }
        .subtitle { font-size: 11pt; color: #6b7280; }
        .meta { font-size: 9.5pt; color: #4b5563; text-align: left; }
        h2 { font-size: 13pt; color: #ffffff; background-color: #4f46e5; padding: 6px 10px; margin: 16px 0 8px 0; }
        h2.orange { background-color: #ea580c; }
        h2.green { background-color: #16a34a; }
        h2.blue { background-color: #2563eb; }
        h2.purple { background-color: #7c3aed; }
        table.grid { width: 100%; border-collapse: collapse; margin-bottom: 6px; }
        table.grid th { background-color: #eef2ff; color: #312e81; border: 1px solid #c7d2fe; padding: 6px; font-size: 10pt; }
        table.grid td { border: 1px solid #e5e7eb; padding: 6px; font-size: 10.5pt; vertical-align: top; }
        .item { border: 1px solid #e5e7eb; background-color: #f9fafb; padding: 8px 10px; margin-bottom: 8px; }
        .item-text { font-size: 11pt; line-height: 1.6; }
        .replies { margin-top: 6px; border-right: 3px solid #a5b4fc; padding-right: 8px; }
        .reply { font-size: 9.5pt; margin-top: 4px; color: #374151; }
        .reply-name { font-weight: bold; color: #4338ca; }
        .reply-name.leader { color: #b45309; }
        .reply-date { color: #9ca3af; font-size: 8.5pt; }
        .kpi td { text-align: center; border: 1px solid #e5e7eb; padding: 8px 4px; }
        .kpi .value { font-size: 15pt; font-weight: bold; color: #4f46e5; }
        .kpi .label { font-size: 9pt; color: #6b7280; }
        .muted { color: #9ca3af; font-style: italic; }
    </style>
</head>
<body>

@php
    $isWeekly = $report->type === 'weekly';
    $date = $report->report_date;
@endphp

{{-- Reusable replies block --}}
@php
    $renderReplies = function ($replies) {
        if (empty($replies)) return '';
        $html = '<div class="replies">';
        foreach ($replies as $reply) {
            $role = ($reply['role'] ?? '') === 'admin' ? '' : ' leader';
            $html .= '<div class="reply"><span class="reply-name' . $role . '">' . e($reply['name'] ?? '') . ':</span> '
                . nl2br(e($reply['text'] ?? ''))
                . (!empty($reply['date']) ? ' <span class="reply-date">(' . e(\Carbon\Carbon::parse($reply['date'])->format('Y-m-d H:i')) . ')</span>' : '')
                . '</div>';
        }
        return $html . '</div>';
    };
@endphp

<table class="header" width="100%">
    <tr>
        <td width="70%">
            <div class="title">{{ $isWeekly ? 'التقرير الأسبوعي' : 'التقرير الشهري' }}</div>
            <div class="subtitle">{{ $family->name ?? '' }} — القائد: {{ $family?->leader?->name ?? 'غير محدد' }}</div>
        </td>
        <td width="30%" class="meta">
            @if($isWeekly)
                التاريخ: {{ $date->format('Y-m-d') }}<br>
            @else
                الشهر: {{ $report->stats_snapshot['month_name'] ?? $date->locale('ar')->monthName }} {{ $date->format('Y') }}<br>
            @endif
            تاريخ الإرسال: {{ $report->created_at?->format('Y-m-d') }}<br>
            حالة الرد: {{ $report->admin_reply_at ? 'تم الرد' : 'بانتظار الرد' }}
        </td>
    </tr>
</table>

@if($isWeekly)
    {{-- ===== Weekly report ===== --}}
    <h2>جدول المواعيد</h2>
    @if(count($sections['timeline']))
        <table class="grid">
            <tr><th width="18%">الوقت</th><th>النشاط</th></tr>
            @foreach($sections['timeline'] as $row)
                <tr>
                    <td>{{ $row['time'] ?? '' }}</td>
                    <td>{{ $row['activity'] ?? '' }}{!! $renderReplies($row['replies']) !!}</td>
                </tr>
            @endforeach
        </table>
    @else
        <p class="muted">لا يوجد</p>
    @endif

    <h2 class="blue">الافتقاد ومعاد الجلسة</h2>
    <table class="grid">
        <tr><th width="30%">ساعات الافتقاد</th><td>{{ $report->visitation_hours !== null ? rtrim(rtrim((string) $report->visitation_hours, '0'), '.') . ' ساعة' : '—' }}
            {!! $renderReplies($report->visitation_replies ?? []) !!}</td></tr>
        <tr><th>معاد الجلسة</th><td>{{ $report->session_time ?: '—' }}
            {!! $renderReplies($report->session_replies ?? []) !!}</td></tr>
    </table>

    <h2 class="green">ما تم تنفيذه والأفكار</h2>
    @forelse($sections['weekly_achievements'] as $item)
        <div class="item">
            <div class="item-text">{!! nl2br(e($item['text'] ?? '')) !!}</div>
            {!! $renderReplies($item['replies']) !!}
        </div>
    @empty
        <p class="muted">لا يوجد</p>
    @endforelse
@else
    {{-- ===== Monthly report ===== --}}
    <h2 class="purple">إحصائيات الشهر</h2>
    @php $snapshot = $report->stats_snapshot ?? []; @endphp
    @if(($snapshot['status'] ?? null) === 'no_data' || empty($snapshot))
        <p class="muted">لا توجد اجتماعات مكتملة في هذا الشهر.</p>
    @else
        <p>عدد الاجتماعات: <b>{{ $snapshot['meetings_count'] ?? 0 }}</b></p>
        @php $metrics = array_filter($metricLabels, fn ($label, $key) => isset($snapshot[$key]), ARRAY_FILTER_USE_BOTH); @endphp
        <table class="kpi" width="100%" style="border-collapse: collapse;">
            @foreach(array_chunk($metrics, 4, true) as $row)
                <tr>
                    @foreach($row as $key => $label)
                        <td width="25%"><div class="value">{{ $snapshot[$key] }}%</div><div class="label">{{ $label }}</div></td>
                    @endforeach
                    @for($i = count($row); $i < 4; $i++)<td width="25%"></td>@endfor
                </tr>
            @endforeach
        </table>
    @endif
    {!! $renderReplies($report->stats_replies ?? []) !!}

    <h2 class="green">ملخص الشهر</h2>
    @forelse($sections['monthly_summary'] as $item)
        <div class="item">
            <div class="item-text">{!! nl2br(e($item['text'] ?? '')) !!}</div>
            {!! $renderReplies($item['replies']) !!}
        </div>
    @empty
        <p class="muted">لا يوجد</p>
    @endforelse

    <h2 class="orange">ملاحظات عن المخدومين</h2>
    @if(count($membersNotes))
        <table class="grid">
            <tr><th width="25%">المخدوم</th><th>الملاحظة</th></tr>
            @foreach($membersNotes as $note)
                <tr>
                    <td><b>{{ $note['name'] }}</b></td>
                    <td>{!! nl2br(e($note['text'] ?? '')) !!}{!! $renderReplies($note['replies']) !!}</td>
                </tr>
            @endforeach
        </table>
    @else
        <p class="muted">لا يوجد</p>
    @endif
@endif

<h2 class="blue">رسالة للأب الكاهن</h2>
@forelse($sections['priest_message'] as $item)
    <div class="item">
        <div class="item-text">{!! nl2br(e($item['text'] ?? '')) !!}</div>
        {!! $renderReplies($item['replies']) !!}
    </div>
@empty
    <p class="muted">لا يوجد</p>
@endforelse

</body>
</html>
