@extends('layouts.schedulingapp')

@section('content')


@if(Auth::user()->role_id != 3)
  <script>window.location = "/";</script>
@endif

<script>

    document.addEventListener('DOMContentLoaded', function() {

    var count = 0;
    var testCalendar = function(module_name){


    var calendarEl = document.getElementById('calendar');


    var calendar = new FullCalendar.Calendar(calendarEl, {
      schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
      plugins: [ 'interaction', 'resourceDayGrid', 'resourceTimeGrid' ],
      defaultView: 'timeGridWeek',
      defaultDate: Date.now(),
      editable: true,
      selectable: true,
      eventLimit: true, // allow "more" link when too many events
      header: {
        left: 'CreateSchedule EditSchedule',
        center: 'title',
        right: 'today prev,next'
      },
      customButtons: {
        CreateSchedule: {
          text: 'Create Schedule',
          click: function() {
            $('#saveBtn').val("create-product");
            $('#schedule_id').val('');
            $('#scheduleForm').trigger("reset");
            $('#truck_id').html('');
            $('#driver_id').html('');
            $('#assistant_id').html('');
            $('.slot_box').removeClass('active_slot_box');
            $('#modelHeading').html("Register Schedule");

            $('div.occupied_slot_box').addClass("slot_box");
            $('div.slot_box').removeClass("occupied_slot_box");
            $('div.slot_box').removeClass("active_slot_box");
            $('#ajaxModel').modal({
              backdrop:'static',
              keyboard: false
            })
          }
        },
        EditSchedule: {
          text: 'Edit Schedule',
          click: function() {

          $('#response').html(''); 
          var id = $('#selected_schedule').val();
          var selected = $('#selected_supplierid').val();

          if(id == "" && selected == ""){
            $('#response').append('<div class="alert alert-warning">Please select schedule to edit.</div>  ')
          }
          $('#truck_id').html('');
          $('#driver_id').html('');
          $('#assistant_id').html('');

          $.ajax({
              url: "{{ url('getSupplierData') }}",
              type: "POST",
              data: {id:selected},
              success: function (data) {
                //console.log(JSON.parse(data))  
                $.each(JSON.parse(data), function(index, item) {
                  // $('#truck_id').append("<option>"+ item.plate_number+"</option>")
                  if(index == 'truckdata'){
                    $.each(item, function(index, truck) {

                      $('#truck_id').append("<option data-type="+truck.type+" value="+ truck.id +">"+ truck.plate_number+"</option>")
                    });
                  }

                  if(index == 'driverdata'){
                    $.each(item, function(index, driver) {
                      $('#driver_id').append("<option value="+ driver.id +">"+ driver.first_name + " " + driver.last_name+"</option>")
                    });
                  }

                  if(index == 'assistantdata'){
                    $.each(item, function(index, assistant) {
                      $('#assistant_id').append("<option value="+assistant.id +">"+ assistant.first_name + " " + assistant.last_name+"</option>")
                    });
                  }
                });

              },
              error: function (data) {
                  console.log('Error:', data);
              }
          });


          $('#scheduleForm').trigger("reset");      
          
          $.get("{{ route('ajaxschedules.index') }}" +'/' + id +'/edit', function (data) {
              $('#modelHeading').html("Edit Schedule");
              $('#saveBtn').val("edit-user");
              $('#ajaxModel').modal({
                backdrop:'static',
                keyboard: false
              })
              //console.log(data.id)
              $('#schedule_id').val(data.id);
              $('#po_number').val(data.po_number);
              $('#supplier_id').val(data.supplier_id);
             
              $('#dock_id').val(data.dock_id);

              $('#date_of_delivery').val(data.date_of_delivery);

              $('#truck_id').val(data.truck_id);

              $('#driver_id').val(data.driver_id);

               $('#assistant_id').val(data.assistant_id);

              $('#container_number').val(data.container_number);

              $("input[name=recurrence][value=" + data.recurrence + "]").prop('checked', 'checked');
              // $('#delivery_type').val(data.delivery_type);
          })

          // change the border color just for fun
          //info.el.style.borderColor = 'red';
          }
        }
      },
      views: {
        resourceTimeGridTwoDay: {
          type: 'resourceTimeGrid',
          duration: { days: 2 },
          buttonText: '2 days',
        }
      },


      //// uncomment this line to hide the all-day slot
      allDaySlot: false,

      //uncomment this for default setup
      resources: [
        { id: 'a', title: 'Room A' },
        { id: 'b', title: 'Room B', eventColor: 'green' },
        { id: 'c', title: 'Room C', eventColor: 'orange' },
        { id: 'd', title: 'Room D', eventColor: 'red' }
      ],
      // events: [
      //   { id: '1', resourceId: 'b', start: '2019-04-06', end: '2019-04-08', title: 'event 1' },
      //   { id: '2', resourceId: 'a', start: '2019-04-07T09:00:00', end: '2019-04-07T14:00:00', title: 'event 2' },
      //   { id: '3', resourceId: 'b', start: '2019-04-07T12:00:00', end: '2019-04-08T06:00:00', title: 'event 3' },
      //   { id: '4', resourceId: 'c', start: '2019-04-07T07:30:00', end: '2019-04-07T09:30:00', title: 'event 4' },
      //   { id: '5', resourceId: 'd', start: '2019-04-07T10:00:00', end: '2019-04-07T15:00:00', title: 'event 5' }
      // ],

       eventRender: function(event, element, view) {
          console.log(event)
       },

      events: {
        url: "{{ url('allschedules') }}",
        method: 'POST',
        extraParams: {
          _token: '{{csrf_token()}}',
          module: module_name
        },
        failure: function() {
          alert('there was an error while fetching events!');
        },
        color: '#1e9',   // a non-ajax option
        textColor: 'black' // a non-ajax option
      },
      select: function(arg) {
        alert("selected")
        console.log(
          'select',
          arg.startStr,
          arg.endStr,
          arg.resource ? arg.resource.id : '(no resource)'
        );
      },
      dateClick: function(arg) {
        alert("date click")
        console.log(
          'dateClick',
          arg.date,
          arg.resource ? arg.resource.id : '(no resource)'
        );
      },
       eventClick: function(info) {
        $('#selected_schedule').val(info.event.id);
        $('#selected_supplierid').val(info.event.extendedProps.supplier_id);
        console.log(info.event.extendedProps.recurrence)
        $('#response').html('');
        $('#response').append('<div class="alert alert-warning">Click Edit Schedule to update the selected schedule.<b>Your about to update '+ info.event.title+' </b></div>  ')
       }
    });



      var value = "";
      $('body').on('click', '.btn-modules', function () {
         count = count + 1;
         value = $(this).data("value");
         calendarEl.innerHTML = "";
         calendar.destroy();
         testCalendar(value)
          
      });


      
      calendar.render();

    
    }

    testCalendar("Null");
  });



</script>
<div class="container-fluid">

  <!-- Breadcrumbs-->
  <ol class="breadcrumb">
    <li class="breadcrumb-item">
      <a href="#">Dashboard</a>
    </li>
    <li class="breadcrumb-item active">Scheduler</li>
    <li class="breadcrumb-item">Slotting Schedule</li>
  </ol>
  <div class="row">
    <div class="col-xl-12 col-sm-12 mb-3">
      <h1>Schedule</h1>
        <div id="response"></div>
        <div class="row">
          <div class="col-xl-6 group-module">
            <input type="hidden" name="selected_schedule" id="selected_schedule">
            <input type="hidden" name="selected_supplierid" id="selected_supplierid">
          </div>
           <div class="col-xl-6 group-module">
            <div class="btn-group btn-group-sm float-right" role="group" aria-label="Basic example">
              <button data-value="Baby Care 1" type="button" class="btn btn-secondary btn-modules">Baby Care 1</button>
              <button data-value="Baby Care 2" type="button" class="btn btn-secondary btn-modules">Baby Care 2</button>
              <button data-value="Baby Care 3" type="button" class="btn btn-secondary btn-modules">Baby Care 3</button>
              <button data-value="Baby Care Scrap" type="button" class="btn btn-secondary btn-modules">Baby Care Scrap</button>
              <button data-value="Laundry" type="button" class="btn btn-secondary btn-modules">Laundry</button>
              <button data-value="Laundry SB" type="button" class="btn btn-secondary btn-modules">Laundry SB</button>
            </div>
            <div class="btn-group btn-group-sm float-right" role="group" aria-label="Basic example">
              <button data-value="Laundry Scrap" type="button" class="btn btn-secondary btn-modules">Laundry Scrap</button>
              <button data-value="PCC 1" type="button" class="btn btn-secondary btn-modules">PCC 1</button>
              <button data-value="PCC 2" type="button" class="btn btn-secondary btn-modules">PCC 2</button>
              <button data-value="Liquids" type="button" class="btn btn-secondary btn-modules">Liquids</button>
              <button data-value="Liquids Out Canopy" type="button" class="btn btn-secondary btn-modules">Liquids Out Canopy</button>
              <button data-value="Fem Care" type="button" class="btn btn-secondary btn-modules">Fem Care</button>
            </div>
          </div>
        </div>
        <div id='calendar'></div>

    </div>
  </div>
</div>
<!-- /.container-fluid -->
<div class="modal fade" id="ajaxModel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modelHeading"></h4>

                <button type="button" class="close" data-dismiss="modal">&times;</button> 
            </div>
            <div class="modal-body">
                <div id="modalresponse"></div> 
                <form id="scheduleForm" name="scheduleForm" class="form-horizontal">
                    
                    <input type="hidden" name="schedule_id" id="schedule_id">

                    <div class="form-group">
                        <label for="name" class="col-sm-12 control-label">*PO Number</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="po_number" name="po_number" placeholder="Enter PO Number" value="" maxlength="100" required="">
                        </div>
                    </div>

                    <div class="form-group">
                       <label for="name" class="col-sm-12 control-label">*Supplier</label>
                       <div class="col-sm-12">
                          <select required class="form-control" id="supplier_id" name="supplier_id">
                              <option value="0">Please select Supplier</option>
                             @foreach($json_data['supplierData']['data'] as $supplier)
                               <option value='{{ $supplier->id }}'>{{ $supplier->supplier_name }}</option>
                             @endforeach
                          </select>
                        </div>
                    </div>

                    <div class="form-group">
                       <label for="name" class="col-sm-12 control-label">*Dock</label>
                       <div class="col-sm-12">
                          <select  class="form-control" id="dock_id" name="dock_id">
                             <option value="0">Please select Dock</option>
                             @foreach($json_data['dockData']['data'] as $dock)
                               <option value='{{ $dock->id }}'>{{ $dock->dock_name }}</option>
                             @endforeach
                          </select>
                        </div>
                    </div>

                    <div class="form-group">
                      <label class="col-sm-12 control-label">*Date of Delivery</label>
                      <div class="col-sm-12">
                        <div class="col-sm-12">
                        <input type="date" class="form-control datepicker" name="dateOfDelivery" id="dateOfDelivery" required="">
                        </div>
                      </div>
                    </div>
                    
                    <div class="form-group">
                      <label class="col-sm-12 control-label recurrence">*Recurrence</label>
                      <div class="col-sm-12">
                        <div class="form-check form-check-inline">
                          <input class="form-check-input" type="radio" name="recurrence" class="recurrence" data-id="single" value="Single Event">
                          <label class="form-check-label" for="inlineRadio1">Single Event</label>
                        </div>
                        <div class="form-check form-check-inline">
                          <input class="form-check-input" type="radio" name="recurrence" class="recurrence" data-id="recurrent" value="Recurrent">
                          <label class="form-check-label" for="inlineRadio1">Recurrent</label>
                        </div>
                      </div>
                    </div>

                    <div class="form-group r_ordering_days">
                      <label class="col-sm-12 control-label ordering_days">*Every</label>
                      <div class="col-sm-12">
                        <div class="form-check form-check-inline">
                          <input class="form-check-input" type="checkbox" name="ordering_days[]" class="ordering_days" id="ordering_days_m" value="Mon">
                          <label class="form-check-label" for="inlineCheckbox1">Mon</label>
                        </div>
                        <div class="form-check form-check-inline">
                          <input class="form-check-input" type="checkbox" name="ordering_days[]" class="ordering_days" id="ordering_days_t" value="Tue">
                          <label class="form-check-label" for="inlineCheckbox2">Tue</label>
                        </div>
                        <div class="form-check form-check-inline">
                          <input class="form-check-input" type="checkbox" name="ordering_days[]" class="ordering_days" id="ordering_days_w" value="Wed">
                          <label class="form-check-label" for="inlineCheckbox2">Wed</label>
                        </div>
                        <div class="form-check form-check-inline">
                          <input class="form-check-input" type="checkbox" name="ordering_days[]" class="ordering_days" id="ordering_days_th" value="Thu">
                          <label class="form-check-label" for="inlineCheckbox2">Thu</label>
                        </div>
                        <div class="form-check form-check-inline">
                          <input class="form-check-input" type="checkbox" name="ordering_days[]" class="ordering_days" id="ordering_days_f" value="Fri">
                          <label class="form-check-label" for="inlineCheckbox2">Fri</label>
                        </div>
                        <div class="form-check form-check-inline">
                          <input class="form-check-input" type="checkbox" name="ordering_days[]" class="ordering_days" id="ordering_days_sat" value="Sat">
                          <label class="form-check-label" for="inlineCheckbox2">Sat</label>
                        </div>
                        <div class="form-check form-check-inline">
                          <input class="form-check-input" type="checkbox" name="ordering_days[]" class="ordering_days" id="ordering_days_sun" value="Sun">
                          <label class="form-check-label" for="inlineCheckbox2">Sun</label>
                        </div>
                      </div>
                    </div>

                    <div class="form-group">
                      <label class="col-sm-12 control-label slotting_time">*Slotting Time</label>
                      <input type="hidden" class="form-control" id="slotting_time" name="slotting_time" required="">
                      <div class="col-sm-12">
                          <div class="parent_slotting">
                            <div class="slotting_time">
                              <div class="slot_box" id="slot1">00:00 - 00:30</div>
                              <div class="slot_box" id="slot2">00:30 - 01:00</div>
                              <div class="slot_box" id="slot3">01:00 - 01:30</div>
                              <div class="slot_box" id="slot4">01:30 - 02:00</div>
                              <div class="slot_box" id="slot5">02:00 - 02:30</div>
                              <div class="slot_box" id="slot6">02:30 - 03:00</div>
                              <div class="slot_box" id="slot7">03:00 - 03:30</div>
                              <div class="slot_box" id="slot8">03:30 - 04:00</div>
                              <div class="slot_box" id="slot9">04:00 - 04:30</div>
                              <div class="slot_box" id="slot10">04:30 - 05:00</div>
                              <div class="slot_box" id="slot11">05:00 - 05:30</div>
                              <div class="slot_box" id="slot12">05:30 - 06:00</div>
                              <div class="slot_box" id="slot13">06:00 - 06:30</div>
                              <div class="slot_box" id="slot14">06:30 - 07:00</div>
                              <div class="slot_box" id="slot15">07:00 - 07:30</div>
                              <div class="slot_box" id="slot16">07:30 - 08:00</div>
                              <div class="slot_box" id="slot17">08:00 - 08:30</div>
                              <div class="slot_box" id="slot18">08:30 - 09:00</div>
                              <div class="slot_box" id="slot19">09:00 - 09:30</div>
                              <div class="slot_box" id="slot20">09:30 - 10:00</div>
                              <div class="slot_box" id="slot21">10:00 - 10:30</div>
                              <div class="slot_box" id="slot22">10:30 - 11:00</div>
                              <div class="slot_box" id="slot23">11:00 - 11:30</div>
                              <div class="slot_box" id="slot24">11:30 - 12:00</div>
                              <div class="slot_box" id="slot25">12:00 - 12:30</div>
                              <div class="slot_box" id="slot26">12:30 - 13:00</div>
                              <div class="slot_box" id="slot27">13:00 - 13:30</div>
                              <div class="slot_box" id="slot28">13:30 - 14:00</div>
                              <div class="slot_box" id="slot29">14:00 - 14:30</div>
                              <div class="slot_box" id="slot30">14:30 - 15:00</div>
                              <div class="slot_box" id="slot31">15:00 - 15:30</div>
                              <div class="slot_box" id="slot32">15:30 - 16:00</div>
                              <div class="slot_box" id="slot33">16:00 - 16:30</div>
                              <div class="slot_box" id="slot34">16:30 - 17:00</div>
                              <div class="slot_box" id="slot35">17:00 - 17:30</div>
                              <div class="slot_box" id="slot36">17:30 - 18:00</div>
                              <div class="slot_box" id="slot37">18:00 - 18:30</div>
                              <div class="slot_box" id="slot38">18:30 - 19:00</div>
                              <div class="slot_box" id="slot39">19:00 - 19:30</div>
                              <div class="slot_box" id="slot40">19:30 - 20:00</div>
                              <div class="slot_box" id="slot41">20:00 - 20:30</div>
                              <div class="slot_box" id="slot42">20:30 - 21:00</div>
                              <div class="slot_box" id="slot43">21:00 - 21:30</div>
                              <div class="slot_box" id="slot44">21:30 - 22:00</div>
                              <div class="slot_box" id="slot45">22:00 - 22:30</div>
                              <div class="slot_box" id="slot46">22:30 - 23:00</div>
                              <div class="slot_box" id="slot47">23:00 - 23:30</div>
                              <div class="slot_box" id="slot48">23:30 - 24:00</div>
                            </div>
                          </div>
                      </div>
                    </div>

                    <div class="form-group">
                       <label for="name" class="col-sm-12 control-label">*Truck</label>
                       <div class="col-sm-12">
                          <select  class="form-control" id="truck_id" name="truck_id">
                            <option value="0">Please select Truck</option>
                          </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="name" class="col-sm-12 control-label">*Container No.</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="container_number" name="container_number" placeholder="Enter Container Number" value="" maxlength="100" required="">
                        </div>
                    </div>

                    <div class="form-group">
                       <label for="name" class="col-sm-12 control-label">*Driver</label>
                       <div class="col-sm-12">
                          <select  class="form-control" id="driver_id" name="driver_id">
                            <option value="0">Please select Driver</option>
                          </select>
                        </div>
                    </div>

                    <div class="form-group">
                       <label for="name" class="col-sm-12 control-label">*Assistant</label>
                       <div class="col-sm-12">
                          <select  class="form-control" id="assistant_id" name="assistant_id">
                            <option value="0">Please select Assistant</option>
                          </select>
                        </div>
                    </div>


                    <div class="form-group">
                       <label for="name" class="col-sm-12 control-label">*Material List</label>
                       <div class="col-sm-12">
                          <table class="table table-responsive table-striped">
                            <tr>
                              <th>GCAS</th>
                              <th>Description</th>
                              <th>Quantity(UOM)</th>
                            </tr>
                            <tr>
                              <td>test</td>
                              <td>test</td>
                              <td>2</td>
                            </tr>
                            <tr>
                              <td>test</td>
                              <td>test</td>
                              <td>2</td>
                            </tr>
                          </table>
                        </div>
                    </div>

                    <div class="col-sm-offset-2 col-sm-10">
                       <button type="submit" class="btn btn-primary" id="saveBtn" value="create">Save changes
                       </button>
                    </div>

                </form>

            </div>
        </div>
    </div>
</div>
  
<script type="text/javascript">

    $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
    });
  
    $(':radio').change(function (event) {
        var id = $(this).data('id');
        if(id == "recurrent"){
          $(".r_ordering_days").css('display','block');
        }else{
          $(".r_ordering_days").css('display','none');
        }
    });

    $('body').on('click', '.slot_box', function () {
         
          var slotting_time = $('#slotting_time'); 
           var _this = $(this);
           value = $(this).html();
           if(_this.hasClass('active_slot_box')){
              $(this).removeClass('active_slot_box');
              var x = slotting_time.val().replace(value + "|","");
              slotting_time.val(x)
           }else{
              $(this).addClass('active_slot_box');
              slotting_time.val(slotting_time.val() + value + "|")
           }
         
          
    });

    // $('div.slot_box:contains("00:00 - 00:30")').css('background-color', 'red');
    // $('div.slot_box:contains("01:00 - 01:30")').css('background-color', 'red');
    // $(".occupied_slot_box").on('click',function(){
    //   return false;
    // })

     $('body').on('change', '#dateOfDelivery', function () {
        $('div.occupied_slot_box').addClass("slot_box");
        $('div.slot_box').removeClass("occupied_slot_box");
        $('div.slot_box').removeClass("active_slot_box");
        var date_of_delivery = $('#dateOfDelivery').val();
        $.ajax({
            url: "{{ url('getSlottingTime') }}",
            type: "POST",
            data: {date_of_delivery:date_of_delivery},
            success: function (data) {
                $.each(JSON.parse(data), function(index, item) {
                  $.each(item.slotting_time, function(i, slot) {
                  console.log(slot)
                    if(slot != ""){
                      $('div.slot_box:contains("'+slot+'")').addClass("occupied_slot_box");
                      $('div.slot_box:contains("'+slot+'")').removeClass("slot_box");
                    }
                  });
                });
            },
            error: function (data) {
                console.log('Error:', data);
            }
        });
      });



     $('body').on('change', '#supplier_id', function () {
         
         var selected = $("#supplier_id option:selected").val();
         //console.log(selected)
         $('#truck_id').html('');
         $('#driver_id').html('');
         $('#assistant_id').html('');

       if(selected != "0"){
        $.ajax({
              url: "{{ url('getSupplierData') }}",
              type: "POST",
              data: {id:selected},
              success: function (data) {
                //console.log(JSON.parse(data))  
                $.each(JSON.parse(data), function(index, item) {
                  // $('#truck_id').append("<option>"+ item.plate_number+"</option>")
                  if(index == 'truckdata'){
                    $.each(item, function(index, truck) {

                      $('#truck_id').append("<option data-type="+truck.type+" value="+ truck.id +">"+ truck.plate_number+"</option>")
                    });
                  }

                  if(index == 'driverdata'){
                    $.each(item, function(index, driver) {
                      $('#driver_id').append("<option value="+ driver.id +">"+ driver.first_name + " " + driver.last_name+"</option>")
                    });
                  }

                  if(index == 'assistantdata'){
                    $.each(item, function(index, assistant) {
                      $('#assistant_id').append("<option value="+assistant.id +">"+ assistant.first_name + " " + assistant.last_name+"</option>")
                    });
                  }
                });

              },
              error: function (data) {
                  console.log('Error:', data);
              }
          });
      }
          
    });

    $('body').on('click', '#saveBtn', function (e) {


        var ordering_days = $(':checkbox[name^=ordering_days]:checked').length;
        var recurrence = $(':radio[name^=recurrence]:checked');

        var trucktype = $("#truck_id > option:selected").data('type');
     
        if($("#po_number").val() == "" || $("#supplier_id").val() == "0" || $("#dock_id").val() ==  "" || $("#truck_id").val() == "" || $("#driver_id").val() == "" || $("#assistant_id").val() == "" || recurrence.length == 0 || $("#dateOfDelivery").val() == "" || $("#slotting_time").val() == ""){

          $(".modalresponse").html("<div class='alert alert-danger'>Please fill in the required fields.</div>")

          $('.modalresponse').fadeIn(1000);
          setTimeout(function(){
            $('.modalresponse').fadeOut(1000);
          },2000)

           if($("#po_number").val() == "")
             $("#po_number").css('outline','1px solid red')
           else
             $("#po_number").css('outline','1px solid transparent')

           if($("#supplier_id").val() == "0")
             $("#supplier_id").css('outline','1px solid red')
           else
             $("#supplier_id").css('outline','1px solid transparent')

           if($("#dock_id").val() == "0")
             $("#dock_id").css('outline','1px solid red')
           else
             $("#dock_id").css('outline','1px solid transparent')

           if($("#truck_id").val() == "0" || $("#truck_id").val() == "")
             $("#truck_id").css('outline','1px solid red')
           else
             $("#truck_id").css('outline','1px solid transparent')

            if($("#driver_id").val() == "0" || $("#driver_id").val() == "")
             $("#driver_id").css('outline','1px solid red')
            else
             $("#driver_id").css('outline','1px solid transparent')

            if($("#assistant_id").val() == "0" || $("#assistant_id").val() == "")
             $("#assistant_id").css('outline','1px solid red')
            else
             $("#assistant_id").css('outline','1px solid transparent')

           if($("#dateOfDelivery").val() == "")
             $("#dateOfDelivery").css('outline','1px solid red')
            else
             $("#dateOfDelivery").css('outline','1px solid transparent')


           if(trucktype == "Containerized"){

             if($("#container_number").val() == ""){

               $("#container_number").css('outline','1px solid red')
             }
              else{
               $("#container_number").css('outline','1px solid transparent')
              }
           }


           if($("#slotting_time").val() == "")
            $(".slotting_time").css('color','red')
           else
            $(".slotting_time").css('color','black')


            if(recurrence.val() == "Recurrent"){
             if(ordering_days == 0)
              $(".ordering_days").css('color','red')
             else
              $(".ordering_days").css('color','black')
            }

            if(recurrence.length == 0)
              $(".recurrence").css('color','red')
             else
              $(".recurrence").css('color','black')

            return false;
        }else{

            e.preventDefault();
            $(this).html('Sending..');
        
            console.log($('#scheduleForm').serialize())
            $.ajax({
              data: $('#scheduleForm').serialize(),
              url: "{{ route('ajaxschedules.store') }}",
              type: "POST",
              dataType: 'json',
              success: function (data) {
                 $('#response').html("<div class='alert alert-success'>"+data.success+"</div>")
                  $('#scheduleForm').trigger("reset");
                  $('#ajaxModel').modal('hide');
                  setTimeout(function(){
                    $('#response').hide("slow");
                  },2000)
                  table.draw();
                   $('#saveBtn').html('Save Changes');
              },
              error: function (data) {
                  console.log('Error:', data);
                  $('#saveBtn').html('Save Changes');
              }
            });
          }
    });
</script>

@endsection
