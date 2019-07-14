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

    <!-- <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script> -->

  
   
   
  <script src="{{ asset('js/jsQR.js') }}"></script>

  <link href="https://fonts.googleapis.com/css?family=Ropa+Sans" rel="stylesheet">    <!-- Styles -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css" />

   <style>
    body {
      font-family: 'Ropa Sans', sans-serif;
      color: #333;
      margin: 0 auto;
      position: relative;
    }

    #githubLink {
      position: absolute;
      right: 0;
      top: 12px;
      color: #2D99FF;
    }

    h1 {
      margin: 10px 0;
      font-size: 40px;
    }

    #loadingMessage {
      text-align: center;
      padding: 40px;
      background-color: #eee;
    }

    #canvas {
      width: 100%;
    }

    #output {
      margin-top: 20px;
      background: #eee;
      padding: 10px;
      padding-bottom: 0;
    }

    #output div {
      padding-bottom: 10px;
      word-wrap: break-word;
    }

    #noQRFound {
      text-align: center;
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

</body>
</html>
