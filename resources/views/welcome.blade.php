<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Imunisasi Posyandu</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    
    <style>
        .hero-pattern {
            background-color: #f3f4f6;
            background-image: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z' fill='%239C92AC' fill-opacity='0.1' fill-rule='evenodd'/%3E%3C/svg%3E");
        }
    </style>
</head>
<body class="font-sans antialiased text-gray-800 bg-gray-50">

    <!-- Navigation -->
    <nav class="bg-white/80 backdrop-blur-md fixed w-full z-50 shadow-sm transition-all duration-300" x-data="{ atTop: true }" @scroll.window="atTop = (window.pageYOffset > 50) ? false : true" :class="{ 'shadow-md': !atTop }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex-shrink-0 flex items-center gap-2">
                    <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center text-white font-bold text-xl">P</div>
                    <span class="font-bold text-xl text-blue-900 tracking-tight">Posyandu<span class="text-blue-500">Care</span></span>
                </div>
                <div class="flex space-x-4">
                    @if (Route::has('login'))
                        @auth
                            @if(Auth::user()->role === 'admin')
                                <a href="{{ route('admin.dashboard') }}" class="text-gray-700 hover:text-blue-600 font-medium px-3 py-2 rounded-md transition">Dashboard</a>
                            @else
                                <a href="{{ route('user.dashboard') }}" class="text-gray-700 hover:text-blue-600 font-medium px-3 py-2 rounded-md transition">Dashboard</a>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="text-gray-700 hover:text-blue-600 font-medium px-3 py-2 rounded-md transition">Login</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium shadow-md transition transform hover:scale-105">Register</a>
                            @endif
                        @endauth
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="relative pt-24 pb-16 sm:pt-32 sm:pb-24 overflow-hidden hero-pattern">
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col-reverse lg:flex-row items-center gap-12">
            <!-- Text Content -->
            <div class="lg:w-1/2 text-center lg:text-left z-10" data-aos="fade-right" data-aos-duration="1000">
                <div class="inline-block px-4 py-1.5 mb-6 rounded-full bg-blue-100 text-blue-700 font-semibold text-sm tracking-wide shadow-sm">
                    ✨ Kesehatan Masa Depan Buah Hati
                </div>
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-gray-900 leading-tight mb-6">
                    Lindungi Si Kecil dengan <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-cyan-500">Imunisasi Lengkap</span>
                </h1>
                <p class="text-lg sm:text-xl text-gray-600 mb-8 leading-relaxed max-w-2xl mx-auto lg:mx-0">
                    Sistem Manajemen Posyandu Digital untuk memantau jadwal vaksinasi, riwayat kesehatan, dan tumbuh kembang anak Anda dengan mudah dan akurat.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                    <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-8 py-3 border border-transparent text-base font-bold rounded-xl text-white bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-700 hover:to-blue-600 shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition duration-200">
                        Ayo Lanjutkan
                        <svg class="w-5 h-5 ml-2 -mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                    </a>
                    <a href="#features" class="inline-flex items-center justify-center px-8 py-3 border border-gray-200 text-base font-semibold rounded-xl text-gray-700 bg-white hover:bg-gray-50 hover:border-gray-300 shadow-sm transition duration-200">
                        Pelajari Lebih Lanjut
                    </a>
                </div>
                
                <!-- Stats -->
                <div class="mt-12 grid grid-cols-3 gap-6 border-t border-gray-200 pt-8" data-aos="fade-up" data-aos-delay="200">
                    <div>
                        <p class="text-3xl font-bold text-blue-600">100%</p>
                        <p class="text-sm text-gray-500 mt-1">Gratis</p>
                    </div>
                    <div>
                        <p class="text-3xl font-bold text-blue-600">500+</p>
                        <p class="text-sm text-gray-500 mt-1">Anak Terdaftar</p>
                    </div>
                    <div>
                        <p class="text-3xl font-bold text-blue-600">24/7</p>
                        <p class="text-sm text-gray-500 mt-1">Akses Data</p>
                    </div>
                </div>
            </div>

            <!-- Hero Image/Illustration -->
            <div class="lg:w-5/12 relative flex justify-center" data-aos="fade-left" data-aos-duration="1000">
                <div class="relative rounded-2xl overflow-hidden shadow-2xl border-4 border-white max-w-md">
                    <img src="{{ asset('images/hero-baby.png') }}" alt="Ibu dan Anak di Posyandu" class="w-full h-auto object-cover">
                </div>
                <!-- Floating Elements -->
                <div class="absolute -top-6 -right-6 w-24 h-24 bg-yellow-400 rounded-full blur-2xl opacity-40 animate-pulse"></div>
                <div class="absolute -bottom-10 -left-10 w-32 h-32 bg-blue-400 rounded-full blur-3xl opacity-30 animate-pulse" style="animation-delay: 1s;"></div>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div id="features" class="py-20 bg-white overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16" data-aos="fade-up">
                <h2 class="text-base text-blue-600 font-semibold tracking-wide uppercase">Fitur Unggulan</h2>
                <p class="mt-2 text-3xl leading-8 font-extrabold tracking-tight text-gray-900 sm:text-4xl">
                    Semua Kebutuhan Posyandu dalam Satu Aplikasi
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="bg-gray-50 rounded-2xl p-8 transition duration-300 hover:shadow-lg hover:-translate-y-1" data-aos="fade-up" data-aos-delay="100">
                    <div class="w-14 h-14 bg-blue-100 rounded-xl flex items-center justify-center text-blue-600 mb-6">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Jadwal Vaksinasi</h3>
                    <p class="text-gray-600">Pantau jadwal imunisasi anak Anda dengan kalender interaktif. Jangan sampai terlewat jadwal penting.</p>
                </div>
                <!-- Feature 2 -->
                <div class="bg-gray-50 rounded-2xl p-8 transition duration-300 hover:shadow-lg hover:-translate-y-1" data-aos="fade-up" data-aos-delay="200">
                    <div class="w-14 h-14 bg-green-100 rounded-xl flex items-center justify-center text-green-600 mb-6">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Pencatatan Digital</h3>
                    <p class="text-gray-600">Riwayat imunisasi tersimpan aman secara digital. Mudah diakses kapan saja dan dimana saja.</p>
                </div>
                <!-- Feature 3 -->
                <div class="bg-gray-50 rounded-2xl p-8 transition duration-300 hover:shadow-lg hover:-translate-y-1" data-aos="fade-up" data-aos-delay="300">
                    <div class="w-14 h-14 bg-purple-100 rounded-xl flex items-center justify-center text-purple-600 mb-6">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Notifikasi Pintar</h3>
                    <p class="text-gray-600">Dapatkan pengingat otomatis sebelum jadwal posyandu dimulai. Membantu Anda disiplin waktu.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Login Section -->
    <div id="cek-jadwal" class="py-16 bg-gradient-to-b from-gray-50 to-white overflow-hidden" x-data="quickLoginModal()">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-10" data-aos="fade-up">
                <h2 class="text-base text-blue-600 font-semibold tracking-wide uppercase">Cek Jadwal Imunisasi</h2>
                <p class="mt-2 text-3xl leading-8 font-extrabold tracking-tight text-gray-900 sm:text-4xl">
                    Lihat Jadwal Vaksinasi Anak Anda
                </p>
                <p class="mt-4 max-w-2xl text-xl text-gray-500 mx-auto">
                    Masukkan tanggal lahir bayi dan nama ibu untuk melihat jadwal imunisasi.
                </p>
            </div>

            <div class="max-w-md mx-auto" data-aos="fade-up" data-aos-delay="100">
                <div class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
                    <!-- Error Message -->
                    <div x-show="errorMessage" x-transition class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-red-700" x-text="errorMessage"></p>
                            </div>
                        </div>
                    </div>

                    <!-- Success Message -->
                    <div x-show="successMessage" x-transition class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-r">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-green-700" x-text="successMessage"></p>
                            </div>
                        </div>
                    </div>

                    <form @submit.prevent="submitForm" class="space-y-6">
                        <div>
                            <label for="date_birth" class="block text-sm font-medium text-gray-700 mb-2">
                                📅 Tanggal Lahir Bayi
                            </label>
                            <input type="date" x-model="dateBirth" id="date_birth" required
                                class="appearance-none block w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 transition">
                        </div>

                        <div>
                            <label for="mother_name" class="block text-sm font-medium text-gray-700 mb-2">
                                👩 Nama Ibu Kandung
                            </label>
                            <input type="text" x-model="motherName" id="mother_name" required minlength="2"
                                placeholder="Masukkan nama ibu (minimal 2 huruf)"
                                class="appearance-none block w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 transition">
                            <p class="mt-1 text-xs text-gray-500">Cukup masukkan sebagian nama untuk mencari</p>
                        </div>

                        <button type="submit" :disabled="loading"
                            class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-lg text-base font-bold text-white bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-700 hover:to-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transform hover:-translate-y-0.5 transition duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                            <span x-show="!loading">🔍 Cari Jadwal Vaksinasi</span>
                            <span x-show="loading" class="flex items-center">
                                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Mencari...
                            </span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function quickLoginModal() {
            return {
                dateBirth: '',
                motherName: '',
                loading: false,
                errorMessage: '',
                successMessage: '',

                async submitForm() {
                    this.loading = true;
                    this.errorMessage = '';
                    this.successMessage = '';

                    try {
                        const response = await fetch('{{ route("quick-login") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                date_birth: this.dateBirth,
                                mother_name: this.motherName
                            })
                        });

                        const data = await response.json();

                        if (data.success) {
                            this.successMessage = data.message || 'Data ditemukan! Mengalihkan...';
                            setTimeout(() => {
                                window.location.href = data.redirect || '/user/dashboard';
                            }, 1000);
                        } else {
                            this.errorMessage = data.message || 'Data tidak ditemukan.';
                        }
                    } catch (error) {
                        this.errorMessage = 'Terjadi kesalahan. Silahkan coba lagi.';
                    } finally {
                        this.loading = false;
                    }
                }
            }
        }
    </script>

    <!-- CTA Section -->
    <div class="bg-blue-600 py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center" data-aos="zoom-in">
            <h2 class="text-3xl font-extrabold text-white sm:text-4xl mb-6">Siap Memberikan yang Terbaik untuk Buah Hati?</h2>
            <p class="text-xl text-blue-100 max-w-2xl mx-auto mb-10">
                Bergabunglah dengan ribuan orang tua lainnya yang telah mempercayakan pencatatan kesehatan anaknya pada kami.
            </p>
            <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-10 py-4 border border-transparent text-lg font-bold rounded-xl text-blue-700 bg-white hover:bg-blue-50 shadow-lg transform hover:-translate-y-1 transition duration-200">
                Daftar Sekarang
            </a>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="mb-6 md:mb-0">
                    <span class="font-bold text-2xl tracking-tight">PosyanduCare</span>
                    <p class="text-gray-400 text-sm mt-2">© 2026 PosyanduCare. All rights reserved.</p>
                </div>
                <div class="flex space-x-6">
                    <a href="#" class="text-gray-400 hover:text-white transition">Tentang Kami</a>
                    <a href="#" class="text-gray-400 hover:text-white transition">Kebijakan Privasi</a>
                    <a href="#" class="text-gray-400 hover:text-white transition">Hubungi Kami</a>
                </div>
            </div>
        </div>
    </footer>

    <script>
        AOS.init();
    </script>
</body>
</html>
