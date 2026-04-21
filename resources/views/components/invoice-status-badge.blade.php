@props(['status'])

@php
    $classes = [
        'SOLDÉE'     => 'bg-green-100 text-green-800 border-green-200',
        'PARTIELLE'  => 'bg-orange-100 text-orange-800 border-orange-200',
        'IMPAYÉE'    => 'bg-gray-100 text-gray-800 border-gray-200',
        'EN_RETARD'  => 'bg-red-100 text-red-800 border-red-200 animate-pulse',
        'ANNULÉE'    => 'bg-red-50 text-red-400 border-red-100 line-through opacity-60',
    ];

    $currentClass = $classes[$status] ?? 'bg-blue-100 text-blue-800 border-blue-200';
@endphp

<span {{ $attributes->merge(['class' => "px-2.5 py-0.5 rounded-full text-xs font-medium border $currentClass inline-flex items-center"]) }}>
    @if($status === 'EN_RETARD')
        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
        </svg>
    @endif
    
    {{ $status }}
</span>