@extends('layouts.datatableapp')

@section('content')

<div class="container-fluid">

  <!-- Breadcrumbs-->
  <ol class="breadcrumb">
    <li class="breadcrumb-item">
      <a href="#">Dashboard</a>
    </li>
    <li class="breadcrumb-item">Scheduler</li>
    <li class="breadcrumb-item active">Slotting Schedule</li>
  </ol>

  <div class="row">
    <div class="col-xl-12 col-sm-12 mb-3">
      <h1>Slotting Schedule</h1>
        <br>
        <div id="response"></div>
        <a class="btn btn-success" href="javascript:void(0)" id="createNewTruck"> Register Truck</a>
        <a class="btn btn-success" href="javascript:void(0)" id="createNewDriver"> Register Driver</a>
        <a class="btn btn-success" href="javascript:void(0)" id="createNewProduct"> Register Assistant</a>

    </div>
  </div>
</div>
<!-- /.container-fluid -->

   
@include('schedulers.slottingschedtrucks')
@include('schedulers.slottingscheddrivers')
@include('schedulers.slottingschedassistants')

@endsection
