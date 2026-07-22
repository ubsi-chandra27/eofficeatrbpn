<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Disposisi - {{ $disposisi->disposisi->surat->nomor_surat ?? '-' }}</title>
    <style>
        body { font-family: Arial, sans-serif; color: #111; margin: 32px; }
        .header { text-align: center; border-bottom: 2px solid #111; margin-bottom: 24px; padding-bottom: 12px; }
        h2 { margin: 0; font-size: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th, td { border: 1px solid #111; padding: 9px; text-align: left; vertical-align: top; }
        th { width: 30%; background: #f2f2f2; }
    </style>
</head>
<body onload="window.print()">
    <div class="header"><h2>LEMBAR DISPOSISI</h2></div>
    <table>
        <tr><th>Nomor Surat</th><td>{{ $disposisi->disposisi->surat->nomor_surat ?? '-' }}</td></tr>
        <tr><th>Tanggal Surat</th><td>{{ optional($disposisi->disposisi->surat->tanggal_surat)->format('d-m-Y') ?? '-' }}</td></tr>
        <tr><th>Perihal</th><td>{{ $disposisi->disposisi->surat->perihal ?? '-' }}</td></tr>
        <tr><th>Pengirim</th><td>{{ $disposisi->disposisi->pengirim->name ?? '-' }}</td></tr>
        <tr><th>Prioritas</th><td>{{ $disposisi->disposisi->prioritas ?? '-' }}</td></tr>
        <tr><th>Tanggal Disposisi</th><td>{{ optional($disposisi->disposisi->tanggal_disposisi)->format('d-m-Y') ?? '-' }}</td></tr>
        <tr><th>Catatan</th><td>{!! nl2br(e($disposisi->disposisi->catatan ?? '-')) !!}</td></tr>
        <tr><th>Status</th><td>{{ $disposisi->status }}</td></tr>
    </table>
</body>
</html>
