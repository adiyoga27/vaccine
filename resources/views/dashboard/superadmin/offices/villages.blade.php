@extends('layouts.superadmin')

@section('content')
<div class="mb-8">
    <div class="flex items-center gap-3 mb-2">
        <a href="{{ route('superadmin.offices') }}" class="text-gray-400 hover:text-gray-600 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-900">Kelola Dusun — {{ $office->name }}</h1>
    </div>
    <p class="text-gray-500 mt-1">Centang dusun yang ingin di-assign ke kantor ini.</p>
</div>

<form action="{{ route('superadmin.offices.villages.update', $office->id) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <h2 class="text-lg font-semibold text-gray-900">Daftar Dusun</h2>
                <span class="px-2.5 py-1 bg-purple-100 text-purple-700 rounded-full text-xs font-semibold" id="selectedCount">{{ count($assignedVillageIds) }} dipilih</span>
            </div>
            <div class="flex items-center gap-3">
                <label class="flex items-center cursor-pointer text-sm text-gray-600">
                    <input type="checkbox" id="selectAll" class="mr-2 rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                    Pilih Semua
                </label>
            </div>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($villages as $village)
                <label class="flex items-center p-4 border border-gray-200 rounded-lg hover:border-purple-300 hover:bg-purple-50 cursor-pointer transition group {{ in_array($village->id, $assignedVillageIds) ? 'border-purple-400 bg-purple-50' : '' }}">
                    <input type="checkbox" name="village_ids[]" value="{{ $village->id }}"
                        class="village-checkbox rounded border-gray-300 text-purple-600 focus:ring-purple-500 mr-3"
                        {{ in_array($village->id, $assignedVillageIds) ? 'checked' : '' }}>
                    <div>
                        <p class="font-medium text-gray-900 group-hover:text-purple-900">{{ $village->name }}</p>
                        @if($village->posyandu_name)
                        <p class="text-xs text-gray-500">{{ $village->posyandu_name }}</p>
                        @endif
                    </div>
                </label>
                @endforeach
            </div>

            @if($villages->isEmpty())
            <div class="text-center py-12 text-gray-500">
                Belum ada dusun. Buat dusun terlebih dahulu melalui admin panel.
            </div>
            @endif
        </div>
    </div>

    <div class="mt-6 flex justify-end gap-3">
        <a href="{{ route('superadmin.offices') }}" class="px-4 py-2 border rounded-lg text-gray-700 hover:bg-gray-50">Batal</a>
        <button type="submit" class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 font-medium shadow-sm transition">
            Simpan Perubahan
        </button>
    </div>
</form>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectAll = document.getElementById('selectAll');
        const checkboxes = document.querySelectorAll('.village-checkbox');
        const selectedCount = document.getElementById('selectedCount');

        function updateCount() {
            const checked = document.querySelectorAll('.village-checkbox:checked').length;
            selectedCount.textContent = checked + ' dipilih';
        }

        selectAll.addEventListener('change', function() {
            checkboxes.forEach(cb => cb.checked = selectAll.checked);
            // Update parent label styling
            checkboxes.forEach(cb => {
                const label = cb.closest('label');
                if (cb.checked) {
                    label.classList.add('border-purple-400', 'bg-purple-50');
                } else {
                    label.classList.remove('border-purple-400', 'bg-purple-50');
                }
            });
            updateCount();
        });

        checkboxes.forEach(cb => {
            cb.addEventListener('change', function() {
                const label = cb.closest('label');
                if (cb.checked) {
                    label.classList.add('border-purple-400', 'bg-purple-50');
                } else {
                    label.classList.remove('border-purple-400', 'bg-purple-50');
                }
                selectAll.checked = document.querySelectorAll('.village-checkbox:checked').length === checkboxes.length;
                updateCount();
            });
        });

        // Initial state
        selectAll.checked = document.querySelectorAll('.village-checkbox:checked').length === checkboxes.length;
    });
</script>
@endsection
