{{--
    Dokumentasi file: View Blade.

    Menjelaskan tanggung jawab file resources/views/components/input-error.blade.php serta hubungan data atau UI-nya dengan bagian aplikasi lain.
--}}
@props(['messages'])

@if ($messages)
    <ul {{ $attributes->merge(['class' => 'text-xs font-medium text-rose-600 space-y-1']) }}>
        @foreach ((array) $messages as $message)
            <li>{{ $message }}</li>
        @endforeach
    </ul>
@endif
