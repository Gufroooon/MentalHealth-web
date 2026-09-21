<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-white border border-slate-200 rounded-2xl font-bold text-xs sm:text-sm text-slate-700 hover:bg-slate-50 active:bg-slate-100 shadow-xs focus:outline-none focus:ring-2 focus:ring-nara-500 focus:ring-offset-2 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
