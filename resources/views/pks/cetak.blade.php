<!DOCTYPE html>
<html>
<head>
    <title>Cetak PKS</title>
    <style>
        body { font-family: Arial; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px;}
        table, th, td { border: 1px solid black; padding: 8px; }
    </style>
</head>
<body>

<h2>PERJANJIAN KERJA SAMA (PKS)</h2>

<p><strong>Nomor:</strong> {{ $pks->nomor }}</p>
<p><strong>Judul:</strong> {{ $pks->judul }}</p>
<p><strong>Tanggal:</strong> {{ $pks->tanggal }}</p>

<hr>

<h4>Client</h4>
<p>{{ $pks->client->nama }}</p>

<hr>

<h4>Detail Item</h4>

<table>
<thead>
<tr>
    <th>Layanan</th>
    <th>Waktu</th>
    <th>Channel</th>
    <th>Periode</th>
    <th>Qty</th>
    <th>Tarif</th>
    <th>Subtotal</th>
</tr>
</thead>
<tbody>
@foreach($pks->items as $item)
<tr>
    <td>{{ $item->katalog->nama_layanan }}</td>
    <td>{{ $item->waktu }}</td>
    <td>{{ $item->channel }}</td>
    <td>
        {{ $item->tanggal_mulai }} s/d {{ $item->tanggal_selesai }}
    </td>
    <td>{{ $item->qty }}</td>
    <td>{{ number_format($item->tarif) }}</td>
    <td>{{ number_format($item->subtotal) }}</td>
</tr>
@endforeach
</tbody>
</table>

<h3>Total: Rp {{ number_format($pks->total) }}</h3>

<br><br>

<p>__________________________</p>
<p>Tanda Tangan</p>

</body>
</html>