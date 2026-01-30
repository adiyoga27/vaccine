@extends('layouts.admin')

@section('content')
<div class="flex justify-between items-center mb-8">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Data Administrator</h1>
        <p class="text-gray-500 mt-1">Kelola akun administrator sistem.</p>
    </div>
    <button onclick="document.getElementById('createModal').classList.remove('hidden')" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium shadow-sm transition flex items-center">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        Tambah Admin
    </button>
</div>

@if(session('error'))
<div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded">
    {{ session('error') }}
</div>
@endif

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-4">
        <table id="adminTable" class="w-full text-sm text-left" style="width: 100%">
            <thead class="bg-gray-50 text-gray-500 font-medium">
                <tr>
                    <th class="px-6 py-3">#</th>
                    <th class="px-6 py-3">Nama</th>
                    <th class="px-6 py-3">Email</th>
                    <th class="px-6 py-3">Dibuat</th>
                    <th class="px-6 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($admins as $admin)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $loop->iteration }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-semibold">
                                {{ strtoupper(substr($admin->name, 0, 1)) }}
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-900">{{ $admin->name }}</p>
                                @if($admin->id === auth()->id())
                                <span class="text-xs text-green-600">(Anda)</span>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $admin->email }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $admin->created_at->format('d M Y') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-right">
                        <div class="flex justify-end gap-2">
                            <button onclick="openEditModal({{ $admin->id }}, '{{ $admin->name }}', '{{ $admin->email }}')" class="text-gray-400 hover:text-blue-600 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                            </button>
                            @if($admin->id !== auth()->id())
                            <form action="{{ route('admin.admins.destroy', $admin->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus admin ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-gray-400 hover:text-red-600 transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                        Belum ada data administrator.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Create Modal -->
<div id="createModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-50 flex items-center justify-center">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Tambah Administrator</h3>
        <form action="{{ route('admin.admins.store') }}" method="POST">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
                    <input type="text" name="name" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <input type="password" name="password" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
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
        <h3 class="text-lg font-bold text-gray-900 mb-4">Edit Administrator</h3>
        <form id="editForm" method="POST">
            @csrf
            @method('PUT')
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
                    <input type="text" name="name" id="edit_name" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" id="edit_email" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Password Baru</label>
                    <input type="password" name="password" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <p class="text-xs text-gray-500 mt-1">Kosongkan jika tidak ingin mengubah password</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password Baru</label>
                    <input type="password" name="password_confirmation" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
            </div>
            <div class="mt-6 flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('editModal').classList.add('hidden')" class="px-4 py-2 border rounded-lg text-gray-700 hover:bg-gray-50">Batal</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Update</button>
            </div>
        </form>
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

    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_filter {
        margin-bottom: 1rem;
    }

    .dataTables_wrapper .dataTables_info,
    .dataTables_wrapper .dataTables_paginate {
        margin-top: 1rem;
    }
</style>

<script>
    $(document).ready(function() {
        $('#adminTable').DataTable({
            autoWidth: false,
            scrollX: true,
            scrollCollapse: true,
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Semua"]],
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
            },
            columnDefs: [
                { orderable: false, targets: [4] } // Disable sorting on Aksi column
            ]
        });
    });

    function openEditModal(id, name, email) {
        document.getElementById('editForm').action = '/admin/admins/' + id;
        document.getElementById('edit_name').value = name;
        document.getElementById('edit_email').value = email;
        document.getElementById('editModal').classList.remove('hidden');
    }
</script>
@endsection
