@props(['disabled' => false])

<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-nara-600 border border-transparent rounded-2xl font-bold text-xs sm:text-sm text-white hover:bg-nara-700 active:bg-nara-800 shadow-md shadow-nara-600/20 focus:outline-none focus:ring-2 focus:ring-nara-500 focus:ring-offset-2 transition ease-in-out duration-150 transform active:scale-98']) }}>
    {{ $slot }}
</button>
