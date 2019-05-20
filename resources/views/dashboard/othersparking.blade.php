@extends('layouts.datatableapp')

@section('content')

<div class="container-fluid">

  <!-- Breadcrumbs-->
  <ol class="breadcrumb">
    <li class="breadcrumb-item">
      <a href="#">Dashboard</a>
    </li>
    <li class="breadcrumb-item">Others</li>
    <li class="breadcrumb-item active">Parking Module</li>
  </ol>

  <div class="row">
    <div class="col-xl-12 col-sm-12 mb-3">
      <h1>Parking</h1>
      <div class="row">
        <div class="col-xl-6">
          <a class="btn btn-success" href="javascript:void(0)" id="createNewProduct"> Register Supplier</a>
          <a class="btn btn-warning" href="{{ route('exportParking') }}">Export Parking Data</a>
        </div>
        <div class="col-xl-3">  
          <form action="{{ route('importParking') }}" method="POST" enctype="multipart/form-data">
              @csrf
              <input type="file" name="file" class="form-control">
              <br>
              <button class="btn btn-success text-right">Import Parking Data</button>
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
                      <th>Parking Name</th>
                      <th>Parking Description</th>
                      <th>Parking Slots Taken</th>
                      <th>Parking Total</th>
                      <th>Parking Available</th>
                      <th>Parking Block</th>
                      <th>Parking Status</th>
                      <th>Active</th>
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
                <div id="modalresponse"></div> 
                <form id="parkingForm" name="parkingForm" class="form-horizontal">
                    
                    <input type="hidden" name="parking_id" id="parking_id">
                    
                    <div class="form-group">
                        <label for="name" class="col-sm-12 control-label">*Parking Name</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="parking_name" name="parking_name" placeholder="Enter Name" value="" maxlength="50" required="">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="name" class="col-sm-12 control-label">*Parking Description</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="parking_description" name="parking_description" placeholder="Enter Description" value="" maxlength="50" required="">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="name" class="col-sm-12 control-label">*Parking Slot</label>
                        <div class="col-sm-12">
                            <input type="number" class="form-control" id="parking_slot" name="parking_slot" placeholder="Enter Slott" value="" maxlength="50" required="">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="name" class="col-sm-12 control-label">*Parking Total</label>
                        <div class="col-sm-12">
                            <input type="number" class="form-control" id="parking_area" name="parking_area" placeholder="Enter Total" value="" maxlength="50" required="">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="name" class="col-sm-12 control-label">*Parking Block</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="parking_block" name="parking_block" placeholder="Enter Block" value="" maxlength="50" required="">
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
                 "url": "{{ url('allparking') }}",
                 "dataType": "json",
                 "type": "POST",
                 "data":{ _token: "{{csrf_token()}}"}
               },
        "columns": [
            // { "data": "id" },
            {"data": 'parking_name'},
            {"data": 'parking_description'},
            {"data": 'parking_slot'},
            {"data": 'parking_area'},
            {"data": 'parking_available'},
            {"data": 'parking_block'},
            {"data": 'parking_status'},
            { "data": "created_at" },
            { "data": "status"},
            { "data": "options" },
        ]  

    });
     
    $('#createNewProduct').click(function () {
        $('#spoc').empty();
        $('#saveBtn').val("create-product");
        $('#parking_id').val('');
        $('#parkingForm').trigger("reset");
        $('#modelHeading').html("Register Parking");
        $('#ajaxModel').modal({
          backdrop:'static',
          keyboard: false
        })
    });
    
    $('body').on('click', '.editProduct', function () {

      $('#spoc').empty();
      $('#parkingForm').trigger("reset");      
      var parking_id = $(this).data('id');
      $.get("{{ route('ajaxparking.index') }}" +'/' + parking_id +'/edit', function (data) {
          $('#modelHeading').html("Edit Product");
          $('#saveBtn').val("edit-user");
          $('#ajaxModel').modal({
            backdrop:'static',
            keyboard: false
          })
          $('#parking_id').val(data.id);
          $('#parking_name').val(data.parking_name);
          $('#parking_description').val(data.parking_description);
          $('#parking_slot').val(data.parking_slot);
          $('#parking_area').val(data.parking_area);
          $('#parking_block').val(data.parking_block);
          $('#parking_status').val(data.parking_status);

          
      })
   });
    
    $('#saveBtn').click(function (e) {
        
      //console.log($("#delivery_type").prop('checked'));

   

        var modules = $(':checkbox[name^=module]:checked').length;
        if($("#parking_name").val() == "" || $("#parking_description").val() == "" || $("#parking_slot").val() == "" || $("#parking_area").val() == "" || $("#parking_block").val() == ""  ){
          $("#modalresponse").html("<div class='alert alert-danger'>Please fill in the required fields.</div>")

            if($("#parking_name").val() == "")
              $("#parking_name").css('outline','1px red solid')
            else
              $("#parking_name").css('outline','1px black solid')
          
            if($("#parking_description").val() == "")
              $("#parking_description").css('outline','1px red solid')
            else
              $("#parking_description").css('outline','1px black solid')

            if($("#parking_slot").val() == "")
              $("#parking_slot").css('outline','1px red solid')
            else
              $("#parking_slot").css('outline','1px black solid')

            if($("#parking_area").val() == "")
              $("#parking_area").css('outline','1px red solid')
            else
              $("#parking_area").css('outline','1px black solid')

            if($("#parking_block").val() == "")
              $("#parking_block").css('outline','1px red solid')
            else
              $("#parking_block").css('outline','1px black solid')


          $('#modalresponse').fadeIn(1000);
          setTimeout(function(){
            $('#modalresponse').fadeOut(1000);
          },2000)
        }
        else{

            e.preventDefault();
            $(this).html('Sending..');
        
            console.log($('#parkingForm').serialize())
            $.ajax({
              data: $('#parkingForm').serialize(),
              url: "{{ route('ajaxparking.store') }}",
              type: "POST",
              dataType: 'json',
              success: function (data) {
                if(data.success != null){
                 $('#response').html("<div class='alert alert-success'>"+data.success+"</div>")
                  $('#parkingForm').trigger("reset");
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
    
    $('body').on('click', '.deactivateOrActivateParking', function () {
     
        var parking_id = $(this).data("id");
        var status = $(this).data("status");
        console.log(status)
        console.log(supplier_id)
        if (confirm("Are you want to proceed?")){
            $.ajax({
                url: "{{ url('deactivateOrActivateParking') }}",
                type: "POST",
                data: {id:parking_id, status:status},
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



    
     
  });
</script>
@endsection
