@extends('layouts.datatableapp')

@section('content')

<div class="container-fluid">

  <!-- Breadcrumbs-->
  <ol class="breadcrumb">
    <li class="breadcrumb-item">
      <a href="#">Dashboard</a>
    </li>
    <li class="breadcrumb-item">Others</li>
    <li class="breadcrumb-item active">Banned and Issue Reporting</li>
  </ol>

  <div class="row">
    <div class="col-xl-12 col-sm-12 mb-3">
      <h1>Banned and Issue Reporting</h1>
      <div class="row">
        <div class="col-xl-6">
          <a class="btn btn-success" href="javascript:void(0)" id="createNewProduct"> Register Banned and Issue</a>
          <a class="btn btn-warning" href="{{ route('exportSupplier') }}">Export Banned and Issue Data</a>
        </div>
        <div class="col-xl-3">  
          <form action="{{ route('importSupplier') }}" method="POST" enctype="multipart/form-data">
              @csrf
              <input type="file" name="file" class="form-control">
              <br>
              <button class="btn btn-success text-right">Import Banned and Issue Data</button>
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
                      <th>Name</th>
                      <th>Location</th>
                      <th>Date</th>
                      <th>Time</th>
                      <th>Nature of Violation</th>
                      <th>Reason</th>
                      <th>Additional Information</th>
                      <th>Supplier</th>
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
                <form id="bannedIssueForm" name="bannedIssueForm" class="form-horizontal">
                    
                    <input type="hidden" name="supplier_id" id="supplier_id">


                    <div class="form-group">
                        <label for="name" class="col-sm-12 control-label">*Name</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="name" name="name" placeholder="Enter Name" value="" maxlength="50" required="">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="name" class="col-sm-12 control-label">*Location</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="location" name="location" placeholder="Enter Location" value="" maxlength="100" required="">
                        </div>
                    </div>
                    
                    <div class="form-group">
                      <label class="col-sm-12 control-label violation">*Nature of Violation</label>
                      <div class="col-sm-12">
                        <div class="form-check form-check-inline">
                          <input class="form-check-input" type="radio" name="violation" class="violation" value="Local">
                          <label class="form-check-label" for="inlineRadio1">Warning</label>
                        </div>
                        <div class="form-check form-check-inline">
                          <input class="form-check-input" type="radio" name="violation" class="delivery_type" value="Imported">
                          <label class="form-check-label" for="inlineRadio1">Ban</label>
                        </div>
                      </div>
                    </div>

                    <div class="form-group">
                      <label class="col-sm-12 control-label">*Date and Time</label>
                      <div class="col-sm-12">
                        <div class="col-sm-12">
                        <input type="datetime-local" class="form-control datepicker" name="date_time" id="date_time" required="">
                        </div>
                      </div>
                    </div>

                    <div class="form-group">
                        <label for="name" class="col-sm-12 control-label">*Reason</label>
                        <div class="col-sm-12">
                            <textarea class="form-control" name="reason" id="reason"  required=""></textarea>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="name" class="col-sm-12 control-label">*Additional Information</label>
                        <div class="col-sm-12">
                            <textarea class="form-control" name="additional_information" id="additional_information"  required=""></textarea>
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
                 "url": "{{ url('allBannedIssue') }}",
                 "dataType": "json",
                 "type": "POST",
                 "data":{ _token: "{{csrf_token()}}"}
               },
        "columns": [
            // { "data": "id" },
            {"data": 'name'},
            {"data": 'location'},
            {"data": 'date'},
            {"data": 'time'},
            {"data": 'violation'},
            {"data": 'reason'},
            {"data": 'additional_information'},
            {"data": 'supplier'},
            { "data": "created_at" },
            { "data": "status"},
            { "data": "options" },
        ]  

    });

     
    $('#createNewProduct').click(function () {
        $('#spoc').empty();
        $('#saveBtn').val("create-product");
        $('#supplier_id').val('');
        $('#bannedIssueForm').trigger("reset");
        $('#modelHeading').html("Register Supplier");
        $('#ajaxModel').modal({
          backdrop:'static',
          keyboard: false
        })
    });
    
    $('body').on('click', '.editProduct', function () {

      $('#spoc').empty();
      $('#bannedIssueForm').trigger("reset");      
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
          var spoc_first_name_arr = data.spoc_firstname.split("|")
          var spoc_last_name_arr = data.spoc_lastname.split("|")
          var spoc_contact_number_arr = data.spoc_contact_number.split("|")
          var spoc_email_address_arr = data.spoc_email_address.split("|")
          var spoc_length = spoc_first_name_arr.length - 1;

          console.log(spoc_length)
          $.each( ordering_days_arr, function( key, value ) {
            $("input[value='" + $.trim(value) + "']").prop('checked', true);
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
        
            console.log($('#bannedIssueForm').serialize())
            $.ajax({
              data: $('#bannedIssueForm').serialize(),
              url: "{{ route('ajaxBannedIssue.store') }}",
              type: "POST",
              dataType: 'json',
              success: function (data) {
                if(data.success != null){
                 $('#response').html("<div class='alert alert-success'>"+data.success+"</div>")
                  $('#bannedIssueForm').trigger("reset");
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
    
    $('body').on('click', '.deactivateOrActivateBannedIssue', function () {
     
        var supplier_id = $(this).data("id");
        var status = $(this).data("status");
        console.log(status)
        console.log(supplier_id)
        if (confirm("Are you want to proceed?")){
            $.ajax({
                url: "{{ url('deactivateOrActivateBannedIssue') }}",
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

  



    
     
  });
</script>
@endsection
