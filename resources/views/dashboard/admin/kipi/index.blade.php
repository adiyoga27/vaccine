@extends('layouts.admin')

@section('content')
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 flex flex-col md:flex-row justify-between items-center gap-4">
            <h3 class="text-lg font-bold text-gray-900">Riwayat Kejadian Ikutan Pasca Imunisasi (KIPI)</h3>
            
            <a href="{{ route('admin.kipi.export') }}" id="btnExport" class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-green-700 transition flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                   <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path> 
                </svg>
                Export Excel
            </a>
        </div>

        <div class="p-6 bg-gray-50 border-b border-gray-100">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Filter List KIPI -->
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Filter Keluhan (KIPI)</label>
                    <select id="kipiFilter" class="w-full border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Semua Keluhan</option>
                        @foreach($kipiList as $kipi)
                            <option value="{{ $kipi }}">{{ $kipi }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Dusun -->
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Filter Dusun</label>
                    <select id="villageFilter" class="w-full border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Semua Dusun</option>
                        @foreach($villages as $village)
                            <option value="{{ $village->id }}">{{ $village->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Date Range -->
                <div>
                     <label class="block text-xs font-medium text-gray-500 mb-1">Tanggal Mulai</label>
                     <input type="date" id="startDate" class="w-full border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                     <label class="block text-xs font-medium text-gray-500 mb-1">Tanggal Akhir</label>
                     <input type="date" id="endDate" class="w-full border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                <!-- Reset Button -->
                <div class="flex items-end md:col-span-4">
                    <button id="btnFilter" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition w-full md:w-auto ml-auto">
                        Terapkan Filter
                    </button>
                </div>
            </div>
        </div>

        <div class="p-6">
            <table id="kipiTable" class="w-full" style="width:100%">
                <thead>
                    <tr>
                        <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Anak / Ibu</th>
                        <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vaksin</th>
                        <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keluhan (KIPI)</th>
                        <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lokasi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200"></tbody>
            </table>
        </div>
    </div>

    <!-- DataTables & jQuery -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>

    <script>
        $(document).ready(function() {
            var table = $('#kipiTable').DataTable({
                processing: true,
                serverSide: true,
                autoWidth: false,
                scrollX: true,
                lengthMenu: [[10, 20, 50, 100, -1], [10, 20, 50, 100, "Semua"]],
                ajax: {
                    url: "{{ route('admin.kipi') }}",
                    data: function (d) {
                        d.kipi_filter = $('#kipiFilter').val();
                        d.village_id = $('#villageFilter').val();
                        d.start_date = $('#startDate').val();
                        d.end_date = $('#endDate').val();
                    }
                },
                columns: [
                    { data: 'date', name: 'vaccinated_at' },
                    { data: 'patient_name', name: 'patient.name' },
                    { data: 'vaccine', name: 'vaccine.name' },
                    { data: 'kipi_tags', name: 'kipi', orderable: false, searchable: false },
                    { data: 'location', name: 'village.name', orderable: false, searchable: false },
                ],
                order: [[ 0, "desc" ]],
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
                }
            });

            $('#btnFilter').click(function() {
                table.draw();
                updateExportLink();
            });

            function updateExportLink() {
                var kipi = $('#kipiFilter').val();
                var village = $('#villageFilter').val();
                var start = $('#startDate').val();
                var end = $('#endDate').val();
                var url = "{{ route('admin.kipi.export') }}?kipi_filter=" + kipi + "&village_id=" + village + "&start_date=" + start + "&end_date=" + end;
                $('#btnExport').attr('href', url);
            }
        });
    </script>
@endsection
