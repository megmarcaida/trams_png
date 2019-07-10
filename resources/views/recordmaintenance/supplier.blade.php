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
    <li class="breadcrumb-item active">Suppliers</li>
  </ol>

  <div class="row">
    <div class="col-xl-12 col-sm-12 mb-3">
      <h1>Suppliers</h1>
      <div class="row">
        <div class="col-xl-6 col-md-6 col-sm-12">
          <div class="btn-group" role="group">
            <a class="btn btn-success" href="javascript:void(0)" id="createNewProduct"> Register Supplier</a>
            <a class="btn btn-warning" href="{{ route('exportSupplier') }}">Export Suppliers</a>
            <!-- <a class="btn btn-secondary" href="https://srv-file2.gofile.io/download/71DQPA/suppliers_template.xlsx">Download Template</a> -->
          </div>
        </div>
        <div class="col-xl-6 col-md-6 col-sm-12">  
          <form action="{{ route('importSupplier') }}" method="POST" enctype="multipart/form-data">
              @csrf
              <div class="input-group mb-3">
                <input type="file" name="file" class="form-control">
                <div class="input-group-append">
                  <button class="btn btn-success"><span class="fa fa-upload"></span> </button>
                  <a class="btn btn-secondary" href="{{ Storage::url('template/suppliers_template.xlsx') }}">Download Template</a>
                </div>
              </div>
              <br>
              
              <!-- <a class="btn btn-secondary" href="{{ Storage::url('template/suppliers_template.xlsx') }}">Download Template Data</a> -->
              
          </form>    
        </div>
      </div>
         <div id="response">
          @if(session()->has('import_message'))
            <div class="alert alert-success">
                {{ session()->get('import_message') }}
            </div>
        @endif
        </div>
        <div class="row">
          <div class="col-md-3">
              
            <div class="form-group">
               <label for="name" class="col-sm-12 control-label">*Filter Delivery Type</label>
               <div class="col-sm-12 dt_dd">
                  <select multiple="true" style="display: none;" class="form-control delivery_type_filter" id="delivery_type_filter" name="delivery_type_filter[]">
                       <option value="">All</option>
                       <option value="Local">Local</option>
                       <option value="Imported">Imported</option>
                  </select>
                </div>
            </div>
          </div>

          <div class="col-md-3">
              <div class="form-group">
               <label for="name" class="col-sm-12 control-label">*Filter Ordering Days</label>
               <div class="col-sm-12 or_dd">
                  <select multiple="true" style="display: none;" class="form-control ordering_days_filter" id="ordering_days_filter">
                       <option value="">All</option>
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
          <div class="col-md-3">
              <div class="form-group md_dd">
               <label for="name" class="col-sm-12 control-label">*Filter Module Days</label>
               <div class="col-sm-12">
                  <select multiple="true" style="display: none;" class="form-control module_filter" id="module_filter" name="module_filter[]">
                       <option value="PCC">PCC</option>
                       <option value="Baby Care">Baby Care</option>
                       <option value="Laundry">Laundry</option>
                       <option value="Liquids">Liquids</option>
                       <option value="Fem Care">Fem Care</option>
                  </select>
                </div>
            </div>
          </div>
          <div class="col-md-3">
              
            <div class="form-group">
               <label for="name" class="col-sm-12 control-label">*Filter Status</label>
               <div class="col-sm-12 fs_dd">
                  <select multiple="true" style="display: none;" class="form-control status_filter" id="status_filter" name="status_filter[]">
                       <option value="">All</option>
                       <option value="Active">Active</option>
                       <option value="Inactive">Inactive</option>
                  </select>
                </div>
            </div>
          </div>
        </div>
        

        <div class="table table-responsive">
          <table class="table table-bordered data-table">
              <thead>
                  <tr>
                      <th>Vendor Code</th>
                      <th>Supplier Name</th>
                      <th>Delivery Type</th>
                      <th>Ordering Days</th>
                      <th>Module</th>
                      <th>Created At</th>
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
<!-- /.container-fluid -->

   
<div class="modal fade" id="ajaxModel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="width:960px;margin-left:-220px;">
            <div class="modal-header">
                <h4 class="modal-title" id="modelHeading"></h4>

                <button type="button" class="close" data-dismiss="modal">&times;</button> 
            </div>
            <div class="modal-body">
                <form id="supplierForm" name="supplierForm" class="form-horizontal">
                    
                    <input type="hidden" name="supplier_id" id="supplier_id">

                    <div class="row">
                      
                      <!-- first column -->
                      <div class="col-md-6">

                          <!-- vendor code -->
                          <div class="form-group">
                              <label for="name" class="col-sm-12 control-label">*Vendor Code</label>
                              <div class="col-sm-12">
                                  <input type="text" class="form-control" id="vendor_code" name="vendor_code" placeholder="Enter Vendor Code" value="" maxlength="100" required="">
                              </div>
                          </div>

                          <!-- supplier name -->
                          <div class="form-group">
                              <label for="name" class="col-sm-12 control-label">*Supplier Name</label>
                              <div class="col-sm-12">
                                  <input type="text" class="form-control" id="supplier_name" name="supplier_name" placeholder="Enter Name" value="" maxlength="50" required="">
                              </div>
                          </div>

                          <!-- delivery types -->
                          <div class="form-group">
                             <label for="name" class="col-sm-12 control-label">*Delivery Types</label>
                             <div class="col-sm-12">
                                <select class="form-control delivery_type" id="delivery_types" name="delivery_types">
                                     <option value="">Please select Delivery Type</option>
                                     <option value="Local">Local</option>
                                     <option value="Imported">Imported</option>
                                </select>
                              </div>
                          </div>

                          <!-- ordering days -->
                          <div class="form-group">
                             <label for="name" class="col-sm-12 control-label">*Ordering Days</label>
                             <div id="ordering_days_add"></div>
                             <div class="col-sm-12 ordering_days_dd">
                                <select multiple="true" class="form-control ordering_days" style="display: none;" id="ordering_days" name="ordering_days[]">
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

                          <!-- module -->
                          <div class="form-group">
                             <label for="name" class="col-sm-12 control-label">*Module</label>
                             <div id="module_add"></div>
                             <div class="col-sm-12 modules_dd">
                                <select multiple="true" class="form-control module" id="modules" style="display: none;" name="module[]">
                                     <option value="PCC">PCC</option>
                                     <option value="Baby Care">Baby Care</option>
                                     <option value="Laundry">Laundry</option>
                                     <option value="Liquids">Liquids</option>
                                     <option value="Fem Care">Fem Care</option>
                                </select>
                              </div>
                          </div>

                      </div>

                      <!-- second column -->
                      <div class="col-md-6">
                          <div class="form-group"><label for="name" class="col-sm-12 control-label">SPOC First Name</label><div class="col-sm-12"><input type="text" class="form-control" id="spoc_first_name0" name="spoc_first_name[]" placeholder="Enter SPOC First Name" value="" maxlength="50" required=""></div></div><div class="form-group"><label for="name" class="col-sm-12 control-label">SPOC Last Name</label><div class="col-sm-12"><input type="text" class="form-control" id="spoc_last_name0" name="spoc_last_name[]" placeholder="Enter SPOC Last Name" value="" maxlength="50" required=""></div></div><div class="form-group"><label for="name" class="col-sm-12 control-label">SPOC Contact Number</label><div class="col-sm-12"><input type="text" class="form-control" id="spoc_contact_number0" name="spoc_contact_number[]" placeholder="Enter SPOC Contact Number" value="" maxlength="50" required=""></div></div><div class="form-group"><label for="name" class="col-sm-12 control-label">SPOC Email Address</label><div class="col-sm-12"><input type="email" class="form-control" id="spoc_email_address0" name="spoc_email_address[]" placeholder="Enter SPOC Email Address" value="" maxlength="150" required=""></div></div>

                          <div id="spoc"></div>


                          <div class="col-sm-12 text-right">
                             <button type="submit" class="btn btn-warning" id="addSpocBtn" value="create">Add SPOC
                             </button>
                          </div>
                      </div>

                    </div>

                    

                    
                    
                    <!-- <div class="form-group">
                      <label class="col-sm-12 control-label delivery_type">*Delivery Type</label>
                      <div class="col-sm-12">
                        <div class="form-check form-check-inline">
                          <input class="form-check-input" type="radio" name="delivery_types" class="delivery_type" value="Local">
                          <label class="form-check-label" for="inlineRadio1">Local</label>
                        </div>
                        <div class="form-check form-check-inline">
                          <input class="form-check-input" type="radio" name="delivery_types" class="delivery_type" value="Imported">
                          <label class="form-check-label" for="inlineRadio1">Imported</label>
                        </div>
                      </div>
                    </div> -->

                    

                    <!-- <div class="form-group">
                      <label class="col-sm-12 control-label ordering_days">*Ordering Days</label>
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
                    

                    <!-- <div class="form-group">
                      <label class="col-sm-12 control-label module">*Module</label>
                      <div class="col-sm-12">
                        <div class="form-check form-check-inline">
                          <input class="form-check-input" type="checkbox" name="module[]" class="module" id="module_baby_care" value="Baby Care">
                          <label class="form-check-label" for="inlineCheckbox1">Baby Care</label>
                        </div>
                        <div class="form-check form-check-inline">
                          <input class="form-check-input" type="checkbox" name="module[]" class="module" id="module_laundry" value="Laundry">
                          <label class="form-check-label" for="inlineCheckbox2">Laundry</label>
                        </div>
                        <div class="form-check form-check-inline">
                          <input class="form-check-input" type="checkbox" name="module[]" class="module" id="module_fem_care" value="Fem Care">
                          <label class="form-check-label" for="inlineCheckbox2">Fem Care</label>
                        </div>
                        <br>
                        <div class="form-check form-check-inline">
                          <input class="form-check-input" type="checkbox" name="module[]" class="module" id="module_fe" value="FE">
                          <label class="form-check-label" for="inlineCheckbox2">FE</label>
                        </div>
                        <div class="form-check form-check-inline">
                          <input class="form-check-input" type="checkbox" name="module[]" class="module" id="module_dish" value="Dish">
                          <label class="form-check-label" for="inlineCheckbox2">Dish</label>
                        </div>
                        <div class="form-check form-check-inline">
                          <input class="form-check-input" type="checkbox" name="module[]" class="module" id="module_pcc" value="PCC">
                          <label class="form-check-label" for="inlineCheckbox2">PCC</label>
                        </div>
                        </div>
                      </div> -->

                      

                    <hr>

                    <br>

                    <div class="offset-8 col-md-4 col-sm-12">
                       <button type="submit" class="btn btn-primary btn-block" id="saveBtn" value="create">Save changes
                       </button>
                    </div>

                    <br>
                    <div id="modalresponse"></div> 
                </form>

            </div>
        </div>
    </div>
</div>
    
<div class="modal fade" id="ajaxModelView" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">View Supplier</h4>

                <button type="button" class="close" data-dismiss="modal">&times;</button> 
            </div>
            <div class="modal-body">
                <div class="row">
                        <div class="col-md-6" style="line-height: 0px">
                          Vendor Code:
                        </div>
                        <div class="col-md-6" style="line-height: 0px">
                          <b><p id="view_vendor_code"></p></b>
                        </div>
                     
                        <div class="col-md-6" style="line-height: 0px">
                          Supplier Name:
                        </div>
                        <div class="col-md-6" style="line-height: 0px">
                          <b><p id="view_supplier_name"></p></b>
                        </div>
                   
                        <div class="col-md-6" style="line-height: 0px">
                          Delivery Type:
                        </div>
                        <div class="col-md-6" style="line-height: 0px">
                          <b><p id="view_delivery_type"></p></b>
                        </div>
                    
                        <div class="col-md-6" style="line-height: 0px">
                          Ordering Days:
                        </div>
                        <div class="col-md-6" style="line-height: 0px">
                          <b><p id="view_ordering_days"></p></b>
                        </div>
                     
                        <div class="col-md-6" style="line-height: 0px">
                          Modules:
                        </div>
                        <div class="col-md-6" style="line-height: 0px">
                          <b><p id="view_modules"></p></b>
                        </div>
                    
                       
                </div>
                <div class="row">
                  <div class="col-md-12 spoc text-center">
                    SPOC
                    <table class="table table-responsive table-condensed view_spoc"></table>
                  </div>
                </div>
                <br>
                <div class="row">
                  <div class="col-xl-4 col-md-4 col-sm-12">
                    <button id="btn-edit" class="btn btn-primary btn-xs btn-block editProduct" type="button">Edit</button>
                  </div>
                  <div class="col-xl-4 col-md-4 col-sm-12">
                    <button id="btn-deactivate" class="btn btn-secondary btn-xs btn-danger btn-block deactivateOrActivateSupplier" type="button">Deactivate</button>
                  </div>
                  <div class="col-xl-4 col-md-4 col-sm-12">  
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
                 "url": "{{ url('allsuppliers') }}",
                 "dataType": "json",
                 "type": "POST",
                 "data":{ _token: "{{csrf_token()}}"}
               },
        "columns": [
            // { "data": "id" },
            {"data": 'vendor_code'},
            {"data": 'supplier_name'},
            {"data": 'delivery_type'},
            {"data": 'ordering_days'},
            {"data": 'module'},
            // {"data": 'spoc_fullname'},
            // {"data": 'spoc_contact_number'},
            // {"data": 'spoc_email_address'},
            { "data": "created_at" },
            { "data": "status"},
            // { "data": "options" },
        ],


    });

    $('.or_dd').dropdown({
      limitCount: 40,
      multipleMode: 'label',
      // callback
      choice: function (event, selectedProp,x) {
        console.log(selectedProp.name)
        table.search(selectedProp.name).draw();   
      },
    });

    $('.dt_dd').dropdown({
      limitCount: 40,
      multipleMode: 'label',
      // callback
      choice: function (event, selectedProp,x) {
        console.log(selectedProp.name)
        table.search(selectedProp.name).draw();   
      },
    });

    $('.md_dd').dropdown({
      limitCount: 40,
      multipleMode: 'label',
      // callback
      choice: function (event, selectedProp,x) {
        console.log(selectedProp.name)
        table.search(selectedProp.name).draw();   
      },
    });

    $('.fs_dd').dropdown({
      limitCount: 40,
      multipleMode: 'label',
      // callback
      choice: function (event, selectedProp,x) {
        console.log(selectedProp.name)
        table.search($("#status_filter").val()).draw();   
      },
    });

    $('#delivery_type_filter').on('change', function(){
       table.search(this.value).draw();   
    });
    $('#ordering_days_filter').on('change', function(){
        var ordering_days = "ordering_days|";
        $.each($(".ordering_days_filter option:selected"), function(){            
            ordering_days += this.value + " | "
        }); 

       console.log(ordering_days)
       table.search(ordering_days).draw();   
    });
    $('#module_filter').on('change', function(){
        var module_ = "module|";
        $.each($(".module_filter option:selected"), function(){            
            module_ += this.value + " | "
        }); 

       console.log(module_)
       table.search(module_).draw();   
    });
    $('#status_filter').on('change', function(){
       table.search(this.value).draw();   
    });
    // var table = $('.data-table').DataTable({
    //     processing: true,
    //     serverSide: true,
    //     ajax: "{{ route('ajaxsuppliers.index') }}",
    //     columns: [
    //         //{data: 'DT_RowIndex', name: 'DT_RowIndex'},
    //         {data: 'vendor_code', name: 'vendor_code'},
    //         {data: 'supplier_name', name: 'supplier_name'},
    //         {data: 'delivery_type', name: 'delivery_type'},
    //         {data: 'ordering_days', name: 'ordering_days'},
    //         //{data: 'module', name: 'module'},
    //         // {data: 'spoc_firstname', name: 'spoc_firstname'},
    //         // {data: 'spoc_lastname', name: 'spoc_lastname'},
    //         // {data: 'spoc_contact_number', name: 'spoc_contact_number'},
    //         // {data: 'spoc_email_address', name: 'spoc_email_address'},
    //         {data: 'action', name: 'action', orderable: false, searchable: false}
    //     ]
    // });
     
    $('#createNewProduct').click(function () {
        $('#spoc').empty();
        $('#saveBtn').val("create-product");
        $('#supplier_id').val('');
        $('#supplierForm').trigger("reset");
        $('#modelHeading').html("Register Supplier");
        $('#ajaxModel').modal({
          backdrop:'static',
          keyboard: false
        })
        $('.ordering_days_dd').remove()
        $('#ordering_days_add').append('<div class="col-sm-12 ordering_days_dd"><select multiple="true" class="form-control ordering_days" style="display: none;" id="ordering_days" name="ordering_days[]"><option value="Mon">Mon</option><option value="Tue">Tue</option><option value="Wed">Wed</option><option value="Thu">Thu</option><option value="Fri">Fri</option><option value="Sat">Sat</option><option value="Sun">Sun</option></select></div>')
        $('.modules_dd').remove()
        $('#module_add').append('<div class="col-sm-12 modules_dd"><select multiple="true" class="form-control module" id="modules" style="display: none;" name="module[]"><option value="PCC">PCC</option><option value="Baby Care">Baby Care</option><option value="Laundry">Laundry</option><option value="Liquids">Liquids</option><option value="Fem Care">Fem Care</option></select></div>')
        $('.ordering_days_dd').dropdown({
          limitCount: 40,
          multipleMode: 'label',
          // callback
          choice: function (event, selectedProp,x) {
            
          },
        });
        $('.modules_dd').dropdown({
          limitCount: 40,
          multipleMode: 'label',
          // callback
          choice: function (event, selectedProp,x) {
            
          },
        });
    });
    
    $('body').on('click', '.editProduct', function () {
      $("#ajaxModelView").modal("hide")
      $('#spoc').empty();
      $('#supplierForm').trigger("reset");      
      var supplier_id = $("#btn-deactivate").attr('data-id');
      $.get("{{ route('ajaxsuppliers.index') }}" +'/' + supplier_id +'/edit', function (data) {
          $('#modelHeading').html("Edit Product");
          $('#saveBtn').val("edit-user");
          $('#ajaxModel').modal({
            backdrop:'static',
            keyboard: false
          })
          $('#supplier_id').val(data.id);
          $('#vendor_code').val(data.vendor_code);
          $('#supplier_name').val(data.supplier_name);
         
          console.log(data.ordering_days)
          var ordering_days_arr = data.ordering_days.split("|")
          var modules_arr = data.module.split("|")
          var spoc_first_name_arr = data.spoc_firstname.split("<br>")
          var spoc_last_name_arr = data.spoc_lastname.split("<br>")
          var spoc_contact_number_arr = data.spoc_contact_number.split("<br>")
          var spoc_email_address_arr = data.spoc_email_address.split("<br>")
          var spoc_length = spoc_first_name_arr.length - 1;

          $('.ordering_days_dd').remove()
          $('#ordering_days_add').append('<div class="col-sm-12 ordering_days_dd"><select multiple="true" class="form-control ordering_days" style="display: none;" id="ordering_days" name="ordering_days[]"><option value="Mon">Mon</option><option value="Tue">Tue</option><option value="Wed">Wed</option><option value="Thu">Thu</option><option value="Fri">Fri</option><option value="Sat">Sat</option><option value="Sun">Sun</option></select></div>')
          $('.modules_dd').remove()
          $('#module_add').append('<div class="col-sm-12 modules_dd"><select multiple="true" class="form-control module" id="modules" style="display: none;" name="module[]"><option value="PCC">PCC</option><option value="Baby Care">Baby Care</option><option value="Laundry">Laundry</option><option value="Liquids">Liquids</option><option value="Fem Care">Fem Care</option></select></div>')

          console.log(spoc_length)
          console.log(ordering_days_arr)
          $.each( ordering_days_arr, function( key, e ) {
            e = e.trim()
            $("#ordering_days option[value='" + e + "']").prop("selected", true);
          });

          $.each( modules_arr, function( key, e ) {
            e = e.trim()
            $("#modules option[value='" + e + "']").prop("selected", true);
          });

          for (var i = 1; i < spoc_length; i++) {
            $("#spoc").append('<div class="appendedSpoc'+ i+'"><hr><a href="#" class="removeSpoc btn btn-danger" data-id="'+i+'">Remove</a><div class="form-group"><label for="name" class="col-sm-12 control-label">SPOC First Name</label><div class="col-sm-12"><input type="text" class="form-control" id="spoc_first_name'+i+'" name="spoc_first_name[]" placeholder="Enter SPOC First Name" value="" maxlength="50" required=""></div></div><div class="form-group"><label for="name" class="col-sm-12 control-label">SPOC Last Name</label><div class="col-sm-12"><input type="text" class="form-control" id="spoc_last_name'+i+'" name="spoc_last_name[]" placeholder="Enter SPOC Last Name" value="" maxlength="50" required=""></div></div><div class="form-group"><label for="name" class="col-sm-12 control-label">SPOC Contact Number</label><div class="col-sm-12"><input type="text" class="form-control" id="spoc_contact_number'+i+'" name="spoc_contact_number[]" placeholder="Enter SPOC Contact Number" value="" maxlength="50" required=""></div></div><div class="form-group"><label for="name" class="col-sm-12 control-label">SPOC Email Address</label><div class="col-sm-12"><input type="email" class="form-control" id="spoc_email_address'+i+'" name="spoc_email_address[]" placeholder="Enter SPOC Email Address" value="" maxlength="150" required=""></div></div></div>');
          }

          $.each( spoc_first_name_arr, function( key, value ) {
            $("#spoc_first_name"+key).val($.trim(value.replace("<br>","")))
            //console.log($.trim(value.replace("<br>","")));
          });

          $.each( spoc_last_name_arr, function( key, value ) {
             $("#spoc_last_name"+key).val($.trim(value.replace("<br>","")))
            //console.log($.trim(value.replace("<br>","")));
          });

          $.each( spoc_contact_number_arr, function( key, value ) {
             $("#spoc_contact_number"+key).val($.trim(value.replace("<br>","")))
            //console.log($.trim(value.replace("<br>","")));
          });

          $.each( spoc_email_address_arr, function( key, value ) {
            $("#spoc_email_address"+key).val($.trim(value.replace("<br>","")))
            //console.log($.trim(value.replace("<br>","")));
          });

          // $("input[name=delivery_types][value=" + data.delivery_type + "]").prop('checked', 'checked');
          $('#delivery_types').val(data.delivery_type)

          $('.ordering_days_dd').dropdown({
            limitCount: 40,
            multipleMode: 'label',
            // callback
            choice: function (event, selectedProp,x) {
              
            },
          });
        $('.modules_dd').dropdown({
          limitCount: 40,
          multipleMode: 'label',
          // callback
          choice: function (event, selectedProp,x) {
            
          },
        });
      })
   });
    
    $('#saveBtn').click(function (e) {
        
      //console.log($("#delivery_type").prop('checked'));

        //var delivery_types = $(':radio[name^=delivery_types]:checked').length;

        var delivery_types = [];
        $.each($("#delivery_types option:selected"), function(){            
            delivery_types.push($(this).val());
        });
          
        // var ordering_days = $(':checkbox[name^=ordering_days]:checked').length;

        var ordering_days = [];
        $.each($("#ordering_days option:selected"), function(){            
            ordering_days.push($(this).val());
        });

        //var modules = $(':checkbox[name^=module]:checked').length;
        var modules = [];
        $.each($("#modules option:selected"), function(){            
            modules.push($(this).val());
        });

        

        if($("#vendor_code").val() == "" || $("#supplier_name").val() == "" || delivery_types.length==0 || ordering_days.length == 0 || modules.length == 0){
          $("#modalresponse").html("<div class='alert alert-danger'>Please fill in the required fields.</div>")

            if($("#vendor_code").val() == "")
              $("#vendor_code").css('outline','1px red solid')
            else
              $("#vendor_code").css('outline','1px black solid')
          
            if($("#supplier_name").val() == "")
              $("#supplier_name").css('outline','1px red solid')
            else
              $("#supplier_name").css('outline','1px black solid')

            // if($(".delivery_type").prop('checked') == false)

            // if($("input[name='delivery_types[]']:checked"))
            //   $(".delivery_type").css('color','red')
            // else
            //   $(".delivery_type").css('color','black')

            
            if(delivery_types.length == 0)
              $(".delivery_type").css('outline','1px red solid')
            else
              $(".delivery_type").css('outline','1px black solid')

            if(ordering_days.length == 0)
              $("#ordering_days").css('outline','1px red solid')
            else
              $("#ordering_days").css('outline','1px black solid')
            console.log(ordering_days.length)
            if(modules.length == 0)
              $("#modules").css('outline','1px red solid')
            else
              $("#modules").css('outline','1px black solid')

          $('#modalresponse').fadeIn(1000);
          setTimeout(function(){
            $('#modalresponse').fadeOut(1000);
          },2000)
        }
        else{

            e.preventDefault();
            $(this).html('Sending..');
        
            console.log($('#supplierForm').serialize())
            $.ajax({
              data: $('#supplierForm').serialize(),
              url: "{{ route('ajaxsuppliers.store') }}",
              type: "POST",
              dataType: 'json',
              success: function (data) {
                if(data.success != null){
                 $('#response').html("<div class='alert alert-success'>"+data.success+"</div>")
                  $('#supplierForm').trigger("reset");
                  $('#ajaxModel').modal('hide');
                  setTimeout(function(){
                    $('#response').hide("slow");
                  },3000)
                  table.draw();
                }else if(data.error != null){ 
                    $('#modalresponse').html("<div class='alert alert-danger'>"+data.error+"</div>")
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
    
    $('body').on('click', '.deactivateOrActivateSupplier', function () {
     
        var supplier_id = $("#btn-deactivate").attr("data-id");
        var status = $("#btn-deactivate").attr("data-status");
        console.log(status)
        if (confirm("Are you want to proceed?")){
            $.ajax({
                url: "{{ url('deactivateOrActivateSupplier') }}",
                type: "POST",
                data: {id:supplier_id, status:status},
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


    $('body').on('click','.removeSpoc',function(){
      console.log('test');
      var id = $(this).attr("data-id");
      console.log(id);
      $(".appendedSpoc"+id).remove();
    
    });
      

    var count = 1;
    $("#addSpocBtn").click(function(e){

        $("#spoc").append('<div class="appendedSpoc'+ count+'"><hr><a href="#" class="removeSpoc btn btn-danger" style="float:right;" data-id="'+count+'">Remove</a><div class="form-group"><label for="name" class="col-sm-12 control-label">SPOC First Name</label><div class="col-sm-12"><input type="text" class="form-control" class="spoc_first_name" name="spoc_first_name[]" placeholder="Enter SPOC First Name" value="" maxlength="50" required=""></div></div><div class="form-group"><label for="name" class="col-sm-12 control-label">SPOC Last Name</label><div class="col-sm-12"><input type="text" class="form-control" class="spoc_last_name" name="spoc_last_name[]" placeholder="Enter SPOC Last Name" value="" maxlength="50" required=""></div></div><div class="form-group"><label for="name" class="col-sm-12 control-label">SPOC Contact Number</label><div class="col-sm-12"><input type="text" class="form-control" class="spoc_contact_number" name="spoc_contact_number[]" placeholder="Enter SPOC Contact Number" value="" maxlength="50" required=""></div></div><div class="form-group"><label for="name" class="col-sm-12 control-label">SPOC Email Address</label><div class="col-sm-12"><input type="email" class="form-control" class="spoc_email_address" name="spoc_email_address[]" placeholder="Enter SPOC Email Address" value="" maxlength="150" required=""></div></div></div>');

        count++;
    });


    $('.data-table tbody').on( 'click', 'tr', function () {
            var vendor_code = $(this).find("td:nth-child(1)").first().text().trim()
            $.ajax({
              data: {vendor_code:vendor_code},
              url: "{{ url('getSupplier') }}",
              type: "POST",
              dataType: 'json',
              success: function (data) {

                  $("#view_vendor_code").html(data.vendor_code)
                  $("#view_supplier_name").html(data.supplier_name)
                  $("#view_delivery_type").html(data.delivery_type)
                  $("#view_ordering_days").html(data.ordering_days)
                  $("#view_modules").html(data.module)

                  console.log(data)
                  var spoc_full_name = data.spoc_full_name.split("<br>") 
                  var spoc_email_address = data.spoc_email_address.split("<br>")
                  var spoc_contact_number = data.spoc_contact_number.split("<br>")    
                  var table = $('.view_spoc');
                  table.html('');
                  table.append("<thead><tr><th>Full Name</th><th>Email Address</th><th>Contact Number</th></tr></thead>")
                  table.append("<tbody>")
                  $.each(spoc_full_name, function( key, value ) {
                    table.append("<tr><td>" + value.replace("<br>","") + "</td><td>"+ spoc_email_address[key].replace("<br>","") +"</td><td>"+ spoc_contact_number[key].replace("<br>","") +"</td></tr>")
                  });    
                  table.append("</tbody>")

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
