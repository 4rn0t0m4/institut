@props(['id' => 'confirm-modal'])

<div x-data="{ open: false }"
    x-show="open"
    x-on:open-modal-{{ $id }}.window="open = true"
    x-on:keydown.escape.window="open = false"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 z-999999 flex items-center justify-center bg-gray-900/50 p-4"
    style="display: none;">
    <div @click.away="open = false"
        class="w-full max-w-md rounded-xl bg-white p-6 shadow-theme-xl dark:bg-gray-900">
        {{ $slot }}
        <div class="mt-6 flex justify-end gap-3">
            <button @click="open = false"
                class="rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-800 dark:text-gray-300 dark:hover:bg-gray-800">
                Annuler
            </button>
            {{ $action ?? '' }}
        </div>
    </div>
</div>
