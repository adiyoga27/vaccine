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

    <!-- Analytics Chart -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 mb-8">
        <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
            <h3 class="text-lg font-bold text-gray-900">Statistik Peserta Baru per Dusun</h3>
            
            <div class="flex gap-2">
                <select id="chartMonth" class="border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500">
                    @foreach(range(1, 12) as $m)
                        <option value="{{ $m }}" {{ date('n') == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                        </option>
                    @endforeach
                </select>
                <select id="chartYear" class="border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500">
                    @foreach(range(date('Y'), date('Y') - 4) as $y)
                        <option value="{{ $y }}" {{ date('Y') == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        
        <div class="relative h-80 w-full">
            <canvas id="villageChart"></canvas>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('villageChart').getContext('2d');
            let villageChart;

            function loadChartData() {
                const month = document.getElementById('chartMonth').value;
                const year = document.getElementById('chartYear').value;

                fetch(`{{ route('admin.chart.data') }}?month=${month}&year=${year}`)
                    .then(response => response.json())
                    .then(data => {
                        if (villageChart) {
                            villageChart.destroy();
                        }

                        villageChart = new Chart(ctx, {
                            type: 'bar', // Type of chart
                            data: {
                                labels: data.labels,
                                datasets: [{
                                    label: 'Jumlah Peserta Baru',
                                    data: data.data,
                                    backgroundColor: 'rgba(59, 130, 246, 0.6)', // Blue-500
                                    borderColor: 'rgba(59, 130, 246, 1)',
                                    borderWidth: 1,
                                    borderRadius: 4
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        ticks: {
                                            stepSize: 1
                                        }
                                    }
                                },
                                plugins: {
                                    legend: {
                                        display: false
                                    }
                                }
                            }
                        });
                    })
                    .catch(error => console.error('Error loading chart data:', error));
            }

            // Initial Load
            loadChartData();

            // Event Listeners
            document.getElementById('chartMonth').addEventListener('change', loadChartData);
            document.getElementById('chartYear').addEventListener('change', loadChartData);
        });
    </script>

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