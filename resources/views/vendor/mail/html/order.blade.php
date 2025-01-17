@component('mail::layout')
    {{-- Header --}}
    @slot('header')
        @component('mail::header', ['url' => $url, 'image' => $image])
        @endcomponent
    @endslot
    {{-- End Header --}}

    <div style="display: block;margin:0 auto;text-align:center">
        <h3>{{ $welcome }}</h3>
    </div>

    <p style="line-height:1.5em;text-align:center;font-size:16px">{{ $caption }}</p>

    <p style="line-height:1.5em;text-align:center;font-size:16px">{{ $textPhone }}</p>

    <div style="display: block;margin:0 auto;text-align:center">
        @foreach($phones as $phone)
            {{ $phone }}
        @endforeach
    </div>

    <p style="line-height:1.5em;text-align:center;font-size:16px">{{ $textEmail }}</p>

    <p style="line-height:1.5em;text-align:center;font-size:16px">{{ $email }}</p>


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

