@extends('components.app')

@section('title', 'Create Application')

@prepend('resources')
   <script src="{{ mix('js/application/create.js') }}"></script>
   <link rel="stylesheet" type="text/css" href="{{ mix('css/application/create.css') }}">
@endprepend


@section('body')
   <x-header/>

   <div class="content">
      @include('components.application.create.content')
   </div>

   @include('components.footer')
@endsection

