@props(['addon'])

<div class="space-y-1">
    <label class="text-sm font-medium text-gray-700">
        {{ $addon->label }}
        @if($addon->required) <span class="text-red-500">*</span> @endif
        @if($addon->price > 0)
            <span class="text-xs text-gray-400 font-normal">
                (+{{ number_format($addon->price, 2, ',', ' ') }}
                {{ $addon->price_type === 'percentage' ? '%' : '€' }})
            </span>
        @endif
    </label>

    @switch($addon->type)
        @case('text')
            <input type="text"
                   name="addons[{{ $addon->id }}][value]"
                   {{ $addon->required ? 'required' : '' }}
                   class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600">
            @break

        @case('textarea')
            <textarea name="addons[{{ $addon->id }}][value]"
                      rows="3"
                      {{ $addon->required ? 'required' : '' }}
                      class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600"></textarea>
            @break

        @case('select')
            <select name="addons[{{ $addon->id }}][value]"
                    {{ $addon->required ? 'required' : '' }}
                    class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600">
                <option value="">— Choisir —</option>
                @foreach($addon->options ?? [] as $option)
                    <option value="{{ $option['value'] ?? $option }}">{{ $option['label'] ?? $option }}</option>
                @endforeach
            </select>
            @break

        @case('radio')
            <div class="flex flex-wrap gap-3">
                @foreach($addon->options ?? [] as $option)
                    <label class="flex items-center gap-2 text-sm cursor-pointer">
                        <input type="radio"
                               name="addons[{{ $addon->id }}][value]"
                               value="{{ $option['value'] ?? $option }}"
                               {{ $addon->required ? 'required' : '' }}>
                        {{ $option['label'] ?? $option }}
                    </label>
                @endforeach
            </div>
            @break

        @case('checkbox')
            <div class="flex flex-wrap gap-3">
                @foreach($addon->options ?? [] as $option)
                    <label class="flex items-center gap-2 text-sm cursor-pointer">
                        <input type="checkbox"
                               name="addons[{{ $addon->id }}][]"
                               value="{{ $option['value'] ?? $option }}">
                        {{ $option['label'] ?? $option }}
                    </label>
                @endforeach
            </div>
            @break

        @case('file')
            <input type="file"
                   name="addons[{{ $addon->id }}][file]"
                   {{ $addon->required ? 'required' : '' }}
                   class="text-sm text-gray-600">
            @break

        @default
            <input type="text"
                   name="addons[{{ $addon->id }}][value]"
                   class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600">
    @endswitch

    {{-- Label transmis pour référence (prix calculé côté serveur) --}}
    <input type="hidden" name="addons[{{ $addon->id }}][label]" value="{{ $addon->label }}">
</div>
