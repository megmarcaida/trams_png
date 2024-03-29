<?php

namespace App\Http\Controllers;

use App\Assistant;
use App\Supplier;
use Illuminate\Http\Request;
use DataTables;
use Auth;
use Carbon\Carbon;
use App\Exports\AssistantExport;
use App\Imports\AssistantImport;
use Maatwebsite\Excel\Facades\Excel;

class AssistantController extends Controller
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
            $data = Assistant::all();
            return Datatables::of($data)->make(); 
            return view('recordmaintenance/assistant');
        }
        return view('recordmaintenance/assistant')->with("supplierData",$supplierData);
    }

     public function showPendingRegistrations()
    {
       $data = Assistant::where("isApproved",0)->get();
    
        echo json_encode($data);     
    }

    public function allAssistants(Request $request){

        $columns = array( 
                            0 =>'logistics_company', 
                            1 => 'supplier_ids',
                            2 =>'first_name',
                            3 => 'last_name',
                            4 => 'mobile_number',
                            5 => 'id',
                            // 6 => 'company_id_number',
                            // 7 => 'valid_id_present',
                            // 9 => 'valid_id_number',
                            10 => 'status',
                            11 => 'dateOfSafetyOrientation'
                        );
        $totalData = Assistant::count();
        

        $totalFiltered = $totalData; 
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        $assistants_suppliers = '';
        $assistants_s = "";
        if(empty($request->input('search.value')))
        {            
            $assistants = Assistant::offset($start)
                         ->limit($limit)
                         ->orderBy($order,$dir)
                         ->get();
            
        }
        else {
            $search = $request->input('search.value'); 

            $suppliers = Supplier::where('supplier_name','LIKE',"%{$search}%")->get();
            foreach ($suppliers as $key => $value) {
                $assistants_s .= $value->id ."|";
            }

            if($assistants_s != ""){

                $assistants =  Assistant::where('supplier_ids','LIKE',"%{$assistants_s}%")
                                ->offset($start)
                                ->limit($limit)
                                ->orderBy($order,$dir)
                                ->get();

                $totalFiltered = Assistant::where('supplier_ids','LIKE',"%{$assistants_s}%")
                             ->count();

            }else{
                if($search == "Approved"){
                    $isApproved = 1;
                }elseif($search == "Rejected"){
                    $isApproved = 0;
                }else{
                    $isApproved = null;
                }
                $assistants =  Assistant::where('id','LIKE',"%{$search}%")
                                ->orWhere('logistics_company','LIKE',"%{$search}%")
                                ->orWhere('first_name','LIKE',"%{$search}%")
                                ->orWhere('mobile_number','LIKE',"%{$search}%")
                                ->orWhere('full_name','LIKE',"%{$search}%")
                                ->orWhere('isApproved',$isApproved)
                                ->offset($start)
                                ->limit($limit)
                                ->orderBy($order,$dir)
                                ->get();

                $totalFiltered = Assistant::where('id','LIKE',"%{$search}%")
                             ->orWhere('logistics_company','LIKE',"%{$search}%")
                            ->orWhere('first_name','LIKE',"%{$search}%")
                            ->orWhere('mobile_number','LIKE',"%{$search}%")
                             ->count();

            }
        }

        $data = array();
        if(!empty($assistants))
        {
            foreach ($assistants as $assistant)
            {
                if(time() > strtotime($assistant->dateOfSafetyOrientation)){
                    Assistant::find($assistant->id)->update(["dateOfSafetyOrientation"=>null,"expirationDate"=>null,"isApproved"=>0]);
                }

                $supplier_ids = explode('|',$assistant->supplier_ids);
                
                foreach($supplier_ids as $supplier_id){
                    if($supplier_id == null){
                        continue;
                    }
                    $suppliers = Supplier::where('id',$supplier_id)->where('status', 1)->first();
                    if($suppliers == null){
                        continue;
                    }
                    $assistants_suppliers .= $suppliers->supplier_name . " | ";
                }
                $num = $assistant->id;
                $number = str_pad($num, 8, "0", STR_PAD_LEFT);
                $nestedData['id'] = $number;
                $nestedData['supplier_ids'] =  $assistants_suppliers;
                // $nestedData['supplier_name'] = substr(strip_tags($post->supplier_name),0,50)."...";
                $nestedData['logistics_company'] = $assistant->logistics_company;
                $nestedData['fullname'] = $assistant->first_name . " " . $assistant->last_name;
                $nestedData['mobile_number'] = $assistant->mobile_number;
                // $nestedData['company_id_number'] = $assistant->company_id_number;
                // $nestedData['valid_id_present'] = $assistant->valid_id_present;
                // $nestedData['valid_id_number'] = $assistant->valid_id_number;
                $nestedData['isApproved'] = $assistant->isApproved == 0 ? "<b class='text-danger'>NO</b>" : "<b class='text-success'>YES</b>";
                $nestedData['dateOfSafetyOrientation'] = $assistant->dateOfSafetyOrientation;

                // if($assistant->status == 1){

                //     $nestedData['options'] = "<a href='javascript:void(0)' data-toggle='tooltip'  data-id='".$assistant->id."' data-original-title='Edit' class='edit btn btn-primary btn-sm editProduct'>Edit</a>

                //         <a href='javascript:void(0)' data-toggle='tooltip'  data-id='".$assistant->id."' data-status='".$assistant->status."'data-original-title='Delete' class='btn btn-danger btn-sm deactivateOrActivateAssistant'>Deactivate</a>";
                // }else{

                //     $nestedData['options'] = "<a href='javascript:void(0)' data-toggle='tooltip'  data-id='".$assistant->id."' data-original-title='Edit' class='edit btn btn-primary btn-sm editProduct'>Edit</a>

                //         <a href='javascript:void(0)' data-toggle='tooltip'  data-id='".$assistant->id."' data-status='".$assistant->status."' data-original-title='Delete' class='btn btn-danger btn-sm deactivateOrActivateAssistant'>Activate</a>";
                // }
                $nestedData['status'] = $assistant->status == 1 ? "<b class='text-success'>Active</b>" : "<b class='text-danger'>Inactive</b>";
                $data[] = $nestedData;
                $assistants_suppliers = '';
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
        $assistant = Assistant::find(ltrim($request->id,0));
        $supplier_ids='';
        $supplier_names='';
        if($request->dateOfSafetyOrientation != null){
            
            $date = $request->dateOfSafetyOrientation;
            $date = strtotime($date);
            $expirationDate = strtotime("+7 day", $date);
            $expirationDate = date("Y-m-d H:i:s",$expirationDate);
            $dateOfSafetyOrientation = $request->dateOfSafetyOrientation;
            $isApproved = 1;
        }else{if($request->id != null){

                $expirationDate =$assistant->expirationDate;
                $dateOfSafetyOrientation = $assistant->dateOfSafetyOrientation;
                $isApproved = $assistant->isApproved;
            }else{
                $expirationDate = null;
                $dateOfSafetyOrientation = null;
                $isApproved = 0;
            }
        }

        foreach ($request->assistant_suppliers as $supplier){
            $supplier_ids .=  $supplier.'|';
            $supplier_name = Supplier::find($supplier);
            $supplier_names .= $supplier_name['supplier_name'] . '|';
        }

        Assistant::updateOrCreate(['id' => ltrim($request->id,0)],
                ['supplier_ids' => $supplier_ids, 'supplier_names' => $supplier_names, 'logistics_company' => $request->logistics_company, 'first_name' => $request->first_name, 'mobile_number' => $request->mobile_number, 'last_name' => $request->last_name, 'full_name' => $request->first_name . " " .$request->last_name, 'company_id_number' => "", 'valid_id_present' => "",'valid_id_number' => "", 'dateOfSafetyOrientation' => $dateOfSafetyOrientation, 'isApproved' => $isApproved,'expirationDate'=>$expirationDate]);        
   
        return response()->json(['success'=>'Assistant saved successfully.']);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $assistant = Assistant::find($id);
        return response()->json($assistant);
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
        Assistant::find($id)->update(['status' => 0]);
        return response()->json(['success'=>'Assistant deactivated successfully.']);
    }

    public function deactivateOrActivateAssistant(Request $request)
    {
        //Supplier::find($id)->delete();
         $id = $request->id;
         $status =$request->status;
         if($status == 1){

            Assistant::find($id)->update(['status' => 0]);
            return response()->json(['success'=>'Assistant deactivated successfully.']);
        }elseif($status == 0){

            Assistant::find($id)->update(['status' => 1]);
            return response()->json(['success'=>'Assistant activated successfully.']);
        }
    }

    public function allsuppliers(){
        $data = Supplier::all();

         echo json_encode($data); 
    }

    public function completeAssistantRegistration(Request $request)
    {
        $id = $request->id;
        $date = $request->dateOfSafetyOrientation;
        $date = strtotime($date);
        Assistant::find($id)->update(['dateOfSafetyOrientation' => $request->dateOfSafetyOrientation, 'isApproved' => 1]);        
   
        return response()->json(['success'=>$request->dateOfSafetyOrientation . ' Assistant saved successfully.']);
    }

    public function export() 
    {
        return Excel::download(new AssistantExport, 'assistants.xlsx');
    }

    public function import() 
    {
        Excel::import(new AssistantImport,request()->file('file'));
           
        return redirect()->back()->with("import_message","Importing of Assistants process successfully."); 
    }

     public function getAssistant(Request $request){
        $id = ltrim($request->id, '0');
        $assistant = Assistant::where("id",$id)->first();

        return json_encode($assistant);
    }
}
