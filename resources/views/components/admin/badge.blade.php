@props(['status'])

@php
    $styles = [
        'pending' => 'bg-warning-50 text-warning-700 dark:bg-warning-500/10 dark:text-warning-400',
        'processing' => 'bg-brand-50 text-brand-700 dark:bg-brand-500/10 dark:text-brand-400',
        'completed' => 'bg-success-50 text-success-700 dark:bg-success-500/10 dark:text-success-400',
        'cancelled' => 'bg-error-50 text-error-700 dark:bg-error-500/10 dark:text-error-400',
        'published' => 'bg-success-50 text-success-700 dark:bg-success-500/10 dark:text-success-400',
        'draft' => 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400',
    ];

    $labels = [
        'pending' => 'En attente',
        'processing' => 'En cours',
        'completed' => 'Terminee',
        'cancelled' => 'Annulee',
        'published' => 'Publiee',
        'draft' => 'Brouillon',
    ];
@endphp

<span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $styles[$status] ?? $styles['pending'] }}">
    {{ $labels[$status] ?? $status }}
</span>
