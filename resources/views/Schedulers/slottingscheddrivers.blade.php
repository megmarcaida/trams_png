<div class="modal fade" id="ajaxModelDriver" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modelHeadingDriver"></h4>

                <button type="button" class="close" data-dismiss="modal">&times;</button> 
            </div>
            <div class="modal-body">
                <div class="modalresponseDriver"></div> 
                <form id="driverForm" name="driverForm" class="form-horizontal">
                    
                    <input type="hidden" name="id" id="id">

                    <div class="form-group">
                        <label for="name" class="col-sm-12 control-label">*Driver ID</label>
                        <div class="col-sm-12">
                            <input type="text" readonly="" class="form-control" id="driver_id" name="id" value="" maxlength="100" required="">
                        </div>
                    </div>


                    <div class="form-group">
                       <label for="name" class="col-sm-12 control-label">*Supplier</label>
                       <div class="col-sm-12">
                          <select  class="form-control" id="supplier_idDriver" name="supplier_id">
                             @foreach($supplierData['data'] as $supplier)
                               <option value='{{ $supplier->id }}'>{{ $supplier->supplier_name }}</option>
                             @endforeach
                          </select>
                        </div>
                        <br>
                        <div class="col-sm-12">
                          <a href="#" class="btn btn-primary add_supplierDriver">Add Supplier</a>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="name" class="col-sm-12 control-label">*Driver Suppliers</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="driver_suppliers" disabled="" required="">
                            <input type="hidden" id="supplier_idsDriver" name="supplier_ids">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="name" class="col-sm-12 control-label">*Logistics Company</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="logistics_company" name="logistics_company" placeholder="Enter Logistics Company" value="" maxlength="100" required="">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="name" class="col-sm-12 control-label">*First Name</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="first_name" name="first_name" placeholder="Enter First Name" value="" maxlength="50" required="">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="name" class="col-sm-12 control-label">*Last Name</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="last_name" name="last_name" placeholder="Enter Last Name" value="" maxlength="50" required="">
                        </div>
                    </div>

                      <div class="form-group">
                        <label for="name" class="col-sm-12 control-label">*Mobile Number</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="mobile_number" name="mobile_number" placeholder="Enter Mobile Number" value="" maxlength="50" required="">
                        </div>
                    </div>

                    <div class="form-group">
                      <label class="col-sm-12 control-label">*Company ID Number</label>
                      <div class="col-sm-12">
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="company_id_number" name="company_id_number" placeholder="Enter Company ID Number" value="" maxlength="50" required="">
                        </div>
                      </div>
                    </div>

                    <div class="form-group">
                      <label class="col-sm-12 control-label">*License Number</label>
                      <div class="col-sm-12">
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="license_number" name="license_number" placeholder="Enter License Number" value="" maxlength="50" required="">
                        </div>
                      </div>
                    </div>
                    <input type="hidden" name="isApproved" value="0">
                    @if(Auth::user()->role_id == 3)
                    <div class="form-group">
                      <label class="col-sm-12 control-label">*Date of Safety Orientation</label>
                      <div class="col-sm-12">
                        <div class="col-sm-12">
                        <input type="datetime-local" class="form-control datepicker" name="dateOfSafetyOrientation" id="dateOfSafetyOrientationDriver" required="">
                        <input type="hidden" id="isApprovedDriver" name="isApproved" value="1">
                        </div>
                      </div>
                    </div>
                     @endif
                    <br>

                    <div class="col-sm-offset-2 col-sm-10">
                       <button type="submit" class="btn btn-primary" id="saveBtnDriver" value="create">Save changes
                       </button>
                    </div>

                </form>

            </div>
        </div>
    </div>
</div>

   
<div class="modal fade" id="viewPendingRegistrationModalDriver" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="viewPendingRegistrationHeadingDriver"></h4>

                <button type="button" class="close" data-dismiss="modal">&times;</button> 
            </div>
            <div class="modal-body">
                <div class="modalresponseDriver"></div> 
                <div class="col-sm-12">
                  <div id="pendingRegistrationListDriver"></div>
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
                 "url": "{{ url('alldrivers') }}",
                 "dataType": "json",
                 "type": "POST",
                 "data":{ _token: "{{csrf_token()}}"}
               },
        "columns": [
            { "data": "id" },
            {"data": 'supplier_ids'},
            {"data": 'logistics_company'},
            {"data": 'first_name'},
            {"data": 'last_name'},
            {"data": 'mobile_number'},
            {"data": 'company_id_number'},
            {"data": 'license_number'},
            {"data": 'dateOfSafetyOrientation'},
            { "data": "status"},
            { "data": "isApproved"},
            { "data": "options" },
        ]  

    });
     
    $('#createNewDriver').click(function () {
        $('#saveBtnDriver').val("create-driver");
        $('#id').val('');
        $('#driverForm').trigger("reset");
        $('#modelHeadingDriver').html("Register Driver");
        $('#ajaxModelDriver').modal({
          backdrop:'static',
          keyboard: false
        })
    });


    $('#viewPendingRegistration').click(function(){
     
        $('#viewPendingRegistrationHeading').html("View Pending Driver Registration");
        $("#viewPendingRegistrationModal .modal-content").css({
          width:"900px",
          left: "-150px"
        }); 
        $('#viewPendingRegistrationModal').modal({
          backdrop:'static',
          keyboard: false
        })
    });


  var loadViewPending = function(){
    var container = $("#pendingRegistrationListDriver");
    $.ajax({
      type: 'GET', //THIS NEEDS TO BE GET
      url: '../showPendingRegistrations',
      dataType: 'json',
      success: function (data) {
          console.log(data.length);

          container.html('');
          if(data.length == 0){
             container.append('No Results found.');
          }
          $.each(data, function(index, item) {
              container.html(''); //clears container for new data
              $.each(data, function(i, item) {
                   container.append('<div class="row" style="border:2px solid #ddd;padding:15px;"><div class="col-sm-6 col-md-6 col-lg-6"> <div class="form-group"><label class="col-sm-12 control-label">Logistics Company: ' + item.logistics_company + '</label><label class="col-sm-12 control-label">First Name: ' + item.first_name + '</label><label class="col-sm-12 control-label">Last Name: ' + item.last_name + '</label><label class="col-sm-12 control-label">Mobile Number: ' + item.mobile_number + '</label><label class="col-sm-12 control-label">Company ID Number: ' + item.company_id_number + '</label><label class="col-sm-12 control-label">License Number: ' + item.license_number + '</label><label class="col-sm-12 control-label">Date of Safety Orientation: ' + item.dateOfSafetyOrientation + '</label></div></div><div class="col-sm-6 col-md-6 col-lg-6"> <div class="form-group"> <div class="col-sm-12"><br><br><form id="driverRegistration'+item.id+'" name="driverRegistration" class="form-horizontal"><input type="hidden" name="id" value="'+item.id+'"><input type="datetime-local" class="form-control datepicker" name="dateOfSafetyOrientation" id="dateOfSafetyOrientation'+item.id+'" required=""><div class="col-sm-offset-2 col-sm-10"><br><a class="btn btn-primary completeDriverRegistration" data-id="'+item.id+'" value="create">Complete Driver Registration</a></div></form></div></div></div><div>');
              });
              container.append('<br>');
          });
      },error:function(){ 
           console.log(data);
      }
    });
  }

  $('body').on('click', '.completeDriverRegistration', function (e) {

      var dateOfSafetyOrientation = $('#dateOfSafetyOrientation'+ $(this).attr("data-id"));
      //console.log(dateOfSafetyOrientation)

      if(dateOfSafetyOrientation.val() == ""){
          $(".modalresponse").html("<div class='alert alert-danger'>Please fill in the required fields.</div>")
          dateOfSafetyOrientation.css("border","red 2px solid")
          $('#modalresponse').fadeIn(1000);
          setTimeout(function(){
            $('#modalresponse').fadeOut(1000);
          },2000)
      }else{
        //console.log($('#driverRegistration').serialize())
        $.ajax({
              data: $('#driverRegistration'+ $(this).attr("data-id")).serialize(),
              url: "{{ url('completeDriverRegistration') }}",
              type: "POST",
              dataType: 'json',
              success: function (data) {
                 $('#response').html("<div class='alert alert-success'>"+data.success+"</div>")
                  $('#viewPendingRegistrationModal').modal('hide');
                  setTimeout(function(){
                    $('#response').hide("slow");
                  },3000)
                  table.draw();
                  loadViewPending();
              },
              error: function (data) {
                  console.log('Error:', data);
                  $('#saveBtnDriver').html('Save Changes');
              }
          });
      }
      
  });

  
  $( function() {

      loadViewPending();
    $(".datepicker").datepicker();
  } ); 


    $('body').on('click', '.editDriver', function () {
      $('.modalresponseDriver').empty();
      $('#driverForm').trigger("reset");      
      var id = $(this).data('id');
      $.get("{{ route('ajaxdrivers.index') }}" +'/' + id +'/edit', function (data) {
          $('#modelHeadingDriver').html("Edit Driver");
          $('#saveBtnDriver').val("edit-user");
          $('#ajaxModelDriver').modal({
            backdrop:'static',
            keyboard: false
          })
          //console.log(data.id)
          $('#driver_id').val(data.id);
          $('#logistics_company').val(data.logistics_company);
          $('#first_name').val(data.first_name);
         
          $('#last_name').val(data.last_name);

          $('#mobile_number').val(data.mobile_number);

          $('#company_id_number').val(data.company_id_number);

           $('#license_number').val(data.license_number);

          var x = data.supplier_ids.split("|")
          
          x.splice(-1,1)
          
          var supplier_drivers = "";
          $.each( x, function( key, value ) {
          
            var _supplier_name = $("#supplier_id option[value='"+value+"']").text()
            supplier_drivers += _supplier_name + " | ";
            //console.log(_supplier_name)
          });

          $('#driver_suppliers').val(supplier_drivers);
          $('#supplier_idsDriver').val(data.supplier_ids);
          data.dateOfSafetyOrientation = data.dateOfSafetyOrientation.replace(" ","T")

          //console.log(data.dateOfSafetyOrientation)
          document.getElementById("dateOfSafetyOrientation").value = data.dateOfSafetyOrientation;
          // $('#delivery_type').val(data.delivery_type);
      })
   });
    
    var role_id = {{ Auth::user()->role_id }}
    $('#saveBtnDriver').click(function (e) {
        
        if(role_id == 3 && $("#dateOfSafetyOrientationDriver").val() == ""){
          $("#isApprovedDriver").val('0')
        }
     
        if($("#logistics_company").val() == "" || $("#first_name").val() == ""){
          $(".modalresponseDriver").html("<div class='alert alert-danger'>Please fill in the required fields.</div>")
          $('.modalresponseDriver').fadeIn(1000);
          setTimeout(function(){
            $('.modalresponseDriver').fadeOut(1000);
          },2000)
        }else{

            e.preventDefault();
            $(this).html('Sending..');
        
            console.log($('#driverForm').serialize())
            $.ajax({
              data: $('#driverForm').serialize(),
              url: "{{ route('ajaxdrivers.store') }}",
              type: "POST",
              dataType: 'json',
              success: function (data) {
                $('#response').show();
                 $('#response').html("<div class='alert alert-success'>"+data.success+"</div>")
                  $('#driverForm').trigger("reset");
                  $('#ajaxModelDriver').modal('hide');
                  setTimeout(function(){
                    $('#response').fadeOut("slow");
                  },3000)
                  table.draw();
                   $('#saveBtnDriver').html('Save Changes');
              },
              error: function (data) {
                  console.log('Error:', data);
                  $('#saveBtnDriver').html('Save Changes');
              }
          });
      }
    });
    
    $('body').on('click', '.deactivateOrActivateDriver', function () {
     
        var id = $(this).data("id");
        var status = $(this).data("status");
        
        if (confirm("Are you want to proceed?")){
            $.ajax({
                url: "{{ url('deactivateOrActivateDriver') }}",
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



    var driver_suppliers = '';
    var supplier_ids = '';
    $('body').on('click', '.add_supplierDriver', function(){
      
      var _supplier_id = $('#supplier_idDriver').children("option:selected").val();
      var _supplier_name = $('#supplier_idDriver').children("option:selected").text();
      var d_suppliers = $('#supplier_idsDriver').val();
      if(!supplier_ids.includes(_supplier_id) || !d_suppliers.includes(_supplier_id)){
          supplier_ids += _supplier_id + '|';
          driver_suppliers += _supplier_name + ' | ';
      }else{
        $('.modalresponseDriver').html("<div class='alert alert-danger'>Supplier already added.</div>")
      }

      //console.log(supplier_ids);
      //console.log(driver_suppliers);

      $('#driver_suppliers').val(driver_suppliers);
      $('#supplier_idsDriver').val(supplier_ids);
    });

  
  });
</script>