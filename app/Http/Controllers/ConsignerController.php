<?php

namespace App\Http\Controllers;

use App\Models\Consigner;
use Illuminate\Http\Request;
use App\Models\Branch;
use App\Models\State;
use DB;
use URL;
use Auth;
use Helper;
use Validator;

class ConsignerController extends Controller
{
    public function __construct()
    {
      $this->title =  "Consigners";
      $this->segment = \Request::segment(2);

    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->prefix = request()->route()->getPrefix();
        $query = Consigner::query();
        $authuser = Auth::user();
        $cc = explode(',',$authuser->branch_id);
        if($authuser->role_id == 2){
            $consigners = $query->whereIn('branch_id',$cc)->orderBy('id','DESC')->with('State')->get();
        }else{
            $consigners = $query->orderBy('id','DESC')->with('State')->get();
        }
        return view('consigners.consigner-list',['consigners'=>$consigners,'prefix'=>$this->prefix,'title'=>$this->title])->with('i', ($request->input('page', 1) - 1) * 5);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->prefix = request()->route()->getPrefix();
        $states = Helper::getStates();
        $branches = Helper::getLocations();
        return view('consigners.create-consigner',['states'=>$states, 'branches'=>$branches, 'prefix'=>$this->prefix, 'title'=>$this->title, 'pagetitle'=>'Create']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->prefix = request()->route()->getPrefix();
        $rules = array(
            'nick_name' => 'required',
            // 'email' => 'required|unique:branches',
        );
        $validator = Validator::make($request->all(),$rules);
    
        if($validator->fails())
        {
            $errors                  = $validator->errors();
            $response['success']     = false;
            $response['validation']  = false;
            $response['formErrors']  = true;
            $response['errors']      = $errors;
            return response()->json($response);
        }
        $consignersave['nick_name']    = $request->nick_name;
        $consignersave['legal_name']   = $request->legal_name;
        $consignersave['gst_number']   = $request->gst_number;
        $consignersave['contact_name'] = $request->contact_name;
        $consignersave['phone']        = $request->phone;
        $consignersave['branch_id']    = $request->branch_id;
        $consignersave['email']        = $request->email;
        $consignersave['address']      = $request->address;
        $consignersave['city']         = $request->city;
        $consignersave['district']     = $request->district;
        $consignersave['postal_code']  = $request->postal_code;
        $consignersave['state_id']     = $request->state_id;
        // $consignersave['status']       = $request->status;

        $saveconsigner = Consigner::create($consignersave); 
        if($saveconsigner)
        {
            $response['success'] = true;
            $response['error']   = false;
            $response['page']    = 'consigner-create';
            $response['success_message'] = "Consigner Added successfully";
            $response['redirect_url'] = URL::to($this->prefix.'/consigners');
            // $response['resetform'] = true;
        }else{
            $response['success'] = false;
            $response['error_message'] = "Can not created consigner please try again";
            $response['error'] = true;
        }
        return response()->json($response);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Consigner  $consigner
     * @return \Illuminate\Http\Response
     */
    public function show($consigner)
    {
        $this->prefix = request()->route()->getPrefix();
        $id = decrypt($consigner);
        $getconsigner = Consigner::where('id',$id)->with('GetBranch','GetState')->first();
        return view('consigners.view-consigner',['prefix'=>$this->prefix,'title'=>$this->title, 'pagetitle'=>'View Details', 'getconsigner'=>$getconsigner]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Consigner  $consigner
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->prefix = request()->route()->getPrefix();
        $id = decrypt($id);      
        $states = Helper::getStates();
        $branches = Helper::getLocations();
        $getconsigner = Consigner::where('id',$id)->first();
        return view('consigners.update-consigner')->with(['prefix'=>$this->prefix,'getconsigner'=>$getconsigner,'states'=>$states,'branches'=>$branches, 'title'=>$this->title, 'pagetitle'=>'Update']);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Consigner  $consigner
     * @return \Illuminate\Http\Response
     */
    public function updateConsigner(Request $request)
    {
        try { 
            $this->prefix = request()->route()->getPrefix();
             $rules = array(
              'nick_name' => 'required',
            //   'email'  => 'required',
            );

            $validator = Validator::make($request->all(),$rules);

            if($validator->fails())
            {
                $errors                  = $validator->errors();
                $response['success']     = false;
                $response['formErrors']  = true;
                $response['errors']      = $errors;
                return response()->json($response);
            }

            $consignersave['nick_name']    = $request->nick_name;
            $consignersave['legal_name']   = $request->legal_name;
            $consignersave['gst_number']   = $request->gst_number;
            $consignersave['contact_name'] = $request->contact_name;
            $consignersave['phone']        = $request->phone;
            $consignersave['branch_id']    = $request->branch_id;
            $consignersave['email']        = $request->email;
            $consignersave['address']      = $request->address;
            $consignersave['city']         = $request->city;
            $consignersave['district']     = $request->district;
            $consignersave['postal_code']  = $request->postal_code;
            $consignersave['state_id']     = $request->state_id;
            // $consignersave['status']       = $request->status;
            
            Consigner::where('id',$request->consigner_id)->update($consignersave);
            $url    =   URL::to($this->prefix.'/consigners');

            $response['page'] = 'consigner-update';
            $response['success'] = true;
            $response['success_message'] = "Consigner Updated Successfully";
            $response['error'] = false;
            // $response['html'] = $html;
            $response['redirect_url'] = $url;
        }catch(Exception $e) {
            $response['error'] = false;
            $response['error_message'] = $e;
            $response['success'] = false;
            $response['redirect_url'] = $url;   
        }
        return response()->json($response);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Consigner  $consigner
     * @return \Illuminate\Http\Response
     */
    public function deleteConsigner(Request $request)
    {
        Consigner::where('id',$request->consignerid)->delete();

        $response['success']         = true;
        $response['success_message'] = 'Consigner deleted successfully';
        $response['error']           = false;
        return response()->json($response);
    }
}
