@extends('layouts.datatableapp')

@section('content')

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
        <div class="col-xl-6">
          <a class="btn btn-success" href="javascript:void(0)" id="createNewProduct"> Register Supplier</a>
          <a class="btn btn-warning" href="{{ route('exportSupplier') }}">Export Suppliers Data</a>
        </div>
        <div class="col-xl-3">  
          <form action="{{ route('importSupplier') }}" method="POST" enctype="multipart/form-data">
              @csrf
              <input type="file" name="file" class="form-control">
              <br>
              <button class="btn btn-success text-right">Import Supplier Data</button>
               <a class="btn btn-secondary" href="{{ route('exportSupplier') }}">Download Template Data</a>
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
        <div class="table table-responsive">
          <table class="table table-bordered data-table">
              <thead>
                  <tr>
                      <th>Vendor Code</th>
                      <th>Supplier Name</th>
                      <th>Delivery Type</th>
                      <th>Ordering Days</th>
                      <th>Module</th>
                      <th>SPOC Full Name</th>
                      <th>SPOC Contact Number</th>
                      <th>SPOC Email Address</th>
                      <th>Created At</th>
                      <th>Status</th>
                      <th>Options</th>
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
                <form id="supplierForm" name="supplierForm" class="form-horizontal">
                    
                    <input type="hidden" name="supplier_id" id="supplier_id">

                    <div class="form-group">
                        <label for="name" class="col-sm-12 control-label">*Vendor Code</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="vendor_code" name="vendor_code" placeholder="Enter Vendor Code" value="" maxlength="100" required="">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="name" class="col-sm-12 control-label">*Supplier Name</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="supplier_name" name="supplier_name" placeholder="Enter Name" value="" maxlength="50" required="">
                        </div>
                    </div>
                    
                    <div class="form-group">
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
                    </div>

                    <div class="form-group">
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
                    </div>

                    <div class="form-group">
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
                      </div>

                    <hr><div class="form-group"><label for="name" class="col-sm-12 control-label">SPOC First Name</label><div class="col-sm-12"><input type="text" class="form-control" id="spoc_first_name0" name="spoc_first_name[]" placeholder="Enter SPOC First Name" value="" maxlength="50" required=""></div></div><div class="form-group"><label for="name" class="col-sm-12 control-label">SPOC Last Name</label><div class="col-sm-12"><input type="text" class="form-control" id="spoc_last_name0" name="spoc_last_name[]" placeholder="Enter SPOC Last Name" value="" maxlength="50" required=""></div></div><div class="form-group"><label for="name" class="col-sm-12 control-label">SPOC Contact Number</label><div class="col-sm-12"><input type="text" class="form-control" id="spoc_contact_number0" name="spoc_contact_number[]" placeholder="Enter SPOC Contact Number" value="" maxlength="50" required=""></div></div><div class="form-group"><label for="name" class="col-sm-12 control-label">SPOC Email Address</label><div class="col-sm-12"><input type="email" class="form-control" id="spoc_email_address0" name="spoc_email_address[]" placeholder="Enter SPOC Email Address" value="" maxlength="150" required=""></div></div>

                    <div id="spoc"></div>


                    <div class="col-sm-offset-2 col-sm-10">
                       <button type="submit" class="btn btn-warning" id="addSpocBtn" value="create">Add SPOC
                       </button>
                    </div>

                    <br>

                    <div class="col-sm-offset-2 col-sm-10">
                       <button type="submit" class="btn btn-primary" id="saveBtn" value="create">Save changes
                       </button>
                    </div>

                    <br>
                    <div id="modalresponse"></div> 
                </form>

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
            {"data": 'spoc_fullname'},
            {"data": 'spoc_contact_number'},
            {"data": 'spoc_email_address'},
            { "data": "created_at" },
            { "data": "status"},
            { "data": "options" },
        ]  

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
    });
    
    $('body').on('click', '.editProduct', function () {

      $('#spoc').empty();
      $('#supplierForm').trigger("reset");      
      var supplier_id = $(this).data('id');
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

          console.log(spoc_length)
          $.each( ordering_days_arr, function( key, value ) {
            $("input[value='" + $.trim(value) + "']").prop('checked', true);
          });

          $.each( modules_arr, function( key, value ) {
            $("input[value='" + $.trim(value) + "']").prop('checked', true);
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

          $("input[name=delivery_types][value=" + data.delivery_type + "]").prop('checked', 'checked');
          // $('#delivery_type').val(data.delivery_type);
      })
   });
    
    $('#saveBtn').click(function (e) {
        
      //console.log($("#delivery_type").prop('checked'));

        var delivery_types = $(':radio[name^=delivery_types]:checked').length;
          
        var ordering_days = $(':checkbox[name^=ordering_days]:checked').length;

        var modules = $(':checkbox[name^=module]:checked').length;
        if($("#vendor_code").val() == "" || $("#supplier_name").val() == "" || delivery_types==0 || ordering_days == 0 || modules == 0){
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

            
            if(delivery_types == 0)
              $(".delivery_type").css('color','red')
            else
              $(".delivery_type").css('color','black')

            if(ordering_days == 0)
              $(".ordering_days").css('color','red')
            else
              $(".ordering_days").css('color','black')

            if(modules == 0)
              $(".module").css('color','red')
            else
              $(".module").css('color','black')

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
     
        var supplier_id = $(this).data("id");
        var status = $(this).data("status");
        console.log(status)
        console.log(supplier_id)
        if (confirm("Are you want to proceed?")){
            $.ajax({
                url: "{{ url('deactivateOrActivateSupplier') }}",
                type: "POST",
                data: {id:supplier_id, status:status},
                success: function (data) {
                    $('#response').html("<div class='alert alert-success'>"+data.success+"</div>")
                    table.draw();
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

        $("#spoc").append('<div class="appendedSpoc'+ count+'"><hr><a href="#" class="removeSpoc btn btn-danger" data-id="'+count+'">Remove</a><div class="form-group"><label for="name" class="col-sm-12 control-label">SPOC First Name</label><div class="col-sm-12"><input type="text" class="form-control" class="spoc_first_name" name="spoc_first_name[]" placeholder="Enter SPOC First Name" value="" maxlength="50" required=""></div></div><div class="form-group"><label for="name" class="col-sm-12 control-label">SPOC Last Name</label><div class="col-sm-12"><input type="text" class="form-control" class="spoc_last_name" name="spoc_last_name[]" placeholder="Enter SPOC Last Name" value="" maxlength="50" required=""></div></div><div class="form-group"><label for="name" class="col-sm-12 control-label">SPOC Contact Number</label><div class="col-sm-12"><input type="text" class="form-control" class="spoc_contact_number" name="spoc_contact_number[]" placeholder="Enter SPOC Contact Number" value="" maxlength="50" required=""></div></div><div class="form-group"><label for="name" class="col-sm-12 control-label">SPOC Email Address</label><div class="col-sm-12"><input type="email" class="form-control" class="spoc_email_address" name="spoc_email_address[]" placeholder="Enter SPOC Email Address" value="" maxlength="150" required=""></div></div></div>');

        count++;
    });



    
     
  });
</script>
@endsection
