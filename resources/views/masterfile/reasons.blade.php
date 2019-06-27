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
    <li class="breadcrumb-item">Master File</li>
    <li class="breadcrumb-item active">Reasons</li>
  </ol>
 
  <div class="row">
    <div class="col-xl-12 col-sm-12 mb-3">
      <h1>Reasons</h1>
        <div class="row">
          <div class="col-xl-6 col-md-6">  
            <a class="btn btn-success" href="javascript:void(0)" id="createNewProduct"> Register Reasons</a>
            <!-- <a class="btn btn-warning" href="{{ route('exportTruck') }}">Export Reasons Data</a> -->
          </div>
           <div class="col-xl-6 col-md-6">   
            <!-- <form action="{{ route('importTruck') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="file" name="file" class="form-control">
                <br>
                <button class="btn btn-success text-right">Import Trucks Data</button>
                <a class="btn btn-secondary" href="https://srv-file2.gofile.io/download/sQJupf/trucks_template.xlsx">Download Template Data</a>
            </form>     -->
          </div>
        </div>
        <br> <br>
        <div id="response">
         <!--  @if(session()->has('import_message'))
            <div class="alert alert-success">
                {{ session()->get('import_message') }}
            </div>
        @endif -->
        </div>
          <div class="row">
            <div class="col-md-2">
                <div class="form-group">
                 <label for="name" class="col-sm-12 control-label">*Filter Tagging</label>
                 <div class="col-sm-12">
                    <select multiple="true" class="form-control container_type_filter" id="container_type_filter">
                         <option value="">All</option>
                         <option value="Reschedule">Reschedule</option>
                         <option value="Cancellation">Cancellation</option>
                    </select>
                  </div>
              </div>
            </div>  
        </div>
        <div class="table table-responsive">
          <table class="table table-bordered data-table">
              <thead>
                  <tr>
                      <th>ID</th>
                      <th>Name</th>
                      <th>Description</th>
                      <th>Tagging</th>
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
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modelHeading"></h4>

                <button type="button" class="close" data-dismiss="modal">&times;</button> 
            </div>
            <div class="modal-body">
                <form id="reasonForm" name="reasonForm" class="form-horizontal">
                    
                    <input type="hidden" name="id" id="id">

                    <div class="form-group reason_id">
                        <label for="name" class="col-sm-12 control-label">*Reason ID</label>
                        <div class="col-sm-12">
                            <input type="text" readonly class="form-control" id="reason_id_display" name="id" value="" maxlength="100" required="">
                        </div>
                        <div class="col-sm-12">
                            <input  type="hidden" readonly class="form-control" id="reason_id" name="id" value="" maxlength="100" required="">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="name" class="col-sm-12 control-label">*Reason Name</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="reason_name" name="reason_name" placeholder="Enter Reason Name" value="" maxlength="100" required="">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="name" class="col-sm-12 control-label">*Description</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="description" name="description" placeholder="Enter Description" value="" maxlength="50" required="">
                        </div>
                    </div>

                    <div class="form-group">
                       <label for="name" class="col-sm-12 control-label">*Type</label>
                       <div class="col-sm-12">
                          <select class="form-control tagging" id="tagging" name="tagging">
                               <option value="">Please select Type</option>
                               <option value="Reschedule">Reschedule</option>
                               <option value="Cancellation">Cancellation</option>
                          </select>
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
                    <button id="btn-deactivate" class="btn btn-secondary btn-xs btn-danger btn-block deactivateOrActivateReason" type="button">Deactivate</button>
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
                 "url": "{{ url('allreasons') }}",
                 "dataType": "json",
                 "type": "POST",
                 "data":{ _token: "{{csrf_token()}}"}
               },
        "columns": [
            { "data": "id" },
            {"data": 'reason_name'},
            {"data": 'description'},
            {"data": 'tagging'},
            { "data": "status"},
        ]  

    });

    $('#container_type_filter').on('change', function(){
       table.search(this.value).draw();   
    });

    $('#supplier_filter').on('change', function(){
       table.search(this.value).draw();   
    });
     
    $('#createNewProduct').click(function () {
        $(".reason_id").hide();
        $('#saveBtn').val("create-product");
        $('#id').val("");
        $("#reason_id").val("")
        $("#modalresponse").hide() 
        $('#reasonForm').trigger("reset");
        $('#modelHeading').html("Register Reason");
        $('#ajaxModel').modal({
          backdrop:'static',
          keyboard: false
        })
    });
    
    $('body').on('click', '.editProduct', function () {
      $("#ajaxModelView").modal("hide")
      $(".reason_id").show();
      $('#reasonForm').trigger("reset");   
      var id = $(this).attr('data-id');   
      $("#modalresponse").hide() 
      $.get("{{ route('ajaxreasons.index') }}" +'/' + id +'/edit', function (data) {
          $('#modelHeading').html("Edit Reason");
          $('#saveBtn').val("edit-user");
          $('#ajaxModel').modal({
            backdrop:'static',
            keyboard: false
          })
          $('#reason_id_display').val(data[0].id);
          $('#reason_id').val(data[0].id);
          $('#reason_name').val(data[0].reason_name);
          $('#description').val(data[0].description);
          $('#tagging').val(data[0].tagging)
      })
   });
    
    $('#saveBtn').click(function (e) {

       
        var tagging = $('#tagging').children("option:selected").val();

        if($("#reason_name").val() == "" || $("#description").val() == "" || tagging== ""){
          $("#modalresponse").show();
          $("#modalresponse").html("<div class='alert alert-danger'>Please fill in the required fields.</div>")
          $('#modalresponse').fadeIn(1000);
          setTimeout(function(){
            $('#modalresponse').fadeOut(1000);
          },2000)
        
          if($("#reason_name").val() == "")
             $("#reason_name").css('outline','1px solid red')
           else
             $("#reason_name").css('outline','1px solid transparent')

           if($("#description").val() == "")
             $("#description").css('outline','1px solid red')
           else
             $("#description").css('outline','1px solid transparent')

           if(tagging == "")
             $("#tagging").css('outline','1px solid red')
            else
             $("#tagging").css('outline','1px solid transparent')

           return false;

        }else{

            e.preventDefault();
            $(this).html('Sending..');
        
            console.log($('#reasonForm').serialize())
            $.ajax({
              data: $('#reasonForm').serialize(),
              url: "{{ route('ajaxreasons.store') }}",
              type: "POST",
              dataType: 'json',
              success: function (data) {
                  if(data.success != null){
                    $('#response').html("<div class='alert alert-success'>"+data.success+"</div>")
                      $('#reasonForm').trigger("reset");
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
    
    $('body').on('click', '.deactivateOrActivateReason', function () {
     
        var id = $("#btn-deactivate").attr("data-id");
        var status = $("#btn-deactivate").attr("data-status");
        
        if (confirm("Are you want to proceed?")){
            $.ajax({
                url: "{{ url('deactivateOrActivateReason') }}",
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


     $('.data-table tbody').on( 'click', 'tr', function () {
            var id = $(this).find("td:nth-child(1)").first().text().trim()
            $.ajax({
              data: {id:id},
              url: "{{ url('getReason') }}",
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
