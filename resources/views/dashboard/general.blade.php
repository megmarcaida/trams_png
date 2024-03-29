@extends('layouts.datatableapp')

@section('content')

<div class="container-fluid">

    <div class="alert alert-secondary">
        <div class="row">
            <div class="col-md-12">
                <div class="float-left">
                    Dashboard / Admin Dashboard
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

    <div style="display:none;" id="response"></div>
    <div class="row">
        <div class="col-xl-6 col-sm-12 mb-3">
          <h3>Incoming</h3>
            <div class="table table-responsive">
              <table class="table table-bordered table-striped data-table-incoming">
                  <thead>
                      <tr>
                          <th>Delivery No.</th>
                          <th>Schedule</th>
                          <th>Supplier Name</th>
                          <th>Truck</th>
                          <th>Plate No.</th>
                          <th>Dock</th>
                          <th>Status</th>
                      </tr>
                  </thead>
                  <tbody>
                  </tbody>
              </table>
            </div>
        </div>
        <div class="col-xl-6 col-sm-12 mb-3">
          <h3>Outgoing</h3>
             
            <div class="table table-responsive">
              <table class="table table-bordered table-striped data-table-outgoing">
                  <thead>
                      <tr>
                          <th>Delivery No.</th>
                          <th>Schedule</th>
                          <th>Supplier Name</th>
                          <th>Truck</th>
                          <th>Plate No.</th>
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
    <div class="row">
        <div class="col-xl-12 col-sm-12 mb-3">
          <h3>General Dashboard</h3>
             
            <div class="table table-responsive">
              <table class="table table-bordered table-striped data-table">
                  <thead>
                      <tr>
                          <th>Delivery Ticket No.</th>
                          <th>Slotting Schedule</th>
                          <th>Supplier Name</th>
                          <th>Truck</th>
                          <th>Plate Number</th>
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

<div class="modal fade" id="ajaxModelView" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="width:1000px;margin-left: -250px;">
            <div class="modal-header">
                <h4 class="modal-title" id="modelHeadingView">Delivery ID: <b class="view_delivery_id"></b></h4>

                <button type="button" class="close" data-dismiss="modal">&times;</button> 
            </div>
            <div class="modal-body">
                
               <div class="row">

                  <div class="col-md-4">
                    <div class="row">
                      <div class="col-md-6">
                        Delivery ID:
                      </div>
                      <div class="col-md-6">
                        <p class="view_delivery_id"></p>
                      </div>

                      <div class="col-md-6">
                        Slotting Time:
                      </div>
                      <div class="col-md-6">
                        <p class="view_slotting_time"></p>
                      </div>

                      <div class="col-md-6">
                        Supplier:
                      </div>
                      <div class="col-md-6">
                        <p class="view_supplier_name"></p>
                      </div>

                      <div class="col-md-6">
                        Truck:
                      </div>
                      <div class="col-md-6">
                        <p class="view_truck"></p>
                      </div>

                      <div class="col-md-6">
                        Container No.:
                      </div>
                      <div class="col-md-6">
                        <p class="view_container_no"></p>
                      </div>
                    </div>
                  </div>

                  <div class="col-md-4">
                    <div class="row">
                      <div class="col-md-12 material_list">
                        <h6>Material List</h6>
                        <table class="table table-responsive view_material_list"></table>
                        
                      </div>
                    </div>
                  </div>
                   <div class="col-md-4">
                    <div class="row">
                      <div class="col-md-12">
                        <div class="row">
                          <div class="col-md-12">
                            <h6>Timestamp</h6>
                          </div>
                          
                          <div class="col-md-4">
                            Gate In:
                          </div>
                          <div class="col-md-8">
                            <p class="view_gate_in"></p>
                          </div>

                          <div class="col-md-4">
                            Dock In:
                          </div>
                          <div class="col-md-8">
                            <p class="view_dock_in"></p>
                          </div>
                          <div class="col-md-4">
                            Dock Out:
                          </div>
                          <div class="col-md-8">
                            <p class="view_dock_out"></p>
                          </div>
                          <div class="col-md-4">
                            Gate Out:
                          </div>
                          <div class="col-md-8">
                            <p class="view_gate_out"></p>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  
                </div>
                <br>
                <div class="row">
                  <div class="col-xl-4 col-md-4 col-sm-12 btn-change-dock">
                  </div>
                  <div class="col-xl-4 col-md-4 col-sm-12 btn-dock-in">
                    <!-- <button id="btn-dock-in" class="btn btn-secondary btn-xs btn-block" type="button">Dock-In</button> -->
                  </div>
                  <div class="col-xl-4 col-md-4 col-sm-12 btn-dock-out">
                    <!-- <button id="btn-dock-out" class="btn btn-secondary btn-xs btn-block" type="button">Dock-Out</button> -->
                  </div>
                  <div class="col-xl-4 col-md-4 col-sm-12 btn-close">  
                    <button id="btn-close" class="btn btn-secondary btn-xs btn-block" type="button" data-dismiss="modal">Close</button>
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
    
    var table_incoming = $('.data-table-incoming').DataTable({
        "bPaginate": true,
        "bLengthChange": false,
        "bFilter": false,
        "bInfo": false,
        "bAutoWidth": false,
        "processing": true,
        "serverSide": true,
        "ajax":{
                 "url": "{{ url('securityDashboard') }}",
                 "dataType": "json",
                 "type": "POST",
                 "data":{ _token: "{{csrf_token()}}",process_status:"incoming"}
               },
        "columns": [
            { "data": "id" },
            {"data": 'slotting_time'},
            {"data": 'supplier_name'},
            {"data": 'truck'},
            {"data": 'plate_number'},
            {"data": 'dock'},
            {"data": 'status'},
        ],
        'columnDefs': [ {
        'targets': [0,1,2,3,4,5], // column index (start from 0)
        'orderable': false, // set orderable false for selected columns
        }],
        "createdRow": function( row, data, dataIndex){
            if( data.status ==  'For-Entry'){
                $(row).addClass('greenClass');
            }

            if( data.status ==  'For-Gate-Out'){
                $(row).addClass('blueClass');
            }

            if(data.status == 'In Process'){
              $(row).addClass('greenClass');
            }

            if(data.status == 'Completed'){
              $(row).addClass('blueClass');
            }
            if(data.status == 'Parking'){
              $(row).addClass('greenClass');
            }
            if(data.status == 'Delayed'){
              $(row).addClass('yellowClass');
            }
            if(data.status == 'Dock'){
              $(row).addClass('greenClass');
            }
            if(data.status == 'Overtime'){
              $(row).addClass('redClass');
            }
            if(data.status == 'Over Staying'){
              $(row).addClass('redClass');
            }
        }       

    });

    var table_outgoing = $('.data-table-outgoing').DataTable({
        "bPaginate": true,
        "bLengthChange": false,
        "bFilter": false,
        "bInfo": false,
        "bAutoWidth": false,
        "processing": true,
        "serverSide": true,
        "ajax":{
                 "url": "{{ url('securityDashboard') }}",
                 "dataType": "json",
                 "type": "POST",
                 "data":{ _token: "{{csrf_token()}}",process_status:"outgoing"}
               },
        "columns": [
            { "data": "id" },
            {"data": 'slotting_time'},
            {"data": 'supplier_name'},
            {"data": 'truck'},
            {"data": 'plate_number'},
            {"data": 'dock'},
            {"data": 'status'},
        ],
        'columnDefs': [ {
        'targets': [0,1,2,3,4,5], // column index (start from 0)
        'orderable': false, // set orderable false for selected columns
        }],
        "createdRow": function( row, data, dataIndex){
            if( data.status ==  'For-Entry'){
                $(row).addClass('greenClass');
            }

            if( data.status ==  'For-Gate-Out'){
                $(row).addClass('blueClass');
            }

            if(data.status == 'In Process'){
              $(row).addClass('greenClass');
            }

            if(data.status == 'Completed'){
              $(row).addClass('blueClass');
            }
            if(data.status == 'Parking'){
              $(row).addClass('greenClass');
            }
            if(data.status == 'Delayed'){
              $(row).addClass('yellowClass');
            }
            if(data.status == 'Dock'){
              $(row).addClass('greenClass');
            }
            if(data.status == 'Overtime'){
              $(row).addClass('redClass');
            }
            if(data.status == 'Over Staying'){
              $(row).addClass('redClass');
            }
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
            {"data": 'dock'},
            {"data": 'status'},
        ],
        "createdRow": function( row, data, dataIndex){
            if( data.status ==  'For-Entry'){
                $(row).addClass('greenClass');
            }

            if( data.status ==  'For-Gate-Out'){
                $(row).addClass('blueClass');
            }

            if(data.status == 'In Process'){
              $(row).addClass('greenClass');
            }

            if(data.status == 'Completed'){
              $(row).addClass('blueClass');
            }
            if(data.status == 'Parking'){
              $(row).addClass('greenClass');
            }
            if(data.status == 'Delayed'){
              $(row).addClass('yellowClass');
            }
            if(data.status == 'Dock'){
              $(row).addClass('greenClass');
            }
            if(data.status == 'Overtime'){
              $(row).addClass('redClass');
            }
            if(data.status == 'Over Staying'){
              $(row).addClass('redClass');
            }
        }  

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
        table_incoming.draw()
        table_outgoing.draw()
    },10000)


    $('.data-table-incoming tbody').on( 'click', 'tr', function () {
        // if ( $(this).hasClass('selected') ) {
        //     $(this).removeClass('selected');
        // }
        // else {
        //   console.log($(this).find("td:nth-child(1)"))
        //     $('.view_delivery_id').html($(this).find("td:nth-child(1)").first().text())  
        //     $('.view_slotting_time').html($(this).find("td:nth-child(2)").first().text())  
        //     $('.view_supplier_name').html($(this).find("td:nth-child(3)").first().text()) 
        //     $('.view_truck').html($(this).find("td:nth-child(4)").text()) 
        //     $('.view_container_no').html($(this).find("td:nth-child(5)").text())
        //     $('.view_material_list').html($(this).find("td:nth-child(8)").html())    


        //     $('.data-table-incoming').DataTable().$('tr.selected').removeClass('selected');
        //     $('.data-table-outgoing').DataTable().$('tr.selected').removeClass('selected');
        //     $(this).addClass('selected');


        //     $("#btn-gate-in").show();
        //     $("#btn-register").show();
        //     $("#btn-gate-out").hide();

        //     $('#ajaxModelView').modal({
        //         backdrop:'static',
        //         keyboard: false
        //     })
        // }
        if ( $(this).hasClass('selected') ) {
            $(this).removeClass('selected');
        }
        else {
            console.log($(this).find("td:nth-child(1)").first().text())
             $('.view_delivery_id').empty()
            $('.view_slotting_time').empty()  
            $('.view_supplier_name').empty()
            $('.view_truck').empty()
            $('.view_container_no').empty()
            $('.view_material_list').empty()
            $('.view_dock_in').empty()
            $('.view_dock_out').empty()
            $('.view_gate_in').empty()
            $('.view_gate_out').empty()
            $.ajax({
                async: false,
                url: "{{ url('getClickDockData') }}",
                type: "POST",
                global: false,
                data: {delivery_ticket_id:$(this).find("td:nth-child(1)").first().text()},
                success: function (data) {
                  console.log(JSON.parse(data))
                  var data = JSON.parse(data)
                  $('.view_delivery_id').html(data.id)  
                  $('.view_slotting_time').html(data.slotting_time)  
                  $('.view_supplier_name').html(data.supplier_name) 
                  $('.view_truck').html(data.truck) 
                  $('.view_container_no').html(data.container_number)
                  $('.view_material_list').html(data.material_list)
                  $('.view_gate_in').html(data.view_gate_in)
                  $('.view_dock_in').html(data.view_dock_in)
                  $('.view_dock_out').html(data.view_dock_out)
                  $('.view_gate_out').html(data.view_gate_out)
                },
                error: function (data) {
                    console.log('Error:', data);
                }
            });

            $('.data-table-incoming').DataTable().$('tr.selected').removeClass('selected');
            $('.data-table-outgoing').DataTable().$('tr.selected').removeClass('selected');
            $(this).addClass('selected');


            $(".btn-dock-in").show();
            //$(".btn-overtime").hide();
            $(".btn-change-dock").show();
            $(".btn-dock-out").hide();
            if($(".btn-close").hasClass('offset-8')){
              $(".btn-close").removeClass('offset-8')
            }

            $('#ajaxModelView').modal({
                backdrop:'static',
                keyboard: false
            })
        }
    });


     $('.data-table-outgoing tbody').on( 'click', 'tr', function () {
        // if ( $(this).hasClass('selected') ) {
        //     $(this).removeClass('selected');
        // }
        // else {
        //     $('.view_delivery_id').html($(this).find("td:nth-child(1)").first().text())  
        //     $('.view_slotting_time').html($(this).find("td:nth-child(2)").first().text())  
        //     $('.view_supplier_name').html($(this).find("td:nth-child(3)").first().text()) 
        //     $('.view_truck').html($(this).find("td:nth-child(4)").text()) 
        //     $('.view_container_no').html($(this).find("td:nth-child(5)").text())
        //     $('.view_material_list').html($(this).find("td:nth-child(8)").html())    


        //     $('.data-table-incoming').DataTable().$('tr.selected').removeClass('selected');
        //     $('.data-table-outgoing').DataTable().$('tr.selected').removeClass('selected');
        //     $(this).addClass('selected');

        //     $("#btn-gate-in").hide();
        //     $("#btn-gate-out").show();
        //     $("#btn-register").hide();

        //     $('#ajaxModelView').modal({
        //         backdrop:'static',
        //         keyboard: false
        //     })
        // }
        if ( $(this).hasClass('selected') ) {
            $(this).removeClass('selected');
        }
        else {

            $('.view_delivery_id').empty()
            $('.view_slotting_time').empty()  
            $('.view_supplier_name').empty()
            $('.view_truck').empty()
            $('.view_container_no').empty()
            $('.view_material_list').empty()
            $('.view_dock_in').empty()
            $('.view_dock_out').empty()
            $('.view_gate_in').empty()
            $('.view_gate_out').empty()
            $.ajax({
                async: false,
                url: "{{ url('getClickDockData') }}",
                type: "POST",
                global: false,
                data: {delivery_ticket_id:$(this).find("td:nth-child(1)").first().text()},
                success: function (data) {
                  console.log(JSON.parse(data))
                  var data = JSON.parse(data)
                  $('.view_delivery_id').html(data.id)  
                  $('.view_slotting_time').html(data.slotting_time)  
                  $('.view_supplier_name').html(data.supplier_name) 
                  $('.view_truck').html(data.truck) 
                  $('.view_container_no').html(data.container_number)
                  $('.view_material_list').html(data.material_list)
                   $('.view_gate_in').html(data.view_gate_in)
                  $('.view_dock_in').html(data.view_dock_in)
                  $('.view_dock_out').html(data.view_dock_out)
                  $('.view_gate_out').html(data.view_gate_out)
                },
                error: function (data) {
                    console.log('Error:', data);
                }
            });   


            $('.data-table-incoming').DataTable().$('tr.selected').removeClass('selected');
            $('.data-table-outgoing').DataTable().$('tr.selected').removeClass('selected');
            $(this).addClass('selected');

            $(".btn-dock-in").hide();
            $(".btn-dock-out").hide();
            $(".btn-change-dock").hide();
            //$(".btn-overtime").hide();
            $(".btn-close").addClass('offset-8')
            $('#ajaxModelView').modal({
                backdrop:'static',
                keyboard: false
            })
        }
    });

   $('body').on( 'click', '#btn-gate-in', function () {
            
        $.ajax({
            async: false,
            url: "{{ url('changeProcessStatus') }}",
            type: "POST",
            global: false,
            data: {delivery_ticket_id:$(".view_delivery_id").html(),status:10,process_status:'incoming',process_name:"gate-in"},
            success: function (data) {
              if(JSON.parse(data).success){
                $("#response").show();
                $("#response").html("<div class='alert alert-success'>" + JSON.parse(data).success + "</div>")
              }

              if(JSON.parse(data).error){
                $("#response").show();
                $("#response").html("<div class='alert alert-danger'>" + JSON.parse(data).error + "</div>")
              }
              $('#ajaxModelView').modal('hide');
              setTimeout(function(){
                $("#response").hide('slow');
              },5000)
            },
            error: function (data) {
                console.log('Error:', data);
            }
        });
    });

    $('body').on( 'click', '#btn-gate-out', function () {
        $("#response").empty();
        $.ajax({
            async: false,
            url: "{{ url('changeProcessStatus') }}",
            type: "POST",
            global: false,
            data: {delivery_ticket_id:$(".view_delivery_id").html(),status:11,process_status:'incoming',process_name:"gate-out"},
            success: function (data) {
              if(JSON.parse(data).success){
                $("#response").show();
                $("#response").html("<div class='alert alert-success'>" + JSON.parse(data).success + "</div>")
              }

              if(JSON.parse(data).error){
                $("#response").show();
                $("#response").html("<div class='alert alert-danger'>" + JSON.parse(data).error + "</div>")
              }
              $('#ajaxModelView').modal('hide');
              setTimeout(function(){
                $("#response").hide('slow');
              },5000)
            },
            error: function (data) {
                console.log('Error:', data);
            }
        });
    });

    $('body').on('click','#btn-register',function(){
      window.location='scheduler/slottingschedule'
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
        ],
        'columnDefs': [ {
        'targets': [0,1,2,3,4,5], // column index (start from 0)
        'orderable': false, // set orderable false for selected columns
        }]   

        });
    });
</script>
@endsection
