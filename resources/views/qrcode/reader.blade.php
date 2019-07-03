<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- CSRF Token -->
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>{{ config('app.name', 'TRAMS') }}</title>
  <script src="{{ asset('js/jsQR.js') }}"></script>
  <link href="https://fonts.googleapis.com/css?family=Ropa+Sans" rel="stylesheet">    <!-- Styles -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css" />
  <style>
    body {
      font-family: 'Ropa Sans', sans-serif;
      color: #333;
      max-width: 640px;
      margin: 0 auto;
      position: relative;
    }

    #githubLink {
      position: absolute;
      right: 0;
      top: 12px;
      color: #2D99FF;
    }

    h1 {
      margin: 10px 0;
      font-size: 40px;
    }

    #loadingMessage {
      text-align: center;
      padding: 40px;
      background-color: #eee;
    }

    #canvas {
      width: 100%;
    }

    #output {
      margin-top: 20px;
      background: #eee;
      padding: 10px;
      padding-bottom: 0;
    }

    #output div {
      padding-bottom: 10px;
      word-wrap: break-word;
    }

    #noQRFound {
      text-align: center;
    }
  </style>
</head>
<body>
  <br>
  <a href="/" class="btn btn-secondary">Back to Dashboard</a>
  <br><br>
  <div class="row">
        <div class="col-xl-12">
              <h3 class="text-center">QR Scanner</h3>
              <div id="outputMessage" class="text-center">No QR code detected.</div>
              <form id="scheduleForm" name="scheduleForm" class="form-horizontal">
                    <div id="response"></div>
                    <div class="row">
                      <div class="col-md-6">
                        <div class="form-group">
                          <label for="name" class="col-xl-12 control-label">*Process Name</label>
                          <div class="col-md-12">
                            
                            <select class="form-control process_name" required name="process_name">
                              <option value="">Please select proccess</option>
                              <option value="gate-in">Gate-In</option>
                              <option value="dock-in">Dock-In</option>
                              <option value="dock-out">Dock-Out</option>
                              <option value="gate-out">Gate-Out</option>
                            </select>
                          </div>
                        </div></div>
                      <div class="col-md-6">
                          <div class="form-group">
                              <label for="name" class="col-xl-12 control-label">*Delivery Ticket No.</label>
                              <div class="col-xl-12">
                                  <input type="text" readonly="" class="form-control" id="delivery_ticket_id" name="delivery_ticket_id" placeholder="Enter Delivery Ticket No" value="" maxlength="100" required="">
                              </div>
                          </div>
                      </div>

                    </div>

                </form>
            
        </div>
    </div>
  <div id="loadingMessage">🎥 Unable to access video stream (please make sure you have a webcam enabled)</div>
  <canvas id="canvas" hidden></canvas>
    <div id="output" hidden>
    
   <!--  <div hidden><b>Data:</b> <span id="outputData"></span></div> -->
  </div>
  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  <script>
    $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
    });

    var video = document.createElement("video");
    var canvasElement = document.getElementById("canvas");
    var canvas = canvasElement.getContext("2d");
    var loadingMessage = document.getElementById("loadingMessage");
    var outputContainer = document.getElementById("output");
    var outputMessage = document.getElementById("outputMessage");
    //var outputData = document.getElementById("outputData");

    function drawLine(begin, end, color) {
      canvas.beginPath();
      canvas.moveTo(begin.x, begin.y);
      canvas.lineTo(end.x, end.y);
      canvas.lineWidth = 4;
      canvas.strokeStyle = color;
      canvas.stroke();
    }

    // Use facingMode: environment to attemt to get the front camera on phones
    
    navigator.mediaDevices.getUserMedia({ video: { facingMode: "environment" } }).then(function(stream) {
      video.srcObject = stream;
      video.setAttribute("playsinline", true); // required to tell iOS safari we don't want fullscreen
      video.play();
      console.log($(".process_name option:selected").val())

      requestAnimationFrame(tick);
      
    });


    var countData = 0
    function tick() {
      loadingMessage.innerText = "⌛ Loading video..."
      if (video.readyState === video.HAVE_ENOUGH_DATA) {
        loadingMessage.hidden = true;
        canvasElement.hidden = false;
        outputContainer.hidden = false;

        canvasElement.height = video.videoHeight;
        canvasElement.width = video.videoWidth;
        canvas.drawImage(video, 0, 0, canvasElement.width, canvasElement.height);
        var imageData = canvas.getImageData(0, 0, canvasElement.width, canvasElement.height);
        var code = jsQR(imageData.data, imageData.width, imageData.height, {
          inversionAttempts: "dontInvert",
        });
        if (code) {
          
          //alert(code.data)

          $(document).ready(function() {
             
               //if(countData==0){

                drawLine(code.location.topLeftCorner, code.location.topRightCorner, "#FF3B58");
                drawLine(code.location.topRightCorner, code.location.bottomRightCorner, "#FF3B58");
                drawLine(code.location.bottomRightCorner, code.location.bottomLeftCorner, "#FF3B58");
                drawLine(code.location.bottomLeftCorner, code.location.topLeftCorner, "#FF3B58");
                //outputMessage.hidden = true;
                //outputData.parentElement.hidden = false;
                //outputData.innerText = code.data;
                $('#delivery_ticket_id').val(code.data);
                //   setTimeout(function(){
                //         window.open('http://192.168.1.10:8000/changeProcessStatus_jsonp?id='+code.data).close();
                //         countData = 0;
                //         console.log("Scanned")
                //   },3000);
                // countData++
                if($(".process_name option:selected").val() != ""){
                    //alert("test");
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
                }else{
                   $('#response').html("<div class='alert alert-danger'>Please select Procces</div>")
                }
                

               //}

              
             
          });
        } else {
          // outputMessage.hidden = false;
          // outputData.parentElement.hidden = true;
        }

      }
      requestAnimationFrame(tick);
    }
  </script>
 
</body>
</html>
