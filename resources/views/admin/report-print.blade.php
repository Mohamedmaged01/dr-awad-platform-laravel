<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }}</title>
    @php $contact = \App\Support\SiteInfo::contact(); @endphp
    <style>
        * { font-family: 'Tahoma', 'Segoe UI', 'Arial', sans-serif; box-sizing: border-box; }
        body { margin: 0; padding: 24px; color: #1f2937; }
        .header { display: flex; align-items: center; justify-content: space-between; border-bottom: 3px solid #1e5f9f; padding-bottom: 12px; margin-bottom: 16px; }
        .brand { display: flex; align-items: center; gap: 12px; }
        .brand img { height: 56px; width: auto; }
        .brand h1 { font-size: 18px; margin: 0; color: #1e5f9f; }
        .brand p { margin: 2px 0 0; font-size: 12px; color: #6b7280; }
        .meta { text-align: left; font-size: 12px; color: #6b7280; line-height: 1.6; }
        h2 { font-size: 20px; margin: 8px 0; }
        .sub { font-size: 13px; color: #4b5563; margin-bottom: 14px; }
        table { width: 100%; border-collapse: collapse; font-size: 12px; }
        th, td { border: 1px solid #d1d5db; padding: 6px 8px; text-align: right; vertical-align: top; }
        th { background: #f3f4f6; font-weight: 700; }
        tr:nth-child(even) td { background: #fafafa; }
        .toolbar { margin-bottom: 16px; }
        .toolbar button { background: #1e5f9f; color: #fff; border: none; padding: 9px 20px; border-radius: 6px; font-size: 14px; cursor: pointer; }
        .footer { margin-top: 22px; font-size: 11px; color: #9ca3af; text-align: center; }
        @media print { .toolbar { display: none; } body { padding: 0; } }
    </style>
</head>
<body>
    <div class="toolbar"><button onclick="window.print()">🖨️ طباعة / حفظ PDF</button></div>

    <div class="header">
        <div class="brand">
            <img src="{{ asset('images/logo.png') }}" alt="logo" onerror="this.style.display='none'">
            <div>
                <h1>{{ \App\Support\SiteInfo::name() }}</h1>
                <p>{{ \App\Support\SiteInfo::title() }}</p>
            </div>
        </div>
        <div class="meta">
            <div>{{ now()->format('Y-m-d H:i') }}</div>
            <div>{{ $contact['phone_display'] ?? '' }}</div>
        </div>
    </div>

    <h2>{{ $title }}</h2>
    <div class="sub">
        @if ($patient)<strong>المريضة:</strong> {{ $patient->name }} — {{ $patient->file_number }} · @endif
        <strong>الفترة:</strong>
        {{ $from ? $from->format('Y-m-d') : 'من البداية' }} — {{ $to ? $to->format('Y-m-d') : 'حتى الآن' }}
        · <strong>عدد السجلات:</strong> {{ count($rows) }}
    </div>

    <table>
        <thead>
            <tr>@foreach ($headers as $h)<th>{{ $h }}</th>@endforeach</tr>
        </thead>
        <tbody>
            @forelse ($rows as $row)
                <tr>@foreach ($row as $cell)<td>{{ $cell }}</td>@endforeach</tr>
            @empty
                <tr><td colspan="{{ count($headers) }}" style="text-align:center;color:#9ca3af;padding:20px;">لا توجد سجلات في هذه الفترة</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">{{ \App\Support\SiteInfo::name() }} · {{ $contact['phone_display'] ?? '' }} · تم إنشاء هذا التقرير من نظام إدارة العيادة</div>
</body>
</html>
