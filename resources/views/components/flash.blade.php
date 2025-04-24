@props(['type' => 'success']) {{-- success | error | info | warning --}}

@php
    $styles = [
        'success' => 'bg-emerald-50 border-emerald-600 text-emerald-800',
        'error'   => 'bg-rose-50   border-rose-600   text-rose-800',
        'info'    => 'bg-sky-50    border-sky-600    text-sky-800',
        'warning' => 'bg-amber-50  border-amber-600  text-amber-800',
    ][$type];
@endphp

<div {{ $attributes->merge([
        'class' => "relative flex items-start gap-3 border-l-4 px-4 py-3 rounded-md shadow-sm $styles"
    ]) }}>
    {{-- Icon --}}
    <div class="pt-0.5">
        @switch($type)
            @case('error')
                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M18 10A8 8 0 11 2 10a8 8 0 0116 0zM9 4h2v6H9V4zm0 8h2v2H9v-2z" />
                </svg>
            @break

            @case('info')
                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9 9h2v6H9V9zm0-4h2v2H9V5zM10 0a10 10 0 100 20A10 10 0 0010 0z" />
                </svg>
            @break

            @case('warning')
                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9 2a1 1 0 012 0l7 13a1 1 0 01-.9 1.5H2.9a1 1 0 01-.9-1.5L9 2zm1 4v4m0 4h.01" />
                </svg>
            @break

            @default {{-- success --}}
                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M16.707 5.293a1 1 0 00-1.414 0L8 12.586 4.707 9.293a1
                             1 0 00-1.414 1.414l4 4a1 1 0 001.414 0l8-8a1
                             1 0 000-1.414z" />
                </svg>
        @endswitch
    </div>

    {{-- Message slot --}}
    <p class="text-sm font-medium">
        {{ $slot }}
    </p>

    {{-- Dismiss button (optional JS to hide) --}}
    <button type="button"
            onclick="this.parentElement.remove()"
            class="absolute right-2 top-2 text-xl leading-none hover:opacity-60">
        &times;
    </button>
</div>
