@extends('layouts.schedulingapp')

@section('content')
<script src="{{ asset('js/jquery.qrcode.js') }}"></script>
<script src="{{ asset('js/qrcode.js') }}" ></script>
<script type="text/javascript">
   //make qr code
   function makeCode(){
     $("#dataUrlQRCode").val('');
      jQuery('#qrcode').qrcode({width: 120,height:120,text: $("#view_delivery_id").html()});

     var canvas = document.querySelector("#qrcode > canvas");
     var dataURL = canvas.toDataURL();
     //$("#dataUrlQRCode").val(dataURL);
     $('#img_qrcode').attr("src",dataURL);
      $("#qrcode").html('')
   }
    
    //end make qr code
</script>
<style type="text/css">
  .fc-time-grid-event{
    cursor: pointer;
  }
</style>
<script>

    document.addEventListener('DOMContentLoaded', function() {

    $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
    });
  
    $('body').on('change','#recurrence', function (event) {
        var id = $("#recurrence").val();
        if(id == "Recurrent"){
          $(".r_ordering_days").css('display','block');
          $(".r_recurrent_dateend").css('display','block');
        }else{
          $(".r_ordering_days").css('display','none');
          $(".r_recurrent_dateend").css('display','none');
        }
    });

    $('body').on('change','#recurrence_unavailability', function (event) {
        var id = $("#recurrence_unavailability").val();
        if(id == "Recurrent"){
          $(".r_ordering_days_u").css('display','block');
          $(".r_recurrent_dateend").css('display','block');
        }else{
          $(".r_ordering_days_u").css('display','none');
          $(".r_recurrent_dateend").css('display','none');
        }
    });

    //Edit single event
    $('body').on('click', '#edit_single_event', function (e) {
        $('#ajaxModelEditRecurrent').modal('hide')
        $('#isEditingRecurrent').val("0")
        $('#isEditingSingle').val("1")
        //console.log($('#selected_schedule').val())  
        //console.log($('#recurrence_hidden').val())
        


          //$('div.slot_box').removeClass("slot_box");
          $('div.editable_slot_box').addClass('slot_box').removeClass("editable_slot_box");
          $('div.occupied_slot_box').addClass('slot_box').removeClass("occupied_slot_box");

          $('#saveBtn').html('Save Changes');
          $('#response').html(''); 

          var id = $('#selected_schedule').val();
          var selected = $('#selected_supplierid').val();

          if(id == "" && selected == ""){
            $('#response').append('<div class="alert alert-warning">Please select schedule to edit.</div>  ')
          }
          $('#truck_id').html('');
          $('#driver_id').html('');
          $('#assistant_id').html('');
          $('#dock_id').html('');
          $('#alt_supplier_id').val('');
          //$('#supplier_id').attr("disabled","disabled");
          $('#po_number').attr("readonly","true");

          $('#supplier_id').not(this).find('option').prop('disabled', 'true');

          $('#supplier_id').addClass('disableSelect');

          $('#dock_id').addClass('disableSelect');
          $('#dock_id').not(this).find('option').prop('disabled', 'true');

          $.ajax({
              url: "{{ url('getSupplierData') }}",
              type: "POST",
              data: {id:selected},
              success: function (data) {
                ////console.log(JSON.parse(data))  
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
                  if(index == 'dockdata'){

                    $.each(item, function(index, dock) {
                      $('#dock_id').append("<option value="+dock.id +">" + dock.dock_name + "</option>")
                    });
                  }
                });

              },
              error: function (data) {
                  //console.log('Error:', data);
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
              //console.log(data.date_of_delivery)
              $('#schedule_id').val(data.id);
              $('#po_number').val(data.po_number);
              $('#supplier_id').val(data.supplier_id);
              $('#alt_supplier_id').val(data.supplier_id);
              $('#dock_id').val(data.dock_id);

              $('#dateOfDelivery').val(data.date_of_delivery);
              $('#recurrent_dateend').val(data.recurrent_dateend);

              $('#truck_id').val(data.truck_id);

              $('#driver_id').val(data.driver_id);

              $('#assistant_id').val(data.assistant_id);

              $('#container_number').val(data.container_number);

              // $("input[name=recurrence][value='" + data.recurrence + "']").prop('checked', 'checked');
              console.log(data.recurrence)
              $("#recurrence").val(data.recurrence)
              if(data.recurrence == "Recurrent"){
                $(".r_ordering_days").css('display','block')
              }
              var ordering_days_arr = data.ordering_days.split("|")
              $.each(ordering_days_arr, function(i,e){
                $("#ordering_days option[value='" + $.trim(e) + "']").prop("selected", true);
              });

              $('#cont').html('');
              createTable();
              //console.log(data.material_list  + "Test")
              if(data.material_list == 0){
                  addRow('','','')
              }
              $.each(data.material_list.gcas, function(index, item) {
                  if(item != ""){

                    addRow(item,data.material_list.description[index],data.material_list.quantity[index])
                  }else{
                    console.log(data.material_list.gcas.length)
                    if(data.material_list.gcas.length==2){
                      addRow("","","")
                    }
                  }
              });

              $("#slotting_time").val(data.slotting_time_text);

               $('div.occupied_slot_box').addClass("slot_box");


              //refresh slot box
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
                          if(slot != ""){
                            $('div.slot_box:contains("'+slot+'")').addClass("occupied_slot_box");
                            $('div.slot_box:contains("'+slot+'")').removeClass("slot_box");
                          }
                        });
                      });
                  },
                  error: function (data) {
                      //console.log('Error:', data);
                  }
              });
              //refresh slot box


              $.each(data.slotting_time, function(index, slot) {
                  //console.log(slot)
                    if(slot != ""){
                      $('div.slot_box:contains("'+slot+'")').addClass("slot_box");
                      $('div.slot_box:contains("'+slot+'")').addClass("editable_slot_box");
                      $('div.editable_slot_box:contains("'+slot+'")').removeClass("occupied_slot_box");
                    }
                });
          })

          // change the border color just for fun
          //info.el.style.borderColor = 'red';

    });

    //EDIT RECURRENCE
    $('body').on('click', '#edit_recurrence', function (e) {
        $('#ajaxModelEditRecurrent').modal('hide')
        $('#isEditingRecurrent').val("1");
        $('#isEditingSingle').val("0")
        $('#dataEditID').val("")
        //console.log($('#selected_schedule').val()) 

        //console.log($('#recurrence_hidden').val())   

        //$('div.slot_box').removeClass("slot_box");
          $('div.editable_slot_box').addClass('slot_box').removeClass("editable_slot_box");
          $('div.occupied_slot_box').addClass('slot_box').removeClass("occupied_slot_box");

          $('#saveBtn').html('Save Changes');
          $('#response').html(''); 
          var id = $('#selected_schedule').val();
          var selected = $('#selected_supplierid').val();

          if(id == "" && selected == ""){
            $('#response').append('<div class="alert alert-warning">Please select schedule to edit.</div>  ')
          }
          $('#truck_id').html('');
          $('#driver_id').html('');
          $('#assistant_id').html('');
          $('#alt_supplier_id').val('');
          //$('#supplier_id').attr("disabled","disabled");
          $('#po_number').attr("readonly","true");

          $('#supplier_id').not(this).find('option').prop('disabled', 'true');

          $('#supplier_id').addClass('disableSelect');

          $.ajax({
              url: "{{ url('getSupplierData') }}",
              type: "POST",
              data: {id:selected},
              success: function (data) {
                ////console.log(JSON.parse(data))  
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

                  if(index == 'dockdata'){

                    $.each(item, function(index, dock) {
                      $('#dock_id').append("<option value="+dock.id +">" + dock.dock_name + "</option>")
                    });
                  }
                });

              },
              error: function (data) {
                  //console.log('Error:', data);
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
              //console.log(data.date_of_delivery)
              $('#schedule_id').val(data.id);
              $('#po_number').val(data.po_number);
              $('#supplier_id').val(data.supplier_id);
              $('#alt_supplier_id').val(data.supplier_id);
              $('#dock_id').val(data.dock_id);

              $('#dateOfDelivery').val(data.date_of_delivery);

              $('#recurrent_dateend').val(data.recurrent_dateend);

              $('#truck_id').val(data.truck_id);

              $('#driver_id').val(data.driver_id);

              $('#assistant_id').val(data.assistant_id);

              $('#container_number').val(data.container_number);

              $("#recurrence").val(data.recurrence)
              if(data.recurrence == "Recurrent"){
                $(".r_ordering_days").css('display','block')
              }
              var ordering_days_arr = data.ordering_days.split("|")
              $.each(ordering_days_arr, function(i,e){
                $("#ordering_days option[value='" + $.trim(e) + "']").prop("selected", true);
              });

              $('#cont').html('');
              createTable();
              //console.log(data.material_list  + "Test")
              if(data.material_list == 0){
                  addRow('','','')
              }
              $.each(data.material_list.gcas, function(index, item) {
                  if(item != ""){

                    addRow(item,data.material_list.description[index],data.material_list.quantity[index])
                  }else{
                    console.log(data.material_list.gcas.length)
                    if(data.material_list.gcas.length==2){
                      addRow("","","")
                    }
                  }
              });

              $("#slotting_time").val(data.slotting_time_text);

               $('div.occupied_slot_box').addClass("slot_box");


              //refresh slot box
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
                          if(slot != ""){
                            $('div.slot_box:contains("'+slot+'")').addClass("occupied_slot_box");
                            $('div.slot_box:contains("'+slot+'")').removeClass("slot_box");
                          }
                        });
                      });
                  },
                  error: function (data) {
                      //console.log('Error:', data);
                  }
              });
              //refresh slot box


              $.each(data.slotting_time, function(index, slot) {
                  //console.log(slot)
                    if(slot != ""){
                      $('div.slot_box:contains("'+slot+'")').addClass("slot_box");
                      $('div.slot_box:contains("'+slot+'")').addClass("editable_slot_box");
                      $('div.editable_slot_box:contains("'+slot+'")').removeClass("occupied_slot_box");
                    }
                });
          })

          // change the border color just for fun
          //info.el.style.borderColor = 'red';
    });

    $('body').on('click', '.edit_single_event', function (e) {
        $('#ajaxModelEditRecurrent').modal('hide')
        $('#isEditingRecurrent').val("0")
        $('#isEditingSingle').val("1")
        $('#dataEditID').val("")
        $('#modelViewErrorRecurrent').modal('hide')
        //console.log($('#selected_schedule').val())  
        //console.log($('#recurrence_hidden').val())
        


          //$('div.slot_box').removeClass("slot_box");
          $('div.editable_slot_box').addClass('slot_box').removeClass("editable_slot_box");
          $('div.occupied_slot_box').addClass('slot_box').removeClass("occupied_slot_box");

          $('#saveBtn').html('Save Changes');
          $('#response').html(''); 

          //var id = $('#selected_schedule').val();
          if($(this).attr("data-edit-id") != null){
              id = $(this).attr("data-edit-id")
              $('#dataEditID').val(id)
          }
          var selected = $('#selected_supplierid').val();

          if(id == "" && selected == ""){
            $('#response').append('<div class="alert alert-warning">Please select schedule to edit.</div>  ')
          }
          $('#truck_id').html('');
          $('#driver_id').html('');
          $('#assistant_id').html('');
          $('#dock_id').html('');
          $('#alt_supplier_id').val('');
          //$('#supplier_id').attr("disabled","disabled");
          $('#po_number').attr("readonly","true");

          $('#supplier_id').not(this).find('option').prop('disabled', 'true');

          $('#supplier_id').addClass('disableSelect');

          $('#dock_id').addClass('disableSelect');
          $('#dock_id').not(this).find('option').prop('disabled', 'true');

         


          $('#scheduleForm').trigger("reset");      
          
          $.get("{{ route('ajaxschedules.index') }}" +'/' + id +'/edit', function (data) {
              $('#modelHeading').html("Edit Schedule");
              $('#saveBtn').val("edit-user");
              $('#ajaxModel').modal({
                backdrop:'static',
                keyboard: false
              })
              //console.log(data.date_of_delivery)
              $('#schedule_id').val(data.id);
              $('#po_number').val(data.po_number);
              $('#supplier_id').val(data.supplier_id);
              $('#alt_supplier_id').val(data.supplier_id);

             $.ajax({
                  url: "{{ url('getSupplierData') }}",
                  type: "POST",
                  data: {id:data.supplier_id},
                  success: function (data) {
                    ////console.log(JSON.parse(data))  
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
                      if(index == 'dockdata'){

                        $.each(item, function(index, dock) {
                          $('#dock_id').append("<option value="+dock.id +">" + dock.dock_name + "</option>")
                        });
                      }
                    });

                  },
                  error: function (data) {
                      //console.log('Error:', data);
                  }
              });

              $('#dock_id').val(data.dock_id);

              $('#dateOfDelivery').val(data.date_of_delivery);
              $('#recurrent_dateend').val(data.recurrent_dateend);

              $('#truck_id').val(data.truck_id);

              $('#driver_id').val(data.driver_id);

              $('#assistant_id').val(data.assistant_id);

              $('#container_number').val(data.container_number);

              // $("input[name=recurrence][value='" + data.recurrence + "']").prop('checked', 'checked');
              console.log(data.recurrence)
              $("#recurrence").val(data.recurrence)
              if(data.recurrence == "Recurrent"){
                $(".r_ordering_days").css('display','block')
              }
              var ordering_days_arr = data.ordering_days.split("|")
              $.each(ordering_days_arr, function(i,e){
                $("#ordering_days option[value='" + $.trim(e) + "']").prop("selected", true);
              });

              $('#cont').html('');
              createTable();
              //console.log(data.material_list  + "Test")
              if(data.material_list == 0){
                  addRow('','','')
              }
              $.each(data.material_list.gcas, function(index, item) {
                  if(item != ""){

                    addRow(item,data.material_list.description[index],data.material_list.quantity[index])
                  }else{
                    console.log(data.material_list.gcas.length)
                    if(data.material_list.gcas.length==2){
                      addRow("","","")
                    }
                  }
              });

              $("#slotting_time").val(data.slotting_time_text);

               $('div.occupied_slot_box').addClass("slot_box");


              //refresh slot box
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
                          if(slot != ""){
                            $('div.slot_box:contains("'+slot+'")').addClass("occupied_slot_box");
                            $('div.slot_box:contains("'+slot+'")').removeClass("slot_box");
                          }
                        });
                      });
                  },
                  error: function (data) {
                      //console.log('Error:', data);
                  }
              });
              //refresh slot box


              $.each(data.slotting_time, function(index, slot) {
                  //console.log(slot)
                    if(slot != ""){
                      $('div.slot_box:contains("'+slot+'")').addClass("slot_box");
                      $('div.slot_box:contains("'+slot+'")').addClass("editable_slot_box");
                      $('div.editable_slot_box:contains("'+slot+'")').removeClass("occupied_slot_box");
                    }
                });
          })

          // change the border color just for fun
          //info.el.style.borderColor = 'red';

    });


    // CLICK SLOT BOX
    $('body').on('click', '.slot_box', function () {
         
          var slotting_time = $('#slotting_time'); 
          var slotting_time_unavailability = $('#slotting_time_unavailability'); 
           var _this = $(this);
           value = $(this).html();
           if(_this.hasClass('active_slot_box')){
              $(this).removeClass('active_slot_box');
              var x = slotting_time.val().replace(value + "|","");
              var y = slotting_time_unavailability.val().replace(value + "|","");
              slotting_time.val(x)
              slotting_time_unavailability.val(y)
           }else{
              $(this).addClass('active_slot_box');
              slotting_time.val(slotting_time.val() + value + "|")
              slotting_time_unavailability.val(slotting_time_unavailability.val() + value + "|")
           }
         
          
    });

    //EDITABLE CLICK SLOT BOX
    $('body').on('click', '.editable_slot_box', function () {
         
          var slotting_time = $('#slotting_time'); 
          var slotting_time_unavailability = $('#slotting_time_unavailability'); 
           var _this = $(this);
           value = $(this).html();
           if(_this.hasClass('editable_slot_box')){
              $(this).removeClass('editable_slot_box').addClass('slot_box');
               $(this).removeClass('occupied_slot_box');
              var x = slotting_time.val().replace(value + "|","");
              var y = slotting_time_unavailability.val().replace(value + "|","");
              slotting_time.val(x)
              slotting_time_unavailability.val(y)
           }else{
              $(this).addClass('active_slot_box');
              slotting_time.val(slotting_time.val() + value + "|")
              slotting_time_unavailability.val(slotting_time_unavailability.val() + value + "|")
           }
         
          
    });


    //ONCHANGE DATEOFDELIVERY
    $('body').on('change', '#dateOfDelivery, #dock_id', function () {
        $('div.occupied_slot_box').addClass("slot_box");
        $('div.slot_box').removeClass("occupied_slot_box");
        $('div.slot_box').removeClass("active_slot_box");

        $('#slotting_time').val('');
        var dock_id = $("#dock_id option:selected").val();
        var date_of_delivery = $('#dateOfDelivery').val();
        //console.log(dock_id)
        $.ajax({
            url: "{{ url('getSlottingTime') }}",
            type: "POST",
            data: {date_of_delivery:date_of_delivery,isForUnavailability:'0',dock_id:dock_id},
            success: function (data) {
                $.each(JSON.parse(data), function(index, item) {
                  $.each(item.slotting_time, function(i, slot) {
                  //console.log(slot)
                    if(slot != ""){
                      $('div.slot_box:contains("'+slot+'")').addClass("occupied_slot_box");
                      $('div.slot_box:contains("'+slot+'")').removeClass("slot_box");
                    }
                  });
                });
            },
            error: function (data) {
                //console.log('Error:', data);
            }
        });
      });

     //ONCHANGE DateOFUnavailability
     $('body').on('change', '#dateOfUnavailability', function () {
        $('div.occupied_slot_box').addClass("slot_box");
        $('div.slot_box').removeClass("occupied_slot_box");
        $('div.slot_box').removeClass("active_slot_box");

        $('#slotting_time_unavailability').val('');
        var date_of_unavailability = $('#dateOfUnavailability').val();
        $.ajax({
            url: "{{ url('getSlottingTime') }}",
            type: "POST",
            data: {date_of_unavailability:date_of_unavailability,isForUnavailability:'1'},
            success: function (data) {
                $.each(JSON.parse(data), function(index, item) {
                  $.each(item.slotting_time, function(i, slot) {
                  //console.log(slot)
                    if(slot != ""){
                      $('div.slot_box:contains("'+slot+'")').addClass("occupied_slot_box");
                      $('div.slot_box:contains("'+slot+'")').removeClass("slot_box");
                    }
                  });
                });
            },
            error: function (data) {
                //console.log('Error:', data);
            }
        });
      });

     //ON CHANGE SUPPLIER ID
     $('body').on('change', '#supplier_id', function () {
         
         var selected = $("#supplier_id option:selected").val();
         ////console.log(selected)
         $('#truck_id').html('');
         $('#driver_id').html('');
         $('#assistant_id').html('');
         $('#dock_id').html('');

       if(selected != "0"){
        $.ajax({
              url: "{{ url('getSupplierData') }}",
              type: "POST",
              data: {id:selected},
              success: function (data) {
                ////console.log(JSON.parse(data))  
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

                  if(index == 'dockdata'){

                    $.each(item, function(index, dock) {
                      $('#dock_id').append("<option value="+dock.id +">" + dock.dock_name + "</option>")
                    });
                  }
                });

              },
              error: function (data) {
                  //console.log('Error:', data);
              }
          });
      }
          
    });



    

    //SCHEDULING CALENDAR
    var count = 0;
    function testCalendar(module_name) {


      var calendarEl = document.getElementById('calendar');

      calendarEl.innerHTML = "";

      var calendar = new FullCalendar.Calendar(calendarEl, {
        schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
        plugins: [ 'interaction', 'resourceDayGrid', 'resourceTimeGrid', 'list' ],
        defaultView: 'timeGridWeek',
        defaultDate: Date.now(),
        editable: false,
        selectable: true,
        eventLimit: true, // allow "more" link when too many events
        header: {
          left: 'title',
          center: 'CreateSchedule ScheduleDockUnavailability',
          right: 'timeGridWeek listMonth today prev,next'
        },
        customButtons: {
          CreateSchedule: {
            text: 'Create Schedule',
            click: function() {
              $('#saveBtn').html('Save Changes');
              $('#saveBtn').val("create-product");
              $('#schedule_id').val('');
              $('#scheduleForm').trigger("reset");
              $('#truck_id').html('');
              $('#driver_id').html('');
              $('#assistant_id').html('');
              $('#dock_id').html('');
              $('#dock_id').removeAttr("disabled");
              $('#supplier_id').removeAttr("disabled");
              $('#po_number').removeAttr("readonly");
              $('.btncancelSchedule').hide();
              $('#btnPrintVoucher').hide();

              $('#supplier_id').not(this).find('option').removeAttr('disabled')
              $('#alt_supplier_id').val('');
              $('#supplier_id').removeClass('disableSelect');
              $('#dock_id').not(this).find('option').removeAttr('disabled')
              $('#dock_id').removeClass('disableSelect');

              $(".r_ordering_days").css('display','none')
              $(".r_recurrent_dateend").css('display','none')

              $('.slot_box').removeClass('active_slot_box');
              $('#modelHeading').html("Register Schedule");

                $('div.occupied_slot_box').addClass("slot_box");
                $('div.slot_box').removeClass("occupied_slot_box");
                $('div.slot_box').removeClass("active_slot_box");
                $('div.slot_box').removeClass("editable_slot_box");

              $('#po_number').css("outline","solid 1px transparent")
              $('#truck_id').css("outline","solid 1px transparent")
              $('#supplier_id').css("outline","solid 1px transparent")
              $('#driver_id').css("outline","solid 1px transparent")
              $('#assistant_id').css("outline","solid 1px transparent")
              $('#dock_id').css("outline","solid 1px transparent")
              $('#dateOfDelivery').css("outline","solid 1px transparent")
              $('#recurrence').css("outline","solid 1px transparent")
              $('.slotting_time').css("color","black")

              $('#cont').html('');
              $('document').ready(function(){
                createTable();
                addRow('','','');
              });
              $('#ajaxModel').modal({
                backdrop:'static',
                keyboard: false
              })
            }
          },
          ScheduleDockUnavailability: {
            text: 'Schedule Dock Unavailability',
            click: function() {
              $('#unavailability_id').val('');
              $('div.occupied_slot_box').addClass("slot_box");
              $('div.slot_box').removeClass("occupied_slot_box");
              $('div.slot_box').removeClass("active_slot_box");
              $('div.slot_box').removeClass("editable_slot_box");
              $('#dock_id_unavailability').val(0)
              $('#dateOfUnavailability').val('')
              $('.r_recurrent_dateend').hide();

              $(".r_ordering_days_u").css('display','none')
              $('#unavailabilityForm').trigger("reset");
              $('#ajaxModelUnavailability').modal({
                backdrop:'static',
                keyboard: false
              })
              $('#modelHeadingUnavailability').html('')
              $('#modelHeadingUnavailability').append('Schedule Dock Unavailability')
              
            }
          },
        },
        views: {
          resourceTimeGridTwoDay: {
            type: 'resourceTimeGrid',
            duration: { days: 2 },
            buttonText: '2 days',
          },
          listMonth: { buttonText: 'List Month' },
          timeGridWeek: { buttonText: 'List Calendar'},
        },


        //// uncomment this line to hide the all-day slot
        allDaySlot: false,

        //uncomment this for default setup
        // resources: [
        //   { id: 'a', title: 'Room A' },
        //   { id: 'b', title: 'Room B', eventColor: 'green' },
        //   { id: 'c', title: 'Room C', eventColor: 'orange' },
        //   { id: 'd', title: 'Room D', eventColor: 'red' }
        // ],
        // events: [
        //   { id: '1', resourceId: 'b', start: '2019-04-06', end: '2019-04-08', title: 'event 1' },
        //   { id: '2', resourceId: 'a', start: '2019-04-07T09:00:00', end: '2019-04-07T14:00:00', title: 'event 2' },
        //   { id: '3', resourceId: 'b', start: '2019-04-07T12:00:00', end: '2019-04-08T06:00:00', title: 'event 3' },
        //   { id: '4', resourceId: 'c', start: '2019-04-07T07:30:00', end: '2019-04-07T09:30:00', title: 'event 4' },
        //   { id: '5', resourceId: 'd', start: '2019-04-07T10:00:00', end: '2019-04-07T15:00:00', title: 'event 5' }
        // ],

        eventRender: function(info) {
        },

        events: {
          url: "{{ url('allschedules') }}",
          method: 'POST',
          extraParams: {
            _token: '{{csrf_token()}}',
            module: module_name
          },
          success: function(event){
            //console.log(event)
          },
          failure: function() {
            //alert('there was an error while fetching events!');
          },
          color: '#1e9',   // a non-ajax option
          textColor: 'black' // a non-ajax option
        },
        select: function(arg) {
          
          console.log(
            'select',
            arg.startStr,
            arg.endStr,
            arg.resource ? arg.resource.id : '(no resource)'
          );
        },
        dateClick: function(arg) {
          
          console.log(
            'dateClick',
            arg.date,
            arg.resource ? arg.resource.id : '(no resource)'
          );
        },
         eventClick: function(info) {

            $('#scheduleForm').trigger("reset");      

            // view hide element
            $('.btncancelSchedule').show();
            $('#btnPrintVoucher').show();

            $('#btnPrintVoucher').attr("data-id",info.event.id)
            //set data
            $('#recurrence_hidden').val('');
            $('#selected_schedule').val(info.event.id);
            $('#selected_supplierid').val(info.event.extendedProps.supplier_id);
            $('#isEditingSingle').val('')
            $('#isEditingRecurrent').val('')
            $('#dataEditID').val('')

            if(info.event.extendedProps.isForUnavailability != 1){
              $('#recurrence_hidden').val(info.event.extendedProps.recurrence)
            }
            
            if($('#recurrence_hidden').val() != 'Recurrent'){


            $('.r_recurrent_dateend').hide();
            $('div.editable_slot_box').addClass('slot_box').removeClass("editable_slot_box");
            $('div.occupied_slot_box').addClass('slot_box').removeClass("occupied_slot_box");

            $('#saveBtn').html('Save Changes');
            $('#response').html(''); 
            var id = info.event.id;
            var selected = info.event.extendedProps.supplier_id;

            if(id == "" && selected == ""){
              $('#response').append('<div class="alert alert-warning">Please select schedule to edit.</div>  ')
            }
            $('#truck_id').html('');
            $('#driver_id').html('');
            $('#assistant_id').html('');
            $('#dock_id').html(''); 
            $('#alt_supplier_id').val('');
            //$('#supplier_id').attr("disabled","disabled");
            $('#po_number').attr("readonly","true");
            $('#dock_id').not(this).find('option').prop('disabled', 'true');
            $('#supplier_id').not(this).find('option').prop('disabled', 'true');

            $('#supplier_id').addClass('disableSelect');
            $('#dock_id').addClass('disableSelect');

            //console.log(info.event.extendedProps)
            $('#recurrence_unavailability').css('outline','1px solid transparent')
            $('#po_number').css('outline','1px solid transparent')
            $('#recurrence').css('outline','1px solid transparent')
            $('#dateOfDelivery').css('outline','1px solid transparent')
            $('#truck_id').css('outline','1px solid transparent')
            $('#driver_id').css('outline','1px solid transparent')
            $('#assistant_id').css('outline','1px solid transparent')
            $('#supplier_id').css('outline','1px solid transparent')
            $('.slotting_time').css('color','black')
            $('#dock_id_unavailability').css('outline','1px solid transparent')
            $('#dateOfUnavailability').css('outline','1px solid transparent')
            $('#type_unavailability').css('outline','1px solid transparent')
            $.ajax({
                url: "{{ url('getSupplierData') }}",
                type: "POST",
                data: {id:selected},
                success: function (data) {
                  console.log("test:" + data)
                  $.each(JSON.parse(data), function(index, item) {
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

                    if(index == 'dockdata'){

                      $.each(item, function(index, dock) {
                        $('#dock_id').append("<option value="+dock.id +">" + dock.dock_name + "</option>")
                      });
                    }
                  });

                },
                error: function (data) {
                    //console.log('Error:', data);
                }
            });

            //unavailability
            //console.log(info.event.extendedProps)
            if(info.event.extendedProps.isForUnavailability == 1){
                $.ajax({
                url: "{{ url('getEditDockUnavailability') }}",
                type: "POST",
                data: {id:id},
                success: function (data) {
                       $('#ajaxModelUnavailability').modal({
                          backdrop:'static',
                          keyboard: false
                        })
                        $('#modelHeadingUnavailability').html('')
                        $('#modelHeadingUnavailability').append('Edit Schedule Dock Unavailability')
                        //console.log(data.date_of_delivery)
                        $('#unavailability_id').val(data.id);
                        $('#dock_id_unavailability').val(data.dock_id);

                        $('#dateOfUnavailability').val(data.date_of_delivery);
                        //console.log(data.date_of_delivery)


                        $("input[name=recurrence_unavailability][value='" + data.recurrence + "']").prop('checked', 'checked');
                        if(data.recurrence == "Recurrent"){
                          $(".r_ordering_days").css('display','block')
                        }
                        var ordering_days_arr = data.ordering_days.split("|")
                        $.each( ordering_days_arr, function( key, value ) {
                          $("input[value='" + $.trim(value) + "']").prop('checked', true);
                        });
                        //console.log(data)
                        $("#slotting_time_unavailability").val(data.slotting_time_text);

                        $('div.occupied_slot_box').addClass("slot_box");
                        //refresh slot box
                        $('div.slot_box').removeClass("occupied_slot_box");
                        $('div.slot_box').removeClass("active_slot_box");
                        var date_of_delivery = $('#dateOfUnavailability').val();

                        $.ajax({
                            url: "{{ url('getSlottingTime') }}",
                            type: "POST",
                            data: {date_of_delivery:date_of_delivery,isForUnavailability:info.event.extendedProps.isForUnavailability},
                            success: function (data) {
                                //console.log(data)
                                $.each(JSON.parse(data), function(index, item) {
                                  $.each(item.slotting_time, function(i, slot) {
                                    if(slot != ""){
                                      $('div.slot_box:contains("'+slot+'")').addClass("occupied_slot_box");
                                      $('div.slot_box:contains("'+slot+'")').removeClass("slot_box");
                                    }
                                  });
                                });
                            },
                            error: function (data) {
                                //console.log('Error:', data);
                            }
                        });
                        //refresh slot box


                        $.each(data.slotting_time, function(index, slot) {
                            //console.log(slot)
                              if(slot != ""){
                                $('div.slot_box:contains("'+slot+'")').addClass("slot_box");
                                $('div.slot_box:contains("'+slot+'")').addClass("editable_slot_box");
                                $('div.editable_slot_box:contains("'+slot+'")').removeClass("occupied_slot_box");
                              }
                          });

                },
                error: function (data) {
                    //console.log('Error:', data);
                }
            });

                
            }else{
              var id = info.event.id;
              console.log("id : " + id)
              $.get("{{ route('ajaxschedules.index') }}" +'/' + id +'/edit', function (data) {
                  $('#modelHeading').html("Edit Schedule");
                  $('#saveBtn').val("edit-user");
                  $('#ajaxModel').modal({
                    backdrop:'static',
                    keyboard: false
                  })
                  //console.log(data.date_of_delivery)
                  $('#schedule_id').val(data.id);
                  $('#po_number').val(data.po_number);
                  $('#supplier_id').val(data.supplier_id);
                  $('#alt_supplier_id').val(data.supplier_id);
                  $('#dock_id').val(data.dock_id);

                  $('#dateOfDelivery').val(data.date_of_delivery);

                  $('#recurrent_dateend').val(data.recurrent_dateend);

                  $('#truck_id').val(data.truck_id);

                  $('#driver_id').val(data.driver_id);

                  $('#assistant_id').val(data.assistant_id);

                  $('#container_number').val(data.container_number);

                  // $("input[name=recurrence][value='" + data.recurrence + "']").prop('checked', 'checked');
                  console.log(data.recurrence)
                  $("#recurrence").val(data.recurrence)
                  if(data.recurrence == "Recurrent"){
                    $(".r_ordering_days").css('display','block')
                  }
                  var ordering_days_arr = data.ordering_days.split("|")
                  // $.each( ordering_days_arr, function( key, value ) {
                  //   $("input[value='" + $.trim(value) + "']").prop('checked', true);
                  // });
                  $.each(ordering_days_arr, function(i,e){
                    $("#submodules option[value='" + $.trim(e) + "']").prop("selected", true);
                  });

                  $('#cont').html('');
                  createTable();
                  //console.log(data.material_list  + "Test")
                  if(data.material_list == 0){
                      addRow('','','')
                  }
                  $.each(data.material_list.gcas, function(index, item) {
                      if(item != ""){

                        addRow(item,data.material_list.description[index],data.material_list.quantity[index])
                      }
                  });

                  $("#slotting_time").val(data.slotting_time_text);

                   $('div.occupied_slot_box').addClass("slot_box");


                  //refresh slot box
                  $('div.slot_box').removeClass("occupied_slot_box");
                  $('div.slot_box').removeClass("active_slot_box");
                  var date_of_delivery = $('#dateOfDelivery').val();
                  $.ajax({
                      url: "{{ url('getSlottingTime') }}",
                      type: "POST",
                      data: {date_of_delivery:date_of_delivery},
                      success: function (data) {
                          //console.log(data)
                          $.each(JSON.parse(data), function(index, item) {
                            $.each(item.slotting_time, function(i, slot) {
                              if(slot != ""){
                                $('div.slot_box:contains("'+slot+'")').addClass("occupied_slot_box");
                                $('div.slot_box:contains("'+slot+'")').removeClass("slot_box");
                              }
                            });
                          });
                      },
                      error: function (data) {
                          //console.log('Error:', data);
                      }
                  });
                  //refresh slot box


                  $.each(data.slotting_time, function(index, slot) {
                      //console.log(slot)
                        if(slot != ""){
                          $('div.slot_box:contains("'+slot+'")').addClass("slot_box");
                          $('div.slot_box:contains("'+slot+'")').addClass("editable_slot_box");
                          $('div.editable_slot_box:contains("'+slot+'")').removeClass("occupied_slot_box");
                        }
                    });
              })
            }

            // change the border color just for fun
            //info.el.style.borderColor = 'red';

            }else{
              if(info.event.extendedProps.status != "No-Show"){
                $('#ajaxModelEditRecurrent').modal({
                    backdrop:'static',
                    keyboard: false
                })
              }
            }


            console.log(info.event.extendedProps.status)
            //RESCHEDULING
            if(info.event.extendedProps.status == "No-Show"){
                $('#isEditingSingle').val('1');
                $.get("{{ route('ajaxschedules.index') }}" +'/' + info.event.id +'/edit', function (data) {
                    $('#modelHeading').html("Reschedule Slotting");
                    $('#saveBtn').val("edit-user");
                    $('#ajaxModel').modal({
                      backdrop:'static',
                      keyboard: false
                    })
                      $.ajax({
                        url: "{{ url('getSupplierData') }}",
                        type: "POST",
                        data: {id:info.event.extendedProps.supplier_id},
                        success: function (data) {
                          console.log("test:" + data)
                          $.each(JSON.parse(data), function(index, item) {
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

                            if(index == 'dockdata'){

                              $.each(item, function(index, dock) {
                                $('#dock_id').append("<option value="+dock.id +">" + dock.dock_name + "</option>")
                              });
                            }
                          });

                        },
                        error: function (data) {
                            //console.log('Error:', data);
                        }
                    });


                    setTimeout(function(){
                        $('#schedule_id').val(data.id);
                        $('#po_number').val(data.po_number);
                        $('#supplier_id').val(data.supplier_id);
                        $('#alt_supplier_id').val(data.supplier_id);
                        $('#dock_id').val(data.dock_id);

                        $('#dateOfDelivery').val(data.date_of_delivery);

                        $('#recurrent_dateend').val(data.recurrent_dateend);

                        $('#truck_id').val(data.truck_id);

                        $('#driver_id').val(data.driver_id);

                        $('#assistant_id').val(data.assistant_id);

                        $('#container_number').val(data.container_number);

                        $('#btnPrintVoucher').hide();

                        $("#recurrence").val(data.recurrence)
                        if(data.recurrence == "Recurrent"){
                          $(".r_ordering_days").css('display','block')
                        }
                        var ordering_days_arr = data.ordering_days.split("|")
                        $.each(ordering_days_arr, function(i,e){
                          $("#ordering_days option[value='" + $.trim(e) + "']").prop("selected", true);
                        });

                        $('#cont').html('');
                        createTable();
                        //console.log(data.material_list  + "Test")
                        if(data.material_list == 0){
                            addRow('','','')
                        }
                        $.each(data.material_list.gcas, function(index, item) {
                            if(item != ""){

                              addRow(item,data.material_list.description[index],data.material_list.quantity[index])
                            }
                        });

                        $("#slotting_time").val(data.slotting_time_text);

                        $('div.occupied_slot_box').addClass("slot_box");


                        //refresh slot box
                        $('div.slot_box').removeClass("occupied_slot_box");
                        $('div.slot_box').removeClass("active_slot_box");
                        var date_of_delivery = $('#dateOfDelivery').val();
                        $.ajax({
                            url: "{{ url('getSlottingTime') }}",
                            type: "POST",
                            data: {date_of_delivery:date_of_delivery},
                            success: function (data) {
                                //console.log(data)
                                $.each(JSON.parse(data), function(index, item) {
                                  $.each(item.slotting_time, function(i, slot) {
                                    if(slot != ""){
                                      $('div.slot_box:contains("'+slot+'")').addClass("occupied_slot_box");
                                      $('div.slot_box:contains("'+slot+'")').removeClass("slot_box");
                                    }
                                  });
                                });
                            },
                            error: function (data) {
                                //console.log('Error:', data);
                            }
                        });
                        //refresh slot box


                        $.each(data.slotting_time, function(index, slot) {
                            //console.log(slot)
                              if(slot != ""){
                                $('div.slot_box:contains("'+slot+'")').addClass("slot_box");
                                $('div.slot_box:contains("'+slot+'")').addClass("editable_slot_box");
                                $('div.editable_slot_box:contains("'+slot+'")').removeClass("occupied_slot_box");
                              }
                        });
                      },2000)
                    
                })
            }

           
        }
  });

  

  $("body").on("click",".btn-block-sched",function(e){

      e.preventDefault();

      $("#ajaxModelDelete").modal('hide');
      $("#modelViewErrorRecurrent").modal('show');
  })

    function formatAMPM(date) {
      var hours = date.getHours();
      var minutes = date.getMinutes();
      var ampm = hours >= 12 ? 'PM' : 'AM';
      hours = hours % 12;
      hours = hours ? hours : 12; // the hour '0' should be '12'
      minutes = minutes < 10 ? '0'+minutes : minutes;
      var strTime = hours + ':' + minutes + ' ' + ampm;
      return strTime;
    }

    var value = "";
    // $('body').on('click', '.btn-modules', function () {
    //    count = count + 1;
    //    value = $(this).attr("data-value");
    //    $("#current_module").val(value)
    //    calendarEl.innerHTML = "";
    //    calendar.destroy();
    //    testCalendar(value)
        
    // });

    $('body').on('change','.btn-modules-dropdown',function(){

        $(".button-area").empty();
      setTimeout(function(){
        $(".fc-center").appendTo(".button-area");
        $(".fc-CreateSchedule-button").addClass('btn-block');
        $(".fc-ScheduleDockUnavailability-button").addClass('btn-block');
        $("#calendar > div.fc-toolbar.fc-header-toolbar > div.fc-left > h2").text($("#calendar > div.fc-toolbar.fc-header-toolbar > div.fc-left > h2").text().replace("â€“"," \u2013 "))
      },100);
      count = count + 1;
      value = $('#btn-modules-dropdown').children("option:selected").val();
      window.location = "/scheduler/index?get_module=" + value
      //calendarEl.innerHTML = "";
      //calendar.destroy();
      //testCalendar(value)
    });


      
      calendar.render();

    
    }

   $('document').ready(function(){
      var val = ""
      <?php if(isset($_GET['get_module'])){ ?>
      val = "<?php echo $_GET["get_module"]; ?>"
      $('#current_dock').html(val)
      $("#current_module").val(val) 
      <?php } ?>  
      testCalendar(val);
    });

    $('body').on('click', '#saveBtn', function (e) {

       
        // var ordering_days = $(':checkbox[name^=ordering_days]:checked').length;
        var ordering_days = [];
        $.each($("#ordering_days option:selected"), function(){            
            ordering_days.push($(this).val());
        });
        //var recurrence = $(':radio[name^=recurrence]:checked');
        var recurrence = $('#recurrence').children("option:selected");
        var trucktype = $("#truck_id > option:selected").data('type');  


        if(recurrence.val() == "Recurrent"){
            $(".gcas").css('border','1px solid black')
            $(".description").css('border','1px solid black')
            $(".quantity").css('border','1px solid black')

             if($("#recurrent_dateend").val() == "")
             $("#recurrent_dateend").css('outline','1px solid red')
            else
             $("#recurrent_dateend").css('outline','1px solid transparent')

            if(ordering_days == 0){
              $("#modalresponse").show();
              $(".ordering_days").css('color','red')
                $("#modalresponse").html("<div class='alert alert-danger'>Please fill in the required fields.</div>")

              $('#modalresponse').fadeIn(1000);
              setTimeout(function(){
                $('#modalresponse').fadeOut(1000);
              },2000)

              return false;
            }
            else{
              $(".ordering_days").css('color','black')
            }
          }else if(recurrence.val() == "Single Event"){
             
              if($('.gcas').val() == "" || $('.quantity').val() == "" || $('.description').val() == ""){

                  $("#modalresponse").show();
                  $("#modalresponse").html("<div class='alert alert-danger'>Please fill in the required fields.</div>")

                  $('#modalresponse').fadeIn(1000);
                  setTimeout(function(){
                    $('#modalresponse').fadeOut(1000);
                  },2000)

                  $(".gcas").css('border','1px solid red')
                  $(".description").css('border','1px solid red')
                  $(".quantity").css('border','1px solid red')
                  return false;
              }else{
                  $(".gcas").css('border','1px solid black')
                  $(".description").css('border','1px solid black')
                  $(".quantity").css('border','1px solid black')
              }
          }
        

        if($("#po_number").val() == "" || $("#supplier_id").val() == "0" || $("#dock_id").val() ==  "" || $("#truck_id").val() == "" || $("#driver_id").val() == "" || $("#assistant_id").val() == "" || $("#recurrence").val() == "" || $("#dateOfDelivery").val() == "" || $("#slotting_time").val() == ""){
          $("#modalresponse").show();
          $("#modalresponse").html("<div class='alert alert-danger'>Please fill in the required fields.</div>")

          $('#modalresponse').fadeIn(1000);
          setTimeout(function(){
            $('#modalresponse').fadeOut(1000);
          },2000)

          console.log("Recurrent:" + $("#recurrence").val())
          if($("#recurrence").val() == "")
            $("#recurrence").css('outline','1px solid red')
          else
            $("#recurrence").css('outline','1px solid transparent')

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

           if($("#truck_id").val() == "0" || $("#truck_id").val() == "" || $("#truck_id").val() == null)
             $("#truck_id").css('outline','1px solid red')
           else
             $("#truck_id").css('outline','1px solid transparent')

            if($("#driver_id").val() == "0" || $("#driver_id").val() == "" || $("#driver_id").val() == null)
             $("#driver_id").css('outline','1px solid red')
            else
             $("#driver_id").css('outline','1px solid transparent')

            if($("#assistant_id").val() == "0" || $("#assistant_id").val() == "" || $("#assistant_id").val() == null)
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


          

          return false;
        }else{

            e.preventDefault();
            $(this).html('Sending..');
        
            //console.log($('#scheduleForm').serialize())
            var isEditingRecurrent = $("#isEditingRecurrent").val()
            var isEditingSingle = $("#isEditingSingle").val()

            var url_ = ""
            console.log("isEditedFinalized:" +isEditingRecurrent)
            console.log("isEditingSingle:" + isEditingSingle )
            if(recurrence.val() == "Recurrent"){
              if(isEditingRecurrent == "0" && isEditingSingle == "0") {
                url_ = "{{ url('recurrentEventInsertInitial') }}"
              }
              else if(isEditingRecurrent == "1"){
                url_ = "{{ url('recurrentEventInsert') }}"
              }
              else if(isEditingSingle == "1") {
                url_ = "{{ url('singleEventInsert') }}"
              }
            }
            else {
              url_ = "{{ route('ajaxschedules.store') }}"
            }
            $.ajax({
              data: $('#scheduleForm').serialize(),
              url: url_,
              type: "POST",
              dataType: 'json',
              success: function (data) {
                console.log(data)
                 if(data.conflict != undefined && data.conflict != null){
                  console.log(data)
                  $('#modelViewErrorRecurrent').modal({
                    backdrop:'static',
                    keyboard: false
                  })
                  $('#ajaxModel').modal('hide');
                  if(data.conflict.length > 0 ){
                      $("#modelViewErrorRecurrent > .modal-dialog > .modal-content").css('width','960px');
                      $("#modelViewErrorRecurrent > .modal-dialog > .modal-content").css('margin-left','-220px');
                      $.each(data.conflict, function(index, sched) {
                        $(".error_recurrent").append("<div class='row'><div class='col-md-3'><p>"+sched.date_of_delivery+"</p><p>"+sched.slotting_time+"</p><p>"+sched.supplier_name+"</p><p>Delivery ID:"+ sched.id +"</p><p> CONFLICT WITH: Delivery ID: "+sched.conflict_id+"</p></div><div class='col-md-3'><button class='btn btn-primary btn-block edit_single_event' data-edit-id='"+sched.id+"'>Edit Schedule</button><button data-supplier_id='"+sched.supplier_id+"' data-id='"+sched.id+"' class='btn btn-danger btn-block btncancelSchedule_reccurent'>Cancel Schedule</button></div><div class='col-md-3'><p>Delivery ID:"+sched.conflict_id+"</p><p> CONFLICT WITH: Delivery ID: "+sched.id+"</p></div><div class='col-md-3'><button class='btn btn-primary btn-block edit_single_event' data-edit-id='"+sched.conflict_id+"'>Edit Schedule</button><button data-supplier_id='"+sched.supplier_id+"' data-id='"+sched.conflict_id+"' class='btn btn-danger btn-block btncancelSchedule_reccurent'>Cancel Schedule</button></div></div>")
                    });
                  }
                  
                 }else{
                   $('#response').show();
                   $('#response').html("<div class='alert alert-success'>"+data.success+"</div>")
                    $('#scheduleForm').trigger("reset");
                    $('#ajaxModel').modal('hide');
                    setTimeout(function(){
                      $('#response').hide("slow");
                    },2000)
                    
                    if(data.data != null){

                      $.each(data.data, function(key,value){   
                          window.open("printVoucher/"+value.id,"_blank")
                      });
                    }else{
                      window.open("printVoucher/"+data.id,"_blank")
                    }

                     var current_module = $("#current_module").val()
                     testCalendar(current_module)
                     $('#saveBtn').html('Save Changes');
                 }

                 console.log($('#dataEditID').val())
                 if($('#dataEditID').val() != '0' && $('#dataEditID').val() != ''){
                    $("#modelViewErrorRecurrent").modal({
                      backdrop:'static',
                      keyboard: false
                    })
                 }
              },
              error: function (data) {
                  console.log('Error:', data);
                  $('#saveBtn').html('Save Changes');
              }
            });
          }
    });

    $('body').on('click', '#delete_single_event', function (e) {
        $('#recurrence_modal').hide();
        $('#reason_modal').show();
        $("#isRecurrent").val("0")
        $('#reason').val('')
        //console.log($('#selected_schedule').val())  
        //console.log($('#recurrence_hidden').val())  
    });

    $('body').on('click', '#delete_recurrence', function (e) {
        $('#recurrence_modal').hide();
        $('#reason_modal').show();
        $("#isRecurrent").val("1")
        $('#reason').val('')
        //console.log($('#selected_schedule').val()) 

        //console.log($('#recurrence_hidden').val())   
    });

    $('body').on('click', '#delete_schedule', function (e) {
        var id = $('#selected_schedule').val(); 
        var reason = $('#reason').val(); 
        var isRecurrent = $('#isRecurrent').val(); 
        $('#response').show();
        if (confirm("Are you want to proceed?")){
            $.ajax({
                url: "{{ url('deactivateOrActivateSchedule') }}",
                type: "POST",
                data: {id:id, reason:reason, isRecurrent:isRecurrent},
                success: function (data) {
                    $('#response').html("<div class='alert alert-success'>"+data.success+"</div>")
                    var current_module = $("#current_module").val()
                    testCalendar(current_module)
                    $('#ajaxModelDelete').modal('hide');
                    if($('#dataEditID').val() != ''){
                      $("#modelViewErrorRecurrent").modal({
                        backdrop:'static',
                        keyboard: false
                      })
                    }
                },
                error: function (data) {
                    //console.log('Error:', data);
                }
            });

        } 
    });

    $('body').on('click', '#saveBtnUnavailability', function (e) {


        var ordering_days = [];
        $.each($("#ordering_days_u option:selected"), function(){            
            ordering_days.push($(this).val());
        });
        //var recurrence = $(':radio[name^=recurrence]:checked');
        var recurrence = $('#recurrence_unavailability').children("option:selected");
        var type = $('#type_unavailability').children("option:selected");



        if(recurrence.val() == "Recurrent"){
          if(ordering_days == 0){

          $("#ordering_days_u").css('outline','solid 1px red')
            return false;
          }
          else{
            $("#ordering_days_u").css('outline','solid 1px transparent')
          }
        }


        if($("#dock_id_unavailability").val() ==  "" || recurrence.val() == "" || type.val() == "" || $("#dateOfUnavailability").val() == "" || $("#slotting_time").val() == "" ){

          $("#modalresponse").html("<div class='alert alert-danger'>Please fill in the required fields.</div>")

          $('#modalresponse').fadeIn(1000);
          setTimeout(function(){
            $('#modalresponse').fadeOut(1000);
          },2000)

          

         

           if($("#dock_id_unavailability").val() == "0")
             $("#dock_id_unavailability").css('outline','1px solid red')
           else
             $("#dock_id_unavailability").css('outline','1px solid transparent')

           
           if($("#dateOfUnavailability").val() == "")
             $("#dateOfUnavailability").css('outline','1px solid red')
            else
             $("#dateOfUnavailability").css('outline','1px solid transparent')

           if($("#slotting_time").val() == "")
            $(".slotting_time").css('color','red')
           else
            $(".slotting_time").css('color','black')


            if(recurrence.val() == "")
              $(".recurrence").css('outline','solid 1px red')
             else
              $(".recurrence").css('outline','solid 1px transparent')

            if(type.val() == "")
              $("#type_unavailability").css('outline','solid 1px red')
             else
              $("#type_unavailability").css('outline','solid 1px transparent')

            return false;
        }else{

            e.preventDefault();
            $(this).html('Sending..');
        
            //console.log($('#unavailabilityForm').serialize())
            $.ajax({
              data: $('#unavailabilityForm').serialize(),
              url: "{{ route('ajaxschedules.store') }}",
              type: "POST",
              dataType: 'json',
              success: function (data) {
                if(data.error != undefined && data.error != null){
                  console.log(data)
                  $('.btn_recurrent_close').show();
                  $('#modelViewErrorRecurrent').modal({
                    backdrop:'static',
                    keyboard: false
                  })
                  $('#ajaxModel').modal('hide');
                  if(data.ids.length > 0 ){

                    $("#modelViewErrorRecurrent > .modal-dialog > .modal-content").css('width','960px');
                    $("#modelViewErrorRecurrent > .modal-dialog > .modal-content").css('margin-left','-220px');
                    
                    if(data.type == "Planned"){
                      $.each(data.ids, function(index, sched) {
                      $(".error_recurrent").append("<div class='row'><div class='col-md-6'<p>Type:<b class='alert-danger'>"+data.type+"</b> CONFLICT WITH: Delivery ID: "+sched.id+"</p></div><div class='col-md-6'><button class='btn btn-primary btn-block edit_single_event' data-edit-id='"+sched.id+"'>Edit Schedule</button><button data-id='"+sched.id+"' class='btn btn-danger btn-block btncancelSchedule_reccurent'>Cancel Schedule</button></div></div>")
                      });
                    }

                    if(data.type == "Unplanned"){
                      $.each(data.ids, function(index, sched) {
                      $(".error_recurrent").append("<div class='row'><div class='col-md-6'<p>Type:<b class='alert-danger'>"+data.type+"</b> CONFLICT WITH: Delivery ID: "+sched.id+"</p></div><div class='col-md-6'><button class='btn btn-primary btn-block edit_single_event' data-edit-id='"+sched.id+"'>Edit Schedule</button><button data-id='"+sched.id+"' class='btn btn-danger btn-block btncancelSchedule_reccurent'>Cancel Schedule</button></div></div>")
                      });
                    }

                    
                  }
                  
                 }

                if(data.success){
                 $('#response').html("<div class='alert alert-success'>"+data.success+"</div>")
                }
                  $('#unavailabilityForm').trigger("reset");
                  $('#ajaxModelUnavailability').modal('hide');
                  setTimeout(function(){
                    $('#response').hide("slow");
                  },2000)
                  

                  var current_module = $("#current_module").val()
                   testCalendar(current_module)
                   $('#saveBtn').html('Save Changes');
              },
              error: function (data) {
                  //console.log('Error:', data);
                  $('#saveBtn').html('Save Changes');
              }
            });
          }
    });
    
  });

  $('body').on('mousedown','.fc-time-grid-event',function(event) { 
            switch (event.which) { 
                case 1: 
                    //console.log('left mouse button');
                    break; 
                case 2: 
                    //console.log('middle mouse button');
                    break; 
                case 3: 

                    // $(this).contextmenu(function() {
                    //     return false;
                    // });

                    $('.fc-time-grid-event').css('border-color','transparent');
                    $(this).css('border-color','red');
                    $(this).css('border-width','2');
                    //console.log($('#selected_schedule').val());
                    return false;
                    break; 
                default: 
                    break; 
            } 
    }); 

    $('body').on('click', '#print-schedule', function () {
          printDiv('forPrint');
    });

    // material list

   // ARRAY FOR HEADER.
    var arrHead = new Array();
    arrHead = ['', '','GCAS', 'Description', 'Quantity(UOM)'];      // SIMPLY ADD OR REMOVE VALUES IN THE ARRAY FOR TABLE HEADERS.

    // FIRST CREATE A TABLE STRUCTURE BY ADDING A FEW HEADERS AND
    // ADD THE TABLE TO YOUR WEB PAGE.
    function createTable() {
        var materialTable = document.createElement('table');
        materialTable.setAttribute('id', 'materialTable');
        materialTable.setAttribute('class', 'table table-condensed');                // SET THE TABLE ID.

        var tr = materialTable.insertRow(-1);

        for (var h = 0; h < arrHead.length; h++) {
            var th = document.createElement('th');          // TABLE HEADER.
            th.innerHTML = arrHead[h];
            tr.appendChild(th);
        }

        var div = document.getElementById('cont');
        div.appendChild(materialTable);    // ADD THE TABLE TO YOUR WEB PAGE.
    }
    // ADD A NEW ROW TO THE TABLE.s
    var count_row = 0
    function addRow(gcas,description,qty) {
        var materialTab = document.getElementById('materialTable');

        var rowCnt = materialTab.rows.length;        // GET TABLE ROW COUNT.
        var tr = materialTab.insertRow(rowCnt);      // TABLE ROW.
        tr = materialTab.insertRow(rowCnt);
        
        for (var c = 0; c < arrHead.length; c++) {
            var td = document.createElement('td');          // TABLE DEFINITION.
            td = tr.insertCell(c);

            if (c == 0) {           // FIRST COLUMN.
                // ADD A BUTTON.
                if(count_row != 0){
                  var button = document.createElement('a');
                  
                  // SET INPUT ATTRIBUTE.
                  button.setAttribute('style', 'cursor:pointer;font-size:14px');
                  button.text = "❌"
                // ADD THE BUTTON's 'onclick' EVENT.
                
                  button.setAttribute('onclick', 'removeRow(this)');
                  td.appendChild(button);
                }


            }if (c == 1) {           // FIRST COLUMN.
                // ADD A BUTTON.
                var button2 = document.createElement('a');
                  
                  // SET INPUT ATTRIBUTE.
                  button2.setAttribute('style', 'cursor:pointer;font-size:14px');
                  button2.text = "➕"

                // ADD THE BUTTON's 'onclick' EVENT.
                button2.setAttribute('onclick', 'addRow("","","","")');


                td.appendChild(button2);
            }

            else if(c== 2){
                var ele = document.createElement('input');
                ele.setAttribute('type', 'text');
                ele.setAttribute('class', 'gcas');
                ele.setAttribute('name', 'gcas[]');
                ele.setAttribute('value', gcas);

                td.appendChild(ele);
            }else if(c== 3){
                var ele = document.createElement('input');
                ele.setAttribute('type', 'text');
                ele.setAttribute('class', 'description');
                ele.setAttribute('name', 'description[]');
                ele.setAttribute('value', description);

                td.appendChild(ele);
            }
            else if(c== 4){
                var ele = document.createElement('input');
                ele.setAttribute('type', 'text');
                ele.setAttribute('class', 'quantity');
                ele.setAttribute('name', 'quantity[]');
                //ele.setAttribute('onkeyup', 'addRow()');
                ele.setAttribute('value', qty);

                td.appendChild(ele);
            }
        }

        count_row++
                
    }

    // DELETE TABLE ROW.
    function removeRow(oButton) {
        var materialTab = document.getElementById('materialTable');
        materialTab.deleteRow(oButton.parentNode.parentNode.rowIndex);       // BUTTON -> TD -> TR.
    }

    // EXTRACT AND SUBMIT TABLE DATA.
    function submit() {
        var myTab = document.getElementById('materialTable');
        var values = new Array();

        // LOOP THROUGH EACH ROW OF THE TABLE.
        for (row = 1; row < myTab.rows.length - 1; row++) {
            for (c = 0; c < myTab.rows[row].cells.length; c++) {   // EACH CELL IN A ROW.

                var element = myTab.rows.item(row).cells[c];
                if (element.childNodes[0].getAttribute('type') == 'text') {
                    values.push("'" + element.childNodes[0].value + "'");
                }
            }
        }
        //console.log(values);
    }

      // material list end

    // for print

    function printDiv(divName) {
       var printContents = document.getElementById(divName).innerHTML;
       var originalContents = document.body.innerHTML;

       document.body.innerHTML = printContents;

       window.print();

       document.body.innerHTML = originalContents;
  }

    // end for print


    //CANCEL SCHEDULE BUTTON

    $('body').on("click",".btncancelSchedule",function(e){
          e.preventDefault();
          $('#response').html(''); 

          var id = $('#selected_schedule').val();
          var selected = $('#selected_supplierid').val();
          $('#response').show()
          $('#single_event_modal').hide();
          $('#recurrence_modal').hide();
          $('#reason_modal').hide();
          $('#ajaxModel').modal('hide');
          var current_recurrence = $('#recurrence_hidden').val()
          //console.log(current_recurrence == "Single Event")
          if(current_recurrence == "Single Event"){
             $('#reason_modal').show();
             $('#single_event_modal').hide();
            $('#recurrence_modal').hide();
          }else if(current_recurrence == "Recurrent"){
              $('#single_event_modal').hide();
              $('#recurrence_modal').show();
              $('#reason_modal').hide();
            
          }
          

        $   ("#isRecurrent").val("0")
          $('#modelHeadingDelete').html(current_recurrence);
          
          $('#ajaxModelDelete').modal({
            backdrop:'static',
            keyboard: false
          })
    });

     $('body').on("click",".btncancelSchedule_reccurent",function(e){

          e.preventDefault();
          $('#response').html(''); 
          $('#selected_schedule').val($(this).attr('data-id'))

          $('#dataEditID').val($(this).attr('data-id'))
          $('#selected_supplierid').val($(this).attr('data-supplier_id'))
          var id = $('#selected_schedule').val();
          var selected = $('#selected_supplierid').val();
          $('#response').show()
          $('#single_event_modal').hide();
          $('#recurrence_modal').hide();
          $('#reason_modal').hide();
          $('#modelViewErrorRecurrent').modal('hide');
          $('#reason_modal').show();
          $('#single_event_modal').hide();
          $('#recurrence_modal').hide();

          $("#isRecurrent").val("0")
          
          $('#ajaxModelDelete').modal({
            backdrop:'static',
            keyboard: false
          })
    });
    //END CANCEL SCHEDULE BUTTON

    //new window
    $('body').on('click','#btnPrintVoucher',function(){

      var id = $(this).attr("data-id");
      window.open("printVoucher/"+id,"_blank")
    });

    //end new window
    setTimeout(function(){
      $(".fc-center").appendTo(".button-area");
      $(".fc-CreateSchedule-button").addClass('btn-block');
      $(".fc-ScheduleDockUnavailability-button").addClass('btn-block');
      $("#calendar > div.fc-toolbar.fc-header-toolbar > div.fc-left > h2").text($("#calendar > div.fc-toolbar.fc-header-toolbar > div.fc-left > h2").text().replace("â€“"," \u2013 "))
    },100);
    
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
          <div class="col-xl-3 button-area group-module">
          </div>
           <div class="col-xl-6 group-module">

            <input type="hidden" name="selected_schedule" id="selected_schedule">
            <input type="hidden" name="selected_supplierid" id="selected_supplierid">
            <input type="hidden" name="current_module" id="current_module">
            <input type="hidden" name="recurrence_hidden" id="recurrence_hidden">
            <div class="form-group">
               <label for="name" class="col-sm-12 control-label"><h4>Current Dock: <b id="current_dock"></b></h4> </label>

               <div class="col-sm-12">
                  <select class="form-control btn-modules-dropdown" id="btn-modules-dropdown">
                       <option value="">Please select Dock</option>
                       @foreach($json_data['dockData']['data'] as $dock)
                         <option value='{{ $dock->dock_name }}'>{{ $dock->dock_name }}</option>
                       @endforeach
                  </select>
                </div>
            </div>
            <!-- <div class="btn-group btn-group-sm float-right" role="group" aria-label="Basic example">
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
            </div>-->
          </div>
        </div>
        <div id='calendar'></div>

    </div>
  </div>
</div>
<!-- /.container-fluid -->

<!--Scheduling Modal-->
<div class="modal fade" id="ajaxModel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="width:970px;margin-left:-230px;">
            <div class="modal-header">
                <h4 class="modal-title" id="modelHeading"></h4>

                <button type="button" class="close" data-dismiss="modal">&times;</button> 
            </div>
            <div class="modal-body">
                <form id="scheduleForm" name="scheduleForm" class="form-horizontal">
                    
                    <input type="hidden" name="schedule_id" id="schedule_id">
                     <input type="hidden" name="isEditedFinalized" id="isEditedFinalized">
                     <input type="hidden" value="0" name="isForUnavailability" id="isForUnavailability">
                     <input type="hidden" value="0" name="isEditingRecurrent" id="isEditingRecurrent">
                     <input type="hidden" value="0" name="isEditingSingle" id="isEditingSingle">

                     <input type="hidden" value="0" name="dataEditID" id="dataEditID">

                     <!-- First Row -->
                     <div class="row">

                        <!-- First Column -->
                       <div class="col-md-4">
                          
                          <!-- PO Number -->
                          <div class="form-group">
                              <label for="name" class="col-sm-12 control-label">*PO Number</label>
                              <div class="col-sm-12">
                                  <input type="text" class="form-control" id="po_number" name="po_number" placeholder="Enter PO Number" value="" maxlength="100" required="">
                              </div>
                          </div>


                          <!-- Truck -->
                          <div class="form-group">
                             <label for="name" class="col-sm-12 control-label">*Truck</label>
                             <div class="col-sm-12">
                                <select  class="form-control" id="truck_id" name="truck_id">
                                  <option value="0">Please select Truck</option>
                                </select>
                              </div>
                          </div>

                       </div>

                        <!-- Second Column -->
                        <div class="col-md-4">

                              <!-- Supplier -->
                              <div class="form-group">
                                 <label for="name" class="col-sm-12 control-label">*Supplier</label>
                                 <div class="col-sm-12">
                                    <select required class="form-control" id="supplier_id" name="supplier_id">
                                        <option value="0">Please select Supplier</option>
                                       @foreach($json_data['supplierData']['data'] as $supplier)
                                         <option value='{{ $supplier->id }}'>{{ $supplier->supplier_name }}</option>
                                       @endforeach
                                    </select>
                                    <input type="hidden" name="alt_supplier_id" id="alt_supplier_id">
                                  </div>
                              </div>

                              <!-- Container No -->
                              <div class="form-group">
                                  <label for="name" class="col-sm-12 control-label">*Container No.</label>
                                  <div class="col-sm-12">
                                      <input type="text" class="form-control" id="container_number" name="container_number" placeholder="Enter Container Number" value="" maxlength="100" required="">
                                  </div>
                              </div>



                             
                        </div>

                        <!-- Third Column -->
                        <div class="col-md-4">

                            <!-- Driver -->
                            <div class="form-group">
                               <label for="name" class="col-sm-12 control-label">*Driver</label>
                               <div class="col-sm-12">
                                  <select  class="form-control" id="driver_id" name="driver_id">
                                    <option value="0">Please select Driver</option>
                                  </select>
                                </div>
                            </div>


                            <!-- Assistant -->
                            <div class="form-group">
                               <label for="name" class="col-sm-12 control-label">*Assistant</label>
                               <div class="col-sm-12">
                                  <select  class="form-control" id="assistant_id" name="assistant_id">
                                    <option value="0">Please select Assistant</option>
                                  </select>
                                </div>
                            </div>

                        </div>

                        
                     </div>

                     <!-- Second Row -->
                     <div class="row">
                         <!-- First Column -->
                       <div class="col-md-4">
                          
                          <!-- Dock -->
                          <div class="form-group">
                             <label for="name" class="col-sm-12 control-label">*Dock</label>
                             <div class="col-sm-12">
                                <select required class="form-control" id="dock_id" name="dock_id">
                                   <option value="0">Please select Dock</option>
                                </select>
                              </div>
                          </div>

                       </div>

                        <!-- Second Column -->
                        <div class="col-md-4">



                              <!-- Date of delivery -->
                              <div class="form-group">
                                <label class="col-sm-12 control-label">*Date of Delivery</label>
                                <div class="col-sm-12">
                                  <input type="date" class="form-control datepicker" name="dateOfDelivery" id="dateOfDelivery" required="">
                                </div>
                              </div>

                              <!-- Ordering days -->
                              <div class="form-group r_ordering_days">
                                 <label for="name" class="col-sm-12 control-label">*Ordering Days</label>
                                 <div class="col-sm-12">
                                    <select multiple="true" class="form-control ordering_days" id="ordering_days" name="ordering_days[]">
                                         <option value="Mon">Mon</option>
                                         <option value="Tue">Tue</option>
                                         <option value="Wed">Wed</option>
                                         <option value="Thu">Thu</option>
                                         <option value="Fri">Fri</option>
                                         <option value="Sat">Sat</option>
                                         <option value="Sun">Sun</option>
                                    </select>
                                  </div>
                              </div>

                        </div>

                        <!-- Third Column -->
                        <div class="col-md-4">

                            

                              <!-- Recurrent -->
                              <div class="form-group">
                                 <label for="name" class="col-sm-12 control-label">*Recurrence</label>
                                 <div class="col-sm-12">
                                    <select class="form-control recurrence" id="recurrence" name="recurrence">
                                         <option value="">Please select Recurrence</option>
                                         <option value="Single Event">Single Event</option>
                                         <option value="Recurrent">Recurrent</option>
                                    </select>
                                  </div>
                              </div>

                              <!-- Valid Until -->
                              <div class="form-group r_recurrent_dateend">
                                <label class="col-sm-12 control-label">*Recurrent Date Valid Until</label>
                                <div class="col-sm-12">
                                  <div class="col-sm-12">
                                  <input type="date" class="form-control datepicker" name="recurrent_dateend" id="recurrent_dateend">
                                  </div>
                                </div>
                              </div>

                        </div>

                     </div>

                     <div class="row">
                       
                        <div class="col-md-12">
                          <!-- Slotting Time -->
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
                                        <br>
                                        <div class="slot_box" id="slot9">04:00 - 04:30</div>
                                        <div class="slot_box" id="slot10">04:30 - 05:00</div>
                                        <div class="slot_box" id="slot11">05:00 - 05:30</div>
                                        <div class="slot_box" id="slot12">05:30 - 06:00</div>
                                        <div class="slot_box" id="slot13">06:00 - 06:30</div>
                                        <div class="slot_box" id="slot14">06:30 - 07:00</div>
                                        <div class="slot_box" id="slot15">07:00 - 07:30</div>
                                        <div class="slot_box" id="slot16">07:30 - 08:00</div>
                                        <br>
                                        <div class="slot_box" id="slot17">08:00 - 08:30</div>
                                        <div class="slot_box" id="slot18">08:30 - 09:00</div>
                                        <div class="slot_box" id="slot19">09:00 - 09:30</div>
                                        <div class="slot_box" id="slot20">09:30 - 10:00</div>
                                        <div class="slot_box" id="slot21">10:00 - 10:30</div>
                                        <div class="slot_box" style="padding-right:15px !important;" id="slot22">10:30 - 11:00</div>
                                        <div class="slot_box" style="padding-right:16px !important;" id="slot23">11:00 - 11:30</div>
                                        <div class="slot_box" style="padding-right:15px !important;" id="slot24">11:30 - 12:00</div>
                                        <br>
                                        <div class="slot_box" id="slot25">12:00 - 12:30</div>
                                        <div class="slot_box" id="slot26">12:30 - 13:00</div>
                                        <div class="slot_box" id="slot27">13:00 - 13:30</div>
                                        <div class="slot_box" id="slot28">13:30 - 14:00</div>
                                        <div class="slot_box" id="slot29">14:00 - 14:30</div>
                                        <div class="slot_box" id="slot30">14:30 - 15:00</div>
                                        <div class="slot_box" id="slot31">15:00 - 15:30</div>
                                        <div class="slot_box" id="slot32">15:30 - 16:00</div>
                                        <br>
                                        <div class="slot_box" id="slot33">16:00 - 16:30</div>
                                        <div class="slot_box" id="slot34">16:30 - 17:00</div>
                                        <div class="slot_box" id="slot35">17:00 - 17:30</div>
                                        <div class="slot_box" id="slot36">17:30 - 18:00</div>
                                        <div class="slot_box" id="slot37">18:00 - 18:30</div>
                                        <div class="slot_box" id="slot38">18:30 - 19:00</div>
                                        <div class="slot_box" id="slot39">19:00 - 19:30</div>
                                        <div class="slot_box" id="slot40">19:30 - 20:00</div>
                                        <br>
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
                          
                        </div>

                     </div>

                    

                    

                    
                    
                    <!-- <div class="form-group">
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
                    </div> -->

                    

                   <!--  <div class="form-group r_ordering_days">
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
                    </div> -->
                    
                    <div class="form-group">
                       <label for="name" class="col-sm-12 control-label">*Material List</label>
                       <div class="col-sm-12">
                            <div id="cont"></div>
                        </div>
                    </div>

                    <div id="modalresponse"></div> 
                    <div class="row">
                      <div class="offset-3 col-md-3">
                         <button type="submit" class="btn btn-success btn-block" id="btnPrintVoucher" value="create">Print
                         </button>
                      </div>
                      <div class="col-md-3">
                         <button type="submit" class="btn btn-danger btn-block btncancelSchedule" value="create">Cancel Schedule
                         </button>
                      </div>
                      <div class="col-md-3">
                         <button type="submit" class="btn btn-primary btn-block" id="saveBtn" value="create">Save changes
                         </button>
                      </div>
                      
                    </div>  

                </form>

            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="ajaxModelDelete" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modelHeadingDelete"></h4>

                <button type="button" class="close" data-dismiss="modal">&times;</button> 
            </div>
            <div id="recurrence_modal" class="modal-body">
                <p id="reccurent">You are deleting a recurrent schedule. Are you deleting this single event schedule of the entire schedule recurrence?</p>
                <div class="row">
                  <div class="col-xl-6">
                    <button id="delete_single_event" style="width:220px;"class="btn btn-primary">Delete Single <br> Event Schedule</button>
                  </div>
                  <div class="col-xl-6">
                    <button id="delete_recurrence" style="width:220px;"class="btn btn-primary">Delete Recurrence <br> Schedule</button>
                  </div>
                </div>
                <br>
                <div class="row">
                  <div class="col-xl-12">
                    <button class="btn btn-secondary btn-block" data-dismiss="modal">Close</button>
                  </div>
                </div>
            </div>
            <div id="reason_modal" class="modal-body">
                <p id="enter_reason">Enter reason for deleting schedules</p>
                <div class="row">
                  <div class="col-xl-12">
                    <textarea class="form-control" id="reason"></textarea>
                    <input type="hidden" id="isRecurrent">
                    <br>
                    <button id="delete_schedule" class="btn btn-primary btn-block">Cancel Schedule</button>
                    <button class="btn btn-secondary btn-block btn-block-sched" data-dismiss="modal">Cancel</button>
                  </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="ajaxModelView" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="width:950px;">
            <div class="modal-header">
                <h4 class="modal-title" id="modelHeadingView">View Schedule</h4>

                <button type="button" class="close" data-dismiss="modal">&times;</button> 
            </div>
            <div class="modal-body">
              <div id="forPrint" >
                <div class="row">
                  
                    
                  <div class="col-xl-12 text-center">
                    <div class="col-xl-12 text-center">
                      <img style="width: 200px;" src="{{ asset('images/logo_png.png') }}">
                    </div>
                     <b>Procter & Gamble Philippines, Incorporated<br>
                    Cabuyao Plant</b>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-12">
                    <h1>TRAMS</h1>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-2">
                    <img id="img_qrcode" style="width:130px; height:130px; margin-top:15px;">
                    <div id="qrcode" style="width:10px; height:10px; margin-top:15px;"></div>
                    <input type="hidden" id="dataUrlQRCode">
                  </div>
                  <div class="col-md-4">
                    Delivery ID
                    <br>
                    <h1><b><div id="view_delivery_id"></div></b></h1>
                    <h5><b>P.O. Number : </b><b id="view_po_number"></b></h5>
                  </div>
                  <div class="col-md-6">
                     
                      <!-- supplier -->
                      <div class="row">
                        <div class="col-md-6" style="line-height: 0px">
                          Supplier:
                        </div>
                        <div class="col-md-6" style="line-height: 0px">
                          <b><p id="view_supplier_name"></p></b>
                        </div>
                     
                        <div class="col-md-6" style="line-height: 0px">
                          Truck:
                        </div>
                        <div class="col-md-6" style="line-height: 0px">
                          <b><p id="view_truck"></p></b>
                        </div>
                   
                        <div class="col-md-6" style="line-height: 0px">
                          Plate Number:
                        </div>
                        <div class="col-md-6" style="line-height: 0px">
                          <b><p id="view_plate_number"></p></b>
                        </div>
                    
                        <div class="col-md-6" style="line-height: 0px">
                          Container Number:
                        </div>
                        <div class="col-md-6" style="line-height: 0px">
                          <b><p id="view_container_no"></p></b>
                        </div>
                     
                        <div class="col-md-6" style="line-height: 0px">
                          Driver:
                        </div>
                        <div class="col-md-6" style="line-height: 0px">
                          <b><p id="view_driver_name"></p></b>
                        </div>
                    
                        <div class="col-md-6" style="line-height: 0px">
                          Assistant:
                        </div>
                        <div class="col-md-6" style="line-height: 0px">
                          <b><p id="view_assistant"></p></b>
                        </div>
                      </div>
                  </div>
                </div>
                <hr>
                <div class="row">
                  <div class="col-md-4 text-center">
                    <h5>Dock:</h5> 
                    <h4><b><div id="view_dock_name"></div></b></h4>
                  </div>
                  <div class="col-md-4 text-center">
                    <h5>Date of Delivery:</h5>
                    <h4><b><div id="view_date_of_delivery"></div></b></h4>
                  </div>
                  <div class="col-md-4 text-center">
                    <h5>Slotting Time:</h5>
                    <h4><b><div id="view_slotting_time"></div></b></h4>
                  </div>
                </div>
                 <hr>
                <div class="row">
                  
                  <div class="col-md-12 text-center">
                      <h5>Material List:</h5>
                      <div id="view_material_list"></div>
                  </div>
                </div>
                
            </div> 
                
                <br>
                <div class="row">
                  <div class="col-md-6">
                    <button class="btn btn-success btn-xs btn-block" type="button" id="print-schedule">Print</button> 
                  </div>
                  <div class="col-md-6">
                    <button class="btn btn-secondary btn-xs btn-block" type="button" data-dismiss="modal">Close</button> 
                  </div>
                </div>
                
                
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="ajaxModelUnavailability" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modelHeadingUnavailability"></h4>

                <button type="button" class="close" data-dismiss="modal">&times;</button> 
            </div>
            <div class="modal-body">
                <div id="modalresponse"></div> 
                <form id="unavailabilityForm" name="unavailabilityForm" class="form-horizontal">
                    
                    <input type="hidden" name="unavailability_id" id="unavailability_id">
                     <input type="hidden" value="1" name="isForUnavailability" id="isForUnavailability">
                     <input type="hidden" name="isEditedFinalized" id="isEditedFinalized">

                   

                    <div class="form-group">
                       <label for="name" class="col-sm-12 control-label">*Dock</label>
                       <div class="col-sm-12">
                          <select  class="form-control" id="dock_id_unavailability" name="dock_id_unavailability">
                             <option value="0">Please select Dock</option>
                             @foreach($json_data['dockData']['data'] as $dock)
                               <option value='{{ $dock->id }}'>{{ $dock->dock_name }}</option>
                             @endforeach
                          </select>
                        </div>
                    </div>

                    <div class="form-group">
                      <label class="col-sm-12 control-label">*Date of Unavailability</label>
                      <div class="col-sm-12">
                        <div class="col-sm-12">
                        <input type="date" class="form-control datepicker" name="dateOfUnavailability" id="dateOfUnavailability" required="">
                        </div>
                      </div>
                    </div>
                    
                   <!--  <div class="form-group">
                      <label class="col-sm-12 control-label recurrence">*Recurrence</label>
                      <div class="col-sm-12">
                        <div class="form-check form-check-inline">
                          <input class="form-check-input" type="radio" name="recurrence_unavailability" class="recurrence" data-id="single" value="Single Event">
                          <label class="form-check-label" for="inlineRadio1">Single Event</label>
                        </div>
                        <div class="form-check form-check-inline">
                          <input class="form-check-input" type="radio" name="recurrence_unavailability" class="recurrence" data-id="recurrent" value="Recurrent">
                          <label class="form-check-label" for="inlineRadio1">Recurrent</label>
                        </div>
                      </div>
                    </div> -->

                    <div class="form-group">
                       <label for="name" class="col-sm-12 control-label">*Recurrence</label>
                       <div class="col-sm-12">
                          <select class="form-control recurrence" id="recurrence_unavailability" name="recurrence_unavailability">
                               <option value="">Please select Recurrence</option>
                               <option value="Single Event">Single Event</option>
                               <option value="Recurrent">Recurrent</option>
                          </select>
                        </div>
                    </div>

                    <div class="form-group r_ordering_days_u">
                       <label for="name" class="col-sm-12 control-label">*Ordering Days</label>
                       <div class="col-sm-12">
                          <select multiple="true" class="form-control ordering_days_u" id="ordering_days_u" name="ordering_days[]">
                               <option value="Mon">Mon</option>
                               <option value="Tue">Tue</option>
                               <option value="Wed">Wed</option>
                               <option value="Thu">Thu</option>
                               <option value="Fri">Fri</option>
                               <option value="Sat">Sat</option>
                               <option value="Sun">Sun</option>
                          </select>
                        </div>
                    </div>


                    <div class="form-group r_recurrent_dateend">
                      <label class="col-sm-12 control-label">*Recurrent Date End</label>
                      <div class="col-sm-12">
                        <div class="col-sm-12">
                        <input type="date" class="form-control datepicker" name="recurrent_dateend" id="recurrent_dateend" required="">
                        </div>
                      </div>
                    </div>

                    <div class="form-group">
                      <label class="col-sm-12 control-label slotting_time">*Slotting Time</label>
                      <input type="hidden" class="form-control" id="slotting_time_unavailability" name="slotting_time_unavailability" required="">
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
                              <div class="slot_box" style="padding-right:16px !important;" id="slot22">10:30 - 11:00</div>
                              <div class="slot_box" style="padding-right:15px !important;" id="slot23">11:00 - 11:30</div>
                              <div class="slot_box" style="padding-right:15px !important;" id="slot24">11:30 - 12:00</div>
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

                    <!-- <div class="form-group">
                      <label class="col-sm-12 control-label recurrence">*Type</label>
                      <div class="col-sm-12">
                        <div class="form-check form-check-inline">
                          <input class="form-check-input" type="radio" name="type_unavailability" class="recurrence" data-id="single" value="Planned">
                          <label class="form-check-label" for="inlineRadio1">Planned</label>
                        </div>
                        <div class="form-check form-check-inline">
                          <input class="form-check-input" type="radio" name="recurrence_unavailability" class="recurrence" data-id="recurrent" value="Recurrent">
                          <label class="form-check-label" for="inlineRadio1">Unplanned</label>
                        </div>
                      </div>
                    </div> -->

                    <div class="form-group">
                       <label for="name" class="col-sm-12 control-label">*Type</label>
                       <div class="col-sm-12">
                          <select class="form-control type_unavailability" id="type_unavailability" name="type_unavailability">
                               <option value="">Please select options</option>
                               <option value="Planned">Planned</option>
                               <option value="Unplanned">Unplanned</option>
                          </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="name" class="col-sm-12 control-label">*Reason</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="reason_unavailability" name="reason_unavailability" placeholder="Enter Reason" value="" required="">
                        </div>
                    </div>

                    <div class="col-sm-offset-2 col-sm-10">
                       <button type="submit" class="btn btn-primary" id="saveBtnUnavailability" value="create">Save changes
                       </button>
                    </div>

                </form>

            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modelViewMaterialList" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modelHeadingViewMaterialList">View Schedule</h4>

                <button type="button" class="close" data-dismiss="modal">&times;</button> 
            </div>
            <div class="modal-body">
                <div class="material_list_details"></div>
                
                <br>
                <button class="btn btn-secondary btn-xs btn-block" type="button" data-dismiss="modal">Close</button> 
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="ajaxModelEditRecurrent" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modelHeadingEditRecurrent">Edit  Schedule</h4>

                <button type="button" class="close" data-dismiss="modal">&times;</button> 
            </div>
            <div id="recurrence_modal" class="modal-body">
                <p id="reccurent">You are editing a recurrent schedule. Are you editing this single event schedule of the entire schedule recurrence?</p>
                <div class="row">
                  <div class="col-xl-6">
                    <button id="edit_single_event" style="width:220px;"class="btn btn-primary">Edit Single <br> Event Schedule</button>
                  </div>
                  <div class="col-xl-6">
                    <button id="edit_recurrence" style="width:220px;"class="btn btn-primary">Edit Recurrence <br> Schedule</button>
                  </div>
                </div>
                <br>
                <div class="row">
                  <div class="col-xl-12">
                    <button class="btn btn-secondary btn-block" data-dismiss="modal">Close</button>
                  </div>
                </div>
            </div>
            
        </div>
    </div>
</div>

<div class="modal fade" id="modelViewErrorRecurrent" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Create Schedule - Error Recurrent Schedule</h4>

               <!--  <button type="button" class="close" data-dismiss="modal">&times;</button> --> 
            </div>
            <div class="modal-body">
                <div class="text-center"><p>The following schedules part of this recurrent schedule cannot be posted due to conflicts with other posted schedules.</p></div>
                <div class="error_recurrent"></div>
                
                <br>
                <button class="btn btn-secondary btn-xs btn-block btn_recurrent_close" type="button" data-dismiss="modal">Close</button> 
            </div>
        </div>
    </div>
</div>
  

@endsection
