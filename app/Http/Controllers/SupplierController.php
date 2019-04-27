<?php

namespace App\Http\Controllers;

use App\Supplier;
use Illuminate\Http\Request;
use DataTables;
use App\Exports\SupplierExport;
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
                            6 => 'spoc_firstname',
                            7 => 'spoc_lastname',
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

            $suppliers =  Supplier::where('id','LIKE',"%{$search}%")
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();

            $totalFiltered = Supplier::where('id','LIKE',"%{$search}%")
                             ->count();
        }

        $data = array();
        if(!empty($suppliers))
        {
            foreach ($suppliers as $supplier)
            {
              

                $nestedData['id'] = $supplier->id;
                $nestedData['vendor_code'] = $supplier->vendor_code;
                // $nestedData['supplier_name'] = substr(strip_tags($post->supplier_name),0,50)."...";
                $nestedData['supplier_name'] = $supplier->supplier_name;
                $nestedData['delivery_type'] = $supplier->delivery_type;
                $nestedData['ordering_days'] = $supplier->ordering_days;
                $nestedData['module'] = $supplier->module;
                $nestedData['spoc_firstname'] = $supplier->spoc_firstname;
                $nestedData['spoc_lastname'] = $supplier->spoc_lastname;
                $nestedData['spoc_contact_number'] = $supplier->spoc_contact_number;
                $nestedData['spoc_email_address'] = $supplier->spoc_email_address;
                $nestedData['created_at'] = date('j M Y h:i a',strtotime($supplier->created_at));
                if($supplier->status == 1){

                    $nestedData['options'] = "<a href='javascript:void(0)' data-toggle='tooltip'  data-id='".$supplier->id."' data-original-title='Edit' class='edit btn btn-primary btn-sm editProduct'>Edit</a>

                        <a href='javascript:void(0)' data-toggle='tooltip'  data-id='".$supplier->id."' data-status='".$supplier->status."'data-original-title='Delete' class='btn btn-danger btn-sm deactivateOrActivateSupplier'>Deactivate</a>";
                }else{

                    $nestedData['options'] = "<a href='javascript:void(0)' data-toggle='tooltip'  data-id='".$supplier->id."' data-original-title='Edit' class='edit btn btn-primary btn-sm editProduct'>Edit</a>

                        <a href='javascript:void(0)' data-toggle='tooltip'  data-id='".$supplier->id."' data-status='".$supplier->status."' data-original-title='Delete' class='btn btn-danger btn-sm deactivateOrActivateSupplier'>Activate</a>";
                }
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

        foreach ($request->ordering_days as $ordering_day){
            $ordering_days .=  $ordering_day.' | ';
        }

         foreach ($request->module as $module){
            $modules .=  $module.' | ';
        }

        foreach ($request->spoc_first_name as $fname){
            $spoc_first_name .=  $fname.' |<br> ';
        }

        foreach ($request->spoc_last_name as $lname){
            $spoc_last_name .=  $lname.' |<br> ';
        }

        foreach ($request->spoc_contact_number as $cnumber){
            $spoc_contact_number .=  $cnumber.' |<br> ';
        }

        foreach ($request->spoc_email_address as $email){
            $spoc_email_address .=  $email.' |<br> ';
        }


        Supplier::updateOrCreate(['id' => $request->supplier_id],
                ['vendor_code' => $request->vendor_code, 'supplier_name' => $request->supplier_name, 'delivery_type' => $request->delivery_types, 'ordering_days' => $ordering_days, 'module' => $modules, 'spoc_firstname' => $spoc_first_name, 'spoc_lastname' => $spoc_last_name, 'spoc_contact_number' => $spoc_contact_number, 'spoc_email_address' => $spoc_email_address]);        
   
        return response()->json(['success'=>'Supplier saved successfully.']);
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
   
}
