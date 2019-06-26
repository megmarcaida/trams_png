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
            $submodules = array();
            $role_account = Role::where('id',$role_id)->first();
            $sub_docks = explode("|",$role_account->submodules);
            $submodules = array_filter($sub_docks,function($values) { return $values !== ''; });
            $docks = array();
            // $pcc = array("title" => "PCC","slug" => "pcc","details" => array("PCC 1","PCC 2"));
            // $babycare = array("title" => "Baby Care","slug" => "baby-care","details" => array("Baby Care 1","Baby Care 2","Baby Care 3","Baby Care Scrap"));
            // $femcare = array("title" => "Fem Care","slug" => "fem-care","details" => array("Fem Care"));
            // $liquids = array("title" => "Liquids", "slug" => "liquids", "details" => array("Liquids","Liquids Out Canopy"));
            // $laundry = array("title" => "Laundry", "slug" => "laundry", "details" => array("Laundry","Laundry SB","Laundry Scrap"));
            $account_dock = array("title"=>"Dock","slug"=>"dock","details"=>$submodules);
            $role_account_desc = $role_account['description'];
            switch ($role_account_desc) {
                case 'North':
                    array_push($docks, $account_dock);
                    break;
                case 'South':
                    array_push($docks, $account_dock);
                    break;
                
                default:
                    array_push($docks, $account_dock);
                    // $docks =  array(
                    //             array(
                    //                 "title" => "PCC", 
                    //                 "slug" => "pcc",
                    //                 "details" => array("PCC1","PCC2")
                    //             ),
                    //             array(
                    //                 "title" => "Baby Care", 
                    //                 "slug" => "baby-care", 
                    //                 "details" => array("Baby Care 1","Baby Care 2","Baby Care 3","Baby Care Scrap")
                    //             ),
                    //             array(
                    //                 "title" => "Fem Care", 
                    //                 "slug" => "fem-care", 
                    //                 "details" => array("Fem Care")
                    //             ),
                    //             array(
                    //                 "title" => "Laundry",  
                    //                 "slug" => "laundry",
                    //                 "details" => array("Laundry","Laundry SB","Laundry Scrap")
                    //             ),
                    //             array(
                    //                 "title" => "Liquids", 
                    //                 "slug" => "liquids", 
                    //                 "details" => array("Liquids","Liquids Out Canopy")
                    //             )
                    //         );
                    break;
            }
            

            $date = Carbon::now();
            $datenow = $date->format("M d, Y"); 
            $dockData['data'] = Dock::whereIn('dock_name',$submodules)->where("status",1)->get();


            return view('dashboard/dock')->with(['datenow'=>$datenow, 'docks'=>$docks, 'role_account'=>$role_account_desc,'dockData'=>$dockData]);
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
        $totalData = Schedule::count();
            
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
                $Schedules = Schedule::where("dock_id",$dock_id_)
                         ->offset($start)
                         ->limit($limit)
                         ->orderBy($order,$dir)
                         ->get();
            }else{
                $Schedules = Schedule::offset($start)
                         ->limit($limit)
                         ->orderBy($order,$dir)
                         ->get();    
            }
            
        }
        else {
            $search = $request->input('search.value'); 
            
            $Schedules =  Schedule::where('id','LIKE',"%{$search}%")->offset($start)
                 ->limit($limit)
                 ->orderBy($order,$dir)
                 ->get();



            $totalFiltered = Schedule::where('id','LIKE',"%{$search}%")->count();
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

                $dateofdeparture = $Schedule->date_of_delivery . " " . $end;
                if(time()-strtotime($dateofdeparture) > 43200 || time()-strtotime($dateofdeparture) < -43200 ){
                //if(time()-strtotime($dateofdeparture) > 86400 || strtotime($dateofdeparture)-time() < 0){
                    continue;
                }

                $suppliers = Supplier::where('id',$Schedule->supplier_id)->where('status', 1)->first();
               
                $truck = Truck::where('id',$Schedule->truck_id)->where('status', 1)->first();

                $num = $Schedule->id;
                $number = str_pad($num, 8, "0", STR_PAD_LEFT);

                $nestedData['id'] = $number;
                $nestedData['slotting_time'] = date('M d, Y', strtotime($Schedule->date_of_delivery)) . " " . $start . " - " . $end;
                $nestedData['supplier_name'] = $suppliers['supplier_name'];
                $nestedData['truck'] = $truck['brand'] . " " . $truck['model'];
                $nestedData['plate_number'] = $truck['plate_number'] . " (" . $Schedule->container_number .")";
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
                            if($Schedule->isDockChange == "1"){
                                $nestedData['status'] = "Dock Changed - Proceed to " . $Schedule->dock_name;
                            }else{
                                $nestedData['status'] = "For-Entry";    
                            }
                            
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
                        if($Schedule->isDockChange == "1"){
                            $nestedData['status'] = "Dock Changed - Proceed to " . $Schedule->dock_name;
                        }else{
                            $nestedData['status'] = "";
                        }
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
        $submodules = array();
        $submodules = explode("|",$roles['submodules']);


        $dock = Dock::whereIn('dock_name',$submodules)->where('status', 1)->get();
        foreach($dock as $d){
            array_push($docks, $d->id);
        }

        $dock_id_ = 0;
        $totalData = Schedule::whereIn("dock_id",$docks)->where("process_status",$request->process_status)->count();
            
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
            
               
            $Schedules = Schedule::whereIn("dock_id",$docks)->where("process_status",$request->process_status)
                     ->offset($start)
                     ->limit($limit)
                     ->orderBy($order,$dir)
                     ->get();
            
            
        }
        else {
            $search = $request->input('search.value'); 
            
            $Schedules =  Schedule::where('id','LIKE',"%{$search}%")->whereIn ("dock_id",$docks)->where("process_status",$request->process_status)->offset($start)
                 ->limit($limit)
                 ->orderBy($order,$dir)
                 ->get();



            $totalFiltered = Schedule::where('id','LIKE',"%{$search}%")->whereIn ("dock_id",$docks)->where("process_status",$request->process_status)->count();
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

                $dateofdeparture = $Schedule->date_of_delivery . " " . $end;
                //before                                         // after
                if(time()-strtotime($dateofdeparture) > 43200 || time()-strtotime($dateofdeparture) < -43200 ){
                    continue;
                }

                $suppliers = Supplier::where('id',$Schedule->supplier_id)->where('status', 1)->first();
               
                $truck = Truck::where('id',$Schedule->truck_id)->where('status', 1)->first();

                $num = $Schedule->id;
                $number = str_pad($num, 8, "0", STR_PAD_LEFT);

                $nestedData['id'] = $number;
                $nestedData['slotting_time'] = date('M d, Y', strtotime($Schedule->date_of_delivery)) . " | " . $start . " - " . $end;
                $nestedData['supplier_name'] = $suppliers['supplier_name'];
                $nestedData['truck'] = $truck['brand'] . " " . $truck['model'];
                $nestedData['plate_number'] = $truck['plate_number'] . " (" . $Schedule->container_number . ")";
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

    public function getCountDock(Request $request){
        $date = Carbon::now();
        $datenow = $date->format("Y-m-d"); 
        $role_id = Auth::user()->role_id;
        $roles = Role::where('id',$role_id)->first();
        $docks = array();
        $submodules = array();
        $submodules = explode("|",$roles['submodules']);


        $dock = Dock::whereIn('dock_name',$submodules)->where('status', 1)->get();
        foreach($dock as $d){
            array_push($docks, $d->id);
        }
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


        $dock = Dock::where('status', 1)->where("dock_name",$dock_name)->first();
    
        $schedules = Schedule::where("dock_id",$dock['id'])->where("process_status",$request->process_status)->where("status",$request->status)->orderBy("slotting_time")->first();

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
        $process_name = $request->process_name;

        $sched = Schedule::find($id);
        if(!empty($sched)){

            //Gate-IN
            if($sched->status == 10 && $sched->process_status == "incoming" && $process_name == "gate-in"){
                
                if($parking_->parking_slot == $parking_->parking_area){
                    return json_encode(["message"=>"Parking is Full"]);
                }    

                $sched->update(['process_status'=>"incoming","status"=> 8,"gate_in_timestamp"=>Carbon::now()]);
                $parking_->update(['parking_slot'=> $parking_->parking_slot + 1]);
                if($sched){
                    $ret = ["success"=>"Successfully Gate-IN"];
                }else{
                    $ret = ["error"=>"Failed to Gate-IN"];
                }

                return json_encode($ret);
            }

            //Dock-IN
            if(($sched->status == 8 || $sched->status == 1 || $sched->status == 10 || $sched->status == 2 || $sched->status == 3 || $sched->status == 4) && $sched->process_status == "incoming" && $process_name == "dock-in"){

                $gate_in_datetime = $sched->gate_in_timestamp;
                $parking_time = time() - strtotime($gate_in_datetime);
                $sched->update(['process_status'=>"incoming_dock_in","status"=> 9,"dock_in_timestamp"=>Carbon::now(),"parking_timestamp"=>$parking_time]);
                $parking_->update(['parking_slot'=> $parking_->parking_slot - 1]);
                if($sched){
                    $ret = ["success"=>"Successfully Dock-IN"];
                }else{
                    $ret = ["error"=>"Failed  to Dock-IN"];
                }

                return json_encode($ret);
            }

            //Dock-OUT
            if($sched->status == 9 && $sched->process_status == "incoming_dock_in" && $process_name == "dock-out"){
                $dock_in_datetime = $sched->dock_in_timestamp;
                $unloading_time = time() - strtotime($dock_in_datetime);
                $sched->update(['process_status'=>"outgoing","status"=> 11,"dock_out_timestamp"=>Carbon::now(),"unloading_timestamp"=>$unloading_time]);
                if($sched){
                    $ret = ["success"=>"Successfully Dock-Out"];
                }else{
                    $ret = ["error"=>"Failed to Dock-Out"];
                }

                return json_encode($ret);
            }

            //Gate-Out
            if($sched->status == 11 && $sched->process_status == "outgoing" && $process_name == "gate-out" ){
                $dock_out_datetime = $sched->dock_out_timestamp;
                $egress_time = time() - strtotime($dock_out_datetime);

                $gate_in_datetime = $sched->gate_in_timestamp;
                $truck_turnaround_timestamp = time() - strtotime($gate_in_datetime);
                
                $sched->update(['process_status'=>"completed","status"=> 7,"gate_out_timestamp"=>Carbon::now(),"egress_timestamp"=>$egress_time,'truck_turnaround_timestamp' => $truck_turnaround_timestamp]);
                if($sched){
                    $ret = ["success"=>"Successfully Gate-Out"];
                }else{
                    $ret = ["error"=>"Failed to Gate-Out"];
                }

                return json_encode($ret);
            }

        }

        if($sched){
            $ret = ["success"=>"Success"];
        }else{
            $ret = ["error"=>"Unable to process."];
        }

        return json_encode($ret);
        //$schedules = Schedule::update(['id'=>$request->id,'process_status'=> $request->process_status]);


       
    }

    public function checkIfIncoming(Request $request){
        $date = Carbon::now();
        $datenow = $date->format("Y-m-d"); 
        $count = 0;
        $incoming = Schedule::where("date_of_delivery", $datenow)->where("process_status","incoming")->whereIn("status",[1,2,3,4])->get();
        
        foreach($incoming as $val){

            $slotting_ = str_replace("|","",$val->slotting_time);
            $start = substr($slotting_, 0, 5);
            $end = substr($slotting_, -5);

            $dateofdeparture = $val->date_of_delivery . " " . $start;
            if(strtotime($dateofdeparture) - time() <= 3601){
                $sched = Schedule::find($val->id);
                $sched->update(['process_status'=>"incoming","status"=>10]);

            }
        }

        echo json_encode($incoming); 
    }

    public function checkIfIncomingDock(Request $request){
        $date = Carbon::now();
        $datenow = $date->format("Y-m-d"); 
        $count = 0;
        $incoming = Schedule::whereNull("process_status")->whereIn("status",[1,2,3,4])->get();
        
        foreach($incoming as $val){

            $slotting_ = str_replace("|","",$val->slotting_time);
            $start = substr($slotting_, 0, 5);
            $end = substr($slotting_, -5);

            $dateofdeparture = $val->date_of_delivery . " " . $start;
            if(strtotime($dateofdeparture) - time() <= 43200){
                $sched = Schedule::find($val->id);
                $sched->update(['process_status'=>"incoming"]);

            }
        }

        echo json_encode($incoming); 
    }


    public function manual(){
        $date = Carbon::now();
        $datenow = $date->format("M d, Y"); 
        return view('qrcode/manual')->with('datenow',$datenow);
    }

    public function executive(){
        $date = Carbon::now();
        $datenow = $date->format("M d, Y"); 
        return view('dashboard/executive')->with('datenow',$datenow);
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
                $nestedData['plate_number'] = $truck['plate_number'] . " (" . $Schedule->container_number . ")";
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
                $Schedules = Schedule::where("date_of_delivery", $datenow)->where("dock_id",$dock_id_)->where('status','<>',7)
                         ->offset($start)
                         ->limit($limit)
                         ->orderBy($order,$dir)
                         ->get();
            }else{
                $Schedules = Schedule::where("date_of_delivery", $datenow)->where('status','<>',7)
                         ->offset($start)
                         ->limit($limit)
                         ->orderBy($order,$dir)
                         ->get();    
            }
            
        }
        else {
            $search = $request->input('search.value'); 
            
            $Schedules =  Schedule::where('id','LIKE',"%{$search}%")->OrWhere("date_of_delivery", $datenow)->where('status','<>',7)->offset($start)
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
                $nestedData['plate_number'] = $truck['plate_number'] . " (" . $Schedule->container_number . ")";
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

    public function changeDock(Request $request){

        $id=0;
        if(strlen($request->delivery_ticket_id) >= 8 ){
           $id = ltrim($request->delivery_ticket_id, '0');
        }else{
            $id = $request->delivery_ticket_id;
        }

        $sched = Schedule::find($id);
        if(!empty($sched)){
            $sched->update(['dock_id'=>$request->dock_id,'dock_name'=>$request->dock_name,'isDockChange'=>'1']);

        }

        if($sched){
            $ret = "Dock successfully changed.";
        }else{
            $ret = "Failed";
        }

        return json_encode(["message"=>$ret]);
        //$schedules = Schedule::update(['id'=>$request->id,'process_status'=> $request->process_status]);


    }

    public function getClickDockData(Request $request){

        $id=0;
        if(strlen($request->delivery_ticket_id) >= 8 ){
           $id = ltrim($request->delivery_ticket_id, '0');
        }else{
            $id = $request->delivery_ticket_id;
        }

        $Schedule = Schedule::find($id);
        $data = array();
        if(!empty($Schedule))
        {
            $slotting_ = str_replace("|","",$Schedule['slotting_time']);
                $start = substr($slotting_, 0, 5);
                $end = substr($slotting_, -5);

            $dateofdeparture = $Schedule->date_of_delivery . " " . $start;
            

            $suppliers = Supplier::where('id',$Schedule['supplier_id'])->where('status', 1)->first();
           
            $truck = Truck::where('id',$Schedule['truck_id'])->where('status', 1)->first();

            $num = $Schedule->id;
            $number = str_pad($num, 8, "0", STR_PAD_LEFT);

            $nestedData['id'] = $number;
            $nestedData['slotting_time'] = $start . " " . $end;
            $nestedData['supplier_name'] = $suppliers['supplier_name'];
            $nestedData['truck'] = $truck['brand'] . " " . $truck['model'];
            $nestedData['plate_number'] = $truck['plate_number'];
            $nestedData['container_number'] = $Schedule['container_number'];
            $mat_list = explode("-;-", $Schedule['material_list']);
               

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
            
            $data[] = $nestedData;

        }

        return json_encode($nestedData);


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
        $totalData = Schedule::whereIn("status",$status)->count();
            
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

                $Schedules = Schedule::whereIn("status",$status)->where("dock_id",$dock_id_)
                         ->offset($start)
                         ->limit($limit)
                         ->orderBy($order,$dir)
                         ->get();
            }else{
                $Schedules = Schedule::whereIn("status",$status)
                         ->offset($start)
                         ->limit($limit)
                         ->orderBy($order,$dir)
                         ->get();    
            }
            
        }
        else {
            $search = $request->input('search.value'); 
            
            $Schedules =  Schedule::where('id','LIKE',"%{$search}%")>whereIn("status",$status)->offset($start)
                 ->limit($limit)
                 ->orderBy($order,$dir)
                 ->get();



            $totalFiltered = Schedule::where('id','LIKE',"%{$search}%")->whereIn("status",$status)->count();
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

                $dateofdeparture = $Schedule->date_of_delivery . " " . $end;

                if($request->process_status == "incoming"){
                    if(time()-strtotime($dateofdeparture) > 43200 || time()-strtotime($dateofdeparture) < -43200 ){
                    //if(strtotime($dateofdeparture) - time() > 43200 || strtotime($dateofdeparture) - time() < 0){
                    //if(time() - strtotime($dateofdeparture) > 43188){
                        continue;
                    }
                }
                if($request->process_status ==  "outgoing"){
                } 

                $suppliers = Supplier::where('id',$Schedule->supplier_id)->where('status', 1)->first();
               
                $truck = Truck::where('id',$Schedule->truck_id)->where('status', 1)->first();

                $num = $Schedule->id;
                $number = str_pad($num, 8, "0", STR_PAD_LEFT);

                $nestedData['id'] = $number;
                $nestedData['slotting_time'] = date('M d, Y', strtotime($Schedule->date_of_delivery)) . " " .$start . " " . $end;
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
                            $nestedData['status'] = "For-Entry " .  (strtotime($dateofentry) - time());
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
        $delivery_ticket_id = $_GET['id'];
        //$callback = $_GET['callback'];
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
            $ret = "This ticket is already processed.";
        }else{
            $ret = "Failed to process";
        }

        return json_encode(["message"=>$ret]);
        //return Response::json(["message"=>$ret])->setCallback($callback);    

        //$schedules = Schedule::update(['id'=>$request->id,'process_status'=> $request->process_status]);


       
    }

    //END JSONP PROCESS


    //executive dashboards
    public function getTrucksCount(Request $request){
        $date = Carbon::now();
        $datenow = $date->format("Y-m-d"); 

        $schedulesTruck = Schedule::where('gate_in_timestamp', '>=', Carbon::now()->subDay())->count();

        return json_encode($schedulesTruck);
    }

    public function getOnTimeDepartures(Request $request){

        if($request->isModal == 1){

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

            $totalData = Schedule::where('gate_in_timestamp', '>=', Carbon::now()->subDay())->whereNotNull('gate_out_timestamp')->count();
                
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
                
                $Schedules = Schedule::where('gate_in_timestamp', '>=', Carbon::now()->subDay())->whereNotNull('gate_out_timestamp')
                             ->offset($start)
                             ->limit($limit)
                             ->orderBy($order,$dir)
                             ->get();
                
            }
            else {
                $search = $request->input('search.value'); 
                
                $Schedules =  Schedule::where('id','LIKE',"%{$search}%")->where('gate_in_timestamp', '>=', Carbon::now()->subDay())->whereNotNull('gate_out_timestamp')->offset($start)
                     ->limit($limit)
                     ->orderBy($order,$dir)
                     ->get();



                $totalFiltered = Schedule::where('id','LIKE',"%{$search}%")->where('gate_in_timestamp', '>=', Carbon::now()->subDay())->whereNotNull('gate_out_timestamp')->count();
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

                    $dateofdeparture = $Schedule->date_of_delivery . " " . $end;
                    $timestamp = strtotime($dateofdeparture) + 60*60;
                    $time = $Schedule->date_of_delivery . " " . date('H:i', $timestamp);
                    if($Schedule->gate_out_timestamp < $time){
                        continue;
                    }

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
                    
                    $data[] = $nestedData;

                }
            }
              
            $json_data = array(
                        "draw"            => intval($request->input('draw')),  
                        "recordsTotal"    => intval($totalData),  
                        "recordsFiltered" => intval($totalFiltered), 
                        "data"            => $data   
                        );
                
            return json_encode($json_data);

        }else{

            $date = Carbon::now();
            $datenow = $date->format("Y-m-d"); 
            $data = array();
            $getSchedules = Schedule::where('gate_in_timestamp', '>=', Carbon::now()->subDay())->whereNotNull('gate_out_timestamp')->get();
            $count = 0;
            $total = 0;
            $percentage = 0;
            
            foreach($getSchedules as $schedule){


                $slotting_ = str_replace("|","",$schedule->slotting_time);
                $end = substr($slotting_, -5);
                $dateofdeparture = $schedule->date_of_delivery . " " . $end;
                $timestamp = strtotime($dateofdeparture) + 60*60;
                $time = $schedule->date_of_delivery . " " . date('H:i', $timestamp);
                if($schedule->gate_out_timestamp < $time){
                    $count++;
                }

                //$nestedData['sched'] = $schedule->gate_in_timestamp . " " . $dateofentry;

                $total++;

                //$data[] = $nestedData;

            }
            if($total == 0){
                return json_encode(0);
            }
            $percentage = round((($count / $total) * 100),1);

            return json_encode($percentage);
        }
    }

    public function getOnTimeArrivals(Request $request){

         if($request->isModal == 1){

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

            $totalData = Schedule::where('gate_in_timestamp', '>=', Carbon::now()->subDay())->count();
                
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
                
                $Schedules = Schedule::where('gate_in_timestamp', '>=', Carbon::now()->subDay())
                             ->offset($start)
                             ->limit($limit)
                             ->orderBy($order,$dir)
                             ->get();
                
            }
            else {
                $search = $request->input('search.value'); 
                
                $Schedules =  Schedule::where('id','LIKE',"%{$search}%")->where('gate_in_timestamp', '>=', Carbon::now()->subDay())->offset($start)
                     ->limit($limit)
                     ->orderBy($order,$dir)
                     ->get();



                $totalFiltered = Schedule::where('id','LIKE',"%{$search}%")->where('gate_in_timestamp', '>=', Carbon::now()->subDay())->count();
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
                    $dateofarrival = $Schedule->date_of_delivery . " " . $start;

                    if($Schedule->gate_in_timestamp < $dateofarrival){
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
                
            return json_encode($json_data);

        }else{

            $date = Carbon::now();
            $datenow = $date->format("Y-m-d"); 
            $data = array();
            $getSchedules = Schedule::where('gate_in_timestamp', '>=', Carbon::now()->subDay())->get();
            $count = 0;
            $total = 0;
            $percentage = 0;
            
            foreach($getSchedules as $schedule){


                $slotting_ = str_replace("|","",$schedule->slotting_time);
                $start = substr($slotting_, 0, 5);
                $dateofarrival = $schedule->date_of_delivery . " " . $start;

                if($schedule->gate_in_timestamp < $dateofarrival){
                    $count++;
                }

                //$nestedData['sched'] = $schedule->gate_in_timestamp . " " . $dateofentry;

                $total++;

                //$data[] = $nestedData;

            }
            if($total == 0){
                return json_encode(0);
            }
            $percentage = round((($count / $total) * 100),1);

            return json_encode($percentage);
        }
    }

    public function getSlottingCompliance(Request $request){
        $date = Carbon::now();
        $datenow = $date->format("Y-m-d"); 
        $data = array();
        $getSchedules = Schedule::where('gate_in_timestamp', '>=', Carbon::now()->subDay())->get();
        $count = 0;
        $total = 0;
        $percentage = 0;
        
        foreach($getSchedules as $schedule){


            $slotting_ = str_replace("|","",$schedule->slotting_time);
             $end = substr($slotting_, -5);
            $dateofdeparture = $schedule->date_of_delivery . " " . $end;

            if($schedule->dock_out_timestamp < $dateofdeparture){
                $count++;
            }

            //$nestedData['sched'] = $schedule->gate_in_timestamp . " " . $dateofentry;

            $total++;

            //$data[] = $nestedData;

        }
        if($total == 0){
            return json_encode(0);
        }
        $percentage = round((($count / $total) * 100),1);

        return json_encode($percentage);
    }

    public function getAverageTurnAroundTime(Request $request){
        $date = Carbon::now();
        $datenow = $date->format("Y-m-d"); 
        $data = array();
        $getSchedules = Schedule::where('gate_in_timestamp', '>=', Carbon::now()->subDay())->where("status",7)->where("process_status","completed")->get();
        
        $turnAroundTime=0;
        $total = 0;
        $percentage = 0;
        $hours = "";
        
        foreach($getSchedules as $schedule){


            $slotting_ = str_replace("|","",$schedule->slotting_time);
             $end = substr($slotting_, -5);
            $dateofdeparture = $schedule->date_of_delivery . " " . $end;

            $turnAroundTime += $schedule->truck_turnaround_timestamp;
  
            //$nestedData['sched'] = $schedule->gate_in_timestamp . " " . $dateofentry;

            $total++;

            //$data[] = $nestedData;

        }
        if($total == 0){
            return json_encode(0);
        }
        $percentage = $turnAroundTime / $total;
        $hours = gmdate('h',(int)$percentage) . " h " . gmdate('i',(int)$percentage) . " m";
        return json_encode($hours);
    }

    public function getOverStaying(Request $request){

        if($request->isModal == 1){
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

            $totalData = Schedule::whereIn("status",[8,9,11])->count();
                
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
                
                $Schedules = Schedule::whereIn("status",[8,9,11])->offset($start)
                             ->limit($limit)
                             ->orderBy($order,$dir)
                             ->get();
                
            }
            else {
                $search = $request->input('search.value'); 
                
                $Schedules =  Schedule::where('id','LIKE',"%{$search}%")->whereIn("status",[8,9,11])->offset($start)
                     ->limit($limit)
                     ->orderBy($order,$dir)
                     ->get();



                $totalFiltered = Schedule::where('id','LIKE',"%{$search}%")->whereIn("status",[8,9,11])->count();
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
                    $dateofarrival = $Schedule->date_of_delivery . " " . $start;


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
                
            return json_encode($json_data);
        }else{
            $date = Carbon::now();
            $datenow = $date->format("Y-m-d"); 
            $data = array();
            $getSchedules = Schedule::whereIn("status",[8,9,11])->get();
            $count = 0;
            
            foreach($getSchedules as $schedule){


                $slotting_ = str_replace("|","",$schedule->slotting_time);
                $end = substr($slotting_, -5);
                $dateofdeparture = $schedule->date_of_delivery . " " . $end;
                $timestamp = strtotime($dateofdeparture) + 60*60;
                if($timestamp - time()  < 3601){
                    $count++;
                }

                $nestedData['time'] = strtotime($timestamp) - time();
                $data[] = $nestedData;

            }

            

            return json_encode($count);
        }
    }
    
    public function getOvertime(Request $request){

        if($request->isModal == 1){
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

            $totalData = Schedule::whereIn("status",[9])->whereNull("dock_out_timestamp")->count();
                
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
                
                $Schedules = Schedule::whereIn("status",[9])->whereNull("dock_out_timestamp")->offset($start)
                             ->limit($limit)
                             ->orderBy($order,$dir)
                             ->get();
                
            }
            else {
                $search = $request->input('search.value'); 
                
                $Schedules =  Schedule::where('id','LIKE',"%{$search}%")->whereIn("status",[9])->whereNull("dock_out_timestamp")->offset($start)
                     ->limit($limit)
                     ->orderBy($order,$dir)
                     ->get();



                $totalFiltered = Schedule::where('id','LIKE',"%{$search}%")->whereIn("status",[9])->whereNull("dock_out_timestamp")->count();
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
                    $dateofarrival = $Schedule->date_of_delivery . " " . $start;


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
                
            return json_encode($json_data);
        }else{

            $date = Carbon::now();
            $datenow = $date->format("Y-m-d"); 
            $data = array();
            $getSchedules = Schedule::whereIn("status",[9])->whereNull("dock_out_timestamp")->get();
            $count = 0;
            foreach($getSchedules as $schedule){


                $slotting_ = str_replace("|","",$schedule->slotting_time);
                $end = substr($slotting_, -5);
                $dateofdeparture = $schedule->date_of_delivery . " " . $end;
                $timestamp = strtotime($dateofdeparture);
                $time = $schedule->date_of_delivery . " " . date('H:i', $timestamp);
                if(strtotime($time) - time()  < 1){
                    $count++;
                }

                //$nestedData['sched'] = $schedule->gate_in_timestamp . " " . $dateofentry;


                //$data[] = $nestedData;

            }

            

            return json_encode($count);
        }
    }

    public function getDelayed(Request $request){

        if($request->isModal == 1){

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

            $totalData = Schedule::whereNull("dock_in_timestamp")->count();
                
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
                
                $Schedules = Schedule::whereNull("dock_in_timestamp")
                             ->offset($start)
                             ->limit($limit)
                             ->orderBy($order,$dir)
                             ->get();
                
            }
            else {
                $search = $request->input('search.value'); 
                
                $Schedules =  Schedule::where('id','LIKE',"%{$search}%")->whereNull("dock_in_timestamp")->offset($start)
                     ->limit($limit)
                     ->orderBy($order,$dir)
                     ->get();



                $totalFiltered = Schedule::where('id','LIKE',"%{$search}%")->whereNull("dock_in_timestamp")->count();
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
                    $dateofarrival = $Schedule->date_of_delivery . " " . $start;


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
                
            return json_encode($json_data);

        }else{

            $date = Carbon::now();
            $datenow = $date->format("Y-m-d"); 
            $data = array();
            $getSchedules = Schedule::whereIn("status",[8,10])->whereNull("dock_in_timestamp")->get();
            $count = 0;
            foreach($getSchedules as $schedule){


                $slotting_ = str_replace("|","",$schedule->slotting_time);
                $end = substr($slotting_, -5);
                $dateofdeparture = $schedule->date_of_delivery . " " . $end;
                $timestamp = strtotime($dateofdeparture) + 60*60;
                $time = $schedule->date_of_delivery . " " . date('H:i', $timestamp);
                $count++;

                //$nestedData['sched'] = $schedule->gate_in_timestamp . " " . $dateofentry;


                //$data[] = $nestedData;

            }

            

            return json_encode($count);

        }
    }

    public function getUnloading(Request $request){

        if($request->isModal == 1){
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

            $totalData = Schedule::where('gate_in_timestamp', '>=', Carbon::now()->subDay())->whereIn("status",[9])->whereNotNull("dock_in_timestamp")->where("process_status","incoming_dock_in")->whereNull("unloading_timestamp")->count();
                
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
                
                $Schedules = Schedule::where('gate_in_timestamp', '>=', Carbon::now()->subDay())->whereIn("status",[9])->whereNotNull("dock_in_timestamp")->where("process_status","incoming_dock_in")->whereNull("unloading_timestamp")->limit($limit)
                             ->orderBy($order,$dir)
                             ->get();
                
            }
            else {
                $search = $request->input('search.value'); 
                
                $Schedules =  Schedule::where('id','LIKE',"%{$search}%")->where('gate_in_timestamp', '>=', Carbon::now()->subDay())->whereIn("status",[9])->whereNotNull("dock_in_timestamp")->where("process_status","incoming_dock_in")->whereNull("unloading_timestamp")->offset($start)
                     ->limit($limit)
                     ->orderBy($order,$dir)
                     ->get();



                $totalFiltered = Schedule::where('id','LIKE',"%{$search}%")->where('gate_in_timestamp', '>=', Carbon::now()->subDay())->whereIn("status",[9])->whereNotNull("dock_in_timestamp")->where("process_status","incoming_dock_in")->whereNull("unloading_timestamp")->count();
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
                    $dateofarrival = $Schedule->date_of_delivery . " " . $start;


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
                
            return json_encode($json_data);
        }else{
            $date = Carbon::now();
            $datenow = $date->format("Y-m-d"); 
            $data = array();
            $getSchedules = Schedule::where('gate_in_timestamp', '>=', Carbon::now()->subDay())->whereIn("status",[9])->whereNotNull("dock_in_timestamp")->where("process_status","incoming_dock_in")->whereNull("unloading_timestamp")->get();
            $count = 0;
            foreach($getSchedules as $schedule){


                $slotting_ = str_replace("|","",$schedule->slotting_time);
                $end = substr($slotting_, -5);
                $dateofdeparture = $schedule->date_of_delivery . " " . $end;
                $timestamp = strtotime($dateofdeparture) + 60*60;
                $time = $schedule->date_of_delivery . " " . date('H:i', $timestamp);
                $count++;

                //$nestedData['sched'] = $schedule->gate_in_timestamp . " " . $dateofentry;


                //$data[] = $nestedData;

            }

            

            return json_encode($count);
        }
    }

    public function getOnSite(Request $request){

        if($request->isModal == 1){
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

            $totalData = Schedule::where('gate_in_timestamp', '>=', Carbon::now()->subDay())->count();
                
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
                
                $Schedules = Schedule::where('gate_in_timestamp', '>=', Carbon::now()->subDay())->offset($start)
                             ->limit($limit)
                             ->orderBy($order,$dir)
                             ->get();
                
            }
            else {
                $search = $request->input('search.value'); 
                
                $Schedules =  Schedule::where('id','LIKE',"%{$search}%")->where('gate_in_timestamp', '>=', Carbon::now()->subDay())
                     ->offset($start)
                     ->limit($limit)
                     ->orderBy($order,$dir)
                     ->get();



                $totalFiltered = Schedule::where('id','LIKE',"%{$search}%")->where('gate_in_timestamp', '>=', Carbon::now()->subDay())->count();
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
                    $dateofarrival = $Schedule->date_of_delivery . " " . $start;


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
                
            return json_encode($json_data);
        }else{

            $date = Carbon::now();
            $datenow = $date->format("Y-m-d"); 
            $data = array();
            $getSchedules = Schedule::where('gate_in_timestamp', '>=', Carbon::now()->subDay())->get();
            $count = 0;
            foreach($getSchedules as $schedule){


                $slotting_ = str_replace("|","",$schedule->slotting_time);
                $end = substr($slotting_, -5);
                $dateofdeparture = $schedule->date_of_delivery . " " . $end;
                $timestamp = strtotime($dateofdeparture) + 60*60;
                $time = $schedule->date_of_delivery . " " . date('H:i', $timestamp);
                $count++;

                //$nestedData['sched'] = $schedule->gate_in_timestamp . " " . $dateofentry;


                //$data[] = $nestedData;

            }

            

            return json_encode($count);
        }
    }
}
