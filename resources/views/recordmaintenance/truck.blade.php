@extends('layouts.datatableapp')

@section('content')

<div class="container-fluid">

  <!-- Breadcrumbs-->
  <ol class="breadcrumb">
    <li class="breadcrumb-item">
      <a href="#">Dashboard</a>
    </li>
    <li class="breadcrumb-item">Record Maintenance</li>
    <li class="breadcrumb-item active">Trucks</li>
  </ol>
 
  <div class="row">
    <div class="col-xl-12 col-sm-12 mb-3">
      <h1>Trucks</h1>
        <div class="row">
          <div class="col-xl-6">
            <a class="btn btn-success" href="javascript:void(0)" id="createNewProduct"> Register Trucks</a>
            <a class="btn btn-warning" href="{{ route('exportTruck') }}">Export Trucks Data</a>
          </div>
          <div class="col-xl-3">  
            <form action="{{ route('importTruck') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="file" name="file" class="form-control">
                <br>
                <button class="btn btn-success text-right">Import Trucks Data</button>
                <a class="btn btn-secondary" href="{{ route('exportTruck') }}">Download Template Data</a>
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
        </div>
        <div class="table table-responsive">
          <table class="table table-bordered data-table">
              <thead>
                  <tr>
                      <th>ID</th>
                      <th>Suppliers</th>
                      <th>Trucking Company</th>
                      <th>Plate Number</th>
                      <th>Brand</th>
                      <th>Model</th>
                      <th>Type</th>
                      <th>Status</th>
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
                <form id="truckForm" name="truckForm" class="form-horizontal">
                    
                    <input type="hidden" name="id" id="id">

                    <div class="form-group truck_id">
                        <label for="name" class="col-sm-12 control-label">*Trucking ID</label>
                        <div class="col-sm-12">
                            <input type="text" readonly class="form-control" id="truck_id_display" name="id" value="" maxlength="100" required="">
                        </div>
                        <div class="col-sm-12">
                            <input  type="hidden" readonly class="form-control" id="truck_id" name="id" value="" maxlength="100" required="">
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
                        <label for="name" class="col-sm-12 control-label">*Truck Suppliers</label>
                        <div class="col-sm-12">
                            <textarea class="form-control" name="supplier_names" id="truck_suppliers" readonly="" required=""></textarea>
                            <!-- <input type="text" class="form-control" id="truck_suppliers" disabled="" required=""> -->
                            <input type="hidden" id="supplier_ids" name="supplier_ids">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="name" class="col-sm-12 control-label">*Trucking Company</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="trucking_company" name="trucking_company" placeholder="Enter Trucking Company" value="" maxlength="100" required="">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="name" class="col-sm-12 control-label">*Plate Number</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="plate_number" name="plate_number" placeholder="Enter Plate Number" value="" maxlength="50" required="">
                        </div>
                    </div>
                    
                    <div class="form-group form-check-inline">
                        <label for="name" class="col-sm-2 control-label">*Truck</label>
                        <div class="col-sm-5">
                            <input type="text" class="form-control" id="brand" name="brand" placeholder="Enter Brand" value="" maxlength="50" required="">
                        </div>
                        <div class="col-sm-5">
                            <input type="text" class="form-control" id="model" name="model" placeholder="Enter Model" value="" maxlength="50" required="">
                        </div>
                    </div>

                  
                    <div class="form-group">
                      <label class="col-sm-12 control-label types">*Type</label>
                      <div class="col-sm-12">
                        <div class="form-check form-check-inline">
                          <input class="form-check-input" type="radio" name="types" id="type_c" value="Containerized">
                          <label class="form-check-label" for="inlineRadio1">Containerized</label>
                        </div>
                        <div class="form-check form-check-inline">
                          <input class="form-check-input" type="radio" name="types" id="type_nc" value="Non-containerized">
                          <label class="form-check-label" for="inlineRadio1">Non-containerized</label>
                        </div>
                      </div>
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
                    <button id="btn-deactivate" class="btn btn-secondary btn-xs btn-danger btn-block deactivateOrActivateTruck" type="button">Deactivate</button>
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
                 "url": "{{ url('alltrucks') }}",
                 "dataType": "json",
                 "type": "POST",
                 "data":{ _token: "{{csrf_token()}}"}
               },
        "columns": [
            { "data": "id" },
            {"data": 'supplier_ids'},
            {"data": 'trucking_company'},
            {"data": 'plate_number'},
            {"data": 'brand'},
            {"data": 'model'},
            {"data": 'type'},
            { "data": "status"},
            //{ "data": "options" },
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
        $(".truck_id").hide();
        $('#saveBtn').val("create-product");
        $('#id').val('');
        $("#truck_suppliers").val("");
        $("#supplier_ids").val("");
        console.log($("#supplier_ids").val());
        $('#truckForm').trigger("reset");
        $('#modelHeading').html("Register Truck");
        $('#ajaxModel').modal({
          backdrop:'static',
          keyboard: false
        })
    });
    
    $('body').on('click', '.editProduct', function () {
      $("#ajaxModelView").modal("hide")
      $(".truck_id").show();
      $('#truckForm').trigger("reset");      
      var id = $(this).attr('data-id');
      $.get("{{ route('ajaxtrucks.index') }}" +'/' + id +'/edit', function (data) {
          $('#modelHeading').html("Edit Truck");
          $('#saveBtn').val("edit-user");
          $('#ajaxModel').modal({
            backdrop:'static',
            keyboard: false
          })
          $('#truck_id_display').val(data[0].id);
          $('#truck_id').val(data[0].id);
          $('#trucking_company').val(data[0].trucking_company);
          $('#plate_number').val(data[0].plate_number);
         
          $('#brand').val(data[0].brand);

          $('#model').val(data[0].model);

          $('#type').val(data[0].type);

          var x = data[0].supplier_ids.split("|")
          
          x.splice(-1,1)
          
          var supplier_trucks = "";
          $.each( x, function( key, value ) {
          
            // var _supplier_name = $("#supplier_id option[value='"+value.trim()+"']").text()
            supplier_trucks += value + " | ";
            console.log(value)
          });

          $('#truck_suppliers').val(supplier_trucks);
          $('#supplier_ids').val(data[0].supplier_trucks_ids);
          $("input[name=types][value=" + data[0]  .type + "]").prop('checked', 'checked');
          // $('#delivery_type').val(data.delivery_type);
      })
   });
    
    $('#saveBtn').click(function (e) {

       
        var types = $(':radio[name^=types]:checked').length;
        if($("#trucking_company").val() == "" || $("#plate_number").val() == "" || $("#model").val() == "" || $("#brand").val() == "" || types==0 || $("#truck_suppliers").val() == ""){
          $("#modalresponse").show();
          $("#modalresponse").html("<div class='alert alert-danger'>Please fill in the required fields.</div>")
          $('#modalresponse').fadeIn(1000);
          setTimeout(function(){
            $('#modalresponse').fadeOut(1000);
          },2000)
        
          if($("#trucking_company").val() == "")
             $("#trucking_company").css('outline','1px solid red')
           else
             $("#trucking_company").css('outline','1px solid black')

           if($("#plate_number").val() == "")
             $("#plate_number").css('outline','1px solid red')
           else
             $("#plate_number").css('outline','1px solid black')

           if($("#model").val() == "")
             $("#model").css('outline','1px solid red')
           else
             $("#model").css('outline','1px solid black')

            if($("#brand").val() == "")
             $("#brand").css('outline','1px solid red')
            else
             $("#brand").css('outline','1px solid black')

            if($("#truck_suppliers").val() == "")
             $("#truck_suppliers").css('outline','1px solid red')
            else
             $("#truck_suppliers").css('outline','1px solid black')

           if(types == 0)
             $(".types").css('color','red')
            else
             $(".types").css('color','black')

           return false;

        }else{

            e.preventDefault();
            $(this).html('Sending..');
        
            console.log($('#truckForm').serialize())
            $.ajax({
              data: $('#truckForm').serialize(),
              url: "{{ route('ajaxtrucks.store') }}",
              type: "POST",
              dataType: 'json',
              success: function (data) {
                  if(data.success != null){
                    $('#response').html("<div class='alert alert-success'>"+data.success+"</div>")
                      $('#truckForm').trigger("reset");
                      $('#ajaxModel').modal('hide');
                       $('#response').show();
                      setTimeout(function(){
                        $('#response').hide("slow");
                      },3000)

                      table.draw();
                  }else if(data.error != null){
                    $('#modalresponse').show();
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
    
    $('body').on('click', '.deactivateOrActivateTruck', function () {
     
        var id = $("#btn-deactivate").attr("data-id");
        var status = $("#btn-deactivate").attr("data-status");
        
        if (confirm("Are you want to proceed?")){
            $.ajax({
                url: "{{ url('deactivateOrActivateTruck') }}",
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



    var truck_suppliers = '';
    var supplier_ids = '';
    $('body').on('click', '.add_supplier', function(){
      
      var _supplier_id = $('#supplier_id').children("option:selected").val();
      var _supplier_name = $('#supplier_id').children("option:selected").text();
      var t_suppliers = $('#supplier_ids').val();
      var t_truck_suppliers = $('#truck_suppliers').val();
     
      //console.log(t_suppliers)
      if(!t_suppliers.includes(_supplier_id) || !t_suppliers.includes(_supplier_id)){
          //supplier_ids += _supplier_id + '|';
          //truck_suppliers += _supplier_name + ' | ';
          t_suppliers += _supplier_id + '|';
          t_truck_suppliers += _supplier_name + ' | ';
          $('#modalresponse').empty();
      }else{
        $('#modalresponse').html("<div class='alert alert-danger'>Supplier already added.</div>")
      }

      //console.log(supplier_ids);
      //console.log(truck_suppliers);
      console.log(t_suppliers)
      $('#truck_suppliers').val(t_truck_suppliers);
      $('#supplier_ids').val(t_suppliers);

    });

    $('body').on('click', '.clear_supplier', function(){
      
      $('#supplier_ids').val("");
      $('#truck_suppliers').val("")

      truck_suppliers = "";
      supplier_ids = "";
    });

    
     $('.data-table tbody').on( 'click', 'tr', function () {
            var id = $(this).find("td:nth-child(1)").first().text().trim()
            $.ajax({
              data: {id:id},
              url: "{{ url('getTruck') }}",
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
