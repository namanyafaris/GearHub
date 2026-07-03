<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Log Rekomendasi - User {{ $user->id }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; }
        h1 { font-size: 18px; margin-bottom: 5px; }
        h2 { font-size: 14px; margin-top: 20px; border-bottom: 1px solid #ccc; padding-bottom: 5px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ccc; padding: 6px; text-align: left; }
        th { background-color: #f5f5f5; }
        .text-center { text-align: center; }
        .footer { margin-top: 30px; font-size: 10px; color: #777; text-align: center; border-top: 1px solid #eee; padding-top: 10px; }
    </style>
</head>
<body>

    <h1>Log Pengujian White-Box: Collaborative Filtering</h1>
    <p><strong>Nama User:</strong> {{ $user->name }} (ID: {{ $user->id }})<br>
       <strong>Tanggal Pengujian:</strong> {{ now()->format('d M Y H:i:s') }}</p>

    <h2>1. Vektor Preferensi User (Target)</h2>
    @if(empty($targetVector))
        <p><em>Tidak ada interaksi (Cold Start Problem).</em></p>
    @else
        <table>
            <thead>
                <tr>
                    <th>ID Produk</th>
                    <th>Akumulasi Bobot</th>
                </tr>
            </thead>
            <tbody>
                @foreach($targetVector as $pId => $weight)
                <tr>
                    <td>{{ $pId }}</td>
                    <td>{{ $weight }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <h2>2. Top Similar Users (Cosine Similarity)</h2>
    @if(empty($similarUsersDetails))
        <p><em>Tidak ada similar user.</em></p>
    @else
        <table>
            <thead>
                <tr>
                    <th>Rank</th>
                    <th>ID User</th>
                    <th>Nama User</th>
                    <th>Cosine Similarity Score</th>
                </tr>
            </thead>
            <tbody>
                @foreach($similarUsersDetails as $idx => $sim)
                <tr>
                    <td>{{ $idx + 1 }}</td>
                    <td>{{ $sim['id'] }}</td>
                    <td>{{ $sim['name'] }}</td>
                    <td>{{ number_format($sim['score'], 4) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <h2>3. Kalkulasi Bobot Produk (Weighted Score)</h2>
    @if(empty($productScoresDetails))
        <p><em>Tidak ada skor produk yang dikalkulasi.</em></p>
    @else
        <table>
            <thead>
                <tr>
                    <th>Rank</th>
                    <th>ID Produk</th>
                    <th>Nama Produk</th>
                    <th>Weighted Score</th>
                </tr>
            </thead>
            <tbody>
                @foreach($productScoresDetails as $idx => $prod)
                <tr>
                    <td>{{ $idx + 1 }}</td>
                    <td>{{ $prod['id'] }}</td>
                    <td>{{ $prod['name'] }}</td>
                    <td>{{ number_format($prod['score'], 4) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <h2>4. Final Top Rekomendasi</h2>
    <table>
        <thead>
            <tr>
                <th>Rank</th>
                <th>ID Produk</th>
                <th>Nama Produk</th>
            </tr>
        </thead>
        <tbody>
            @forelse($recommendations as $idx => $rec)
            <tr>
                <td>{{ $idx + 1 }}</td>
                <td>{{ $rec->id }}</td>
                <td>{{ $rec->name }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="3" class="text-center">Tidak ada rekomendasi.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dicetak otomatis oleh Sistem Rekomendasi GearHub untuk keperluan Lampiran Skripsi.
    </div>

</body>
</html>
