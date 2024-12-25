@component('mail::layout')
    {{-- Header --}}
    @slot('header')
        @component('mail::header', ['url' => $url, 'image' => $image])
        @endcomponent
    @endslot
    {{-- End Header --}}

    {{ $welcome }}

    {{-- Body --}}
    {{ $slot }}
    {{-- End Body --}}

    {{-- Footer --}}
    @slot('footer')
        @component('mail::footer')
            {{ $footer }}
            <br>
            © {{ date('Y') }} {{ config('app.name') }}. @lang('All rights reserved.')
        @endcomponent
    @endslot
@endcomponent
{{-- End Footer --}}

