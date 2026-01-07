<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Peserta - PosyanduCare</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gray-100 font-sans" x-data="{ openModal: false }">
    
    <div class="min-h-screen flex flex-col">
        <!-- Navbar -->
        <nav class="bg-white shadow-sm z-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center text-white font-bold">P</div>
                        <span class="font-bold text-xl text-gray-800">PosyanduCare</span>
                    </div>
                    <div class="flex items-center gap-4">
                        <span class="text-gray-700 text-sm hidden sm:block">Halo, <span class="font-semibold">{{ Auth::user()->name }}</span></span>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="bg-gray-100 hover:bg-gray-200 text-gray-600 px-4 py-2 rounded-lg text-sm font-medium transition">Keluar</button>
                        </form>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
            
            @if(session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg flex items-center gap-2" role="alert">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <span>{{ session('success') }}</span>
            </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <!-- Left Sidebar: Profile & Status -->
                <div class="lg:col-span-1 space-y-6">
                    <!-- Child Profile Card -->
                    <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100">
                        <div class="flex items-center gap-4 mb-6">
                            <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 text-2xl font-bold">
                                {{ substr($patient->name ?? 'A', 0, 1) }}
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-gray-900">{{ $patient->name ?? 'Belum ada data' }}</h3>
                                <p class="text-sm text-gray-500">{{ $patient->gender == 'male' ? 'Laki-laki' : 'Perempuan' }} • {{ \Carbon\Carbon::parse($patient->date_birth)->age }} Tahun</p>
                                <p class="text-xs text-gray-400 mt-1">Usia: {{ \Carbon\Carbon::parse($patient->date_birth)->diffInMonths(now()) }} Bulan</p>
                            </div>
                        </div>
                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between border-b pb-2">
                                <span class="text-gray-500">Ibu Kandung</span>
                                <span class="font-medium">{{ $patient->mother_name }}</span>
                            </div>
                            <div class="flex justify-between border-b pb-2">
                                <span class="text-gray-500">Desa</span>
                                <span class="font-medium text-right">{{ $patient->address }}</span>
                            </div>
                        </div>
                        <button @click="openModal = true" class="mt-6 w-full bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-xl font-medium shadow-sm transition">
                            + Ajukan Vaksinasi
                        </button>
                    </div>

                    <!-- Vaccination Status System -->
                    <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100">
                        <h4 class="font-bold text-gray-900 mb-4">Jadwal & Status Imunisasi</h4>
                        <div class="space-y-3 max-h-[400px] overflow-y-auto pr-2">
                            @foreach($vaccineStatus as $item)
                            <div class="flex items-center justify-between p-3 rounded-lg border {{ $item->status == 'selesai' ? 'border-green-100 bg-green-50' : ($item->status == 'bisa_diajukan' ? 'border-blue-100 bg-white' : 'border-gray-100 bg-gray-50 opacity-70') }}">
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">{{ $item->vaccine->name }}</p>
                                    <p class="text-xs text-gray-500">Min. Usia: {{ $item->min_age }} Bulan</p>
                                </div>
                                <div>
                                    @if($item->status == 'selesai')
                                        <span class="px-2 py-1 bg-green-100 text-green-700 text-xs rounded-full font-bold">Selesai</span>
                                    @elseif($item->status == 'pengajuan')
                                        <span class="px-2 py-1 bg-yellow-100 text-yellow-700 text-xs rounded-full font-bold">Diproses</span>
                                    @elseif($item->status == 'bisa_diajukan')
                                        <span class="px-2 py-1 bg-blue-100 text-blue-700 text-xs rounded-full font-bold">Tersedia</span>
                                    @else
                                        <span class="px-2 py-1 bg-gray-200 text-gray-500 text-xs rounded-full">Nanti</span>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Right Content: Calendar -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100 h-full min-h-[500px]">
                        <div id="calendar"></div>
                    </div>
                </div>

            </div>
        </main>
    </div>

    <!-- Request Modal -->
    <div x-cloak x-show="openModal" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="openModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="openModal = false"></div>

            <div x-show="openModal" class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form action="{{ route('user.request') }}" method="POST">
                    @csrf
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4" id="modal-title">Ajukan Vaksinasi</h3>
                        
                        @if(count($schedules) > 0)
                        <div class="space-y-4" x-data="{ selectedSchedule: '', schedules: {{ json_encode($schedules) }} }">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Pilih Jadwal Kegiatan</label>
                                <select name="schedule_id" x-model="selectedSchedule" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm border p-2">
                                    <option value="" disabled selected>-- Pilih Tanggal --</option>
                                    @foreach($schedules as $sch)
                                        <option value="{{ $sch->id }}">
                                            Posyandu Tgl {{ \Carbon\Carbon::parse($sch->scheduled_at)->format('d F Y') }}
                                        </option>
                                    @endforeach
                                </select>
                                <p class="text-xs text-gray-500 mt-1">Hanya jadwal yang tersedia di desa Anda yang ditampilkan.</p>
                            </div>
                            
                            <template x-if="selectedSchedule">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Jenis Vaksin (Yang Tersedia)</label>
                                    <select name="vaccine_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm border p-2">
                                        <option value="" disabled selected>-- Pilih Vaksin --</option>
                                        <template x-for="schedule in schedules.filter(s => s.id == selectedSchedule)" :key="schedule.id">
                                            <template x-for="vaccine in schedule.vaccines">
                                                <option :value="vaccine.id" x-text="vaccine.name + ' (Min. ' + vaccine.minimum_age + ' Bln)'"></option>
                                            </template>
                                        </template>
                                    </select>
                                    <!-- Warning if no intersection with eligible vaccines (Optional enhancement, complex to do purely in Alpine without huge payload. Keeping it simple implies showing ALL available in schedule, OR filtering further. 
                                         For now, showing what is available in the schedule is the safer bet as 'Eligible' is just a suggestion, availability is a constraint.) 
                                    -->
                                </div>
                            </template>
                            <template x-if="!selectedSchedule">
                                <div class="text-sm text-gray-500 italic">Pilih jadwal terlebih dahulu untuk melihat vaksin yang tersedia.</div>
                            </template>
                        </div>
                        @else
                        <div class="text-center py-4 bg-yellow-50 rounded-lg text-yellow-700 text-sm">
                            Maaf, belum ada jadwal kegiatan Posyandu yang tersedia untuk desa Anda saat ini.
                        </div>
                        @endif

                    </div>
                    @if(count($schedules) > 0 && !$eligibleVaccines->isEmpty())
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none sm:w-auto sm:text-sm">
                            Kirim Pengajuan
                        </button>
                        <button type="button" @click="openModal = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:w-auto sm:text-sm">
                            Batal
                        </button>
                    </div>
                    @else
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="button" @click="openModal = false" class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:w-auto sm:text-sm">
                            Tutup
                        </button>
                    </div>
                    @endif
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'id',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,listWeek'
                },
                events: [
                    // User History Events
                    @foreach($histories as $history)
                    {
                        title: 'Vaksin: {{ $history->vaccine->name }}',
                        start: '{{ $history->request_date->format("Y-m-d") }}',
                        color: '{{ $history->status == "selesai" ? "#10B981" : "#F59E0B" }}'
                    },
                    @endforeach

                    // Upcoming Schedules (Read-only visualization)
                    @foreach($schedules as $sch)
                    {
                        title: 'Posyandu',
                        start: '{{ $sch->scheduled_at->format("Y-m-d") }}',
                        // display: 'background', // Removed for better visibility
                        color: '#3B82F6' // Blue-500
                    },
                    @endforeach
                ]
            });
            calendar.render();
        });
    </script>
</body>
</html>
