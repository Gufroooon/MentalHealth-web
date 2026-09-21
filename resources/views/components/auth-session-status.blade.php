{{--
    Dokumentasi file: View Blade.

    Menjelaskan tanggung jawab file resources/views/components/auth-session-status.blade.php serta hubungan data atau UI-nya dengan bagian aplikasi lain.
--}}
@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'font-medium text-sm text-emerald-700 bg-emerald-50 border border-emerald-100 rounded-xl px-3 py-2']) }}>
        {{ $status }}
    </div>
@endif
