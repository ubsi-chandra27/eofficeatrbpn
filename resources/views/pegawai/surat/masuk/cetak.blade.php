<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Surat Masuk {{ $surat->nomor_surat ?? '' }}</title>
    <style>
        body{font-family:Arial,sans-serif;color:#111;margin:36px;line-height:1.5} h1{text-align:center;font-size:20px}
        table{width:100%;border-collapse:collapse;margin-top:24px}th,td{padding:9px;border:1px solid #999;text-align:left;vertical-align:top}th{width:28%;background:#f2f2f2}
        .actions{text-align:right;margin-bottom:20px}@media print{.actions{display:none}body{margin:0}}
    </style>
</head>
<body>
    <div class="actions"><button onclick="window.print()">Cetak</button></div>
    <h1>SURAT MASUK</h1>
    <table>
        <tr><th>Nomor Surat</th><td>{{ $surat->nomor_surat ?? '-' }}</td></tr>
        <tr><th>Tanggal</th><td>{{ $surat?->tanggal_surat?->translatedFormat('d F Y') ?? '-' }}</td></tr>
        <tr><th>Asal Surat</th><td>{{ $surat->asal_surat ?? '-' }}</td></tr>
        <tr><th>Perihal</th><td>{{ $surat->perihal ?? '-' }}</td></tr>
        <tr><th>Metode</th><td>{{ $surat->metode ?? '-' }}</td></tr>
        <tr><th>Status</th><td>{{ $surat->status_label ?? '-' }}</td></tr>
        <tr><th>Keterangan</th><td>{{ $surat->deskripsi ?? '-' }}</td></tr>
    </table>
</body>
</html>
