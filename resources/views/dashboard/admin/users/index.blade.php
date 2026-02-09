@extends('layouts.admin')

@section('content')
    <div x-data="{
                selectedItems: [],
                importModalOpen: false,
                detailModalOpen: false,
                detail: {},
                isImporting: false,

                init() {
                    // Listen for custom events to update state
                    window.addEventListener('toggle-item', (event) => {
                        this.toggleItem(event.detail);
                    });
                    window.addEventListener('open-detail', (event) => {
                        this.openDetail(event.detail);
                    });
                },

                toggleSelectAll(event) {
                    this.selectedItems = [];
                    if (event.target.checked) {
                        // Select all visible items in the current DataTable page
                        $('#usersTable tbody .user-checkbox').each((index, element) => {
                            $(element).prop('checked', true);
                            this.selectedItems.push($(element).val());
                        });
                    } else {
                        $('#usersTable tbody .user-checkbox').prop('checked', false);
                    }
                    // Ensure the header checkbox reflects the state of all checkboxes
                    this.updateHeaderCheckbox();
                },

                toggleItem(id) {
                    const index = this.selectedItems.indexOf(id);
                    if (index > -1) {
                        this.selectedItems.splice(index, 1);
                    } else {
                        this.selectedItems.push(id);
                    }
                    this.updateHeaderCheckbox();
                },

                updateHeaderCheckbox() {
                    const allCheckboxes = $('#usersTable tbody .user-checkbox');
                    const checkedCheckboxes = $('#usersTable tbody .user-checkbox:checked');
                    const headerCheckbox = $('#usersTable thead .select-all-checkbox');

                    if (allCheckboxes.length === checkedCheckboxes.length && allCheckboxes.length > 0) {
                        headerCheckbox.prop('checked', true);
                    } else {
                        headerCheckbox.prop('checked', false);
                    }
                },

                confirmBulkDelete() {
                    if (this.selectedItems.length === 0) return;

                    if (confirm(`Apakah Anda yakin ingin menghapus ${this.selectedItems.length} peserta terpilih? Tindakan ini tidak dapat dibatalkan!`)) {
                        fetch('{{ route('admin.users.bulk-delete') }}', {
                            method: 'DELETE',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ ids: this.selectedItems })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert(data.message);
                                $('#usersTable').DataTable().ajax.reload();
                                this.selectedItems = [];
                                this.updateHeaderCheckbox();
                            } else {
                                alert('Gagal menghapus data.');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Terjadi kesalahan saat menghapus data.');
                        });
                    }
                },

                openDetail(user) {
                    this.detail = user;
                    this.detailModalOpen = true;
                },

                calculateAge(dateOfBirth) {
                    if (!dateOfBirth) return '-';
                    const dob = new Date(dateOfBirth);
                    const today = new Date();
                    let age = today.getFullYear() - dob.getFullYear();
                    const m = today.getMonth() - dob.getMonth();
                    if (m < 0 || (m === 0 && today.getDate() < dob.getDate())) {
                        age--;
                    }
                    return `${age} tahun`;
                },

                formatDate(dateString) {
                    if (!dateString) return '-';
                    const options = { year: 'numeric', month: 'long', day: 'numeric' };
                    return new Date(dateString).toLocaleDateString('id-ID', options);
                }
            }">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Data Peserta</h1>
                <p class="text-gray-500 mt-1">Kelola data peserta vaksinasi dan riwayat.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <!-- Bulk Delete -->
                <button x-show="selectedItems.length > 0" @click="confirmBulkDelete()" style="display: none;"
                    class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition shadow-sm flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                        </path>
                    </svg>
                    Hapus Selected (<span x-text="selectedItems.length"></span>)
                </button>

                <!-- Export Buttons -->
                <a href="{{ route('admin.users.export') }}"
                    class="inline-flex justify-center items-center px-4 py-2 border border-green-600 shadow-sm text-sm font-medium rounded-lg text-green-600 bg-white hover:bg-green-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>
                    </svg>
                    Excel
                </a>
                <a href="{{ route('admin.users.export-pdf') }}"
                    class="inline-flex justify-center items-center px-4 py-2 border border-red-600 shadow-sm text-sm font-medium rounded-lg text-red-600 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z">
                        </path>
                    </svg>
                    PDF
                </a>

                <button @click="importModalOpen = true"
                    class="inline-flex justify-center items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-5 h-5 mr-2 -ml-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                    </svg>
                    Import
                </button>
                <a href="{{ route('admin.users.create') }}"
                    class="inline-flex justify-center items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-5 h-5 mr-2 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Tambah Peserta
                </a>
            </div>
        </div>

        <!-- DataTables Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-visible p-4">
            <table id="usersTable" class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left">
                            <input type="checkbox"
                                class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 select-all-checkbox"
                                @click="toggleSelectAll($event)">
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Peserta
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Orang Tua
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIK
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tgl Lahir
                            / Usia</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Alamat/Dusun</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Posyandu</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Riwayat
                            Vaksin</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Sertifikat</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <!-- DataTables will populate this -->
                </tbody>
            </table>
        </div>

        <!-- Import Modal -->
        <div x-show="importModalOpen" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"
                    @click="importModalOpen = false"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div
                    class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form action="{{ route('admin.users.import') }}" method="POST" enctype="multipart/form-data"
                        @submit="isImporting = true">
                        @csrf
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Import Data Peserta</h3>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">File Excel (.xlsx,
                                        .xls)</label>
                                    <input type="file" name="file" required accept=".xlsx, .xls, .csv"
                                        class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                </div>
                                <div class="bg-yellow-50 p-4 rounded-md">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd"
                                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                                    clip-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <h3 class="text-sm font-medium text-yellow-800">Perhatian Import</h3>
                                            <div class="mt-2 text-sm text-yellow-700">
                                                <p>Pastikan format file sesuai dengan template.
                                                    <a href="{{ route('admin.users.import-template') }}"
                                                        class="font-bold underline text-yellow-800 hover:text-yellow-900">Download
                                                        Template</a>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="submit"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-purple-600 text-base font-medium text-white hover:bg-purple-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50 disabled:cursor-not-allowed">
                                <span x-show="!isImporting">Import</span>
                                <span x-show="isImporting" class="flex items-center" style="display: none;">
                                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                            stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                    Proses...
                                </span>
                            </button>
                            <button type="button" @click="importModalOpen = false"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Detail Modal (Reused) -->
        <div x-show="detailModalOpen" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity" aria-hidden="true" @click="detailModalOpen = false">
                    <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                </div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div
                    class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                    <!-- Modal Content (Dynamically filled via Alpine) -->
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start justify-between border-b pb-4 mb-4">
                            <h3 class="text-xl leading-6 font-bold text-gray-900">Detail Peserta & Riwayat Vaksinasi</h3>
                            <button @click="detailModalOpen = false"
                                class="text-gray-400 hover:text-gray-500 focus:outline-none">
                                <span class="sr-only">Close</span>
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Patient Info -->
                            <div class="md:col-span-1 bg-gray-50 p-4 rounded-lg">
                                <h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4 border-b pb-2">
                                    Informasi Pasien</h4>
                                <div class="space-y-4">
                                    <div class="text-center mb-4">
                                        <div
                                            class="h-24 w-24 rounded-full bg-blue-100 mx-auto flex items-center justify-center text-blue-500 text-3xl font-bold">
                                            <span
                                                x-text="detail.patient?.name ? detail.patient.name.charAt(0) : '?'"></span>
                                        </div>
                                        <h5 class="mt-2 font-bold text-gray-900" x-text="detail.patient?.name"></h5>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-400">Nama Ibu</p>
                                        <p class="text-sm font-medium text-gray-900"
                                            x-text="detail.patient?.mother_name || '-'"></p>
                                    </div>
                                    <div class="grid grid-cols-2 gap-2">
                                        <div>
                                            <p class="text-xs text-gray-400">Usia</p>
                                            <p class="text-sm font-medium text-gray-900"
                                                x-text="calculateAge(detail.patient?.date_birth)"></p>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-400">Gender</p>
                                            <p class="text-sm font-medium text-gray-900"
                                                x-text="detail.patient?.gender == 'male' ? 'Laki-laki' : 'Perempuan'"></p>
                                        </div>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-400">Alamat</p>
                                        <p class="text-sm font-medium text-gray-900"
                                            x-text="detail.patient?.address || '-'"></p>
                                    </div>
                                </div>
                            </div>
                            <!-- Vaccine History -->
                            <div class="md:col-span-2">
                                <h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4 border-b pb-2">
                                    Riwayat Vaksinasi</h4>
                                <div class="overflow-y-auto max-h-[400px]">
                                    <template
                                        x-if="detail.patient?.vaccine_patients && detail.patient.vaccine_patients.length > 0">
                                        <div class="space-y-4">
                                            <template x-for="vp in detail.patient.vaccine_patients" :key="vp.id">
                                                <div
                                                    class="flex items-start p-3 bg-white border border-gray-100 rounded-lg shadow-sm">
                                                    <div class="ml-4 flex-1">
                                                        <div class="flex items-center justify-between">
                                                            <h5 class="text-sm font-bold text-gray-900"
                                                                x-text="vp.vaccine?.name"></h5>
                                                            <span
                                                                class="px-2 py-0.5 rounded-full text-xs font-medium uppercase bg-green-100 text-green-800"
                                                                x-text="vp.status"></span>
                                                        </div>
                                                        <div class="mt-1 text-sm text-gray-500">
                                                            <span>Divaksin pada: </span>
                                                            <span class="font-medium"
                                                                x-text="formatDate(vp.vaccinated_at)"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </template>
                                    <template
                                        x-if="!detail.patient?.vaccine_patients || detail.patient.vaccine_patients.length === 0">
                                        <p class="text-center text-gray-500 py-4">Belum ada riwayat.</p>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

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

        #usersTable tbody {
            min-height: 600px;
        }

        #usersTable_wrapper .dataTables_scrollBody {
            min-height: 600px;
        }

        /* Ensure table container has minimum height */
        #usersTable_wrapper {
            min-height: 700px;
        }
    </style>

    <script>
        // Use Global function for Detail Button
        window.openDetailModal = function (user) {
            const event = new CustomEvent('open-detail', { detail: user });
            window.dispatchEvent(event);
        };

        // Dropdown toggle for action menu
        window.toggleDropdown = function (id) {
            const el = document.getElementById(id);
            if (!el) return;
            // Close all other dropdowns first
            document.querySelectorAll('[id^="dd-"]').forEach(function (dd) {
                if (dd.id !== id) dd.classList.add('hidden');
            });
            el.classList.toggle('hidden');
        };

        // Close dropdown when clicking outside
        document.addEventListener('click', function (e) {
            if (!e.target.closest('[id^="dd-"]') && !e.target.closest('button[onclick*="toggleDropdown"]')) {
                document.querySelectorAll('[id^="dd-"]').forEach(function (dd) {
                    dd.classList.add('hidden');
                });
            }
        });

        $(document).ready(function () {
            var table = $('#usersTable').DataTable({
                processing: true,
                serverSide: true,
                scrollX: true,
                autoWidth: false,
                ajax: '{{ route("admin.users") }}',
                columns: [
                    { data: 'checkbox', name: 'checkbox', orderable: false, searchable: false },
                    { data: 'peserta', name: 'patient.name' },
                    { data: 'orang_tua', name: 'patient.mother_name' },
                    { data: 'nik', name: 'nik', orderable: false, searchable: false },
                    { data: 'usia', name: 'patient.date_birth' },
                    { data: 'alamat', name: 'patient.village.name' },
                    { data: 'posyandu', name: 'posyandu', orderable: false, searchable: false },
                    { data: 'riwayat', name: 'riwayat', orderable: false, searchable: false },
                    { data: 'sertifikat', name: 'sertifikat', orderable: false, searchable: false },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ],
                order: [[8, 'desc']],
                language: { url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json' },
                lengthMenu: [10, 20, 50, 100, 200, 500],
                pageLength: 100,
                drawCallback: function () {
                    // After table redraws, ensure Alpine.js state for checkboxes is consistent
                    const selectedItems = window.patientData && window.patientData.selectedItems ? window.patientData.selectedItems : [];

                    if (window.patientData) {
                        $('#usersTable tbody .user-checkbox').each(function () {
                            if (selectedItems.includes($(this).val())) {
                                $(this).prop('checked', true);
                            } else {
                                $(this).prop('checked', false);
                            }
                        });
                        window.patientData.updateHeaderCheckbox();
                    } else {
                        // Fallback in case Alpine hasn't initialized yet
                        // Usually won't happen if loaded correctly
                    }
                }
            });

            // Handle Checkbox clicks
            $('#usersTable').on('change', '.user-checkbox', function () {
                const id = $(this).val();
                window.dispatchEvent(new CustomEvent('toggle-item', { detail: id }));
            });

            // Expose patientData to the global scope manually to ensure safe access
            document.addEventListener('alpine:initialized', () => {
                const element = document.querySelector('[x-data]');
                if (element) {
                    window.patientData = Alpine.$data(element);
                }
            });
        });
    </script>
@endsection