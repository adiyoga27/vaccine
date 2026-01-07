@extends('layouts.admin')

@section('content')
<div class="flex justify-between items-center mb-8">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Jadwal Posyandu</h1>
        <p class="text-gray-500 mt-1">Buat jadwal rutin untuk setiap desa.</p>
    </div>
    <button onclick="document.getElementById('createModal').classList.remove('hidden')" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium shadow-sm transition flex items-center">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        Buat Jadwal
    </button>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    @if(count($schedules) > 0)
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Desa</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @foreach($schedules as $schedule)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                    {{ $schedule->scheduled_at->format('l, d F Y') }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ $schedule->village->name }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    @if($schedule->scheduled_at->isPast())
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Selesai</span>
                    @else
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">Akan Datang</span>
                    @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <form action="{{ route('admin.schedules.destroy', $schedule->id) }}" method="POST" onsubmit="return confirm('Hapus jadwal ini?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <div class="text-center py-12">
        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-400">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
        </div>
        <h3 class="text-lg font-medium text-gray-900">Belum ada jadwal</h3>
        <p class="text-gray-500 mt-1">Silakan buat jadwal posyandu baru.</p>
    </div>
    @endif
</div>

<!-- Create Modal -->
<div id="createModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-50 flex items-center justify-center">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Buat Jadwal Baru</h3>
        <form action="{{ route('admin.schedules.store') }}" method="POST">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Desa</label>
                    <select name="village_id" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @foreach($villages as $village)
                            <option value="{{ $village->id }}">{{ $village->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Kegiatan</label>
                    <input type="date" name="scheduled_at" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
            </div>
            <div class="mt-6 flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('createModal').classList.add('hidden')" class="px-4 py-2 border rounded-lg text-gray-700 hover:bg-gray-50">Batal</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Simpan Jadwal</button>
            </div>
        </form>
    </div>
</div>
@endsection
