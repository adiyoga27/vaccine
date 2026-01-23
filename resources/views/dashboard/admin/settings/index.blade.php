@extends('layouts.admin')

@section('content')
<div class="flex justify-between items-center mb-8">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Pengaturan Sertifikat</h1>
        <p class="text-gray-500 mt-1">Atur data master untuk sertifikat imunisasi.</p>
    </div>
</div>

@if(session('success'))
<div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded">
    {{ session('success') }}
</div>
@endif

@if($errors->any())
<div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded">
    <ul class="list-disc list-inside">
        @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<form action="{{ route('admin.certificate-settings.update') }}" method="POST" enctype="multipart/form-data">
    @csrf
    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Kepala UPT Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                Kepala UPT BLUD Puskesmas
            </h3>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                    <input type="text" name="kepala_upt_name" value="{{ old('kepala_upt_name', $settings->kepala_upt_name) }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Contoh: SABRI, SKM">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanda Tangan</label>
                    @if($settings->kepala_upt_signature)
                    <div class="mb-2 p-3 bg-gray-50 rounded-lg">
                        <img src="{{ asset($settings->kepala_upt_signature) }}" alt="TTD Kepala UPT" class="h-20 object-contain">
                        <p class="text-xs text-gray-500 mt-1">Gambar saat ini</p>
                    </div>
                    @endif
                    <input type="file" name="kepala_upt_signature" accept="image/png,image/jpeg" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    <p class="text-xs text-gray-500 mt-1">PNG/JPG, maks 2MB. Kosongkan jika tidak ingin mengubah.</p>
                </div>
            </div>
        </div>

        <!-- Petugas Jurim Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                Petugas Jurim Pustu ILP
            </h3>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                    <input type="text" name="petugas_jurim_name" value="{{ old('petugas_jurim_name', $settings->petugas_jurim_name) }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Contoh: Endang Junaela, S.ST.,NS">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanda Tangan</label>
                    @if($settings->petugas_jurim_signature)
                    <div class="mb-2 p-3 bg-gray-50 rounded-lg">
                        <img src="{{ asset($settings->petugas_jurim_signature) }}" alt="TTD Petugas Jurim" class="h-20 object-contain">
                        <p class="text-xs text-gray-500 mt-1">Gambar saat ini</p>
                    </div>
                    @endif
                    <input type="file" name="petugas_jurim_signature" accept="image/png,image/jpeg" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-green-50 file:text-green-700 hover:file:bg-green-100">
                    <p class="text-xs text-gray-500 mt-1">PNG/JPG, maks 2MB. Kosongkan jika tidak ingin mengubah.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Background Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mt-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            Background Sertifikat
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @if($settings->background_image)
            <div class="p-3 bg-gray-50 rounded-lg">
                <img src="{{ asset($settings->background_image) }}" alt="Background Sertifikat" class="w-full max-h-48 object-contain rounded">
                <p class="text-xs text-gray-500 mt-1">Gambar background saat ini</p>
            </div>
            @endif
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Upload Background Baru</label>
                <input type="file" name="background_image" accept="image/png,image/jpeg" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-pink-50 file:text-pink-700 hover:file:bg-pink-100">
                <p class="text-xs text-gray-500 mt-1">PNG/JPG, maks 5MB. Ukuran rekomendasi: 1122 x 793 px (A4 Landscape).</p>
            </div>
        </div>
    </div>

    <!-- Submit Button -->
    <div class="mt-6 flex justify-end">
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium shadow-sm transition flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            Simpan Pengaturan
        </button>
    </div>
</form>

<!-- Info Box -->
<div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mt-6">
    <div class="flex">
        <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        <div>
            <h4 class="font-semibold text-blue-900">Catatan Penting</h4>
            <p class="text-sm text-blue-700 mt-1">Pengaturan ini akan diterapkan pada sertifikat <strong>baru</strong> yang diterbitkan. Sertifikat yang sudah diterbitkan sebelumnya akan tetap menggunakan data saat sertifikat tersebut dibuat (snapshot), sehingga perubahan di sini tidak akan mempengaruhi sertifikat lama.</p>
        </div>
    </div>
</div>
@endsection
