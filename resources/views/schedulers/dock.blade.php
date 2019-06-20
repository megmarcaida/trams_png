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
                      <th>Dock ID</th>
                      <th>Dock Name</th>
                      <th>Module</th>
                      <th>Created At</th>
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
                       <label for="name" class="col-sm-12 control-label">*Module</label>
                       <div class="col-sm-12">
                          <select  class="form-control" id="module" name="module">
                             <option value="0">Please select Dock</option>
                              <option value="PCC">PCC</option>
                              <option value="Baby Care">Baby Care</option>
                              <option value="Laundry">Laundry</option>
                              <option value="Liquids">Liquids</option>
                              <option value="Fem Care">Fem Care</option>
                          </select>
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
                    <button id="btn-deactivate" class="btn btn-secondary btn-xs btn-danger btn-block deactivateOrActivateDocker" type="button">Deactivate</button>
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
                 "url": "{{ url('alldockers') }}",
                 "dataType": "json",
                 "type": "POST",
                 "data":{ _token: "{{csrf_token()}}"}
               },
        "columns": [
            { "data": "id" },
            {"data": 'dock_name'},
            {"data": 'module'},
            { "data": "created_at" },
            { "data": "status"},
            //{ "data": "options" },
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
      $("#ajaxModelView").modal("hide")
      $('#spoc').empty();
      $('#dockForm').trigger("reset");      
      var dock_id = $(this).attr('data-id');
      $.get("{{ route('ajaxdockers.index') }}" +'/' + dock_id +'/edit', function (data) {
          $('#modelHeading').html("Edit Dock");
          $('#saveBtn').val("edit-user");
          $('#ajaxModel').modal({
            backdrop:'static',
            keyboard: false
          })
          $('#dock_id').val(data.id);
          $('#dock_name').val(data.dock_name);
         
          $('#module').val(data.module)
          $('#user_type').val(data.user_type)

      })
   });
    
    $('#saveBtn').click(function (e) {
        
        if($("#dock_name").val() == "" || $("#module").val() =="0" ){
          $("#modalresponse").html("<div class='alert alert-danger'>Please fill in the required fields.</div>")

            if($("#dock_name").val() == "")
              $("#dock_name").css('outline','1px red solid')
            else
              $("#dock_name").css('outline','1px black solid')
          
            if($("#module").val() == "0" || $("#dock_id").val() == null)
              $("#module").css('outline','1px red solid')
            else
              $("#module").css('outline','1px black solid')

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
     
        var dock_id = $("#btn-deactivate").attr("data-id");
        var status = $("#btn-deactivate").attr("data-status");
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
            console.log(id)
            $.ajax({
              data: {id:id},
              url: "{{ url('getDock') }}",
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

    $.ajax({
            url: "{{ url('getUserType') }}",
            type: "POST",
            data: {},
            success: function (data) {
                console.log(data)
                $.each(JSON.parse(data), function(index, item) {
                  console.log(item)
                   $('#user_type').append("<option  value="+ item.id +">"+ item.name+"</option>")
                });
            },
            error: function (data) {
                console.log('Error:', data);
            }
    });
 
  });
</script>
@endsection
