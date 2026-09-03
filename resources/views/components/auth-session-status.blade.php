{{--
    Dokumentasi file: View Blade.

    Menjelaskan tanggung jawab file resources/views/components/auth-session-status.blade.php serta hubungan data atau UI-nya dengan bagian aplikasi lain.
--}}
@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'font-medium text-sm text-green-600']) }}>
        {{ $status }}
    </div>
@endif
