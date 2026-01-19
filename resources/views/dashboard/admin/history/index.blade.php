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

<div x-data="approvalData">
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
                        <td class="px-6 py-4 font-medium text-gray-900">
                            {{ $item->patient->name }} 
                            <div class="text-xs text-gray-500 mt-1">
                                Ibu: {{ $item->patient->mother_name }} <span class="mx-1">|</span> Umur: {{ $item->age }}
                            </div>
                        </td>
                        <td class="px-6 py-4">{{ $item->vaccine->name }}</td>
                        <td class="px-6 py-4">
                            <span class="text-green-600 font-bold">{{ $item->start_date->format('d M Y') }}</span> - {{ $item->end_date->format('d M Y') }}
                        </td>
                        <td class="px-6 py-4">{{ $item->patient->village->name ?? '-' }}</td>
                        <td class="px-6 py-4 flex items-center gap-2">
                            <button @click="openApproveModal('{{ $item->patient->id }}', '{{ $item->vaccine->id }}', '{{ $item->patient->name }}', '{{ $item->vaccine->name }}')" class="px-3 py-1 bg-blue-600 text-white rounded text-xs hover:bg-blue-700 transition flex items-center">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                Approve
                            </button>
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
                        <th class="px-6 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($upcoming as $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-medium text-gray-900">
                            {{ $item->patient->name }}
                            <div class="text-xs text-gray-500 mt-1">
                                Ibu: {{ $item->mother_name }} <span class="mx-1">|</span> Umur: {{ $item->age }}
                            </div>
                        </td>
                        <td class="px-6 py-4">{{ $item->vaccine->name }}</td>
                        <td class="px-6 py-4 text-gray-500">{{ $item->start_date->format('d M Y') }}</td>
                        <td class="px-6 py-4">{{ $item->patient->village->name ?? '-' }}</td>
                        <td class="px-6 py-4">
                            <button @click="openApproveModal('{{ $item->patient->id }}', '{{ $item->vaccine->id }}', '{{ $item->patient->name }}', '{{ $item->vaccine->name }}')" class="px-3 py-1 bg-blue-600 text-white rounded text-xs hover:bg-blue-700 transition flex items-center">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                Approve
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">Tidak ada data.</td>
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
                        <th class="px-6 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($done as $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-medium text-gray-900">
                            {{ $item->patient->name }}
                            <div class="text-xs text-gray-500 mt-1">
                                Ibu: {{ $item->mother_name }} <span class="mx-1">|</span> Umur: {{ $item->age }}
                            </div>
                        </td>
                        <td class="px-6 py-4">{{ $item->vaccine->name }}</td>
                        <td class="px-6 py-4 text-emerald-600 font-medium">{{ \Carbon\Carbon::parse($item->date)->format('d M Y H:i') }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 bg-emerald-100 text-emerald-700 rounded-full text-xs font-bold">Selesai</span>
                        </td>
                        <td class="px-6 py-4 flex items-center gap-2">
                            <button @click='openDetailModal(@json($item))' class="px-3 py-1 bg-cyan-600 text-white rounded text-xs hover:bg-cyan-700 transition flex items-center">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                Detail
                            </button>
                            <form action="{{ route('admin.history.rollback', $item->id) }}" method="POST" onsubmit="return confirm('Apakah anda yakin akan mengembalikan data belum di approve?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-3 py-1 bg-red-600 text-white rounded text-xs hover:bg-red-700 transition flex items-center">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path></svg>
                                    Rollback
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">Belum ada data vaksinasi selesai.</td>
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
                        <th class="px-6 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($overdue as $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-medium text-gray-900">
                            {{ $item->patient->name }}
                            <div class="text-xs text-gray-500 mt-1">
                                Ibu: {{ $item->mother_name }} <span class="mx-1">|</span> Umur: {{ $item->age }}
                            </div>
                        </td>
                        <td class="px-6 py-4">{{ $item->vaccine->name }}</td>
                        <td class="px-6 py-4 text-red-600 font-bold">
                            {{ $item->start_date->format('d M Y') }} - {{ $item->end_date->format('d M Y') }}
                        </td>
                        <td class="px-6 py-4">{{ $item->patient->village->name ?? '-' }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 bg-red-100 text-red-700 rounded-full text-xs font-bold">Terlewat</span>
                        </td>
                        <td class="px-6 py-4">
                            <button @click="openApproveModal('{{ $item->patient->id }}', '{{ $item->vaccine->id }}', '{{ $item->patient->name }}', '{{ $item->vaccine->name }}')" class="px-3 py-1 bg-blue-600 text-white rounded text-xs hover:bg-blue-700 transition flex items-center">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                Approve
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">Tidak ada jadwal terlewat.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Approve Modal -->
    <div x-show="approveModalOpen" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form action="{{ route('admin.history.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="patient_id" x-model="selectedPatientId">
                    <input type="hidden" name="vaccine_id" x-model="selectedVaccineId">
                    
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                    Approve Vaksinasi
                                </h3>
                                <div class="mt-2 text-sm text-gray-500 mb-4">
                                    <p>Konfirmasi vaksinasi untuk <span class="font-bold" x-text="selectedPatientName"></span> - <span class="font-bold" x-text="selectedVaccineName"></span></p>
                                </div>
                                
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Tanggal Vaksin</label>
                                        <input type="date" name="vaccinated_at" required value="{{ date('Y-m-d') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Lokasi Desa</label>
                                        <select name="village_id" x-model="selectedVillageId" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                            <option value="">Pilih Desa</option>
                                            @foreach($villages as $v)
                                            <option value="{{ $v->id }}">{{ $v->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Lokasi Posyandu</label>
                                        <select name="posyandu_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                            <option value="">Pilih Posyandu</option>
                                            <!-- Logic to filter posyandu based on village using Alpine or JS -->
                                            @foreach($villages as $v)
                                                @foreach($v->posyandus as $p)
                                                    <option x-show="selectedVillageId == '{{ $v->id }}'" value="{{ $p->id }}">{{ $p->name }}</option>
                                                @endforeach
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Simpan & Approve
                        </button>
                        <button @click="approveModalOpen = false" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Detail Modal -->
    <div x-show="detailModalOpen" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-xl leading-8 font-bold text-gray-900 border-b pb-2 mb-4">
                                Detail Vaksinasi
                            </h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Data Pasien -->
                                <div>
                                    <h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">Data Pasien</h4>
                                    <div class="space-y-3">
                                        <div>
                                            <p class="text-xs text-gray-400">Nama Lengkap</p>
                                            <p class="text-sm font-medium text-gray-900" x-text="detail.patient.name"></p>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-400">Nama Ibu</p>
                                            <p class="text-sm font-medium text-gray-900" x-text="detail.mother_name"></p>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-400">Tanggal Lahir</p>
                                            <p class="text-sm font-medium text-gray-900" x-text="detail.dob"></p>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-400">Usia Saat Ini</p>
                                            <p class="text-sm font-medium text-gray-900" x-text="detail.age"></p>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-400">Jenis Kelamin</p>
                                            <p class="text-sm font-medium text-gray-900" x-text="detail.gender"></p>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-400">Alamat</p>
                                            <p class="text-sm font-medium text-gray-900" x-text="detail.address"></p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Data Vaksinasi -->
                                <div>
                                    <h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">Data Vaksinasi</h4>
                                    <div class="space-y-3">
                                        <div>
                                            <p class="text-xs text-gray-400">Jenis Vaksin</p>
                                            <p class="text-sm font-bold text-blue-600" x-text="detail.vaccine.name"></p>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-400">Tanggal Vaksinasi</p>
                                            <p class="text-sm font-medium text-gray-900" x-text="formatDate(detail.date)"></p>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-400">Desa</p>
                                            <p class="text-sm font-medium text-gray-900" x-text="detail.patient.village ? detail.patient.village.name : '-'"></p>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-400">Posyandu</p>
                                            <p class="text-sm font-medium text-gray-900" x-text="detail.posyandu"></p>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-400">Status</p>
                                            <span class="px-2 py-1 bg-emerald-100 text-emerald-700 rounded-full text-xs font-bold inline-block mt-1">Selesai</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button @click="detailModalOpen = false" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('approvalData', () => ({
            tab: 'jadwal',
            approveModalOpen: false,
            detailModalOpen: false,
            
            selectedPatientId: '',
            selectedVaccineId: '',
            selectedPatientName: '',
            selectedVaccineName: '',
            selectedVillageId: '',

            // Detail Modal Data
            detail: {
                patient: {},
                vaccine: {}
            },

            openApproveModal(patientId, vaccineId, patientName, vaccineName) {
                this.selectedPatientId = patientId;
                this.selectedVaccineId = vaccineId;
                this.selectedPatientName = patientName;
                this.selectedVaccineName = vaccineName;
                this.approveModalOpen = true;
            },

            openDetailModal(item) {
                this.detail = item;
                this.detailModalOpen = true;
            },

            formatDate(dateString) {
                if(!dateString) return '-';
                const date = new Date(dateString);
                return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
            }
        }))
    })
</script>
@endsection
