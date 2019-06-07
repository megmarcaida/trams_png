@extends('layouts.schedulingapp')

@section('content')

<div class="container-fluid">

    <div class="alert alert-secondary">
        <div class="row">
            <div class="col-md-12">
                <div class="float-left">
                    QR Code / Manual Process
                </div>
            </div>
        </div>
    </div>

    <center>
      <div class="clock">
            {{ $datenow }} &nbsp;
            <div class="hours"></div><!--
         --><div class="minutes"></div><!--
          --><div class="seconds"></div><!--
          --><div class="twelvehr"></div>
      </div>
    </center>

    <div class="row">
        <div class="col-xl-6">
          <h3>Manual Process</h3>
              <form id="scheduleForm" name="scheduleForm" class="form-horizontal">
                    <div id="response"></div>
                    <div class="form-group">
                      <label for="name" class="col-xl-6 control-label">*Process Name</label>
                      <div class="col-md-6">
                        
                        <select class="form-control" required name="process_name">
                          <option value="">Please select proccess</option>
                          <option value="gate-in">Gate-In</option>
                          <option value="dock-in">Dock-In</option>
                          <option value="dock-out">Dock-Out</option>
                          <option value="gate-out">Gate-Out</option>
                        </select>
                      </div>
                    </div>
                    <div class="form-group">
                        <label for="name" class="col-xl-6 control-label">*Delivery Ticket No.</label>
                        <div class="col-xl-6">
                            <input type="text" class="form-control" id="delivery_ticket_id" name="delivery_ticket_id" placeholder="Enter Delivery Ticket No" value="" maxlength="100" required="">
                        </div>
                    </div>
                    <div class="col-xl-6 col-sm-10">
                       <button type="submit" class="btn btn-primary btn-block" id="saveBtn" value="create">Process
                       </button>
                    </div>

                </form>
            
        </div>
    </div>

</div>

    
    
<script type="text/javascript">

      $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
      });

      $('body').on('click', '#saveBtn', function (e) {


        var delivery_ticket_id = $('#delivery_ticket_id').val();
          console.log($("#delivery_ticket_id").val() == "" )
        if($("#delivery_ticket_id").val() == "" ){
            $('#response').show()
            $("#response").html("<div class='alert alert-danger'>Please fill in the required fields.</div>")


            return false;
        }else{

            e.preventDefault();
            $(this).html('Sending..');
        
            console.log($('#scheduleForm').serialize())
            $.ajax({
              data: $('#scheduleForm').serialize(),
              url: "{{ route('changeProcessStatus') }}",
              type: "POST",
              dataType: 'json',
              success: function (data) {
                $('#response').show();
                 if(data.error){

                    $('#response').html("<div class='alert alert-danger'>"+data.error+"</div>")
                 }

                 if(data.success){

                    $('#response').html("<div class='alert alert-success'>"+data.success+"</div>")
                 }
                  $('#scheduleForm').trigger("reset");
                  
                  setTimeout(function(){
                    $('#response').hide("slow");
                  },2000)
                  
                   $('#saveBtn').html('Save Changes');
              },
              error: function (data) {
                  console.log('Error:', data);
                  $('#saveBtn').html('Save Changes');
              }
            });
          }
    });
</script>
@endsection
