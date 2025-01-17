@component('mail::layout')
    {{-- Header --}}
    @slot('header')
        @component('mail::header', ['url' => $url, 'image' => $image])
        @endcomponent
    @endslot
    {{-- End Header --}}

    @slot('subcopy')
        @component('mail::subcopy')
            <h3 style="text-align: center">{{ $welcome }}</h3>
        @endcomponent
    @endslot

    <div>{{ $caption }}</div>
    <div>{{ $textPhone }}</div>
    <div>
        @foreach($phones as $phone)
            {{ $phone }}
        @endforeach
    </div>

    <div>{{ $textEmail }}</div>
    <div>{{ $email }}</div>


    {{-- Subcopy --}}
    @slot('subcopy')
        @component('mail::subcopy')
            <p style="line-height:1.5em;text-align:center;font-size:16px">{{ $thanks }}</p>
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

