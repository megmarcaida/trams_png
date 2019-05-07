@extends('layouts.datatableapp')

@section('content')

<div class="container-fluid">

  <!-- Breadcrumbs-->
  <ol class="breadcrumb">
    <li class="breadcrumb-item">
      <a href="#">Dashboard</a>
    </li>
    <li class="breadcrumb-item">Scheduler</li>
    <li class="breadcrumb-item active">Dock</li>
  </ol>

  <div class="row">
    <div class="col-xl-12 col-sm-12 mb-3">
      <h1>Docks</h1>
      <div class="row">
        <div class="col-xl-6">
          <a class="btn btn-success" href="javascript:void(0)" id="createNewProduct"> Register Dock</a>
          <a class="btn btn-warning" href="{{ route('exportDocker') }}">Export Docks Data</a>
        </div>
        <div class="col-xl-3">  
          <form action="{{ route('importDocker') }}" method="POST" enctype="multipart/form-data">
              @csrf
              <input type="file" name="file" class="form-control">
              <br>
              <button class="btn btn-success text-right">Import Dock Data</button>
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
                      <th>Dock Name</th>
                      <th>Module</th>
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
                <form id="dockForm" name="dockForm" class="form-horizontal">
                    
                    <input type="hidden" name="dock_id" id="dock_id">

                    <div class="form-group">
                        <label for="name" class="col-sm-12 control-label">*Dock Name</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="dock_name" name="dock_name" placeholder="Enter Name" value="" maxlength="50" required="">
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

                    <br>

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
                 "url": "{{ url('alldockers') }}",
                 "dataType": "json",
                 "type": "POST",
                 "data":{ _token: "{{csrf_token()}}"}
               },
        "columns": [
            // { "data": "id" },
            {"data": 'dock_name'},
            {"data": 'module'},
            { "data": "created_at" },
            { "data": "status"},
            { "data": "options" },
        ]  

    });
     
    $('#createNewProduct').click(function () {
        $('#spoc').empty();
        $('#saveBtn').val("create-product");
        $('#dock_id').val('');
        $('#dockForm').trigger("reset");
        $('#modelHeading').html("Register Dock");
        $('#ajaxModel').modal({
          backdrop:'static',
          keyboard: false
        })
    });
    
    $('body').on('click', '.editProduct', function () {

      $('#spoc').empty();
      $('#dockForm').trigger("reset");      
      var dock_id = $(this).data('id');
      $.get("{{ route('ajaxdockers.index') }}" +'/' + dock_id +'/edit', function (data) {
          $('#modelHeading').html("Edit Dock");
          $('#saveBtn').val("edit-user");
          $('#ajaxModel').modal({
            backdrop:'static',
            keyboard: false
          })
          $('#dock_id').val(data.id);
          $('#dock_name').val(data.dock_name);
         
          var modules_arr = data.module.split("|")
         
          $.each( modules_arr, function( key, value ) {
            $("input[value='" + $.trim(value) + "']").prop('checked', true);
          });

      })
   });
    
    $('#saveBtn').click(function (e) {
        
        var modules = $(':checkbox[name^=module]:checked').length;
        if($("#dock_name").val() == "" || modules == 0){
          $("#modalresponse").html("<div class='alert alert-danger'>Please fill in the required fields.</div>")

            if($("#dock_name").val() == "")
              $("#dock_name").css('outline','1px red solid')
            else
              $("#dock_name").css('outline','1px black solid')
          
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
        
            console.log($('#dockForm').serialize())
            $.ajax({
              data: $('#dockForm').serialize(),
              url: "{{ route('ajaxdockers.store') }}",
              type: "POST",
              dataType: 'json',
              success: function (data) {
                if(data.success != null){
                 $('#response').html("<div class='alert alert-success'>"+data.success+"</div>")
                  $('#dockForm').trigger("reset");
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
    
    $('body').on('click', '.deactivateOrActivateDocker', function () {
     
        var dock_id = $(this).data("id");
        var status = $(this).data("status");
        console.log(status)
        console.log(dock_id)
        if (confirm("Are you want to proceed?")){
            $.ajax({
                url: "{{ url('deactivateOrActivateDocker') }}",
                type: "POST",
                data: {id:dock_id, status:status},
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
