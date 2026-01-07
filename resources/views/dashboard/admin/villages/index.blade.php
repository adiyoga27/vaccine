@extends('layouts.admin')

@section('content')
<div class="flex justify-between items-center mb-8">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Data Desa</h1>
        <p class="text-gray-500 mt-1">Kelola data desa dan posyandu wilayah kerja.</p>
    </div>
    <button onclick="document.getElementById('createModal').classList.remove('hidden')" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium shadow-sm transition flex items-center">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        Tambah Desa
    </button>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Desa</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Posyandu</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Peserta</th>
                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @foreach($villages as $village)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $village->name }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $village->posyandu_name ?? '-' }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $village->vaccine_patients_count }} Riwayat</td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <div class="flex justify-end gap-2">
                        <button onclick="openEditModal({{ $village->id }}, '{{ $village->name }}', '{{ $village->posyandu_name }}')" class="text-indigo-600 hover:text-indigo-900">Edit</button>
                        <form action="{{ route('admin.villages.destroy', $village->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Create Modal -->
<div id="createModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-50 flex items-center justify-center">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Tambah Desa Baru</h3>
        <form action="{{ route('admin.villages.store') }}" method="POST">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Desa</label>
                    <input type="text" name="name" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Posyandu (Opsional)</label>
                    <input type="text" name="posyandu_name" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
            </div>
            <div class="mt-6 flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('createModal').classList.add('hidden')" class="px-4 py-2 border rounded-lg text-gray-700 hover:bg-gray-50">Batal</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-50 flex items-center justify-center">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Edit Data Desa</h3>
        <form id="editForm" method="POST">
            @csrf
            @method('PUT')
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Desa</label>
                    <input type="text" name="name" id="edit_name" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Posyandu</label>
                    <input type="text" name="posyandu_name" id="edit_posyandu_name" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
            </div>
            <div class="mt-6 flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('editModal').classList.add('hidden')" class="px-4 py-2 border rounded-lg text-gray-700 hover:bg-gray-50">Batal</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Update</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openEditModal(id, name, posyandu) {
        document.getElementById('editForm').action = '/admin/villages/' + id;
        document.getElementById('edit_name').value = name;
        document.getElementById('edit_posyandu_name').value = posyandu == null ? '' : posyandu;
        document.getElementById('editModal').classList.remove('hidden');
    }
</script>
@endsection
