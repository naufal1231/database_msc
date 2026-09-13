<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Rekap Kemajuan Atlet - Marabunta Swimming Club</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333;
            margin: 20px;
            font-size: 11pt;
            line-height: 1.4;
        }
        .header-print {
            text-align: center;
            border-bottom: 3px double #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .club-title {
            font-size: 18pt;
            font-weight: bold;
            margin: 0 0 5px 0;
            text-transform: uppercase;
        }
        .club-subtitle {
            font-size: 10pt;
            margin: 0;
            color: #555;
        }
        .report-meta {
            margin-bottom: 20px;
            font-size: 10pt;
        }
        .report-meta table {
            width: 100%;
        }
        .report-meta td {
            padding: 3px 0;
        }
        .table-data {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .table-data th, .table-data td {
            border: 1px solid #000;
            padding: 8px 10px;
            font-size: 9.5pt;
        }
        .table-data th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: left;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .font-mono {
            font-family: monospace;
            font-weight: bold;
        }
        @media print {
            body {
                margin: 0;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>

    <div class="header-print">
        <h1 class="club-title">Marabunta Swimming Club</h1>
        <p class="club-subtitle">Laporan Resmi Rekap Kemajuan & Catatan Performa Atlet</p>
        <p class="club-subtitle" style="font-size: 9pt;">Sekretariat: Kolam Renang Tirtomoyo, Surakarta | Telp: 081234567890</p>
    </div>

    <div class="report-meta">
        <table>
            <tr>
                <td style="width: 15%; font-weight: bold;">Jenis Laporan:</td>
                <td>Rekapitulasi Kemajuan Atlet Latihan</td>
                <td style="width: 15%; font-weight: bold; text-align: right;">Tanggal Cetak:</td>
                <td style="width: 20%; text-align: right;">{{ date('d/m/Y H:i') }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Filter Kelas:</td>
                <td>{{ $selectedClass ? $selectedClass->name : 'Semua Kelas Latihan' }}</td>
                <td style="font-weight: bold; text-align: right;">KU:</td>
                <td style="text-align: right;">{{ $selectedKu ? 'KU ' . $selectedKu : 'Semua Umur' }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Gender:</td>
                <td>{{ $selectedGender === 'L' ? 'PA (Putera)' : ($selectedGender === 'P' ? 'PI (Puteri)' : 'Semua Gender') }}</td>
                <td></td>
                <td></td>
            </tr>
        </table>
    </div>

    <table class="table-data">
        <thead>
            <tr>
                <th style="width: 5%; text-align: center;">NO</th>
                <th style="width: 30%;">NAMA ATLET</th>
                <th style="width: 10%; text-align: center;">KU</th>
                <th style="width: 10%; text-align: center;">PA/PI</th>
                <th style="width: 15%;">KELAS SEKARANG</th>
                <th style="width: 10%; text-align: center;">LOMBA</th>
                <th style="width: 10%; text-align: center;">BEST BEBAS 50M</th>
                <th style="width: 10%; text-align: center;">BEST DADA 50M</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reportData as $index => $row)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td style="font-weight: bold;">{{ $row['name'] }}</td>
                    <td class="text-center">KU {{ $row['ku'] }}</td>
                    <td class="text-center">{{ $row['gender'] }}</td>
                    <td>{{ $row['class_name'] }}</td>
                    <td class="text-center">{{ $row['races_count'] }} kali</td>
                    <td class="text-center font-mono">{{ $row['pb_free_50'] }}</td>
                    <td class="text-center font-mono">{{ $row['pb_breast_50'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center; padding: 20px; font-style: italic;">
                        Tidak ada data atlet yang tercatat.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 50px; text-align: right; font-size: 10pt;" class="no-print">
        <button onclick="window.print();" style="padding: 8px 15px; font-weight: bold; background-color: #000; color: #fff; border: none; cursor: pointer;">
            🖨️ Mulai Cetak Dokumen
        </button>
    </div>

    <script>
        // Auto print trigger when page loads
        window.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => {
                window.print();
            }, 500);
        });
    </script>
</body>
</html>
