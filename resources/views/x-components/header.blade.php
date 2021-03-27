<header class="header">
   <div class="header__logo">
      <img class="header__img" src="{{ asset('img/logo.png') }}" alt="">
      <div class="header__logo-title">
         <div class="header__text">Госэкспертиза</div>
         <div class="header__text">Челябинской области</div>
      </div>
   </div>
   <div class="header__actions">
      <a class="header__link" href="{{ route('navigation') }}">
         <i class="header__icon fas fa-home"></i>
         <span class="header__action-label">Домой</span>
      </a>
      <a class="header__link" href="{{ route('navigation') }}">
         <i class="header__icon fas fa-bars"></i>
         <span class="header__action-label">Меню</span>
      </a>
      <a class="header__link" href="{{ route('navigation') }}">
         <i class="header__icon fas fa-comment-alt"></i>
         <span class="header__action-label">Уведомления</span>
      </a>
      <a class="header__link" href="{{ route('navigation') }}">
         <i class="header__icon fas fa-question-circle"></i>
         <span class="header__action-label">Справка</span>
      </a>
   </div>

   <div class="header__user">{{ $fio }}</div>
   <a class="header__logout" href="{{ route('logout') }}">
      <i class="header__icon fas fa-sign-out-alt"></i>
   </a>
</header>
