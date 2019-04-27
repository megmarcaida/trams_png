<?php

namespace App\Http\Controllers;

use App\Supplier;
use Illuminate\Http\Request;

class SchedulerController extends Controller
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
    	$supplierData['data'] = Supplier::where("status",1)->get();

        return view('schedulers/slottingschedule')->with("supplierData",$supplierData);
    }
}
