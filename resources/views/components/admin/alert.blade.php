@props(['type' => 'success', 'message'])

@php
    $colors = [
        'success' => 'border-success-500 bg-success-50 text-success-700 dark:bg-success-500/10 dark:text-success-400',
        'error' => 'border-error-500 bg-error-50 text-error-700 dark:bg-error-500/10 dark:text-error-400',
        'warning' => 'border-warning-500 bg-warning-50 text-warning-700 dark:bg-warning-500/10 dark:text-warning-400',
        'info' => 'border-brand-500 bg-brand-50 text-brand-700 dark:bg-brand-500/10 dark:text-brand-400',
    ];
@endphp

<div x-data="{ show: true }" x-show="show" x-transition
    class="mb-4 flex items-center justify-between rounded-lg border-l-4 p-4 {{ $colors[$type] ?? $colors['info'] }}">
    <p class="text-sm font-medium">{{ $message }}</p>
    <button @click="show = false" class="ml-4 opacity-70 hover:opacity-100">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
    </button>
</div>
