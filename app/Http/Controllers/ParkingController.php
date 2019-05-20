<?php

namespace App\Http\Controllers;

use App\Parking;
use Illuminate\Http\Request;
use DataTables;
use App\Exports\ParkingExport;
use App\Imports\ParkingImport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class ParkingController extends Controller
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
   
        if ($request->ajax()) {
            $data = Parking::all();
            return Datatables::of($data)->make();
        }

        $date = Carbon::now();
        $datenow = $date->format("M d, Y"); 
      
        return view('dashboard/othersparking')->with('datenow',$datenow);
    }


    public function allParking(Request $request){

        $columns = array( 
                            0 =>'parking_name', 
                            1 =>'parking_description',
                            2 => 'parking_slot',
                            3 => 'parking_area',
                            4 => 'id',
                            5 => 'parking_block',
                            6 => 'parking_status',
                            7 => 'status',
                            8 => 'created_at'
                        );
  
        $totalData = Parking::count();
            
        $totalFiltered = $totalData; 

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
            
        if(empty($request->input('search.value')))
        {            
            $parking = Parking::offset($start)
                         ->limit($limit)
                         ->orderBy($order,$dir)
                         ->get();
        }
        else {
            $search = $request->input('search.value'); 
            
            $parking =  Parking::where('id','LIKE',"%{$search}%")
                            ->orWhere('parking_name','LIKE',"%{$search}%")
                            ->orWhere('parking_description','LIKE',"%{$search}%")
                            ->orWhere('parking_slot','LIKE',"%{$search}%")
                            ->orWhere('parking_area','LIKE',"%{$search}%")
                            ->orWhere('parking_block','LIKE',"%{$search}%")
                            ->orWhere('parking_status','LIKE',"%{$search}%")
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();



            $totalFiltered = Parking::where('id','LIKE',"%{$search}%")
                            ->orWhere('parking_name','LIKE',"%{$search}%")
                            ->orWhere('parking_description','LIKE',"%{$search}%")
                            ->orWhere('parking_slot','LIKE',"%{$search}%")
                            ->orWhere('parking_area','LIKE',"%{$search}%")
                            ->orWhere('parking_block','LIKE',"%{$search}%")
                            ->orWhere('parking_status','LIKE',"%{$search}%")
                            ->count();
        }

        $data = array();
        if(!empty($parking))
        {
            foreach ($parking as $park)
            {
              

                $nestedData['id'] = $park->id;
                $nestedData['parking_name'] = $park->parking_name;
                $nestedData['parking_description'] = $park->parking_description;
                $nestedData['parking_slot'] = $park->parking_slot;
                $nestedData['parking_area'] = $park->parking_area;
                $nestedData['parking_block'] = $park->parking_block;
                $nestedData['parking_status'] = $park->parking_status;
                $nestedData['created_at'] = date('j M Y h:i a',strtotime($park->created_at));
                if($park->status == 1){

                    $nestedData['options'] = "<a href='javascript:void(0)' data-toggle='tooltip'  data-id='".$park->id."' data-original-title='Edit' class='edit btn btn-primary btn-sm editProduct'>Edit</a>

                        <a href='javascript:void(0)' data-toggle='tooltip'  data-id='".$park->id."' data-status='".$park->status."'data-original-title='Delete' class='btn btn-danger btn-sm deactivateOrActivateParking'>Deactivate</a>";
                }else{

                    $nestedData['options'] = "<a href='javascript:void(0)' data-toggle='tooltip'  data-id='".$park->id."' data-original-title='Edit' class='edit btn btn-primary btn-sm editProduct'>Edit</a>

                        <a href='javascript:void(0)' data-toggle='tooltip'  data-id='".$park->id."' data-status='".$park->status."' data-original-title='Delete' class='btn btn-danger btn-sm deactivateOrActivateParking'>Activate</a>";
                }
                $nestedData['status'] = $park->status == 1 ? "Active" : "Inactive";
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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $ret = ['success'=>'Parking saved successfully.'];
        Parking::updateOrCreate(['id' => $request->parking_id],
            ['parking_name' => $request->parking_name, 'parking_description' => $request->parking_description, 'parking_slot' => $request->parking_slot, 'parking_area' => $request->parking_area, 'parking_block' => $request->parking_block, 'parking_status' => ""]);        
       

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
        $parking = Parking::find($id);
        return response()->json($parking);
    }
  
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy($id,$status)
    {
        Parking::find($id)->update(['status' => 0]);
        return response()->json(['success'=>'Parking deactivated successfully.']);
    }

    public function deactivateOrActivateParking(Request $request)
    {
         $id = $request->id;
         $status = $request->status;
         if($status == 1){

            Parking::find($id)->update(['status' => 0]);
            return response()->json(['success'=>'Parking deactivated successfully.']);
        }elseif($status == 0){

            Parking::find($id)->update(['status' => 1]);
            return response()->json(['success'=>'Parking activated successfully.']);
        }


    }

    public function export() 
    {
        return Excel::download(new ParkingImport, 'parking.xlsx');
    }

     public function import() 
    {
        Excel::import(new ParkingImport,request()->file('file'));
       
        return redirect()->back()->with("import_message","Importing of Parking process successfully."); 
    }
}
