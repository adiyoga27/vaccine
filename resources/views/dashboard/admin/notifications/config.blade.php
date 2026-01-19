@extends('layouts.admin')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Konfigurasi WhatsApp</h1>
    <p class="text-gray-500 mt-1">Kelola koneksi WAHA untuk mengirim notifikasi.</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6" x-data="wahaConfig">
    <!-- Connection Status -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 md:col-span-1">
        <h3 class="font-bold text-gray-700 mb-4 border-b pb-2">Status Koneksi</h3>
        
        <!-- Loading State -->
        <div x-show="loading" class="text-center py-8">
            <svg class="animate-spin h-8 w-8 text-blue-500 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <p class="mt-2 text-sm text-gray-500">Memuat status...</p>
        </div>

        <!-- Not Connected / QR -->
        <div x-show="!loading && status !== 'WORKING'" class="text-center">
             <div x-show="qrCode" class="mb-4">
                <img :src="qrCode" alt="Scan QR" class="mx-auto border p-2 rounded-lg w-48 h-48 object-contain">
                <p class="text-xs text-gray-500 mt-2">Scan QR Code dengan WhatsApp</p>
             </div>
             <div x-show="!qrCode" class="py-4">
                 <p class="text-sm text-red-500">Menunggu QR Code...</p>
                 <button @click="fetchStatus" class="mt-2 text-blue-600 hover:underline text-xs">Refresh</button>
             </div>
        </div>

        <!-- Connected -->
        <div x-show="!loading && status === 'WORKING'" class="text-center">
            <div class="w-20 h-20 rounded-full bg-green-100 mx-auto flex items-center justify-center text-green-600 mb-3">
                 <svg class="w-10 h-10" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
            </div>
            <h4 class="font-bold text-gray-900" x-text="me?.pushName || 'WhatsApp User'"></h4>
            <p class="text-sm text-gray-500 mb-4" x-text="me?.id ? '+' + me.id.split('@')[0] : ''"></p>
            
            <div class="inline-flex items-center px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs font-bold mb-4">
                <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                Terhubung
            </div>

            <form action="{{ route('admin.notifications.logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full bg-red-50 text-red-600 hover:bg-red-100 py-2 rounded-lg text-sm font-medium transition">
                    Logout
                </button>
            </form>
        </div>
    </div>

    <!-- Stats Overview -->
    <div class="md:col-span-2 space-y-6">
        <div class="grid grid-cols-3 gap-4">
            <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                <p class="text-xs text-gray-500 uppercase font-semibold">Terkirim</p>
                <h3 class="text-2xl font-bold text-gray-900 mt-2">{{ $stats['sent'] }}</h3>
            </div>
            <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                <p class="text-xs text-gray-500 uppercase font-semibold">Pending</p>
                <h3 class="text-2xl font-bold text-gray-900 mt-2">{{ $stats['pending'] }}</h3>
            </div>
            <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                <p class="text-xs text-gray-500 uppercase font-semibold">Gagal</p>
                <h3 class="text-2xl font-bold text-gray-900 mt-2">{{ $stats['failed'] }}</h3>
            </div>
        </div>
        
        <div class="bg-blue-50 border border-blue-100 rounded-xl p-6">
             <h3 class="font-bold text-blue-800 mb-2">Informasi API</h3>
             <ul class="list-disc list-inside text-sm text-blue-700 space-y-1">
                 <li>Sesi: <strong>adiyoga</strong></li>
                 <li>URL: <strong>https://waha.galkasoft.id</strong></li>
                 <li>Gunakan halaman 'Template' untuk mengatur pesan otomatis.</li>
             </ul>
        </div>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('wahaConfig', () => ({
            status: 'STARTING',
            qrCode: null,
            me: null,
            loading: true,
            pollInterval: null,

            init() {
                this.fetchStatus();
                this.pollInterval = setInterval(() => {
                    this.fetchStatus(false);
                }, 10000); // Poll every 10s
            },

            async fetchStatus(showLoading = true) {
                if (showLoading) this.loading = true;
                try {
                    const res = await fetch('{{ route("admin.notifications.status") }}');
                    const data = await res.json();
                    
                    if (data.status) {
                        this.status = data.status;
                        this.me = data.me;
                        if (this.status === 'SCAN_QR_CODE') {
                            await this.fetchQR();
                        } else {
                            this.qrCode = null;
                        }
                    } else {
                         this.status = 'STOPPED';
                    }
                } catch (e) {
                    console.error(e);
                } finally {
                    this.loading = false;
                }
            },

            async fetchQR() {
                try {
                    const res = await fetch('{{ route("admin.notifications.scan") }}');
                    const data = await res.json();
                    if(data.qr) {
                        this.qrCode = data.qr;
                    }
                } catch (e) {
                    console.error(e);
                }
            }
        }))
    })
</script>
@endsection
