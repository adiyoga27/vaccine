@extends('layouts.admin')

@section('content')
<div class="mb-8">
    <h1 class="text-2xl font-bold text-gray-900">Data Peserta</h1>
    <p class="text-gray-500 mt-1">Daftar semua orang tua dan anak yang terdaftar.</p>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Anak</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Orang Tua</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tgl Lahir / Usia</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Alamat/Desa</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Riwayat Vaksin</th>
                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Sertifikat</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @foreach($users as $user)
            @php
                $completedVaccines = $user->patient ? $user->patient->vaccinePatients->where('status', 'selesai')->unique('vaccine_id') : collect([]);
                $isCompleted = ($totalVaccines > 0 && $completedVaccines->count() >= $totalVaccines);
            @endphp
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4">
                    <div class="text-sm font-bold text-gray-900">{{ $user->patient->name ?? '-' }}</div>
                    <div class="text-xs text-gray-500">{{ $user->patient->gender == 'male' ? 'Laki-laki' : 'Perempuan' }}</div>
                </td>
                <td class="px-6 py-4">
                    <div class="text-sm text-gray-900">{{ $user->name }}</div>
                    <div class="text-xs text-gray-500">{{ $user->email }}</div>
                    <div class="text-xs text-gray-500">Ibu: {{ $user->patient->mother_name ?? '-' }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    @if($user->patient)
                    <div class="text-sm text-gray-900">{{ $user->patient->date_birth->format('d M Y') }}</div>
                    <div class="text-xs text-gray-500">{{ $user->patient->date_birth->age }} Tahun</div>
                    @else
                    <span class="text-gray-400">-</span>
                    @endif
                </td>
                <td class="px-6 py-4">
                    <div class="text-sm text-gray-900">{{ $user->patient->address ?? '-' }}</div>
                </td>
                <td class="px-6 py-4">
                    <div class="flex flex-wrap gap-1 mb-2">
                        @forelse($completedVaccines as $vp)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                {{ $vp->vaccine->name }}
                            </span>
                        @empty
                            <span class="text-xs text-gray-400 italic">Belum ada vaksin selesai</span>
                        @endforelse
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                    @if($isCompleted)
                        <a href="{{ route('admin.certificate', $user->patient->id) }}" target="_blank" class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-full shadow-sm text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
                            Unduh Sertifikat
                        </a>
                    @else
                        <span class="text-xs text-gray-400">Belum Lengkap</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="px-6 py-4 border-t border-gray-100">
        {{ $users->links() }}
    </div>
</div>
@endsection
