@extends('layouts.admin')

@section('content')
    <div class="mb-8" x-data="{
            filterVillage: '{{ request('village_id') }}',
            filterPosyandu: '{{ request('posyandu_id') }}'
        }">
        <h1 class="text-2xl font-bold text-gray-900">Riwayat & Status Vaksinasi</h1>
        <p class="text-gray-500 mt-1">Monitoring status vaksinasi seluruh peserta.</p>

        <!-- Search & Filter Box -->
        <div class="mt-6 bg-white p-4 rounded-xl shadow-sm border border-gray-100">
            <form action="{{ route('admin.history') }}" method="GET">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Search -->
                    <div class="col-span-1 md:col-span-2 lg:col-span-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Pencarian</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <input type="text" name="search" value="{{ request('search') }}"
                                class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:border-blue-300 focus:ring focus:ring-blue-200 sm:text-sm transition duration-150 ease-in-out"
                                placeholder="Cari Nama Anak atau Nama Ibu...">
                        </div>
                    </div>

                    <!-- Vaccine Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Vaksin</label>
                        <select name="vaccine_id"
                            class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                            <option value="">Semua Vaksin</option>
                            @foreach($vaccines as $vac)
                                <option value="{{ $vac->id }}" {{ request('vaccine_id') == $vac->id ? 'selected' : '' }}>{{ $vac->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Village Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Dusun</label>
                        <select name="village_id" x-model="filterVillage" @change="filterPosyandu = ''"
                            class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                            <option value="">Semua Dusun</option>
                            @foreach($villages as $village)
                                <option value="{{ $village->id }}">{{ $village->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Posyandu Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Posyandu</label>
                        <select name="posyandu_id" x-model="filterPosyandu"
                            class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                            <option value="">Semua Posyandu</option>
                            @foreach($villages as $village)
                                @foreach($village->posyandus as $posyandu)
                                    <option value="{{ $posyandu->id }}" x-show="filterVillage == '{{ $village->id }}'" style="display: none;">{{ $posyandu->name }}</option>
                                @endforeach
                            @endforeach
                        </select>
                    </div>

                    <!-- Date Range Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Rentang Jadwal</label>
                        <div class="flex gap-2 items-center">
                            <input type="date" name="start_date" value="{{ request('start_date') }}" class="block w-full px-2 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md" placeholder="Awal">
                            <span class="text-gray-500 font-bold">-</span>
                            <input type="date" name="end_date" value="{{ request('end_date') }}" class="block w-full px-2 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md" placeholder="Akhir">
                        </div>
                    </div>
                </div>

                <div class="mt-4 flex justify-end gap-2">
                     @if(request('search') || request('vaccine_id') || request('village_id') || request('posyandu_id') || request('start_date') || request('end_date'))
                        <a href="{{ route('admin.history') }}"
                            class="px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 focus:outline-none focus:bg-gray-200 transition duration-150 ease-in-out flex items-center">
                            Reset Filter
                        </a>
                    @endif
                    <button type="submit"
                        class="px-6 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:bg-blue-700 transition duration-150 ease-in-out shadow-sm">
                        Terapkan Filter
                    </button>
                </div>
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
                <span class="ml-2 bg-green-100 text-green-700 py-0.5 px-2 rounded-full text-xs">{{ $active_count }}</span>
            </button>
            <button @click="tab = 'akan'"
                :class="tab === 'akan' ? 'bg-white text-blue-700 shadow-sm' : 'text-gray-500 hover:text-gray-700'"
                class="flex-1 py-2.5 px-4 rounded-lg text-sm font-medium transition whitespace-nowrap">
                Akan Vaksin (Upcoming)
                <span class="ml-2 bg-blue-100 text-blue-700 py-0.5 px-2 rounded-full text-xs">{{ $upcoming_count }}</span>
            </button>
            <button @click="tab = 'sudah'"
                :class="tab === 'sudah' ? 'bg-white text-emerald-700 shadow-sm' : 'text-gray-500 hover:text-gray-700'"
                class="flex-1 py-2.5 px-4 rounded-lg text-sm font-medium transition whitespace-nowrap">
                Sudah Vaksin (Done)
                <span class="ml-2 bg-emerald-100 text-emerald-700 py-0.5 px-2 rounded-full text-xs">{{ $done_count }}</span>
            </button>
            <button @click="tab = 'terlewat'"
                :class="tab === 'terlewat' ? 'bg-white text-red-700 shadow-sm' : 'text-gray-500 hover:text-gray-700'"
                class="flex-1 py-2.5 px-4 rounded-lg text-sm font-medium transition whitespace-nowrap">
                Terlewat (Overdue)
                <span class="ml-2 bg-red-100 text-red-700 py-0.5 px-2 rounded-full text-xs">{{ $overdue_count }}</span>
            </button>
            <button @click="tab = 'schedule'"
                :class="tab === 'schedule' ? 'bg-white text-indigo-700 shadow-sm' : 'text-gray-500 hover:text-gray-700'"
                class="flex-1 py-2.5 px-4 rounded-lg text-sm font-medium transition whitespace-nowrap">
                Jadwal
                <span class="ml-2 bg-indigo-100 text-indigo-700 py-0.5 px-2 rounded-full text-xs">{{ $schedule_count ?? 0 }}</span>
            </button>
        </div>

        <!-- Tab Contents -->

        <!-- 1. Jadwal (Active) -->
        <div x-show="tab === 'jadwal'" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-green-50">
                <h3 class="font-bold text-green-800">Sedang Berlangsung (Wajib Vaksin Sekarang)</h3>
            </div>
            <div class="p-4">
                <table id="table-jadwal" class="w-full text-sm text-left" style="width: 100%">
                    <thead class="bg-gray-50 text-gray-500 font-medium">
                        <tr>
                            <th class="px-6 py-3">#</th>
                            <th class="px-6 py-3">Peserta</th>
                            <th class="px-6 py-3">Vaksin</th>
                            <th class="px-6 py-3">Jadwal (Mulai - Selesai)</th>
                            <th class="px-6 py-3">Dusun</th>
                            <th class="px-6 py-3">Posyandu</th>
                            <th class="px-6 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100"></tbody>
                </table>
            </div>
        </div>

        <!-- 2. Akan (Upcoming) -->
        <div x-show="tab === 'akan'" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden"
            style="display: none;">
            <div class="px-6 py-4 border-b border-gray-100 bg-blue-50">
                <h3 class="font-bold text-blue-800">Akan Datang</h3>
            </div>
            <div class="p-4">
                <table id="table-akan" class="w-full text-sm text-left" style="width: 100%">
                    <thead class="bg-gray-50 text-gray-500 font-medium">
                        <tr>
                            <th class="px-6 py-3">#</th>
                            <th class="px-6 py-3">Peserta</th>
                            <th class="px-6 py-3">Vaksin</th>
                            <th class="px-6 py-3">Rencana Jadwal</th>
                            <th class="px-6 py-3">Dusun</th>
                            <th class="px-6 py-3">Posyandu</th>
                            <th class="px-6 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100"></tbody>
                </table>
            </div>
        </div>

        <!-- 3. Sudah (Done) -->
        <div x-show="tab === 'sudah'" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden"
            style="display: none;">
            <div class="px-6 py-4 border-b border-gray-100 bg-emerald-50">
                <h3 class="font-bold text-emerald-800">Selesai Vaksinasi</h3>
            </div>
            <div class="p-4">
                <table id="table-sudah" class="w-full text-sm text-left" style="width: 100%">
                    <thead class="bg-gray-50 text-gray-500 font-medium">
                        <tr>
                            <th class="px-6 py-3">#</th>
                            <th class="px-6 py-3">Peserta</th>
                            <th class="px-6 py-3">Vaksin</th>
                            <th class="px-6 py-3">Tanggal Vaksin</th>
                            <th class="px-6 py-3">Posyandu</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3">KIPI</th>
                            <th class="px-6 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100"></tbody>
                </table>
            </div>
        </div>

        <!-- 4. Terlewat (Overdue) -->
        <div x-show="tab === 'terlewat'" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden"
            style="display: none;">
            <div class="px-6 py-4 border-b border-gray-100 bg-red-50">
                <h3 class="font-bold text-red-800">Terlewat (Overdue)</h3>
            </div>
            <div class="p-4">
                <table id="table-terlewat" class="w-full text-sm text-left" style="width: 100%">
                    <thead class="bg-gray-50 text-gray-500 font-medium">
                        <tr>
                            <th class="px-6 py-3">#</th>
                            <th class="px-6 py-3">Peserta</th>
                            <th class="px-6 py-3">Vaksin</th>
                            <th class="px-6 py-3">Seharusnya</th>
                            <th class="px-6 py-3">Dusun</th>
                            <th class="px-6 py-3">Posyandu</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100"></tbody>
                </table>
            </div>
        </div>

        <!-- 5. Schedule (Jadwal) -->
        <div x-show="tab === 'schedule'" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden"
            style="display: none;">
            <div class="px-6 py-4 border-b border-gray-100 bg-indigo-50">
                <h3 class="font-bold text-indigo-800">Jadwal Vaksinasi (Scheduled)</h3>
            </div>
            <div class="p-4">
                <table id="table-schedule" class="w-full text-sm text-left" style="width: 100%">
                    <thead class="bg-gray-50 text-gray-500 font-medium">
                        <tr>
                            <th class="px-6 py-3">#</th>
                            <th class="px-6 py-3">Peserta</th>
                            <th class="px-6 py-3">Vaksin</th>
                            <th class="px-6 py-3">Jadwal Tanggal</th>
                            <th class="px-6 py-3">Dusun</th>
                            <th class="px-6 py-3">Posyandu</th>
                            <th class="px-6 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100"></tbody>
                </table>
            </div>
        </div>

        <!-- Approve Modal -->
        <div x-show="approveModalOpen" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity" aria-hidden="true" @click="approveModalOpen = false">
                    <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                </div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div
                    class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
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
                                        <p>Konfirmasi vaksinasi untuk <span class="font-bold"
                                                x-text="selectedPatientName"></span> - <span class="font-bold"
                                                x-text="selectedVaccineName"></span></p>
                                    </div>

                                    <div class="space-y-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Tanggal Vaksin</label>
                                            <input type="date" name="vaccinated_at" required value="{{ date('Y-m-d') }}"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Lokasi Dusun</label>
                                            <select name="village_id" x-model="selectedVillageId" required readonly
                                                tabindex="-1"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 bg-gray-100 pointer-events-none">
                                                <option value="">Pilih Dusun</option>
                                                @foreach($villages as $v)
                                                    <option value="{{ $v->id }}">{{ $v->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Lokasi Posyandu</label>
                                            <select name="posyandu_id" x-model="selectedPosyanduId" required readonly
                                                tabindex="-1"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 bg-gray-100 pointer-events-none">
                                                <option value="">Pilih Posyandu</option>
                                                <!-- Logic to filter posyandu based on village using Alpine or JS -->
                                                @foreach($villages as $v)
                                                    @foreach($v->posyandus as $p)
                                                        <option x-show="selectedVillageId == '{{ $v->id }}'" value="{{ $p->id }}"
                                                            data-village="{{ $v->id }}">
                                                            {{ $p->name }}
                                                        </option>
                                                    @endforeach
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="submit"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                                Simpan & Approve
                            </button>
                            <button @click="approveModalOpen = false" type="button"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
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
                <div class="fixed inset-0 transition-opacity" aria-hidden="true" @click="detailModalOpen = false">
                    <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                </div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div
                    class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-xl leading-8 font-bold text-gray-900 border-b pb-2 mb-4">
                                    Detail Vaksinasi
                                </h3>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Data Pasien -->
                                    <div>
                                        <h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">Data
                                            Pasien</h4>
                                        <div class="space-y-3">
                                            <div>
                                                <p class="text-xs text-gray-400">Nama Lengkap</p>
                                                <p class="text-sm font-medium text-gray-900" x-text="detail.patient.name">
                                                </p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-gray-400">Nama Ibu</p>
                                                <p class="text-sm font-medium text-gray-900" x-text="detail.mother_name">
                                                </p>
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
                                        <h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">Data
                                            Vaksinasi</h4>
                                        <div class="space-y-3">
                                            <div>
                                                <p class="text-xs text-gray-400">Jenis Vaksin</p>
                                                <p class="text-sm font-bold text-blue-600" x-text="detail.vaccine.name"></p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-gray-400">Tanggal Vaksinasi</p>
                                                <p class="text-sm font-medium text-gray-900"
                                                    x-text="formatDate(detail.date)"></p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-gray-400">Dusun</p>
                                                <p class="text-sm font-medium text-gray-900"
                                                    x-text="detail.patient.village ? detail.patient.village.name : '-'"></p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-gray-400">Posyandu</p>
                                                <p class="text-sm font-medium text-gray-900" x-text="detail.posyandu"></p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-gray-400">Status</p>
                                                <span
                                                    class="px-2 py-1 bg-emerald-100 text-emerald-700 rounded-full text-xs font-bold inline-block mt-1">Selesai</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button @click="detailModalOpen = false" type="button"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- KIPI Modal -->
        <div x-show="kipiModalOpen" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity" aria-hidden="true" @click="kipiModalOpen = false">
                    <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                </div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form action="{{ route('admin.history.kipi') }}" method="POST">
                        @csrf
                        <input type="hidden" name="id" x-model="kipiId">
                        
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                                Laporan KIPI (Kejadiaan Ikutan Pasca Imunisasi)
                            </h3>
                            
                            <div class="space-y-2">
                                <template x-for="option in kipiOptions" :key="option">
                                    <label class="flex items-center space-x-3">
                                        <input type="checkbox" name="kipi[]" :value="option" x-model="selectedKipi" class="form-checkbox h-5 w-5 text-blue-600">
                                        <span class="text-gray-700 font-medium" x-text="option"></span>
                                    </label>
                                </template>
                            </div>

                            <!-- Other Input -->
                            <div class="mt-4" x-show="selectedKipi.includes('Lainnya')">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Keluhan Lainnya</label>
                                <input type="text" name="kipi_other" x-model="kipiOther" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md" placeholder="Tuliskan keluhan...">
                            </div>
                        </div>

                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                                Simpan Laporan
                            </button>
                            <button type="button" @click="kipiModalOpen = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Schedule Modal -->
        <div x-show="scheduleModalOpen" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
             <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                 <div class="fixed inset-0 transition-opacity" aria-hidden="true" @click="scheduleModalOpen = false">
                     <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                 </div>
                 <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                 <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                     <form action="{{ route('admin.history.schedule') }}" method="POST">
                         @csrf
                         <input type="hidden" name="patient_id" x-model="selectedPatientId">
                         <input type="hidden" name="vaccine_id" x-model="selectedVaccineId">

                         <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                             <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                                 Atur Jadwal Vaksinasi
                             </h3>
                             <p class="text-sm text-gray-500 mb-4">
                                 Atur jadwal untuk <span class="font-bold" x-text="selectedPatientName"></span> - <span class="font-bold" x-text="selectedVaccineName"></span>
                             </p>
                             <div>
                                 <label class="block text-sm font-medium text-gray-700">Pilih Tanggal</label>
                                 <input type="date" name="schedule_at" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                             </div>
                         </div>
                         <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                             <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                                 Simpan Jadwal
                             </button>
                             <button type="button" @click="scheduleModalOpen = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                 Batal
                             </button>
                         </div>
                     </form>
                 </div>
             </div>
        </div>
    </div> <!-- Closing approvalData data scope -->

    <!-- jQuery & DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>

    <style>
        .dataTables_wrapper .dataTables_length select {
            border-radius: 0.375rem;
            padding: 0.25rem 2rem 0.25rem 0.5rem;
            border: 1px solid #d1d5db;
        }

        .dataTables_wrapper .dataTables_filter input {
            border-radius: 0.375rem;
            padding: 0.25rem 0.5rem;
            margin-left: 0.5rem;
            border: 1px solid #d1d5db;
        }

        table.dataTable.no-footer {
            border-bottom: 1px solid #e5e7eb !important;
        }
    </style>

    <script>
        // Global Functions for Action Buttons
        window.openApproveModal = function (patientId, vaccineId, patientName, vaccineName, villageId) {
            const event = new CustomEvent('open-approve', {
                detail: {
                    patientId, vaccineId, patientName, vaccineName, villageId
                }
            });
            window.dispatchEvent(event);
        };
        window.openDetailModal = function (item) {
            const event = new CustomEvent('open-detail', {
                detail: item
            });
            window.dispatchEvent(event);
        };
        window.openKipiModal = function (item) {
            const event = new CustomEvent('open-kipi', {
                detail: { item }
            });
            window.dispatchEvent(event);
        };
        window.openScheduleModal = function (patientId, vaccineId, patientName, vaccineName) {
            const event = new CustomEvent('open-schedule', {
                detail: {
                    patientId, vaccineId, patientName, vaccineName
                }
            });
            window.dispatchEvent(event);
        };

        $(document).ready(function () {
            // Common Config
            const commonConfig = {
                processing: true,
                serverSide: true,
                searching: false, // Drop DataTables search, use Global Search
                autoWidth: false, // Ensure full width
                scrollX: true, // Enable horizontal scrolling
                scrollY: '500px', // Enable vertical scrolling
                scrollCollapse: true, // Allow height to shrink if few rows
                lengthMenu: [10, 20, 50, 100, -1], // -1 is All
                language: { url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json' }
            };

            // Helper to get AJAX config
            function getAjax(status) {
                return {
                    url: '{{ route("admin.history") }}',
                    data: function (d) {
                        d.status = status;
                        d.search = '{{ request("search") }}';
                        d.vaccine_id = '{{ request("vaccine_id") }}';
                        d.village_id = '{{ request("village_id") }}';
                        d.posyandu_id = '{{ request("posyandu_id") }}';
                        d.start_date = '{{ request("start_date") }}';
                        d.end_date = '{{ request("end_date") }}';
                    }
                };
            }

            // 1. Jadwal Table
            $('#table-jadwal').DataTable({
                ...commonConfig,
                ajax: getAjax('jadwal'),
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'peserta', name: 'patient.name' },
                    { data: 'vaccine.name', name: 'vaccine.name' },
                    { data: 'jadwal_range', name: 'jadwal_range', orderable: false, searchable: false },
                    { data: 'village_name', name: 'village_name', orderable: false, searchable: false },
                    { data: 'posyandu_name', name: 'posyandu_name', orderable: false, searchable: false },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ]
            });

            // 2. Akan Table
            $('#table-akan').DataTable({
                ...commonConfig,
                ajax: getAjax('akan'),
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'peserta', name: 'patient.name' },
                    { data: 'vaccine.name', name: 'vaccine.name' },
                    { data: 'jadwal_range', name: 'jadwal_range', orderable: false, searchable: false },
                    { data: 'village_name', name: 'village_name', orderable: false, searchable: false },
                    { data: 'posyandu_name', name: 'posyandu_name', orderable: false, searchable: false },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ]
            });

            // 3. Sudah Table
            $('#table-sudah').DataTable({
                ...commonConfig,
                ajax: getAjax('sudah'),
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'peserta', name: 'patient.name' },
                    { data: 'vaccine.name', name: 'vaccine.name' },
                    { data: 'jadwal_range', name: 'jadwal_range', orderable: false, searchable: false }, // date
                    { data: 'posyandu_name', name: 'posyandu_name', orderable: false, searchable: false },
                    { data: 'status_badge', name: 'status_badge', orderable: false, searchable: false },
                    { data: 'kipi', name: 'kipi', orderable: false, searchable: false },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ]
            });

            // 4. Terlewat Table
            $('#table-terlewat').DataTable({
                ...commonConfig,
                ajax: getAjax('terlewat'),
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'peserta', name: 'patient.name' },
                    { data: 'vaccine.name', name: 'vaccine.name' },
                    { data: 'seharusnya', name: 'seharusnya', orderable: false, searchable: false },
                    { data: 'village_name', name: 'village_name', orderable: false, searchable: false },
                    { data: 'posyandu_name', name: 'posyandu_name', orderable: false, searchable: false },
                    { data: 'status_badge', name: 'status_badge', orderable: false, searchable: false },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ]
            });

            // 5. Schedule Table
            $('#table-schedule').DataTable({
                ...commonConfig,
                ajax: getAjax('schedule'),
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'peserta', name: 'patient.name' },
                    { data: 'vaccine.name', name: 'vaccine.name' },
                    { data: 'jadwal_range', name: 'jadwal_range', orderable: false, searchable: false },
                    { data: 'village_name', name: 'village_name', orderable: false, searchable: false },
                    { data: 'posyandu_name', name: 'posyandu_name', orderable: false, searchable: false },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ]
            });
        });

        document.addEventListener('alpine:init', () => {
            Alpine.data('approvalData', () => ({
                tab: 'jadwal',
                approveModalOpen: false,
                detailModalOpen: false,
                scheduleModalOpen: false,

                selectedPatientId: '',
                selectedVaccineId: '',
                selectedPatientName: '',
                selectedVaccineName: '',
                selectedVillageId: '',
                selectedPosyanduId: '',

                // Detail Modal Data
                detail: {
                    patient: {},
                    vaccine: {}
                },

                init() {
                    // Watch for tab changes to resizing DataTables
                    this.$watch('tab', (value) => {
                        this.$nextTick(() => {
                            $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();
                        });
                    });

                    window.addEventListener('open-approve', (event) => {
                        this.openApproveModal(
                            event.detail.patientId,
                            event.detail.vaccineId,
                            event.detail.patientName,
                            event.detail.vaccineName,
                            event.detail.villageId
                        );
                    });
                    window.addEventListener('open-detail', (event) => {
                        this.openDetailModal(event.detail);
                    });
                    window.addEventListener('open-kipi', (event) => {
                        this.openKipiModal(event.detail.item);
                    });
                    window.addEventListener('open-schedule', (event) => {
                        this.openScheduleModal(
                            event.detail.patientId,
                            event.detail.vaccineId,
                            event.detail.patientName,
                            event.detail.vaccineName
                        );
                    });
                },

                openScheduleModal(patientId, vaccineId, patientName, vaccineName) {
                    this.selectedPatientId = patientId;
                    this.selectedVaccineId = vaccineId;
                    this.selectedPatientName = patientName;
                    this.selectedVaccineName = vaccineName;
                    this.scheduleModalOpen = true;
                },

                openApproveModal(patientId, vaccineId, patientName, vaccineName, villageId) {
                    this.selectedPatientId = patientId;
                    this.selectedVaccineId = vaccineId;
                    this.selectedPatientName = patientName;
                    this.selectedVaccineName = vaccineName;
                    this.selectedVillageId = villageId; // Pre-select village

                    // Auto-select first posyandu for this village
                    this.$nextTick(() => {
                        const selector = `select[name="posyandu_id"] option[data-village="${villageId}"]`;
                        const firstOption = document.querySelector(selector);
                        if (firstOption) {
                            this.selectedPosyanduId = firstOption.value;
                        } else {
                            this.selectedPosyanduId = '';
                        }
                    });

                    this.approveModalOpen = true;
                },

                openDetailModal(item) {
                    this.detail = item;
                    this.detailModalOpen = true;
                },

                formatDate(dateString) {
                    if (!dateString) return '-';
                    const date = new Date(dateString);
                    return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
                },

                // KIPI Logic
                kipiModalOpen: false,
                kipiId: '',
                kipiOptions: ['Demam', 'Bengkak', 'Merah', 'Muntah', 'Lainnya'],
                selectedKipi: [],
                kipiOther: '',

                openKipiModal(item) {
                    this.kipiId = item.id;
                    this.selectedKipi = [];
                    this.kipiOther = '';

                    // Logic to populate choices
                    if (item.kipi) {
                         try {
                            const kipiData = JSON.parse(item.kipi);
                            if (Array.isArray(kipiData)) {
                                kipiData.forEach(k => {
                                    if (this.kipiOptions.includes(k)) {
                                        this.selectedKipi.push(k);
                                    } else {
                                        // Treat as "Lainnya"
                                        if (!this.selectedKipi.includes('Lainnya')) {
                                            this.selectedKipi.push('Lainnya');
                                        }
                                        this.kipiOther = k;
                                    }
                                });
                            }
                        } catch (e) {
                            console.error('Failed to parse KIPI data', e);
                        }
                    }

                    this.kipiModalOpen = true;
                }
            }))
        })
    </script>
@endsection