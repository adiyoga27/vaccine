@extends('layouts.admin')

@section('content')
<div class="mb-8">
    <h1 class="text-2xl font-bold text-gray-900">Riwayat Vaksinasi</h1>
    <p class="text-gray-500 mt-1">Daftar semua transaksi dan status vaksinasi peserta.</p>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Pasien</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vaksin / Desa</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Pengajuan</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tgl Disetujui</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @foreach($histories as $history)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4">
                    <div class="text-sm font-bold text-gray-900">{{ $history->patient->name ?? 'Deleted' }}</div>
                </td>
                <td class="px-6 py-4">
                    <div class="text-sm text-gray-900">{{ $history->vaccine->name ?? '-' }}</div>
                    <div class="text-xs text-gray-500">{{ $history->village->name ?? '-' }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ $history->request_date->format('d M Y') }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $history->status == 'selesai' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                        {{ ucfirst($history->status) }}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ $history->vaccinated_at ? $history->vaccinated_at->format('d M Y H:i') : '-' }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="px-6 py-4 border-t border-gray-100">
        {{ $histories->links() }}
    </div>
</div>
@endsection
