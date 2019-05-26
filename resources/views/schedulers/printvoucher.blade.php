@extends('layouts.schedulingapp')

@section('content')
<script src="{{ asset('js/jquery.qrcode.js') }}"></script>
<script src="{{ asset('js/qrcode.js') }}" ></script>
<script type="text/javascript">
   //make qr code
   function makeCode(){
     $("#dataUrlQRCode").val('');
      jQuery('#qrcode').qrcode({width: 120,height:120,text: "{{ $json_data['id'] }}"});

     var canvas = document.querySelector("#qrcode > canvas");
     var dataURL = canvas.toDataURL();
     //$("#dataUrlQRCode").val(dataURL);
     $('#img_qrcode').attr("src",dataURL);
      $("#qrcode").html('')
   }
    
    //end make qr code

    function formatAMPM(date) {
      console.log(date)
      var hours = date.getHours();
      var minutes = date.getMinutes();
      var ampm = hours >= 12 ? 'PM' : 'AM';
      hours = hours % 12;
      hours = hours ? hours : 12; // the hour '0' should be '12'
      minutes = minutes < 10 ? '0'+minutes : minutes;
      var strTime = hours + ':' + minutes + ' ' + ampm;
      return strTime;
    }

    $('body').on('click', '#print-schedule', function () {
          printDiv('forPrint');
    });

    // for print

    function printDiv(divName) {
       var printContents = document.getElementById(divName).innerHTML;
       var originalContents = document.body.innerHTML;

       document.body.innerHTML = printContents;

       window.print();

       document.body.innerHTML = originalContents;
  }

    // end for print



//$('#recurrence_hidden').val(info.event.extendedProps.recurrence)


  setTimeout(function(){
    var start = new Date("{{ $json_data['start'] }}").toLocaleTimeString().replace(/([\d]+:[\d]{2})(:[\d]{2})(.*)/, "$1$3")
    var end = new Date("{{ $json_data['end']}}").toLocaleTimeString().replace(/([\d]+:[\d]{2})(:[\d]{2})(.*)/, "$1$3")
    $("#view_slotting_time").append(start + " - " + end)
    makeCode();
  },500) 

</script>
<div class="container-fluid">

  <!-- Breadcrumbs-->
  <ol class="breadcrumb">
    <li class="breadcrumb-item">
      <a href="#">Dashboard</a>
    </li>
    <li class="breadcrumb-item active">Scheduler</li>
    <li class="breadcrumb-item">Print Voucher</li>
  </ol>
  <div class="row">
    <div class="col-xl-12 col-sm-12 mb-3">
      <h1>Print Voucher</h1>
        <div id="response"></div>
        <div class="row">
        </div>

    </div>
  </div>
</div>
<!-- /.container-fluid -->
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
        <h1>TRAMS </h1>
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
        <h1><b><div id="view_delivery_id">{{ $json_data['id'] }}</div></b></h1>
        <h5><b>P.O. Number : </b><b id="view_po_number">{{ $json_data['po_number'] }}</b></h5>
      </div>
      <div class="col-md-6">
         
          <!-- supplier -->
          <div class="row">
            <div class="col-md-6" style="line-height: 0px">
              Supplier:
            </div>
            <div class="col-md-6" style="line-height: 0px">
              <b><p id="view_supplier_name">{{ $json_data['supplier_name'] }}</p></b>
            </div>
         
            <div class="col-md-6" style="line-height: 0px">
              Truck:
            </div>
            <div class="col-md-6" style="line-height: 0px">
              <b><p id="view_truck">{{ $json_data['truck_details'] }}</p></b>
            </div>
       
            <div class="col-md-6" style="line-height: 0px">
              Plate Number:
            </div>
            <div class="col-md-6" style="line-height: 0px">
              <b><p id="view_plate_number">{{ $json_data['plate_number'] }}</p></b>
            </div>
        
            <div class="col-md-6" style="line-height: 0px">
              Container Number:
            </div>
            <div class="col-md-6" style="line-height: 0px">
              <b><p id="view_container_no">{{ $json_data['container_no'] }}</p></b>
            </div>
         
            <div class="col-md-6" style="line-height: 0px">
              Driver:
            </div>
            <div class="col-md-6" style="line-height: 0px">
              <b><p id="view_driver_name">{{ $json_data['driver_name'] }}</p></b>
            </div>
        
            <div class="col-md-6" style="line-height: 0px">
              Assistant:
            </div>
            <div class="col-md-6" style="line-height: 0px">
              <b><p id="view_assistant">{{ $json_data['assistant_name'] }}</p></b>
            </div>
          </div>
      </div>
    </div>
    <hr>
    <div class="row">
      <div class="col-md-4 text-center">
        <h5>Dock:</h5> 
        <h4><b><div id="view_dock_name">{{ $json_data['dock_name'] }}</div></b></h4>
      </div>
      <div class="col-md-4 text-center">
        <h5>Date of Delivery:</h5>
        <h4><b><div id="view_date_of_delivery">{{ $json_data['date_of_delivery'] }}</div></b></h4>
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
          <div id="view_material_list">{!! $json_data['material_list'] !!}</div>
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



  

@endsection
