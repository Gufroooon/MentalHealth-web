<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-rose-600 border border-transparent rounded-2xl font-bold text-xs sm:text-sm text-white hover:bg-rose-700 active:bg-rose-800 shadow-md shadow-rose-600/20 focus:outline-none focus:ring-2 focus:ring-rose-500 focus:ring-offset-2 transition ease-in-out duration-150 transform active:scale-98']) }}>
    {{ $slot }}
</button>
