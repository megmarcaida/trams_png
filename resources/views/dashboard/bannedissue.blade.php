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
    <li class="breadcrumb-item">Others</li>
    <li class="breadcrumb-item active">Issue Reporting</li>
  </ol>

  <div class="row">
    <div class="col-xl-12 col-sm-12 mb-3">
      <h1>Issue Reporting</h1>
      <div class="row">
        <div class="col-xl-6">
          <a class="btn btn-success" href="javascript:void(0)" id="createNewProduct"> Register Issue</a>
          <!-- <a class="btn btn-warning" href="{{ route('exportSupplier') }}">Export Banned and Issue Data</a> -->
        </div>
        <div class="col-xl-3">  
          <!-- <form action="{{ route('importSupplier') }}" method="POST" enctype="multipart/form-data">
              @csrf
              <input type="file" name="file" class="form-control">
              <br>
              <button class="btn btn-success text-right">Import Banned and Issue Data</button>
          </form> -->    
        </div>
      </div>
      <br>
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
                      <th>Delivery No</th>
                      <th>Location</th>
                      <th>Date</th>
                      <th>Nature of Violation</th>
                      <th>Reason</th>
                      <th>Additional Information</th>
                      <th>Created At</th>
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
                <div id="modalresponse"></div> 
                <form id="bannedIssueForm" name="bannedIssueForm" class="form-horizontal">
                    
                    <input type="hidden" name="bannedissue_id" id="bannedissue_id">


                    <div class="form-group">
                       <label for="name" class="col-sm-12 control-label">*Delivery No.</label>
                       <div id="delivery_dd_add"></div>
                       <div class="col-sm-12 delivery_dd">
                          <select class="form-control" id="delivery_id"></select>
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
                        <select class="form-control violation" id="violation" name="violation">
                          <option value=''>Please select Nature of Violation</option>
                          <option value='Warning'>Warning</option>
                          <option value='Ban'>Ban</option>
                        </select>
                      </div>
                    </div>

                    <div class="form-group">
                      <label class="col-sm-12 control-label">*Date and Time</label>
                      <div class="col-sm-12">
                        <input type="date" class="form-control datepicker" name="date_time" id="date_time" required="">
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

                    <div class="offset-8 col-md-2 col-sm-12">
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
                <h4 class="modal-title">View Issue</h4>

                <button type="button" class="close" data-dismiss="modal">&times;</button> 
            </div>
            <div class="modal-body">
                
                <div class="row">

                  <div class="col-md-6" style="line-height: 0px">
                    Delivery No.:
                  </div>
                  <div class="col-md-6" style="line-height: 0px">
                    <b><p id="view_name"></p></b>
                  </div>
               
                  <div class="col-md-6" style="line-height: 0px">
                    Location:
                  </div>
                  <div class="col-md-6" style="line-height: 0px">
                    <b><p id="view_location"></p></b>
                  </div>
             
                  <div class="col-md-6" style="line-height: 0px">
                    Date and Time:
                  </div>
                  <div class="col-md-6" style="line-height: 0px">
                    <b><p id="view_date_time"></p></b>
                  </div>
              
                  <div class="col-md-6" style="line-height: 0px">
                    Nature of Violation:
                  </div>
                  <div class="col-md-6" style="line-height: 0px">
                    <b><p id="view_violation"></p></b>
                  </div>
                  <div class="col-md-6" style="line-height: 0px">
                    Reason:
                  </div>
                  <div class="col-md-6" style="line-height: 0px">
                    <b><p id="view_reason"></p></b>
                  </div>
                  <div class="col-md-6" style="line-height: 0px">
                    Additional Information:
                  </div>
                  <div class="col-md-6" style="line-height: 0px">
                    <b><p id="view_additional_information"></p></b>
                  </div>
                  <br></br>
                  <div class="col-xl-4 col-sm-12">
                    <button id="btn-edit" class="btn btn-primary btn-xs btn-block editProduct" type="button">Edit</button>
                  </div>
                  <div class="col-xl-4 col-sm-12">
                    <button id="btn-deactivate" class="btn btn-secondary btn-xs btn-danger btn-block deactivateOrActivateBannedIssue" type="button">Deactivate</button>
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
                 "url": "{{ url('allBannedIssue') }}",
                 "dataType": "json",
                 "type": "POST",
                 "data":{ _token: "{{csrf_token()}}"}
               },
        "columns": [
            { "data": "id" },
            {"data": 'name'},
            {"data": 'location'},
            {"data": 'date'},
            {"data": 'violation'},
            {"data": 'reason'},
            {"data": 'additional_information'},
            { "data": "created_at" },
            { "data": "status"},
        ]  

    });

     
    $('#createNewProduct').click(function () {
        $('#spoc').empty();
        $('#saveBtn').val("create-product");
        $('#supplier_id').val('');
        $('#bannedIssueForm').trigger("reset");

        $('.delivery_dd').remove()
        $.ajax({
          type: 'POST', 
          url: "{{ url('getAllSchedules') }}",
          dataType: 'json',
          data:{ _token: "{{csrf_token()}}"},
          success: function (data) {
              console.log(data);
              $('#delivery_dd_add').append('<div class="col-sm-12 delivery_dd"><select class="form-control" multiple id="delivery_id" style="display:none"  name="name"></select></div>')
              $.each(data, function(index, item) {
                  $('#delivery_id').append('<option value="'+item.delivery_id+'">'+item.delivery_id+'</option>')
              });
              $('.delivery_dd').dropdown({
                limitCount: 1,
                multipleMode: 'label',
                // callback
                choice: function (event, selectedProp,x) {
                  
                },
              });
          },error:function(){ 
               console.log(data);
          }
        });


        $('#modelHeading').html("Register Banned Issue");
        $('#ajaxModel').modal({
          backdrop:'static',
          keyboard: false
        })
    });
    
    $('body').on('click', '.editProduct', function () {
      $("#ajaxModelView").modal("hide")
      $('#bannedIssueForm').trigger("reset");      
      var bannedissue_id = $(this).data('id');
      console.log(bannedissue_id)
      $.get("{{ route('ajaxBannedIssue.index') }}" +'/' + bannedissue_id +'/edit', function (data) {
          $('#modelHeading').html("Edit Issue");
          $('#saveBtn').val("edit-user");
          $('#ajaxModel').modal({
            backdrop:'static',
            keyboard: false
          })
          $('#bannedissue_id').val(data.id);
          $('#delivery_id').val(data.name);
          $('#location').val(data.location);
          $('#reason').val(data.reason);
          $('#additional_information').val(data.additional_information);
         
          $("#violation").val(data.violation)


          $('.delivery_dd').dropdown({
            limitCount: 1,
            multipleMode: 'label',
            // callback
            choice: function (event, selectedProp,x) {
              
            },
          });

          $('#date_time').val(data.date_time);
      })
   });
    
    $('#saveBtn').click(function (e) {
        
      //console.log($("#delivery_type").prop('checked'));

        var violation = $("#violation option:selected").val()

        if($("#name").val() == "" || $("#location").val() == "" || $("#reason").val() == "" || $("#date_time").val() == "" || $("#additional_information").val() == "" || violation == ""){
          $("#modalresponse").html("<div class='alert alert-danger'>Please fill in the required fields.</div>")

            if($("#name").val() == "")
              $("#name").css('outline','1px red solid')
            else
              $("#name").css('outline','1px black solid')
          
            if($("#location").val() == "")
              $("#location").css('outline','1px red solid')
            else
              $("#location").css('outline','1px black solid')

            if($("#date_time").val() == "")
              $("#date_time").css('outline','1px red solid')
            else
              $("#date_time").css('outline','1px black solid')

            if($("#reason").val() == "")
              $("#reason").css('outline','1px red solid')
            else
              $("#reason").css('outline','1px black solid')

            if($("#additional_information").val() == "")
              $("#additional_information").css('outline','1px red solid')
            else
              $("#additional_information").css('outline','1px black solid')

             if(violation == "")
              $(".violation").css('color','red')
            else
              $(".violation").css('color','black')


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
     
      $("#ajaxModelView").modal("hide")
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

    $('.data-table tbody').on( 'click', 'tr', function () {
            $('.delivery_dd').remove()
            var id = $(this).find("td:nth-child(1)").first().text().trim()
            $.ajax({
              data: {id:id},
              url: "{{ url('getBannedIssue') }}",
              type: "POST",
              dataType: 'json',
              success: function (data) {
                  console.log(data)
                  

                  $('#view_name').html(data.name)
                  $('#view_location').html(data.location)
                  $('#view_violation').html(data.violation)
                  $('#view_date_time').html(data.date_time)
                  $('#view_reason').html(data.reason)
                  $('#view_additional_information').html(data.additional_information)
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

          $.ajax({
            type: 'POST', 
            url: "{{ url('getAllSchedules') }}",
            dataType: 'json',
            data:{ _token: "{{csrf_token()}}"},
            success: function (data) {
                console.log(data);
                $('#delivery_dd_add').append('<div class="col-sm-12 delivery_dd"><select class="form-control" multiple id="delivery_id" style="display:none"  name="name"></select></div>')
                $.each(data, function(index, item) {
                    $('#delivery_id').append('<option value="'+item.delivery_id+'">'+item.delivery_id+'</option>')
                });
            },error:function(){ 
                 console.log(data);
            }
          });
    });

  



    
     
  });
</script>
@endsection
