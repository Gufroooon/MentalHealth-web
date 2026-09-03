{{--
    Dokumentasi file: View Blade.

    Menjelaskan tanggung jawab file resources/views/components/text-input.blade.php serta hubungan data atau UI-nya dengan bagian aplikasi lain.
--}}
@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm']) }}>
