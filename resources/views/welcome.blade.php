<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Primary Meta Tags -->
    <title>TANDU GEMAS - Sistem Informasi Posyandu Digital & Imunisasi Anak</title>
    <meta name="title" content="TANDU GEMAS - Sistem Informasi Posyandu Digital & Imunisasi Anak">
    <meta name="description" content="TANDU GEMAS adalah aplikasi posyandu digital resmi dari UPT BLUD Puskesmas Kayangan untuk memantau jadwal imunisasi, riwayat vaksinasi, dan tumbuh kembang anak secara gratis dan akurat.">
    <meta name="keywords" content="posyandu, imunisasi, vaksin anak, jadwal imunisasi, kesehatan anak, stunting, posyandu digital, lombok utara, puskesmas kayangan, tandu gemas, imunisasi online">
    <meta name="author" content="UPT BLUD Puskesmas Kayangan">
    <meta name="robots" content="index, follow">
    <meta name="language" content="Indonesian">
    <meta name="geo.region" content="ID-NB">
    <meta name="geo.placename" content="Lombok Utara">

    <!-- Canonical URL -->
    <link rel="canonical" href="{{ url('/') }}">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/logo-tandu-gemas.png') }}">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url('/') }}">
    <meta property="og:title" content="TANDU GEMAS - Digital Posyandu untuk Generasi Keluarga Emas">
    <meta property="og:description" content="Pantau jadwal imunisasi dan kesehatan anak dengan mudah melalui TANDU GEMAS. Gratis, Akurat, dan Terpercaya.">
    <meta property="og:image" content="{{ asset('images/logo-tandu-gemas.png') }}">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ url('/') }}">
    <meta property="twitter:title" content="TANDU GEMAS - Digital Posyandu untuk Generasi Keluarga Emas">
    <meta property="twitter:description" content="Pantau jadwal imunisasi dan kesehatan anak dengan mudah melalui TANDU GEMAS. Gratis, Akurat, dan Terpercaya.">
    <meta property="twitter:image" content="{{ asset('images/logo-tandu-gemas.png') }}">

    <!-- Structured Data (JSON-LD) -->
    <!-- Structured Data (JSON-LD) -->
    <script type="application/ld+json">
    {
      "@@context": "https://schema.org",
      "@@type": "GovernmentService",
      "name": "TANDU GEMAS",
      "alternateName": "Digital Posyandu UPT BLUD Puskesmas Kayangan",
      "url": "{{ url('/') }}",
      "logo": "{{ asset('images/logo-tandu-gemas.png') }}",
      "description": "Layanan informasi digital untuk pemantauan imunisasi dan kesehatan anak di wilayah kerja Puskesmas Kayangan.",
      "provider": {
        "@@type": "GovernmentOrganization",
        "name": "UPT BLUD Puskesmas Kayangan",
        "address": {
          "@@type": "PostalAddress",
          "addressLocality": "Kayangan",
          "addressRegion": "Lombok Utara",
          "addressCountry": "ID"
        }
      },
      "areaServed": {
        "@@type": "AdministrativeArea",
        "name": "Lombok Utara"
      },
      "audience": {
        "@@type": "Audience",
        "audienceType": "Masyarakat Umum",
        "geographicArea": {
          "@@type": "AdministrativeArea",
          "name": "Kecamatan Kayangan"
        }
      }
    }
    </script>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Poppins', sans-serif; }
        .hero-pattern {
            background-color: #f3f4f6;
            background-image: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z' fill='%239C92AC' fill-opacity='0.1' fill-rule='evenodd'/%3E%3C/svg%3E");
        }
        .gold-gradient { background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 50%, #f59e0b 100%); }
        .text-gold { color: #f59e0b; }
        .acronym-letter { 
            display: inline-block;
            font-weight: 800;
            background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
    </style>
</head>
<body class="font-sans antialiased text-gray-800 bg-gray-50">

    <!-- Navigation -->
    <nav class="bg-white/80 backdrop-blur-md fixed w-full z-50 shadow-sm transition-all duration-300" x-data="{ atTop: true }" @scroll.window="atTop = (window.pageYOffset > 50) ? false : true" :class="{ 'shadow-md': !atTop }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex-shrink-0 flex items-center gap-2">
                    <img src="{{ asset('images/logo-tandu-gemas.png') }}" alt="TANDU GEMAS" class="w-10 h-10 rounded-full shadow-lg">
                    <div>
                        <span class="font-extrabold text-xl tracking-tight">
                            <span class="text-amber-500">TANDU</span><span class="text-emerald-600"> GEMAS</span>
                        </span>
                    </div>
                </div>
                <div class="flex space-x-4">
                    @if (Route::has('login'))
                        @auth
                            @if(Auth::user()->role === 'admin')
                                <a href="{{ route('admin.dashboard') }}" class="text-gray-700 hover:text-amber-600 font-medium px-3 py-2 rounded-md transition">Dashboard</a>
                            @else
                                <a href="{{ route('user.dashboard') }}" class="text-gray-700 hover:text-amber-600 font-medium px-3 py-2 rounded-md transition">Dashboard</a>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="text-gray-700 hover:text-amber-600 font-medium px-3 py-2 rounded-md transition">Login Admin</a>
                        @endauth
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="relative pt-24 pb-16 sm:pt-32 sm:pb-24 overflow-hidden hero-pattern" x-data="quickLoginModal()">
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col lg:flex-row items-center gap-12">
            <!-- Text Content -->
            <div class="lg:w-1/2 text-center lg:text-left z-10" data-aos="fade-right" data-aos-duration="1000">
                <!-- Creative Acronym Banner -->
                <div class="inline-block mb-6" data-aos="zoom-in">
                    <div class="bg-gradient-to-r from-amber-50 to-emerald-50 border border-amber-200 rounded-2xl px-6 py-4 shadow-lg">
                        <div class="flex flex-wrap justify-center lg:justify-start gap-x-1 text-sm sm:text-base">
                            <span>digi<span class="acronym-letter text-lg">TA</span>l</span>
                            <span>posy<span class="acronym-letter text-lg">ND</span>u</span>
                            <span><span class="acronym-letter text-lg">U</span>ntuk</span>
                            <span><span class="acronym-letter text-lg">G</span>enerasi</span>
                            <span>k<span class="acronym-letter text-lg">E</span>luarga</span>
                            <span>e<span class="acronym-letter text-lg">MAS</span></span>
                        </div>
                    </div>
                </div>

                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-gray-900 leading-tight mb-6">
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-amber-500 to-amber-600">TANDU</span> 
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-500 to-emerald-600">GEMAS</span>
                    <span class="block text-sm sm:text-base font-medium text-emerald-600 mt-3 mb-1 tracking-wide">By Endang Junaela, S.ST., Ns</span>
                    <span class="block text-2xl sm:text-3xl font-semibold text-gray-600">✨ Imunisasi Anak Sehat & Cerdas</span>
                </h1>
                <p class="text-lg sm:text-xl text-gray-600 mb-8 leading-relaxed max-w-2xl mx-auto lg:mx-0">
                    Sistem Digital Posyandu Modern untuk memantau jadwal vaksinasi, riwayat kesehatan, dan tumbuh kembang buah hati Anda dengan mudah, akurat, dan <span class="text-amber-600 font-semibold">100% GRATIS</span>!
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                    <a href="#features" class="inline-flex items-center justify-center px-8 py-4 border-2 border-emerald-500 text-base font-semibold rounded-xl text-emerald-700 bg-white hover:bg-emerald-50 shadow-sm transition duration-200">
                        📖 Pelajari Lebih Lanjut
                    </a>
                </div>
                
                <!-- Stats -->
                <div class="mt-12 grid grid-cols-3 gap-6 border-t border-gray-200 pt-8" data-aos="fade-up" data-aos-delay="200">
                    <div>
                        <p class="text-3xl font-bold text-amber-500">100%</p>
                        <p class="text-sm text-gray-500 mt-1">Gratis</p>
                    </div>
                    <div>
                        <p class="text-3xl font-bold text-emerald-500">500+</p>
                        <p class="text-sm text-gray-500 mt-1">Anak Terdaftar</p>
                    </div>
                    <div>
                        <p class="text-3xl font-bold text-amber-500">24/7</p>
                        <p class="text-sm text-gray-500 mt-1">Akses Data</p>
                    </div>
                </div>
            </div>

            <!-- Hero Form (Replaces Image) -->
            <div class="lg:w-1/2 w-full relative z-10" data-aos="fade-left" data-aos-duration="1000">
                 <div class="bg-white rounded-2xl shadow-2xl p-6 sm:p-8 border border-gray-100 relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-amber-100 rounded-bl-[4rem] -mr-4 -mt-4 z-0"></div>

                    <div class="relative z-10">
                        <h2 class="text-2xl font-bold text-gray-900 mb-2">Cek Jadwal Imunisasi</h2>
                        <p class="text-gray-500 mb-6">Masukkan data anak untuk melihat jadwal.</p>

                        <!-- Search Mode Switch -->
                        <div class="mb-6 flex space-x-4 bg-gray-50 p-1.5 rounded-xl">
                            <button @click="searchMode = 'nik'; errorMessage = ''; successMessage = ''" 
                                :class="{ 'bg-white shadow-sm text-blue-600 font-bold': searchMode === 'nik', 'text-gray-500 hover:text-gray-700': searchMode !== 'nik' }"
                                class="flex-1 py-2 px-4 rounded-lg text-sm font-medium transition duration-200 focus:outline-none">
                                🆔 Berdasarkan NIK
                            </button>
                            <button @click="searchMode = 'date'; errorMessage = ''; successMessage = ''"
                                :class="{ 'bg-white shadow-sm text-blue-600 font-bold': searchMode === 'date', 'text-gray-500 hover:text-gray-700': searchMode !== 'date' }"
                                class="flex-1 py-2 px-4 rounded-lg text-sm font-medium transition duration-200 focus:outline-none">
                                📅 Tanggal & Nama
                            </button>
                        </div>

                        <!-- Error Message -->
                        <div x-show="errorMessage" x-transition class="mb-4 bg-red-50 border-l-4 border-red-500 p-4 rounded-r">
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
                        <div x-show="successMessage" x-transition class="mb-4 bg-green-50 border-l-4 border-green-500 p-4 rounded-r">
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

                        <form @submit.prevent="submitForm" class="space-y-4">
                            <!-- NIK Input -->
                            <div x-show="searchMode === 'nik'" x-transition>
                                <label for="nik" class="block text-sm font-medium text-gray-700 mb-1">
                                    🆔 NIK (Nomor Induk Kependudukan)
                                </label>
                                <input type="text" x-model="nik" id="nik" :required="searchMode === 'nik'"
                                    placeholder="Masukkan 16 digit NIK" maxlength="16" pattern="[0-9]*" inputmode="numeric"
                                    class="appearance-none block w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 transition">
                            </div>

                            <!-- Date & Name Inputs -->
                            <div x-show="searchMode === 'date'" class="space-y-4" x-transition>
                                <div>
                                    <label for="date_birth" class="block text-sm font-medium text-gray-700 mb-1">
                                        📅 Tanggal Lahir Bayi
                                    </label>
                                    <input type="date" x-model="dateBirth" id="date_birth" :required="searchMode === 'date'"
                                        class="appearance-none block w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 transition">
                                </div>

                                <div>
                                    <label for="child_name" class="block text-sm font-medium text-gray-700 mb-1">
                                        👶 Nama Anak
                                    </label>
                                    <input type="text" x-model="childName" id="child_name" :required="searchMode === 'date'" minlength="2"
                                        placeholder="Masukkan nama anak"
                                        class="appearance-none block w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 transition">
                                </div>
                            </div>

                            <button type="submit" :disabled="loading"
                                class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-lg text-base font-bold text-white bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-700 hover:to-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transform hover:-translate-y-0.5 transition duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                                <span x-show="!loading">🔍 Cari Data</span>
                                <span x-show="loading" class="flex items-center">
                                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Mencari...
                                </span>
                            </button>
                        </form>

                        <!-- Patient Selection List -->
                        <div x-show="showPatientList" x-transition class="mt-4 border-t border-gray-100 pt-4">
                            <h4 class="text-sm font-semibold text-gray-700 mb-3">Pilih Data Anak:</h4>
                            <div class="space-y-2 max-h-48 overflow-y-auto">
                                <template x-for="patient in patients" :key="patient.id">
                                    <button @click="selectPatient(patient.id)" :disabled="confirming"
                                        class="w-full p-3 bg-gray-50 hover:bg-blue-50 rounded-lg border border-gray-200 hover:border-blue-300 text-left transition flex justify-between items-center disabled:opacity-50">
                                        <div>
                                            <p class="font-semibold text-gray-900 text-sm" x-text="patient.name"></p>
                                            <p class="text-xs text-gray-500">
                                                <span x-text="patient.date_birth"></span>
                                            </p>
                                        </div>
                                        <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </button>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Features Section (No changes needed) -->
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

    <script>
        function quickLoginModal() {
            return {
                searchMode: 'date',
                nik: '',
                dateBirth: '',
                childName: '',
                loading: false,
                confirming: false,
                errorMessage: '',
                successMessage: '',
                showPatientList: false,
                patients: [],

                async submitForm() {
                    this.loading = true;
                    this.errorMessage = '';
                    this.successMessage = '';
                    this.showPatientList = false;
                    this.patients = [];

                    try {
                        const response = await fetch('{{ route("quick-login") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                search_mode: this.searchMode,
                                nik: this.nik,
                                date_birth: this.dateBirth,
                                child_name: this.childName
                            })
                        });

                        const data = await response.json();

                        if (data.success && data.multiple) {
                            // Show patient selection list
                            this.patients = data.patients;
                            this.showPatientList = true;
                            this.successMessage = data.message;
                        } else if (data.success && data.redirect) {
                            // Direct redirect (e.g. NIK match)
                            this.successMessage = data.message;
                            setTimeout(() => {
                                window.location.href = data.redirect;
                            }, 1000);
                        } else if (!data.success) {
                            this.errorMessage = data.message || 'Data tidak ditemukan.';
                        }
                    } catch (error) {
                        this.errorMessage = 'Terjadi kesalahan. Silahkan coba lagi.';
                    } finally {
                        this.loading = false;
                    }
                },

                async selectPatient(patientId) {
                    this.confirming = true;
                    this.errorMessage = '';

                    try {
                        const response = await fetch('{{ route("confirm-quick-login") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                patient_id: patientId
                            })
                        });

                        const data = await response.json();

                        if (data.success) {
                            this.successMessage = data.message || 'Berhasil! Mengalihkan...';
                            this.showPatientList = false;
                            setTimeout(() => {
                                window.location.href = data.redirect || '/user/dashboard';
                            }, 1000);
                        } else {
                            this.errorMessage = data.message || 'Gagal login.';
                        }
                    } catch (error) {
                        this.errorMessage = 'Terjadi kesalahan. Silahkan coba lagi.';
                    } finally {
                        this.confirming = false;
                    }
                }
            }
        }
    </script>

    <!-- CTA Section -->
    <div class="bg-gradient-to-r from-amber-500 to-emerald-500 py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center" data-aos="zoom-in">
            <h2 class="text-3xl font-extrabold text-white sm:text-4xl mb-6">🌟 Generasi Keluarga Emas Dimulai dari Sini</h2>
            <p class="text-xl text-white/90 max-w-2xl mx-auto mb-10">
                Pantau kesehatan dan jadwal vaksinasi anak Anda dengan mudah melalui TANDU GEMAS.
            </p>
            <a href="#cek-jadwal" class="inline-flex items-center justify-center px-10 py-4 border border-transparent text-lg font-bold rounded-xl text-amber-600 bg-white hover:bg-gray-50 shadow-lg transform hover:-translate-y-1 transition duration-200">
                🔍 Cek Jadwal Sekarang
            </a>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="mb-6 md:mb-0 text-center md:text-left">
                    <span class="font-extrabold text-2xl tracking-tight">
                        <span class="text-amber-400">TANDU</span> <span class="text-emerald-400">GEMAS</span>
                    </span>
                    <p class="text-gray-400 text-sm mt-2">Digital Posyandu untuk Generasi Keluarga Emas</p>
                    <p class="text-gray-500 text-xs mt-1">© 2026 TANDU GEMAS. All rights reserved.</p>
                </div>
                <div class="flex space-x-6">
                    <a href="#" class="text-gray-400 hover:text-amber-400 transition">Tentang Kami</a>
                    <a href="#" class="text-gray-400 hover:text-amber-400 transition">Kebijakan Privasi</a>
                    <a href="#" class="text-gray-400 hover:text-amber-400 transition">Hubungi Kami</a>
                </div>
            </div>
        </div>
    </footer>

    <script>
        AOS.init();
    </script>
</body>
</html>
