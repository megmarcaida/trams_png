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

      var module_class = module_name.replace(/ /g,'')
      
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
                console.log(item.status)
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
                    Dashboard / Dock Dashboard
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
        <div class="col-xl-6 col-sm-4 mb-3">
          <div class="response"></div>
          <h3>Incoming</h3>
          <hr>
            <div class="table table-responsive">
              <table class="table table-bordered table-striped data-table-incoming" style="height: 170px;font-size:12px;">
                  <thead>
                      <tr>
                          <th style="width: 120px;">Delivery No.</th>
                          <th>Schedule</th>
                          <th>Supplier</th>
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

          <br><br>

          <h3>Outgoing</h3>
          <hr>
            <div class="table table-responsive">
              <table class="table table-bordered table-striped data-table-outgoing" style="height: 170px;font-size:12px;">
                  <thead>
                      <tr>
                          <th>Delivery No.</th>
                          <th>Schedule</th>
                          <th>Supplier</th>
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
        <div class="col-xl-6 col-sm-2 mb-3">
            <h3>Dock</h3>
            <hr>
            <input type="hidden" id="module_account" value="{{ $role_account }}">
             @foreach($docks as $dock)
                <div class="row {{ $dock['slug'] }}-dock">
                    <div class="col-xl-12">
                    <div class="text-center">
                        <h4><b><!-- {{ $dock['title'] }} --> </b></h4>
                    </div>
                    <div class="row">
                    @foreach($dock['details'] as $d)
                        <div class="col-xl-6 col-sm-6">
                        <div class="card data" style="font-size: 13px;padding: 10px;">
                            <table id="{{ str_replace(' ','',$d) }}" class="table-borderless {{ str_replace(' ','',$d) }}">
                                <tbody>
                                    <tr>
                                      <td style="width: 160px;">Delivery No:</td>
                                      <td><div class="delivery_id"></div></td>
                                    </tr>
                                    <tr>
                                      <td style="width: 160px;">Slotting Time:</td>
                                      <td><div class="slotting"></div></td>
                                    </tr>
                                    <tr>
                                      <td style="width: 160px;">Supplier:</td>
                                      <td><div class="supplier"></div></td>
                                    </tr>
                                    <tr>
                                      <td style="width: 160px;">Truck:</td>
                                      <td><div class="truck"></div></td>
                                    </tr>
                                    <tr>
                                      <td style="width: 160px;">Plate No:</td>
                                      <td><div class="plate_number"></div></td>
                                    </tr>
                                    <tr>
                                      <td style="width: 160px;">Container No:</td>
                                      <td><div class="container_number"></div></td>
                                    </tr>
                                </tbody>
                                
                            </table>
                        </div>
                        
                        <div class="text-center"><b><h3>{{ $d }}</h3></b></div>
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

                      </div>
                    @endforeach
                    </div>
                    </div>
                </div>
                @endforeach
                <br>
                
        </div>
    </div>



    <div class="modal fade" id="ajaxModelView" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="width:780px;">
            <div class="modal-header">
                <h4 class="modal-title" id="modelHeadingView">Delivery ID: <b class="view_delivery_id"></b></h4>

                <button type="button" class="close" data-dismiss="modal">&times;</button> 
            </div>
            <div class="modal-body">
                
               <div class="row">

                  <div class="col-md-6">
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
                        Container Number:
                      </div>
                      <div class="col-md-6">
                        <p class="view_container_no"></p>
                      </div>
                    </div>
                  </div>

                  <div class="col-md-6">
                    <div class="row">
                      <div class="col-md-12 material_list">
                      Material List:
                      <table class="table table-responsive view_material_list"></table>
                    </div>
                  </div>
                  </div>
                  
                </div>
                <br>
                <div class="form-group">
                  <div class="row">
                     <label for="name" class="col-xl-4 col-sm-12 control-label"><h5>Change Dock:</h5> </label>

                     <div class="col-xl-8 col-sm-12">
                        <select class="form-control btn-modules-dropdown" id="dropdown-dock">
                             <option value="">Please select Dock</option>
                             @foreach($dockData['data'] as $dock)
                               <option value='{{ $dock->id }}'>{{ $dock->dock_name }}</option>
                             @endforeach
                        </select>
                      </div>
                    </div>
                </div>
                <div class="row">
                  <div class="col-xl-4 col-sm-12 btn-dock-in">
                    <button id="btn-dock-in" class="btn btn-secondary btn-xs btn-block" type="button">Dock-In</button>
                  </div>
                  <div class="col-xl-4 col-sm-12 btn-dock-out">
                    <button id="btn-dock-out" class="btn btn-secondary btn-xs btn-block" type="button">Dock-Out</button>
                  </div>
                  <div class="col-xl-4 col-sm-12 btn-change-dock">
                    <button id="btn-change-dock" class="btn btn-secondary btn-xs btn-block" type="button">Change Dock</button>
                  </div>
                  <!-- <div class="col-xl-4 col-sm-12 btn-overtime">
                    <button id="btn-overtime" class="btn btn-secondary btn-xs btn-block" type="button">Overtime</button>
                  </div> -->
                  <div class="col-xl-4 col-sm-12 btn-close">  
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
            {"data": 'dock'},
            {"data": 'status'},
        ],
        "displayLength":25, 
        "bLengthChange": false, 
        "bFilter": false,   

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
            {"data": 'dock'},
            {"data": 'status'},
        ],
        "displayLength":25  ,
        "bLengthChange": false, 
        "bFilter": false, 

    });

    setInterval(function(){
        table_incoming.draw()
        table_outgoing.draw()
    },5000)



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
            console.log($(this).find("td:nth-child(1)").first().text())
             $('.view_delivery_id').empty()
            $('.view_slotting_time').empty()  
            $('.view_supplier_name').empty()
            $('.view_truck').empty()
            $('.view_container_no').empty()
            $('.view_material_list').empty()
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

    $('body').on( 'click', '.data', function () {
       
             $('.view_delivery_id').empty()
            $('.view_slotting_time').empty()  
            $('.view_supplier_name').empty()
            $('.view_truck').empty()
            $('.view_container_no').empty()
            $('.view_material_list').empty()
            // $('.view_delivery_id').html($(this).find("tr:nth-child(1) > td:nth-child(2)").first().text())  
            // $('.view_slotting_time').html($(this).find("tr:nth-child(2) > td:nth-child(2)").first().text())  
            // $('.view_supplier_name').html($(this).find("tr:nth-child(3) > td:nth-child(2)").first().text()) 
            // $('.view_truck').html($(this).find("tr:nth-child(4) > td:nth-child(2)").text()) 
            // $('.view_container_no').html($(this).find("tr:nth-child(5) > td:nth-child(2)").text())
            //$('.view_material_list').html($(this).find("td:nth-child(8)").html())    
            // $('.material_list').hide();

            $.ajax({
                async: false,
                url: "{{ url('getClickDockData') }}",
                type: "POST",
                global: false,
                data: {delivery_ticket_id:$(this).find("tr:nth-child(1) > td:nth-child(2)").first().text()},
                success: function (data) {
                  console.log(JSON.parse(data))
                  var data = JSON.parse(data)
                  $('.view_delivery_id').html(data.id)  
                  $('.view_slotting_time').html(data.slotting_time)  
                  $('.view_supplier_name').html(data.supplier_name) 
                  $('.view_truck').html(data.truck) 
                  $('.view_container_no').html(data.container_number)
                  $('.view_material_list').html(data.material_list)
                },
                error: function (data) {
                    console.log('Error:', data);
                }
            });   

            $('.data-table-incoming').DataTable().$('tr.selected').removeClass('selected');
            $('.data-table-outgoing').DataTable().$('tr.selected').removeClass('selected');
            $(this).addClass('selected');

            $(".btn-dock-out").show();
            $(".btn-dock-in").hide();
            $(".btn-change-dock").hide();
            //$(".btn-overtime").show();
            if($(".btn-close").hasClass('offset-8')){
              $(".btn-close").removeClass('offset-8')
            }
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

  $('body').on( 'click', '#btn-change-dock', function () {
        var dock_id = $("#dropdown-dock option:selected").val();
        var dock_name = $("#dropdown-dock option:selected").text();
        $.ajax({
            async: false,
            url: "{{ url('changeDock') }}",
            type: "POST",
            global: false,
            data: {delivery_ticket_id:$(".view_delivery_id").html(),dock_id:dock_id,dock_name:dock_name},
            success: function (data) {
              $("#ajaxModelView").modal('hide');
              $(".response").show();
              $(".response").html("<div class='alert alert-success'>" + JSON.parse(data).message + "</div>")
              setTimeout(function(){
                $('.response').fadeOut(1000);
              },2000)
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
            data: {delivery_ticket_id:$(".view_delivery_id").html(),status:8,process_status:'incoming',process_name:'dock-in'},
            success: function (data) {
              console.log(JSON.parse(data)) 
              if(JSON.parse(data).success){
                  $("#ajaxModelView").modal('hide');
                  $(".response").show();
                  $(".response").html("<div class='alert alert-success'>" + JSON.parse(data).success + "</div>")
                  setTimeout(function(){
                    $('.response').fadeOut(1000);
                  },2000)

              }
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
            data: {delivery_ticket_id:$(".view_delivery_id").html(),status:9,process_status:'incoming',process_name:'dock-out'},
            success: function (data) {
              if(JSON.parse(data).success){
                  $("#ajaxModelView").modal('hide');
                  $(".response").show();
                  $(".response").html(JSON.parse(data).success)
                  setTimeout(function(){
                    $('.response').fadeOut(1000);
                  },2000)

              }
            },
            error: function (data) {
                console.log('Error:', data);
            }
        });
    });


</script>
@endsection
