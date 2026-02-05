@extends('layouts.admin')

@section('content')
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Laporan Capaian Vaksinasi</h2>
            <p class="text-gray-500 text-sm">Rekapitulasi jumlah peserta vaksin per dusun dan jenis vaksin.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.reports.immunization.export', request()->all()) }}" target="_blank"
                class="flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition shadow-sm">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                    </path>
                </svg>
                Export Excel
            </a>
        </div>
    </div>

    <!-- Filter -->
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 mb-6">
        <form action="{{ route('admin.reports.immunization') }}" method="GET" class="flex flex-wrap gap-4 items-end">
            <div>
                <label for="month" class="block text-xs font-medium text-gray-700 mb-1">Bulan</label>
                <select name="month" id="month"
                    class="border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 w-32">
                    @for($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}" {{ $month == $i ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($i)->translatedFormat('F') }}
                        </option>
                    @endfor
                </select>
            </div>
            <div>
                <label for="year" class="block text-xs font-medium text-gray-700 mb-1">Tahun</label>
                <select name="year" id="year"
                    class="border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 w-24">
                    @foreach(range(date('Y'), 2020) as $y)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="search" class="block text-xs font-medium text-gray-700 mb-1">Cari Dusun</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Nama Dusun..."
                    class="border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500">
            </div>
            <button type="submit"
                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm font-medium">
                Tampilkan
            </button>
        </form>
    </div>

    <!-- Table Container -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 border-collapse">
                <thead class="bg-gray-50">
                    <!-- Header Row 1: Vaccines -->
                    <tr>
                        <th rowspan="2"
                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border text-center w-12">
                            No</th>
                        <th rowspan="2"
                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border w-48 sticky left-0 bg-gray-50 z-10">
                            Dusun</th>
                        @foreach($vaccines as $vaccine)
                            <th colspan="3"
                                class="px-2 py-2 text-center text-xs font-bold text-gray-700 uppercase tracking-wider border bg-blue-50">
                                {{ $vaccine->name }}
                            </th>
                        @endforeach
                    </tr>
                    <!-- Header Row 2: Gender -->
                    <tr>
                        @foreach($vaccines as $vaccine)
                            <th
                                class="px-2 py-1 text-center text-xs font-medium text-gray-500 uppercase tracking-wider border w-12">
                                L</th>
                            <th
                                class="px-2 py-1 text-center text-xs font-medium text-gray-500 uppercase tracking-wider border w-12">
                                P</th>
                            <th
                                class="px-2 py-1 text-center text-xs font-medium text-gray-500 uppercase tracking-wider border w-12 bg-gray-100">
                                JML</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($villages as $index => $village)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-500 border text-center font-mono">
                                {{ $index + 1 }}</td>
                            <td
                                class="px-4 py-2 whitespace-nowrap text-sm font-medium text-gray-900 border sticky left-0 bg-white group-hover:bg-gray-50 z-10">
                                {{ $village->name }}</td>

                            @foreach($vaccines as $vaccine)
                                            @php
                                                $stats = $data[$village->id][$vaccine->id] ?? ['L' => 0, 'P' => 0];
                                                $total = $stats['L'] + $stats['P'];
                                            @endphp
                                 <td
                                                class="px-2 py-2 whitespace-nowrap text-xs text-gray-600 border text-center {{ $stats['L'] > 0 ? 'bg-blue-50 font-bold text-blue-700' : '' }}">
                                                {{ $stats['L'] ?: '-' }}
                                            </td>
                                            <td
                                                class="px-2 py-2 whitespace-nowrap text-xs text-gray-600 border text-center {{ $stats['P'] > 0 ? 'bg-pink-50 font-bold text-pink-700' : '' }}">
                                                {{ $stats['P'] ?: '-' }}
                                            </td>
                                            <td
                                                class="px-2 py-2 whitespace-nowrap text-xs text-gray-900 border text-center bg-gray-50 font-semibold">
                                                {{ $total ?: '-' }}
                                            </td>
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ 2 + ($vaccines->count() * 3) }}"
                                class="px-6 py-12 text-center text-gray-500 border">
                                Data tidak ditemukan
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <!-- Footer for Totals -->
                <tfoot class="bg-gray-100 font-bold">
                    <tr>
                        <td colspan="2"
                            class="px-4 py-3 text-right text-sm text-gray-700 border sticky left-0 bg-gray-100 z-10">Total
                            Keseluruhan</td>
                        @foreach($vaccines as $vaccine)
                            @php
                                $sumL = 0;
                                $sumP = 0;
                                foreach ($villages as $v) {
                                    $sumL += $data[$v->id][$vaccine->id]['L'] ?? 0;
                                    $sumP += $data[$v->id][$vaccine->id]['P'] ?? 0;
                                }
                            @endphp
                            <td class="px-2 py-2 text-center text-xs border text-blue-800">{{ $sumL }}</td>
                            <td class="px-2 py-2 text-center text-xs border text-pink-800">{{ $sumP }}</td>
                            <td class="px-2 py-2 text-center text-xs border bg-gray-200">{{ $sumL + $sumP }}</td>
                        @endforeach
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
@endsection