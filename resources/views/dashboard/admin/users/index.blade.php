@extends('layouts.admin')

@section('content')
<div x-data="patientData">
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-800">Data Peserta Imunisasi</h1>
            <a href="{{ route('admin.users.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition shadow-sm">
                + Registrasi Peserta
            </a>
        </div>
        <p class="text-gray-500 mt-1">Daftar semua orang tua dan anak yang terdaftar.</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Anak</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Orang Tua</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tgl Lahir / Usia</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Alamat/Desa</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Riwayat Vaksin</th>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Sertifikat</th>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($users as $user)
                @php
                    $completedVaccines = $user->patient ? $user->patient->vaccinePatients->where('status', 'selesai')->unique('vaccine_id') : collect([]);
                    $isCompleted = ($totalVaccines > 0 && $completedVaccines->count() >= $totalVaccines);
                @endphp
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <div class="text-sm font-bold text-gray-900">{{ $user->patient->name ?? '-' }}</div>
                        <div class="text-xs text-gray-500">{{ $user->patient->gender == 'male' ? 'Laki-laki' : 'Perempuan' }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm text-gray-900">{{ $user->name }}</div>
                        <div class="text-xs text-gray-500">{{ $user->email }}</div>
                        <div class="text-xs text-gray-500">Ibu: {{ $user->patient->mother_name ?? '-' }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($user->patient)
                        <div class="text-sm text-gray-900">{{ $user->patient->date_birth->format('d M Y') }}</div>
                        <div class="text-xs text-gray-500">{{ $user->patient->date_birth->age }} Tahun</div>
                        @else
                        <span class="text-gray-400">-</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm text-gray-900">{{ $user->patient->address ?? '-' }}</div>
                        <div class="text-xs text-gray-600">{{ $user->patient->village->name ?? '' }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex flex-wrap gap-1 mb-2">
                            @forelse($completedVaccines as $vp)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                    {{ $vp->vaccine->name }}
                                </span>
                            @empty
                                <span class="text-xs text-gray-400 italic">Belum ada vaksin selesai</span>
                            @endforelse
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                        @if($isCompleted)
                            <a href="{{ route('admin.certificate', $user->patient->id) }}" target="_blank" class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-full shadow-sm text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
                                Unduh Sertifikat
                            </a>
                        @else
                            <span class="text-xs text-gray-400">Belum Lengkap</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                        <button @click='openDetailModal(@json($user))' class="inline-flex items-center px-3 py-1 bg-cyan-600 text-white rounded text-xs hover:bg-cyan-700 transition">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            Detail
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $users->links() }}
        </div>
    </div>

    <!-- Detail Modal -->
    <div x-show="detailModalOpen" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true" @click="detailModalOpen = false">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start justify-between border-b pb-4 mb-4">
                        <h3 class="text-xl leading-6 font-bold text-gray-900" id="modal-title">
                            Detail Peserta & Riwayat Vaksinasi
                        </h3>
                        <button @click="detailModalOpen = false" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                            <span class="sr-only">Close</span>
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Left Column: Patient Info -->
                        <div class="md:col-span-1 bg-gray-50 p-4 rounded-lg">
                            <h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4 border-b pb-2">Informasi Pasien</h4>
                            
                            <div class="space-y-4">
                                <div class="text-center mb-4">
                                    <div class="h-24 w-24 rounded-full bg-blue-100 mx-auto flex items-center justify-center text-blue-500 text-3xl font-bold">
                                        <span x-text="detail.patient?.name ? detail.patient.name.charAt(0) : '?'"></span>
                                    </div>
                                    <h5 class="mt-2 font-bold text-gray-900" x-text="detail.patient?.name"></h5>
                                    <p class="text-sm text-gray-500" x-text="detail.patient?.nik || 'NIK Belum diisi'"></p>
                                </div>

                                <div>
                                    <p class="text-xs text-gray-400">Nama Ibu</p>
                                    <p class="text-sm font-medium text-gray-900" x-text="detail.patient?.mother_name || '-'"></p>
                                </div>
                                
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <p class="text-xs text-gray-400">Usia</p>
                                        <p class="text-sm font-medium text-gray-900" x-text="calculateAge(detail.patient?.date_birth)"></p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-400">Gender</p>
                                        <p class="text-sm font-medium text-gray-900" x-text="detail.patient?.gender == 'male' ? 'Laki-laki' : 'Perempuan'"></p>
                                    </div>
                                </div>

                                <div>
                                    <p class="text-xs text-gray-400">Tanggal Lahir</p>
                                    <p class="text-sm font-medium text-gray-900" x-text="formatDate(detail.patient?.date_birth)"></p>
                                </div>

                                <div>
                                    <p class="text-xs text-gray-400">Alamat</p>
                                    <p class="text-sm font-medium text-gray-900" x-text="detail.patient?.address || '-'"></p>
                                    <p class="text-xs text-gray-600" x-text="detail.patient?.village?.name ? 'Desa ' + detail.patient.village.name : ''"></p>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column: Vaccination History -->
                        <div class="md:col-span-2">
                            <h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4 border-b pb-2">Riwayat Vaksinasi</h4>
                            
                            <div class="overflow-y-auto max-h-[400px]">
                                <template x-if="detail.patient?.vaccine_patients && detail.patient.vaccine_patients.length > 0">
                                    <div class="space-y-4">
                                        <template x-for="vp in detail.patient.vaccine_patients" :key="vp.id">
                                            <div class="flex items-start p-3 bg-white border border-gray-100 rounded-lg shadow-sm hover:shadow-md transition">
                                                <div class="flex-shrink-0 mt-1">
                                                    <!-- Icon based on status -->
                                                    <template x-if="vp.status === 'selesai'">
                                                        <div class="h-8 w-8 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                        </div>
                                                    </template>
                                                    <template x-if="vp.status === 'active' || vp.status === 'upcoming'">
                                                        <div class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                        </div>
                                                    </template>
                                                    <template x-if="vp.status === 'overdue'">
                                                        <div class="h-8 w-8 rounded-full bg-red-100 flex items-center justify-center text-red-600">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                        </div>
                                                    </template>
                                                </div>
                                                <div class="ml-4 flex-1">
                                                    <div class="flex items-center justify-between">
                                                        <h5 class="text-sm font-bold text-gray-900" x-text="vp.vaccine?.name"></h5>
                                                        <span class="px-2 py-0.5 rounded-full text-xs font-medium uppercase" 
                                                              :class="{
                                                                  'bg-emerald-100 text-emerald-800': vp.status === 'selesai',
                                                                  'bg-blue-100 text-blue-800': vp.status === 'active' || vp.status === 'upcoming',
                                                                  'bg-red-100 text-red-800': vp.status === 'overdue'
                                                              }"
                                                              x-text="vp.status"></span>
                                                    </div>
                                                    <div class="mt-1 text-sm text-gray-500">
                                                        <span x-text="vp.status === 'selesai' ? 'Divaksin pada: ' : 'Jadwal: '"></span>
                                                        <span class="font-medium" x-text="formatDate(vp.date || vp.start_date)"></span>
                                                    </div>
                                                    <template x-if="vp.village">
                                                         <p class="text-xs text-gray-400 mt-1">Lokasi: Desa <span x-text="vp.village.name"></span></p>
                                                    </template>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                                <template x-if="!detail.patient?.vaccine_patients || detail.patient.vaccine_patients.length === 0">
                                    <div class="text-center py-10 text-gray-500 bg-gray-50 rounded-lg border border-dashed border-gray-300">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        <p class="mt-2 text-sm">Belum ada data riwayat vaksinasi.</p>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm" @click="detailModalOpen = false">
                        Tutup
                    </button>
                    <!-- Optional: Certificate Button inside Modal -->
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('patientData', () => ({
            detailModalOpen: false,
            detail: {},

            openDetailModal(user) {
                this.detail = user;
                this.detailModalOpen = true;
            },

            formatDate(dateString) {
                if(!dateString) return '-';
                const date = new Date(dateString);
                return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
            },

            calculateAge(dateString) {
                if(!dateString) return '-';
                const birthDate = new Date(dateString);
                const today = new Date();
                let age = today.getFullYear() - birthDate.getFullYear();
                const m = today.getMonth() - birthDate.getMonth();
                if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
                    age--;
                }
                return age + ' Tahun';
            }
        }))
    })
</script>
@endsection
