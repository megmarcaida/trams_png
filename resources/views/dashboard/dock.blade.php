@extends('layouts.datatableapp')

@section('content')
<script type="text/javascript">
    var delivery_id = $(".delivery_id").html('')
    var slotting = $(".slotting").html('')
    var supplier = $(".supplier").html('')
    var truck = $(".truck").html('')
    var plate_number = $(".plate_number").html('')
    var container_number = $(".container_number").html('')

    $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
    });
   function first_dock_data(module_name) {
      var tmp_incoming = null;
      
      var module_class = module_name.toUpperCase().replace(/ /g,'')
      
      var del_id = $('#'+module_class+' > tbody > tr > td > div.delivery_id')
      var slotting = $('#'+module_class+' > tbody > tr > td > div.slotting')
      var supplier = $('#'+module_class+' > tbody > tr > td > div.supplier')
      var truck = $('#'+module_class+' > tbody > tr > td > div.truck')
      var plate_number = $('#'+module_class+' > tbody > tr > td > div.plate_number')
      var container_number = $('#'+module_class+' > tbody > tr > td > div.container_number')

      var moduleClass = $('.'+module_class)
      
      $.ajax({
          async: false,
          url: "{{ url('getFirstDockData') }}",
          type: "POST",
          global: false,
          data: {process_status:"incoming_dock_in",module_name:module_name,status:"9"},
          success: function (data) {
            if(JSON.parse(data).length != 0){
                $.each(JSON.parse(data), function(index, item) {
                //console.log(item.id)
                del_id.html(item.id)
                slotting.html(item.slotting_time)
                supplier.html(item.supplier_name)
                truck.html(item.truck)
                plate_number.html(item.plate_number)
                container_number.html(item.container_number)
                if(item.isDocked == 1){
                    moduleClass.addClass('alert alert-success')
                }
                // $('.'+module_ +' > .delivery_id').html(item.id)
                // $('.'+module_ +' > .slotting').html(item.slotting_time)
                // $('.'+module_ +' > .supplier').html(item.supplier_name)
                // $('.'+module_ +' > .truck').html(item.truck)
                // $('.'+module_ +' > .plate_number').html(item.plate_number)
                // $('.'+module_ +' > .container_number').html(item.container_number)

                });
            }
            
            tmp_incoming = data; 
          },
          error: function (data) {
              console.log('Error:', data);
          }
      });
      return tmp_incoming;
    };

    function new_first_dock(module_name) {
      var tmp_incoming = null;

      var module_class = module_name.toUpperCase().replace(/ /g,'')
      
      var del_id = $('#'+module_class+' > tbody > tr > td > div.delivery_id')
      var slotting = $('#'+module_class+' > tbody > tr > td > div.slotting')
      var supplier = $('#'+module_class+' > tbody > tr > td > div.supplier')
      var truck = $('#'+module_class+' > tbody > tr > td > div.truck')
      var plate_number = $('#'+module_class+' > tbody > tr > td > div.plate_number')
      var container_number = $('#'+module_class+' > tbody > tr > td > div.container_number')
      var moduleClass = $('.'+module_class)
      var timenow = ""
      var today = new Date()
      del_id.html('')
      slotting.html('')
      supplier.html('')
      truck.html('')
      plate_number.html('')
      container_number.html('')

      moduleClass.addClass('alert alert-secondary')
      moduleClass.removeClass('alert-success')
      moduleClass.removeClass('alert-danger')
      $.ajax({
          async: false,
          url: "{{ url('getFirstDockData') }}",
          type: "POST",
          global: false,
          data: {process_status:"incoming_dock_in",module_name:module_name,status:"9"},
          success: function (data) {
            $.each(JSON.parse(data), function(index, item) {
                del_id.html(item.id)
                slotting.html(item.slotting_time)
                supplier.html(item.supplier_name)
                truck.html(item.truck)
                plate_number.html(item.plate_number)
                container_number.html(item.container_number)

                timenow = today.getHours() + ":" + today.getMinutes() 
                //console.log(timenow)

                if(item.status == ""){
                    moduleClass.addClass('alert alert-secondary')
                    moduleClass.removeClass('alert-success')
                    moduleClass.removeClass('alert-danger')
                }else if(item.status == "Dock"){
                    moduleClass.addClass('alert alert-success')
                    moduleClass.removeClass('alert-secondary')
                    moduleClass.removeClass('alert-danger')
                }else if(item.status == "Overtime"){
                    moduleClass.addClass('alert alert-danger')
                    moduleClass.removeClass('alert-secondary')
                    moduleClass.removeClass('alert-success')
                }else{
                    moduleClass.addClass('alert alert-secondary')
                    moduleClass.removeClass('alert-success')
                    moduleClass.removeClass('alert-danger')
                }

                // if(timenow > item.endtime){
                //   moduleClass.addClass('alert alert-danger')
                // }
              
            });
            tmp_incoming = data; 
          },
          error: function (data) {
              console.log('Error:', data);
          }
      });
      return tmp_incoming;
  };
</script>
<div class="container-fluid">

    <div class="alert alert-secondary">
        <div class="row">
            <div class="col-md-12">
                <div class="float-left">
                    Dashboard / General Dashboard
                </div>
                <div class="float-right">| 11:00 AM</div>
                <div class="float-right"> {{ $datenow }} &nbsp;</div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-5 col-sm-4 mb-3">
          <h3>Incoming</h3>
          <hr>
            <div class="table table-responsive">
              <table class="table table-bordered table-striped data-table-incoming">
                  <thead>
                      <tr>
                          <th>Delivery Ticket No.</th>
                          <th>Slotting Time</th>
                          <th>Supplier</th>
                          <th>Truck</th>
                          <th>Plate Number</th>
                          <th>Container Number</th>
                          <th>Dock</th>
                          <th>Material List</th>
                      </tr>
                  </thead>
                  <tbody>
                  </tbody>
              </table>
            </div>
        </div>
        <div class="col-xl-2 col-sm-2 mb-3">
                    
            <h3>Dock</h3>
            <hr>
            <input type="hidden" id="module_account" value="{{ $role_account }}">
             @foreach($docks as $dock)
                <div class="row {{ $dock['slug'] }}-dock">
                    <div class="col-xl-12">
                    <div class="text-center">
                        <h4><b>Module: {{ $dock['title'] }} </b></h4>
                    </div>
                    @foreach($dock['details'] as $d)
                        
                        <div class="card data">
                            <table id="{{ str_replace(' ','',$d) }}" class="table table-bordered  {{ str_replace(' ','',$d) }}">
                                <tbody>
                                    <tr>
                                      <td>Delivery ID:</td>
                                      <td><div class="delivery_id"></div></td>
                                    </tr>
                                    <tr>
                                      <td>Slotting Time:</td>
                                      <td><div class="slotting"></div></td>
                                    </tr>
                                    <tr>
                                      <td>Supplier:</td>
                                      <td><div class="supplier"></div></td>
                                    </tr>
                                    <tr>
                                      <td>Truck:</td>
                                      <td><div class="truck"></div></td>
                                    </tr>
                                    <tr>
                                      <td>Plate Number:</td>
                                      <td><div class="plate_number"></div></td>
                                    </tr>
                                    <tr>
                                      <td>Container Number:</td>
                                      <td><div class="container_number"></div></td>
                                    </tr>
                                </tbody>
                                
                            </table>
                        </div>
                        
                        <div class="text-center">{{ $d }}</div>
                        <br>
                        <br>
                        <script type="text/javascript">
                          var first_data = first_dock_data("{{$d }}");
                          
                      setInterval(function(){

                        var new_first_dock_data = new_first_dock("{{$d }}")
                        //console.log(new_first_dock_data)
                        // //if(new_first_dock_data != first_data){
                        //   first_data = new_first_dock_data
                        // //}
                      },5000)
                        </script>
                    @endforeach
                    </div>
                </div>
                @endforeach
                <br>
                
        </div>
        <div class="col-xl-5 col-sm-5 mb-3">
          <h3>Outgoing</h3>
          <hr>
            <div class="table table-responsive">
              <table class="table table-bordered table-striped data-table-outgoing">
                  <thead>
                      <tr>
                          <th>Delivery Ticket No.</th>
                          <th>Slotting Time</th>
                          <th>Supplier</th>
                          <th>Truck</th>
                          <th>Plate Number</th>
                          <th>Container Number</th>
                          <th>Dock</th>
                          <th>Material List</th>
                      </tr>
                  </thead>
                  <tbody>
                  </tbody>
              </table>
            </div>
        </div>
    </div>



    <div class="modal fade" id="ajaxModelView" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modelHeadingView">Delivery ID: <b class="view_delivery_id"></b></h4>

                <button type="button" class="close" data-dismiss="modal">&times;</button> 
            </div>
            <div class="modal-body">
                
               <div class="row">
                  <div class="col-md-6">
                    Delivery ID:
                  </div>
                  <div class="col-md-6">
                    <p class="view_delivery_id"></p>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-6">
                    Slotting Time:
                  </div>
                  <div class="col-md-6">
                    <p class="view_slotting_time"></p>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-6">
                    Supplier:
                  </div>
                  <div class="col-md-6">
                    <p class="view_supplier_name"></p>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-6">
                    Truck:
                  </div>
                  <div class="col-md-6">
                    <p class="view_truck"></p>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-6">
                    Container Number:
                  </div>
                  <div class="col-md-6">
                    <p class="view_container_no"></p>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-12 material_list">
                    Material List:
                    <table class="view_material_list"></table>
                  </div>
                </div>
                <br>
                <div class="row">
                  <div class="col-xl-4 col-sm-12">
                    <button id="btn-dock-in" class="btn btn-secondary btn-xs btn-block" type="button">Dock-In</button>
                    <button id="btn-dock-out" class="btn btn-secondary btn-xs btn-block" type="button">Dock-Out</button>
                  </div>
                  <div class="col-xl-4 col-sm-12">
                    <button id="btn-change-dock" class="btn btn-secondary btn-xs btn-block" type="button">Change Dock</button>
                    <button id="btn-overtime" class="btn btn-secondary btn-xs btn-block" type="button">Overtime</button>
                  </div>
                  <div class="col-xl-4 col-sm-12">  
                    <button id="btn-close" class="btn btn-secondary btn-xs btn-block" type="button" data-dismiss="modal">Close</button>
                  </div> 
                </div>
            </div>
        </div>
    </div>
    </div>


</div>

  
    
<script type="text/javascript">
  $(function () {
     
    


    

    var module_account = $("#module_account").val()
    
    var table_incoming = $('.data-table-incoming').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax":{
                 "url": "{{ url('alldockdashboardsched') }}",
                 "dataType": "json",
                 "type": "POST",
                 "data":{ _token: "{{csrf_token()}}",process_status: "incoming" }
               },
        "columns": [
            { "data": "id" },
            {"data": 'slotting_time'},
            {"data": 'supplier_name'},
            {"data": 'truck'},
            {"data": 'plate_number'},
            {"data": 'container_number'},
            {"data": 'dock'},
            {"data": 'material_list'}
        ]  

    });

    var table_outgoing = $('.data-table-outgoing').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax":{
                 "url": "{{ url('alldockdashboardsched') }}",
                 "dataType": "json",
                 "type": "POST",
                 "data":{ _token: "{{csrf_token()}}",process_status: "outgoing" }
               },
        "columns": [
            { "data": "id" },
            {"data": 'slotting_time'},
            {"data": 'supplier_name'},
            {"data": 'truck'},
            {"data": 'plate_number'},
            {"data": 'container_number'},
            {"data": 'dock'},
            {"data": 'material_list'}
        ]  

    });



    var get_incoming_count = function () {
      var tmp_incoming = null;
      $.ajax({
          async: false,
          url: "{{ url('getCountDock') }}",
          type: "POST",
          global: false,
          data: {process_status:"incoming"},
          success: function (data) {
            tmp_incoming = data; 
          },
          error: function (data) {
              console.log('Error:', data);
          }
      });
      return tmp_incoming;
    }();


    var get_outgoing_count = function () {
      var tmp_incoming = null;
      $.ajax({
          async: false,
          url: "{{ url('getCountDock') }}",
          type: "POST",
          global: false,
          data: {process_status:"outgoing"},
          success: function (data) {
            tmp_incoming = data; 
          },
          error: function (data) {
              console.log('Error:', data);
          }
      });
      return tmp_incoming;
    }();




    var outgoingcount = get_outgoing_count
    var incomingcount = get_incoming_count
    var first_data = ""//first_dock_data


    //socket
    setInterval(function(){
      var newoutgoingcount = function () {
        var tmp_incoming = null;
        $.ajax({
            async: false,
            url: "{{ url('getCountDock') }}",
            type: "POST",
            global: false,
            data: {process_status:"outgoing"},
            success: function (data) {
              tmp_incoming = data; 
            },
            error: function (data) {
                console.log('Error:', data);
            }
        });
        return tmp_incoming;
      }();

      var newincomingcount = function () {
        var tmp_incoming = null;
        $.ajax({
            async: false,
            url: "{{ url('getCountDock') }}",
            type: "POST",
            global: false,
            data: {process_status:"incoming"},
            success: function (data) {
              tmp_incoming = data; 
            },
            error: function (data) {
                console.log('Error:', data);
            }
        });
        return tmp_incoming;
      }();

      
      
      if(newincomingcount != incomingcount){
        table_incoming.draw();
        incomingcount = newincomingcount
      }
      

      if(newoutgoingcount != outgoingcount){
        table_outgoing.draw();
        outgoingcount = newoutgoingcount
      }


    },5000)
    //socket

     $('.data-table-incoming tbody').on( 'click', 'tr', function () {
        if ( $(this).hasClass('selected') ) {
            $(this).removeClass('selected');
        }
        else {
          console.log($(this).find("td:nth-child(1)"))
            $('.view_delivery_id').html($(this).find("td:nth-child(1)").first().text())  
            $('.view_slotting_time').html($(this).find("td:nth-child(2)").first().text())  
            $('.view_supplier_name').html($(this).find("td:nth-child(3)").first().text()) 
            $('.view_truck').html($(this).find("td:nth-child(4)").text()) 
            $('.view_container_no').html($(this).find("td:nth-child(5)").text())
            $('.view_material_list').html($(this).find("td:nth-child(8)").html())    


            $('.data-table-incoming').DataTable().$('tr.selected').removeClass('selected');
            $('.data-table-outgoing').DataTable().$('tr.selected').removeClass('selected');
            $(this).addClass('selected');


            $("#btn-dock-in").show();
            $("#btn-overtime").show();
            $("#btn-dock-out").hide();

            $('#ajaxModelView').modal({
                backdrop:'static',
                keyboard: false
            })
        }
    });


     $('.data-table-outgoing tbody').on( 'click', 'tr', function () {
        if ( $(this).hasClass('selected') ) {
            $(this).removeClass('selected');
        }
        else {
            $('.view_delivery_id').html($(this).find("td:nth-child(1)").first().text())  
            $('.view_slotting_time').html($(this).find("td:nth-child(2)").first().text())  
            $('.view_supplier_name').html($(this).find("td:nth-child(3)").first().text()) 
            $('.view_truck').html($(this).find("td:nth-child(4)").text()) 
            $('.view_container_no').html($(this).find("td:nth-child(5)").text())
            $('.view_material_list').html($(this).find("td:nth-child(8)").html())    


            $('.data-table-incoming').DataTable().$('tr.selected').removeClass('selected');
            $('.data-table-outgoing').DataTable().$('tr.selected').removeClass('selected');
            $(this).addClass('selected');

            $("#btn-dock-out").hide();
            $("#btn-overtime").hide();

            $('#ajaxModelView').modal({
                backdrop:'static',
                keyboard: false
            })
        }
    });

    $('body').on( 'click', '.data', function () {
       
            $('.view_delivery_id').html($(this).find("tr:nth-child(1) > td:nth-child(2)").first().text())  
            $('.view_slotting_time').html($(this).find("tr:nth-child(2) > td:nth-child(2)").first().text())  
            $('.view_supplier_name').html($(this).find("tr:nth-child(3) > td:nth-child(2)").first().text()) 
            $('.view_truck').html($(this).find("tr:nth-child(4) > td:nth-child(2)").text()) 
            $('.view_container_no').html($(this).find("tr:nth-child(5) > td:nth-child(2)").text())
            //$('.view_material_list').html($(this).find("td:nth-child(8)").html())    
            $('.material_list').hide();

            $('.data-table-incoming').DataTable().$('tr.selected').removeClass('selected');
            $('.data-table-outgoing').DataTable().$('tr.selected').removeClass('selected');
            $(this).addClass('selected');

            $("#btn-dock-out").show();
            $("#btn-dock-in").hide();
            $("#btn-overtime").show();

            $('#ajaxModelView').modal({
                backdrop:'static',
                keyboard: false
            })
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

  $('body').on( 'click', '#btn-overtime', function () {
            
        $.ajax({
            async: false,
            url: "{{ url('setOvertime') }}",
            type: "POST",
            global: false,
            data: {delivery_id:$(".view_delivery_id").html()},
            success: function (data) {
              tmp_incoming = data; 
            },
            error: function (data) {
                console.log('Error:', data);
            }
        });
    });
    

    $('body').on( 'click', '#btn-dock-in', function () {
            
        $.ajax({
            async: false,
            url: "{{ url('changeProcessStatus') }}",
            type: "POST",
            global: false,
            data: {delivery_ticket_id:$(".view_delivery_id").html(),status:8,process_status:'incoming'},
            success: function (data) {
              console.log(JSON.parse(data).message) 
            },
            error: function (data) {
                console.log('Error:', data);
            }
        });
    });

    $('body').on( 'click', '#btn-dock-out', function () {
            
        $.ajax({
            async: false,
            url: "{{ url('changeProcessStatus') }}",
            type: "POST",
            global: false,
            data: {delivery_ticket_id:$(".view_delivery_id").html(),status:9,process_status:'incoming'},
            success: function (data) {
              console.log(JSON.parse(data).message) 
            },
            error: function (data) {
                console.log('Error:', data);
            }
        });
    });


</script>
@endsection
