@props(['active', 'compact' => false])

@if ($compact)
    @php
    $classes = ($active ?? false)
                ? 'inline-flex items-center px-1 pt-1 border-b-2 border-indigo-400 text-xs font-medium leading-5 text-gray-900 focus:outline-none focus:border-indigo-700 transition duration-150 ease-in-out'
                : 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-xs font-medium leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out';
    @endphp
@else
    @php
    $classes = ($active ?? false)
                ? 'inline-flex items-center px-2 pt-1 border-b-2 border-indigo-400 text-sm font-semibold leading-5 text-gray-900 focus:outline-none focus:border-indigo-700 transition duration-150 ease-in-out'
                : 'inline-flex items-center px-2 pt-1 border-b-2 border-transparent text-sm font-semibold leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out';
    @endphp
@endif

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
