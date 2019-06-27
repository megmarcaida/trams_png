<?php

namespace App\Http\Controllers;

use App\Reason;
use Illuminate\Http\Request;
use DataTables;

class ReasonController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Reason::all();
            return Datatables::of($data)->make();
            return view('masterfile/reasons');
        }
        return view('masterfile/reasons');
    }

    public function allReason(Request $request){

        $columns = array( 
                            0 =>'reason_name', 
                            1 => 'description',
                            2 =>'id',
                            3 => 'tagging',
                            4 => 'status'
                        );
  
        $totalData = Reason::count();
            
        $totalFiltered = $totalData; 

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
       

        if(empty($request->input('search.value')))
        {            
            $reasons = Reason::offset($start)
                         ->limit($limit)
                         ->orderBy($order,$dir)
                         ->get();
        }
        else {
            $search = $request->input('search.value'); 

            $reasons =  Reason::where('id','LIKE',"%{$search}%")
                                ->orWhere('reason_name','LIKE',"%{$search}%")
                                ->orWhere('description','LIKE',"%{$search}%")
                                ->orWhere('tagging',$search)
                                ->offset($start)
                                ->limit($limit)
                                ->orderBy($order,$dir)
                                ->get();

            $totalFiltered = Reason::where('id','LIKE',"%{$search}%")
                                ->orWhere('reason_name','LIKE',"%{$search}%")
                                ->orWhere('description','LIKE',"%{$search}%")
                                ->orWhere('tagging',$search)
                                ->count();

            //}
        }

        $data = array();
        if(!empty($reasons))
        {
            foreach ($reasons as $reason)
            {
              
                $num = $reason->id;
                $number = str_pad($num, 8, "0", STR_PAD_LEFT);
                $nestedData['id'] = $number;
                $nestedData['reason_name'] = $reason->reason_name;
                $nestedData['description'] = $reason->description;
                $nestedData['tagging'] = $reason->tagging;
                $nestedData['status'] = $reason->status == 1 ? "Active" : "Inactive";
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

        $ret = ['success'=>'Reason saved successfully.'];
        Reason::updateOrCreate(['id' => $request->id],
                ['reason_name' => $request->reason_name, 'description' => $request->description, 'tagging' => $request->tagging]);  
                
   
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
        $reason = Reason::find($id);
        $data = array();
        if(!empty($reason))
        {
            $num = $reason['id'];
            $number = str_pad($num, 8, "0", STR_PAD_LEFT);
            $nestedData['id'] = $number;
            $nestedData['reason_name'] =  $reason['reason_name'];
            $nestedData['description'] = $reason['description'];
            $nestedData['tagging'] = $reason['tagging'];
            $data[] = $nestedData;
        }
          
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
        Reason::find($id)->update(['status' => 0]);
        return response()->json(['success'=>'Reason deactivated successfully.']);
    }

    public function deactivateOrActivateReason(Request $request)
    {
        //Supplier::find($id)->delete();
         $id = $request->id;
         $status =$request->status;
         if($status == 1){

            Reason::find($id)->update(['status' => 0]);
            return response()->json(['success'=>'Reason deactivated successfully.']);
        }elseif($status == 0){

            Reason::find($id)->update(['status' => 1]);
            return response()->json(['success'=>'Reason activated successfully.']);
        }
    }

    public function getReason(Request $request){
        $id = ltrim($request->id, '0');
        $reason = Reason::where("id",$id)->first();

        return json_encode($reason);
    }
}
