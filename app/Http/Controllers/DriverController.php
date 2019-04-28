<?php

namespace App\Http\Controllers;

use App\Driver;
use App\Supplier;
use Illuminate\Http\Request;
use DataTables;
use Auth;
use App\Exports\DriverExport;
use Maatwebsite\Excel\Facades\Excel;

class DriverController extends Controller
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
            $data = Driver::all();
            return Datatables::of($data)->make(); 
            

            return view('recordmaintenance/driver');
        }
        return view('recordmaintenance/driver')->with("supplierData",$supplierData);
    }

     public function showPendingRegistrations()
    {
       $drivers = Driver::where("isApproved",0)->get();
        $data = array();
       $drivers_suppliers='';
       foreach ($drivers as $driver)
            {
                $supplier_ids = explode('|',$driver->supplier_ids);
                
                foreach($supplier_ids as $supplier_id){
                    if($supplier_id == null){
                        continue;
                    }
                    $suppliers = Supplier::where('id',$supplier_id)->where('status', 1)->first();
                    if($suppliers == null){
                        continue;
                    }
                    $drivers_suppliers .= $suppliers->supplier_name . " | ";
                }
              
                $nestedData['id'] = $driver->id;
                $nestedData['supplier_ids'] =  $drivers_suppliers;
                $nestedData['logistics_company'] = $driver->logistics_company;
                $nestedData['first_name'] = $driver->first_name;
                $nestedData['mobile_number'] = $driver->mobile_number;
                $nestedData['last_name'] = $driver->last_name;
                $nestedData['company_id_number'] = $driver->company_id_number;
                $nestedData['license_number'] = $driver->license_number;

                $nestedData['isApproved'] = $driver->isApproved == 0 ? "<b class='text-danger'>NO</b>" : "<b class='text-success'>YES</b>";
                $nestedData['dateOfSafetyOrientation'] = $driver->dateOfSafetyOrientation;

                $nestedData['status'] = $driver->status == 1 ? "<b class='text-success'>Active</b>" : "<b class='text-danger'>Inactive</b>";
                $data[] = $nestedData;
                $drivers_suppliers = '';
            }

        echo json_encode($data);     
    }

    public function allDrivers(Request $request){

        $columns = array( 
                            0 =>'logistics_company', 
                            1 => 'supplier_ids',
                            2 =>'first_name',
                            3 => 'last_name',
                            4 => 'mobile_number',
                            5 => 'id',
                            6 => 'company_id_number',
                            7 => 'license_number',
                            8 => 'status',
                            9 => 'dateOfSafetyOrientation'
                        );
        $totalData = Driver::count();
        

        $totalFiltered = $totalData; 
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        $drivers_suppliers = '';
        if(empty($request->input('search.value')))
        {            
            $drivers = Driver::offset($start)
                         ->limit($limit)
                         ->orderBy($order,$dir)
                         ->get();
            
        }
        else {
            $search = $request->input('search.value'); 

            $drivers =  Driver::where('id','LIKE',"%{$search}%")
                            ->where('logistics_company','LIKE',"%{$search}%")
                            ->where('first_name','LIKE',"%{$search}%")
                            ->where('last_name','LIKE',"%{$search}%")
                            ->where('mobile_number','LIKE',"%{$search}%")
                            ->where('company_id_number','LIKE',"%{$search}%")
                            ->where('license_number','LIKE',"%{$search}%")
                            ->where('dateOfSafetyOrientation','LIKE',"%{$search}%")
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();

            $totalFiltered = Driver::where('id','LIKE',"%{$search}%")
                            ->where('logistics_company','LIKE',"%{$search}%")
                            ->where('first_name','LIKE',"%{$search}%")
                            ->where('last_name','LIKE',"%{$search}%")
                            ->where('mobile_number','LIKE',"%{$search}%")
                            ->where('company_id_number','LIKE',"%{$search}%")
                            ->where('license_number','LIKE',"%{$search}%")
                            ->where('dateOfSafetyOrientation','LIKE',"%{$search}%")
                             ->count();
        }

        $data = array();
        if(!empty($drivers))
        {
            foreach ($drivers as $driver)
            {
              
                $supplier_ids = explode('|',$driver->supplier_ids);
                
                foreach($supplier_ids as $supplier_id){
                    if($supplier_id == null){
                        continue;
                    }
                    $suppliers = Supplier::where('id',$supplier_id)->where('status', 1)->first();
                    if($suppliers == null){
                        continue;
                    }
                    $drivers_suppliers .= $suppliers->supplier_name . " | ";
                }

                $nestedData['id'] = $driver->id;
                $nestedData['supplier_ids'] =  $drivers_suppliers;
                // $nestedData['supplier_name'] = substr(strip_tags($post->supplier_name),0,50)."...";
                $nestedData['logistics_company'] = $driver->logistics_company;
                $nestedData['first_name'] = $driver->first_name;
                $nestedData['mobile_number'] = $driver->mobile_number;
                $nestedData['last_name'] = $driver->last_name;
                $nestedData['company_id_number'] = $driver->company_id_number;
                $nestedData['license_number'] = $driver->license_number;

                // $nestedData['license_number'] = $driver->license_number;
                $nestedData['isApproved'] = $driver->isApproved == 0 ? "<b class='text-danger'>NO</b>" : "<b class='text-success'>YES</b>";
                $nestedData['dateOfSafetyOrientation'] = $driver->dateOfSafetyOrientation;

                if($driver->status == 1){

                    $nestedData['options'] = "<a href='javascript:void(0)' data-toggle='tooltip'  data-id='".$driver->id."' data-original-title='Edit' class='edit btn btn-primary btn-sm editProduct'>Edit</a>

                        <a href='javascript:void(0)' data-toggle='tooltip'  data-id='".$driver->id."' data-status='".$driver->status."'data-original-title='Delete' class='btn btn-danger btn-sm deactivateOrActivateDriver'>Deactivate</a>";
                }else{

                    $nestedData['options'] = "<a href='javascript:void(0)' data-toggle='tooltip'  data-id='".$driver->id."' data-original-title='Edit' class='edit btn btn-primary btn-sm editProduct'>Edit</a>

                        <a href='javascript:void(0)' data-toggle='tooltip'  data-id='".$driver->id."' data-status='".$driver->status."' data-original-title='Delete' class='btn btn-danger btn-sm deactivateOrActivateDriver'>Activate</a>";
                }
                $nestedData['status'] = $driver->status == 1 ? "<b class='text-success'>Active</b>" : "<b class='text-danger'>Inactive</b>";
                $data[] = $nestedData;
                $drivers_suppliers = '';
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

        Driver::updateOrCreate(['id' => $request->id],
                ['supplier_ids' => $request->supplier_ids, 'logistics_company' => $request->logistics_company, 'first_name' => $request->first_name, 'mobile_number' => $request->mobile_number, 'last_name' => $request->last_name, 'company_id_number' => $request->company_id_number, 'license_number' => $request->license_number, 'dateOfSafetyOrientation' => $request->dateOfSafetyOrientation, 'isApproved' => $request->isApproved]);        
   
        return response()->json(['success'=>'Driver saved successfully.']);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $driver = Driver::find($id);
        return response()->json($driver);
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
        Driver::find($id)->update(['status' => 0]);
        return response()->json(['success'=>'Driver deactivated successfully.']);
    }

    public function deactivateOrActivateDriver(Request $request)
    {
        //Supplier::find($id)->delete();
         $id = $request->id;
         $status =$request->status;
         if($status == 1){

            Driver::find($id)->update(['status' => 0]);
            return response()->json(['success'=>'Driver deactivated successfully.']);
        }elseif($status == 0){

            Driver::find($id)->update(['status' => 1]);
            return response()->json(['success'=>'Driver activated successfully.']);
        }
    }

    public function allsuppliers(){
        $data = Supplier::all();

         echo json_encode($data); 
    }

    public function completeDriverRegistration(Request $request)
    {
        $id = $request->id;

        Driver::find($id)->update(['dateOfSafetyOrientation' => $request->dateOfSafetyOrientation, 'isApproved' => 1]);        
   
        return response()->json(['success'=>$request->dateOfSafetyOrientation . ' Driver saved successfully.']);
    }

    public function export() 
    {
        return Excel::download(new DriverExport, 'drivers.xlsx');
    }
}
