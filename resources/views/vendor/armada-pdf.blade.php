<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #222;
            line-height: 1.5;
        }

        h1 {
            text-align: center;
            margin-bottom: 5px;
        }

        .subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 20px;
        }

        .summary {
            margin-bottom: 20px;
            padding: 10px;
            background: #f5f5f5;
            border: 1px solid #ddd;
        }

        .section-title {
            background: #0B3C9B;
            color: white;
            padding: 8px 10px;
            font-size: 13px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .car {
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 25px;
            page-break-inside: avoid;
        }

        .image-wrapper {
            text-align: center;
            margin-bottom: 15px;
        }

        .main-image {
            max-width: 100%;
            max-height: 350px;
            width: auto;
            height: auto;
        }

        .gallery-image {
            max-width: 100%;
            max-height: 250px;
            width: auto;
            height: auto;
            margin-bottom: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td,
        th {
            border: 1px solid #ddd;
            padding: 6px;
        }

        th {
            background: #f0f0f0;
            font-weight: bold;
        }

        .label {
            width: 30%;
            background: #f7f7f7;
            font-weight: bold;
        }

        .page-break {
            page-break-after: always;
        }

        .car-title {
            font-size: 16px;
            font-weight: bold;
            color: #0B3C9B;
            margin-bottom: 10px;
        }

        .gallery-container {
            margin-top: 15px;
        }

        .gallery-item {
            margin-bottom: 15px;
            text-align: center;
        }
    </style>
</head>

<body>

    <h1>Daftar Armada MD Car Rental</h1>

    <div class="subtitle">
        Katalog Seluruh Armada Rental
    </div>

    <div class="summary">
        <strong>Total Armada:</strong> {{ count($cars) }}
    </div>

    {{-- DAFTAR ARMADA --}}
    <div class="section-title">
        Daftar Armada
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th>Nama Mobil</th>
                <th>Brand</th>
                <th width="10%">Tahun</th>
                <th width="15%">Kapasitas</th>
                <th width="20%">Harga / Hari</th>
            </tr>
        </thead>

        <tbody>

            @foreach($cars as $index => $car)

            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $car['name'] }}</td>
                <td>{{ $car['brand'] }}</td>
                <td>{{ $car['year'] }}</td>
                <td>{{ $car['seat_count'] }} Orang</td>
                <td>
                    Rp {{ number_format($car['daily_rate'], 0, ',', '.') }}
                </td>
            </tr>

            @endforeach

        </tbody>
    </table>

    <div class="page-break"></div>

    {{-- DETAIL ARMADA --}}
    @foreach($cars as $index => $car)

    <div class="car">

        <div class="car-title">
            Armada {{ $index + 1 }} - {{ $car['name'] }}
        </div>

        {{-- FOTO UTAMA --}}
        @if($car['main_image'])

        <div class="section-title">
            Foto Utama
        </div>

        <div class="image-wrapper">
            <img
                src="{{ $car['main_image'] }}"
                class="main-image"
                alt="Main Image">
        </div>

        @endif

        {{-- INFORMASI --}}
        <div class="section-title">
            Informasi Armada
        </div>

        <table>

            <tr>
                <td class="label">Nama</td>
                <td>{{ $car['name'] }}</td>
            </tr>

            <tr>
                <td class="label">Brand</td>
                <td>{{ $car['brand'] }}</td>
            </tr>

            <tr>
                <td class="label">Plat Nomor</td>
                <td>{{ $car['license_plate'] }}</td>
            </tr>

            <tr>
                <td class="label">Tahun</td>
                <td>{{ $car['year'] }}</td>
            </tr>

            <tr>
                <td class="label">CC</td>
                <td>{{ number_format($car['cc']) }} cc</td>
            </tr>

            <tr>
                <td class="label">Transmisi</td>
                <td>{{ $car['transmission'] }}</td>
            </tr>

            <tr>
                <td class="label">Kapasitas</td>
                <td>{{ $car['seat_count'] }} Penumpang</td>
            </tr>

            <tr>
                <td class="label">Harga / Hari</td>
                <td>
                    Rp {{ number_format($car['daily_rate'], 0, ',', '.') }}
                </td>
            </tr>

            <tr>
                <td class="label">Deskripsi</td>
                <td>{{ $car['description'] }}</td>
            </tr>

        </table>

        {{-- GALERI --}}
        @if(count($car['gallery_urls']) > 0)

        <div class="gallery-container">

            <div class="section-title">
                Galeri Armada
            </div>

            @foreach($car['gallery_urls'] as $gallery)

            <div class="gallery-item">

                <img
                    src="{{ $gallery }}"
                    class="gallery-image"
                    alt="Gallery Image">

            </div>

            @endforeach

        </div>

        @endif

    </div>

    @endforeach

</body>

</html>