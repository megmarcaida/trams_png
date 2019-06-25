<?php

namespace App\Http\Controllers;

use App\Schedule;
use App\Dock;
use App\Supplier;
use App\Truck;
use App\Driver;
use App\Assistant;
use App\Dock_Unavailability;
use App\Role;
use DateTime;
use Auth;
use Illuminate\Http\Request;

class SchedulerController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
    	$supplierData['data'] = Supplier::where("status",1)->get();

        return view('schedulers/slottingschedule')->with("supplierData",$supplierData);
    }

    public function scheduling(Request $request)
    {
        $supplierData['data'] = Supplier::where("status",1)->get();
        $dockData['data'] = Dock::where("status",1)->get();

        $json_data = array( 
                "supplierData" => $supplierData, 
                "dockData"     => $dockData   
        );
        return view('schedulers/index')->with("json_data",$json_data);
    }

     public function allSchedules(Request $request){

        $search = $request->input('module');
        $status = "";
        $slotting = array();
        $data = array();
        $dow = array();
        
        $role_id = Auth::user()->role_id;
        $roles =  Role::where('id',$role_id)->first();
        $sub_m = explode("|", $roles['submodules']);
        $dock_names = array();
        $dock_ids = array();
        foreach($sub_m as $submodules){
            if($submodules != ""){
                array_push($dock_names, $submodules);
            }
        }
        $dock = Dock::whereIn("dock_name",$dock_names)->get();
        foreach($dock as $d){
            array_push($dock_ids, $d->id);
        }

        $schedules = Schedule::whereIn('dock_id',$dock_ids)->where("status","<>","0")->get();

        $unavailabilities = Dock_Unavailability::whereIn('dock_id',$dock_ids)->where('status','<>','0')->get();
        $get_docks = Dock::where("dock_name",$search)->first();
           
        if(!empty($schedules))
        {
            foreach ($schedules as $schedule)
            {
                if($schedule->dock_id != $get_docks['id']){
                    continue;
                }
                $supplier = Supplier::where('id',$schedule->supplier_id)->first();
                $truck = Truck::where('id',$schedule->truck_id)->first();
                $driver = Driver::where('id',$schedule->driver_id)->first();
                $assistant = Assistant::where('id',$schedule->assistant_id)->first();

                $scheds = trim($schedule->ordering_days);
                $scheds = explode("|", $scheds);
                
                foreach($scheds as $sched){
                    $sched = trim($sched);
                    if($sched == ""){
                        continue;
                    }
                    switch ($sched) {
                        case 'Mon':
                            array_push($dow , 1);
                            break;
                        case 'Tue':
                            array_push($dow, 2);
                            break;
                        case 'Wed':
                            array_push($dow, 3);
                            break;
                        case 'Thu':
                            array_push($dow, 4);
                            break;
                        case 'Fri':
                            array_push($dow, 5);
                            break;
                        case 'Sat':
                            array_push($dow, 6);
                            break;
                        case 'Sun':
                            array_push($dow, 0);
                            break;
                        
                        default:
                            
                            break;
                    }
                }

                $supplier_name = $supplier->supplier_name;
                $po_number = $schedule->po_number;
                $driver_name = $driver->first_name . " " . $driver->last_name;
                $assistant_name = $assistant->first_name . " " . $assistant->last_name;
                $truck_details = "\n" . $schedule->container_number . "\n" . $truck->plate_number;


                $nestedData['id'] = $schedule->id;

                $nestedData['supplier_id'] = $schedule->supplier_id;
                $nestedData['po_number'] = $schedule->po_number;
                $nestedData['supplier_name'] = $supplier->supplier_name;
                $nestedData['slotting_time'] = $schedule->slotting_time;
                $nestedData['container_no'] = $schedule->container_number;
                $nestedData['driver_name'] = $driver_name;
                $nestedData['truck_details'] =  $truck->model . " " . $truck->brand;
                $nestedData['assistant_name'] = $assistant_name;
                $nestedData['plate_number'] = $truck->plate_number;
                $num = $schedule->id;
                $number = str_pad($num, 8, "0", STR_PAD_LEFT);
                $nestedData['delivery_id'] = $number;
                $nestedData['dock_name'] = $schedule->dock_name;
                $nestedData['date_of_delivery'] = $schedule->date_of_delivery;
                $nestedData['recurrent_dateend'] = $schedule->recurrent_dateend;
                $nestedData['recurrence'] = $schedule->recurrence;

                $slotting_ = str_replace("|","",$schedule->slotting_time);
                $start = substr($slotting_, 0, 5);
                $end = substr($slotting_, -5);

                $slotting = explode("|", $schedule->slotting_time);


                $nestedData['start'] = $schedule->date_of_delivery . "T" .$start .":00";
                        $nestedData['end'] = $schedule->date_of_delivery . "T" .$end .":00";
              
                $mat_list = explode("-;-", $schedule->material_list);
           

                $gcas = explode("|", $mat_list[0]);
                $description = explode("|", $mat_list[1]);
                $quantity = explode("|", $mat_list[2]);

                $material_list = "<table class='table table-bordered' style='width:100%'>";
                $material_list .= "<tr>";
                $material_list .= "<th>GCAS</th> <th>Description</th> <th>Quantity</th>";
                $material_list .= "<tr>";
                foreach($gcas as $key => $value){

                    $material_list .= "<tr>";
                    $material_list .= "<td>". $value ."</td>";
                    $material_list .= "<td>". $description[$key] . "</td>";
                    $material_list .= "<td>". $quantity[$key] . "</td>";
                    $material_list .= "<tr>";

                }
                $material_list .= "</table>";

                $nestedData['material_list'] = $material_list;

                $nestedData['created_at'] = date('j M Y h:i a',strtotime($schedule->created_at));
                switch ($schedule->status) {
                    case 1:
                        $status = "Active";
                        break;
                    case 2:
                        $status = "Edited";
                        break;
                    case 3:
                        $status = "Finalized";
                        break;
                    case 4:
                        $status = "Edited Finalized";
                        break;
                    case 5:
                        $status = "No-Show";
                        break;
                    case 6:
                        $status = "Emergency Reschedule";
                        break;
                    
                    default:
                        # code...
                        break;
                }
                $nestedData['status'] = $status;
                $nestedData['title'] =  $supplier_name . "\n Trucks" . $truck_details . "\n" . $driver_name . "\n" . $assistant_name . "\n" . $status;

                $nestedData['backgroundColor'] = $schedule->status == 5 ? "#F08080" : "#11ee99";
                $nestedData['borderColor'] = $schedule->status == 5 ? "#F08080" : "#11ee99";
                $data[] = $nestedData;
                $dow = array();

            }

        }

        if(!empty($unavailabilities))
        {
            foreach ($unavailabilities as $unavailability)
            {

                if($unavailability->dock_id != $get_docks['id']){
                    continue;
                }

                    $_Data['id'] = $unavailability->id;
                    $_Data['title'] =  $unavailability->reason;
                    $_Data['slotting_time'] = $unavailability->time;
                    $_Data['dock_name'] = $unavailability->dock_name;
                    $_Data['date_of_delivery'] = $unavailability->date_of_unavailability;
                    $_Data['recurrence'] = $unavailability->recurrence;

                    $slotting_ = str_replace("|","",$unavailability->time);
                    $start = substr($slotting_, 0, 5);
                    $end = substr($slotting_, -5);

                    $slotting = explode("|", $unavailability->time);

                    $_Data['start'] = date('Y-m-d',strtotime($unavailability->date_of_unavailability)) . "T" .$start .":00";
                    $_Data['end'] = date('Y-m-d',strtotime($unavailability->date_of_unavailability)) . "T" .$end .":00";
                    $_Data['created_at'] = date('j M Y h:i a',strtotime($unavailability->created_at));
                    switch ($unavailability->status) {
                        case 1:
                            $status = "Active";
                            break;
                        case 2:
                            $status = "Edited";
                            break;
                        case 3:
                            $status = "Finalized";
                            break;
                        case 4:
                            $status = "Edited Finalized";
                            break;
                        case 5:
                            $status = "No-Show";
                            break;
                        case 6:
                            $status = "Emergency Reschedule";
                            break;
                        
                        default:
                            # code...
                            break;
                    }
                    $_Data['status'] = $status;
                    $_Data['isForUnavailability'] = 1;
                    $_Data['backgroundColor'] = "#ff7f7f";

                    array_push($data, $_Data);
                

            }
        }
            
        echo json_encode($data); 
    }

    public function getSupplierData(Request $request){


        $trucks = Truck::where("status","1")->get();
        

        $drivers = Driver::where("status","1")->where('isApproved','1')->get();
        $assistants = Assistant::where("status","1")->where('isApproved','1')->get();
        $docks = Dock::where("status","1")->get();
        
        $trucks_suppliers = '';
        $drivers_suppliers = '';
        $assistants_suppliers = '';

        $truckdata = array();
        $driverdata = array();
        $assistantdata = array();
        $dockdata = array();
        

        if(!empty($trucks))
        {
            foreach ($trucks as $truck)
            {
              
                $supplier_ids = explode('|',$truck->supplier_ids);
                
                foreach($supplier_ids as $supplier_id){
                    if($supplier_id == null){
                        continue;
                    }

                    if($supplier_id == $request->id){

                        $suppliers = Supplier::where('id',$supplier_id)->where('status', 1)->first();

                        if($suppliers == null){
                            continue;
                        }


                        $trucks_suppliers .= $suppliers->supplier_name . " | ";

                        $nestedData['id'] = $truck->id;
                        $nestedData['supplier_ids'] =  $trucks_suppliers;
                        $nestedData['plate_number'] = $truck->plate_number;
                        $nestedData['brand'] = $truck->brand;
                        $nestedData['model'] = $truck->model;
                        $nestedData['type'] = $truck->type;
                       
                        $nestedData['status'] = $truck->status == 1 ? "Active" : "Inactive";
                        $truckdata[] = $nestedData;
                    }
                }

                
                //$trucks_suppliers = '';
            }
        }

        if(!empty($drivers))
        {
            foreach ($drivers as $driver)
            {
                $supplier_ids = explode('|',$driver->supplier_ids);
                foreach($supplier_ids as $supplier_id){
                    if($supplier_id == null){
                        continue;
                    }

                    if($supplier_id == $request->id){

                        $suppliers = Supplier::where('id',$supplier_id)->where('status', 1)->first();

                        if($suppliers == null){
                            continue;
                        }


                        $nestedData['id'] = $driver->id;
                        $nestedData['first_name'] = $driver->first_name;
                        $nestedData['last_name'] = $driver->last_name;
                        $nestedData['status'] = $driver->status == 1 ? "Active" : "Inactive";
                        $driverdata[] = $nestedData;
                    }
                }
                
            }
        }

        if(!empty($assistants))
        {
            foreach ($assistants as $assistant)
            {
                $supplier_ids = explode('|',$assistant->supplier_ids);
                foreach($supplier_ids as $supplier_id){
                    if($supplier_id == null){
                        continue;
                    }

                    if($supplier_id == $request->id){

                        $suppliers = Supplier::where('id',$supplier_id)->where('status', 1)->first();

                        if($suppliers == null){
                            continue;
                        }

                        $nestedData['id'] = $assistant->id;
                        $nestedData['first_name'] = $assistant->first_name;
                        $nestedData['last_name'] = $assistant->last_name;
                        $nestedData['status'] = $assistant->status == 1 ? "Active" : "Inactive";
                        $assistantdata[] = $nestedData;
                    }
                }
            }
        }

        if(!empty($docks))
        {
            $suppliers = Supplier::where('id',$request->id)->where('status', 1)->first();
            $dock_ids = explode("|", $suppliers['module']);
            
            foreach($dock_ids as $value){
                foreach($docks as $dock){
                    if(trim($value) == ""){
                        continue;
                    }
                    if(trim($value) == trim($dock->module)){
                        $nestedData['id'] = $dock->id;
                        $nestedData['dock_name'] = $dock->dock_name;
                        $nestedData['status'] = $dock->status == 1 ? "Active" : "Inactive";
                        $dockdata[] = $nestedData;
                    } 
                }
                
            }
            
        }


        $json_data = array(
                    "assistantdata"    => $assistantdata,  
                    "driverdata"       => $driverdata, 
                    "truckdata"        => $truckdata,
                    "dockdata"         => $dockdata
                    );

        echo json_encode($json_data); 
    }

    public function getSlottingTime(Request $request){

        if($request->isForUnavailability == "0"){
            if($request->dock_id == null){

                $slotting = Schedule::where("date_of_delivery",$request->date_of_delivery)->where('status','<>','0')->get();
            }else{

                $slotting = Schedule::where("date_of_delivery",$request->date_of_delivery)->where('dock_id',$request->dock_id)->where('status','<>','0')->get();
            }
        }elseif($request->isForUnavailability == "1") {
            $slotting = Dock_Unavailability::where("date_of_unavailability",$request->date_of_unavailability)->where('status','1')->get();
            $schedule = Schedule::where("date_of_delivery",$request->date_of_unavailability)->where('status','<>','0')->get();
        }

        
        $scheduleData = array();
        $slotting_time = array();
        if(!empty($slotting))
        {
            foreach ($slotting as $slot)
            {

                $nestedData['id'] = $slot->id;
                // $nestedData['slotting_time'] = $slot->slotting_time;
                $nestedData['date_of_delivery'] = $slot->date_of_delivery;
               
                $nestedData['status'] = $slot->status == 1 ? "Active" : "Inactive";

                if($request->isForUnavailability == "0"){
                    $nestedData['slotting_time'] = explode("|",$slot->slotting_time);
                }elseif($request->isForUnavailability == "1"){
                    $nestedData['slotting_time'] = explode("|",$slot->time);
                }

                $scheduleData[] = $nestedData;

            }

            if($request->isForUnavailability == "1"){
                foreach($schedule as $sched){
                    $nestedData['id'] = $sched->id;
                    $nestedData['date_of_delivery'] = $sched->date_of_delivery;
                   
                    $nestedData['status'] = $sched->status == 1 ? "Active" : "Inactive";

                    $nestedData['slotting_time'] = explode("|",$sched->slotting_time);

                    $scheduleData[] = $nestedData;
                }
            }
        }


        echo json_encode($scheduleData); 
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
       
        //for Scheduling
        if($request->isForUnavailability == "0"){


            $gcas = '';
            $description = '';
            $quantity = '';
            $material_list = '';

            foreach($request->gcas as $_gcas){
                $gcas .= $_gcas ."|";
            }

            foreach($request->description as $_description){
                $description .= $_description ."|";
            }

            foreach($request->quantity as $_quantity){
                $quantity .= $_quantity ."|";
            }

            $material_list = $gcas . "-;-" . $description . "-;-" . $quantity; 
            
            $ordering_days='';

            if($request->ordering_days){
                foreach ($request->ordering_days as $ordering_day){
                    $ordering_days .=  $ordering_day.' | ';
                }    
            } 

            $dock_name = '';

            $dock = Dock::where('id',$request->dock_id)->first();
            $dock_name = $dock->dock_name;

            $supplier_id = $request->alt_supplier_id == '' ? $request->supplier_id : $request->alt_supplier_id;
           
            $status = $request->schedule_id != null ? '2' : '1';

            //check if Single Event
            if($request->schedule_id != null){

                $checkIfFinalized = Schedule::find($request->schedule_id);
                $date_yesterday = date('d.m.Y',strtotime("-1 days"));
                $timestamp = $checkIfFinalized->updated_at;
                $timestamp = date('d.m.Y',strtotime($timestamp));
                
                if($timestamp === $date_yesterday){
                    if($checkIfFinalized->status == 3){
                        $checkIfFinalized->update(["status" => 0]);
                        $status = 4;
                        $request->schedule_id = null;
                    }else{
                        $status = 2;
                    }
                }else{
                    $status = 2;
                }

                //return json_encode($timestamp . " - " . $date_yesterday);
                //$date_created = $schedule_finalized['created_date'];
            }

            $last_insert = Schedule::updateOrCreate(['id' => $request->schedule_id],['po_number' => $request->po_number, 'supplier_id' => $supplier_id, 'dock_id' => $request->dock_id,  'dock_name' => $dock_name,'date_of_delivery' => $request->dateOfDelivery,'recurrent_dateend' => $request->recurrent_dateend, 'recurrence' => $request->recurrence, 'ordering_days' => $ordering_days, 'slotting_time' => $request->slotting_time, 'truck_id' => $request->truck_id, 'container_number' => $request->container_number, 'driver_id' => $request->driver_id, 'assistant_id' => $request->assistant_id, 'status' => $status, 'material_list' => $material_list]);  

            $ret = ['success'=>'Schedule saved successfully.',"id"=>$last_insert->id];
                        



                 
        }elseif($request->isForUnavailability == "1") {

            $ordering_days='';

            if($request->ordering_days_unavailability){
                foreach ($request->ordering_days_unavailability as $ordering_day){
                    $ordering_days .=  $ordering_day.' | ';
                }    
            } 

            $dock_name = '';

            $dock = Dock::where('id',$request->dock_id_unavailability)->first();
            $dock_name = $dock->dock_name;

            $status = $request->schedule_id != null ? 2 : 1;

            if($request->recurrence_unavailability == 'Recurrent'){

                $getSlot = str_replace("|","",$request->slotting_time_unavailability);
                $start = substr($getSlot, 0, 5);
                $end = substr($getSlot, -5);

                $startDateTime = $request->dateOfUnavailability;
                $endDateTime = $request->recurrent_dateend;

                // $scheds = trim($ordering_days);
                // $scheds = explode("|", $scheds);
                
                $dates = array();

                foreach($request->ordering_days as $sched){
                    $sched = trim($sched);
                    if($sched == ""){
                        continue;
                    }
                    switch ($sched) {
                        case 'Mon':
                        $dates = $this->getDateForSpecificDayBetweenDates($startDateTime,$endDateTime,1);
                            break;
                        case 'Tue':
                        $dates = $this->getDateForSpecificDayBetweenDates($startDateTime,$endDateTime,2);
                            break;
                        case 'Wed':
                        $dates = $this->getDateForSpecificDayBetweenDates($startDateTime,$endDateTime,3);
                            break;
                        case 'Thu':
                        $dates = $this->getDateForSpecificDayBetweenDates($startDateTime,$endDateTime,4);
                            break;
                        case 'Fri':
                        $dates = $this->getDateForSpecificDayBetweenDates($startDateTime,$endDateTime,5);
                            break;
                        case 'Sat':
                        $dates = $this->getDateForSpecificDayBetweenDates($startDateTime,$endDateTime,6);
                            break;
                        case 'Sun':
                        $dates = $this->getDateForSpecificDayBetweenDates($startDateTime,$endDateTime,7);
                            break;
                        
                        default:
                            
                            break;
                    }

                    $return_conflicts = array();
                    $count = 0;
                    foreach($dates as $date){

                        $checkConflict = $this->checkIfConflictsDate($date,$request->slotting_time_unavailability);
                        if($checkConflict > 0){
                            $nestedData['id'] = $checkConflict;
                            $return_conflicts[] = $nestedData;
                        }else{
                            Dock_Unavailability::updateOrCreate(['id' => $request->unavailability_id],['dock_id' => $request->dock_id_unavailability,  'dock_name' => $dock_name,'date_of_unavailability' => $date, 'recurrence' => $request->recurrence_unavailability, 'ordering_days' => $ordering_days, 'time' => $request->slotting_time_unavailability, 'emergency' => "", 'reason' => $request->reason_unavailability, 'status' => $status]);  
                        } 
                    }

                    if(!empty($return_conflicts)){
                        return response()->json(["error"=>"Recurrent: The dock unavailabilities has conflict","ids"=>$return_conflicts,"type"=>$request->type_unavailability    ]);
                    }

                }
                
                $ret = ['success'=>'Schedule saved successfully.']; 
            }else{

                $checkConflict = $this->checkIfConflictsDate($request->dateOfUnavailability,$request->slotting_time_unavailability);

                if($checkConflict > 0){
                    return response()->json(["success"=>"The dock unavailabilities has conflict with this Delivery ID Number: " . $checkConflict]);
                }else{
                    $ret = ['success'=>'Schedule saved successfully.'];
                    Dock_Unavailability::updateOrCreate(['id' => $request->unavailability_id],
                    ['dock_id' => $request->dock_id_unavailability,  'dock_name' => $dock_name,'date_of_unavailability' => $request->dateOfUnavailability, 'recurrence' => $request->recurrence_unavailability, 'ordering_days' => $ordering_days, 'time' => $request->slotting_time_unavailability, 'emergency' => "", 'reason' => $request->reason_unavailability, 'status' => $status]); 
                }


                
            }

            
        }
        
        return response()->json($ret);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit($id,$isForUnavailability = 0)
    {
        $schedule = Schedule::find($id);
        $dock_unavailability = Dock_Unavailability::find($id);
        $gcas = array();
        $description = array();
        $quantity = array();
        $material_list = array();
        $scheduleData = array();
        if(!empty($schedule))
        {
            $nestedData['id'] = $schedule->id;
            $nestedData['po_number'] = $schedule->po_number;
                $nestedData['supplier_id'] = $schedule->supplier_id;
                $nestedData['dock_id'] = $schedule->dock_id;
                $nestedData['dock_name'] = $schedule->dock_name;
                $nestedData['date_of_delivery'] = date("Y-m-d", strtotime($schedule->date_of_delivery));
                $nestedData['recurrent_dateend'] = date("Y-m-d", strtotime($schedule->recurrent_dateend));
                $nestedData['recurrence'] = $schedule->recurrence;
                $nestedData['ordering_days'] = $schedule->ordering_days;
                $nestedData['truck_id'] = $schedule->truck_id;
                $nestedData['container_number'] = $schedule->container_number;
                $nestedData['driver_id'] = $schedule->driver_id;
                $nestedData['assistant_id'] = $schedule->assistant_id;

                if($schedule->material_list == "" || $schedule->material_list == null){
                    $gcas = array();
                    $description = array();
                    $quantity = array();
                }else{

                    $material_list = explode("-;-", $schedule->material_list);

                    $gcas = explode("|", $material_list[0]);
                    $description = explode("|", $material_list[1]);
                    $quantity = explode("|", $material_list[2]);
                }

                $nestedData['material_list']['gcas'] = $gcas;
                $nestedData['material_list']['description'] = $description;
                $nestedData['material_list']['quantity'] = $quantity;
               
                $nestedData['status'] = $schedule->status == 1 ? "Active" : "Inactive";

                $nestedData['slotting_time_text'] =$schedule->slotting_time;
                $nestedData['slotting_time'] = explode("|",$schedule->slotting_time);    
            $scheduleData = $nestedData;

        }

        return response()->json($scheduleData);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy($id,$status)
    {
        Schedule::find($id)->update(['status' => 0]);
        return response()->json(['success'=>'Supplier deactivated successfully.']);
    }

    public function deactivateOrActivateSchedule(Request $request)
    {
        $schedule = Schedule::find($request->id);
        if($request->isRecurrent == "0"){
            $schedule->update(['reason'=> $request->reason,'status' => 0]);
        }else{
            Schedule::where("recurrent_id",$schedule->recurrent_id)->update(['reason'=> $request->reason,'status' => 0]);
        }
        
        return response()->json(['success'=>'Schedule deactivated successfully.']);
    }

    public function fetchIncompleteMaterials()    {
        $incompleteMaterialList = Schedule::where('material_list','=',null)->where('status','<>','0')->get();
        return response()->json($incompleteMaterialList);
    }

    // public function getDateForSpecificDayBetweenDates($startDate,$endDate,$day_number){
    // $endDate = strtotime($endDate);
    // $days=array('1'=>'Monday','2' => 'Tuesday','3' => 'Wednesday','4'=>'Thursday','5' =>'Friday','6' => 'Saturday','7'=>'Sunday');
    // for($i = strtotime($days[$day_number], strtotime($startDate)); $i <= $endDate; $i = strtotime('+1 week', $i))
    //     $date_array[]=date('Y-m-d',$i);

    //     return $date_array;
    // }

    public function getDateForSpecificDayBetweenDates($startDate, $endDate, $weekdayNumber)
    {
        $startDate = strtotime($startDate);
        $endDate = strtotime($endDate);
        $dateArr = array();
        do {
            if (date("w", $startDate) != $weekdayNumber) {
                $startDate += 24 * 3600;
                // add 1 day
            }
        } while (date("w", $startDate) != $weekdayNumber);
        while ($startDate <= $endDate) {
            $dateArr[] = date('Y-m-d', $startDate);
            $startDate += 7 * 24 * 3600;
            // add 7 days
        }
        return $dateArr;
    }

    public function getEditDockUnavailability(Request $request)
    {
        $dock_unavailability = Dock_Unavailability::find($request->id);
        $scheduleData = array();        
        if(!empty($dock_unavailability))
        {
            $nestedData['id'] = $dock_unavailability->id;
            $nestedData['po_number'] = $dock_unavailability->po_number;
                $nestedData['supplier_id'] = $dock_unavailability->supplier_id;
                $nestedData['dock_id'] = $dock_unavailability->dock_id;
                $nestedData['dock_name'] = $dock_unavailability->dock_name;
                $nestedData['date_of_delivery'] = date("Y-m-d", strtotime($dock_unavailability->date_of_unavailability));
                $nestedData['recurrent_dateend'] = date("Y-m-d", strtotime($dock_unavailability->recurrent_dateend));
                $nestedData['recurrence'] = $dock_unavailability->recurrence;
                $nestedData['ordering_days'] = $dock_unavailability->ordering_days;

               
                $nestedData['status'] = $dock_unavailability->status == 1 ? "Active" : "Inactive";

                $nestedData['slotting_time_text'] =$dock_unavailability->time;
                $nestedData['slotting_time'] = explode("|",$dock_unavailability->time);    
            $scheduleData = $nestedData;

        }

        return response()->json($scheduleData);
    }

    public function checkIfConflictsDate($dateOfDelivery,$slotting_time){
        $schedules = Schedule::where("date_of_delivery",$dateOfDelivery)->where("status","<>",0)->get();
        $slotting_time = explode("|", $slotting_time);
        $ret = 0;
        foreach($schedules as $schedule){
            
            $get_time = explode("|",$schedule->slotting_time);
            foreach($slotting_time as $time){
                if($time == ""){
                    continue;
                }
                foreach($get_time as $val){
                    if($val == ""){
                        continue;
                    }
                    if($time == $val){
                        $ret = $schedule->id;
                        
                    }
                }
            }
        }

        return $ret;
    }

    public function checkIfConflictsDockUnavailability($dateOfDelivery,$slotting_time){
        $schedules = Dock_Unavailability::where("date_of_delivery",$dateOfDelivery)->where("status","<>",0)->get();
        $slotting_time = explode("|", $slotting_time);
        $ret = 0;
        foreach($schedules as $schedule){
            
            $get_time = explode("|",$schedule->time);
            foreach($slotting_time as $time){
                if($time == ""){
                    continue;
                }
                foreach($get_time as $val){
                    if($val == ""){
                        continue;
                    }
                    if($time == $val){
                        $ret = $schedule->id;
                        
                    }
                }
            }
        }

        return $ret;
    }

    public function checkIfNoShowSchedule(Request $request){
        $schedule = Schedule::where('status','<>',0)->get();
        $data = array();
        foreach($schedule as $value){
            $slotting_ = str_replace("|","",$value->slotting_time);
            $start = substr($slotting_, 0, 5);
            $end = substr($slotting_, -5);
            $date1 = strtotime($value->date_of_delivery . " " . $start);  
            $date2 = strtotime($value->date_of_delivery . " " . $end);  
            $curdate = strtotime(date('Y-m-d H:i:s'));
            $schedule_diff = abs($date2 - $date1);
            $current_diff = abs($curdate - $date2);    

            $nestedData['schedule_diff'] = $schedule_diff;
            $nestedData['current_diff'] =  $current_diff;
            
            $number = 100 - floor(($current_diff / $schedule_diff) * 100);
            $nestedData['number'] = $number;
            $nestedData['isNoShow'] = $number >=  75 ? true : false;
            if($number >= 75 && $number < 100){
                $noshowSched = Schedule::find($value->id);
                $noshowSched->update(['status'=>5]);
            }
            $data[] = $nestedData;
        }

        return $data;
    }

    public function getVoucher($id){

        $schedule = Schedule::find($id);

        $data = array();
        $supplier = Supplier::where('id',$schedule->supplier_id)->first();
        $truck = Truck::where('id',$schedule->truck_id)->first();
        $driver = Driver::where('id',$schedule->driver_id)->first();
        $assistant = Assistant::where('id',$schedule->assistant_id)->first();

        $scheds = trim($schedule->ordering_days);
        $scheds = explode("|", $scheds);
       

        $supplier_name = $supplier->supplier_name;
        $po_number = $schedule->po_number;
        $driver_name = $driver->first_name . " " . $driver->last_name;
        $assistant_name = $assistant->first_name . " " . $assistant->last_name;
        $truck_details = "\n" . $schedule->container_number . "\n" . $truck->plate_number;

        $num = $schedule->id;
        $number = str_pad($num, 8, "0", STR_PAD_LEFT);
        $nestedData['id'] = $number;
        $nestedData['title'] =  $supplier_name . "\n Trucks" . $truck_details . "\n" . $driver_name . "\n" . $assistant_name;

        $nestedData['supplier_id'] = $schedule->supplier_id;
        $nestedData['po_number'] = $schedule->po_number;
        $nestedData['supplier_name'] = $supplier->supplier_name;
        $nestedData['slotting_time'] = $schedule->slotting_time;
        $nestedData['container_no'] = $schedule->container_number;
        $nestedData['driver_name'] = $driver_name;
        $nestedData['truck_details'] =  $truck->brand . " " . $truck->model;
        $nestedData['assistant_name'] = $assistant_name;
        $nestedData['plate_number'] = $truck->plate_number;
        $num = $schedule->id;
        $number = str_pad($num, 8, "0", STR_PAD_LEFT);
        $nestedData['delivery_id'] = $number;
        $nestedData['dock_name'] = $schedule->dock_name;
        $nestedData['date_of_delivery'] = $schedule->date_of_delivery;
        $nestedData['recurrent_dateend'] = $schedule->recurrent_dateend;
        $nestedData['recurrence'] = $schedule->recurrence;

        $slotting_ = str_replace("|","",$schedule->slotting_time);
        $start = substr($slotting_, 0, 5);
        $end = substr($slotting_, -5);

        $slotting = explode("|", $schedule->slotting_time);


        $nestedData['start'] = $schedule->date_of_delivery . "T" .$start .":00";
                $nestedData['end'] = $schedule->date_of_delivery . "T" .$end .":00";
      
        $mat_list = explode("-;-", $schedule->material_list);
   

        $gcas = explode("|", $mat_list[0]);
        $description = explode("|", $mat_list[1]);
        $quantity = explode("|", $mat_list[2]);

        $material_list = "<table class='table table-bordered' style='width:100%'>";
        $material_list .= "<tr>";
        $material_list .= "<th>GCAS</th> <th>Description</th> <th>Quantity</th>";
        $material_list .= "<tr>";
        foreach($gcas as $key => $value){

            $material_list .= "<tr>";
            $material_list .= "<td>". $value ."</td>";
            $material_list .= "<td>". $description[$key] . "</td>";
            $material_list .= "<td>". $quantity[$key] . "</td>";
            $material_list .= "<tr>";

        }
        $material_list .= "</table>";

        $nestedData['material_list'] = $material_list;

        $nestedData['created_at'] = date('j M Y h:i a',strtotime($schedule->created_at));
        switch ($schedule->status) {
            case 1:
                $status = "Active";
                break;
            case 2:
                $status = "Edited";
                break;
            case 3:
                $status = "Finalized";
                break;
            case 4:
                $status = "Edited Finalized";
                break;
            case 5:
                $status = "No-Show";
                break;
            case 6:
                $status = "Emergency Reschedule";
                break;
            
            default:
                $status = "";
                break;
        }
        $nestedData['status'] = $status;


        $data[] = $nestedData;

        return view('schedulers/printvoucher')->with("json_data",$nestedData);
    }

    public function changeToFinalized(Request $request){
            $schedule = Schedule::whereIn("status", [1,2])->get();

            foreach($schedule as $row){
                    $sched = Schedule::find($row->id);
                    $sched->update(["status" => "3"]);
            }

            return json_encode($schedule);
    }

    public function singleEventInsert(Request $request){
        
             //set data
            $gcas = '';
            $description = '';
            $quantity = '';
            $material_list = '';

            foreach($request->gcas as $_gcas){
                $gcas .= $_gcas ."|";
            }

            foreach($request->description as $_description){
                $description .= $_description ."|";
            }

            foreach($request->quantity as $_quantity){
                $quantity .= $_quantity ."|";
            }

            $material_list = $gcas . "-;-" . $description . "-;-" . $quantity; 
            
            $ordering_days='';

            if($request->ordering_days){
                foreach ($request->ordering_days as $ordering_day){
                    $ordering_days .=  $ordering_day.' | ';
                }    
            } 

            $dock_name = '';

            $dock = Dock::where('id',$request->dock_id)->first();
            $dock_name = $dock->dock_name;

            $supplier_id = $request->alt_supplier_id == '' ? $request->supplier_id : $request->alt_supplier_id;
           
            $status = $request->schedule_id != null ? '2' : '1';


            $getSlot = str_replace("|","",$request->slotting_time);
            $start = substr($getSlot, 0, 5);
            $end = substr($getSlot, -5);

            $startDateTime = $request->dateOfDelivery;
            $endDateTime = $request->recurrent_dateend;
            //check if Single Event
            if($request->schedule_id != null){

                $checkIfFinalized = Schedule::find($request->schedule_id);
                $date_yesterday = date('d.m.Y',strtotime("-1 days"));
                $timestamp = $checkIfFinalized->updated_at;
                $timestamp = date('d.m.Y',strtotime($timestamp));
                
                if($timestamp == $date_yesterday){
                    if($checkIfFinalized->status == 3){
                        $checkIfFinalized->update(["status" => 0]);
                        $status = 4;
                        $request->schedule_id = null;
                    }else{
                        $status = 2;
                    }
                }else{
                    $status = 2;
                }
                //$date_created = $schedule_finalized['created_date'];
            }
            $last_insert = Schedule::updateOrCreate(['id' => $request->schedule_id],['po_number' => $request->po_number, 'supplier_id' => $supplier_id, 'dock_id' => $request->dock_id,  'dock_name' => $dock_name,'date_of_delivery' => $request->dateOfDelivery,'recurrent_dateend' => $request->recurrent_dateend, 'recurrence' => $request->recurrence, 'ordering_days' => $ordering_days, 'slotting_time' => $request->slotting_time, 'truck_id' => $request->truck_id, 'container_number' => $request->container_number, 'driver_id' => $request->driver_id, 'assistant_id' => $request->assistant_id, 'status' => $status, 'material_list' => $material_list]);  

            $ret = ['success'=>'Schedule saved successfully.',"id"=>$last_insert->id];

            return json_encode($ret);
            
    }

    public function recurrentEventInsert(Request $request){
        //Recurrent Event insert

        //set data
        $gcas = '';
        $description = '';
        $quantity = '';
        $material_list = '';

        foreach($request->gcas as $_gcas){
            $gcas .= $_gcas ."|";
        }

        foreach($request->description as $_description){
            $description .= $_description ."|";
        }

        foreach($request->quantity as $_quantity){
            $quantity .= $_quantity ."|";
        }

        $material_list = $gcas . "-;-" . $description . "-;-" . $quantity; 
        
        $ordering_days='';

        if($request->ordering_days){
            foreach ($request->ordering_days as $ordering_day){
                $ordering_days .=  $ordering_day.' | ';
            }    
        } 

        $dock_name = '';

        $dock = Dock::where('id',$request->dock_id)->first();
        $dock_name = $dock->dock_name;

        $supplier_id = $request->alt_supplier_id == '' ? $request->supplier_id : $request->alt_supplier_id;
       
        $status = $request->schedule_id != null ? '2' : '1';


        $getSlot = str_replace("|","",$request->slotting_time);
        $start = substr($getSlot, 0, 5);
        $end = substr($getSlot, -5);

        $startDateTime = $request->dateOfDelivery;
        $endDateTime = $request->recurrent_dateend;

        //end set data
        
        $dates = array();
        $recurrent_id = time().'-'.mt_rand();
        $ids_ = array();

        $compare_data = Schedule::where("id",$request->schedule_id)->first();
        if(!empty($compare_data)){
            if($compare_data->date_of_delivery == $request->dateOfDelivery){

                 $sched = Schedule::where("recurrent_id",$compare_data->recurrent_id)->get();
                 foreach($sched as $data){
                    $data->update(['po_number' => $request->po_number, 'supplier_id' => $supplier_id, 'dock_id' => $request->dock_id,  'dock_name' => $dock_name, 'recurrence' => $request->recurrence, 'slotting_time' => $request->slotting_time, 'truck_id' => $request->truck_id, 'container_number' => $request->container_number, 'driver_id' => $request->driver_id, 'assistant_id' => $request->assistant_id, 'status' => 1, 'material_list' => $material_list]);
                 }       
                 // $sched = Schedule::updateOrCreate(['recurrent_id' => $compare_data->recurrent_id],['po_number' => $request->po_number, 'supplier_id' => $supplier_id, 'dock_id' => $request->dock_id,  'dock_name' => $dock_name, 'recurrence' => $request->recurrence, 'slotting_time' => $request->slotting_time, 'truck_id' => $request->truck_id, 'container_number' => $request->container_number, 'driver_id' => $request->driver_id, 'assistant_id' => $request->assistant_id, 'status' => 1, 'material_list' => $material_list]); 

                 $ret = ['success'=>"Schedule successfully saved!","id"=>$request->schedule_id]; 
                return json_encode($ret);
                    
            }else{
                if(!empty($compare_data)){
                    $recurrent_schedule = Schedule::where("recurrent_id",$compare_data->recurrent_id);
                    $recurrent_schedule->update(["status"=>0]);
                    $request->schedule_id = null;
                }
                foreach($request->ordering_days as $sched)
                {
                    $sched = trim($sched);
                    if($sched == ""){
                        continue;
                    }
                    switch ($sched) {
                        case 'Mon':
                        $dates = $this->getDateForSpecificDayBetweenDates($startDateTime,$endDateTime,1);
                            break;
                        case 'Tue':
                        $dates = $this->getDateForSpecificDayBetweenDates($startDateTime,$endDateTime,2);
                            break;
                        case 'Wed':
                        $dates = $this->getDateForSpecificDayBetweenDates($startDateTime,$endDateTime,3);
                            break;
                        case 'Thu':
                        $dates = $this->getDateForSpecificDayBetweenDates($startDateTime,$endDateTime,4);
                            break;
                        case 'Fri':
                        $dates = $this->getDateForSpecificDayBetweenDates($startDateTime,$endDateTime,5);
                            break;
                        case 'Sat':
                        $dates = $this->getDateForSpecificDayBetweenDates($startDateTime,$endDateTime,6);
                            break;
                        case 'Sun':
                        $dates = $this->getDateForSpecificDayBetweenDates($startDateTime,$endDateTime,7);
                            break;
                        
                        default:
                            
                            break;
                    }

                    foreach($dates as $date) {
                        //check if has conflict on date
                        $hasConflict = $this->checkIfConflictsDate($date,$request->slotting_time);

                        if($hasConflict > 0){
                            
                            // CONFLICTS ENTRY
                            

                            $sched = Schedule::updateOrCreate(['id' => $request->schedule_id],['po_number' => $request->po_number, 'supplier_id' => $supplier_id, 'dock_id' => $request->dock_id,  'dock_name' => $dock_name,'date_of_delivery' => $date,'recurrent_dateend' => $request->recurrent_dateend, 'recurrence' => $request->recurrence, 'ordering_days' => $ordering_days, 'slotting_time' => $request->slotting_time, 'truck_id' => $request->truck_id, 'container_number' => $request->container_number, 'driver_id' => $request->driver_id, 'assistant_id' => $request->assistant_id, 'status' => 10, 'material_list' => $material_list,'recurrent_id' => $recurrent_id,'conflict_id'=>$hasConflict]); 
                            



                        }else{
                            //CONTINUE NORMAL ENTRY
                            if($request->schedule_id != null){

                                $checkIfFinalized = Schedule::find($request->schedule_id);
                                $date_yesterday = date('d.m.Y',strtotime("-1 days"));
                                $timestamp = $checkIfFinalized->updated_at;
                                $timestamp = date('d.m.Y',strtotime($timestamp));
                                
                                if($timestamp == $date_yesterday){
                                    if($checkIfFinalized->status == 3){
                                        $checkIfFinalized->update(["status" => 0]);
                                        $status = 4;
                                        $request->schedule_id = null;
                                    }else{
                                        $status = 2;
                                    }
                                }else{
                                    $status = 2;
                                }
                            }
                            
                            

                            $last_inserted_id = Schedule::updateOrCreate(['id' => $request->schedule_id],['po_number' => $request->po_number, 'supplier_id' => $supplier_id, 'dock_id' => $request->dock_id,  'dock_name' => $dock_name,'date_of_delivery' => $date,'recurrent_dateend' => $request->recurrent_dateend, 'recurrence' => $request->recurrence, 'ordering_days' => $ordering_days, 'slotting_time' => $request->slotting_time, 'truck_id' => $request->truck_id, 'container_number' => $request->container_number, 'driver_id' => $request->driver_id, 'assistant_id' => $request->assistant_id, 'status' => $status, 'material_list' => $material_list,'recurrent_id' => $recurrent_id]);
                            $nestedData['id'] = $last_inserted_id->id;  
                            $ids_[] = $nestedData;
                            
                           
                        }
                    }

                }
            }
        }
        else{
            if(!empty($compare_data)){
                $recurrent_schedule = Schedule::where("recurrent_id",$compare_data->recurrent_id);
                $recurrent_schedule->update(["status"=>0]);
            }
            foreach($request->ordering_days as $sched)
            {
                $sched = trim($sched);
                if($sched == ""){
                    continue;
                }
                switch ($sched) {
                    case 'Mon':
                    $dates = $this->getDateForSpecificDayBetweenDates($startDateTime,$endDateTime,1);
                        break;
                    case 'Tue':
                    $dates = $this->getDateForSpecificDayBetweenDates($startDateTime,$endDateTime,2);
                        break;
                    case 'Wed':
                    $dates = $this->getDateForSpecificDayBetweenDates($startDateTime,$endDateTime,3);
                        break;
                    case 'Thu':
                    $dates = $this->getDateForSpecificDayBetweenDates($startDateTime,$endDateTime,4);
                        break;
                    case 'Fri':
                    $dates = $this->getDateForSpecificDayBetweenDates($startDateTime,$endDateTime,5);
                        break;
                    case 'Sat':
                    $dates = $this->getDateForSpecificDayBetweenDates($startDateTime,$endDateTime,6);
                        break;
                    case 'Sun':
                    $dates = $this->getDateForSpecificDayBetweenDates($startDateTime,$endDateTime,7);
                        break;
                    
                    default:
                        
                        break;
                }

                foreach($dates as $date) {
                    //check if has conflict on date
                    $hasConflict = $this->checkIfConflictsDate($date,$request->slotting_time);

                    if($hasConflict > 0){
                        
                        // CONFLICTS ENTRY
                        

                        $sched = Schedule::updateOrCreate(['id' => $request->schedule_id],['po_number' => $request->po_number, 'supplier_id' => $supplier_id, 'dock_id' => $request->dock_id,  'dock_name' => $dock_name,'date_of_delivery' => $date,'recurrent_dateend' => $request->recurrent_dateend, 'recurrence' => $request->recurrence, 'ordering_days' => $ordering_days, 'slotting_time' => $request->slotting_time, 'truck_id' => $request->truck_id, 'container_number' => $request->container_number, 'driver_id' => $request->driver_id, 'assistant_id' => $request->assistant_id, 'status' => 10, 'material_list' => $material_list,'recurrent_id' => $recurrent_id,'conflict_id'=>$hasConflict]); 
                        



                    }else{
                        //CONTINUE NORMAL ENTRY
                        if($request->schedule_id != null){

                            $checkIfFinalized = Schedule::find($request->schedule_id);
                            $date_yesterday = date('d.m.Y',strtotime("-1 days"));
                            $timestamp = $checkIfFinalized->updated_at;
                            $timestamp = date('d.m.Y',strtotime($timestamp));
                            
                            if($timestamp == $date_yesterday){
                                if($checkIfFinalized->status == 3){
                                    $checkIfFinalized->update(["status" => 0]);
                                    $status = 4;
                                    $request->schedule_id = null;
                                }else{
                                    $status = 2;
                                }
                            }else{
                                $status = 2;
                            }
                        }
                        
                        

                        $last_inserted_id = Schedule::updateOrCreate(['id' => $request->schedule_id],['po_number' => $request->po_number, 'supplier_id' => $supplier_id, 'dock_id' => $request->dock_id,  'dock_name' => $dock_name,'date_of_delivery' => $date,'recurrent_dateend' => $request->recurrent_dateend, 'recurrence' => $request->recurrence, 'ordering_days' => $ordering_days, 'slotting_time' => $request->slotting_time, 'truck_id' => $request->truck_id, 'container_number' => $request->container_number, 'driver_id' => $request->driver_id, 'assistant_id' => $request->assistant_id, 'status' => $status, 'material_list' => $material_list,'recurrent_id' => $recurrent_id]);
                        $nestedData['id'] = $last_inserted_id->id;  
                        $ids_[] = $nestedData;
                        
                       
                    }
                }

            }
        }

        
        
        $return_schedule = Schedule::where("recurrent_id",$recurrent_id)->where("status",10)->get();

        $data=array();
        if(!empty($return_schedule)){
            foreach ($return_schedule as $schedule)
            {
                
                $supplier = Supplier::where('id',$schedule->supplier_id)->first();
                $scheds = trim($schedule->ordering_days);
                $scheds = explode("|", $scheds);
                
                $slotting_ = str_replace("|","",$schedule->slotting_time);
                $start = substr($slotting_, 0, 5);
                $end = substr($slotting_, -5);

                $supplier_name = $supplier->supplier_name;
                $po_number = $schedule->po_number;
               

                $nestedData['id'] = $schedule->id;
                $nestedData['supplier_id'] = $schedule->supplier_id;
                $nestedData['conflict_id'] = $schedule->conflict_id;
                $nestedData['po_number'] = $schedule->po_number;
                $nestedData['supplier_name'] = $supplier->supplier_name;
                $nestedData['slotting_time'] = $start . " - " . $end;
                $num = $schedule->id;
                $number = str_pad($num, 8, "0", STR_PAD_LEFT);
                $nestedData['delivery_id'] = $number;
                $nestedData['dock_name'] = $schedule->dock_name;
                $nestedData['date_of_delivery'] = $schedule->date_of_delivery;

                $nestedData['status'] = $status;


                $data[] = $nestedData;

            }
            $ret = ["conflict"=>$data];
        }
       
        if(count($return_schedule) < 1) {

            $ret = ['success'=>"Schedule saved successfull","data"=>$ids_]; 
        }
        

        return json_encode($ret);
    }

    public function recurrentEventInsertInitial(Request $request){
        //Recurrent Event insert

        //set data
        $gcas = '';
        $description = '';
        $quantity = '';
        $material_list = '';

        foreach($request->gcas as $_gcas){
            $gcas .= $_gcas ."|";
        }

        foreach($request->description as $_description){
            $description .= $_description ."|";
        }

        foreach($request->quantity as $_quantity){
            $quantity .= $_quantity ."|";
        }

        $material_list = $gcas . "-;-" . $description . "-;-" . $quantity; 
        
        $ordering_days='';

        if($request->ordering_days){
            foreach ($request->ordering_days as $ordering_day){
                $ordering_days .=  $ordering_day.' | ';
            }    
        } 

        $dock_name = '';

        $dock = Dock::where('id',$request->dock_id)->first();
        $dock_name = $dock->dock_name;

        $supplier_id = $request->alt_supplier_id == '' ? $request->supplier_id : $request->alt_supplier_id;
       
        $status = $request->schedule_id != null ? '2' : '1';


        $getSlot = str_replace("|","",$request->slotting_time);
        $start = substr($getSlot, 0, 5);
        $end = substr($getSlot, -5);

        $startDateTime = $request->dateOfDelivery;
        $endDateTime = $request->recurrent_dateend;

        //end set data
        
        $dates = array();
        $recurrent_id = time().'-'.mt_rand();
        $ids_ = array();
        foreach($request->ordering_days as $sched)
        {
            $sched = trim($sched);
            if($sched == ""){
                continue;
            }
            switch ($sched) {
                case 'Mon':
                $dates = $this->getDateForSpecificDayBetweenDates($startDateTime,$endDateTime,1);
                    break;
                case 'Tue':
                $dates = $this->getDateForSpecificDayBetweenDates($startDateTime,$endDateTime,2);
                    break;
                case 'Wed':
                $dates = $this->getDateForSpecificDayBetweenDates($startDateTime,$endDateTime,3);
                    break;
                case 'Thu':
                $dates = $this->getDateForSpecificDayBetweenDates($startDateTime,$endDateTime,4);
                    break;
                case 'Fri':
                $dates = $this->getDateForSpecificDayBetweenDates($startDateTime,$endDateTime,5);
                    break;
                case 'Sat':
                $dates = $this->getDateForSpecificDayBetweenDates($startDateTime,$endDateTime,6);
                    break;
                case 'Sun':
                $dates = $this->getDateForSpecificDayBetweenDates($startDateTime,$endDateTime,7);
                    break;
                
                default:
                    
                    break;
            }

            foreach($dates as $date) {
                //check if has conflict on date
                $hasConflict = $this->checkIfConflictsDate($date,$request->slotting_time);

                if($hasConflict > 0){
                    
                    // CONFLICTS ENTRY
                    

                    $sched = Schedule::updateOrCreate(['id' => $request->schedule_id],['po_number' => $request->po_number, 'supplier_id' => $supplier_id, 'dock_id' => $request->dock_id,  'dock_name' => $dock_name,'date_of_delivery' => $date,'recurrent_dateend' => $request->recurrent_dateend, 'recurrence' => $request->recurrence, 'ordering_days' => $ordering_days, 'slotting_time' => $request->slotting_time, 'truck_id' => $request->truck_id, 'container_number' => $request->container_number, 'driver_id' => $request->driver_id, 'assistant_id' => $request->assistant_id, 'status' => 10, 'material_list' => $material_list,'recurrent_id' => $recurrent_id,'conflict_id'=>$hasConflict]); 
                    



                }else{
                    //CONTINUE NORMAL ENTRY
                    if($request->schedule_id != null){

                        $checkIfFinalized = Schedule::find($request->schedule_id);
                        $date_yesterday = date('d.m.Y',strtotime("-1 days"));
                        $timestamp = $checkIfFinalized->updated_at;
                        $timestamp = date('d.m.Y',strtotime($timestamp));
                        
                        if($timestamp == $date_yesterday){
                            if($checkIfFinalized->status == 3){
                                $checkIfFinalized->update(["status" => 0]);
                                $status = 4;
                                $request->schedule_id = null;
                            }else{
                                $status = 2;
                            }
                        }else{
                            $status = 2;
                        }
                    }
                    
                    

                    $last_inserted_id = Schedule::updateOrCreate(['id' => $request->schedule_id],['po_number' => $request->po_number, 'supplier_id' => $supplier_id, 'dock_id' => $request->dock_id,  'dock_name' => $dock_name,'date_of_delivery' => $date,'recurrent_dateend' => $request->recurrent_dateend, 'recurrence' => $request->recurrence, 'ordering_days' => $ordering_days, 'slotting_time' => $request->slotting_time, 'truck_id' => $request->truck_id, 'container_number' => $request->container_number, 'driver_id' => $request->driver_id, 'assistant_id' => $request->assistant_id, 'status' => $status, 'material_list' => $material_list,'recurrent_id' => $recurrent_id]);
                    $nestedData['id'] = $last_inserted_id->id;  
                    $ids_[] = $nestedData;
                    
                   
                }
            }

        }
        
        $return_schedule = Schedule::where("recurrent_id",$recurrent_id)->where("status",10)->get();

        $data=array();
        if(!empty($return_schedule)){
            foreach ($return_schedule as $schedule)
            {
                
                $supplier = Supplier::where('id',$schedule->supplier_id)->first();
                $scheds = trim($schedule->ordering_days);
                $scheds = explode("|", $scheds);
                
                $slotting_ = str_replace("|","",$schedule->slotting_time);
                $start = substr($slotting_, 0, 5);
                $end = substr($slotting_, -5);

                $supplier_name = $supplier->supplier_name;
                $po_number = $schedule->po_number;
               

                $nestedData['id'] = $schedule->id;
                $nestedData['supplier_id'] = $schedule->supplier_id;
                $nestedData['conflict_id'] = $schedule->conflict_id;
                $nestedData['po_number'] = $schedule->po_number;
                $nestedData['supplier_name'] = $supplier->supplier_name;
                $nestedData['slotting_time'] = $start . " - " . $end;
                $num = $schedule->id;
                $number = str_pad($num, 8, "0", STR_PAD_LEFT);
                $nestedData['delivery_id'] = $number;
                $nestedData['dock_name'] = $schedule->dock_name;
                $nestedData['date_of_delivery'] = $schedule->date_of_delivery;

                $nestedData['status'] = $status;


                $data[] = $nestedData;

            }
            $ret = ["conflict"=>$data];
        }
       
        if(count($return_schedule) < 1) {

            $ret = ['success'=>"Schedule saved successfull","data"=>$ids_]; 
        }
        

        return json_encode($ret);
    }
}
