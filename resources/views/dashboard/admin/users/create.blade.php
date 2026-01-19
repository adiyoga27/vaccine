@extends('layouts.admin')

@section('title', 'Registrasi Peserta Baru')

@section('content')
<div class="mb-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-800">Registrasi Peserta Baru</h1>
        <a href="{{ route('admin.users') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg text-sm transition">
            Kembali
        </a>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
    <form action="{{ route('admin.users.store') }}" method="POST">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Akun User -->
            <div class="md:col-span-2">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Informasi Akun</h3>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap (Anak)</label>
                <input type="text" name="name" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" required value="{{ old('name') }}">
                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email (Untuk Login)</label>
                <input type="email" name="email" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" required value="{{ old('email') }}">
                @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input type="password" name="password" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" required>
                @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password</label>
                <input type="password" name="password_confirmation" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" required>
            </div>

            <!-- Data Pasien -->
            <div class="md:col-span-2 mt-4">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Data Pasien</h3>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Ibu Kandung</label>
                <input type="text" name="mother_name" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" required value="{{ old('mother_name') }}">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir Anak</label>
                <input type="date" name="date_birth" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" required value="{{ old('date_birth') }}">
                @error('date_birth') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin</label>
                <select name="gender" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" required>
                    <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Laki-laki</option>
                    <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Perempuan</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Desa</label>
                <select name="village_id" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" required>
                    <option value="">-- Pilih Desa --</option>
                    @foreach($villages as $village)
                        <option value="{{ $village->id }}" {{ old('village_id') == $village->id ? 'selected' : '' }}>{{ $village->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Lengkap</label>
                <textarea name="address" rows="2" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" required>{{ old('address') }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">No. WhatsApp (Untuk Notifikasi)</label>
                <input type="text" name="phone" placeholder="ex: 08123456789" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" required value="{{ old('phone') }}">
                <p class="text-xs text-gray-500 mt-1">Pastikan nomor terdaftar di WhatsApp.</p>
            </div>

        </div>

        <div class="mt-8 flex justify-end">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition shadow-lg shadow-blue-500/30">
                Simpan Data
            </button>
        </div>
    </form>
</div>
@endsection
