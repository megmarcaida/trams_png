@extends('layouts.datatableapp')

@section('content')
<link rel="stylesheet" href="{{ asset('css/jquery.dropdown.css') }}">
<script src="{{ asset('js/jquery.dropdown.js') }}"></script>
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
          <div class="col-xl-6 col-md-6">
            <div class="btn-group" role="group">
               <a class="btn btn-success" href="javascript:void(0)" id="createNewProduct"> Register Driver</a>
            
               <!-- <a class="btn btn-success" href="javascript:void(0)" id="viewPendingRegistration"> View Pending Registrations</a>  -->

              <a class="btn btn-warning" href="{{ route('exportDriver') }}">Export Drivers Data</a>
              <!-- <a class="btn btn-secondary" href="https://srv-file2.gofile.io/download/MRQ91R/drivers_template.xlsx" >Download Template</a> -->
            </div>
          </div>
          <div class="col-xl-6 col-md-6">
              <form action="{{ route('importDriver') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="input-group mb-3">
                  <input type="file" name="file" class="form-control">
                  <div class="input-group-append">
                    <button class="btn btn-success text-right"><span class="fa fa-upload"></span></button>
                    <a class="btn btn-secondary" href="{{ Storage::url('template/drivers_template.xlsx') }}">Download Template</a>
                  </div>
                </div>
                <br>
                <!-- <a class="btn btn-secondary" href="{{ Storage::url('template/drivers_template.xlsx') }}">Download Template Data</a> -->
                
                
            </form> 
          </div>
        </div>
        <div class="row">
          <div class="col-md-3">
              
            <div class="form-group">
               <label for="name" class="col-sm-12 control-label">*Filter Suppler</label>
               <div class="col-sm-12 fs_dd">
                  <select multiple="true" class="form-control delivery_type_filter" id="delivery_type_filter" name="delivery_type_filter[]">
                       <option value="">All</option>
                       @foreach($supplierData['data'] as $supplier)
                         <option value='{{ $supplier->supplier_name }}'>{{ $supplier->supplier_name }}</option>
                       @endforeach
                  </select>
                </div>
            </div>
          </div>
          <div class="col-md-3">
              
            <div class="form-group">
               <label for="name" class="col-sm-12 control-label">*Filter Is Approved</label>
               <div class="col-sm-12 sf_dd">
                  <select multiple="true" class="form-control status_filter" id="status_filter" name="status_filter[]">
                       <option value="">All</option>
                       <option value="Approved">Approved</option>
                       <option value="Rejected">Rejected</option>
                  </select>
                </div>
            </div>
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
                      <!-- <th>Company ID Number</th>
                      <th>License Number</th> -->
                      <th>Date of Safety Orientation</th>
                      <!-- <th>Expiration Date</th> -->
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
        <div class="modal-content" style="width:960px;margin-left:-220px;">
            <div class="modal-header">
                <h4 class="modal-title" id="modelHeading"></h4>

                <button type="button" class="close" data-dismiss="modal">&times;</button> 
            </div>
            <div class="modal-body">
                <form id="driverForm" name="driverForm" class="form-horizontal">
                    
                   <!--  <input type="hidden" name="id" id="id"> -->


                    <div class="row">

                    <!-- first column -->
                    <div class="col-6">
                      
                      <!-- driver id -->
                      <div class="form-group driver_id">
                          <label for="name" class="col-sm-12 control-label">*Driver ID</label>
                          <div class="col-sm-12">
                              <input type="text" readonly="" class="form-control" id="driver_id" name="id" value="" maxlength="100" required="">
                          </div>
                      </div>

                      <!-- Suppliers -->
                      <div class="form-group">
                         <label for="name" class="col-sm-12 control-label">*Supplier</label>
                         <div class="col-sm-12">
                            <!-- <input type="text" id="driver_suppliers" name="supplier_names" readonly="" class="form-control supplier_id"> -->
                            
                            <div id="driver_supplier_add"></div>
                            <div class="driver_supplier">
                              <select class="form-control" id="driver_suppliers" style="display:none"  name="driver_suppliers" multiple>
                                @foreach($supplierData['data'] as $supplier)
                                 <option value='{{ $supplier->id }}'>{{ $supplier->supplier_name }}</option>
                               @endforeach
                              </select>
                            </div>
                            <!-- <select  class="form-control" multiple id="supplier_id" name="supplier_id">
                               @foreach($supplierData['data'] as $supplier)
                                 <option value='{{ $supplier->id }}'>{{ $supplier->supplier_name }}</option>
                               @endforeach
                            </select> -->
                            <input type="hidden" id="supplier_ids" name="supplier_ids">
                          </div>
                          <!-- <div class="col-sm-12">
                            <a href="#" class="btn btn-primary add_supplier">Add Supplier</a>
                            <a href="#" class="btn btn-danger clear_supplier">Clear Supplier</a>
                          </div> -->
                      </div>

                      <!-- driver suppliers -->
                      <!-- <div class="form-group">
                          <label for="name" class="col-sm-12 control-label">*Driver Suppliers</label>
                          <div class="col-sm-12">
                              <textarea class="form-control" id="driver_suppliers" name="supplier_names" readonly="" required=""></textarea>
                              <input type="hidden" id="supplier_ids" name="supplier_ids">
                          </div>
                      </div> -->

                      <!-- first name -->
                      <div class="form-group">
                          <label for="name" class="col-sm-12 control-label">*First Name</label>
                          <div class="col-sm-12">
                              <input type="text" class="form-control" id="first_name" name="first_name" placeholder="Enter First Name" value="" maxlength="50" required="">
                          </div>
                      </div>

                      <!-- Mobile Number -->
                      <div class="form-group">
                          <label for="name" class="col-sm-12 control-label">*Mobile Number</label>
                          <div class="col-sm-12">
                              <input type="text" class="form-control" id="mobile_number" name="mobile_number" placeholder="Enter Mobile Number" value="" maxlength="50" required="">
                          </div>
                      </div>

                    </div>

                    <!-- second column -->
                     <div class="col-6 ">
                        
                        <!-- logistic company -->
                        <div class="form-group">
                            <label for="name" class="col-sm-12 control-label">*Logistics Company</label>
                            <div class="col-sm-12">
                                <input type="text" class="form-control" id="logistics_company" name="logistics_company" placeholder="Enter Logistics Company" value="" maxlength="100" required="">
                            </div>
                        </div>


                      
                      


                      
                      <!-- Last Name -->
                      <div class="form-group">
                          <label for="name" class="col-sm-12 control-label">*Last Name</label>
                          <div class="col-sm-12">
                              <input type="text" class="form-control" id="last_name" name="last_name" placeholder="Enter Last Name" value="" maxlength="50" required="">
                          </div>
                      </div>

                      <!-- Date of validity -->
                      @if(Auth::user()->role_id == 3)
                        <input type="hidden" name="isApproved" value="0">
                        <div class="form-group">
                          <label class="col-sm-12 control-label">*Date of Validity</label>
                          <div class="col-sm-12">
                            <div class="col-sm-12">
                            <input type="date" class="form-control datepicker" name="dateOfSafetyOrientation" id="dateOfSafetyOrientation" required="">
                            <input type="hidden" id="isApproved" name="isApproved" value="1">
                            </div>
                          </div>
                        </div>
                      @endif

                     </div>


                     <!-- third column -->
                    <!--<div class="col-4">
                      -->



                      

                      <!-- Company ID Number -->
                      <!-- <div class="form-group">
                        <label class="col-sm-12 control-label">*Company ID Number</label>
                        <div class="col-sm-12">
                              <input type="hidden" class="form-control" id="company_id_number" name="company_id_number" placeholder="Enter Company ID Number" value="" maxlength="50" required="">
                        </div>
                      </div> -->

                      <!-- License Number -->
                      <!-- <div class="form-group">
                        <label class="col-sm-12 control-label">*License Number</label>
                        <div class="col-sm-12">
                              <input type="hidden" class="form-control" id="license_number" name="license_number" placeholder="Enter License Number" value="" maxlength="50" required="">
                        </div>
                      </div> -->
                      
                    <!-- </div> -->
                    
                    <div class="col-12">
                    <br> <br>
                      <div class="offset-8 col-sm-4">
                         <button type="submit" class="btn btn-primary btn-block" id="saveBtn" value="create">Save changes
                         </button>
                      </div>
                    </div>
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
                <h4 class="modal-title">View Driver</h4>

                <button type="button" class="close" data-dismiss="modal">&times;</button> 
            </div>
            <div class="modal-body">
                
                <div class="row">
                        <div class="col-md-6 col-xs-6" style="line-height: 0px">
                          Supplier:
                        </div>
                        <div class="col-md-6 col-xs-6" style="line-height: 0px">
                          <b><p id="view_supplier_name"></p></b>
                        </div>
                </div>
                <div class="row">
                     
                        <div class="col-md-6 col-xs-6" style="line-height: 0px">
                          Logistic Company:
                        </div>
                        <div class="col-md-6 col-xs-6" style="line-height: 0px">
                          <b><p id="view_logistics"></p></b>
                        </div>
                </div>
                <div class="row">
                        <div class="col-md-6 col-xs-6" style="line-height: 0px">
                          Full Name:
                        </div>
                        <div class="col-md-6 col-xs-6" style="line-height: 0px">
                          <b><p id="view_full_name"></p></b>
                        </div>
                </div>
                <div class="row">
                        <div class="col-md-6 col-xs-6" style="line-height: 0px">
                          Mobile Number:
                        </div>
                        <div class="col-md-6 col-xs-6" style="line-height: 0px">
                          <b><p id="view_mobile_no"></p></b>
                        </div>
                </div>     
                       <!--  <div class="col-md-6" style="line-height: 0px">
                          Company ID Number:
                        </div>
                        <div class="col-md-6" style="line-height: 0px">
                          <b><p id="view_company_id_number"></p></b>
                        </div>
                    
                        <div class="col-md-6" style="line-height: 0px">
                          License Number:
                        </div>
                        <div class="col-md-6" style="line-height: 0px">
                          <b><p id="view_license_number"></p></b>
                        </div> -->
                <div class="row">
                        <div class="col-md-6 col-xs-6" style="line-height: 0px">
                          Date of Validity:
                        </div>
                        <div class="col-md-6 col-xs-6" style="line-height: 0px">
                          <b><p id="view_validity_date"></p></b>
                        </div>
                        <br></br>
                </div>
                <div class="row">
                          <!-- Date of validity -->
                        @if(Auth::user()->role_id == 3)
                        <div class="col-xl-12 col-sm-12">
                          <input type="hidden" name="isApproved" value="0">
                          <div class="form-group">
                            <label class="col-sm-12 control-label">*Date of Validity</label>
                            <div class="col-xl-12 col-sm-12">
                              <div class="row">
                                <div class="col-xl-8 col-sm-12">
                                  <input type="date" class="form-control datepicker" name="dateOfSafetyOrientation_approved" id="dateOfSafetyOrientation_approved" required="">
                                  <input type="hidden" id="isApproved" name="isApproved" value="1">
                                </div>
                                <div class="col-xl-4 col-sm-12">
                                  <button id="btn-approved" class="btn btn-primary btn-xs btn-block completeDriverRegistration" type="button">Approve</button>
                                </div>
                              </div>
                            </div>
                          </div>
                          <br>
                        </div>
                        @endif
                  </div>
                  <div class="row">
                        <div class="col-xl-4 col-md-4 col-sm-12">
                          <button id="btn-edit" class="btn btn-primary btn-xs btn-block editProduct" type="button">Edit</button>
                        </div>
                        <div class="col-xl-4 col-md-4 col-sm-12">
                          <button id="btn-deactivate" class="btn btn-secondary btn-xs btn-danger btn-block deactivateOrActivateDriver" type="button">Deactivate</button>
                        </div>
                        <div class="col-xl-4 col-md-4 col-sm-12">  
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
            // {"data": 'company_id_number'},
            // {"data": 'license_number'},
            {"data": 'dateOfSafetyOrientation'},
            // {"data": 'expirationDate'},
            { "data": "status"},
            { "data": "isApproved"},
            // { "data": "options" },
        ]  

    });

    setTimeout(function(){
      table.draw();
    },10000)
     
    $('#createNewProduct').click(function () {
        $(".driver_id").hide();
        $('#saveBtn').val("create-product");
        $('#id').val('');
        $("#driver_suppliers").val("");
        $("#supplier_ids").val("");
        $('#driverForm').trigger("reset");
        $('#modelHeading').html("Register Driver");


        $('#saveBtn').html('Save Changes')
        $('#supplier_id').not(this).find('option').removeAttr('disabled');
        $('#supplier_id').removeClass('disableSelect');
        $('#logistics_company').attr('readonly',false);
        $('#first_name').attr('readonly',false);
        $('#last_name').attr('readonly',false);
        $('#mobile_number').attr('readonly',false);
        $('#company_id_number').attr('readonly',false);
        $('#license_number').attr('readonly',false);
        $('#logistics_company').attr('readonly',false);
       
        $('.driver_supplier').remove()
        $.ajax({
          type: 'POST', 
          url: "{{ url('getAllSupplier') }}",
          dataType: 'json',
          data:{ _token: "{{csrf_token()}}"},
          success: function (data) {
              console.log(data);
              $('#driver_supplier_add').append('<div class="driver_supplier"><select class="form-control" id="driver_suppliers" style="display:none"  name="driver_suppliers[]" multiple></select></div>')
              $.each(data, function(index, item) {
                  $('#driver_suppliers').append('<option value="'+item.id+'">'+item.supplier_name+'</option>')
              });
              $('.driver_supplier').dropdown({
                limitCount: 40,
                multipleMode: 'label',
                // callback
                choice: function (event, selectedProp,x) {
                  
                },
              });
          },error:function(){ 
               console.log(data);
          }
        });
        
        $('#ajaxModel').modal({
          backdrop:'static',
          keyboard: false
        })
    });

    $('.fs_dd').dropdown({
      limitCount: 40,
      multipleMode: 'label',
      // callback
      choice: function (event, selectedProp,x) {
        console.log(selectedProp.id)
        table.search(selectedProp.id).draw();   
      },
    });

    $('.sf_dd').dropdown({
      limitCount: 40,
      multipleMode: 'label',
      // callback
      choice: function (event, selectedProp,x) {
        console.log(selectedProp.id)
        table.search(selectedProp.id).draw();   
      },
    });

    $('#delivery_type_filter').on('change', function(){
       table.search(this.value).draw();   
    });
    $('#status_filter').on('change', function(){
       table.search(this.value).draw();   
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

      var dateOfSafetyOrientation = $('#dateOfSafetyOrientation_approved');
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
              data: {id:$(this).attr("data-id"),dateOfSafetyOrientation:dateOfSafetyOrientation.val()},
              url: "{{ url('completeDriverRegistration') }}",
              type: "POST",
              dataType: 'json',
              success: function (data) {
                 $('#response').html("<div class='alert alert-success'>"+data.success+"</div>")
                  $('#viewPendingRegistrationModal').modal('hide');
                  setTimeout(function(){
                    $('#response').hide("slow");
                  },3000)
                  $("#ajaxModelView").modal("hide");
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

  var role_id = {{ Auth::user()->role_id }}
    $('body').on('click', '.editProduct', function () {
      $("#ajaxModelView").modal("hide");
      $(".driver_id").show();
      $('.modalresponse').empty();
      $('#driverForm').trigger("reset");
      
      if(role_id != 3){
        $('#saveBtn').html('Save Changes')
      }else{
        $('#supplier_id').not(this).find('option').prop('disabled', 'true');
        $('#supplier_id').addClass('disableSelect');
        $('#logistics_company').attr('readonly',true);
        $('#first_name').attr('readonly',true);
        $('#last_name').attr('readonly',true);
        $('#mobile_number').attr('readonly',true);
        $('#company_id_number').attr('readonly',true);
        $('#license_number').attr('readonly',true);
        $('#logistics_company').attr('readonly',true);
        $('#saveBtn').html('Approve')
      }
      var id = $(this).attr('data-id');
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
     
          $.each( x, function( key, e ) {
            e = e.trim()
            if(e != ""){
              $('#driver_suppliers option[value="' + e + '"]').prop('selected', true);
            }
            
          });
         


          //$('#driver_suppliers').val(data.supplier_names);
          //$('#supplier_ids').val(data.supplier_ids);

          

          //console.log(data.dateOfSafetyOrientation)
          
          // $('#delivery_type').val(data.delivery_type);
          $('.driver_supplier').dropdown({
            limitCount: 40,
            multipleMode: 'label',
            // callback
            choice: function (event, selectedProp,x) {
              
            },
          });

          //console.log(data.dateOfSafetyOrientation)
          if(data.dateOfSafetyOrientation != null || data.dateOfSafetyOrientation != undefined){

            data.dateOfSafetyOrientation = data.dateOfSafetyOrientation.replace(" ","T")
            document.getElementById("dateOfSafetyOrientation").value = data.dateOfSafetyOrientation;
          }
      })
   });
    
    $('#saveBtn').click(function (e) {
      e.preventDefault();
      console.log("Test")
        $.ajax({
              data: $('#driverForm').serialize(),
              url: "{{ route('checkDriver') }}",
              type: "POST",
              dataType: 'json',
              success: function (data) {
                console.log(data)
                
                if(data > 0){
                  if(confirm("Driver's name already exist. Are you sure you want to continue?")){
                    saveData();
                  }else{
                    $('#response').html("<div class='alert alert-danger'>Registration cancelled.</div>")
                    $('#driverForm').trigger("reset");
                    $('#ajaxModel').modal('hide');
                    setTimeout(function(){
                      $('#response').hide("slow");
                    },2000)
                  }  
                }else{
                  console.log("here test")
                  saveData();
                }
                

              },
              error: function (data) {
                  console.log('Error:', data);
                  $('#saveBtn').html('Save Changes');
              }
          });
        
       
    });

    function saveData(){
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
                  $('#response').show();
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
    }
    
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
            $('.driver_supplier').remove()
            var id = $(this).find("td:nth-child(1)").first().text().trim()
            $.ajax({
              data: {id:id},
              url: "{{ url('getDriver') }}",
              type: "POST",
              dataType: 'json',
              success: function (data) {
                  console.log(data)
                  
                  $('#view_supplier_name').html(data.supplier_names)
                  $('#view_logistics').html(data.logistics_company)
                  $('#view_full_name').html(data.first_name + " " + data.last_name)
                  $('#view_mobile_no').html(data.mobile_number)
                  $('#view_company_id_number').html(data.company_id_number)
                  $('#view_license_number').html(data.license_number)
                  $('#view_validity_date').html(data.dateOfSafetyOrientation)  

                  $("#btn-edit").attr("data-id",data.id)
                  $("#btn-deactivate").attr("data-id",data.id)
                  $("#btn-approved").attr("data-id",data.id)
                  
                  if(data.status == "1"){
                    $("#btn-deactivate").html("Deactivate")
                  }else{
                    $("#btn-deactivate").html("Activate")
                  }

                  if(role_id == 3){
                    $('#dateOfSafetyOrientation_approved').val(data.dateOfSafetyOrientation) 
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
         
            $.ajax({
              type: 'POST', 
              url: "{{ url('getAllSupplier') }}",
              dataType: 'json',
              data:{ _token: "{{csrf_token()}}"},
              success: function (data) {
                  console.log(data);
                  $('#driver_supplier_add').append('<div class="driver_supplier"><select class="form-control" id="driver_suppliers" style="display:none"  name="driver_suppliers[]" multiple></select></div>')
                  $.each(data, function(index, item) {
                      $('#driver_suppliers').append('<option value="'+item.id+'">'+item.supplier_name+'</option>')
                  });
                  
              },error:function(){ 
                   console.log(data);
              }
            });
    });
  
  });

//var value = ""
// $('.driver_supplier').dropdown({
//   // read only
//   readOnly: false,
//   // min count
//   minCount: 0,
//   // error message
//   minCountErrorMessage: '',
//   // the maximum number of options allowed to be selected
//   limitCount: Infinity,
//   // error message
//   limitCountErrorMessage: '',
//   // search field
//   input: '<input type="text" maxLength="20" placeholder="Search">',
//   // is search able?
//   searchable: true,
//   // when there's no result
//   searchNoData: '<li style="color:#ddd">No Results</li>',
//   // callback
//   choice: function (event, selectedProp,x) {
//     var d_suppliers = $('#supplier_ids').val();
//     var d_driver_suppliers = $('#driver_suppliers').val();
//     if(selectedProp != undefined){
//       console.log(selectedProp)
//         if(selectedProp.selected == true){
//           var _supplier_id = selectedProp.id;
//           var _supplier_name = selectedProp.name;
//           if(!d_suppliers.includes(_supplier_id)){
//               d_suppliers += _supplier_id + '|';
//               d_driver_suppliers += _supplier_name + '|';
//               $('.modalresponse').empty();
//           }else{
//             $('.modalresponse').html("<div class='alert alert-danger'>Supplier already added.</div>")
//           }
//           $('#driver_suppliers').val(d_driver_suppliers);
//           $('#supplier_ids').val(d_suppliers); 
//         }
//         if(selectedProp.selected == false){
//           if(d_suppliers.includes(selectedProp.id+ '|')){
//               console.log(d_suppliers)
//               d_suppliers.replace(selectedProp.id + '|',"");
//               d_driver_suppliers.replace(selectedProp.name + '|',"");
//               $('#driver_suppliers').val(d_driver_suppliers.replace(selectedProp.name + '|',""));
//           $('#supplier_ids').val(d_suppliers.replace(selectedProp.id + '|',""));
//           }
//         }
//     }
//   },
// });

// $('div.driver_supplier > a.dropdown-clear-all').on('click',function(){
//   $('#driver_suppliers').val('');
//           $('#supplier_ids').val('');
// });
</script>
@endsection
