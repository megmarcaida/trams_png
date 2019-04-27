@extends('layouts.datatableapp')

@section('content')

<div class="container-fluid">

  <!-- Breadcrumbs-->
  <ol class="breadcrumb">
    <li class="breadcrumb-item">
      <a href="#">Dashboard</a>
    </li>
    <li class="breadcrumb-item">Master File</li>
    <li class="breadcrumb-item active">Users</li>
  </ol>

  <div class="row">
    <div class="col-xl-12 col-sm-12 mb-3">
      <h1>Users</h1>
        <a class="btn btn-success" href="javascript:void(0)" id="createNewProduct"> Register User </a>
        <br> <br>
        <div id="response"></div>
        <table class="table table-bordered data-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Password</th>
                    <th>Role ID</th>
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
                <form id="userForm" name="userForm" class="form-horizontal">
                    
                    <input type="hidden" name="id" id="id">

                    <div class="form-group">
                        <label for="name" class="col-sm-12 control-label">*Name</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="name" name="name" placeholder="Enter User Name" required="">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="name" class="col-sm-12 control-label">*Email</label>
                        <div class="col-sm-12">
                            <input type="email" class="form-control" id="email" name="email" placeholder="Enter User Email" value="" maxlength="100" required="">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="name" class="col-sm-12 control-label">*Password</label>
                        <div class="col-sm-12">
                            <input type="password" class="form-control" id="password" name="password" placeholder="Enter User Password" value="" maxlength="100" required="">
                        </div>
                    </div>

                     <div class="form-group">
                        <label for="name" class="col-sm-12 control-label">*Role</label>
                        <div class="col-sm-12">
                          <input type="hidden" id="role_id" name="role_id">
                            <input type="text" class="form-control" id="role_name" name="role_name" value="" maxlength="100" required="">
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
                 "url": "{{ url('allusers') }}",
                 "dataType": "json",
                 "type": "POST",
                 "data":{ _token: "{{csrf_token()}}"}
               },
        "columns": [
            {"data": 'name'},
            {"data": 'email'},
            {"data": 'password'},
            {"data": 'role_id'},
            { "data": "status"},
            { "data": "options" },
        ]  

    });
     
    $('#createNewProduct').click(function () {
        $('#saveBtn').val("create-product");
        $('#id').val('');
        $('#userForm').trigger("reset");
        $('#modelHeading').html("Register User");
        $('#ajaxModel').modal({
          backdrop:'static',
          keyboard: false
        })
    });
    
    $('body').on('click', '.editProduct', function () {

      $('#userForm').trigger("reset");      
      var id = $(this).data('id');
      $.get("{{ route('ajaxusers.index') }}" +'/' + id +'/edit', function (data) {
          $('#modelHeading').html("Edit User");
          $('#saveBtn').val("edit-user");
          $('#ajaxModel').modal({
            backdrop:'static',
            keyboard: false
          })

          console.log(data[0].id)
          $('#id').val(data[0].id);
          $('#email').val(data[0].email);
          $('#name').val(data[0].name);
          $('#password').val("");
          $('#role_id').val(data[0].role_id);
          $('#role_name').val(data[0].role_name);
      })
   });
    
    $('#saveBtn').click(function (e) {
        
     
        if($("#email").val() == "" || $("#name").val() == "" || $("#password").val() == "" || $("#role_id").val() == ""){
          $("#modalresponse").html("<div class='alert alert-danger'>Please fill in the required fields.</div>")
          $('#modalresponse').fadeIn(1000);
          setTimeout(function(){
            $('#modalresponse').fadeOut(1000);
          },2000)
        }else{

            e.preventDefault();
            $(this).html('Sending..');
        
            console.log($('#userForm').serialize())
            $.ajax({
              data: $('#userForm').serialize(),
              url: "{{ route('ajaxusers.store') }}",
              type: "POST",
              dataType: 'json',
              success: function (data) {
                 $('#response').html("<div class='alert alert-success'>"+data.success+"</div>")
                  $('#userForm').trigger("reset");
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
    
    $('body').on('click', '.deactivateOrActivateUser', function () {
     
        var id = $(this).data("id");
        var status = $(this).data("status");
        
        if (confirm("Are You sure want to delete !")){
            $.ajax({
                url: "{{ url('deactivateOrActivateUser') }}",
                type: "POST",
                data: {id:id, status:status},
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
