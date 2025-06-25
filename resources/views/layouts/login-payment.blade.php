<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>{{ config('app.name', 'Doctor Blue') }}</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
   <!-- CSRF Token -->
  <meta name="csrf-token" content="{{ csrf_token() }}">
  

  <!-- Bootstrap 3.3.7 -->
  <link href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{ asset('vendor/font-awesome/css/font-awesome.min.css') }}">
  <!-- Ionicons -->
  <link rel="stylesheet" href="{{ asset('vendor/Ionicons/css/ionicons.min.css') }}">

  @yield('header')

  <!-- Theme style -->
  <link rel="stylesheet" href="{{ asset('vendor/AdminLTE.min.css') }}">

  <link rel="stylesheet" href="{{ asset('vendor/skin-blue.css') }}">

  <!-- iCheck -->
  <link rel="stylesheet" href="{{ asset('vendor/iCheck/square/blue.css') }}">

  <!-- Google Font -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">

  
  @vite(['resources/sass/app.scss', 'resources/js/app.js'])
  <script>
        window.App = {};
  </script>
</head>
<body class="hold-transition login-payment-page">
<div id="app" class="login-payment-box">

  <div class="login-logo">
     <a href="/"><img src="/img/logo-white.png" alt="{{ config('app.name', 'Doctor Blue') }}"></a>
  </div>
  
   <flash message="{{ session()->get('flash_message') }}" type="{{ session()->get('flash_message_level') }}" ></flash>

  @yield('content')

</div>
<!-- /.login-box -->
<!-- jQuery 3 -->
<script src="{{ asset('vendor/jquery/dist/jquery.min.js') }}"></script>
<!-- Bootstrap 3.3.7 -->
<script src="{{ asset('vendor/bootstrap/js/bootstrap.min.js') }}"></script>
<!-- iCheck -->
<script src="{{ asset('vendor/iCheck/icheck.min.js') }}"></script>

<script>
  $(function () {
    // $('input').iCheck({
    //   checkboxClass: 'icheckbox_square-blue',
    //   radioClass: 'iradio_square-blue',
    //   increaseArea: '20%' /* optional */
    // });
  });
</script>

@stack('scripts')

</body>
</html>
