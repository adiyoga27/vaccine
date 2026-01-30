@extends('layouts.admin')

@section('content')
    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <div class="text-sm font-medium text-gray-500 mb-1">Total Peserta</div>
            <div class="text-3xl font-bold text-gray-900">{{ $stats['users'] }}</div>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <div class="text-sm font-medium text-gray-500 mb-1">Total Dusun</div>
            <div class="text-3xl font-bold text-gray-900">{{ $stats['villages'] }}</div>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <div class="text-sm font-medium text-gray-500 mb-1">Jenis Vaksin</div>
            <div class="text-3xl font-bold text-gray-900">{{ $stats['vaccines'] }}</div>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 bg-yellow-50 border-yellow-100">
            <div class="text-sm font-medium text-yellow-800 mb-1">Menunggu Konfirmasi</div>
            <div class="text-3xl font-bold text-yellow-900">{{ $stats['pending'] }}</div>
        </div>
    </div>

    <!-- Main Section: Approval Queue -->
    <!-- Main Section: Approval Queue (HIDDEN)
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center">
            <h3 class="text-lg font-bold text-gray-900">Permintaan Vaksinasi Masuk</h3>
            <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded">{{ $requests->count() }}
                Baru</span>
        </div>

        @if($requests->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Anak
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vaksin
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dusun
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tgl
                                Pengajuan</th>
                            <th scope="col"
                                class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($requests as $req)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $req->patient->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $req->patient->gender }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                        {{ $req->vaccine->name }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $req->village->name ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $req->request_date->format('d M Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <form action="{{ route('admin.approve', $req->id) }}" method="POST" class="inline-block">
                                        @csrf
                                        <button type="submit"
                                            class="text-green-600 hover:text-green-900 font-bold bg-green-50 px-3 py-1 rounded-lg hover:bg-green-100 transition text-xs">Setujui</button>
                                    </form>
                                    <form action="{{ route('admin.reject', $req->id) }}" method="POST" class="inline-block ml-1"
                                        onsubmit="return confirm('Apakah Anda yakin ingin menolak permintaan ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="text-red-600 hover:text-red-900 font-bold bg-red-50 px-3 py-1 rounded-lg hover:bg-red-100 transition text-xs">Tolak</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-12">
                <p class="text-gray-500">Tidak ada permintaan vaksinasi baru.</p>
            </div>
        @endif
    </div>
    -->
@endsection