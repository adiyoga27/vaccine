@extends('layouts.admin')

@section('content')
<div class="mb-8">
    <h1 class="text-2xl font-bold text-gray-900">Data Peserta</h1>
    <p class="text-gray-500 mt-1">Daftar semua orang tua dan anak yang terdaftar.</p>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Anak</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Orang Tua</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tgl Lahir / Usia</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Alamat/Desa</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bergabung</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @foreach($users as $user)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4">
                    <div class="text-sm font-bold text-gray-900">{{ $user->patient->name ?? '-' }}</div>
                    <div class="text-xs text-gray-500">{{ $user->patient->gender == 'male' ? 'Laki-laki' : 'Perempuan' }}</div>
                </td>
                <td class="px-6 py-4">
                    <div class="text-sm text-gray-900">{{ $user->name }}</div>
                    <div class="text-xs text-gray-500">{{ $user->email }}</div>
                    <div class="text-xs text-gray-500">Ibu: {{ $user->patient->mother_name ?? '-' }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    @if($user->patient)
                    <div class="text-sm text-gray-900">{{ $user->patient->date_birth->format('d M Y') }}</div>
                    <div class="text-xs text-gray-500">{{ $user->patient->date_birth->age }} Tahun</div>
                    @else
                    <span class="text-gray-400">-</span>
                    @endif
                </td>
                <td class="px-6 py-4">
                    <div class="text-sm text-gray-900">{{ $user->patient->address ?? '-' }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ $user->created_at->format('d M Y') }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="px-6 py-4 border-t border-gray-100">
        {{ $users->links() }}
    </div>
</div>
@endsection
