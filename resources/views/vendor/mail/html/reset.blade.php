@component('mail::layout')
    {{-- Header --}}
    @slot('header')
        @component('mail::header')
        @endcomponent
    @endslot
    {{--  End  --}}


    @component('mail::message', ['password' => $password])@endcomponent


    @slot('footer')
        @component('mail::footer')
        @endcomponent
    @endslot
@endcomponent
{{-- End  --}}

