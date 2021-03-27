@extends('components.app')

@section('title', 'Вход в АИС')

@prepend('resources')
   <script src="{{ mix('js/login.js') }}"></script>
   <link rel="stylesheet" type="text/css" href="{{ mix('css/login.css') }}">
@endprepend

@section('body')
   <div class="form-login">
      <div class="form-login__title">Вход в АИС Экспертиза</div>

      <form id="loginForm" class="form-login__body" action="/login" method="POST">
         @csrf
         <div class="form-login__field field">
            <input id="email" class="form-login__input" required type="text" name="email" placeholder="Почта">
         </div>
         <div class="form-login__field field">
            <input id="password" class="form-login__input" required type="password" name="password" placeholder="Пароль">
         </div>

         <div class="form-login__remember">
            <x-forms.yes-radio-block
                title="Запомнить меня"
                name="remember"
                required="false"
            />
         </div>

         <div id="loginSubmit" class="form-login__button action-button">Войти</div>

      </form>

      <div id="loginError" class="form-login__error">
         @error('authenticate')
         {{ $message }}
         @enderror
      </div>
   </div>

   @include('components.modals.error')

@endsection
