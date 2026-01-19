@extends('layouts.admin')

@section('content')
<div x-data="{ 
    showModal: false, 
    activeLog: null,
    formatJson(json) {
        try {
            const parsed = JSON.parse(json);
            return JSON.stringify(parsed, null, 2);
        } catch(e) {
            return json;
        }
    }
}">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Riwayat Notifikasi</h1>
        <p class="text-gray-500 mt-1">Daftar lengkap pesan WhatsApp yang telah diproses.</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <!-- Filters -->
        <div class="p-4 border-b border-gray-100 bg-gray-50 flex flex-col md:flex-row gap-4 items-center justify-between">
            <form action="{{ route('admin.notifications.history') }}" method="GET" class="flex flex-col md:flex-row gap-4 w-full md:w-auto">
                <select name="status" onchange="this.form.submit()" class="rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>Semua Status</option>
                    <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>Terkirim</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Gagal</option>
                </select>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nomor atau pesan..." class="rounded-lg border-gray-300 text-sm pl-10 focus:ring-blue-500 focus:border-blue-500 w-full md:w-64">
                    <svg class="w-4 h-4 text-gray-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700 transition">Cari</button>
            </form>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left font-medium text-gray-500">Tujuan</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500">Pesan</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500">Status</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500">Waktu</th>
                        <th class="px-6 py-3 text-right font-medium text-gray-500">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($logs as $log)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-medium text-gray-900">{{ $log->to }}</td>
                        <td class="px-6 py-4 text-gray-600 max-w-xs break-words truncate">{{ Str::limit($log->message, 50) }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 rounded-full text-xs font-bold
                                {{ $log->status == 'sent' ? 'bg-green-100 text-green-700' : 
                                   ($log->status == 'failed' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                                {{ ucfirst($log->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-gray-500 whitespace-nowrap">
                            {{ $log->created_at->format('d M Y H:i') }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <button @click="activeLog = {{ json_encode($log) }}; showModal = true" class="inline-flex items-center text-blue-600 hover:text-blue-900 bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-lg text-xs font-medium transition">
                                Detail
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                            Tidak ada log notifikasi yang ditemukan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $logs->links() }}
        </div>
    </div>

    <!-- Modal -->
    <div x-show="showModal" 
         style="display: none;"
         class="fixed inset-0 z-50 overflow-y-auto" 
         aria-labelledby="modal-title" role="dialog" aria-modal="true">
        
        <!-- Backdrop -->
        <div x-show="showModal" 
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" 
             @click="showModal = false"></div>

        <div class="flex items-center justify-center min-h-screen p-4 text-center sm:p-0">
            <!-- Modal Panel -->
            <div x-show="showModal" 
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="relative bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:max-w-2xl w-full">
                
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="flex justify-between items-start mb-4 border-b pb-3">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                            Detail Notifikasi
                        </h3>
                        <button @click="showModal = false" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                            <span class="sr-only">Close</span>
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    
                    <template x-if="activeLog">
                        <div class="space-y-4">
                             <!-- Info Grid -->
                            <div class="grid grid-cols-2 gap-4 bg-gray-50 p-3 rounded-lg text-sm">
                                <div>
                                    <span class="block text-gray-500 text-xs uppercase mb-1">Penerima</span>
                                    <span class="font-medium text-gray-900" x-text="activeLog.to"></span>
                                </div>
                                <div>
                                    <span class="block text-gray-500 text-xs uppercase mb-1">Waktu</span>
                                    <span class="font-medium text-gray-900" x-text="new Date(activeLog.created_at).toLocaleString('id-ID')"></span>
                                </div>
                                <div>
                                    <span class="block text-gray-500 text-xs uppercase mb-1">Status</span>
                                    <span :class="{
                                        'bg-green-100 text-green-700': activeLog.status === 'sent',
                                        'bg-red-100 text-red-700': activeLog.status === 'failed',
                                        'bg-yellow-100 text-yellow-700': activeLog.status !== 'sent' && activeLog.status !== 'failed'
                                    }" class="px-2 py-0.5 rounded text-xs font-bold" x-text="activeLog.status.toUpperCase()"></span>
                                </div>
                            </div>

                            <!-- Message Content -->
                            <div>
                                <h4 class="text-sm font-bold text-gray-700 mb-2 flex items-center">
                                    <svg class="w-4 h-4 mr-1.5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
                                    Isi Pesan
                                </h4>
                                <div class="bg-blue-50 rounded-lg p-3 text-gray-800 whitespace-pre-wrap font-sans text-sm border border-blue-100" x-text="activeLog.message"></div>
                            </div>

                            <!-- Payload -->
                            <div x-data="{ expanded: false }">
                                <h4 @click="expanded = !expanded" class="text-sm font-bold text-gray-700 mb-2 flex items-center cursor-pointer hover:text-blue-600 transition">
                                    <svg class="w-4 h-4 mr-1.5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path></svg>
                                    Data Respons API
                                    <svg class="w-4 h-4 ml-1 transform transition-transform" :class="expanded ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </h4>
                                <div x-show="expanded" class="bg-gray-900 rounded-lg p-3 overflow-x-auto">
                                    <pre class="text-xs text-green-400 font-mono" x-text="formatJson(activeLog.response)"></pre>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
                
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm" @click="showModal = false">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
