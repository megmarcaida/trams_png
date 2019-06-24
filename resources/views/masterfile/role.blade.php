@extends('layouts.datatableapp')

@section('content')

<div class="container-fluid">

  <!-- Breadcrumbs-->
  <ol class="breadcrumb">
    <li class="breadcrumb-item">
      <a href="#">Dashboard</a>
    </li>
    <li class="breadcrumb-item">Master File</li>
    <li class="breadcrumb-item active">Roles</li>
  </ol>

  <div class="row">
    <div class="col-xl-12 col-sm-12 mb-3">
      <h1>Roles</h1>
        <a class="btn btn-success" href="javascript:void(0)" id="createNewProduct"> Register Role </a>
        <br> <br>
        <div id="response"></div>
        <table class="table table-bordered data-table">
            <thead>
                <tr>
                    <th>Role ID</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Docks</th>
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
                <form id="roleForm" name="roleForm" class="form-horizontal">
                    
                    <input type="hidden" name="id" id="id">

                    <div class="form-group">
                        <label for="name" class="col-sm-12 control-label">*Role Name</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="name" name="name" placeholder="Enter Role Name" required="">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="name" class="col-sm-12 control-label">*Description</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="description" name="description" placeholder="Enter Role Description" value="" maxlength="100" required="">
                        </div>
                    </div>

                     <div class="form-group">
                       <label for="name" class="col-sm-12 control-label">*Docks</label>
                       <div class="col-sm-12">
                          <select multiple="true" class="form-control" id="submodules" name="submodules[]">
                               @foreach($dockData['data'] as $dock)
                                 <option value='{{ $dock->dock_name }}'>{{ $dock->dock_name }}</option>
                               @endforeach
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
                    <button id="btn-deactivate" class="btn btn-secondary btn-xs btn-danger btn-block deactivateOrActivateRole" type="button">Deactivate</button>
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
                 "url": "{{ url('allroles') }}",
                 "dataType": "json",
                 "type": "POST",
                 "data":{ _token: "{{csrf_token()}}"}
               },
        "columns": [
            {"data": 'id'},
            {"data": 'name'},
            {"data": 'description'},
            {"data": 'submodules'},
            { "data": "status"},
            //{ "data": "options" },
        ]  

    });
     
    $('#createNewProduct').click(function () {
        $('#saveBtn').val("create-product");
        $('#id').val('');
        $('#roleForm').trigger("reset");
        $('#modelHeading').html("Register Role");
        $('#ajaxModel').modal({
          backdrop:'static',
          keyboard: false
        })
    });
    
    $('body').on('click', '.editProduct', function () {
      $("#ajaxModelView").modal("hide")
      $('#roleForm').trigger("reset");      
      var id = $(this).attr('data-id');
      $.get("{{ route('ajaxroles.index') }}" +'/' + id +'/edit', function (data) {
          $('#modelHeading').html("Edit Role");
          $('#saveBtn').val("edit-user");
          $('#ajaxModel').modal({
            backdrop:'static',
            keyboard: false
          })
          $('#id').val(data.id);
          $('#description').val(data.description);
          $('#name').val(data.name);

          var data_ = data.submodules
          var newdata = data_.split("|")
           
          
          console.log(newdata)
          $.each(newdata, function(i,e){
              $("#submodules option[value='" + e + "']").prop("selected", true);
          });
        
      })
   });
    
    $('#saveBtn').click(function (e) {
        
        var submodules = [];
        $.each($("#submodules option:selected"), function(){            
            submodules.push($(this).val());
        });
        

        var submod = JSON.stringify(submodules)
        console.log(submod)
        if($("#description").val() == "" || $("#name").val() == "" || submodules.length == 0){
          $("#modalresponse").html("<div class='alert alert-danger'>Please fill in the required fields.</div>")
          $('#modalresponse').fadeIn(1000);
          setTimeout(function(){
            $('#modalresponse').fadeOut(1000);
          },2000)
        }else{

            e.preventDefault();
            $(this).html('Sending..');
        
            console.log($('#roleForm').serialize())
            $.ajax({
              data: $('#roleForm').serialize(),
              url: "{{ route('ajaxroles.store') }}",
              type: "POST",
              dataType: 'json',
              success: function (data) {
                 $('#response').html("<div class='alert alert-success'>"+data.success+"</div>")
                  $('#roleForm').trigger("reset");
                  $('#ajaxModel').modal('hide');
                  setTimeout(function(){
                    $('#response').hide("slow");
                  },3000);
                  table.draw();
                  $('#saveBtn').html('Save Changes');
              },
              error: function (data) {
                  console.log('Error:', data);
                  $('#saveBtn').html('Save Changes');
              }
          });
      }
    });
    
    $('body').on('click', '.deactivateOrActivateRole', function () {
     
        var id = $("#btn-deactivate").attr("data-id");
        var status = $("#btn-deactivate").attr("data-status");
        
        if (confirm("Are You sure want to delete !")){
            $.ajax({
                url: "{{ url('deactivateOrActivateRole') }}",
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
              url: "{{ url('getRole') }}",
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
