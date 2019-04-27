
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
                 "url": "{{ url('alltrucks') }}",
                 "dataType": "json",
                 "type": "POST",
                 "data":{ _token: "{{csrf_token()}}"}
               },
        "columns": [
            { "data": "id" },
            {"data": 'supplier_ids'},
            {"data": 'trucking_company'},
            {"data": 'plate_number'},
            {"data": 'brand'},
            {"data": 'model'},
            {"data": 'type'},
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
     
    $('#createNewTruck').click(function () {
        $('#saveBtnTruck').val("create-truck");
        $('#id').val('');
        $('#truckForm').trigger("reset");
        $('#modelHeadingTruck').html("Register Truck");
        $('#ajaxModelTruck').modal({
          backdrop:'static',
          keyboard: false
        })
    });
    
    $('body').on('click', '.editTruck', function () {

      $('#truckForm').trigger("reset");      
      var id = $(this).data('id');
      $.get("{{ route('ajaxtrucks.index') }}" +'/' + id +'/edit', function (data) {
          $('#modelHeadingTruck').html("Edit Truck");
          $('#saveBtnTruck').val("edit-user");
          $('#ajaxModelTruck').modal({
            backdrop:'static',
            keyboard: false
          })
          //console.log(data.id)
          $('#truck_id').val(data.id);
          $('#trucking_company').val(data.trucking_company);
          $('#plate_number').val(data.plate_number);
         
          $('#brand').val(data.brand);

          $('#model').val(data.model);

          $('#type').val(data.type);

          var x = data.supplier_ids.split("|")
          
          x.splice(-1,1)
          
          var supplier_trucks = "";
          $.each( x, function( key, value ) {
          
            var _supplier_name = $("#supplier_id option[value='"+value+"']").text()
            supplier_trucks += _supplier_name + " | ";
            //console.log(_supplier_name)
          });

          $('#truck_suppliers').val(supplier_trucks);
          $('#supplier_ids').val(data.supplier_ids);
          $("input[name=types][value=" + data.type + "]").prop('checked', 'checked');
          // $('#delivery_type').val(data.delivery_type);
      })
    });
    
    $('#saveBtnTruck').click(function (e) {
        
     
        if($("#trucking_company").val() == "" || $("#plate_number").val() == ""){
          $("#modalresponseTruck").html("<div class='alert alert-danger'>Please fill in the required fields.</div>")
          $('#modalresponseTruck').fadeIn(1000);
          setTimeout(function(){
            $('#modalresponseTruck').fadeOut(1000);
          },2000)
        }else{

            e.preventDefault();
            $(this).html('Sending..');
        
            console.log($('#truckForm').serialize())
            $.ajax({
              data: $('#truckForm').serialize(),
              url: "{{ route('ajaxtrucks.store') }}",
              type: "POST",
              dataType: 'json',
              success: function (data) {
                 $('#responseTruck').html("<div class='alert alert-success'>"+data.success+"</div>")
                  $('#truckForm').trigger("reset");
                  $('#ajaxModelTruck').modal('hide');
                  setTimeout(function(){
                    $('#response').hide("slow");
                  },3000)
                  table.draw();
                  $('#saveBtnTruck').html('Save Changes');
             
              },
              error: function (data) {
                  console.log('Error:', data);
                  $('#saveBtnTruck').html('Save Changes');
              }
          });
      }
    });
    
    $('body').on('click', '.deactivateOrActivateTruck', function () {
     
        var id = $(this).data("id");
        var status = $(this).data("status");
        
        if (confirm("Are you want to proceed?")){
            $.ajax({
                url: "{{ url('deactivateOrActivateTruck') }}",
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



    var truck_suppliers = '';
    var supplier_ids = '';
    $('body').on('click', '.add_supplierTruck', function(){
      
      var _supplier_id = $('#supplier_idTruck').children("option:selected").val();
      var _supplier_name = $('#supplier_idTruck').children("option:selected").text();
      var t_suppliers = $('#truck_suppliers').val();
      if(!truck_suppliers.includes(_supplier_name) || !t_suppliers.includes(_supplier_name)){
          supplier_ids += _supplier_id + '|';
          truck_suppliers += _supplier_name + ' | ';
      }else{
        $('#modalresponse').html("<div class='alert alert-danger'>Supplier already added.</div>")
      }

      console.log(supplier_ids);
      console.log(truck_suppliers);

      $('#truck_suppliers').val(truck_suppliers);
      $('#supplier_idsTruck').val(supplier_ids);
    });

  
  });