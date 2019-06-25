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
    <li class="breadcrumb-item active">Assistants</li>
  </ol>

  <div class="row">
    <div class="col-xl-12 col-sm-12 mb-3">
      <h1>Assistants</h1>

        <div class="row">
          <div class="col-xl-6 col-md-6">
              
               <a class="btn btn-success" href="javascript:void(0)" id="createNewProduct"> Register Assistant</a>
          
           
            <a class="btn btn-warning" href="{{ route('exportAssistant') }}">Export Assistants</a>
          </div>
          <div class="col-xl-6 col-md-6">
              <form action="{{ route('importAssistant') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="file" name="file" class="form-control">
                <br>
                <button class="btn btn-success text-right">Import Assistants Data</button>
               <!--  <a class="btn btn-secondary" href="{{ Storage::url('template/assistants_template.xlsx') }}">Download Template Data</a> -->
                <a class="btn btn-secondary" href="https://srv-file2.gofile.io/download/5kYPxn/assistants_template.xlsx">Download Template Data</a>
            </form> 
          </div>
        </div>
        <div class="row">
          <div class="col-md-3">
              
            <div class="form-group">
               <label for="name" class="col-sm-12 control-label">*Filter Supplier</label>
               <div class="col-sm-12">
                  <select multiple="true" class="form-control supplier_filter" id="supplier_filter" name="supplier_filter[]">
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
               <label for="name" class="col-sm-12 control-label">*Filter Status</label>
               <div class="col-sm-12">
                  <select multiple="true" class="form-control status_filter" id="status_filter" name="status_filter[]">
                       <option value="">All</option>
                       <option value="Active">Active</option>
                       <option value="Inactive">Inactive</option>
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
                      <th>Valid ID Present</th>
                      <th>Valid ID Number</th> -->
                      <th>Date of Safety Orientation</th>
                      <th>Status</th>
                      <th>Is Approved?</th>
                     <!--  <th>Options</th> -->
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
                <form id="assistantForm" name="assistantForm" class="form-horizontal">
                    
                    <input type="hidden" name="id" id="id">
                    <div class="row">

                        <!-- First Column -->
                        <div class="col-6">

                          <!-- assistant id -->
                          <div class="form-group assistant_id">
                              <label for="name" class="col-sm-12 control-label">*Assistant ID</label>
                              <div class="col-sm-12">
                                  <input type="text" readonly="" class="form-control" id="assistant_id" name="id" value="" maxlength="100" required="">
                              </div>
                          </div>

                          <!-- supplier -->

                          <div class="form-group">
                             <label for="name" class="col-sm-12 control-label">*Supplier</label>
                             <div class="col-sm-12">
                                <!-- <input type="text" id="assistant_suppliers" name="supplier_names" readonly="" class="form-control supplier_id"> -->
                                <div id="assistant_supplier_add"></div>
                                <div class="assistant_supplier">
                                  <select class="form-control" style="display:none"  name="" multiple>
                                    @foreach($supplierData['data'] as $supplier)
                                     <option value='{{ $supplier->id }}'>{{ $supplier->supplier_name }}</option>
                                   @endforeach
                                  </select>
                                </div>
                                <!-- <select multiple style="height:90px;" class="form-control" id="supplier_id" name="supplier_id">
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


                          <!-- assistant supplier -->
                         <!--  <div class="form-group">
                              <label for="name" class="col-sm-12 control-label">*Assistant Suppliers</label>
                              <div class="col-sm-12">
                                  <textarea class="form-control" id="assistant_suppliers" name="supplier_names" readonly="" required=""></textarea>
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

                          <!-- mobile number -->
                          <div class="form-group">
                            <label for="name" class="col-sm-12 control-label">*Mobile Number</label>
                            <div class="col-sm-12">
                                <input type="text" class="form-control" id="mobile_number" name="mobile_number" placeholder="Enter Mobile Number" value="" maxlength="50" required="">
                            </div>
                          </div>

                        </div> 

                        <!-- Second Column -->
                        <div class="col-6">

                          <!-- logistics company -->
                          <div class="form-group">
                              <label for="name" class="col-sm-12 control-label">*Logistics Company</label>
                              <div class="col-sm-12">
                                  <input type="text" class="form-control" id="logistics_company" name="logistics_company" placeholder="Enter Logistics Company" value="" maxlength="100" required="">
                              </div>
                          </div>

                          <!-- last name -->
                          <div class="form-group">
                              <label for="name" class="col-sm-12 control-label">*Last Name</label>
                              <div class="col-sm-12">
                                  <input type="text" class="form-control" id="last_name" name="last_name" placeholder="Enter Last Name" value="" maxlength="50" required="">
                              </div>
                          </div>

                          <!-- date of validity -->
                          <input type="hidden" name="isApproved" value="0">
                          @if(Auth::user()->role_id == 3)
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

                        <!-- Third Column -->
                        <!-- <div class="col-4"> -->



                          

                          <!-- company id number -->
                          <!-- <div class="form-group">
                            <label class="col-sm-12 control-label">*Company ID Number</label>
                            <div class="col-sm-12">
                              <div class="col-sm-12">
                                  <input type="text" class="form-control" id="company_id_number" name="company_id_number" placeholder="Enter Company ID Number" value="" maxlength="50" required="">
                              </div>
                            </div>
                          </div> -->

                          <!-- valid id Present -->
                          <!-- <div class="form-group">
                            <label class="col-sm-12 control-label">*Valid ID Present</label>
                            <div class="col-sm-12">
                              <div class="col-sm-12">
                                  <input type="text" class="form-control" id="valid_id_present" name="valid_id_present" placeholder="Enter Valid ID Present" value="" maxlength="50" required="">
                              </div>
                            </div>
                          </div> -->

                          <!-- valid id number -->
                          <!-- <div class="form-group">
                            <label class="col-sm-12 control-label">*Valid ID Number</label>
                            <div class="col-sm-12">
                              <div class="col-sm-12">
                                  <input type="text" class="form-control" id="valid_id_number" name="valid_id_number" placeholder="Enter Valid ID Number" value="" maxlength="50" required="">
                              </div>
                            </div>
                          </div> -->

                          

                       <!--  </div>  -->

                        <div class="offset-8 col-sm-4">
                           <button type="submit" class="btn btn-primary btn-block" id="saveBtn" value="create">Save changes
                           </button>
                        </div>
                        <br>
                    </div>
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
                <h4 class="modal-title">View Assistant</h4>

                <button type="button" class="close" data-dismiss="modal">&times;</button> 
            </div>
            <div class="modal-body">
                
                <div class="row">

                  <div class="col-md-6" style="line-height: 0px">
                    Supplier:
                  </div>
                  <div class="col-md-6" style="line-height: 0px">
                    <b><p id="view_supplier_name"></p></b>
                  </div>
               </div>
               <div class="row">
                  <div class="col-md-6" style="line-height: 0px">
                    Logistic Company:
                  </div>
                  <div class="col-md-6" style="line-height: 0px">
                    <b><p id="view_logistics"></p></b>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-6" style="line-height: 0px">
                    Full Name:
                  </div>
                  <div class="col-md-6" style="line-height: 0px">
                    <b><p id="view_full_name"></p></b>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-6" style="line-height: 0px">
                    Mobile Number:
                  </div>
                  <div class="col-md-6" style="line-height: 0px">
                    <b><p id="view_mobile_no"></p></b>
                  </div>
                </div>
                  <!-- <div class="col-md-6" style="line-height: 0px">
                    Company ID Number:
                  </div>
                  <div class="col-md-6" style="line-height: 0px">
                    <b><p id="view_company_id_number"></p></b>
                  </div>
              
                  <div class="col-md-6" style="line-height: 0px">
                    Valid Id Present:
                  </div>

                  <div class="col-md-6" style="line-height: 0px">
                    <b><p id="view_valid_id_present"></p></b>
                  </div>

                  <div class="col-md-6" style="line-height: 0px">
                    Valid Id Number:
                  </div>
                  <div class="col-md-6" style="line-height: 0px">
                    <b><p id="view_valid_id_number"></p></b>
                  </div> -->
                <div class="row">  
                  <div class="col-md-6" style="line-height: 0px">
                    Date of Validity:
                  </div>
                  <div class="col-md-6" style="line-height: 0px">
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
                            <button id="btn-approved" class="btn btn-primary btn-xs btn-block completeAssistantRegistration" type="button">Approve</button>
                          </div>
                        </div>
                      </div>
                    </div>
                    <br>
                  </div>
                  @endif
                </div>
                <div class="row">
                  <div class="col-xl-4 col-sm-12">
                    <button id="btn-edit" class="btn btn-primary btn-xs btn-block editProduct" type="button">Edit</button>
                  </div>
                  <div class="col-xl-4 col-sm-12">
                    <button id="btn-deactivate" class="btn btn-secondary btn-xs btn-danger btn-block deactivateOrActivateAssistant" type="button">Deactivate</button>
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
                 "url": "{{ url('allassistants') }}",
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
            // {"data": 'valid_id_present'},
            // {"data": 'valid_id_number'},
            {"data": 'dateOfSafetyOrientation'},
            { "data": "status"},
            { "data": "isApproved"},
            //{ "data": "options" },
        ]  

    });

    setTimeout(function(){
      table.draw();
    },10000)

    var role_id = {{ Auth::user()->role_id }}
     

    var today = new Date().toISOString().split('T')[0];

    if(role_id == 3){
      
    document.getElementById("dateOfSafetyOrientation").setAttribute('min', today);
    }
    $('#createNewProduct').click(function () {
        $('#saveBtn').val("create-product");
        $('#id').val('');
        $(".assistant_id").hide();
        $("#assistant_suppliers").val("");
        $("#supplier_ids").val("");
        $('#assistantForm').trigger("reset");
        $('#modelHeading').html("Register Assistant");

        $('#saveBtn').html('Save Changes')
        $('#supplier_id').not(this).find('option').removeAttr('disabled');
        $('#supplier_id').removeClass('disableSelect');
        $('#logistics_company').attr('readonly',false);
        $('#first_name').attr('readonly',false);
        $('#last_name').attr('readonly',false);
        $('#mobile_number').attr('readonly',false);
        $('#company_id_number').attr('readonly',false);
        $('#valid_id_number').attr('readonly',false);
        $('#valid_id_present').attr('readonly',false);
        $('#logistics_company').attr('readonly',false);

        $('.assistant_supplier').remove()
        $.ajax({
          type: 'POST', 
          url: "{{ url('getAllSupplier') }}",
          dataType: 'json',
          data:{ _token: "{{csrf_token()}}"},
          success: function (data) {
              console.log(data);
              $('#assistant_supplier_add').append('<div class="assistant_supplier"><select class="form-control" id="assistant_suppliers" style="display:none"  name="assistant_suppliers[]" multiple></select></div>')
              $.each(data, function(index, item) {
                  $('#assistant_suppliers').append('<option value="'+item.id+'">'+item.supplier_name+'</option>')
              });
              $('.assistant_supplier').dropdown({
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

    $('#supplier_filter').on('change', function(){
       table.search(this.value).draw();   
    });
    $('#logistics_company_filter').on('change', function(){
        var ordering_days = "ordering_days|";
        $.each($(".ordering_days_filter option:selected"), function(){            
            ordering_days += this.value + " | "
        }); 

       console.log(ordering_days)
       table.search(ordering_days).draw();   
    });
    $('#status_filter').on('change', function(){
       table.search(this.value).draw();   
    });


    $('#viewPendingRegistration').click(function(){
     
        $('#viewPendingRegistrationHeading').html("View Pending Assistant Registration");
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
      type: 'GET', //THIS NEEDS TO BE GET
      url: '../showPendingRegistrations',
      dataType: 'json',
      success: function (data) {
          console.log(data.length);

          container.html('');
          if(data.length == 0){
             container.append('No Results found.');
          }
          $.each(data, function(index, item) {
              container.html(''); //clears container for new data
              $.each(data, function(i, item) {
                   container.append('<div class="row" style="border:2px solid #ddd;padding:15px;"><div class="col-sm-6 col-md-6 col-lg-6"> <div class="form-group"><label class="col-sm-12 control-label">Logistics Company: ' + item.logistics_company + '</label><label class="col-sm-12 control-label">First Name: ' + item.first_name + '</label><label class="col-sm-12 control-label">Last Name: ' + item.last_name + '</label><label class="col-sm-12 control-label">Mobile Number: ' + item.mobile_number + '</label><label class="col-sm-12 control-label">Company ID Number: ' + item.company_id_number + '</label><label class="col-sm-12 control-label">Valid ID Present: ' + item.valid_id_present + '</label><label class="col-sm-12 control-label">Valid ID Number: ' + item.valid_id_number + '</label><label class="col-sm-12 control-label">Date of Safety Orientation: ' + item.dateOfSafetyOrientation + '</label></div></div><div class="col-sm-6 col-md-6 col-lg-6"> <div class="form-group"> <div class="col-sm-12"><br><br><form id="assistantRegistration'+item.id+'" name="assistantRegistration" class="form-horizontal"><input type="hidden" name="id" value="'+item.id+'"><input type="datetime-local" class="form-control datepicker" name="dateOfSafetyOrientation" id="dateOfSafetyOrientation'+item.id+'" required=""><div class="col-sm-offset-2 col-sm-10"><br><a class="btn btn-primary completeAssistantRegistration" data-id="'+item.id+'" value="create">Complete Assistant Registration</a></div></form></div></div></div><div>');
              });
              container.append('<br>');
          });
      },error:function(){ 
           console.log(data);
      }
    });
  }

  $('body').on('click', '.completeAssistantRegistration', function (e) {

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
        $.ajax({
              data: {id:$(this).attr("data-id"),dateOfSafetyOrientation:dateOfSafetyOrientation.val()},
              url: "{{ url('completeAssistantRegistration') }}",
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
    $('body').on('click', '.editProduct', function () {

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
        $('#valid_id_present').attr('readonly',true);
        $('#valid_id_number').attr('readonly',true);
        $('#logistics_company').attr('readonly',true);
        $('#saveBtn').html('Approve')
      }

      $("#ajaxModelView").modal("hide");
      $('.modalresponse').empty();
      $('#assistantForm').trigger("reset");      
      var id = $(this).attr('data-id');
      $(".assistant_id").show();
      $.get("{{ route('ajaxassistants.index') }}" +'/' + id +'/edit', function (data) {
          $('#modelHeading').html("Edit Assistant");
          $('#saveBtn').val("edit-user");
          $('#ajaxModel').modal({
            backdrop:'static',
            keyboard: false
          })
          //console.log(data.id)
          $('#assistant_id').val(pad(data.id,8));
          $('#logistics_company').val(data.logistics_company);
          $('#first_name').val(data.first_name);
         
          $('#last_name').val(data.last_name);

          $('#mobile_number').val(data.mobile_number);

          $('#company_id_number').val(data.company_id_number);

          $('#valid_id_present').val(data.valid_id_present);
           $('#valid_id_number').val(data.valid_id_number);

          var x = data.supplier_ids.split("|")
     
          $.each( x, function( key, e ) {
            e = e.trim()
            if(e != ""){
              $('#assistant_suppliers option[value="' + e + '"]').prop('selected', true);
            }
            
          });

          // $('#assistant_suppliers').val(data.supplier_names);
          // $('#supplier_ids').val(data.supplier_ids);
          $('.assistant_supplier').dropdown({
            limitCount: 40,
            multipleMode: 'label',
            // callback
            choice: function (event, selectedProp,x) {
              
            },
          });

          if(data.dateOfSafetyOrientation != null || data.dateOfSafetyOrientation != undefined){

            data.dateOfSafetyOrientation = data.dateOfSafetyOrientation.replace(" ","T")
            document.getElementById("dateOfSafetyOrientation").value = data.dateOfSafetyOrientation;

          }
          // $('#delivery_type').val(data.delivery_type);
      })
   });
    
    $('#saveBtn').click(function (e) {
        if(role_id == 3 && $("#dateOfSafetyOrientation").val() == ""){
          $("#isApproved").val('0')
        }

        if($("#logistics_company").val() == "" || $("#first_name").val() == "" || $("#last_name").val() == "" || $("#mobile_number").val() == "" || $("#company_id_number").val() == "" || $("#valid_id_present").val() == "" || $("#valid_id_number").val() == "" || $("#assistant_suppliers").val() == ""){ 


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

           if($("#valid_id_present").val() == "")
             $("#valid_id_present").css('outline','1px solid red')
            else
             $("#valid_id_present").css('outline','1px solid black')

           if($("#valid_id_number").val() == "")
             $("#valid_id_number").css('outline','1px solid red')
            else
             $("#valid_id_number").css('outline','1px solid black')

           if($("#assistant_suppliers").val() == "")
             $("#assistant_suppliers").css('outline','1px solid red')
            else
             $("#assistant_suppliers").css('outline','1px solid black')

            return false;
        }else{

            e.preventDefault();
            $(this).html('Sending..');
        
            console.log($('#assistantForm').serialize())
            $.ajax({
              data: $('#assistantForm').serialize(),
              url: "{{ route('ajaxassistants.store') }}",
              type: "POST",
              dataType: 'json',
              success: function (data) {
                 $('#response').html("<div class='alert alert-success'>"+data.success+"</div>")
                  $('#assistantForm').trigger("reset");
                  $('#ajaxModel').modal('hide');
                  setTimeout(function(){
                    $('#response').hide("slow");
                  },3000)
                  table.draw();
                  loadViewPending();
                   $('#saveBtn').html('Save Changes');
              },
              error: function (data) {
                  console.log('Error:', data);
                  $('#saveBtn').html('Save Changes');
              }
          });
      }
    });
    
    $('body').on('click', '.deactivateOrActivateAssistant', function () {
     
        var id = $("#btn-deactivate").attr("data-id");
        var status = $("#btn-deactivate").attr("data-status");
        
        if (confirm("Are you want to proceed?")){
            $.ajax({
                url: "{{ url('deactivateOrActivateAssistant') }}",
                type: "POST",
                data: {id:id, status:status},
                success: function (data) {
                    $('#response').html("<div class='alert alert-success'>"+data.success+"</div>")
                    table.draw();
                    loadViewPending();
                    $("#ajaxModelView").modal("hide")
                },
                error: function (data) {
                    console.log('Error:', data);
                }
            });

        }
    });



    var assistant_suppliers = '';
    var supplier_ids = '';
    $('body').on('click', '.add_supplier', function(){
      
      var _supplier_id = $('#supplier_id').children("option:selected").val();
      var _supplier_name = $('#supplier_id').children("option:selected").text();
      var a_suppliers = $('#supplier_ids').val();
      var a_assistant_suppliers = $('#assistant_suppliers').val();
      if(!a_suppliers.includes(_supplier_id) || !a_suppliers.includes(_supplier_id)){
          a_suppliers += _supplier_id + '|';
          a_assistant_suppliers += _supplier_name + ' | ';
          $('.modalresponse').empty();
      }else{
        $('.modalresponse').html("<div class='alert alert-danger'>Supplier already added.</div>")
      }

      // console.log(supplier_ids);
      // console.log(assistant_suppliers);

      $('#assistant_suppliers').val(a_assistant_suppliers);
      $('#supplier_ids').val(a_suppliers);
    });

    $('body').on('click', '.clear_supplier', function(){
      
      $('#supplier_ids').val("");
      $('#assistant_suppliers').val("")

      assistant_suppliers = "";
      supplier_ids = "";
    });

    $('.data-table tbody').on( 'click', 'tr', function () {
            $('.assistant_supplier').remove()
            var id = $(this).find("td:nth-child(1)").first().text().trim()
            $.ajax({
              data: {id:id},
              url: "{{ url('getAssistant') }}",
              type: "POST",
              dataType: 'json',
              success: function (data) {
                  console.log(data)
                  

                  $('#view_supplier_name').html(data.supplier_names)
                  $('#view_logistics').html(data.logistics_company)
                  $('#view_full_name').html(data.first_name + " " + data.last_name)
                  $('#view_mobile_no').html(data.mobile_number)
                  $('#view_company_id_number').html(data.company_id_number)
                  $('#view_valid_id_present').html(data.valid_id_present)
                  $('#view_valid_id_number').html(data.valid_id_number)
                  $('#view_validity_date').html(data.dateOfSafetyOrientation) 
                  $("#btn-edit").attr("data-id",data.id)
                  $("#btn-deactivate").attr("data-id",data.id)
                  
                  $("#btn-approved").attr("data-id",data.id)
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

          $.ajax({
            type: 'POST', 
            url: "{{ url('getAllSupplier') }}",
            dataType: 'json',
            data:{ _token: "{{csrf_token()}}"},
            success: function (data) {
                console.log(data);
                $('#assistant_supplier_add').append('<div class="assistant_supplier"><select class="form-control" id="assistant_suppliers" style="display:none"  name="assistant_suppliers[]" multiple></select></div>')
                $.each(data, function(index, item) {
                    $('#assistant_suppliers').append('<option value="'+item.id+'">'+item.supplier_name+'</option>')
                });
            },error:function(){ 
                 console.log(data);
            }
          });
    });

  });


  // $('#supplier_id option').mousedown(function(e) {
  //     e.preventDefault();
  //     var originalScrollTop = $(this).parent().scrollTop();
  //     //onsole.log(originalScrollTop);
  //     $(this).prop('selected', $(this).prop('selected') ? false : true);
  //     var self = this;
  //     $(this).parent().focus();
  //     setTimeout(function() {
  //         $(self).parent().scrollTop(originalScrollTop);
  //     }, 0);
      
  //     var data = "";
  //     var a_suppliers = "";
  //     var $el=$("#supplier_id");
  //     $el.find('option:selected').each(function(){
  //         data += $(this).text() + " | ";
  //         a_suppliers += $(this).val() + " | ";
  //     });
  //     //console.log(data)
  //     //console.log($('.supplier_id').val(data))

  //     var _supplier_id = $('#supplier_id').children("option:selected").val();
      
  //     $('#assistant_suppliers').val(data);
  //     $('#supplier_ids').val(a_suppliers);
  //     //return false;
  // });

// var value = ""
// $('.assistant_supplier').dropdown({
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
//     var d_driver_suppliers = $('#assistant_suppliers').val();
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
//           $('#assistant_suppliers').val(d_driver_suppliers);
//           $('#supplier_ids').val(d_suppliers); 
//         }
//         if(selectedProp.selected == false){
//           if(d_suppliers.includes(selectedProp.id+ '|')){
//               console.log(d_suppliers)
//               d_suppliers.replace(selectedProp.id + '|',"");
//               d_driver_suppliers.replace(selectedProp.name + '|',"");
//               $('#assistant_suppliers').val(d_driver_suppliers.replace(selectedProp.name + '|',""));
//           $('#supplier_ids').val(d_suppliers.replace(selectedProp.id + '|',""));
//           }
//         }
//     }
//   },
// });

// $('div.assistant_supplier > a.dropdown-clear-all').on('click',function(){
//   $('#assistant_suppliers').val('');
//           $('#supplier_ids').val('');
// });
</script>
@endsection
