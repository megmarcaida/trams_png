<?php

namespace App\Http\Controllers;

use App\Schedule;
use App\Dock;
use App\Supplier;
use App\Truck;
use App\Driver;
use App\Assistant;
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

     
    
        $schedules =  Schedule::all();        
        $search = $request->input('module');
        $status = "";
        $slotting = array();
        $data = array();
        if(!empty($schedules))
        {
            foreach ($schedules as $schedule)
            {

                $docks =    Dock::where('module','LIKE',"%{$search}%")->count();
                if($docks == 0){
                    continue;
                }else{


                    $nestedData['id'] = $schedule->id;
                    $nestedData['title'] = $request->input('module');
                    $nestedData['supplier_id'] = $schedule->supplier_id;
                    $nestedData['dock_id'] = $schedule->dock_id;


                    $nestedData['dock_name'] = $schedule->dock_name;
                    $nestedData['date_of_delivery'] = $schedule->date_of_delivery;
                    $nestedData['recurrence'] = $schedule->recurrence;

                    $slotting_ = str_replace("|","",$schedule->slotting_time);
                    $start = substr($slotting_, 0, 5);
                    $end = substr($slotting_, -5);

                    $slotting = explode("|", $schedule->slotting_time);

                    $nestedData['start'] = $schedule->date_of_delivery . "T" .$start .":00";
                    $nestedData['end'] = $schedule->date_of_delivery . "T" .$end .":00";
                    $nestedData['material_list'] = $schedule->material_list;
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
                }
                $data[] = $nestedData;

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

        $slotting = Schedule::where("date_of_delivery",$request->date_of_delivery)->get();
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


                $nestedData['slotting_time'] = explode("|",$slot->slotting_time);
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

        $ordering_days='';

        if($request->ordering_days){
            foreach ($request->ordering_days as $ordering_day){
                $ordering_days .=  $ordering_day.' | ';
            }    
        } 

        $dock_name = '';

        $dock = Dock::where('id',$request->dock_id)->first();
        $dock_name = $dock->dock_name;

        $isExistPoNumber = Schedule::where("po_number",$request->po_number)->first();

        $isExist = Schedule::find($request->schedule_id);

        if($isExistPoNumber && !$isExist){
            $ret = ['error'=>'PO Number already exists.'];
        }else{
            $ret = ['success'=>'Schedule saved successfully.'];
            Schedule::updateOrCreate(['id' => $request->schedule_id],
                ['po_number' => $request->po_number, 'supplier_id' => $request->supplier_id, 'dock_id' => $request->dock_id,  'dock_name' => $dock_name,'date_of_delivery' => $request->dateOfDelivery, 'recurrence' => $request->recurrence, 'ordering_days' => $ordering_days, 'slotting_time' => $request->slotting_time, 'truck_id' => $request->truck_id, 'container_number' => $request->container_number, 'driver_id' => $request->driver_id, 'assistant_id' => $request->assistant_id]);        
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
        return response()->json($schedule);
    }
  
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy($id,$status)
    {
        //Supplier::find($id)->delete();
        Supplier::find($id)->update(['status' => 0]);
        return response()->json(['success'=>'Supplier deactivated successfully.']);
    }
}
