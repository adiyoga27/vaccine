<table>
    <thead>
        <tr>
            <th colspan="{{ 2 + ($vaccines->count() * 3) }}"
                style="font-weight: bold; font-size: 14px; text-align: center;">
                LAPORAN CAPAIAN VAKSINASI TAHUN {{ $year }} BULAN
                {{ \Carbon\Carbon::create()->month($month)->translatedFormat('F') }}
            </th>
        </tr>
        <tr>
            <th rowspan="2"
                style="border: 1px solid #000000; font-weight: bold; text-align: center; vertical-align: middle;">No
            </th>
            <th rowspan="2"
                style="border: 1px solid #000000; font-weight: bold; text-align: center; vertical-align: middle; width: 30px;">
                Dusun</th>
            @foreach($vaccines as $vaccine)
                <th colspan="3"
                    style="border: 1px solid #000000; font-weight: bold; text-align: center; background-color: #fca5a5;">
                    {{ $vaccine->name }}
                </th>
            @endforeach
        </tr>
        <tr>
            @foreach($vaccines as $vaccine)
                <th style="border: 1px solid #000000; font-weight: bold; text-align: center;">L</th>
                <th style="border: 1px solid #000000; font-weight: bold; text-align: center;">P</th>
                <th style="border: 1px solid #000000; font-weight: bold; text-align: center;">JML</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach($villages as $index => $village)
            <tr>
                <td style="border: 1px solid #000000; text-align: center;">{{ $index + 1 }}</td>
                <td style="border: 1px solid #000000;">{{ $village->name }}</td>

                @foreach($vaccines as $vaccine)
                    @php
                        $stats = $data[$village->id][$vaccine->id] ?? ['L' => 0, 'P' => 0];
                        $total = $stats['L'] + $stats['P'];
                    @endphp
                    <td style="border: 1px solid #000000; text-align: center;">{{ $stats['L'] ?: '' }}</td>
                    <td style="border: 1px solid #000000; text-align: center;">{{ $stats['P'] ?: '' }}</td>
                    <td style="border: 1px solid #000000; text-align: center; font-weight: bold;">{{ $total ?: '' }}</td>
                @endforeach
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="2" style="border: 1px solid #000000; font-weight: bold; text-align: right;">Total Keseluruhan
            </td>
            @foreach($vaccines as $vaccine)
                @php
                    $sumL = 0;
                    $sumP = 0;
                    foreach ($villages as $v) {
                        $sumL += $data[$v->id][$vaccine->id]['L'] ?? 0;
                        $sumP += $data[$v->id][$vaccine->id]['P'] ?? 0;
                    }
                @endphp
                <td style="border: 1px solid #000000; text-align: center; font-weight: bold;">{{ $sumL }}</td>
                <td style="border: 1px solid #000000; text-align: center; font-weight: bold;">{{ $sumP }}</td>
                <td style="border: 1px solid #000000; text-align: center; font-weight: bold; background-color: #e5e7eb;">
                    {{ $sumL + $sumP }}</td>
            @endforeach
        </tr>
    </tfoot>
</table>