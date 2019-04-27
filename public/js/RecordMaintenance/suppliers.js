
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
                 "url": "{{ url('allsuppliers') }}",
                 "dataType": "json",
                 "type": "POST",
                 "data":{ _token: "{{csrf_token()}}"}
               },
        "columns": [
            // { "data": "id" },
            {"data": 'vendor_code'},
            {"data": 'supplier_name'},
            {"data": 'delivery_type'},
            {"data": 'ordering_days'},
            {"data": 'module'},
            {"data": 'spoc_firstname'},
            {"data": 'spoc_lastname'},
            {"data": 'spoc_contact_number'},
            {"data": 'spoc_email_address'},
            { "data": "created_at" },
            { "data": "status"},
            { "data": "options" },
        ]  

    });

    // var table = $('.data-table').DataTable({
    //     processing: true,
    //     serverSide: true,
    //     ajax: "{{ route('ajaxsuppliers.index') }}",
    //     columns: [
    //         //{data: 'DT_RowIndex', name: 'DT_RowIndex'},
    //         {data: 'vendor_code', name: 'vendor_code'},
    //         {data: 'supplier_name', name: 'supplier_name'},
    //         {data: 'delivery_type', name: 'delivery_type'},
    //         {data: 'ordering_days', name: 'ordering_days'},
    //         //{data: 'module', name: 'module'},
    //         // {data: 'spoc_firstname', name: 'spoc_firstname'},
    //         // {data: 'spoc_lastname', name: 'spoc_lastname'},
    //         // {data: 'spoc_contact_number', name: 'spoc_contact_number'},
    //         // {data: 'spoc_email_address', name: 'spoc_email_address'},
    //         {data: 'action', name: 'action', orderable: false, searchable: false}
    //     ]
    // });
     
    $('#createNewProduct').click(function () {
        $('#spoc').empty();
        $('#saveBtn').val("create-product");
        $('#supplier_id').val('');
        $('#supplierForm').trigger("reset");
        $('#modelHeading').html("Register Supplier");
        $('#ajaxModel').modal({
          backdrop:'static',
          keyboard: false
        })
    });
    
    $('body').on('click', '.editProduct', function () {

      $('#spoc').empty();
      $('#supplierForm').trigger("reset");      
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
            $("input[value=" + $.trim(value) + "]").prop('checked', true);
          });

          $.each( modules_arr, function( key, value ) {
            $("input[value='" + $.trim(value) + "']").prop('checked', true);
          });

          for (var i = 1; i < spoc_length; i++) {
            $("#spoc").append('<div class="appendedSpoc'+ i+'"><hr><a href="#" class="removeSpoc btn btn-danger" data-id="'+i+'">Remove</a><div class="form-group"><label for="name" class="col-sm-12 control-label">SPOC First Name</label><div class="col-sm-12"><input type="text" class="form-control" id="spoc_first_name'+i+'" name="spoc_first_name[]" placeholder="Enter SPOC First Name" value="" maxlength="50" required=""></div></div><div class="form-group"><label for="name" class="col-sm-12 control-label">SPOC Last Name</label><div class="col-sm-12"><input type="text" class="form-control" id="spoc_last_name'+i+'" name="spoc_last_name[]" placeholder="Enter SPOC Last Name" value="" maxlength="50" required=""></div></div><div class="form-group"><label for="name" class="col-sm-12 control-label">SPOC Contact Number</label><div class="col-sm-12"><input type="text" class="form-control" id="spoc_contact_number'+i+'" name="spoc_contact_number[]" placeholder="Enter SPOC Contact Number" value="" maxlength="50" required=""></div></div><div class="form-group"><label for="name" class="col-sm-12 control-label">SPOC Email Address</label><div class="col-sm-12"><input type="email" class="form-control" id="spoc_email_address'+i+'" name="spoc_email_address[]" placeholder="Enter SPOC Email Address" value="" maxlength="150" required=""></div></div></div>');
          }

          $.each( spoc_first_name_arr, function( key, value ) {
            $("#spoc_first_name"+key).val($.trim(value.replace("<br>","")))
            //console.log($.trim(value.replace("<br>","")));
          });

          $.each( spoc_last_name_arr, function( key, value ) {
             $("#spoc_last_name"+key).val($.trim(value.replace("<br>","")))
            //console.log($.trim(value.replace("<br>","")));
          });

          $.each( spoc_contact_number_arr, function( key, value ) {
             $("#spoc_contact_number"+key).val($.trim(value.replace("<br>","")))
            //console.log($.trim(value.replace("<br>","")));
          });

          $.each( spoc_email_address_arr, function( key, value ) {
            $("#spoc_email_address"+key).val($.trim(value.replace("<br>","")))
            //console.log($.trim(value.replace("<br>","")));
          });

          $("input[name=delivery_types][value=" + data.delivery_type + "]").prop('checked', 'checked');
          // $('#delivery_type').val(data.delivery_type);
      })
   });
    
    $('#saveBtn').click(function (e) {
        
      console.log($("#delivery_type").prop('checked'));


        if($("#vendor_code").val() == "" || $("#supplier_name").val() == "" || $("#delivery_type").prop('checked') == false){
          $("#modalresponse").html("<div class='alert alert-danger'>Please fill in the required fields.</div>")
          $('#modalresponse').fadeIn(1000);
          setTimeout(function(){
            $('#modalresponse').fadeOut(1000);
          },2000)
        }else{

            e.preventDefault();
            $(this).html('Sending..');
        
            console.log($('#supplierForm').serialize())
            $.ajax({
              data: $('#supplierForm').serialize(),
              url: "{{ route('ajaxsuppliers.store') }}",
              type: "POST",
              dataType: 'json',
              success: function (data) {
                 $('#response').html("<div class='alert alert-success'>"+data.success+"</div>")
                  $('#supplierForm').trigger("reset");
                  $('#ajaxModel').modal('hide');
                  setTimeout(function(){
                    $('#response').hide("slow");
                  },3000)
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
    
    $('body').on('click', '.deactivateOrActivateSupplier', function () {
     
        var supplier_id = $(this).data("id");
        var status = $(this).data("status");
        console.log(status)
        console.log(supplier_id)
        if (confirm("Are you want to proceed?")){
            $.ajax({
                url: "{{ url('deactivateOrActivateSupplier') }}",
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


    $('body').on('click','.removeSpoc',function(){
      console.log('test');
      var id = $(this).attr("data-id");
      console.log(id);
      $(".appendedSpoc"+id).remove();
    
    });
      

    var count = 1;
    $("#addSpocBtn").click(function(e){

        $("#spoc").append('<div class="appendedSpoc'+ count+'"><hr><a href="#" class="removeSpoc btn btn-danger" data-id="'+count+'">Remove</a><div class="form-group"><label for="name" class="col-sm-12 control-label">SPOC First Name</label><div class="col-sm-12"><input type="text" class="form-control" class="spoc_first_name" name="spoc_first_name[]" placeholder="Enter SPOC First Name" value="" maxlength="50" required=""></div></div><div class="form-group"><label for="name" class="col-sm-12 control-label">SPOC Last Name</label><div class="col-sm-12"><input type="text" class="form-control" class="spoc_last_name" name="spoc_last_name[]" placeholder="Enter SPOC Last Name" value="" maxlength="50" required=""></div></div><div class="form-group"><label for="name" class="col-sm-12 control-label">SPOC Contact Number</label><div class="col-sm-12"><input type="text" class="form-control" class="spoc_contact_number" name="spoc_contact_number[]" placeholder="Enter SPOC Contact Number" value="" maxlength="50" required=""></div></div><div class="form-group"><label for="name" class="col-sm-12 control-label">SPOC Email Address</label><div class="col-sm-12"><input type="email" class="form-control" class="spoc_email_address" name="spoc_email_address[]" placeholder="Enter SPOC Email Address" value="" maxlength="150" required=""></div></div></div>');

        count++;
    });



    
     
  });