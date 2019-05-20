@extends('layouts.datatableapp')

@section('content')
<div class="container-fluid">

    <div class="alert alert-secondary">
        <div class="row">
            <div class="col-md-12">
                <div class="float-left">
                    Dashboard / Gate Dashboard
                </div>
                <div class="float-right">| 11:00 AM</div>
                <div class="float-right"> {{ $datenow }} &nbsp;</div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-12 col-sm-12 mb-3">
          <h3>Gate Dashboard</h3>
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

</div>

    
    
<script type="text/javascript">
  $(function () {
     
      $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
    });
    
    var table = $('.data-table').DataTable({
        "bPaginate": true,
        "bLengthChange": false,
        "bFilter": false,
        "bInfo": false,
        "bAutoWidth": false,
        "processing": true,
        "serverSide": true,
        "ajax":{
                 "url": "{{ url('parkingDashboard') }}",
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
                 "url": "{{ url('parkingDashboard') }}",
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
