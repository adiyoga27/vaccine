@extends('layouts.admin')

@section('title', 'Edit Data Peserta')

@section('content')
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-800">Edit Data Peserta</h1>
            <a href="{{ route('admin.users') }}"
                class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg text-sm transition">
                Kembali
            </a>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <form action="{{ route('admin.users.update', $user->id) }}" method="POST"
            x-data="{
                villages: {{ \Illuminate\Support\Js::from($villages) }},
                selectedVillage: '{{ old('village_id', $user->patient->village_id ?? '') }}',
                selectedPosyandu: '{{ old('posyandu_id', $user->patient->posyandu_id ?? '') }}',
                availablePosyandus: [],
                
                init() {
                    // Ensure string type for comparison
                    this.selectedVillage = String(this.selectedVillage);
                    this.selectedPosyandu = String(this.selectedPosyandu);
                    
                    if(this.selectedVillage) {
                        this.updatePosyandus();
                    }
                },

                updatePosyandus() {
                    // Find village, comparing as strings or loosen type
                    const village = this.villages.find(v => v.id == this.selectedVillage);
                    this.availablePosyandus = village ? village.posyandus : [];
                }
            }"
        >
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Akun User -->
                <div class="md:col-span-2">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Informasi Akun</h3>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap (Anak)</label>
                    <input type="text" name="name"
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" required
                        value="{{ old('name', $user->patient->name ?? $user->name) }}">
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Removed Email and Password fields as requested -->

                <!-- Data Pasien -->
                <div class="md:col-span-2 mt-4">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Data Pasien</h3>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Ibu Kandung</label>
                    <input type="text" name="mother_name"
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" required
                        value="{{ old('mother_name', $user->patient->mother_name ?? '') }}">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">NIK (Nomor Induk Kependudukan)</label>
                    <input type="text" name="nik" maxlength="16"
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                        value="{{ old('nik', $user->patient->nik ?? '') }}" placeholder="16 digit NIK">
                    @error('nik') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir Anak</label>
                    <input type="date" name="date_birth"
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" required
                        value="{{ old('date_birth', $user->patient ? $user->patient->date_birth->format('Y-m-d') : '') }}">
                    @error('date_birth') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin</label>
                    <select name="gender"
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" required>
                        <option value="male" {{ (old('gender', $user->patient->gender ?? '') == 'male') ? 'selected' : '' }}>
                            Laki-laki</option>
                        <option value="female" {{ (old('gender', $user->patient->gender ?? '') == 'female') ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Dusun</label>
                    <select name="village_id" x-model="selectedVillage" @change="updatePosyandus()"
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" required>
                        <option value="">-- Pilih Dusun --</option>
                        @foreach($villages as $village)
                        <option value="{{ $village->id }}">{{ $village->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Posyandu</label>
                    <select name="posyandu_id" x-model="selectedPosyandu" :disabled="!selectedVillage"
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 disabled:bg-gray-100 disabled:text-gray-400">
                        <option value="">-- Pilih Posyandu --</option>
                        <template x-for="posyandu in availablePosyandus" :key="posyandu.id">
                            <option :value="posyandu.id" x-text="posyandu.name"></option>
                        </template>
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Lengkap</label>
                    <textarea name="address" rows="2"
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                        required>{{ old('address', $user->patient->address ?? '') }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">No. WhatsApp (Untuk Notifikasi)</label>
                    <input type="text" name="phone" placeholder="ex: 08123456789"
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" required
                        value="{{ old('phone', $user->patient->phone ?? '') }}">
                    <p class="text-xs text-gray-500 mt-1">Pastikan nomor terdaftar di WhatsApp.</p>
                </div>

                @if(isset($isCompleted) && $isCompleted)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Sertifikat (Opsional)</label>
                        <input type="text" name="certificate_number"
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                            value="{{ old('certificate_number', $user->patient->certificate_number ?? '') }}">
                    </div>
                @endif

            </div>

            <div class="mt-8 flex justify-end">
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition shadow-lg shadow-blue-500/30">
                    Update Data
                </button>
            </div>
        </form>
    </div>
@endsection