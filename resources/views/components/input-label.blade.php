{{--
    Dokumentasi file: View Blade.

    Menjelaskan tanggung jawab file resources/views/components/input-label.blade.php serta hubungan data atau UI-nya dengan bagian aplikasi lain.
--}}
@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-medium text-sm text-gray-700']) }}>
    {{ $value ?? $slot }}
</label>
