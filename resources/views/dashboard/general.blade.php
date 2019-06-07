@extends('layouts.datatableapp')

@section('content')
<div class="container-fluid">

    <div class="alert alert-secondary">
        <div class="row">
            <div class="col-md-12">
                <div class="float-left">
                    Dashboard / General Dashboard
                </div>
            </div>
        </div>
    </div>

    <center>
      <div class="clock">
            {{ $datenow }} &nbsp;
            <div class="hours"></div><!--
         --><div class="minutes"></div><!--
          --><div class="seconds"></div><!--
          --><div class="twelvehr"></div>
      </div>
    </center>

    <div class="row">
        <div class="col-xl-12 col-sm-12 mb-3">
          <h3>General Dashboard</h3>
             <div id="response">
              @if(session()->has('import_message'))
                <div class="alert alert-success">
                    {{ session()->get('import_message') }}
                </div>
            @endif
            </div>
            <div class="table table-responsive">
              <table class="table table-bordered table-striped data-table">
                  <thead>
                      <tr>
                          <th>Delivery Ticket No.</th>
                          <th>Slotting Number</th>
                          <th>Supplier Name</th>
                          <th>Truck</th>
                          <th>Plate Number</th>
                          <th>Container Number</th>
                          <th>Dock</th>
                          <th>Status</th>
                      </tr>
                  </thead>
                  <tbody>
                  </tbody>
              </table>
            </div>
        </div>
    </div>


    <h3>Dock View</h3>
    <nav>
        <div class="nav nav-tabs" id="nav-tab" role="tablist">
            <a class="nav-item nav-link active" id="nav-femcare-tab" data-toggle="tab" href="#nav-femcare" role="tab" aria-controls="nav-home" aria-selected="true">Fem Care</a>
            <a class="nav-item nav-link" id="nav-liquids-tab" data-toggle="tab" href="#nav-liquids" role="tab" aria-controls="nav-profile" aria-selected="false">Liquids</a>
            <a class="nav-item nav-link" id="nav-pcc-tab" data-toggle="tab" href="#nav-pcc" role="tab" aria-controls="nav-contact" aria-selected="false">PCC</a>
            <a class="nav-item nav-link" id="nav-babycare-tab" data-toggle="tab" href="#nav-babycare" role="tab" aria-controls="nav-contact" aria-selected="false">Baby Care</a>
            <a class="nav-item nav-link" id="nav-laundry-tab" data-toggle="tab" href="#nav-laundry" role="tab" aria-controls="nav-laundry" aria-selected="false">Laundry</a>
        </div>
    </nav>
    <div class="tab-content" id="nav-tabContent">
        <div class="tab-pane fade show active" id="nav-femcare" role="tabpanel" aria-labelledby="nav-femcare-tab">
            <br>
            <div class="row">
                <div class="col-md-2">
                    <div class="nav flex-column nav-pills" role="tablist" aria-orientation="vertical">
                      <a class="nav-link table-click" data-table_name="fem-care-table" data-category="Fem Care" data-module="Fem Care" data-toggle="pill" href="#v-pills-fem-care" role="tab" aria-controls="v-pills-fem-care" aria-selected="true">Fem Care</a>
                    </div>
                </div>
                <div class="col-md-10">
                     <div class="table table-responsive">
                      <table class="table table-bordered table-striped docks-table fem-care-table">
                            <thead>
                                <tr>
                                    <th>Delivery Ticket No.</th>
                                    <th>Slotting Number</th>
                                    <th>Supplier Name</th>
                                    <th>Truck</th>
                                    <th>Plate Number</th>
                                    <th>Container Number</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                      </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane fade" id="nav-liquids" role="tabpanel" aria-labelledby="nav-liquids-tab">
            <br>
            <div class="row">
                <div class="col-md-2">
                    <div class="nav flex-column nav-pills" role="tablist" aria-orientation="vertical">
                      <a class="nav-link table-click" data-table_name="liquids-table" data-category="Liquids" data-module="Liquids" data-toggle="pill" href="#v-pills-liquids" role="tab" aria-controls="v-pills-liquids" aria-selected="true">Liquids</a>
                      <a class="nav-link table-click" data-table_name="liquids-table" data-category="Liquids" data-module="Liquids Out Canopy" data-toggle="pill" href="#v-pills-liquids-out-canopy" role="tab" aria-controls="v-pills-liquids-out-canopy" aria-selected="false">Liquids Out Canopy</a>
                    </div>
                </div>
                <div class="col-md-10">
                     <div class="table table-responsive">
                      <table class="table table-bordered table-striped docks-table liquids-table">
                            <thead>
                                <tr>
                                    <th>Delivery Ticket No.</th>
                                    <th>Slotting Number</th>
                                    <th>Supplier Name</th>
                                    <th>Truck</th>
                                    <th>Plate Number</th>
                                    <th>Container Number</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                      </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane fade" id="nav-pcc" role="tabpanel" aria-labelledby="nav-pcc-tab">
            <br>
            <div class="row">
                <div class="col-md-2">
                    <div class="nav flex-column nav-pills" role="tablist" aria-orientation="vertical">
                      <a class="nav-link table-click" data-table_name="pcc-table" data-category="PCC" data-module="PCC 1" data-toggle="pill" href="#v-pills-pcc1" role="tab" aria-controls="v-pills-pcc1" aria-selected="true">PCC 1</a>
                      <a class="nav-link table-click" data-table_name="pcc-table" data-category="PCC" data-module="PCC 2" data-toggle="pill" href="#v-pills-pcc2" role="tab" aria-controls="v-pills-pcc2" aria-selected="false">PCC 2</a>
                    </div>
                </div>
                <div class="col-md-10">
                     <div class="table table-responsive">
                      <table class="table table-bordered table-striped docks-table pcc-table">
                            <thead>
                                <tr>
                                    <th>Delivery Ticket No.</th>
                                    <th>Slotting Number</th>
                                    <th>Supplier Name</th>
                                    <th>Truck</th>
                                    <th>Plate Number</th>
                                    <th>Container Number</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                      </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane fade" id="nav-babycare" role="tabpanel" aria-labelledby="nav-babycare-tab">
            <br>
            <div class="row">
                <div class="col-md-2">
                    <div class="nav flex-column nav-pills" role="tablist" aria-orientation="vertical">
                      <a class="nav-link table-click" data-table_name="baby-care-table" data-category="Baby Care" data-module="Baby Care 1" data-toggle="pill" href="#v-pills-baby-care-1" role="tab" aria-controls="v-pills-baby-care-1" aria-selected="true">Baby Care 1</a>
                      <a class="nav-link table-click" data-table_name="baby-care-table" data-category="Baby Care" data-module="Baby Care 2" data-toggle="pill" href="#v-pills-baby-care-2" role="tab" aria-controls="v-pills-baby-care-2" aria-selected="false">Baby Care 2</a>
                      <a class="nav-link table-click" data-table_name="baby-care-table" data-category="Baby Care" data-module="Baby Care 3" data-toggle="pill" href="#v-pills-baby-care-3" role="tab" aria-controls="v-pills-baby-care-3" aria-selected="false">Baby Care 3</a>
                       <a class="nav-link table-click" data-table_name="baby-care-table" data-category="Baby Care" data-module="Baby Care Scrap" data-toggle="pill" href="#v-pills-baby-care-scrap" role="tab" aria-controls="v-pills-baby-care-scrap" aria-selected="false">Baby Care Scrap</a>
                    </div>
                </div>
                <div class="col-md-10">
                     <div class="table table-responsive">
                      <table class="table table-bordered table-striped docks-table baby-care-table">
                            <thead>
                                <tr>
                                    <th>Delivery Ticket No.</th>
                                    <th>Slotting Number</th>
                                    <th>Supplier Name</th>
                                    <th>Truck</th>
                                    <th>Plate Number</th>
                                    <th>Container Number</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                      </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane fade" id="nav-laundry" role="tabpanel" aria-labelledby="nav-laundry-tab">
            <br>
            <div class="row">
                <div class="col-md-2">
                    <div class="nav flex-column nav-pills" role="tablist" aria-orientation="vertical">
                      <a class="nav-link table-click" data-table_name="laundry-table" data-category="Laundry" data-module="Laundry" data-toggle="pill" href="#v-pills-laundry" role="tab" aria-controls="v-pills-laundry" aria-selected="true">Laundry</a>
                      <a class="nav-link table-click" data-table_name="laundry-table" data-category="Laundry" data-module="Laundry SB" data-toggle="pill" href="#v-pills-laundry-sb" role="tab" aria-controls="v-pills-laundry-sb" aria-selected="false">Laundry SB</a>
                      <a class="nav-link table-click" data-table_name="laundry-table" data-category="Laundry" data-module="Laundry Scrap" data-toggle="pill" href="#v-pills-laundry-scrap" role="tab" aria-controls="v-pills-laundry-scrap" aria-selected="false">Laundry Scrap</a>
                    </div>
                </div>
                <div class="col-md-10">
                     <div class="table table-responsive">
                      <table class="table table-bordered table-striped docks-table laundry-table">
                            <thead>
                                <tr>
                                    <th>Delivery Ticket No.</th>
                                    <th>Slotting Number</th>
                                    <th>Supplier Name</th>
                                    <th>Truck</th>
                                    <th>Plate Number</th>
                                    <th>Container Number</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                      </table>
                    </div>
                </div>
            </div>
        </div>
    </div>




</div>

    
    
<script type="text/javascript">
  $(function () {
     
      $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
    });
    
    var table = $('.data-table').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax":{
                 "url": "{{ url('allgeneraldashboardsched') }}",
                 "dataType": "json",
                 "type": "POST",
                 "data":{ _token: "{{csrf_token()}}"}
               },
        "columns": [
            { "data": "id" },
            {"data": 'slotting_time'},
            {"data": 'supplier_name'},
            {"data": 'truck'},
            {"data": 'plate_number'},
            {"data": 'container_number'},
            {"data": 'dock'},
            {"data": 'status'},
        ]  

    });



    function incomingTrucks(){
        $.ajax({
            async: false,
            url: "{{ url('checkIfIncoming') }}",
            type: "POST",
            global: false,
            data: {process_status:"incoming"},
            success: function (data) {
              console.log(data.length)
              if(data.length > 2){
                table.draw();
              }
            },
            error: function (data) {
                console.log('Error:', data);
            }
        });
    }

    setInterval(function(){
        incomingTrucks();
        table.draw()
    },10000)
     
  });

  $('.table-click').on('click',function(){
        var category_ = $(this).data('category');
        var module_ = $(this).data('module');
        var table_name_ = $(this).data('table_name')
        console.log(table_name_)


        var table = $('.docks-table').DataTable();
        table.destroy();

        var table_ = $('.'+table_name_).DataTable({
        "processing": true,
        "serverSide": true,
        "ajax":{
                 "url": "{{ url('allgeneraldashboardsched') }}",
                 "dataType": "json",
                 "type": "POST",
                 "data":{ _token: "{{csrf_token()}}",module: module_}
               },
        "columns": [
            { "data": "id" },
            {"data": 'slotting_time'},
            {"data": 'supplier_name'},
            {"data": 'truck'},
            {"data": 'plate_number'},
            {"data": 'container_number'},
        ]  

        });
    });
      
</script>
@endsection
