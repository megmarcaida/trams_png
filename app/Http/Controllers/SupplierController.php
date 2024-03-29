<?php

namespace App\Http\Controllers;

use App\Supplier;
use Illuminate\Http\Request;
use DataTables;
use App\Exports\SupplierExport;
use App\Imports\SupplierImport;
use Maatwebsite\Excel\Facades\Excel;

class SupplierController extends Controller
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
            $data = Supplier::all();
            return Datatables::of($data)->make();
                    // ->addIndexColumn()
                    // ->addColumn('action', function($row){
   
                    //        $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="'.$row->id.'" data-original-title="Edit" class="edit btn btn-primary btn-sm editProduct">Edit</a>';
   
                    //        $btn = $btn.' <a href="javascript:void(0)" data-toggle="tooltip"  data-id="'.$row->id.'" data-original-title="Delete" class="btn btn-danger btn-sm deleteProduct">Delete</a>';
    
                    //         return $btn;
                    // })
                    // ->rawColumns(['action'])
                    // ->make(true);
        }
      
        return view('recordmaintenance/supplier');
    }

    public function allSuppliers(Request $request){

        $columns = array( 
                            0 =>'vendor_code', 
                            1 =>'supplier_name',
                            2 => 'delivery_type',
                            3 => 'ordering_days',
                            4 => 'id',
                            5 => 'module',
                            6 => 'spoc_fullname',
                            8 => 'spoc_contact_number',
                            9 => 'spoc_email_address',
                            10 => 'created_at',
                            11 => 'status'
                        );
  
        $totalData = Supplier::count();
            
        $totalFiltered = $totalData; 

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
            
        if(empty($request->input('search.value')))
        {            
            $suppliers = Supplier::offset($start)
                         ->limit($limit)
                         ->orderBy($order,$dir)
                         ->get();
        }
        else {
            $search = $request->input('search.value'); 
            
            if(strpos($search, "|") !== false ){
                $keywords = explode("|", $search);
                $filter = "";
                $suppliers = Supplier::where(function($query) use($keywords){
                    foreach ($keywords as $key => $value) {
                        if($key == 0){
                            $filter = $value;
                        }else{

                            $query->Where($filter, 'like',  '%' . $value .'%');
                        }
                    }
                })->get();

            }else{

            $status = 0;
            switch ($search) {
                case 'Active':
                    $status = 1;
                    break;
                case 'Inactive':
                    $status = 0;
                    break;
                
                default:
                    $status = 0;
                    break;
            }

                if($search == "Active" || $search == "Inactive"){

                    $suppliers =  Supplier::where('id','LIKE',"%{$search}%")
                                ->orWhere('vendor_code','LIKE',"%{$search}%")
                                ->orWhere('supplier_name','LIKE',"%{$search}%")
                                ->orWhere('spoc_full_name','LIKE',"%{$search}%")
                                ->orWhere('spoc_email_address','LIKE',"%{$search}%")
                                ->orWhere('spoc_contact_number','LIKE',"%{$search}%")
                                ->orWhere('ordering_days','LIKE',"%{$search}%")
                                ->orWhere('delivery_type','LIKE',"%{$search}%")
                                ->orWhere('module','LIKE',"%{$search}%")
                                ->orWhere('status',$status)
                                ->offset($start)
                                ->limit($limit)
                                ->orderBy($order,$dir)
                                ->get();

                                $totalFiltered = Supplier::where('id','LIKE',"%{$search}%")
                                ->orWhere('vendor_code','LIKE',"%{$search}%")
                                ->orWhere('supplier_name','LIKE',"%{$search}%")
                                ->orWhere('spoc_full_name','LIKE',"%{$search}%")
                                ->orWhere('spoc_email_address','LIKE',"%{$search}%")
                                ->orWhere('spoc_contact_number','LIKE',"%{$search}%")
                                ->orWhere('ordering_days','LIKE',"%{$search}%")
                                ->orWhere('delivery_type','LIKE',"%{$search}%")
                                ->orWhere('module','LIKE',"%{$search}%")
                                ->orWhere('status',$status)
                                ->count();
                }else{

                    $suppliers =  Supplier::where('id','LIKE',"%{$search}%")
                                ->orWhere('vendor_code','LIKE',"%{$search}%")
                                ->orWhere('supplier_name','LIKE',"%{$search}%")
                                ->orWhere('spoc_full_name','LIKE',"%{$search}%")
                                ->orWhere('spoc_email_address','LIKE',"%{$search}%")
                                ->orWhere('spoc_contact_number','LIKE',"%{$search}%")
                                ->orWhere('ordering_days','LIKE',"%{$search}%")
                                ->orWhere('delivery_type','LIKE',"%{$search}%")
                                ->orWhere('module','LIKE',"%{$search}%")
                                ->orWhere('status','LIKE',"%{$search}%")
                                ->offset($start)
                                ->limit($limit)
                                ->orderBy($order,$dir)
                                ->get();

                    $totalFiltered = Supplier::where('id','LIKE',"%{$search}%")
                                ->orWhere('vendor_code','LIKE',"%{$search}%")
                                ->orWhere('supplier_name','LIKE',"%{$search}%")
                                ->orWhere('spoc_full_name','LIKE',"%{$search}%")
                                ->orWhere('spoc_email_address','LIKE',"%{$search}%")
                                ->orWhere('spoc_contact_number','LIKE',"%{$search}%")
                                ->orWhere('ordering_days','LIKE',"%{$search}%")
                                ->orWhere('delivery_type','LIKE',"%{$search}%")
                                ->orWhere('module','LIKE',"%{$search}%")
                                ->orWhere('status','LIKE',"%{$search}%")
                                ->count();
                }

            }   
        }

        $data = array();
        if(!empty($suppliers))
        {
            foreach ($suppliers as $supplier)
            {
              
                $num = $supplier->id;
                $number = str_pad($num, 8, "0", STR_PAD_LEFT);
                $nestedData['id'] = $number;
                $nestedData['vendor_code'] = $supplier->vendor_code;
                // $nestedData['supplier_name'] = substr(strip_tags($post->supplier_name),0,50)."...";
                $nestedData['supplier_name'] = $supplier->supplier_name;
                $nestedData['delivery_type'] = $supplier->delivery_type;
                $nestedData['ordering_days'] = $supplier->ordering_days;
                $nestedData['module'] = $supplier->module;
                // $nestedData['spoc_fullname'] = str_replace("|", "", $supplier->spoc_firstname) . " " . str_replace("|", "",$supplier->spoc_lastname) . " | ";
               
                // $nestedData['spoc_fullname'] = $supplier->spoc_full_name;
                // // $nestedData['spoc_lastname'] = $supplier->spoc_lastname;
                // $nestedData['spoc_contact_number'] = $supplier->spoc_contact_number;
                // $nestedData['spoc_email_address'] = $supplier->spoc_email_address;
                $nestedData['created_at'] = date('j M Y h:i a',strtotime($supplier->created_at));
                // if($supplier->status == 1){

                //     $nestedData['options'] = "<a href='javascript:void(0)' data-toggle='tooltip'  data-id='".$supplier->id."' data-original-title='Edit' class='edit btn btn-primary btn-sm editProduct'>Edit</a>

                //         <a href='javascript:void(0)' data-toggle='tooltip'  data-id='".$supplier->id."' data-status='".$supplier->status."'data-original-title='Delete' class='btn btn-danger btn-sm deactivateOrActivateSupplier'>Deactivate</a>";
                // }else{

                //     $nestedData['options'] = "<a href='javascript:void(0)' data-toggle='tooltip'  data-id='".$supplier->id."' data-original-title='Edit' class='edit btn btn-primary btn-sm editProduct'>Edit</a>

                //         <a href='javascript:void(0)' data-toggle='tooltip'  data-id='".$supplier->id."' data-status='".$supplier->status."' data-original-title='Delete' class='btn btn-danger btn-sm deactivateOrActivateSupplier'>Activate</a>";
                // }
                $nestedData['status'] = $supplier->status == 1 ? "Active" : "Inactive";
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

        $ordering_days='';
        $modules = '';
        $spoc_first_name = '';
        $spoc_last_name = '';
        $spoc_contact_number = '';
        $spoc_email_address = '';
        $spoc_fullname = '';

        foreach ($request->ordering_days as $ordering_day){
            $ordering_days .=  $ordering_day.' | ';
        }

         foreach ($request->module as $module){
            $modules .=  $module.' | ';
        }

        foreach ($request->spoc_first_name as $fname){
            $spoc_first_name .=  $fname.' <br> ';
        }

        foreach ($request->spoc_last_name as $lname){
            $spoc_last_name .=  $lname.' <br> ';
        }

        foreach ($request->spoc_first_name as $fname){
            $spoc_fullname .=  $fname. ' '. $lname .' <br> ';
        }


        foreach ($request->spoc_contact_number as $cnumber){
            $spoc_contact_number .=  $cnumber.' <br> ';
        }

        foreach ($request->spoc_email_address as $email){
            $spoc_email_address .=  $email.' <br> ';
        }

        $isExistVendorCode = Supplier::where("vendor_code",$request->vendor_code)->first();

        $isExist = Supplier::find($request->supplier_id);

        if($isExistVendorCode && !$isExist){
            $ret = ['error'=>'Vendor Code already exists.'];
        }else{
            $ret = ['success'=>'Supplier saved successfully.'];
            Supplier::updateOrCreate(['id' => $request->supplier_id],
                ['vendor_code' => $request->vendor_code, 'supplier_name' => $request->supplier_name, 'delivery_type' => $request->delivery_types, 'ordering_days' => $ordering_days, 'module' => $modules, 'spoc_firstname' => $spoc_first_name, 'spoc_lastname' => $spoc_last_name, 'spoc_full_name' => $spoc_fullname,'spoc_contact_number' => $spoc_contact_number, 'spoc_email_address' => $spoc_email_address]);        
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
        $supplier = Supplier::find($id);
        return response()->json($supplier);
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

    public function deactivateOrActivateSupplier(Request $request)
    {
        //Supplier::find($id)->delete();
         $id = $request->id;
         $status = $request->status;
         if($status == 1){

            Supplier::find($id)->update(['status' => 0]);
            return response()->json(['success'=>'Supplier deactivated successfully.']);
        }elseif($status == 0){

            Supplier::find($id)->update(['status' => 1]);
            return response()->json(['success'=>'Supplier activated successfully.']);
        }

       //  $response = array(
       //      'status' => $request->status,
       //      'id' => $request->id,
       //  );

       // echo json_encode($response); 
    }

    public function export() 
    {
        return Excel::download(new SupplierExport, 'suppliers.xlsx');
    }

     public function import() 
    {
        Excel::import(new SupplierImport,request()->file('file'));
       
        return redirect()->back()->with("import_message","Importing of Suppliers process successfully."); 
    }

    public function getSupplier(Request $request){
        $supplier = Supplier::where("vendor_code",$request->vendor_code)->first();

        return json_encode($supplier);
    }

    public function getAllSupplier(Request $request){
        $supplier = Supplier::where("status","<>","0")->get();

        return json_encode($supplier);
    }
   
}
