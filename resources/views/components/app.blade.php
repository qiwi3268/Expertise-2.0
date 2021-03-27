<html>
<head>
   <title>@yield('title')</title>
   <x-js-transfer/>
   <script src="{{ mix('js/app.js') }}"></script>
   @stack('resources')
</head>
<body>


   <div class="admin-panel">
      <div class="admin-panel__header">Admin panel 🚀</div>
      <div class="admin-panel__body">Контент</div>
   </div>

   <div class="main-container">
      @yield('body')
   </div>
</body>
</html>
