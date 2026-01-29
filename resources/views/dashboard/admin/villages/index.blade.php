@extends('layouts.admin')

@section('content')
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Data Dusun</h1>
            <p class="text-gray-500 mt-1">Kelola data Dusun dan posyandu wilayah kerja.</p>
        </div>
        <button onclick="document.getElementById('createModal').classList.remove('hidden')"
            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium shadow-sm transition flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Tambah Dusun
        </button>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Nama Dusun</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Nama Posyandu</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Total Peserta</th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($villages as $village)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $village->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            @if($village->posyandus->count() > 0)
                                <div class="flex flex-wrap gap-1">
                                    @foreach($village->posyandus as $posyandu)
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $posyandu->name }}
                                        </span>
                                    @endforeach
                                </div>
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <button onclick="openPatientsModal({{ $village->id }}, '{{ $village->name }}')"
                                class="text-blue-600 hover:text-blue-800 hover:underline font-medium focus:outline-none">
                                {{ $village->patients_count }} Peserta
                            </button>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex justify-end gap-2">
                                <button onclick='openPosyanduModal(@json($village))'
                                    class="text-green-600 hover:text-green-900 text-sm bg-green-50 px-3 py-1 rounded-md border border-green-200">
                                    Kelola Posyandu
                                </button>
                                <button onclick="openEditModal({{ $village->id }}, '{{ $village->name }}')"
                                    class="text-indigo-600 hover:text-indigo-900 border border-indigo-200 px-3 py-1 rounded-md hover:bg-indigo-50">Edit</button>
                                <form action="{{ route('admin.villages.destroy', $village->id) }}" method="POST"
                                    onsubmit="return confirm('Yakin ingin menghapus?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="text-red-600 hover:text-red-900 border border-red-200 px-3 py-1 rounded-md hover:bg-red-50">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Create Modal -->
    <div id="createModal"
        class="hidden fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-50 flex items-center justify-center">
        <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Tambah Dusun Baru</h3>
            <form action="{{ route('admin.villages.store') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Dusun</label>
                        <input type="text" name="name" required
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                </div>
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" onclick="document.getElementById('createModal').classList.add('hidden')"
                        class="px-4 py-2 border rounded-lg text-gray-700 hover:bg-gray-50">Batal</button>
                    <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="editModal"
        class="hidden fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-50 flex items-center justify-center">
        <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Edit Data Dusun</h3>
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Dusun</label>
                        <input type="text" name="name" id="edit_name" required
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                </div>
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" onclick="document.getElementById('editModal').classList.add('hidden')"
                        class="px-4 py-2 border rounded-lg text-gray-700 hover:bg-gray-50">Batal</button>
                    <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Update</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Posyandu Management Modal -->
    <div id="posyanduModal"
        class="hidden fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-50 flex items-center justify-center">
        <div class="bg-white rounded-xl shadow-xl max-w-2xl w-full p-6 mx-4">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h3 class="text-xl font-bold text-gray-900" id="posyanduModalTitle">Kelola Posyandu</h3>
                    <p class="text-sm text-gray-500" id="posyanduModalSubtitle">Daftar posyandu</p>
                </div>
                <button onclick="document.getElementById('posyanduModal').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>

            <!-- Add New Posyandu Form -->
            <div class="bg-gray-50 p-4 rounded-lg mb-6 border border-gray-100">
                <h4 class="text-sm font-semibold text-gray-700 mb-3">Tambah Posyandu Baru</h4>
                <form action="{{ route('admin.posyandus.store') }}" method="POST" class="flex gap-2">
                    @csrf
                    <input type="hidden" name="village_id" id="posyandu_village_id">
                    <input type="text" name="name" placeholder="Nama Posyandu" required
                        class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                    <input type="text" name="address" placeholder="Alamat (Opsional)"
                        class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                    <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 whitespace-nowrap">
                        + Tambah
                    </button>
                </form>
            </div>

            <!-- Posyandu List -->
            <div class="overflow-y-auto max-h-64">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nama Posyandu</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Alamat</th>
                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="posyanduListBody">
                        <!-- JS will populate this -->
                    </tbody>
                </table>
                <p id="noPosyanduMsg" class="text-center text-gray-500 py-4 hidden">Belum ada data posyandu.</p>
            </div>
        </div>
    </div>

    <!-- Patients List Modal -->
    <div id="patientsModal"
        class="hidden fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-50 flex items-center justify-center">
        <div class="bg-white rounded-xl shadow-xl max-w-4xl w-full p-6 mx-4">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h3 class="text-xl font-bold text-gray-900" id="patientsModalTitle">Daftar Peserta</h3>
                    <p class="text-sm text-gray-500" id="patientsModalSubtitle">Dusun ...</p>
                </div>
                <button onclick="document.getElementById('patientsModal').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>

            <!-- Loading State -->
            <div id="patientsLoading" class="hidden py-8 text-center">
                <svg class="animate-spin h-8 w-8 text-blue-600 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                    </circle>
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                    </path>
                </svg>
                <p class="text-gray-500 mt-2">Memuat data peserta...</p>
            </div>

            <!-- Patients Table -->
            <div class="overflow-y-auto max-h-[60vh] relative">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50 sticky top-0">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Peserta</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ibu</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">JK</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">No. HP</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Alamat</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="patientsListBody">
                        <!-- JS will populate this -->
                    </tbody>
                </table>
                <p id="noPatientsMsg" class="text-center text-gray-500 py-8 hidden">Belum ada peserta di dusun ini.</p>
            </div>
        </div>
    </div>

    <script>
        function openEditModal(id, name) {
            document.getElementById('editForm').action = '/admin/villages/' + id;
            document.getElementById('edit_name').value = name;
            document.getElementById('editModal').classList.remove('hidden');
        }

        function openPosyanduModal(village) {
            document.getElementById('posyanduModalTitle').textContent = 'Kelola Posyandu: ' + village.name;
            document.getElementById('posyandu_village_id').value = village.id;
            document.getElementById('posyanduModal').classList.remove('hidden');

            const tbody = document.getElementById('posyanduListBody');
            tbody.innerHTML = '';

            if (village.posyandus && village.posyandus.length > 0) {
                document.getElementById('noPosyanduMsg').classList.add('hidden');
                village.posyandus.forEach(posyandu => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                                                    <td class="px-4 py-3 text-sm text-gray-900">${posyandu.name}</td>
                                                    <td class="px-4 py-3 text-sm text-gray-500">${posyandu.address || '-'}</td>
                                                    <td class="px-4 py-3 text-right text-sm font-medium flex justify-end gap-2">
                                                         <form action="/admin/posyandus/${posyandu.id}" method="POST" onsubmit="return confirm('Hapus posyandu ini?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                                                        </form>
                                                    </td>
                                                `;
                    tbody.appendChild(tr);
                });
            } else {
                document.getElementById('noPosyanduMsg').classList.remove('hidden');
            }
        }

        function openPatientsModal(villageId, villageName) {
            document.getElementById('patientsModalTitle').textContent = 'Daftar Peserta';
            document.getElementById('patientsModalSubtitle').textContent = 'Dusun: ' + villageName;

            const modal = document.getElementById('patientsModal');
            const tbody = document.getElementById('patientsListBody');
            const loading = document.getElementById('patientsLoading');
            const noMsg = document.getElementById('noPatientsMsg');

            modal.classList.remove('hidden');
            tbody.innerHTML = '';
            loading.classList.remove('hidden');
            noMsg.classList.add('hidden');

            fetch(`/admin/villages/${villageId}/patients`)
                .then(response => response.json())
                .then(data => {
                    loading.classList.add('hidden');

                    if (data.length > 0) {
                        data.forEach(patient => {
                            const tr = document.createElement('tr');
                            const gender = patient.gender == 'male' ? 'L' : 'P';
                            const genderClass = patient.gender == 'male' ? 'bg-blue-100 text-blue-800' : 'bg-pink-100 text-pink-800';

                            tr.innerHTML = `
                                    <td class="px-4 py-3 text-sm font-medium text-gray-900">${patient.name}</td>
                                    <td class="px-4 py-3 text-sm text-gray-500">${patient.mother_name || '-'}</td>
                                    <td class="px-4 py-3 text-sm text-gray-500">
                                        <span class="px-2 py-0.5 rounded-full text-xs font-bold ${genderClass}">${gender}</span>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-500">${patient.phone || '-'}</td>
                                    <td class="px-4 py-3 text-sm text-gray-500 truncate max-w-xs" title="${patient.address || ''}">${patient.address || '-'}</td>
                                `;
                            tbody.appendChild(tr);
                        });
                    } else {
                        noMsg.classList.remove('hidden');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    loading.classList.add('hidden');
                    alert('Gagal memuat data peserta.');
                    modal.classList.add('hidden');
                });
        }
    </script>
@endsection