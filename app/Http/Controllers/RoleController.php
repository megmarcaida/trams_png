<?php

namespace App\Http\Controllers;

use App\Role;
use App\Dock;
use Illuminate\Http\Request;
use Datatables;

class RoleController extends Controller
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
        if ($request->ajax()) {
            $data = Role::all();
            return Datatables::of($data)->make(); 
            return view('masterfile/role');
        }

        $dockData['data'] = Dock::where("status",1)->get();

        return view('masterfile/role')->with(['dockData'=>$dockData]);
       
    }

    public function allRoles(Request $request){

        $columns = array( 
                            0 =>'description', 
                            1 => 'id',
                            2 => 'name',
                            3 => 'status',
                            
                        );
  
        $totalData = Role::count();
            
        $totalFiltered = $totalData; 

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        
        if(empty($request->input('search.value')))
        {            
            $roles = Role::offset($start)
                         ->limit($limit)
                         ->orderBy($order,$dir)
                         ->get();

        }
        else {
            $search = $request->input('search.value'); 

            $roles =  Role::where('id','LIKE',"%{$search}%")
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();

            $totalFiltered = Role::where('id','LIKE',"%{$search}%")
                             ->count();
        }

        $data = array();
        if(!empty($roles))
        {
            foreach ($roles as $role)
            {
              
                $nestedData['id'] = $role->id;
                $nestedData['description'] = $role->description;
                $nestedData['name'] = $role->name;
                $nestedData['submodules'] = $role->submodules;
                // if($role->status == 1){

                //     $nestedData['options'] = "<a href='javascript:void(0)' data-toggle='tooltip'  data-id='".$role->id."' data-original-title='Edit' class='edit btn btn-primary btn-sm editProduct'>Edit</a>

                //         <a href='javascript:void(0)' data-toggle='tooltip'  data-id='".$role->id."' data-status='".$role->status."'data-original-title='Delete' class='btn btn-danger btn-sm deactivateOrActivateRole'>Deactivate</a>";
                // }else{

                //     $nestedData['options'] = "<a href='javascript:void(0)' data-toggle='tooltip'  data-id='".$role->id."' data-original-title='Edit' class='edit btn btn-primary btn-sm editProduct'>Edit</a>

                //         <a href='javascript:void(0)' data-toggle='tooltip'  data-id='".$role->id."' data-status='".$role->status."' data-original-title='Delete' class='btn btn-danger btn-sm deactivateOrActivateRole'>Activate</a>";
                // }
                $nestedData['status'] = $role->status == 1 ? "Active" : "Inactive";
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
        $submodules = "";
        if($request->submodules != null){
            foreach($request->submodules as $value){
                    $submodules .= $value . "|";
            }
        }

        Role::updateOrCreate(['id' => $request->id],
                ['name' => $request->name, 'description' => $request->description, 'submodules'=> $submodules]);        
   
        return response()->json(['success'=>'Role saved successfully.']);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $role = Role::find($id);
        return response()->json($role);
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
        Role::find($id)->update(['status' => 0]);
        return response()->json(['success'=>'Supplier deactivated successfully.']);
    }

    public function deactivateOrActivateRole(Request $request)
    {
        //Supplier::find($id)->delete();
         $id = $request->id;
         $status =$request->status;
         if($status == 1){

            Role::find($id)->update(['status' => 0]);
            return response()->json(['success'=>'Role deactivated successfully.']);
        }elseif($status == 0){

            Role::find($id)->update(['status' => 1]);
            return response()->json(['success'=>'Role activated successfully.']);
        }
    }

    public function getRole(Request $request){
        $id = ltrim($request->id, '0');
        $role = Role::where("id",$id)->first();

        return json_encode($role);
    }
}
