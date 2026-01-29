<!DOCTYPE html>
<html>
<head>
    <title>Data Peserta Vaksinasi</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        h2 { text-align: center; margin-bottom: 20px; }
    </style>
</head>
<body>
    <h2>Data Peserta Vaksinasi</h2>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Anak</th>
                <th>Nama Ibu</th>
                <th>Tgl Lahir / Usia</th>
                <th>L/P</th>
                <th>Desa / Alamat</th>
                <th>No. HP</th>
                <th>Status Vaksin</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $index => $user)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $user->patient->name ?? '-' }}</td>
                <td>{{ $user->patient->mother_name ?? '-' }}</td>
                <td>
                    {{ $user->patient->date_birth ? $user->patient->date_birth->format('d/m/Y') : '-' }}<br>
                    <small>{{ $user->patient ? number_format(\Carbon\Carbon::parse($user->patient->date_birth)->floatDiffInMonths(now()), 1) . ' Bln' : '-' }}</small>
                </td>
                <td>{{ $user->patient && $user->patient->gender == 'male' ? 'L' : 'P' }}</td>
                <td>
                    <strong>{{ $user->patient->village->name ?? '-' }}</strong><br>
                    {{ $user->patient->address ?? '-' }}
                </td>
                <td>{{ $user->patient->phone ?? '-' }}</td>
                <td>
                    @if($user->patient)
                        @php 
                            $completed = $user->patient->vaccinePatients->where('status', 'selesai')->count();
                            $total = $totalVaccines; // Passed from controller
                        @endphp
                        {{ $completed }} / {{ $total }} Vaksin
                    @else
                        -
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
