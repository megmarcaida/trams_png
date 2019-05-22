@extends('layouts.datatableapp')

@section('content')
<!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css" /> -->
<link rel="stylesheet" type="text/css" href="http://www.shieldui.com/shared/components/latest/css/shieldui-all.min.css" />
<link rel="stylesheet" type="text/css" href="http://www.shieldui.com/shared/components/latest/css/light/all.min.css" />
<!-- <script type="text/javascript" src="http://www.shieldui.com/shared/components/latest/js/jquery-1.10.2.min.js"></script> -->
<script type="text/javascript" src="http://www.shieldui.com/shared/components/latest/js/shieldui-all.min.js"></script>
<script src="{{ asset('js/gridData.js') }}"></script>
<style>
        .section {
            top: 0;
            width: 100%;
            z-index: 999;
            background-color: #111111;
            color: #ccc;
            border-top: solid 3px #1E97E3;
            height: 50px;
        }


        .menuitem {
            margin-top: 12px;
            display: inline-block;
            width: 100px;
        }

        .menulink {
            height: 40px;
            padding: 7px;
            border-bottom: 3px solid transparent;
        }

            .menulink:hover {
                border-bottom: 3px solid #1E97E3;
                text-decoration: none;
                color: #1E97E3;
            }

        .footer {
            margin-top: 12px;
        }

        .liitem {
            height: 45px;
        }

        .skillLine {
            display: inline-block;
            width: 100%;
            min-height: 90px;
            padding: 3px 4px;
        }

        skillLineDefault {
            padding: 3px 4px;
        }

        div.skill {
            background: #F58723;
            border-radius: 3px;
            color: white;
            font-weight: bold;
            padding: 3px 4px;
            width: 70px;
        }

        .rating {
            margin-left: 30px;
        }

        .footerQR {
            margin-left: 30%;
        }

        .leftText {
            font-size: 41px;
        }

        .rightText {
            font-size: 41px;
            background: #F58723;
            border-radius: 3px;
            color: white;
            padding: 3px 4px;
        }
    </style>
<div class="container-fluid">

    <div class="alert alert-secondary">
        <div class="row">
            <div class="col-md-12">
                <div class="float-left">
                    Dashboard / General Dashboard
                </div>
                <div class="float-right">| 11:00 AM</div>
                <div class="float-right"> {{ $datenow }} &nbsp;</div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12 col-md-12 col-lg-12">
                <div class="panel panel-primary" style="height: 370px; min-width: 220px;">
                    <div class="panel-body text-center">
                        <p class="lead">
                            <strong>Executive Report</strong>
                        </p>
                    </div>
                    <div>
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-2 col-lg-2 offset-1">
                              <div style="width: 190px; height: 190px; margin-left: 30px;" id="progress1">
                              </div>
                              <div class="text-center" style="margin-top:10px;">
                                <b>ON SITE</b>
                              </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-2 col-lg-2">
                              <div style="width: 190px; height: 190px;margin-left: 30px;" id="progress2">
                              </div>
                              <div class="text-center" style="margin-top:10px;">
                                <b>UNLOADING</b>
                              </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-2 col-lg-2">
                              <div style="width: 190px; height: 190px;margin-left: 30px;" id="progress3">
                              </div>
                              <div class="text-center" style="margin-top:10px;">
                                <b>DELAYED</b>
                              </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-2 col-lg-2">
                              <div style="width: 190px; height: 190px;margin-left: 30px;" id="progress4">
                              </div>
                              <div class="text-center" style="margin-top:10px;">
                                <b>OVERTIME</b>
                              </div>
                            </div>
                             <div class="col-xs-12 col-sm-12 col-md-2 col-lg-2">
                              <div style="width: 190px; height: 190px;margin-left: 30px;" id="progress5">
                              </div>
                              <div class="text-center" style="margin-top:10px;">
                                <b>OVERSTAYING</b>
                              </div>
                            </div>
                        </div>
                    </div>
                </div>
        </div>
      <div class="col-sm-12 col-md-12 col-lg-12">
                <div class="panel panel-primary" style="height: 370px; min-width: 220px;">
                    <div class="panel-body text-center">
                        <p class="lead">
                            <strong>24 Hours Performance Monitoring</strong>
                        </p>
                    </div>
                    <div>
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-2 col-lg-2 offset-1">
                              <div style="width: 190px; height: 190px; margin-left: 30px;" id="progress6">
                              </div>
                              <div class="text-center" style="margin-top:10px;">
                                <b>AVERAGE TRUCK TURNAROUND TIME</b>
                              </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-2 col-lg-2">
                              <div style="width: 190px; height: 190px;margin-left: 30px;" id="progress7">
                              </div>
                              <div class="text-center" style="margin-top:10px;">
                                <b>SLOTTING COMPLIANCE</b>
                              </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-2 col-lg-2">
                              <div style="width: 190px; height: 190px;margin-left: 30px;" id="progress8">
                              </div>
                              <div class="text-center" style="margin-top:10px;">
                                <b>ON-TIME ARRIVALS</b>
                              </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-2 col-lg-2 progress9" style="cursor: pointer;">
                              <div style="width: 190px; height: 190px;margin-left: 30px;" id="progress9">
                              </div>
                              <div class="text-center" style="margin-top:10px;">
                                <b>ON-TIME DEPARTURES</b>
                              </div>
                            </div>
                             <div class="col-xs-12 col-sm-12 col-md-2 col-lg-2">
                              <div style="width: 190px; height: 190px;margin-left: 30px;" id="progress10">
                              </div>
                              <div class="text-center" style="margin-top:10px;">
                                <b>TRUCKS RECEIVED</b>
                              </div>
                            </div>
                        </div>
                    </div>
                </div>
        </div>
    </div>
</div>

<div class="modal fade" id="ajaxModalView" aria-hidden="true">
    <div class="modal-dialog" style="margin-left: 25%;">
        <div class="modal-content" style="min-width: 1000px;width: 100%;">
            <div class="modal-header">
                <h4 class="modal-title" id="modelHeadingView">View Schedule</h4>

                <button type="button" class="close" data-dismiss="modal">&times;</button> 
            </div>
            <div class="modal-body">
              <div class="table table-responsive">
              <table class="table table-bordered table-striped data-table">
                  <thead>
                      <tr>
                          <th>Delivery Ticket No.</th>
                          <th>Slotting Number</th>
                          <th>Supplier Name</th>
                          <th>Truck</th>
                          <th>Plate Number</th>
                          <th>Container Number</th>
                          <th>Dock</th>
                      </tr>
                  </thead>
                  <tbody>
                  </tbody>
              </table>
            </div>
            </div> 
                
            <br>
            <div class="row">
              <div class="col-md-12">
                <button class="btn btn-secondary btn-xs btn-block" type="button" data-dismiss="modal">Close</button> 
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
   

    initializeProgress1();
    initializeProgress2();
    initializeProgress3();
    initializeProgress4();
    initializeProgress5();
    initializeProgress6();
    initializeProgress7();
    initializeProgress8();
    initializeProgress9();
    initializeProgress10();


   setInterval(function(){

      initializeProgress1();
      initializeProgress2();
      initializeProgress3();
      initializeProgress4();
      initializeProgress5();
      initializeProgress6();
      initializeProgress7();
      initializeProgress8();
      initializeProgress9();
      initializeProgress10();
    },5000)

    function initializeProgress1() {
        $.ajax({
            async: false,
            url: "{{ url('getUnloading') }}",
            type: "POST",
            global: false,
            data: {},
            success: function (data) {
              $("#progress1").shieldProgressBar({
                  min: 0,
                  max: data,
                  value: data,
                  layout: "circular",
                  layoutOptions: {
                      circular: {
                          borderColor: data > 31 ? "#8B0000" : "#32CD32",
                          width: 17,
                          borderWidth: 3,
                          color: data > 31 ? "#8B0000" : "#32CD32",
                          backgroundColor: "transparent"
                      }
                  },
                  text: {
                      enabled: true,
                      template: '<span style="font-size:52px;color:#1E98E4;">{0:n0}</span> '
                  }
              });
              console.log(data)
            },
            error: function (data) {
                console.log('Error:', data);
            }
        });
    }

    function initializeProgress2() {
        $.ajax({
            async: false,
            url: "{{ url('getUnloading') }}",
            type: "POST",
            global: false,
            data: {},
            success: function (data) {
              $("#progress2").shieldProgressBar({
                  min: 0,
                  max: data,
                  value: data,
                  layout: "circular",
                  layoutOptions: {
                      circular: {
                          borderColor: data == 0 ? "#32CD32" : "#32CD32",
                          width: 17,
                          borderWidth: 3,
                          color: data == 0 ? "#32CD32" : "#32CD32",
                          backgroundColor: "transparent"
                      }
                  },
                  text: {
                      enabled: true,
                      template: '<span style="font-size:52px;color:#1E98E4;">{0:n0}</span> '
                  }
              });
              console.log(data)
            },
            error: function (data) {
                console.log('Error:', data);
            }
        });

    }

     function initializeProgress3() {
        $.ajax({
            async: false,
            url: "{{ url('getDelayed') }}",
            type: "POST",
            global: false,
            data: {},
            success: function (data) {
              $("#progress3").shieldProgressBar({
                  min: 0,
                  max: data,
                  value: data,
                  layout: "circular",
                  layoutOptions: {
                      circular: {
                          borderColor: data == 0 ? "#32CD32" : "#8B0000",
                          width: 17,
                          borderWidth: 3,
                          color: data == 0 ? "#32CD32" : "#8B0000",
                          backgroundColor: "transparent"
                      }
                  },
                  text: {
                      enabled: true,
                      template: '<span style="font-size:52px;color:#1E98E4;">{0:n0}</span> '
                  }
              });
              console.log(data)
            },
            error: function (data) {
                console.log('Error:', data);
            }
        });
    }

    function initializeProgress4() {

        $.ajax({
            async: false,
            url: "{{ url('getOvertime') }}",
            type: "POST",
            global: false,
            data: {},
            success: function (data) {
              $("#progress4").shieldProgressBar({
                  min: 0,
                  max: data,
                  value: data,
                  layout: "circular",
                  layoutOptions: {
                      circular: {
                          borderColor: data == 0 ? "#32CD32" : "#8B0000",
                          width: 17,
                          borderWidth: 3,
                          color: data == 0 ? "#32CD32" : "#8B0000",
                          backgroundColor: "transparent"
                      }
                  },
                  text: {
                      enabled: true,
                      template: '<span style="font-size:52px;color:#1E98E4;">{0:n0}</span> '
                  }
              });
              console.log(data)
            },
            error: function (data) {
                console.log('Error:', data);
            }
        });
    }

     function initializeProgress5() {

        $.ajax({
            async: false,
            url: "{{ url('getOverStaying') }}",
            type: "POST",
            global: false,
            data: {},
            success: function (data) {
              $("#progress5").shieldProgressBar({
                  min: 0,
                  max: data,
                  value: data,
                  layout: "circular",
                  layoutOptions: {
                      circular: {
                          borderColor: data == 0 ? "#32CD32" : "#8B0000",
                          width: 17,
                          borderWidth: 3,
                          color: data == 0 ? "#32CD32" : "#8B0000",
                          backgroundColor: "transparent"
                      }
                  },
                  text: {
                      enabled: true,
                      template: '<span style="font-size:52px;color:#1E98E4;">{0:n0}</span> '
                  }
              });
              console.log(data)
            },
            error: function (data) {
                console.log('Error:', data);
            }
        });
    }

    function initializeProgress6() {
         $.ajax({
            async: false,
            url: "{{ url('getAverageTurnAroundTime') }}",
            type: "POST",
            global: false,
            data: {},
            success: function (data) {
              var x = data.replace('"','')
              x = x.replace('"','')
              console.log(x)
               $("#progress6").shieldProgressBar({
                  min: 0,
                  max: 0,
                  value: x,
                  layout: "circular",
                  layoutOptions: {
                      circular: {
                          borderColor: "#1E98E4",
                          width: 17,
                          borderWidth: 3,
                          color: "#1E98E4",
                          backgroundColor: "transparent"
                      }
                  },
                  text: {
                      enabled: true,
                      template: '<span style="font-size:28px;color:#1E98E4;">{0}</span> '
                  }
              });
            },
            error: function (data) {
                console.log('Error:', data);
            }
        });
    }

     function initializeProgress7() {
        $.ajax({
            async: false,
            url: "{{ url('getSlottingCompliance') }}",
            type: "POST",
            global: false,
            data: {},
            success: function (data) {
               $("#progress7").shieldProgressBar({
                  min: 0,
                  max: 100,
                  value: data,
                  layout: "circular",
                  layoutOptions: {
                      circular: {
                          borderColor: "#FF7900",
                          width: 17,
                          borderWidth: 3,
                          color: "#1E98E4",
                          backgroundColor: "transparent"
                      }
                  },
                  text: {
                      enabled: true,
                      template: '<span style="font-size:52px;color:#1E98E4;">{0:n0}%</span> '
                  }
              });
              console.log(data)
            },
            error: function (data) {
                console.log('Error:', data);
            }
        });

    }

    function initializeProgress8() {
        $.ajax({
            async: false,
            url: "{{ url('getOnTimeArrivals') }}",
            type: "POST",
            global: false,
            data: {},
            success: function (data) {
               $("#progress8").shieldProgressBar({
                  min: 0,
                  max: 100,
                  value: data,
                  layout: "circular",
                  layoutOptions: {
                      circular: {
                          borderColor: "#FF7900",
                          width: 17,
                          borderWidth: 3,
                          color: "#1E98E4",
                          backgroundColor: "transparent"
                      }
                  },
                  text: {
                      enabled: true,
                      template: '<span style="font-size:52px;color:#1E98E4;">{0:n0}%</span> '
                  }
              });
              console.log(data)
            },
            error: function (data) {
                console.log('Error:', data);
            }
        });

    }

    function initializeProgress9() {

         $.ajax({
            async: false,
            url: "{{ url('getOnTimeDepartures') }}",
            type: "POST",
            global: false,
            data: {},
            success: function (data) {
               $("#progress9").shieldProgressBar({
                  min: 0,
                  max: 100,
                  value: data,
                  layout: "circular",
                  layoutOptions: {
                      circular: {
                          borderColor: "#FF7900",
                          width: 17,
                          borderWidth: 3,
                          color: "#1E98E4",
                          backgroundColor: "transparent"
                      }
                  },
                  text: {
                      enabled: true,
                      template: '<span style="font-size:52px;color:#1E98E4;">{0:n0}%</span> '
                  }
              });
              console.log(data)
            },
            error: function (data) {
                console.log('Error:', data);
            }
        });


       
    }

    function initializeProgress10() {

        $.ajax({
            async: false,
            url: "{{ url('getTrucksCount') }}",
            type: "POST",
            global: false,
            data: {},
            success: function (data) {
              $("#progress10").shieldProgressBar({
                  min: 0,
                  max: data,
                  value: data,
                  layout: "circular",
                  layoutOptions: {
                      circular: {
                          borderColor: "#FF7900",
                          width: 17,
                          borderWidth: 3,
                          color: "#FF7900",
                          backgroundColor: "transparent"
                      }
                  },
                  text: {
                      enabled: true,
                      template: '<span style="font-size:52px;color:#1E98E4;">{0:n0}</span> '
                  }
              });
              console.log(data)
            },
            error: function (data) {
                console.log('Error:', data);
            }
        });
    }


    function getData(url,process_status,status,isModal){
      if(url == "getTrucksThatsIsNotOnTime"){
        url = "{{ url('getOnTimeDepartures') }}"
      }
      var table_incoming = $('.data-table').DataTable({
            "bPaginate": true,
            "bLengthChange": false,
            "bFilter": false,
            "bInfo": false,
            "bAutoWidth": false,
            "processing": true,
            "serverSide": true,
            "ajax":{
                     "url": url,
                     "dataType": "json",
                     "type": "POST",
                     "data":{ _token: "{{csrf_token()}}",process_status:process_status,status:status,isModal:isModal}
                   },
            "columns": [
                { "data": "id" },
                {"data": 'slotting_time'},
                {"data": 'supplier_name'},
                {"data": 'truck'},
                {"data": 'plate_number'},
                {"data": 'container_number'},
                {"data": 'dock'},
            ],
            'columnDefs': [ {
            'targets': [0,1,2,3,4,5], // column index (start from 0)
            'orderable': false, // set orderable false for selected columns
            }]    
      });
    }
      


    //click reports
    $("body").on("click",".progress9",function(){
      console.log("clicked");

      getData("getTrucksThatsIsNotOnTime","incoming",[8,10],1)
      $('#ajaxModalView').modal({
        backdrop:'static',
        keyboard: false
      });
    });
     
  });



      
</script>
@endsection
