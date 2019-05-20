<?php

namespace App\Http\Controllers;

use App\Schedule;
use App\Dock;
use App\Supplier;
use App\Truck;
use App\Driver;
use App\Assistant;
use App\Dock_Unavailability;
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

     
    
        $schedules =  Schedule::where("status","<>","0")->get();        
        $unavailabilities = Dock_Unavailability::where('status','<>','0')->get();    

        $search = $request->input('module');
        $status = "";
        $slotting = array();
        $data = array();
        $dow = array();
        if(!empty($schedules))
        {
            foreach ($schedules as $schedule)
            {

                $docks =    Dock::where('module','LIKE',"%{$search}%")->first();

                $hasScheduleModule = Schedule::where('dock_id',$docks['id'])->count();

                $supplier = Supplier::where('id',$schedule->supplier_id)->first();
                $truck = Truck::where('id',$schedule->truck_id)->first();
                $driver = Driver::where('id',$schedule->driver_id)->first();
                $assistant = Assistant::where('id',$schedule->assistant_id)->first();
                if($hasScheduleModule == 0 || $docks['id'] != $schedule->dock_id){
                    continue;
                }else{
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
                    $nestedData['title'] =  $supplier_name . "\n Trucks" . $truck_details . "\n" . $driver_name . "\n" . $assistant_name;


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
                            $status = "Archived";
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


                    $data[] = $nestedData;
                    $dow = array();

                }

            }
        }

        if(!empty($unavailabilities))
        {
            foreach ($unavailabilities as $unavailability)
            {

                $docks = Dock::where('module','LIKE',"%{$search}%")->first();

                $hasScheduleModule = Dock_Unavailability::where('dock_id',$docks['id'])->count();

                if($hasScheduleModule == 0 || $docks['id'] != $unavailability->dock_id){
                    continue;
                }else{

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

                    $_Data['start'] = $unavailability->date_of_unavailability . "T" .$start .":00";
                    $_Data['end'] = $unavailability->date_of_unavailability . "T" .$end .":00";
                    $_Data['created_at'] = date('j M Y h:i a',strtotime($unavailability->created_at));
                    switch ($schedule->status) {
                        case 1:
                            $status = "Active";
                            break;
                        case 2:
                            $status = "Edited";
                            break;
                        case 3:
                            $status = "Archived";
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
                    $_Data['backgroundColor'] = "#ff7f7f";

                    array_push($data, $_Data);
                }

            }
        }
            
        echo json_encode($data); 
    }

    public function getSupplierData(Request $request){

        $trucks = Truck::where("status","1")->get();
        $drivers = Driver::where("status","1")->where('isApproved','1')->get();
        $assistants = Assistant::where("status","1")->where('isApproved','1')->get();
        
        $trucks_suppliers = '';
        $drivers_suppliers = '';
        $assistants_suppliers = '';

        $truckdata = array();
        $driverdata = array();
        $assistantdata = array();
        

        if(!empty($trucks))
        {
            foreach ($trucks as $truck)
            {
              
                $supplier_ids = explode('|',$truck->supplier_ids);
                
                foreach($supplier_ids as $supplier_id){
                    if($supplier_id == null){
                        continue;
                    }
                    $suppliers = Supplier::where('id',$supplier_id)->where('status', 1)->first();
                    if($suppliers == null){
                        continue;
                    }
                    $trucks_suppliers .= $suppliers->supplier_name . " | ";
                }

                $nestedData['id'] = $truck->id;
                $nestedData['supplier_ids'] =  $trucks_suppliers;
                $nestedData['plate_number'] = $truck->plate_number;
                $nestedData['brand'] = $truck->brand;
                $nestedData['model'] = $truck->model;
                $nestedData['type'] = $truck->type;
               
                $nestedData['status'] = $truck->status == 1 ? "Active" : "Inactive";
                $truckdata[] = $nestedData;
                $trucks_suppliers = '';
            }
        }

        if(!empty($drivers))
        {
            foreach ($drivers as $driver)
            {
                $nestedData['id'] = $driver->id;
                $nestedData['first_name'] = $driver->first_name;
                $nestedData['last_name'] = $driver->last_name;
                $nestedData['status'] = $driver->status == 1 ? "Active" : "Inactive";
                $driverdata[] = $nestedData;
            }
        }

        if(!empty($assistants))
        {
            foreach ($assistants as $assistant)
            {
                $nestedData['id'] = $assistant->id;
                $nestedData['first_name'] = $assistant->first_name;
                $nestedData['last_name'] = $assistant->last_name;
                $nestedData['status'] = $assistant->status == 1 ? "Active" : "Inactive";
                $assistantdata[] = $nestedData;
            }
        }


        $json_data = array(
                    "assistantdata"    => $assistantdata,  
                    "driverdata"       => $driverdata, 
                    "truckdata"        => $truckdata   
                    );

        echo json_encode($json_data); 
    }

    public function getSlottingTime(Request $request){

        if($request->isForUnavailability == "0"){
            $slotting = Schedule::where("date_of_delivery",$request->date_of_delivery)->where('status','<>','0')->get();
        }elseif($request->isForUnavailability == "1") {
            $slotting = Dock_Unavailability::where("date_of_unavailability",$request->date_of_unavailability)->where('status','1')->get();
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


        if($request->isForUnavailability == "0"){


            if($request->isEditingRecurrent == "1"){
                $del = Schedule::where('po_number', $request->po_number);
                $del->delete();
            }

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


            if($request->recurrence == 'Recurrent'){

                $getSlot = str_replace("|","",$request->slotting_time);
                $start = substr($getSlot, 0, 5);
                $end = substr($getSlot, -5);

                $startDateTime = $request->dateOfDelivery;
                $endDateTime = date("Y-m-d", strtotime(date("Y-m-d", strtotime($request->dateOfDelivery)) . " + 100 day"));

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

                    foreach($dates as $date){
                        Schedule::updateOrCreate(['id' => $request->schedule_id],['po_number' => $request->po_number, 'supplier_id' => $supplier_id, 'dock_id' => $request->dock_id,  'dock_name' => $dock_name,'date_of_delivery' => $date, 'recurrence' => $request->recurrence, 'ordering_days' => $ordering_days, 'slotting_time' => $request->slotting_time, 'truck_id' => $request->truck_id, 'container_number' => $request->container_number, 'driver_id' => $request->driver_id, 'assistant_id' => $request->assistant_id, 'status' => $status, 'material_list' => $material_list]);  
                    }

                }
                

                $ret = ['success'=>'Schedule saved successfully.']; 
            }else{
                $ret = ['success'=>'Schedule saved successfully.'];
                Schedule::updateOrCreate(['id' => $request->schedule_id],['po_number' => $request->po_number, 'supplier_id' => $supplier_id, 'dock_id' => $request->dock_id,  'dock_name' => $dock_name,'date_of_delivery' => $request->dateOfDelivery, 'recurrence' => $request->recurrence, 'ordering_days' => $ordering_days, 'slotting_time' => $request->slotting_time, 'truck_id' => $request->truck_id, 'container_number' => $request->container_number, 'driver_id' => $request->driver_id, 'assistant_id' => $request->assistant_id, 'status' => $status, 'material_list' => $material_list]);  
            }



                 
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
                $endDateTime = date("Y-m-d", strtotime(date("Y-m-d", strtotime($request->dateOfUnavailability)) . " + 100 day"));

                // $scheds = trim($ordering_days);
                // $scheds = explode("|", $scheds);
                
                $dates = array();

                foreach($request->ordering_days_unavailability as $sched){
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

                    foreach($dates as $date){
                        Dock_Unavailability::updateOrCreate(['id' => $request->unavailability_id],['dock_id' => $request->dock_id_unavailability,  'dock_name' => $dock_name,'date_of_unavailability' => $date, 'recurrence' => $request->recurrence_unavailability, 'ordering_days' => $ordering_days, 'time' => $request->slotting_time_unavailability, 'emergency' => "", 'reason' => $request->reason_unavailability, 'status' => $status]);   
                    }

                }
                

                $ret = ['success'=>'Schedule saved successfully.']; 
            }else{
                $ret = ['success'=>'Schedule saved successfully.'];
                Dock_Unavailability::updateOrCreate(['id' => $request->unavailability_id],
                ['dock_id' => $request->dock_id_unavailability,  'dock_name' => $dock_name,'date_of_unavailability' => $request->dateOfUnavailability, 'recurrence' => $request->recurrence_unavailability, 'ordering_days' => $ordering_days, 'time' => $request->slotting_time_unavailability, 'emergency' => "", 'reason' => $request->reason_unavailability, 'status' => $status]); 
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
    public function edit($id)
    {
        $schedule = Schedule::find($id);
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
        Schedule::find($request->id)->update(['reason'=> $request->reason,'status' => 0]);
        return response()->json(['success'=>'Supplier deactivated successfully.']);
    }

    public function fetchIncompleteMaterials()    {
        $incompleteMaterialList = Schedule::where('material_list','=',null)->where('status','<>','0')->get();
        return response()->json($incompleteMaterialList);
    }

    public function getDateForSpecificDayBetweenDates($startDate,$endDate,$day_number){
    $endDate = strtotime($endDate);
    $days=array('1'=>'Monday','2' => 'Tuesday','3' => 'Wednesday','4'=>'Thursday','5' =>'Friday','6' => 'Saturday','7'=>'Sunday');
    for($i = strtotime($days[$day_number], strtotime($startDate)); $i <= $endDate; $i = strtotime('+1 week', $i))
        $date_array[]=date('Y-m-d',$i);

        return $date_array;
     }
}
