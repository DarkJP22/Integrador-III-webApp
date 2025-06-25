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
  <!-- AdminLTE Skins. Choose a skin from the css/skins
       folder instead of downloading all of them to reduce the load. -->
  <link rel="stylesheet" href="{{ asset('vendor/skin-blue.css') }}">

   <link href="{{ asset('vendor/magnific-popup/magnific-popup.css') }}" rel="stylesheet">
   <link href="{{ asset('vendor/slick/slick.css') }}" rel="stylesheet">

  <!-- Google Font -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">

  @vite(['resources/sass/app.scss', 'resources/js/app.js'])

  <script>
     window.App = {!! json_encode([
          'user' => Auth::user(),
          'signedIn' => Auth::check(),
          'fe' => Auth::user()->fe,
          'isBeautician' => Auth::user()->isBeautician()
      ]) !!};
  </script>
</head>
<body class="hold-transition skin-blue sidebar-mini">
<!-- Site wrapper -->
<div id="app" class="wrapper">

  @include('layouts.beauticians._header')



  @include('layouts.beauticians._sidebar')




  <div class="content-wrapper">
     @include('layouts.beauticians._information-app')

     @yield('content')
  </div>

  <flash message="{{ session()->get('flash_message') }}" type="{{ session()->get('flash_message_level') }}" ></flash>

  @include('layouts._footer')

  <contact-modal></contact-modal>
 
</div>
<!-- ./wrapper -->

<!-- jQuery 3 -->
 <script src="{{ asset('vendor/jquery/dist/jquery.min.js') }}"></script> 
<!-- Bootstrap 3.3.7 -->
<script src="{{ asset('vendor/bootstrap/js/bootstrap.min.js') }}"></script>
<!-- SlimScroll -->
<script src="{{ asset('vendor/jquery-slimscroll/jquery.slimscroll.min.js') }}"></script>
<!-- FastClick -->
<script src="{{ asset('vendor/fastclick/lib/fastclick.js') }}"></script>
<!-- AdminLTE App -->
<script src="{{ asset('vendor/adminlte.min.js') }}"></script>

<script src="{{ asset('vendor/magnific-popup/jquery.magnific-popup.min.js') }}"></script>
<script src="{{ asset('vendor/slick/slick.min.js') }}"></script>
<!-- AdminLTE for demo purposes -->


@stack('scripts')

</body>
</html>
