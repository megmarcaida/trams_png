<?php

namespace App\Http\Controllers;

use App\User;
use App\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserRoleController extends Controller
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
            $data = User::all();


            return Datatables::of($data)->make(); 
            return view('masterfile/users',compact('users'));
        }

        return view('masterfile/users',compact('users'));
       
    }

    public function allUsers(Request $request){

        $columns = array( 
                            0 =>'email', 
                            1 => 'id',
                            2 => 'name',
                            3 => 'password',
                            4 => 'role_id',
                            
                        );
  
        $totalData = User::count();
            
        $totalFiltered = $totalData; 

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        
        if(empty($request->input('search.value')))
        {            
            $users = User::offset($start)
                         ->limit($limit)
                         ->orderBy($order,$dir)
                         ->get();

        }
        else {
            $search = $request->input('search.value'); 

            $users =  User::where('id','LIKE',"%{$search}%")
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();

            $totalFiltered = User::where('id','LIKE',"%{$search}%")
                             ->count();
        }

        $data = array();
        if(!empty($users))
        {
            foreach ($users as $user)
            {
            	$role = Role::where("id", $user->role_id)->first();
              
                $nestedData['id'] = $user->id;
                $nestedData['name'] = $user->name;
                $nestedData['password'] = $user->password;
                $nestedData['email'] = $user->email;
                $nestedData['role_id'] = $role->name;

                if($user->status == 1){

                    $nestedData['options'] = "<a href='javascript:void(0)' data-toggle='tooltip'  data-id='".$user->id."' data-original-title='Edit' class='edit btn btn-primary btn-sm editProduct'>Edit</a>

                        <a href='javascript:void(0)' data-toggle='tooltip'  data-id='".$user->id."' data-status='".$user->status."'data-original-title='Delete' class='btn btn-danger btn-sm deactivateOrActivateUser'>Deactivate</a>";
                }else{

                    $nestedData['options'] = "<a href='javascript:void(0)' data-toggle='tooltip'  data-id='".$user->id."' data-original-title='Edit' class='edit btn btn-primary btn-sm editProduct'>Edit</a>

                        <a href='javascript:void(0)' data-toggle='tooltip'  data-id='".$user->id."' data-status='".$user->status."' data-original-title='Delete' class='btn btn-danger btn-sm deactivateOrActivateUser'>Activate</a>";
                }
                $nestedData['status'] = $user->status == 1 ? "Active" : "Inactive";
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

        User::updateOrCreate(['id' => $request->id],
                ['name' => $request->name, 'email' => $request->email,'role_id' => $request->role_id, 'password' => Hash::make($request->password) ]);        
   
        return response()->json(['success'=>'User saved successfully.']);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = User::find($id);
        $data = array();
        
        	$role = Role::where("id", $user->role_id)->first();
          
            $nestedData['id'] = $user->id;
            $nestedData['name'] = $user->name;
            $nestedData['password'] = $user->password;
            $nestedData['email'] = $user->email;
            $nestedData['role_name'] = $role->name;
            $nestedData['role_id'] = $user->role_id;

            $data[] = $nestedData;
        
        return response()->json($data);
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
        User::find($id)->update(['status' => 0]);
        return response()->json(['success'=>'Supplier deactivated successfully.']);
    }

    public function deactivateOrActivateUser(Request $request)
    {
        //Supplier::find($id)->delete();
         $id = $request->id;
         $status =$request->status;
         if($status == 1){

            User::find($id)->update(['status' => 0]);
            return response()->json(['success'=>'User deactivated successfully.']);
        }elseif($status == 0){

            User::find($id)->update(['status' => 1]);
            return response()->json(['success'=>'User activated successfully.']);
        }
    }
}
