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
                            </div>
                        </div>
                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between border-b pb-2">
                                <span class="text-gray-500">Ibu Kandung</span>
                                <span class="font-medium">{{ $patient->mother_name }}</span>
                            </div>
                            <div class="flex justify-between border-b pb-2">
                                <span class="text-gray-500">Desa</span>
                                <span class="font-medium text-right">{{ $patient->address }}</span> <!-- Address used as desa temporarily if not linked -->
                            </div>
                        </div>
                        <button @click="openModal = true" class="mt-6 w-full bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-xl font-medium shadow-sm transition">
                            + Ajukan Vaksinasi
                        </button>
                    </div>

                    <!-- History List -->
                    <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100">
                        <h4 class="font-bold text-gray-900 mb-4">Riwayat Pengajuan</h4>
                        @if(count($histories) > 0)
                            <div class="space-y-4">
                                @foreach($histories as $history)
                                <div class="flex items-start gap-3">
                                    <div class="mt-1 w-2 h-2 rounded-full {{ $history->status == 'selesai' ? 'bg-green-500' : 'bg-yellow-500' }}"></div>
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $history->vaccine->name }}</p>
                                        <div class="flex items-center gap-2 text-xs text-gray-500 mt-1">
                                            <span>{{ \Carbon\Carbon::parse($history->request_date)->format('d M Y') }}</span>
                                            <span class="px-2 py-0.5 rounded-full {{ $history->status == 'selesai' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                                {{ ucfirst($history->status) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm text-gray-500 text-center py-4">Belum ada riwayat vaksinasi.</p>
                        @endif
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
            <div x-show="openModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" @click="openModal = false"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="openModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form action="{{ route('user.request') }}" method="POST">
                    @csrf
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Ajukan Vaksinasi</h3>
                                <div class="mt-4 space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Tanggal Rencana</label>
                                        <input type="date" name="request_date" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm border p-2">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Jenis Vaksin</label>
                                        <select name="vaccine_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm border p-2">
                                            @foreach($vaccines as $v)
                                                <option value="{{ $v->id }}">{{ $v->name }} (Min. {{ $v->minimum_age }} Bulan)</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                            Kirim Pengajuan
                        </button>
                        <button type="button" @click="openModal = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Batal
                        </button>
                    </div>
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
                    // Mock events for now, ideally populated from DB
                    @foreach($histories as $history)
                    {
                        title: '{{ $history->vaccine->name }}',
                        start: '{{ $history->request_date->format("Y-m-d") }}',
                        color: '{{ $history->status == "selesai" ? "#10B981" : "#F59E0B" }}'
                    },
                    @endforeach
                ]
            });
            calendar.render();
        });
    </script>
</body>
</html>
