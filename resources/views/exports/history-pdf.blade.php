<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Riwayat Vaksin - {{ $statusLabel }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
        }
        h1 {
            text-align: center;
            font-size: 16px;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #333;
            padding: 6px 8px;
            text-align: left;
        }
        th {
            background-color: #4a5568;
            color: white;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f7fafc;
        }
        .footer {
            margin-top: 20px;
            text-align: right;
            font-size: 9px;
            color: #666;
        }
    </style>
</head>
<body>
    <h1>Riwayat Vaksinasi - {{ $statusLabel }}</h1>
    
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Nama Anak</th>
                <th>Nama Ibu</th>
                <th>Vaksin</th>
                @if($status === 'schedule')
                    <th>Jadwal Tanggal</th>
                @elseif($status === 'jadwal')
                    <th>Jadwal Mulai</th>
                    <th>Jadwal Selesai</th>
                @elseif($status === 'akan')
                    <th>Rencana Jadwal</th>
                @elseif($status === 'sudah')
                    <th>Tanggal Vaksin</th>
                @elseif($status === 'terlewat')
                    <th>Seharusnya</th>
                @endif
                @if(in_array($status, ['schedule', 'jadwal', 'akan', 'terlewat']))
                    <th>Dusun</th>
                @endif
                <th>Posyandu</th>
                @if(in_array($status, ['sudah', 'terlewat']))
                    <th>Status</th>
                @endif
                @if($status === 'sudah')
                    <th>KIPI</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @php $index = 0; @endphp
            @foreach($data as $row)
                @php $index++; @endphp
                <tr>
                    <td>{{ $index }}</td>
                    <td>{{ $row->patient->name ?? '-' }}</td>
                    <td>{{ $row->mother_name ?? $row->patient->mother_name ?? '-' }}</td>
                    <td>{{ $row->vaccine->name ?? '-' }}</td>
                    @if($status === 'schedule')
                        <td>{{ isset($row->schedule_at) ? \Carbon\Carbon::parse($row->schedule_at)->format('d M Y') : '-' }}</td>
                    @elseif($status === 'jadwal')
                        <td>{{ isset($row->start_date) ? \Carbon\Carbon::parse($row->start_date)->format('d M Y') : '-' }}</td>
                        <td>{{ isset($row->end_date) ? \Carbon\Carbon::parse($row->end_date)->format('d M Y') : '-' }}</td>
                    @elseif($status === 'akan')
                        <td>{{ isset($row->start_date) ? \Carbon\Carbon::parse($row->start_date)->format('d M Y') . ' - ' . \Carbon\Carbon::parse($row->end_date)->format('d M Y') : '-' }}</td>
                    @elseif($status === 'sudah')
                        <td>{{ isset($row->date) ? \Carbon\Carbon::parse($row->date)->format('d M Y') : '-' }}</td>
                    @elseif($status === 'terlewat')
                        <td>{{ isset($row->start_date) ? \Carbon\Carbon::parse($row->start_date)->format('d M Y') . ' - ' . \Carbon\Carbon::parse($row->end_date)->format('d M Y') : '-' }}</td>
                    @endif
                    @if(in_array($status, ['schedule', 'jadwal', 'akan', 'terlewat']))
                        <td>{{ $row->patient->village->name ?? '-' }}</td>
                    @endif
                    <td>{{ $row->posyandu ?? $row->patient->posyandu->name ?? '-' }}</td>
                    @if($status === 'sudah')
                        <td>Selesai</td>
                    @elseif($status === 'terlewat')
                        <td>Terlewat</td>
                    @endif
                    @if($status === 'sudah')
                        <td>
                            @php
                                $kipi = $row->kipi ?? null;
                                if ($kipi) {
                                    $kipiData = json_decode($kipi, true);
                                    echo is_array($kipiData) ? implode(', ', $kipiData) : '-';
                                } else {
                                    echo '-';
                                }
                            @endphp
                        </td>
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>
    
    <div class="footer">
        Dicetak pada: {{ now()->format('d M Y H:i:s') }}
    </div>
</body>
</html>
