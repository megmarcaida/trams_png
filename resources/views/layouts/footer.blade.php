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

        incomingTrucks12hrs();
        incomingTrucks();
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

     function incomingTrucks12hrs(){
        $.ajax({
            async: false,
            url: "{{ url('checkIfIncomingDock') }}",
            type: "POST",
            global: false,
            data: {process_status:"incoming"},
            success: function (data) {
              console.log(data)
              
            },
            error: function (data) {
                console.log('Error:', data);
            }
        });
    }

    function incomingTrucks(){
        $.ajax({
            async: false,
            url: "{{ url('checkIfIncoming') }}",
            type: "POST",
            global: false,
            data: {process_status:"incoming"},
            success: function (data) {
              console.log(data.length)
             
            },
            error: function (data) {
                console.log('Error:', data);
            }
        });
    }

</script>

<script type="text/javascript">
  var $document = $(document);
(function () { 
  var clock = function () {
      clearTimeout(timer);
    
      date = new Date();    
      hours = date.getHours();
      minutes = date.getMinutes();
      seconds = date.getSeconds();
      dd = (hours >= 12) ? 'pm' : 'am';
      
      hours = (hours > 12) ? (hours - 12) : hours
      
      var timer = setTimeout(clock, 1000);
      var preMinute = ""
      var preSeconds = ""

    if(minutes < 10){
       preMinute = "0";
    }

    if(seconds < 10){
       preSeconds = "0";
    }

    $('.hours').html('<p>' + Math.floor(hours) + ':</p>');
    $('.minutes').html('<p>' + preMinute + Math.floor(minutes) + ':</p>');
    $('.seconds').html('<p>' + preSeconds + Math.floor(seconds) + '</p>');
      $('.twelvehr').html('<p>' + dd + '</p>');
  };
  clock();
})();

(function () {
  $document.bind('contextmenu', function (e) {
    e.preventDefault();
  });  
})();
</script>
<footer class="sticky-footer">
	<div class="container my-auto">
	  <div class="copyright text-center my-auto">
	    <span>Copyright © Terrasoft 2019</span>
	  </div>
	</div>
</footer>