<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Peserta - TANDU GEMAS</title>
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
                        <img src="{{ asset('images/logo-tandu-gemas.png') }}" alt="TANDU GEMAS" class="w-8 h-8 rounded-full">
                        <span class="font-bold text-xl"><span class="text-amber-500">TANDU</span> <span class="text-emerald-600">GEMAS</span></span>
                    </div>
                    <div class="flex items-center gap-4">
                        @auth
                            <span class="text-gray-700 text-sm hidden sm:block">Halo, <span class="font-semibold">{{ Auth::user()->name }}</span></span>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="bg-gray-100 hover:bg-gray-200 text-gray-600 px-4 py-2 rounded-lg text-sm font-medium transition">Keluar</button>
                            </form>
                        @else
                            <span class="text-gray-700 text-sm hidden sm:block">Halo, <span class="font-semibold">{{ $patient->name ?? 'Tamu' }}</span></span>
                            <a href="{{ url('/') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-600 px-4 py-2 rounded-lg text-sm font-medium transition">Beranda</a>
                        @endauth
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

            @if(session('error'))
            <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg flex items-center gap-2" role="alert">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span>{{ session('error') }}</span>
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
                                <p class="text-xs text-blue-600 font-bold mt-1">Usia Saat Ini: {{ $patientAgeMonths }} Bulan</p>
                            </div>
                        </div>
                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between border-b pb-2">
                                <span class="text-gray-500">Ibu Kandung</span>
                                <span class="font-medium">{{ $patient->mother_name }}</span>
                            </div>
                            <div class="flex justify-between border-b pb-2">
                                <span class="text-gray-500">Tanggal Lahir</span>
                                <span class="font-medium">{{ \Carbon\Carbon::parse($patient->date_birth)->format('d F Y') }}</span>
                            </div>
                            <div class="flex justify-between border-b pb-2">
                                <span class="text-gray-500">Desa</span>
                                <span class="font-medium text-right">{{ $patient->address }}</span>
                            </div>
                        </div>
                        
                        @if($allVaccinesCompleted)
                        <a href="{{ route('user.certificate') }}" class="mt-6 w-full bg-emerald-600 hover:bg-emerald-700 text-white py-2 px-4 rounded-xl font-medium shadow-sm transition block text-center">
                            Download Sertifikat
                        </a>
                        @else
                        <div class="mt-6 w-full bg-blue-50 border border-blue-200 text-blue-800 py-3 px-4 rounded-xl text-sm text-center">
                            <p class="font-bold mb-1">📅 Jadwal Imunisasi</p>
                            <p class="text-xs">Pantau kalender untuk jadwal vaksinasi anak Anda.</p>
                        </div>
                        @endif
                    </div>

                    <!-- Vaccination Status System -->
                    <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100">
                        <h4 class="font-bold text-gray-900 mb-4">Jadwal & Status Imunisasi</h4>
                        <div class="space-y-3 max-h-[500px] overflow-y-auto pr-2">
                            @foreach($vaccineSchedules as $item)
                            <div class="flex items-center justify-between p-3 rounded-lg border {{ $item->status == 'selesai' ? 'border-emerald-100 bg-emerald-50' : ($item->status == 'bisa_diajukan' ? 'border-green-100 bg-green-50' : ($item->status == 'terlewat' ? 'border-red-100 bg-red-50' : 'border-gray-100 bg-white')) }}">
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">{{ $item->vaccine->name }}</p>
                                    <p class="text-xs text-gray-500 mt-0.5">Jadwal: {{ $item->start_date ? $item->start_date->format('d M Y') : 'N/A' }}</p>
                                    @if($item->end_date)
                                      <p class="text-[10px] text-gray-400">s/d {{ $item->end_date->format('d M Y') }}</p>
                                    @endif
                                </div>
                                <div>
                                    @if($item->status == 'selesai')
                                        <span class="px-2 py-1 bg-green-100 text-green-700 text-xs rounded-full font-bold">Selesai</span>
                                    @elseif($item->status == 'bisa_diajukan')
                                        <span class="px-2 py-1 bg-green-100 text-green-700 text-xs rounded-full font-bold animate-pulse">Saatnya Vaksin</span>
                                    @elseif($item->status == 'terlewat')
                                        <span class="px-2 py-1 bg-red-100 text-red-700 text-xs rounded-full font-bold">Terlewat</span>
                                    @else
                                        <span class="px-2 py-1 bg-gray-100 text-gray-500 text-xs rounded-full">Nanti ({{ $item->min_age }} Bln)</span>
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
                        <h3 class="font-bold text-gray-800 mb-4 text-lg">Kalender Imunisasi Anak</h3>
                        <div id="calendar"></div>
                    </div>
                </div>

            </div>
        </main>
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
                events: @json($calendarEvents),
                eventClick: function(info) {
                    alert('Jadwal: ' + info.event.title + '\nTanggal: ' + info.event.start.toLocaleDateString('id-ID'));
                }
            });
            calendar.render();
        });
    </script>
</body>
</html>
