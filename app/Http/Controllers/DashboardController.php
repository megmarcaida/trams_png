<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Schedule;
use App\Supplier;
use App\Truck;
use App\Dock;
use App\Role;
use App\Parking;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $role_id = Auth::user()->role_id;
        if($role_id == "1"){
            $date = Carbon::now();
            $datenow = $date->format("M d, Y"); 
            return view('dashboard/general')->with('datenow',$datenow);
        }else if($role_id != "1" && $role_id != "3"){

            $role_account = Role::where('id',$role_id)->first();
            $docks = array();
            $pcc = array("title" => "PCC","slug" => "pcc","details" => array("PCC 1","PCC 2"));
            $babycare = array("title" => "Baby Care","slug" => "baby-care","details" => array("Baby Care 1","Baby Care 2","Baby Care 3","Baby Care Scrap"));
            $femcare = array("title" => "Fem Care","slug" => "fem-care","details" => array("Fem Care"));
            $liquids = array("title" => "Liquids", "slug" => "liquids", "details" => array("Liquids","Liquids Out Canopy"));
            $laundry = array("title" => "Laundry", "slug" => "laundry", "details" => array("Laundry","Laundry SB","Laundry Scrap"));

            $role_account_desc = $role_account['description'];
            switch ($role_account_desc) {
                case 'North':
                    array_push($docks, $pcc);
                    array_push($docks, $babycare);
                    break;
                case 'South':
                    array_push($docks, $liquids);
                    array_push($docks, $laundry);
                    array_push($docks, $femcare);
                    break;
                
                default:
                    $docks =  array(
                                array(
                                    "title" => "PCC", 
                                    "slug" => "pcc",
                                    "details" => array("PCC1","PCC2")
                                ),
                                array(
                                    "title" => "Baby Care", 
                                    "slug" => "baby-care", 
                                    "details" => array("Baby Care 1","Baby Care 2","Baby Care 3","Baby Care Scrap")
                                ),
                                array(
                                    "title" => "Fem Care", 
                                    "slug" => "fem-care", 
                                    "details" => array("Fem Care")
                                ),
                                array(
                                    "title" => "Laundry",  
                                    "slug" => "laundry",
                                    "details" => array("Laundry","Laundry SB","Laundry Scrap")
                                ),
                                array(
                                    "title" => "Liquids", 
                                    "slug" => "liquids", 
                                    "details" => array("Liquids","Liquids Out Canopy")
                                )
                            );
                    break;
            }
            

            $date = Carbon::now();
            $datenow = $date->format("M d, Y"); 
            return view('dashboard/dock')->with(['datenow'=>$datenow, 'docks'=>$docks, 'role_account'=>$role_account_desc]);
        }else if($role_id == "3"){
            $date = Carbon::now();
            $datenow = $date->format("M d, Y");
            return view('dashboard/security')->with(['datenow'=>$datenow]);
        }else{

            return view('home');
        }

    }

    public function allGeneralSchedule(Request $request){
        $columns = array( 
                            0 =>'id', 
                            1 =>'slotting_time',
                            2 => 'supplier_name',
                            3 => 'truck',
                            4 => 'plate_number',
                            5 => 'container_number',
                            6 => 'dock',
                            7 => 'status'
                        );
    
        
        $date = Carbon::now();
        $datenow = $date->format("Y-m-d"); 

        $dock = Dock::where('status', 1)->get();
        $dock_id_ = 0;
        $totalData = Schedule::where("date_of_delivery", $datenow)->count();
            
        $totalFiltered = $totalData; 

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        if($order == "id"){
            $order = "slotting_time";
        }
        $dir = $request->input('order.0.dir');
        if(empty($request->input('search.value')))
        {   
            if($request->module != null){
                $module_name = $request->module;
                foreach($dock as $d){
                    if(strpos($d->module, $module_name) !== false){
                        $dock_id_ = $d->id;
                    }
                }  
                $Schedules = Schedule::where("date_of_delivery", $datenow)->where("dock_id",$dock_id_)
                         ->offset($start)
                         ->limit($limit)
                         ->orderBy($order,$dir)
                         ->get();
            }else{
                $Schedules = Schedule::where("date_of_delivery", $datenow)
                         ->offset($start)
                         ->limit($limit)
                         ->orderBy($order,$dir)
                         ->get();    
            }
            
        }
        else {
            $search = $request->input('search.value'); 
            
            $Schedules =  Schedule::where('id','LIKE',"%{$search}%")->OrWhere("date_of_delivery", $datenow)->offset($start)
                 ->limit($limit)
                 ->orderBy($order,$dir)
                 ->get();



            $totalFiltered = Schedule::where('id','LIKE',"%{$search}%")->OrWhere("date_of_delivery", $datenow)->count();
        }

        $data = array();
        $trucks_suppliers='';
        $trucks = '';

        if(!empty($Schedules))
        {
            foreach ($Schedules as $Schedule)
            {
               
                
                $slotting_ = str_replace("|","",$Schedule->slotting_time);
                    $start = substr($slotting_, 0, 5);
                    $end = substr($slotting_, -5);

                $suppliers = Supplier::where('id',$Schedule->supplier_id)->where('status', 1)->first();
               
                $truck = Truck::where('id',$Schedule->truck_id)->where('status', 1)->first();

                $num = $Schedule->id;
                $number = str_pad($num, 8, "0", STR_PAD_LEFT);

                $nestedData['id'] = $number;
                $nestedData['slotting_time'] = $start . " " . $end;
                $nestedData['supplier_name'] = $suppliers['supplier_name'];
                $nestedData['truck'] = $truck['brand'] . " " . $truck['model'];
                $nestedData['plate_number'] = $truck['plate_number'];
                $nestedData['container_number'] = $Schedule->container_number;
                $nestedData['dock'] = $Schedule->dock_name;
                
                switch ($Schedule->status) {
                    case 7:
                        $nestedData['status'] = "Completed";
                        break;
                    case 8:
                        $dateofdockin = $Schedule->date_of_delivery . " " . $start;
                        if(time() - strtotime($dateofdockin) < 1){
                            $nestedData['status'] = "Parking";
                        }elseif(time() - strtotime($dateofdockin) > 0){
                            $nestedData['status'] = "Delayed";
                        }
                        break;
                    
                    case 9:
                        $dateofdockout = $Schedule->date_of_delivery . " " . $end;
                        if(time() - strtotime($dateofdockout) < 1){
                            $nestedData['status'] = "Dock";
                        }elseif(time() - strtotime($dateofdockout) > 0){
                            $nestedData['status'] = "Overtime";
                        }
                        break;

                    case 10:
                        $dateofentry = $Schedule->date_of_delivery . " " . $start;
                        if(time() - strtotime($dateofentry) > 0){
                            $nestedData['status'] = "Delayed";
                        }elseif(time() - strtotime($dateofentry) < 0){
                            $nestedData['status'] = "For-Entry";
                        }
                        break;
                    case 11:
                        $dateofgateout = $Schedule->date_of_delivery . " " . $end;
                        if(time() - strtotime($dateofgateout) < 1){
                            $nestedData['status'] = "For-Gate-Out";
                        }elseif(time() - strtotime($dateofgateout) > 0){

                        $nestedData['status'] = "Over Staying";
                        }
                        break;   
                        
                    default:
                        $nestedData['status'] = "";
                        break;
                }
                $data[] = $nestedData;

            }
        }
          
        $json_data = array(
                    "draw"            => intval($request->input('draw')),  
                    "recordsTotal"    => intval($totalData),  
                    "recordsFiltered" => intval($totalFiltered), 
                    "data"            => $data   
                    );
            
        echo json_encode($json_data); 
    }

    public function allDockSchedule(Request $request){
        $columns = array( 
                            0 =>'id', 
                            1 =>'slotting_time',
                            2 => 'supplier_name',
                            3 => 'truck',
                            4 => 'plate_number',
                            5 => 'container_number',
                            6 => 'dock',
                        );
    
        
        $date = Carbon::now();
        $datenow = $date->format("Y-m-d"); 
        $role_id = Auth::user()->role_id;
        $roles = Role::where('id',$role_id)->first();
        $docks = array();
        switch ($roles['description']) {
                case 'North':
                        $docks = array(1,2);
                    break;
                case 'South':
                        $docks = array(3,4,5);
                    break;
                
                default:
                    
                    break;
            }


        $dock = Dock::where('status', 1)->get();
        $dock_id_ = 0;
        $totalData = Schedule::where("date_of_delivery", $datenow)->whereIn("dock_id",$docks)->where("process_status",$request->process_status)->count();
            
        $totalFiltered = $totalData; 

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        if($order == "id"){
            $order = "slotting_time";
        }
        $dir = $request->input('order.0.dir');
        if(empty($request->input('search.value')))
        {   
            
               
            $Schedules = Schedule::where("date_of_delivery", $datenow)->whereIn("dock_id",$docks)->where("process_status",$request->process_status)
                     ->offset($start)
                     ->limit($limit)
                     ->orderBy($order,$dir)
                     ->get();
            
            
        }
        else {
            $search = $request->input('search.value'); 
            
            $Schedules =  Schedule::where('id','LIKE',"%{$search}%")->OrWhere("date_of_delivery", $datenow)->whereIn ("dock_id",$docks)->where("process_status",$request->process_status)->offset($start)
                 ->limit($limit)
                 ->orderBy($order,$dir)
                 ->get();



            $totalFiltered = Schedule::where('id','LIKE',"%{$search}%")->OrWhere("date_of_delivery", $datenow)->whereIn ("dock_id",$docks)->where("process_status",$request->process_status)->count();
        }

        $data = array();
        $trucks_suppliers='';
        $trucks = '';
        $material_list = array();
        $mats_ = "";
        if(!empty($Schedules))
        {
            foreach ($Schedules as $Schedule)
            {
               
                    
                $slotting_ = str_replace("|","",$Schedule->slotting_time);
                    $start = substr($slotting_, 0, 5);
                    $end = substr($slotting_, -5);

                $dateofdeparture = $Schedule->date_of_delivery . " " . $start;
                if(time() - strtotime($dateofdeparture) > 43188){
                    continue;
                }

                $suppliers = Supplier::where('id',$Schedule->supplier_id)->where('status', 1)->first();
               
                $truck = Truck::where('id',$Schedule->truck_id)->where('status', 1)->first();

                $num = $Schedule->id;
                $number = str_pad($num, 8, "0", STR_PAD_LEFT);

                $nestedData['id'] = $number;
                $nestedData['slotting_time'] = $start . " - " . $end;
                $nestedData['supplier_name'] = $suppliers['supplier_name'];
                $nestedData['truck'] = $truck['brand'] . " " . $truck['model'];
                $nestedData['plate_number'] = $truck['plate_number'];
                $nestedData['container_number'] = $Schedule->container_number;
                $mat_list = explode("-;-", $Schedule->material_list);
               

                $gcas = explode("|", $mat_list[0]);
                $description = explode("|", $mat_list[1]);
                $quantity = explode("|", $mat_list[2]);
                
                $material_list = "<table class='table table-responsive table-striped'>";
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
                $nestedData['dock'] = $Schedule->dock_name;
                
                $data[] = $nestedData;

            }
        }
          
        $json_data = array(
                    "draw"            => intval($request->input('draw')),  
                    "recordsTotal"    => intval($totalData),  
                    "recordsFiltered" => intval($totalFiltered), 
                    "data"            => $data   
                    );
            
        echo json_encode($json_data); 
    }

    public function getCountDock(Request $request){
        $date = Carbon::now();
        $datenow = $date->format("Y-m-d"); 
        $role_id = Auth::user()->role_id;
        $roles = Role::where('id',$role_id)->first();
        $docks = array();
        switch ($roles['description']) {
                case 'North':
                        $docks = array(1,2);
                    break;
                case 'South':
                        $docks = array(3,4,5);
                    break;
                
                default:
                    
                    break;
            }

        $dock = Dock::where('status', 1)->get();
        $dock_id_ = 0;
        $count = Schedule::where("date_of_delivery", $datenow)->whereIn("dock_id",$docks)->where("process_status",$request->process_status)->count();
        echo json_encode($count); 
        
    }

    public function getFirstDockData(Request $request){
        $date = Carbon::now();
        $datenow = $date->format("Y-m-d"); 
        $role_id = Auth::user()->role_id;
        $roles = Role::where('id',$role_id)->first();
        $dock_name = $request->module_name;

        // switch ($roles['description']) {
        //     case 'North':
        //             $docks = array(1,2);
        //         break;
        //     case 'South':
        //             $docks = array(3,4,5);
        //         break;
            
        //     default:
                
        //         break;
        // }


        $dock = Dock::where('status', 1)->where("dock_name",$dock_name)->first();
    
        $schedules = Schedule::where("date_of_delivery", $datenow)->where("dock_id",$dock['id'])->where("process_status",$request->process_status)->where("status",$request->status)->orderBy("slotting_time")
                     ->first();

        $data = array();
        $trucks_suppliers='';
        $trucks = '';
        $mats_ = "";
        if(!empty($schedules))
        {
            $slotting_ = str_replace("|","",$schedules['slotting_time']);
                $start = substr($slotting_, 0, 5);
                $end = substr($slotting_, -5);

            $suppliers = Supplier::where('id',$schedules['supplier_id'])->where('status', 1)->first();
           
            $truck = Truck::where('id',$schedules['truck_id'])->where('status', 1)->first();

            $num = $schedules['id'];
            $number = str_pad($num, 8, "0", STR_PAD_LEFT);

            $nestedData['id'] = $number;
            $nestedData['slotting_time'] = $start . " - " . $end;
            $nestedData['supplier_name'] = $suppliers['supplier_name'];
            $nestedData['truck'] = $truck['brand'] . " " . $truck['model'];
            $nestedData['plate_number'] = $truck['plate_number'];
            $nestedData['container_number'] = $schedules['container_number'];
            $nestedData['dock'] = $schedules['dock_name'];
            $nestedData['isDocked'] = $schedules->isDocked;

            $dateofdockout = $schedules->date_of_delivery . " " . $end;
            if(time() - strtotime($dateofdockout) < 1){
                $nestedData['status'] = "Dock";
            }elseif(time() - strtotime($dateofdockout) > 0){
                $nestedData['status'] = "Overtime";
            }else{
                $nestedData['status'] = "";
            }

            $nestedData['endtime'] = $end;
            $mat_list = explode("-;-", $schedules->material_list);
               

            $gcas = explode("|", $mat_list[0]);
            $description = explode("|", $mat_list[1]);
            $quantity = explode("|", $mat_list[2]);

            $nestedData['material_list']['gcas'] = $gcas;
            $nestedData['material_list']['description'] = $description;
            $nestedData['material_list']['quantity'] = $quantity;
            
            $data[] = $nestedData;
        }
            
        echo json_encode($data); 
    }

    public function changeProcessStatus(Request $request){

        $id=0;
        if(strlen($request->delivery_ticket_id) >= 8 ){
           $id = ltrim($request->delivery_ticket_id, '0');
        }else{
            $id = $request->delivery_ticket_id;
        }

        $parking_ = Parking::where("id",1)->first();

        $sched = Schedule::find($id);
        if(!empty($sched)){

            //Gate-IN
            if($sched->status == 10 && $sched->process_status == "incoming"){
                
                if($parking_->parking_slot == $parking_->parking_area){
                    return json_encode(["message"=>"Parking is Full"]);
                }    

                $sched->update(['process_status'=>"incoming","status"=> 8,"gate_in_timestamp"=>Carbon::now()]);
                $parking_->update(['parking_slot'=> $parking_->parking_slot + 1]);
                if($sched){
                    $ret = "Successfully Gate-IN";
                }else{
                    $ret = "Failed to Gate-IN";
                }

                return json_encode(["message"=>$ret]);
            }

            //Dock-IN
            if($sched->status == 8 && $sched->process_status == "incoming"){

                $gate_in_datetime = $sched->gate_in_timestamp;
                $parking_time = time() - strtotime($gate_in_datetime);
                $sched->update(['process_status'=>"incoming_dock_in","status"=> 9,"dock_in_timestamp"=>Carbon::now(),"parking_timestamp"=>$parking_time]);
                 $parking_->update(['parking_slot'=> $parking_->parking_slot - 1]);
                if($sched){
                    $ret = "Successfully Dock-IN";
                }else{
                    $ret = "Failed  to Dock-IN";
                }

                return json_encode(["message"=>$ret]);
            }

            //Dock-OUT
            if($sched->status == 9 && $sched->process_status == "incoming_dock_in"){
                $dock_in_datetime = $sched->dock_in_timestamp;
                $unloading_time = time() - strtotime($dock_in_datetime);
                $sched->update(['process_status'=>"outgoing","status"=> 11,"dock_out_timestamp"=>Carbon::now(),"unloading_timestamp"=>$unloading_time]);
                if($sched){
                    $ret = "Successfully Dock-Out";
                }else{
                    $ret = "Failed to Dock-Out";
                }

                return json_encode(["message"=>$ret]);
            }

            //Gate-Out
            if($sched->status == 11 && $sched->process_status == "outgoing"){
                $dock_out_datetime = $sched->dock_out_timestamp;
                $egress_time = time() - strtotime($dock_out_datetime);

                $gate_in_datetime = $sched->gate_in_timestamp;
                $truck_turnaround_timestamp = time() - strtotime($gate_in_datetime);
                
                $sched->update(['process_status'=>"completed","status"=> 7,"gate_out_timestamp"=>Carbon::now(),"egress_timestamp"=>$egress_time,'truck_turnaround_timestamp' => $truck_turnaround_timestamp]);
                if($sched){
                    $ret = "Successfully Gate-Out";
                }else{
                    $ret = "Failed to Gate-Out";
                }

                return json_encode(["message"=>$ret]);
            }

        }

        if($sched){
            $ret = "Success";
        }else{
            $ret = "Failed";
        }

        return json_encode(["message"=>$ret]);
        //$schedules = Schedule::update(['id'=>$request->id,'process_status'=> $request->process_status]);


       
    }

    public function checkIfIncoming(Request $request){
        $date = Carbon::now();
        $datenow = $date->format("Y-m-d"); 
        $count = 0;
        $incoming = Schedule::where("date_of_delivery", $datenow)->whereNull("process_status")->whereIn("status",[1,2])->get();
        
        foreach($incoming as $val){

            $slotting_ = str_replace("|","",$val->slotting_time);
            $start = substr($slotting_, 0, 5);
            $end = substr($slotting_, -5);

            $dateofdeparture = $val->date_of_delivery . " " . $start;
            if(time() - strtotime($dateofdeparture) <= 3601){
                $sched = Schedule::find($val->id);
                $sched->update(['process_status'=>"incoming","status"=>10]);

            }
        }

        echo json_encode($incoming); 
    }


    public function manual(){
        $date = Carbon::now();
        $datenow = $date->format("M d, Y"); 
        return view('qrcode/manual')->with('datenow',$datenow);
    }

    public function parking(){
        $date = Carbon::now();
        $datenow = $date->format("M d, Y"); 
        return view('dashboard/parking')->with('datenow',$datenow);
    }

    public function docking(){
        $date = Carbon::now();
        $datenow = $date->format("M d, Y"); 
        return view('dashboard/docking')->with('datenow',$datenow);
    }

    public function gate(){
        $date = Carbon::now();
        $datenow = $date->format("M d, Y"); 
        return view('dashboard/gate')->with('datenow',$datenow);
    }

    public function parkingDashboard(Request $request){
        $columns = array( 
                            0 =>'id', 
                            1 =>'slotting_time',
                            2 => 'supplier_name',
                            3 => 'truck',
                            4 => 'plate_number',
                            5 => 'container_number',
                            6 => 'dock',
                            7 => 'status'
                        );
    
        
        $date = Carbon::now();
        $datenow = $date->format("Y-m-d"); 

        $dock = Dock::where('status', 1)->get();
        $dock_id_ = 0;
        $totalData = Schedule::where("date_of_delivery", $datenow)->where("status",8)->count();
            
        $totalFiltered = $totalData; 

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        if($order == "id"){
            $order = "slotting_time";
        }
        $dir = $request->input('order.0.dir');
        if(empty($request->input('search.value')))
        {   
            if($request->module != null){
                $module_name = $request->module;
                foreach($dock as $d){
                    if(strpos($d->module, $module_name) !== false){
                        $dock_id_ = $d->id;
                    }
                }  
                $Schedules = Schedule::where("date_of_delivery", $datenow)->where("dock_id",$dock_id_)->where("status",8)
                         ->offset($start)
                         ->limit($limit)
                         ->orderBy($order,$dir)
                         ->get();
            }else{
                $Schedules = Schedule::where("date_of_delivery", $datenow)->where("status",8)
                         ->offset($start)
                         ->limit($limit)
                         ->orderBy($order,$dir)
                         ->get();    
            }
            
        }
        else {
            $search = $request->input('search.value'); 
            
            $Schedules =  Schedule::where('id','LIKE',"%{$search}%")->OrWhere("date_of_delivery", $datenow)->offset($start)
                 ->limit($limit)
                 ->orderBy($order,$dir)
                 ->get();



            $totalFiltered = Schedule::where('id','LIKE',"%{$search}%")->OrWhere("date_of_delivery", $datenow)->where("status",8)->count();
        }

        $data = array();
        $trucks_suppliers='';
        $trucks = '';

        if(!empty($Schedules))
        {
            foreach ($Schedules as $Schedule)
            {
               
                
                $slotting_ = str_replace("|","",$Schedule->slotting_time);
                    $start = substr($slotting_, 0, 5);
                    $end = substr($slotting_, -5);

                $suppliers = Supplier::where('id',$Schedule->supplier_id)->where('status', 1)->first();
               
                $truck = Truck::where('id',$Schedule->truck_id)->where('status', 1)->first();

                $nestedData['id'] = $Schedule->id;
                $nestedData['slotting_time'] = $start . " " . $end;
                $nestedData['supplier_name'] = $suppliers['supplier_name'];
                $nestedData['truck'] = $truck['brand'] . " " . $truck['model'];
                $nestedData['plate_number'] = $truck['plate_number'];
                $nestedData['container_number'] = $Schedule->container_number;
                $nestedData['dock'] = $Schedule->dock_name;
                
                 switch ($Schedule->status) {
                    case 8:
                        $dateofdockin = $Schedule->date_of_delivery . " " . $start;
                        if(time() - strtotime($dateofdockin) < 1){
                            $nestedData['status'] = "Parking";
                        }elseif(time() - strtotime($dateofdockin) > 0){
                            $nestedData['status'] = "Delayed";
                        }
                        break;
                    
                    case 9:
                        $dateofdockout = $Schedule->date_of_delivery . " " . $end;
                        if(time() - strtotime($dateofdockout) < 1){
                            $nestedData['status'] = "PROCEED TO " . $Schedule->dock_name;
                        }elseif(time() - strtotime($dateofdockout) > 0){
                            $nestedData['status'] = "Delayed";
                        }
                        break;  
                        
                    default:
                        $nestedData['status'] = "";
                        break;
                }
                $data[] = $nestedData;

            }
        }
          
        $json_data = array(
                    "draw"            => intval($request->input('draw')),  
                    "recordsTotal"    => intval($totalData),  
                    "recordsFiltered" => intval($totalFiltered), 
                    "data"            => $data   
                    );
            
        echo json_encode($json_data); 
    }

    public function dockingDashboard(Request $request){
        $columns = array( 
                            0 =>'id', 
                            1 =>'slotting_time',
                            2 => 'supplier_name',
                            3 => 'truck',
                            4 => 'plate_number',
                            5 => 'container_number',
                            6 => 'dock',
                            7 => 'status'
                        );
    
        
        $date = Carbon::now();
        $datenow = $date->format("Y-m-d"); 

        $dock = Dock::where('status', 1)->get();
        $dock_id_ = 0;
        $totalData = Schedule::where("date_of_delivery", $datenow)->count();
            
        $totalFiltered = $totalData; 

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        if($order == "id"){
            $order = "slotting_time";
        }
        $dir = $request->input('order.0.dir');
        if(empty($request->input('search.value')))
        {   
            if($request->module != null){
                $module_name = $request->module;
                foreach($dock as $d){
                    if(strpos($d->module, $module_name) !== false){
                        $dock_id_ = $d->id;
                    }
                }  
                $Schedules = Schedule::where("date_of_delivery", $datenow)->where("dock_id",$dock_id_)
                         ->offset($start)
                         ->limit($limit)
                         ->orderBy($order,$dir)
                         ->get();
            }else{
                $Schedules = Schedule::where("date_of_delivery", $datenow)
                         ->offset($start)
                         ->limit($limit)
                         ->orderBy($order,$dir)
                         ->get();    
            }
            
        }
        else {
            $search = $request->input('search.value'); 
            
            $Schedules =  Schedule::where('id','LIKE',"%{$search}%")->OrWhere("date_of_delivery", $datenow)->offset($start)
                 ->limit($limit)
                 ->orderBy($order,$dir)
                 ->get();



            $totalFiltered = Schedule::where('id','LIKE',"%{$search}%")->OrWhere("date_of_delivery", $datenow)->count();
        }

        $data = array();
        $trucks_suppliers='';
        $trucks = '';

        if(!empty($Schedules))
        {
            foreach ($Schedules as $Schedule)
            {
               
                
                $slotting_ = str_replace("|","",$Schedule->slotting_time);
                    $start = substr($slotting_, 0, 5);
                    $end = substr($slotting_, -5);

                $suppliers = Supplier::where('id',$Schedule->supplier_id)->where('status', 1)->first();
               
                $truck = Truck::where('id',$Schedule->truck_id)->where('status', 1)->first();

                $num = $Schedule->id;
                $number = str_pad($num, 8, "0", STR_PAD_LEFT);

                $nestedData['id'] = $number;
                $nestedData['slotting_time'] = $start . " " . $end;
                $nestedData['supplier_name'] = $suppliers['supplier_name'];
                $nestedData['truck'] = $truck['brand'] . " " . $truck['model'];
                $nestedData['plate_number'] = $truck['plate_number'];
                $nestedData['container_number'] = $Schedule->container_number;
                $nestedData['dock'] = $Schedule->dock_name;
                
                 switch ($Schedule->status) {
                    case 1:
                        $dateofdockin = $Schedule->date_of_delivery . " " . $start;
                        $dateofdockout = $Schedule->date_of_delivery . " " . $end;
                        if(time() - strtotime($dateofdockin) > 0 && time() - strtotime($dateofdockout) < 1){
                            $nestedData['status'] = "For Dock-In";
                        }else{
                            $nestedData['status'] = "";
                        }
                        break;
                    case 9:
                        $dateofdockout = $Schedule->date_of_delivery . " " . $end;
                        if(time() - strtotime($dateofdockout) < 1){
                            $nestedData['status'] = "UNLOADING";
                        }elseif(time() - strtotime($dateofdockout) > 0){
                            $nestedData['status'] = "Overtime";
                        }
                        break;  
                    case 11:
                        $dateofgateout = $Schedule->date_of_delivery . " " . $end;
                        if(time() - strtotime($dateofgateout) < 1){
                            $nestedData['status'] = "For-Gate-Out";
                        }elseif(time() - strtotime($dateofgateout) > 0){

                        $nestedData['status'] = "Over Staying";
                        }
                        break;   
                        
                    default:
                        $nestedData['status'] = "";
                        break;
                }
                $data[] = $nestedData;

            }
        }
          
        $json_data = array(
                    "draw"            => intval($request->input('draw')),  
                    "recordsTotal"    => intval($totalData),  
                    "recordsFiltered" => intval($totalFiltered), 
                    "data"            => $data   
                    );
            
        echo json_encode($json_data); 
    }   

    public function gateDashboard(Request $request){
        $columns = array( 
                            0 =>'id', 
                            1 =>'slotting_time',
                            2 => 'supplier_name',
                            3 => 'truck',
                            4 => 'plate_number',
                            5 => 'container_number',
                            6 => 'dock',
                            7 => 'status'
                        );
        $status = array();
        if($request->process_status == "incoming"){
            $status = array(10);
        }
        if($request->process_status ==  "outgoing"){
            $status = array(8,11);
        } 
        
        $date = Carbon::now();
        $datenow = $date->format("Y-m-d"); 

        $dock = Dock::where('status', 1)->get();
        $dock_id_ = 0;
        $totalData = Schedule::where("date_of_delivery", $datenow)->where("status",$status)->count();
            
        $totalFiltered = $totalData; 

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        if($order == "id"){
            $order = "slotting_time";
        }
        $dir = $request->input('order.0.dir');

        
        if(empty($request->input('search.value')))
        {   
            if($request->module != null){
                $module_name = $request->module;
                foreach($dock as $d){
                    if(strpos($d->module, $module_name) !== false){
                        $dock_id_ = $d->id;
                    }
                } 

                $Schedules = Schedule::where("date_of_delivery", $datenow)->whereIn("status",$status)->where("dock_id",$dock_id_)
                         ->offset($start)
                         ->limit($limit)
                         ->orderBy($order,$dir)
                         ->get();
            }else{
                $Schedules = Schedule::where("date_of_delivery", $datenow)->whereIn("status",$status)
                         ->offset($start)
                         ->limit($limit)
                         ->orderBy($order,$dir)
                         ->get();    
            }
            
        }
        else {
            $search = $request->input('search.value'); 
            
            $Schedules =  Schedule::where('id','LIKE',"%{$search}%")->OrWhere("date_of_delivery", $datenow)->whereIn("status",$status)->offset($start)
                 ->limit($limit)
                 ->orderBy($order,$dir)
                 ->get();



            $totalFiltered = Schedule::where('id','LIKE',"%{$search}%")->OrWhere("date_of_delivery", $datenow)->whereIn("status",$status)->count();
        }

        $data = array();
        $trucks_suppliers='';
        $trucks = '';

        if(!empty($Schedules))
        {
            foreach ($Schedules as $Schedule)
            {
               
                
                $slotting_ = str_replace("|","",$Schedule->slotting_time);
                    $start = substr($slotting_, 0, 5);
                    $end = substr($slotting_, -5);

                $suppliers = Supplier::where('id',$Schedule->supplier_id)->where('status', 1)->first();
               
                $truck = Truck::where('id',$Schedule->truck_id)->where('status', 1)->first();

                $num = $Schedule->id;
                $number = str_pad($num, 8, "0", STR_PAD_LEFT);

                $nestedData['id'] = $number;
                $nestedData['slotting_time'] = $start . " " . $end;
                $nestedData['supplier_name'] = $suppliers['supplier_name'];
                $nestedData['truck'] = $truck['brand'] . " " . $truck['model'];
                $nestedData['plate_number'] = $truck['plate_number'];
                $nestedData['container_number'] = $Schedule->container_number;
                $nestedData['dock'] = $Schedule->dock_name;
                
                 switch ($Schedule->status) {
                    case 8:
                        $nestedData['status'] = "In Process";
                        break;
                    case 10:
                        $dateofentry = $Schedule->date_of_delivery . " " . $start;
                        if(strtotime($dateofentry) - time() <= 3601){
                            $nestedData['status'] = "For-Entry";
                        }else{
                            $nestedData['status'] = "";
                        }
                        break;
                    case 11:
                        $nestedData['status'] = "For-Gate-Out";
                        break;   
                        
                    default:
                        $nestedData['status'] = "";
                        break;
                }
                $data[] = $nestedData;

            }
        }
          
        $json_data = array(
                    "draw"            => intval($request->input('draw')),  
                    "recordsTotal"    => intval($totalData),  
                    "recordsFiltered" => intval($totalFiltered), 
                    "data"            => $data   
                    );
            
        echo json_encode($json_data); 
    }

    public function qrreader(){
        $date = Carbon::now();
        $datenow = $date->format("M d, Y"); 
        return view('qrcode/reader')->with('datenow',$datenow);
    }

    public function setOvertime(Request $request){

        $id=0;
        if(strlen($request->delivery_ticket_id) >= 8 ){
           $id = ltrim($request->delivery_ticket_id, '0');
        }else{
            $id = $request->delivery_ticket_id;
        }

        $sched = Schedule::find($id);
        if(!empty($sched)){
            if($sched->status == 10 && $sched->process_status == "incoming"){
                $sched->update(['process_status'=>"incoming","status"=> 8,"gate_in_timestamp"=>Carbon::now()]);
                if($sched){
                    $ret = "Success";
                }else{
                    $ret = "Failed";
                }

                return json_encode(["message"=>$ret]);
            }

            if($sched->status == 8 && $sched->process_status == "incoming"){
                $gate_in_datetime = $sched->gate_in_timestamp;
                $parking_time = time() - strtotime($gate_in_datetime);
                $sched->update(['process_status'=>"incoming","status"=> 9,"dock_in_timestamp"=>Carbon::now(),"parking_timestamp"=>$parking_time]);
                if($sched){
                    $ret = "Success";
                }else{
                    $ret = "Failed";
                }

                return json_encode(["message"=>$ret]);
            }

            if($sched->status == 9 && $sched->process_status == "incoming"){
                $dock_in_datetime = $sched->dock_in_timestamp;
                $unloading_time = time() - strtotime($dock_in_datetime);
                $sched->update(['process_status'=>"outgoing","status"=> 11,"dock_out_timestamp"=>Carbon::now(),"unloading_timestamp"=>$unloading_time]);
                if($sched){
                    $ret = "Success";
                }else{
                    $ret = "Failed";
                }

                return json_encode(["message"=>$ret]);
            }

            if($sched->status == 11 && $sched->process_status == "outgoing"){
                $dock_out_datetime = $sched->dock_out_timestamp;
                $egress_time = time() - strtotime($dock_out_datetime);

                $gate_in_datetime = $sched->gate_in_timestamp;
                $truck_turnaround_timestamp = time() - strtotime($gate_in_datetime);
                
                $sched->update(['process_status'=>"completed","status"=> 7,"gate_out_timestamp"=>Carbon::now(),"egress_timestamp"=>$egress_time,'truck_turnaround_timestamp' => $truck_turnaround_timestamp]);
                if($sched){
                    $ret = "Success";
                }else{
                    $ret = "Failed";
                }

                return json_encode(["message"=>$ret]);
            }

        }

        if($sched){
            $ret = "Success";
        }else{
            $ret = "Failed";
        }

        return json_encode(["message"=>$ret]);
        //$schedules = Schedule::update(['id'=>$request->id,'process_status'=> $request->process_status]);


       
    }

    public function securityDashboard(Request $request){
        $columns = array( 
                            0 =>'id', 
                            1 =>'slotting_time',
                            2 => 'supplier_name',
                            3 => 'truck',
                            4 => 'plate_number',
                            5 => 'container_number',
                            6 => 'dock',
                            7 => 'status'
                        );
        
        $status = array();
        if($request->process_status == "incoming"){
            $status = array(10,1);
        }
        if($request->process_status ==  "outgoing"){
            $status = array(8,11);
        } 
        
        $date = Carbon::now();
        $datenow = $date->format("Y-m-d"); 

        $dock = Dock::where('status', 1)->get();
        $dock_id_ = 0;
        $totalData = Schedule::where("date_of_delivery", $datenow)->whereIn("status",$status)->count();
            
        $totalFiltered = $totalData; 

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        if($order == "id"){
            $order = "slotting_time";
        }
        $dir = $request->input('order.0.dir');

        if(empty($request->input('search.value')))
        {   
            if($request->module != null){
                $module_name = $request->module;
                foreach($dock as $d){
                    if(strpos($d->module, $module_name) !== false){
                        $dock_id_ = $d->id;
                    }
                } 

                $Schedules = Schedule::where("date_of_delivery", $datenow)->whereIn("status",$status)->where("dock_id",$dock_id_)
                         ->offset($start)
                         ->limit($limit)
                         ->orderBy($order,$dir)
                         ->get();
            }else{
                $Schedules = Schedule::where("date_of_delivery", $datenow)->whereIn("status",$status)
                         ->offset($start)
                         ->limit($limit)
                         ->orderBy($order,$dir)
                         ->get();    
            }
            
        }
        else {
            $search = $request->input('search.value'); 
            
            $Schedules =  Schedule::where('id','LIKE',"%{$search}%")->where("date_of_delivery", $datenow)->whereIn("status",$status)->offset($start)
                 ->limit($limit)
                 ->orderBy($order,$dir)
                 ->get();



            $totalFiltered = Schedule::where('id','LIKE',"%{$search}%")->where("date_of_delivery", $datenow)->whereIn("status",$status)->count();
        }

        $data = array();
        $trucks_suppliers='';
        $trucks = '';

        if(!empty($Schedules))
        {
            foreach ($Schedules as $Schedule)
            {
               
                
                $slotting_ = str_replace("|","",$Schedule->slotting_time);
                    $start = substr($slotting_, 0, 5);
                    $end = substr($slotting_, -5);

                $suppliers = Supplier::where('id',$Schedule->supplier_id)->where('status', 1)->first();
               
                $truck = Truck::where('id',$Schedule->truck_id)->where('status', 1)->first();

                $num = $Schedule->id;
                $number = str_pad($num, 8, "0", STR_PAD_LEFT);

                $nestedData['id'] = $number;
                $nestedData['slotting_time'] = $start . " " . $end;
                $nestedData['supplier_name'] = $suppliers['supplier_name'];
                $nestedData['truck'] = $truck['brand'] . " " . $truck['model'];
                $nestedData['plate_number'] = $truck['plate_number'];
                $nestedData['container_number'] = $Schedule->container_number;
                $nestedData['dock'] = $Schedule->dock_name;
                
                 switch ($Schedule->status) {
                    case 8:
                    case 9:
                        $nestedData['status'] = "In Process";
                        break;
                    case 10:
                        $dateofentry = $Schedule->date_of_delivery . " " . $start;
                        if(strtotime($dateofentry) - time() <= 3601){
                            $nestedData['status'] = "For-Entry";
                        }else{
                            $nestedData['status'] = "";
                        }
                        break;
                    case 11:
                        $nestedData['status'] = "For-Gate-Out";
                        break;   
                        
                    default:
                        $nestedData['status'] = "";
                        break;
                }
                $data[] = $nestedData;

            }
        }
          
        $json_data = array(
                    "draw"            => intval($request->input('draw')),  
                    "recordsTotal"    => intval($totalData),  
                    "recordsFiltered" => intval($totalFiltered), 
                    "data"            => $data   
                    );
            
        echo json_encode($json_data); 
    }  


    //JSONP Process
    public function changeProcessStatus_jsonp(){
        $delivery_ticket_id = Input::get('id');
        $callback = Input::get('callback');
        $id=0;
        if(strlen($delivery_ticket_id) >= 8 ){
           $id = ltrim($delivery_ticket_id, '0');
        }else{
            $id = $delivery_ticket_id;
        }

        $parking_ = Parking::where("id",1)->first();

        $sched = Schedule::find($id);
        if(!empty($sched)){

            //Gate-IN
            if($sched->status == 10 && $sched->process_status == "incoming"){
                
                if($parking_->parking_slot == $parking_->parking_area){
                    return json_encode(["message"=>"Parking is Full"]);
                }    

                $sched->update(['process_status'=>"incoming","status"=> 8,"gate_in_timestamp"=>Carbon::now()]);
                $parking_->update(['parking_slot'=> $parking_->parking_slot + 1]);
                if($sched){
                    $ret = "Successfully Gate-IN";
                }else{
                    $ret = "Failed to Gate-IN";
                }

                return json_encode(["message"=>$ret]);
            }

            //Dock-IN
            if($sched->status == 8 && $sched->process_status == "incoming"){

                $gate_in_datetime = $sched->gate_in_timestamp;
                $parking_time = time() - strtotime($gate_in_datetime);
                $sched->update(['process_status'=>"incoming_dock_in","status"=> 9,"dock_in_timestamp"=>Carbon::now(),"parking_timestamp"=>$parking_time]);
                 $parking_->update(['parking_slot'=> $parking_->parking_slot - 1]);
                if($sched){
                    $ret = "Successfully Dock-IN";
                }else{
                    $ret = "Failed  to Dock-IN";
                }

                return json_encode(["message"=>$ret]);
            }

            //Dock-OUT
            if($sched->status == 9 && $sched->process_status == "incoming_dock_in"){
                $dock_in_datetime = $sched->dock_in_timestamp;
                $unloading_time = time() - strtotime($dock_in_datetime);
                $sched->update(['process_status'=>"outgoing","status"=> 11,"dock_out_timestamp"=>Carbon::now(),"unloading_timestamp"=>$unloading_time]);
                if($sched){
                    $ret = "Successfully Dock-Out";
                }else{
                    $ret = "Failed to Dock-Out";
                }

                return json_encode(["message"=>$ret]);
            }

            //Gate-Out
            if($sched->status == 11 && $sched->process_status == "outgoing"){
                $dock_out_datetime = $sched->dock_out_timestamp;
                $egress_time = time() - strtotime($dock_out_datetime);

                $gate_in_datetime = $sched->gate_in_timestamp;
                $truck_turnaround_timestamp = time() - strtotime($gate_in_datetime);
                
                $sched->update(['process_status'=>"completed","status"=> 7,"gate_out_timestamp"=>Carbon::now(),"egress_timestamp"=>$egress_time,'truck_turnaround_timestamp' => $truck_turnaround_timestamp]);
                if($sched){
                    $ret = "Successfully Gate-Out";
                }else{
                    $ret = "Failed to Gate-Out";
                }

                return json_encode(["message"=>$ret]);
            }

        }

        if($sched){
            $ret = "Success";
        }else{
            $ret = "Failed";
        }

        return $callback. "(". json_encode(["message"=>$ret]).");";
        //$schedules = Schedule::update(['id'=>$request->id,'process_status'=> $request->process_status]);


       
    }

    //END JSONP PROCESS
}
