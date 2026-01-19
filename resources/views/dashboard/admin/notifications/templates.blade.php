@extends('layouts.admin')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Template Pesan</h1>
    <p class="text-gray-500 mt-1">Kelola template pesan otomatis untuk notifikasi.</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    @foreach($templates as $template)
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden flex flex-col h-full">
        <div class="p-6 border-b border-gray-100 bg-gray-50">
            <h3 class="font-bold text-lg text-gray-800">{{ $template->name }}</h3>
            <span class="text-xs text-gray-500 font-mono mt-1 block">ID: {{ $template->slug }}</span>
        </div>
        
        <form action="{{ route('admin.notifications.templates.update', $template->id) }}" method="POST" class="flex-1 flex flex-col p-6">
            @csrf
            @method('PUT')
            
            <div class="mb-4 flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-2">Konten Pesan</label>
                <textarea name="content" rows="6" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 text-sm font-sans">{{ $template->content }}</textarea>
                <p class="text-xs text-gray-500 mt-2">
                    Variabel Pasien: <span class="font-mono text-blue-600">[name], [mother_name], [date_birth], [address], [gender], [phone], [village_name]</span><br>
                    Variabel Lain: <span class="font-mono text-blue-600">{{ $template->variables }}</span>
                </p>
            </div>
            
            <div class="flex justify-end">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700 transition">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
    @endforeach
</div>
@endsection
