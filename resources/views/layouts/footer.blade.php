<!-- Sticky Footer -->
<script type="text/javascript">
	 //change to Finalized
    function changeToFinalized(){
       $.ajax({
          data: {},
          url: "{{ url('changeToFinalized') }}",
          type: "POST",
          dataType: 'json',
          success: function (data) {
            //console.log(data)
            //var current_module = $("#current_module").val()
            //testCalendar(current_module)
            $('#saveBtn').html('Save Changes');
          },
          error: function (data) {
              //console.log('Error:', data);
              $('#saveBtn').html('Save Changes');
          }
        });
    }

    setInterval(function(){
        var today = new Date();
        var time = today.getHours();
       //console.log(time);
        if(time > 16)
        {
          changeToFinalized();
        }
        checkIfNoShowSchedule();
    },5000);


    //change to No-Show
    function checkIfNoShowSchedule(){
       $.ajax({
          data: {},
          url: "{{ url('checkIfNoShowSchedule') }}",
          type: "POST",
          dataType: 'json',
          success: function (data) {
            //console.log(data)
            //var current_module = $("#current_module").val()
            //testCalendar(current_module)
          },
          error: function (data) {
              //console.log('Error:', data);
              $('#saveBtn').html('Save Changes');
          }
        });
    }
</script>
<footer class="sticky-footer">
	<div class="container my-auto">
	  <div class="copyright text-center my-auto">
	    <span>Copyright © Terrasoft 2019</span>
	  </div>
	</div>
</footer>