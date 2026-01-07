@extends('layouts.admin')

@section('content')
<div class="mb-8">
    <h1 class="text-2xl font-bold text-gray-900">Log Aktivitas</h1>
    <p class="text-gray-500 mt-1">Pantau semua aktivitas perubahan data sistem.</p>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deskripsi</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subject</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Causer (User)</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @foreach($logs as $log)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $log->event == 'created' ? 'bg-green-100 text-green-800' : ($log->event == 'updated' ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800') }}">
                        {{ ucfirst($log->event) }}
                    </span>
                    <span class="ml-2 text-sm text-gray-900">{{ $log->description }}</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ class_basename($log->subject_type) }} #{{ $log->subject_id }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ $log->causer ? $log->causer->name : 'System' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ $log->created_at->diffForHumans() }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="px-6 py-4 border-t border-gray-100">
        {{ $logs->links() }}
    </div>
</div>
@endsection
