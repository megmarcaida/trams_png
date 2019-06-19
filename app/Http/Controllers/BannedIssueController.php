<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\BannedIssue;
class BannedIssueController extends Controller
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
            $data = BannedIssue::all();
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
      
        return view('dashboard/bannedissue');
    }

    public function allBannedIssue(Request $request){

        $columns = array( 
                            0 =>'name', 
                            1 =>'location',
                            2 => 'date',
                            3 => 'time',
                            4 => 'id',
                            5 => 'violation',
                            6 => 'reason',
                            7 => 'additional_information',
                            8 => 'supplier',
                            10 => 'created_at',
                            11 => 'status'
                        );
  
        $totalData = BannedIssue::count();
            
        $totalFiltered = $totalData; 

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
            
        if(empty($request->input('search.value')))
        {            
            $suppliers = BannedIssue::offset($start)
                         ->limit($limit)
                         ->orderBy($order,$dir)
                         ->get();
        }
        else {
            $search = $request->input('search.value'); 
            
            $suppliers =  BannedIssue::where('id','LIKE',"%{$search}%")
                            ->orWhere('name','LIKE',"%{$search}%")
                            ->orWhere('location','LIKE',"%{$search}%")
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();



            $totalFiltered = BannedIssue::where('id','LIKE',"%{$search}%")
                             ->orWhere('name','LIKE',"%{$search}%")
                            ->orWhere('location','LIKE',"%{$search}%")
                            ->count();
        }

        $data = array();
        if(!empty($suppliers))
        {
            foreach ($suppliers as $supplier)
            {
              

                $nestedData['id'] = $supplier->id;
                $nestedData['name'] = $supplier->name;
                // $nestedData['supplier_name'] = substr(strip_tags($post->supplier_name),0,50)."...";
                $nestedData['location'] = $supplier->location;
                $nestedData['violation'] = $supplier->violation;
                $nestedData['date'] = date('j M Y',strtotime($supplier->date_time));
                $nestedData['time'] = date('h:i a',strtotime($supplier->date_time));
                $nestedData['reason'] = $supplier->reason;
                $nestedData['additional_information'] = $supplier->additional_information;
                $nestedData['supplier'] = "";
                
                $nestedData['created_at'] = date('j M Y h:i a',strtotime($supplier->created_at));
                // if($supplier->status == 1){

                //     $nestedData['options'] = "<a href='javascript:void(0)' data-toggle='tooltip'  data-id='".$supplier->id."' data-original-title='Edit' class='edit btn btn-primary btn-sm editProduct'>Edit</a>

                //         <a href='javascript:void(0)' data-toggle='tooltip'  data-id='".$supplier->id."' data-status='".$supplier->status."'data-original-title='Delete' class='btn btn-danger btn-sm deactivateOrActivateBannedIssue'>Deactivate</a>";
                // }else{

                //     $nestedData['options'] = "<a href='javascript:void(0)' data-toggle='tooltip'  data-id='".$supplier->id."' data-original-title='Edit' class='edit btn btn-primary btn-sm editProduct'>Edit</a>

                //         <a href='javascript:void(0)' data-toggle='tooltip'  data-id='".$supplier->id."' data-status='".$supplier->status."' data-original-title='Delete' class='btn btn-danger btn-sm deactivateOrActivateBannedIssue'>Activate</a>";
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

    $ret = ['success'=>'Supplier saved successfully.'];
    BannedIssue::updateOrCreate(['id' => $request->bannedissue_id],
        ['name' => $request->name, 'location' => $request->location, 'violation' => $request->violation, 'date_time' => $request->date_time, 'reason' => $request->reason, 'additional_information' => $request->additional_information]);        
      

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
        $supplier = BannedIssue::find($id);
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
        //BannedIssue::find($id)->delete();
        BannedIssue::find($id)->update(['status' => 0]);
        return response()->json(['success'=>'Data deactivated successfully.']);
    }

    public function deactivateOrActivateBannedIssue(Request $request)
    {
        //BannedIssue::find($id)->delete();
         $id = $request->id;
         $status = $request->status;
         if($status == 1){

            BannedIssue::find($id)->update(['status' => 0]);
            return response()->json(['success'=>'Data deactivated successfully.']);
        }elseif($status == 0){

            BannedIssue::find($id)->update(['status' => 1]);
            return response()->json(['success'=>'Data activated successfully.']);
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
    public function getBannedIssue(Request $request){
        $bannedissue = BannedIssue::where("id",$request->id)->first();

        return json_encode($bannedissue);
    }
}
