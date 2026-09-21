@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'block w-full border-slate-200 focus:border-nara-500 focus:ring-nara-500 rounded-2xl shadow-none text-xs sm:text-sm p-3']) }}>
