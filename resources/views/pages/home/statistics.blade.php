@extends('components.app')

@section('title', 'Статистика')

@prepend('resources')
   <scriptS src="{{ mix('js/navigation.js') }}"></scriptS>
   <link rel="stylesheet" type="text/css" href="{{ mix('css/navigation.css') }}">
@endprepend


@section('body')
   <x-header/>

   <div class="content">


   </div>

   @include('components.footer')
@endsection

