@extends('layouts.admin')

@section('content')
<div class="mb-8">
    <h1 class="text-2xl font-bold text-gray-900">Riwayat & Status Vaksinasi</h1>
    <p class="text-gray-500 mt-1">Monitoring status vaksinasi seluruh peserta.</p>

    <!-- Search Box -->
    <div class="mt-6">
        <form action="{{ route('admin.history') }}" method="GET" class="flex gap-2 max-w-lg">
            <div class="relative flex-1">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" 
                    class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:border-blue-300 focus:ring focus:ring-blue-200 sm:text-sm transition duration-150 ease-in-out" 
                    placeholder="Cari Nama Anak atau Nama Ibu...">
            </div>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:bg-blue-700 transition duration-150 ease-in-out">
                Cari
            </button>
            @if(request('search'))
                <a href="{{ route('admin.history') }}" class="px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 focus:outline-none focus:bg-gray-200 transition duration-150 ease-in-out flex items-center">
                    Reset
                </a>
            @endif
        </form>
    </div>
</div>

<div x-data="{ tab: 'jadwal' }">
    <!-- Tabs Header -->
    <div class="flex space-x-1 bg-gray-100 p-1 rounded-xl mb-6 overflow-x-auto">
        <button @click="tab = 'jadwal'" 
                :class="tab === 'jadwal' ? 'bg-white text-green-700 shadow-sm' : 'text-gray-500 hover:text-gray-700'"
                class="flex-1 py-2.5 px-4 rounded-lg text-sm font-medium transition whitespace-nowrap">
            Jadwal Vaksin (Active) 
            <span class="ml-2 bg-green-100 text-green-700 py-0.5 px-2 rounded-full text-xs">{{ $active->count() }}</span>
        </button>
        <button @click="tab = 'akan'" 
                :class="tab === 'akan' ? 'bg-white text-blue-700 shadow-sm' : 'text-gray-500 hover:text-gray-700'"
                class="flex-1 py-2.5 px-4 rounded-lg text-sm font-medium transition whitespace-nowrap">
            Akan Vaksin (Upcoming)
            <span class="ml-2 bg-blue-100 text-blue-700 py-0.5 px-2 rounded-full text-xs">{{ $upcoming->count() }}</span>
        </button>
        <button @click="tab = 'sudah'" 
                :class="tab === 'sudah' ? 'bg-white text-emerald-700 shadow-sm' : 'text-gray-500 hover:text-gray-700'"
                class="flex-1 py-2.5 px-4 rounded-lg text-sm font-medium transition whitespace-nowrap">
            Sudah Vaksin (Done)
            <span class="ml-2 bg-emerald-100 text-emerald-700 py-0.5 px-2 rounded-full text-xs">{{ $done->count() }}</span>
        </button>
        <button @click="tab = 'terlewat'" 
                :class="tab === 'terlewat' ? 'bg-white text-red-700 shadow-sm' : 'text-gray-500 hover:text-gray-700'"
                class="flex-1 py-2.5 px-4 rounded-lg text-sm font-medium transition whitespace-nowrap">
            Terlewat (Overdue)
            <span class="ml-2 bg-red-100 text-red-700 py-0.5 px-2 rounded-full text-xs">{{ $overdue->count() }}</span>
        </button>
    </div>

    <!-- Tab Contents -->
    
    <!-- 1. Jadwal (Active) -->
    <div x-show="tab === 'jadwal'" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-green-50">
            <h3 class="font-bold text-green-800">Sedang Berlangsung (Wajib Vaksin Sekarang)</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-gray-500 font-medium">
                    <tr>
                        <th class="px-6 py-3">Peserta</th>
                        <th class="px-6 py-3">Vaksin</th>
                        <th class="px-6 py-3">Jadwal (Mulai - Selesai)</th>
                        <th class="px-6 py-3">Desa</th>
                        <th class="px-6 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($active as $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-medium text-gray-900">{{ $item->patient->name }} <br><span class="text-xs text-gray-500">Ibu: {{ $item->patient->mother_name }}</span></td>
                        <td class="px-6 py-4">{{ $item->vaccine->name }}</td>
                        <td class="px-6 py-4">
                            <span class="text-green-600 font-bold">{{ $item->start_date->format('d M Y') }}</span> - {{ $item->end_date->format('d M Y') }}
                        </td>
                        <td class="px-6 py-4">{{ $item->patient->village->name ?? '-' }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs font-bold animate-pulse">Segera</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">Tidak ada jadwal aktif saat ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- 2. Akan (Upcoming) -->
    <div x-show="tab === 'akan'" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden" style="display: none;">
        <div class="px-6 py-4 border-b border-gray-100 bg-blue-50">
            <h3 class="font-bold text-blue-800">Akan Datang</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-gray-500 font-medium">
                    <tr>
                        <th class="px-6 py-3">Peserta</th>
                        <th class="px-6 py-3">Vaksin</th>
                        <th class="px-6 py-3">Rencana Jadwal</th>
                        <th class="px-6 py-3">Desa</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($upcoming as $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-medium text-gray-900">{{ $item->patient->name }}</td>
                        <td class="px-6 py-4">{{ $item->vaccine->name }}</td>
                        <td class="px-6 py-4 text-gray-500">{{ $item->start_date->format('d M Y') }}</td>
                        <td class="px-6 py-4">{{ $item->patient->village->name ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-gray-500">Tidak ada data.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- 3. Sudah (Done) -->
    <div x-show="tab === 'sudah'" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden" style="display: none;">
        <div class="px-6 py-4 border-b border-gray-100 bg-emerald-50">
            <h3 class="font-bold text-emerald-800">Selesai Vaksinasi</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-gray-500 font-medium">
                    <tr>
                        <th class="px-6 py-3">Peserta</th>
                        <th class="px-6 py-3">Vaksin</th>
                        <th class="px-6 py-3">Tanggal Vaksin</th>
                        <th class="px-6 py-3">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($done as $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-medium text-gray-900">{{ $item->patient->name }}</td>
                        <td class="px-6 py-4">{{ $item->vaccine->name }}</td>
                        <td class="px-6 py-4 text-emerald-600 font-medium">{{ \Carbon\Carbon::parse($item->date)->format('d M Y H:i') }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 bg-emerald-100 text-emerald-700 rounded-full text-xs font-bold">Selesai</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-gray-500">Belum ada data vaksinasi selesai.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- 4. Terlewat (Overdue) -->
    <div x-show="tab === 'terlewat'" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden" style="display: none;">
        <div class="px-6 py-4 border-b border-gray-100 bg-red-50">
            <h3 class="font-bold text-red-800">Terlewat (Overdue)</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-gray-500 font-medium">
                    <tr>
                        <th class="px-6 py-3">Peserta</th>
                        <th class="px-6 py-3">Vaksin</th>
                        <th class="px-6 py-3">Seharusnya</th>
                        <th class="px-6 py-3">Desa</th>
                        <th class="px-6 py-3">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($overdue as $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-medium text-gray-900">{{ $item->patient->name }}</td>
                        <td class="px-6 py-4">{{ $item->vaccine->name }}</td>
                        <td class="px-6 py-4 text-red-600 font-bold">
                            {{ $item->start_date->format('d M Y') }} - {{ $item->end_date->format('d M Y') }}
                        </td>
                        <td class="px-6 py-4">{{ $item->patient->village->name ?? '-' }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 bg-red-100 text-red-700 rounded-full text-xs font-bold">Terlewat</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">Tidak ada jadwal terlewat.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
