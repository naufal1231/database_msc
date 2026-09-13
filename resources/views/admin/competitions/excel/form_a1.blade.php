<table>
    <thead>
        <!-- Kop Surat -->
        <tr>
            <th colspan="8" style="font-weight: bold; font-size: 14pt; text-align: center;">MARABUNTA SWIMMING CLUB</th>
        </tr>
        <tr>
            <th colspan="8" style="font-weight: bold; font-size: 11pt; text-align: center;">FORMULIR HASIL PERLOMBAAN - FORM A1 (PERORANGAN GAYA BEBAS/DADA/KUPU/PUNGGUNG)</th>
        </tr>
        <tr>
            <th colspan="8" style="text-align: center; font-size: 10pt;">Event: {{ $competition->name }} | Penyelenggara: {{ $competition->organizer }}</th>
        </tr>
        <tr>
            <th colspan="8" style="text-align: center; font-size: 10pt;">Lokasi: {{ $competition->location }} | Tanggal: {{ $competition->start_date->format('d/m/Y') }}</th>
        </tr>
        <tr>
            <th colspan="8"></th>
        </tr>
        <!-- Header Tabel -->
        <tr>
            <th style="font-weight: bold; border: 1px solid #000000; background-color: #e0f2fe; text-align: center; width: 50px;">NO</th>
            <th style="font-weight: bold; border: 1px solid #000000; background-color: #e0f2fe; width: 250px;">NAMA ATLET</th>
            <th style="font-weight: bold; border: 1px solid #000000; background-color: #e0f2fe; text-align: center; width: 100px;">TGL LAHIR</th>
            <th style="font-weight: bold; border: 1px solid #000000; background-color: #e0f2fe; text-align: center; width: 80px;">KU</th>
            <th style="font-weight: bold; border: 1px solid #000000; background-color: #e0f2fe; text-align: center; width: 80px;">PA/PI</th>
            <th style="font-weight: bold; border: 1px solid #000000; background-color: #e0f2fe; width: 200px;">NOMOR LOMBA</th>
            <th style="font-weight: bold; border: 1px solid #000000; background-color: #e0f2fe; text-align: center; width: 150px;">CATATAN WAKTU</th>
            <th style="font-weight: bold; border: 1px solid #000000; background-color: #e0f2fe; text-align: center; width: 100px;">PERINGKAT</th>
        </tr>
    </thead>
    <tbody>
        @php $no = 1; @endphp
        @forelse($entries as $entry)
            <tr>
                <td style="border: 1px solid #000000; text-align: center;">{{ $no++ }}</td>
                <td style="border: 1px solid #000000;">{{ $entry->athlete->name }}</td>
                <td style="border: 1px solid #000000; text-align: center;">{{ $entry->athlete->birth_date->format('d/m/Y') }}</td>
                <td style="border: 1px solid #000000; text-align: center;">KU {{ $entry->athlete->ku }}</td>
                <td style="border: 1px solid #000000; text-align: center;">{{ $entry->athlete->gender === 'L' ? 'PA' : 'PI' }}</td>
                <td style="border: 1px solid #000000;">Gaya {{ ucfirst($entry->competitionEvent->stroke) }} {{ $entry->competitionEvent->distance }}m</td>
                <td style="border: 1px solid #000000; text-align: center; font-weight: bold;">
                    @if($entry->result)
                        {{ $entry->result->status === 'finish' ? $entry->result->time_result : $entry->result->status }}
                    @else
                        -
                    @endif
                </td>
                <td style="border: 1px solid #000000; text-align: center; font-weight: bold;">
                    {{ ($entry->result && $entry->result->status === 'finish') ? $entry->result->rank ?? '-' : '-' }}
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="8" style="border: 1px solid #000000; text-align: center; font-style: italic;">Tidak ada data hasil perlombaan untuk Form A1.</td>
            </tr>
        @endforelse
    </tbody>
</table>
