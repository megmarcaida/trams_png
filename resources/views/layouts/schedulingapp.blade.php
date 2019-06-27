<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'TRAMS') }}</title>
    <!-- Custom fonts for this template-->
    <link href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">

    <!-- Styles -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css" />
  
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/sb-admin.css') }}" rel="stylesheet">

    <link href="{{ asset('packages/core/main.css') }}" rel="stylesheet">
    <link href="{{ asset('packages/daygrid/main.css') }}" rel="stylesheet">
    <link href="{{ asset('packages/timegrid/main.css') }}" rel="stylesheet">

    <!-- <script src="https://fullcalendar.io/releases/core/4.0.2/main.min.js"></script> -->
    <script src="{{ asset('packages/core/new_main.min.js') }}"></script>


 <!--    <script src="https://fullcalendar.io/releases/interaction/4.0.2/main.min.js"></script> -->
    <script src="{{ asset('packages/interaction/new_main.min.js') }}"></script>


   <!--  <script src="https://fullcalendar.io/releases/daygrid/4.0.1/main.min.js"></script> -->
    <script src="{{ asset('packages/resource-daygrid/new_main.min.js') }}"></script>

<!--     <script src="https://fullcalendar.io/releases/timegrid/4.0.1/main.min.js"></script> -->
    <script src="{{ asset('packages/resource-timegrid/new_main.min.js') }}"></script>



    <script src="{{ asset('packages/resource-common/main.js') }}"></script>
    <script src="{{ asset('packages/resource-daygrid/main.js') }}"></script>
    <script src="{{ asset('packages/resource-timegrid/main.js') }}"></script>

<!--     <script src="https://fullcalendar.io/releases/list/4.1.0/main.min.js"></script> -->
    <script src="{{ asset('packages/list/main.min.js') }}"></script>

    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.11/jquery-ui.min.js"></script>
      <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>

    <script src="https://unpkg.com/popper.js@1.15.0/dist/umd/popper.min.js"></script>
    <script src="https://unpkg.com/tooltip.js/dist/umd/tooltip.min.js"></script>

<style>

  body {
    margin: 0;
    padding: 0;
    font-family: Arial, Helvetica Neue, Helvetica, sans-serif;
    font-size: 14px;
  }

  #calendar {
    margin: 50px auto;
  }

</style>
</head>
<body id="page-top">

  @include('layouts.navbar')

  <div id="wrapper">

    @include('layouts.sidebar')

    <div id="content-wrapper">

      @yield('content')

      
      @include('layouts.footer')
    </div>
    <!-- /.content-wrapper -->

  </div>
  <!-- /#wrapper -->

  <!-- Scroll to Top Button-->
  <a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
  </a>

  <script src="{{ asset('js/sb-admin.min.js') }}"></script>
  <script src="{{ asset('js/admin.js') }}"></script>
  <!-- Demo scripts for this page-->
<!--   <script src="js/demo/datatables-demo.js"></script>
  <script src="js/demo/chart-area-demo.js"></script> -->

</body>
</html>
