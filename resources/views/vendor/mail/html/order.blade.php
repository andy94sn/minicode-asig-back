@component('mail::layout')
    {{-- Header --}}
    @slot('header')
        @component('mail::header', ['url' => $url, 'image' => $image])
        @endcomponent
    @endslot
    {{-- End Header --}}

    @slot('subcopy')
    {{ $welcome }}
    @endslot

    @slot('subcopy')
    {{ $caption }}
    @endslot

    @slot('subcopy')
    {{ $textPhone }}
    @endslot

    @slot('subcopy')
        @foreach($phones as $phone)
            {{ $phone }}
        @endforeach
        {{ $textEmail }}
        {{ $email }}
    @endslot

    {{-- Subcopy --}}
    @slot('subcopy')
        @component('mail::subcopy')
            {{ $thanks }}
        @endcomponent
    @endslot
    {{-- Endcopy --}}

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

