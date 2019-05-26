<?php

namespace App\Http\Controllers;

use App\Dock;
use App\Role;
use Illuminate\Http\Request;
use DataTables;

class DockController extends Controller
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
            $data = Dock::all();
            return Datatables::of($data)->make();
        }
      
        return view('schedulers/dock');
    }

    public function allDockers(Request $request){

        $columns = array( 
                            0 =>'dock_name', 
                            1 =>'module',
                            10 => 'created_at',
                            11 => 'status'
                        );
  
        $totalData = Dock::count();
            
        $totalFiltered = $totalData; 

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
            
        if(empty($request->input('search.value')))
        {            
            $docks = Dock::offset($start)
                         ->limit($limit)
                         ->orderBy($order,$dir)
                         ->get();
        }
        else {
            $search = $request->input('search.value'); 
            
            $docks =  Dock::where('id','LIKE',"%{$search}%")
                            ->orWhere('dock_name','LIKE',"%{$search}%")
                            ->orWhere('module','LIKE',"%{$search}%")
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();



            $totalFiltered = Dock::where('id','LIKE',"%{$search}%")
                            ->orWhere('dock_name','LIKE',"%{$search}%")
                            ->orWhere('module','LIKE',"%{$search}%")
                            ->count();
        }

        $data = array();
        if(!empty($docks))
        {
            foreach ($docks as $dock)
            {
                $role = Role::where("id",$dock->user_type)->first();
                $num = $dock->id;
                $number = str_pad($num, 8, "0", STR_PAD_LEFT);
                $nestedData['id'] = $number;
                $nestedData['dock_name'] = $dock->dock_name;
                $nestedData['module'] = $dock->module;
                if($role){

                    $nestedData['user_type'] = $role['name'];
                }else{

                    $nestedData['user_type'] = "";
                }
                
                $nestedData['created_at'] = date('j M Y h:i a',strtotime($dock->created_at));
                // if($dock->status == 1){

                //     $nestedData['options'] = "<a href='javascript:void(0)' data-toggle='tooltip'  data-id='".$dock->id."' data-original-title='Edit' class='edit btn btn-primary btn-sm editProduct'>Edit</a>

                //         <a href='javascript:void(0)' data-toggle='tooltip'  data-id='".$dock->id."' data-status='".$dock->status."'data-original-title='Delete' class='btn btn-danger btn-sm deactivateOrActivateDocker'>Deactivate</a>";
                // }else{

                //     $nestedData['options'] = "<a href='javascript:void(0)' data-toggle='tooltip'  data-id='".$dock->id."' data-original-title='Edit' class='edit btn btn-primary btn-sm editProduct'>Edit</a>

                //         <a href='javascript:void(0)' data-toggle='tooltip'  data-id='".$dock->id."' data-status='".$dock->status."' data-original-title='Delete' class='btn btn-danger btn-sm deactivateOrActivateDocker'>Activate</a>";
                // }
                $nestedData['status'] = $dock->status == 1 ? "Active" : "Inactive";
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

        $isExistDock = Dock::where("dock_name",$request->dock_name)->first();

        $isExist = Dock::find($request->dock_id);

        if($isExistDock && !$isExist){
            $ret = ['error'=>'Dock already exists.'];
        }else{
            $ret = ['success'=>'Dock saved successfully.'];
            Dock::updateOrCreate(['id' => $request->dock_id],
                ['dock_name' => $request->dock_name, 'module' => $request->module,'user_type' => $request->user_type]);        
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
        $dock = Dock::find($id);
        return response()->json($dock);
    }
  
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy($id,$status)
    {
        Dock::find($id)->update(['status' => 0]);
        return response()->json(['success'=>'Dock deactivated successfully.']);
    }

    public function deactivateOrActivateDocker(Request $request)
    {
         $id = $request->id;
         $status = $request->status;
         if($status == 1){

            Dock::find($id)->update(['status' => 0]);
            return response()->json(['success'=>'Dock deactivated successfully.']);
        }elseif($status == 0){

            Dock::find($id)->update(['status' => 1]);
            return response()->json(['success'=>'Dock activated successfully.']);
        }
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

    public function getDock(Request $request){
        $id = ltrim($request->id, '0');
        $truck = Dock::where("id",$id)->first();

        return json_encode($truck);
    }

    public function getUserType(){
        $roles = Role::all();

        return json_encode($roles);
    }
}
