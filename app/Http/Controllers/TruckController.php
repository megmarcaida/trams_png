<?php

namespace App\Http\Controllers;

use App\Truck;
use App\Supplier;
use Illuminate\Http\Request;
use DataTables;
use App\Exports\TruckExport;
use Maatwebsite\Excel\Facades\Excel;

class TruckController extends Controller
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
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $supplierData['data'] = Supplier::where("status",1)->get();

        if ($request->ajax()) {
            $data = Truck::all();
            return Datatables::of($data)->make();
            // return view('recordmaintenance/truck')->with("supplierData",$supplierData)->with("trucks",$data);   
            return view('recordmaintenance/truck');
        }
        return view('recordmaintenance/truck')->with("supplierData",$supplierData);
    }

    public function allTrucks(Request $request){

        $columns = array( 
                            0 =>'trucking_company', 
                            1 => 'supplier_ids',
                            2 =>'plate_number',
                            3 => 'brand',
                            4 => 'model',
                            5 => 'id',
                            6 => 'type',
                            7 => 'status'
                        );
  
        $totalData = Truck::count();
            
        $totalFiltered = $totalData; 

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        $trucks_suppliers = '';
        if(empty($request->input('search.value')))
        {            
            $trucks = Truck::offset($start)
                         ->limit($limit)
                         ->orderBy($order,$dir)
                         ->get();

            // foreach($trucks as $truck){
                    
            //     $supplier_ids = $truck->supplier_ids;
            //     $supplier_ids = explode(" | ", $supplier_ids);

            //     foreach($supplier_ids as $supplier_id){
            //         $suppliers = Supplier::where('id',$supplier_id)->first();
            //         $trucks_suppliers .= $suppliers->supplier_name . " | ";
            //     }
            // }


        }
        else {
            $search = $request->input('search.value'); 

            $trucks =  Truck::where('id','LIKE',"%{$search}%")
                            ->orWhere('trucking_company','LIKE',"%{$search}%")
                            ->orWhere('plate_number','LIKE',"%{$search}%")
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();

            $totalFiltered = Truck::where('id','LIKE',"%{$search}%")
                             ->orWhere('trucking_company','LIKE',"%{$search}%")
                            ->orWhere('plate_number','LIKE',"%{$search}%")
                            ->count();
        }

        $data = array();
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
                // $nestedData['supplier_name'] = substr(strip_tags($post->supplier_name),0,50)."...";
                $nestedData['trucking_company'] = $truck->trucking_company;
                $nestedData['plate_number'] = $truck->plate_number;
                $nestedData['brand'] = $truck->brand;
                $nestedData['model'] = $truck->model;
                $nestedData['type'] = $truck->type;
               
                if($truck->status == 1){

                    $nestedData['options'] = "<a href='javascript:void(0)' data-toggle='tooltip'  data-id='".$truck->id."' data-original-title='Edit' class='edit btn btn-primary btn-sm editProduct'>Edit</a>

                        <a href='javascript:void(0)' data-toggle='tooltip'  data-id='".$truck->id."' data-status='".$truck->status."'data-original-title='Delete' class='btn btn-danger btn-sm deactivateOrActivateTruck'>Deactivate</a>";
                }else{

                    $nestedData['options'] = "<a href='javascript:void(0)' data-toggle='tooltip'  data-id='".$truck->id."' data-original-title='Edit' class='edit btn btn-primary btn-sm editProduct'>Edit</a>

                        <a href='javascript:void(0)' data-toggle='tooltip'  data-id='".$truck->id."' data-status='".$truck->status."' data-original-title='Delete' class='btn btn-danger btn-sm deactivateOrActivateTruck'>Activate</a>";
                }
                $nestedData['status'] = $truck->status == 1 ? "Active" : "Inactive";
                $data[] = $nestedData;
                $trucks_suppliers = '';
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

        $isExistPlateNumber = Truck::where("plate_number",$request->plate_number)->first();

        $isExist = Truck::find($request->id);

        if($isExistPlateNumber && !$isExist){
            $ret = ['error'=>'Plate Number already exists.'];
        }else{
            $ret = ['success'=>'Truck saved successfully.'];
            Truck::updateOrCreate(['id' => $request->id],
                ['supplier_ids' => $request->supplier_ids, 'trucking_company' => $request->trucking_company, 'plate_number' => $request->plate_number, 'brand' => $request->brand, 'model' => $request->model, 'type' => $request->types]);  
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
        $truck = Truck::find($id);
        return response()->json($truck);
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
        Truck::find($id)->update(['status' => 0]);
        return response()->json(['success'=>'Truck deactivated successfully.']);
    }

    public function deactivateOrActivateTruck(Request $request)
    {
        //Supplier::find($id)->delete();
         $id = $request->id;
         $status =$request->status;
         if($status == 1){

            Truck::find($id)->update(['status' => 0]);
            return response()->json(['success'=>'Truck deactivated successfully.']);
        }elseif($status == 0){

            Truck::find($id)->update(['status' => 1]);
            return response()->json(['success'=>'Truck activated successfully.']);
        }
    }

    public function allsuppliers(){
        $data = Supplier::all();

         echo json_encode($data); 
    }

    public function export() 
    {
        return Excel::download(new TruckExport, 'trucks.xlsx');
    }
}
