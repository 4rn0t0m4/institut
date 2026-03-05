@extends('admin.layouts.app')

@section('content')
    <x-admin.page-breadcrumb title="Avis clients" :breadcrumbs="['Avis' => null]" />

    {{-- Filtres --}}
    <div class="flex items-center gap-3 mb-5">
        <a href="{{ route('admin.reviews.index') }}"
           class="px-3 py-1.5 text-sm rounded-lg {{ !request('status') ? 'bg-brand-500 text-white' : 'text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-800' }}">
            Tous
        </a>
        <a href="{{ route('admin.reviews.index', ['status' => 'pending']) }}"
           class="px-3 py-1.5 text-sm rounded-lg flex items-center gap-1.5 {{ request('status') === 'pending' ? 'bg-brand-500 text-white' : 'text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-800' }}">
            En attente
            @if($pendingCount > 0)
                <span class="inline-flex items-center justify-center w-5 h-5 rounded-full text-xs font-bold {{ request('status') === 'pending' ? 'bg-white text-brand-500' : 'bg-amber-100 text-amber-700' }}">{{ $pendingCount }}</span>
            @endif
        </a>
        <a href="{{ route('admin.reviews.index', ['status' => 'approved']) }}"
           class="px-3 py-1.5 text-sm rounded-lg {{ request('status') === 'approved' ? 'bg-brand-500 text-white' : 'text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-800' }}">
            Approuves
        </a>
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-gray-800">
                        <th class="px-5 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400">Produit</th>
                        <th class="px-5 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400">Auteur</th>
                        <th class="px-5 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400">Avis</th>
                        <th class="px-5 py-3 text-center text-sm font-medium text-gray-500 dark:text-gray-400">Note</th>
                        <th class="px-5 py-3 text-center text-sm font-medium text-gray-500 dark:text-gray-400">Statut</th>
                        <th class="px-5 py-3 text-right text-sm font-medium text-gray-500 dark:text-gray-400">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($reviews as $review)
                        <tr class="border-b border-gray-100 dark:border-gray-800 last:border-0 {{ !$review->is_approved ? 'bg-amber-50/50 dark:bg-amber-900/10' : '' }}">
                            <td class="px-5 py-4">
                                <a href="{{ route('admin.products.edit', $review->product) }}" class="text-sm font-medium text-gray-800 hover:text-brand-500 dark:text-white/90">
                                    {{ Str::limit($review->product->name, 30) }}
                                </a>
                            </td>
                            <td class="px-5 py-4">
                                <div class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $review->author_name }}</div>
                                <div class="text-xs text-gray-500">{{ $review->author_email }}</div>
                                @if($review->is_verified_buyer)
                                    <span class="inline-flex items-center gap-1 text-xs mt-0.5 font-medium" style="color: #276e44;">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                        Acheteur verifie
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $review->title }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ Str::limit($review->body, 80) }}</p>
                                <p class="text-xs text-gray-400 mt-1">{{ $review->created_at->format('d/m/Y H:i') }}</p>
                            </td>
                            <td class="px-5 py-4 text-center">
                                <div class="flex items-center justify-center gap-0.5">
                                    @for($i = 1; $i <= 5; $i++)
                                        <svg class="w-4 h-4" fill="{{ $i <= $review->rating ? '#f59e0b' : '#e5e7eb' }}" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    @endfor
                                </div>
                            </td>
                            <td class="px-5 py-4 text-center">
                                @if($review->is_approved)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">Approuve</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400">En attente</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    @if(!$review->is_approved)
                                        <form method="POST" action="{{ route('admin.reviews.approve', $review) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="text-green-600 hover:text-green-800 dark:text-green-400" title="Approuver">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 13l4 4L19 7"/>
                                                </svg>
                                            </button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('admin.reviews.reject', $review) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="text-amber-600 hover:text-amber-800 dark:text-amber-400" title="Rejeter">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                                </svg>
                                            </button>
                                        </form>
                                    @endif
                                    <form method="POST" action="{{ route('admin.reviews.destroy', $review) }}"
                                          onsubmit="return confirm('Supprimer cet avis ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-gray-500 hover:text-error-500 dark:text-gray-400" title="Supprimer">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                Aucun avis.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($reviews->hasPages())
            <div class="px-5 py-4 border-t border-gray-200 dark:border-gray-800">
                {{ $reviews->links() }}
            </div>
        @endif
    </div>
@endsection
