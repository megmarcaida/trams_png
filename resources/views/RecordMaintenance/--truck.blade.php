@extends('layouts.datatableapp')

@section('content')
<div class="container-fluid">
    <h1>Truck</h1>
    <a class="btn btn-success" href="javascript:void(0)" id="createNewProduct"> Create New Product</a>
    <table class="table table-bordered data-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Name</th>
                <th>Details</th>
                <th width="280px">Action</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>
   
<div class="modal fade" id="ajaxModel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modelHeading"></h4>
            </div>
            <div class="modal-body">
                <form id="supplierForm" name="supplierForm" class="form-horizontal">
                   <input type="hidden" name="supplier_id" id="supplier_id">
                    <div class="form-group">
                        <label for="name" class="col-sm-12 control-label">Supplier Name</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="supplier_name" name="supplier_name" placeholder="Enter Name" value="" maxlength="50" required="">
                        </div>
                    </div>
     
                    <div class="form-group">
                        <label class="col-sm-12 control-label">Delivery Type</label>
                        <div class="col-sm-12">
                            <textarea id="delivery_type" name="delivery_type" required="" placeholder="Enter Delivery Type" class="form-control"></textarea>
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
        processing: true,
        serverSide: true,
        ajax: "{{ route('ajaxsuppliers.index') }}",
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex'},
            {data: 'supplier_name', name: 'supplier_name'},
            {data: 'delivery_type', name: 'delivery_type'},
            {data: 'action', name: 'action', orderable: false, searchable: false},
        ]
    });
     
    $('#createNewProduct').click(function () {
        $('#saveBtn').val("create-product");
        $('#supplier_id').val('');
        $('#supplierForm').trigger("reset");
        $('#modelHeading').html("Create New Product");
        $('#ajaxModel').modal('show');
    });
    
    $('body').on('click', '.editProduct', function () {
      var supplier_id = $(this).data('id');
      $.get("{{ route('ajaxsuppliers.index') }}" +'/' + supplier_id +'/edit', function (data) {
          $('#modelHeading').html("Edit Product");
          $('#saveBtn').val("edit-user");
          $('#ajaxModel').modal('show');
          $('#supplier_id').val(data.id);
          $('#supplier_name').val(data.supplier_name);
          $('#delivery_type').val(data.delivery_type);
      })
   });
    
    $('#saveBtn').click(function (e) {
        e.preventDefault();
        $(this).html('Sending..');
    
        $.ajax({
          data: $('#supplierForm').serialize(),
          url: "{{ route('ajaxsuppliers.store') }}",
          type: "POST",
          dataType: 'json',
          success: function (data) {
     
              $('#supplierForm').trigger("reset");
              $('#ajaxModel').modal('hide');
              table.draw();
         
          },
          error: function (data) {
              console.log('Error:', data);
              $('#saveBtn').html('Save Changes');
          }
      });
    });
    
    $('body').on('click', '.deleteProduct', function () {
     
        var supplier_id = $(this).data("id");
        confirm("Are You sure want to delete !");
      
        $.ajax({
            type: "DELETE",
            url: "{{ route('ajaxsuppliers.store') }}"+'/'+supplier_id,
            success: function (data) {
                table.draw();
            },
            error: function (data) {
                console.log('Error:', data);
            }
        });
    });
     
  });
</script>
@endsection
