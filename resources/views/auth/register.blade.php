<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - PosyanduCare</title>
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
                                <span class="w-8 h-8 flex items-center justify-center rounded-full font-bold transition-colors duration-300" :class="step >= 1 ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-500'">1</span>
                                <span class="ml-2 font-medium text-sm" :class="step >= 1 ? 'text-blue-600' : 'text-gray-500'">Akun</span>
                            </div>
                            <div class="h-1 w-12 bg-gray-200" :class="step >= 2 ? 'bg-blue-600' : ''"></div>
                            <div class="flex items-center">
                                <span class="w-8 h-8 flex items-center justify-center rounded-full font-bold transition-colors duration-300" :class="step >= 2 ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-500'">2</span>
                                <span class="ml-2 font-medium text-sm" :class="step >= 2 ? 'text-blue-600' : 'text-gray-500'">Data Anak</span>
                            </div>
                        </div>
                    </div>

                    <!-- Step 1: Account & Parent Info -->
                    <div x-show="step === 1" x-transition.opacity>
                        <h3 class="text-lg font-bold text-gray-900 mb-4 border-b pb-2">Informasi Akun & Orang Tua</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap (Orang Tua)</label>
                                <input type="text" name="name" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" value="{{ old('name') }}">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                <input type="email" name="email" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" value="{{ old('email') }}">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                                <input type="password" name="password" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password</label>
                                <input type="password" name="password_confirmation" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                             <div class="col-span-1 md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Ibu Kandung</label>
                                <input type="text" name="mother_name" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" value="{{ old('mother_name') }}">
                            </div>
                        </div>
                        <div class="mt-6 flex justify-end">
                            <button type="button" @click="step = 2" class="px-6 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition">Lanjut</button>
                        </div>
                    </div>

                    <!-- Step 2: Patient/Child Info -->
                    <div x-show="step === 2" style="display: none;" x-transition.opacity>
                        <h3 class="text-lg font-bold text-gray-900 mb-4 border-b pb-2">Data Anak</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir</label>
                                <input type="date" name="date_birth" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" value="{{ old('date_birth') }}">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin</label>
                                <select name="gender" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="male">Laki-laki</option>
                                    <option value="female">Perempuan</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Telepon / WA</label>
                                <input type="text" name="phone" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" value="{{ old('phone') }}">
                            </div>
                             <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Desa Domisili</label>
                                <select name="village_id" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    @foreach($villages as $v)
                                        <option value="{{ $v->id }}">{{ $v->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-span-1 md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Lengkap</label>
                                <textarea name="address" rows="3" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('address') }}</textarea>
                            </div>
                        </div>
                        <div class="mt-6 flex justify-between">
                            <button type="button" @click="step = 1" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg font-medium hover:bg-gray-300 transition">Kembali</button>
                            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition">Daftar Sekarang</button>
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
