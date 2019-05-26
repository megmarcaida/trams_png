@extends('layouts.datatableapp')

@section('content')

<div class="container-fluid">

  <!-- Breadcrumbs-->
  <ol class="breadcrumb">
    <li class="breadcrumb-item">
      <a href="#">Dashboard</a>
    </li>
    <li class="breadcrumb-item">Record Maintenance</li>
    <li class="breadcrumb-item active">Drivers</li>
  </ol>

  <div class="row">
    <div class="col-xl-12 col-sm-12 mb-3">
      <h1>Drivers</h1>

        <div class="row">
          <div class="col-xl-6">
            @if(Auth::user()->role_id == 1 || Auth::user()->role_id == 2)         
               <a class="btn btn-success" href="javascript:void(0)" id="createNewProduct"> Register Driver</a>
            @elseif(Auth::user()->role_id == 3)
               <a class="btn btn-success" href="javascript:void(0)" id="viewPendingRegistration"> View Pending Registrations</a>       
            @endif

            <a class="btn btn-warning" href="{{ route('exportDriver') }}">Export Drivers Data</a>
          </div>
          <div class="col-xl-3">
              <form action="{{ route('importDriver') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="file" name="file" class="form-control">
                <br>
                <button class="btn btn-success text-right">Import Drivers Data</button>
                <a class="btn btn-secondary" href="{{ route('exportDriver') }}">Download Template Data</a>
            </form> 
          </div>
        </div>
        <br> <br>
        <div id="response">
          @if(session()->has('import_message'))
            <div class="alert alert-success">
                {{ session()->get('import_message') }}
            </div>
        @endif
        @if(session()->has('import_message_error'))
            <div class="alert alert-danger">
                {{ session()->get('import_message_error') }}
            </div>
        @endif
        </div>
        <div class="table table-responsive">
          <table class="table table-bordered data-table">
              <thead>
                  <tr>
                      <th>ID</th>
                      <th>Suppliers</th>
                      <th>Logistics Company</th>
                      <th>Full Name</th>
                      <th>Mobile Number</th>
                      <th>Company ID Number</th>
                      <th>License Number</th>
                      <th>Date of Safety Orientation</th>
                      <th>Expiration Date</th>
                      <th>Status</th>
                      <th>Is Approved?</th>
                      <!-- <th>Options</th> -->
                  </tr>
              </thead>
              <tbody>
              </tbody>
          </table>
        </div>
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
                <form id="driverForm" name="driverForm" class="form-horizontal">
                    
                    <input type="hidden" name="id" id="id">

                    <div class="form-group driver_id">
                        <label for="name" class="col-sm-12 control-label">*Driver ID</label>
                        <div class="col-sm-12">
                            <input type="text" readonly="" class="form-control" id="driver_id" name="id" value="" maxlength="100" required="">
                        </div>
                    </div>


                    <div class="form-group">
                       <label for="name" class="col-sm-12 control-label">*Supplier</label>
                       <div class="col-sm-12">
                          <select  class="form-control" id="supplier_id" name="supplier_id">
                             @foreach($supplierData['data'] as $supplier)
                               <option value='{{ $supplier->id }}'>{{ $supplier->supplier_name }}</option>
                             @endforeach
                          </select>
                        </div>
                        <br>
                        <div class="col-sm-12">
                          <a href="#" class="btn btn-primary add_supplier">Add Supplier</a>
                          <a href="#" class="btn btn-danger clear_supplier">Clear Supplier</a>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="name" class="col-sm-12 control-label">*Driver Suppliers</label>
                        <div class="col-sm-12">
                            <textarea class="form-control" id="driver_suppliers" name="supplier_names" readonly="" ="" required=""></textarea>
                           <!--  <input type="text" class="form-control" id="driver_suppliers" disabled="" required=""> -->
                            <input type="hidden" id="supplier_ids" name="supplier_ids">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="name" class="col-sm-12 control-label">*Logistics Company</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="logistics_company" name="logistics_company" placeholder="Enter Logistics Company" value="" maxlength="100" required="">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="name" class="col-sm-12 control-label">*First Name</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="first_name" name="first_name" placeholder="Enter First Name" value="" maxlength="50" required="">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="name" class="col-sm-12 control-label">*Last Name</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="last_name" name="last_name" placeholder="Enter Last Name" value="" maxlength="50" required="">
                        </div>
                    </div>

                      <div class="form-group">
                        <label for="name" class="col-sm-12 control-label">*Mobile Number</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="mobile_number" name="mobile_number" placeholder="Enter Mobile Number" value="" maxlength="50" required="">
                        </div>
                    </div>

                    <div class="form-group">
                      <label class="col-sm-12 control-label">*Company ID Number</label>
                      <div class="col-sm-12">
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="company_id_number" name="company_id_number" placeholder="Enter Company ID Number" value="" maxlength="50" required="">
                        </div>
                      </div>
                    </div>

                    <div class="form-group">
                      <label class="col-sm-12 control-label">*License Number</label>
                      <div class="col-sm-12">
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="license_number" name="license_number" placeholder="Enter License Number" value="" maxlength="50" required="">
                        </div>
                      </div>
                    </div>
                    <input type="hidden" name="isApproved" value="0">
                    @if(Auth::user()->role_id == 3)
                    <div class="form-group">
                      <label class="col-sm-12 control-label">*Date of Safety Orientation</label>
                      <div class="col-sm-12">
                        <div class="col-sm-12">
                        <input type="datetime-local" class="form-control datepicker" name="dateOfSafetyOrientation" id="dateOfSafetyOrientation" required="">
                        <input type="hidden" id="isApproved" name="isApproved" value="1">
                        </div>
                      </div>
                    </div>
                     @endif
                    <br>

                    <div class="col-sm-offset-2 col-sm-10">
                       <button type="submit" class="btn btn-primary" id="saveBtn" value="create">Save changes
                       </button>
                    </div>

                    <br>
                    <div class="modalresponse"></div>  

                </form>

            </div>
        </div>
    </div>
</div>

   
<div class="modal fade" id="viewPendingRegistrationModal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="viewPendingRegistrationHeading"></h4>

                <button type="button" class="close" data-dismiss="modal">&times;</button> 
            </div>
            <div class="modal-body">
                <div class="modalresponse"></div> 
                <div class="col-sm-12">
                  <div id="pendingRegistrationList"></div>
                </div>

            </div>
        </div>
    </div>
</div> 

<div class="modal fade" id="ajaxModelView" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Actions</h4>

                <button type="button" class="close" data-dismiss="modal">&times;</button> 
            </div>
            <div class="modal-body">
                
                <div class="row">
                  <div class="col-xl-4 col-sm-12">
                    <button id="btn-edit" class="btn btn-primary btn-xs btn-block editProduct" type="button">Edit</button>
                  </div>
                  <div class="col-xl-4 col-sm-12">
                    <button id="btn-deactivate" class="btn btn-secondary btn-xs btn-danger btn-block deactivateOrActivateDriver" type="button">Deactivate</button>
                  </div>
                  <div class="col-xl-4 col-sm-12">  
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
    
    var table = $('.data-table').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax":{
                 "url": "{{ url('alldrivers') }}",
                 "dataType": "json",
                 "type": "POST",
                 "data":{ _token: "{{csrf_token()}}"}
               },
        "columns": [
            { "data": "id" },
            {"data": 'supplier_ids'},
            {"data": 'logistics_company'},
            {"data": 'fullname'},
            {"data": 'mobile_number'},
            {"data": 'company_id_number'},
            {"data": 'license_number'},
            {"data": 'dateOfSafetyOrientation'},
            {"data": 'expirationDate'},
            { "data": "status"},
            { "data": "isApproved"},
            // { "data": "options" },
        ]  

    });
     
    $('#createNewProduct').click(function () {
        $(".driver_id").hide();
        $('#saveBtn').val("create-product");
        $('#id').val('');
        $("#driver_suppliers").val("");
        $("#supplier_ids").val("");
        $('#driverForm').trigger("reset");
        $('#modelHeading').html("Register Driver");
        $('#ajaxModel').modal({
          backdrop:'static',
          keyboard: false
        })
    });


    $('#viewPendingRegistration').click(function(){
     
        $('#viewPendingRegistrationHeading').html("View Pending Driver Registration");
        $("#viewPendingRegistrationModal .modal-content").css({
          width:"900px",
          left: "-150px"
        }); 
        $('#viewPendingRegistrationModal').modal({
          backdrop:'static',
          keyboard: false
        })
    });


  var loadViewPending = function(){
    var container = $("#pendingRegistrationList");
    $.ajax({
      type: 'POST', 
      url: "{{ url('showPendingRegistrationsDriver') }}",
      dataType: 'json',
      data:{ _token: "{{csrf_token()}}"},
      success: function (data) {
          console.log(data.length);

          container.html('');
          if(data.length == 0){
             container.append('No Results found.');
          }
          $.each(data, function(index, item) {
              container.html(''); //clears container for new data
              $.each(data, function(i, item) {
                   container.append('<div class="row" style="border:2px solid #ddd;padding:15px;"><div class="col-sm-6 col-md-6 col-lg-6"> <div class="form-group"><label class="col-sm-12 control-label">Suppliers : ' + item.supplier_ids + '</label><label class="col-sm-12 control-label">Logistics Company: ' + item.logistics_company + '</label><label class="col-sm-12 control-label">First Name: ' + item.first_name + '</label><label class="col-sm-12 control-label">Last Name: ' + item.last_name + '</label><label class="col-sm-12 control-label">Mobile Number: ' + item.mobile_number + '</label><label class="col-sm-12 control-label">Company ID Number: ' + item.company_id_number + '</label><label class="col-sm-12 control-label">License Number: ' + item.license_number + '</label><label class="col-sm-12 control-label">Date of Safety Orientation: ' + item.dateOfSafetyOrientation + '</label></div></div><div class="col-sm-6 col-md-6 col-lg-6"> <div class="form-group"> <div class="col-sm-12"><br><br><form id="driverRegistration'+item.id+'" name="driverRegistration" class="form-horizontal"><input type="hidden" name="id" value="'+item.id+'"><input type="datetime-local" class="form-control datepicker" name="dateOfSafetyOrientation" id="dateOfSafetyOrientation'+item.id+'" required=""><div class="col-sm-offset-2 col-sm-10"><br><a class="btn btn-primary completeDriverRegistration" data-id="'+item.id+'" value="create">Complete Driver Registration</a></div></form></div></div></div><div>');
              });
              container.append('<br>');
          });
      },error:function(){ 
           console.log(data);
      }
    });
  }

  $('body').on('click', '.completeDriverRegistration', function (e) {

      var dateOfSafetyOrientation = $('#dateOfSafetyOrientation'+ $(this).attr("data-id"));
      //console.log(dateOfSafetyOrientation)

      if(dateOfSafetyOrientation.val() == ""){
          $(".modalresponse").html("<div class='alert alert-danger'>Please fill in the Date of Safety Orientation fields.</div>")
          dateOfSafetyOrientation.css("border","red 2px solid")
          $('#modalresponse').fadeIn(1000);
          setTimeout(function(){
            $('#modalresponse').fadeOut(1000);
          },2000)
      }else{
        //console.log($('#driverRegistration').serialize())
        $.ajax({
              data: $('#driverRegistration'+ $(this).attr("data-id")).serialize(),
              url: "{{ url('completeDriverRegistration') }}",
              type: "POST",
              dataType: 'json',
              success: function (data) {
                 $('#response').html("<div class='alert alert-success'>"+data.success+"</div>")
                  $('#viewPendingRegistrationModal').modal('hide');
                  setTimeout(function(){
                    $('#response').hide("slow");
                  },3000)
                  table.draw();
                  loadViewPending();
              },
              error: function (data) {
                  console.log('Error:', data);
                  $('#saveBtn').html('Save Changes');
              }
          });
      }
      
  });

  
  $( function() {

      loadViewPending();
    $(".datepicker").datepicker();
  } ); 

  function pad (str, max) {
    str = str.toString();
    return str.length < max ? pad("0" + str, max) : str;
  }
    $('body').on('click', '.editProduct', function () {
      $("#ajaxModelView").modal("hide");
      $(".driver_id").show();
      $('.modalresponse').empty();
      $('#driverForm').trigger("reset");      
      var id = $(this).data('id');
      $.get("{{ route('ajaxdrivers.index') }}" +'/' + id +'/edit', function (data) {
          $('#modelHeading').html("Edit Driver");
          $('#saveBtn').val("edit-user");
          $('#ajaxModel').modal({
            backdrop:'static',
            keyboard: false
          })
          //console.log(data.id)
          $('#driver_id').val(pad(data.id,8));
          $('#logistics_company').val(data.logistics_company);
          $('#first_name').val(data.first_name);
         
          $('#last_name').val(data.last_name);

          $('#mobile_number').val(data.mobile_number);

          $('#company_id_number').val(data.company_id_number);

           $('#license_number').val(data.license_number);

          var x = data.supplier_ids.split("|")
          
          x.splice(-1,1)
          
          var supplier_drivers = "";
          $.each( x, function( key, value ) {
          
            var _supplier_name = $("#supplier_id option[value='"+value+"']").text()
            supplier_drivers += _supplier_name + " | ";
            //console.log(_supplier_name)
          });

          $('#driver_suppliers').val(supplier_drivers);
          $('#supplier_ids').val(data.supplier_ids);
          if(data.dateOfSafetyOrientation != undefined){

            data.dateOfSafetyOrientation = data.dateOfSafetyOrientation.replace(" ","T")
            document.getElementById("dateOfSafetyOrientation").value = data.dateOfSafetyOrientation;

          }

          //console.log(data.dateOfSafetyOrientation)
          
          // $('#delivery_type').val(data.delivery_type);
      })
   });
    
    var role_id = {{ Auth::user()->role_id }}
    $('#saveBtn').click(function (e) {
        
        if(role_id == 3 && $("#dateOfSafetyOrientation").val() == ""){
          $("#isApproved").val('0')
        }
     
        if($("#logistics_company").val() == "" || $("#first_name").val() == "" || $("#last_name").val() == "" || $("#mobile_number").val() == "" || $("#company_id_number").val() == "" || $("#license_number").val() == "" || $("#driver_suppliers").val() == ""){
          $(".modalresponse").html("<div class='alert alert-danger'>Please fill in the required fields.</div>")
          $('.modalresponse').fadeIn(1000);
          setTimeout(function(){
            $('.modalresponse').fadeOut(1000);
          },2000)

           if($("#logistics_company").val() == "")
             $("#logistics_company").css('outline','1px solid red')
           else
             $("#logistics_company").css('outline','1px solid black')

           if($("#first_name").val() == "")
             $("#first_name").css('outline','1px solid red')
           else
             $("#first_name").css('outline','1px solid black')

           if($("#last_name").val() == "")
             $("#last_name").css('outline','1px solid red')
           else
             $("#last_name").css('outline','1px solid black')

            if($("#mobile_number").val() == "")
             $("#mobile_number").css('outline','1px solid red')
            else
             $("#mobile_number").css('outline','1px solid black')

            if($("#company_id_number").val() == "")
             $("#company_id_number").css('outline','1px solid red')
            else
             $("#company_id_number").css('outline','1px solid black')

          

           if($("#license_number").val() == "")
             $("#license_number").css('outline','1px solid red')
            else
             $("#license_number").css('outline','1px solid black')

           if($("#driver_suppliers").val() == "")
             $("#driver_suppliers").css('outline','1px solid red')
            else
             $("#driver_suppliers").css('outline','1px solid black')

            return false;
        }else{

            e.preventDefault();
            $(this).html('Sending..');
        
            //console.log($('#driverForm').serialize())
            $.ajax({
              data: $('#driverForm').serialize(),
              url: "{{ route('ajaxdrivers.store') }}",
              type: "POST",
              dataType: 'json',
              success: function (data) {
                console.log(data.error)
                if(data.success != null){
                 $('#response').html("<div class='alert alert-success'>"+data.success+"</div>")
                  $('#driverForm').trigger("reset");
                  $('#ajaxModel').modal('hide');
                  setTimeout(function(){
                    $('#response').hide("slow");
                  },2000)
                  table.draw();
                  }else if(data.error != null){ 
                    $('.modalresponse').show();
                    $('.modalresponse').html("<div class='alert alert-danger'>"+data.error+"</div>")
                  }
                   $('#saveBtn').html('Save Changes');
              },
              error: function (data) {
                  console.log('Error:', data);
                  $('#saveBtn').html('Save Changes');
              }
          });
      }
    });
    
    $('body').on('click', '.deactivateOrActivateDriver', function () {
     
        var id = $("#btn-deactivate").attr("data-id");
        var status = $("#btn-deactivate").attr("data-status");
        
        if (confirm("Are you want to proceed?")){
            $.ajax({
                url: "{{ url('deactivateOrActivateDriver') }}",
                type: "POST",
                data: {id:id, status:status},
                success: function (data) {
                    $('#response').html("<div class='alert alert-success'>"+data.success+"</div>")
                    table.draw();

                    $("#ajaxModelView").modal("hide")
                },
                error: function (data) {
                    console.log('Error:', data);
                }
            });

        }
    });



    var driver_suppliers = '';
    var supplier_ids = '';
    $('body').on('click', '.add_supplier', function(){
      
      var _supplier_id = $('#supplier_id').children("option:selected").val();
      var _supplier_name = $('#supplier_id').children("option:selected").text();

      console.log(_supplier_name)
      var d_suppliers = $('#supplier_ids').val();
      var d_driver_suppliers = $('#driver_suppliers').val();


      if(!d_suppliers.includes(_supplier_id) || !d_suppliers.includes(_supplier_id)){
          // supplier_ids += _supplier_id + '|';
          // driver_suppliers += _supplier_name + ' | ';
          d_suppliers += _supplier_id + '|';
          console.log(d_suppliers)
          d_driver_suppliers += _supplier_name + ' | ';
          $('.modalresponse').empty();
      }else{
        $('.modalresponse').html("<div class='alert alert-danger'>Supplier already added.</div>")
      }

      // console.log(supplier_ids);
      // console.log(driver_suppliers);

      $('#driver_suppliers').val(d_driver_suppliers);
      $('#supplier_ids').val(d_suppliers);

    });

    $('body').on('click', '.clear_supplier', function(){
      
      $('#supplier_ids').val("");
      $('#driver_suppliers').val("")

      driver_suppliers = "";
      supplier_ids = "";
    });

    $('.data-table tbody').on( 'click', 'tr', function () {
            var id = $(this).find("td:nth-child(1)").first().text().trim()
            $.ajax({
              data: {id:id},
              url: "{{ url('getDriver') }}",
              type: "POST",
              dataType: 'json',
              success: function (data) {
                  console.log(data)
                  

                  $("#btn-edit").attr("data-id",data.id)
                  $("#btn-deactivate").attr("data-id",data.id)
                  
                  if(data.status == "1"){
                    $("#btn-deactivate").html("Deactivate")
                  }else{
                    $("#btn-deactivate").html("Activate")
                  }

                  $("#btn-deactivate").attr("data-status",data.status)

                  $('.data-table-incoming').DataTable().$('tr.selected').removeClass('selected');
                  
                  $('.data-table-outgoing').DataTable().$('tr.selected').removeClass('selected');
                  
                  $(this).addClass('selected');
                  
                  $('#ajaxModelView').modal({
                      backdrop:'static',
                      keyboard: false
                  })

              },
              error: function (data) {
                  console.log('Error:', data);
                  $('#saveBtn').html('Save Changes');
              }
          });
    });
  
  });
</script>
@endsection
