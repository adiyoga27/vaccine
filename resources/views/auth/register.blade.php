<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - TANDU GEMAS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-blue-50 min-h-screen py-10">

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <div class="p-8 sm:p-12">
                <div class="mb-10 text-center">
                    <h2 class="text-3xl font-extrabold text-blue-900">Pendaftaran Peserta Baru</h2>
                    <p class="mt-2 text-gray-600">Isi data orang tua dan anak untuk memulai</p>
                </div>

                <form action="{{ route('register') }}" method="POST" x-data="{ step: 1 }" class="space-y-8">
                    @csrf
                    
                    @if ($errors->any())
                        <div class="bg-red-50 text-red-700 p-4 rounded-lg">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Step Indicators -->
                    <div class="flex justify-center mb-8">
                        <div class="flex items-center space-x-4">
                            <div class="flex items-center">
                                <span class="w-10 h-10 flex items-center justify-center rounded-full font-bold transition-all duration-300 shadow-sm" :class="step >= 1 ? 'bg-blue-600 text-white ring-4 ring-blue-100' : 'bg-gray-200 text-gray-500'">1</span>
                                <span class="ml-3 font-medium text-sm hidden sm:block" :class="step >= 1 ? 'text-blue-600' : 'text-gray-500'">Akun</span>
                            </div>
                            <div class="h-1 w-16 bg-gray-200 rounded-full" :class="step >= 2 ? 'bg-blue-600' : ''"></div>
                            <div class="flex items-center">
                                <span class="w-10 h-10 flex items-center justify-center rounded-full font-bold transition-all duration-300 shadow-sm" :class="step >= 2 ? 'bg-blue-600 text-white ring-4 ring-blue-100' : 'bg-gray-200 text-gray-500'">2</span>
                                <span class="ml-3 font-medium text-sm hidden sm:block" :class="step >= 2 ? 'text-blue-600' : 'text-gray-500'">Data Anak</span>
                            </div>
                        </div>
                    </div>

                    <!-- Step 1: Account & Parent Info -->
                    <div x-show="step === 1" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-x-4" x-transition:enter-end="opacity-100 transform translate-x-0">
                        <h3 class="text-xl font-bold text-gray-900 mb-6 border-b pb-3">Informasi Akun & Orang Tua</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-6">
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">Nama Lengkap Anak</label>
                                <input type="text" name="name" required class="w-full px-4 py-3 rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition border bg-gray-50 focus:bg-white" placeholder="Masukan nama lengkap anak" value="{{ old('name') }}">
                            </div>
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">Email</label>
                                <input type="email" name="email" required class="w-full px-4 py-3 rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition border bg-gray-50 focus:bg-white" placeholder="contoh@email.com" value="{{ old('email') }}">
                            </div>
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">Password</label>
                                <input type="password" name="password" required class="w-full px-4 py-3 rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition border bg-gray-50 focus:bg-white" placeholder="••••••••">
                            </div>
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">Konfirmasi Password</label>
                                <input type="password" name="password_confirmation" required class="w-full px-4 py-3 rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition border bg-gray-50 focus:bg-white" placeholder="Ulangi password">
                            </div>
                             <div class="col-span-1 md:col-span-2 space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">Nama Ibu Kandung</label>
                                <input type="text" name="mother_name" required class="w-full px-4 py-3 rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition border bg-gray-50 focus:bg-white" placeholder="Nama lengkap ibu kandung" value="{{ old('mother_name') }}">
                            </div>
                        </div>
                        <div class="mt-8 flex justify-end">
                            <button type="button" @click="step = 2" class="px-8 py-3 bg-blue-600 text-white rounded-xl font-bold hover:bg-blue-700 transform hover:scale-105 transition shadow-lg flex items-center">
                                Lanjut
                                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </button>
                        </div>
                    </div>

                    <!-- Step 2: Patient/Child Info -->
                    <div x-show="step === 2" style="display: none;" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-x-4" x-transition:enter-end="opacity-100 transform translate-x-0">
                        <h3 class="text-xl font-bold text-gray-900 mb-6 border-b pb-3">Data Anak</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-6">
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">Tanggal Lahir</label>
                                <input type="date" name="date_birth" required class="w-full px-4 py-3 rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition border bg-gray-50 focus:bg-white" value="{{ old('date_birth') }}">
                            </div>
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">Jenis Kelamin</label>
                                <select name="gender" required class="w-full px-4 py-3 rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition border bg-gray-50 focus:bg-white">
                                    <option value="" disabled selected>Pilih Jenis Kelamin</option>
                                    <option value="male">Laki-laki</option>
                                    <option value="female">Perempuan</option>
                                </select>
                            </div>
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">Nomor Telepon / WA</label>
                                <input type="text" name="phone" required class="w-full px-4 py-3 rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition border bg-gray-50 focus:bg-white" placeholder="Contoh: 0812xxxxxxxx" value="{{ old('phone') }}">
                            </div>
                             <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">Desa Domisili</label>
                                <select name="village_id" class="w-full px-4 py-3 rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition border bg-gray-50 focus:bg-white">
                                    <option value="" disabled selected>Pilih Desa</option>
                                    @foreach($villages as $v)
                                        <option value="{{ $v->id }}">{{ $v->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-span-1 md:col-span-2 space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">Alamat Lengkap</label>
                                <textarea name="address" rows="3" required class="w-full px-4 py-3 rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition border bg-gray-50 focus:bg-white" placeholder="Jalan, RT/RW, Dusun...">{{ old('address') }}</textarea>
                            </div>
                        </div>
                        <div class="mt-8 flex justify-between items-center">
                            <button type="button" @click="step = 1" class="px-6 py-3 bg-gray-100 text-gray-700 rounded-xl font-bold hover:bg-gray-200 transition flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                                Kembali
                            </button>
                            <button type="submit" class="px-8 py-3 bg-blue-600 text-white rounded-xl font-bold hover:bg-blue-700 transform hover:scale-105 transition shadow-lg">Daftar Sekarang</button>
                        </div>
                    </div>
                </form>

                <div class="mt-8 text-center border-t pt-6">
                    <p class="text-sm text-gray-600">
                        Sudah punya akun? 
                        <a href="{{ route('login') }}" class="font-medium text-blue-600 hover:text-blue-500">Masuk disini</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
