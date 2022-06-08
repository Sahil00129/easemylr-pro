<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ConsignmentNote;
use App\Models\ConsignmentItem;
use App\Models\Consigner;
use App\Models\Consignee;
use App\Models\Branch;
use App\Models\Vehicle;
use App\Models\VehicleType;
use App\Models\BranchAddress;
use Auth;
use DB;
use Crypt;
use Helper;
use Validator;
Use PDF;
use PDFMerger;

class ConsignmentController extends Controller
{

    public function __construct()
    {
      $this->title =  "Consignments Listing";
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
        $peritem = 20;
        $query = ConsignmentNote::query();
        $consignments = $query->orderby('id','DESC')->paginate($peritem);
        return view('consignments.consignment-list',['consignments'=>$consignments,'prefix'=>$this->prefix])
            ->with('i', ($request->input('page', 1) - 1) * 5);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->prefix = request()->route()->getPrefix();
        $authuser = Auth::user();
        $cc = explode(',',$authuser->branch_id);
        if($authuser->role_id == 2){
            $consigners = Consigner::select('id','nick_name')->whereIn('branch_id',$cc)->get();
            $consignees = Consignee::select('id','nick_name')->whereIn('branch_id',$cc)->get();
        }else{
            $consigners = Consigner::select('id','nick_name')->get();
            $consignees = Consignee::select('id','nick_name')->get();
        }
        $branchs = Branch::where('status','1')->select('id','consignment_note')->get();
        $vehicles = Vehicle::where('status','1')->select('id','regn_no')->get();
        $vehicletypes = VehicleType::where('status','1')->select('id','name')->get();
        return view('consignments.create-consignment',['prefix'=>$this->prefix,'consigners'=>$consigners,'consignees'=>$consignees,'branchs'=>$branchs,'vehicles'=>$vehicles,'vehicletypes'=>$vehicletypes]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try{
            DB::beginTransaction();

            $this->prefix = request()->route()->getPrefix();
            $authuser = Auth::user();
            $rules = array(
                'consigner_id' => 'required',
                'consignee_id' => 'required',
                'ship_to_id'   => 'required',
                'invoice_no'   => 'required',
            );
            $validator = Validator::make($request->all(),$rules);
        
            if($validator->fails())
            {
                $errors                 = $validator->errors();
                $response['success']    = false;
                $response['validation'] = false;
                $response['formErrors'] = true;
                $response['errors']     = $errors;
                return response()->json($response);
            }
            
            $consignmentsave['consigner_id']     = $request->consigner_id;
            $consignmentsave['consignee_id']     = $request->consignee_id;
            $consignmentsave['ship_to_id']       = $request->ship_to_id;
            //$consignmentsave['consignment_no']   = $request->consignment_no;
            $consignmentsave['consignment_date'] = $request->consignment_date;
            $consignmentsave['dispatch']         = $request->dispatch;
            $consignmentsave['invoice_no']       = $request->invoice_no;
            $consignmentsave['invoice_date']     = $request->invoice_date;
            $consignmentsave['invoice_amount']   = $request->invoice_amount;
            $consignmentsave['total_quantity']   = $request->total_quantity;
            $consignmentsave['total_weight']     = $request->total_weight;          
            $consignmentsave['total_gross_weight']= $request->total_gross_weight;          
            $consignmentsave['total_freight']     = $request->total_freight;          
            $consignmentsave['transporter_name']  = $request->transporter_name;          
            $consignmentsave['vehicle_type']      = $request->vehicle_type;          
            $consignmentsave['purchase_price']    = $request->purchase_price; 
            $consignmentsave['user_id']           = $authuser->id; 
            $consignmentsave['vehicle_id']        = $request->vehicle_id;
            $consignmentsave['status']            = 1;

            $saveconsignment = ConsignmentNote::create($consignmentsave); 
            if($saveconsignment)
            {
                $consignment_no = str_pad($saveconsignment->id,8,"0", STR_PAD_LEFT);
                ConsignmentNote::where('id',$saveconsignment->id)->update(['consignment_no'=>$consignment_no]);

                // insert consignment items
                if(!empty($request->data)){ 
                    $get_data=$request->data;
                    foreach ($get_data as $key => $save_data ) { 
                      $save_data['consignment_id'] = $saveconsignment->id; 
                      $save_data['status']         = 1;
                      $saveconsignmentitems = ConsignmentItem::create($save_data);
                    }              
                }

                $response['success'] = true;
                $response['success_message'] = "Consignment Added successfully";
                $response['error'] = false;
                $response['resetform'] = true;
                $response['page'] = 'create-branch'; 
            }
            else{
                $response['success'] = false;
                $response['error_message'] = "Can not created consignment please try again";
                $response['error'] = true;
            }
            DB::commit();
        }catch(Exception $e){
            $response['error'] = false;
            $response['error_message'] = $e;
            $response['success'] = false;
            $response['redirect_url'] = $url;
        }
        return response()->json($response);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($consignment)
    {
        $this->prefix = request()->route()->getPrefix();
        $id = $consignment;
        $auth = Auth::user();
        $query = ConsignmentNote::query();
        if ( ($auth->role_id == 1) || ($auth->role_id == 2) ) {
            $getconsignment = $query->orderBy('id','DESC')->get();
        } else {
            $getconsignment = $query->where('branch_id',$auth->branch_id)->orderBy('id','DESC')->get();
        }
        return view('consignments.view-consignment',['prefix'=>$this->prefix,'title'=>$this->title,'getconsignment'=>$getconsignment]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    // get consigner address on change
    public function getConsigners(Request $request){
        $getconsigners = Consigner::select('address','gst_number','phone','city')->where(['id'=>$request->consigner_id,'status'=>'1'] )->first();
        if($getconsigners)
        {
            $response['success']         = true;
            $response['success_message'] = "Consigner list fetch successfully";
            $response['error']           = false;
            $response['data']            = $getconsigners;
        }else{
            $response['success']         = false;
            $response['error_message']   = "Can not fetch consigner list please try again";
            $response['error']           = true;
        }
    	return response()->json($response);
    }

    // get consigner address on change
    public function getConsignees(Request $request){
        $getconsignees = Consignee::select('address_line1','address_line2','address_line3','gst_number','phone')->where(['id'=>$request->consignee_id,'status'=>'1'] )->first();
       if($getconsignees)
        {
            $response['success']         = true;
            $response['success_message'] = "Consignee list fetch successfully";
            $response['error']           = false;
            $response['data']            = $getconsignees;
        }else{
            $response['success']         = false;
            $response['error_message']   = "Can not fetch consignee list please try again";
            $response['error']           = true;
        }
    	return response()->json($response);
    }

    public function get_cn_details(Request $request){
        $consignment_id = request()->consignment_id;
        $consignment_details = ConsignmentNote::select('consignment_no','consignment_date','dispatch','invoice_no','invoice_date','invoice_amount','total_quantity','total_weight','total_gross_weight','total_freight','transporter_name','vehicle_type','purchase_price')->where(['id'=>$consignment_id])->first();
        if($consignment_details)
        {
            $response['success']         = true;
            $response['success_message'] = "Consignment details fetch successfully";
            $response['error']           = false;
            $response['data']            = $consignment_details;
        }else{
            $response['success']         = false;
            $response['error_message']   = "Can not fetch consignment details please try again";
            $response['error']           = true;
        }
        return response()->json($response);
    }

    // getConsigndetails
    public function getConsigndetails(Request $request){
        $cn_id = $request->id;
        $cn_details = ConsignmentNote::where('id',$cn_id)->with('ConsignmentItems','ConsignerDetail','ConsigneeDetail','ShiptoDetail')->first();
        if($cn_details)
        {
            $response['success']         = true;
            $response['success_message'] = "Consignment details fetch successfully";
            $response['error']           = false;
            $response['data']            = $cn_details;
        }else{
            $response['success']         = false;
            $response['error_message']   = "Can not fetch consignment details please try again";
            $response['error']           = true;
        }
        return response()->json($response);
    }

    public function consignPrintview(Request $request){
        $b_add = BranchAddress::get();
        $branch_add = json_decode(json_encode($b_add), true);
        
        $cn_id = $request->id;
        $getdata = ConsignmentNote::where('id',$cn_id)->with('ConsignmentItems','ConsignerDetail','ConsigneeDetail','ShiptoDetail','VehicleDetail')->first();
       
        $data = json_decode(json_encode($getdata), true);
        // dd($data['consigner_detail']['branch_id']);
        $conr_add = '<p>'.'CONSIGNOR NAME & ADDRESS'.'</p>
            <p><b>'.$data['consigner_detail']['nick_name'].'</b></p><p>'.$data['consigner_detail']['address'].',</p><br>
            <p>'.$data['consigner_detail']['district'].'</p>
            <p>GST No. : '.$data['consigner_detail']['gst_number'].'</p>
            <p>Phone No. : '.$data['consigner_detail']['phone'].'</p>';
        $consnee_add = '<p>'.'CONSIGNEE NAME & ADDRESS'.'</p>
            <p><b>'.$data['consignee_detail']['nick_name'].'</b></p>
            <p>'.$data['consignee_detail']['address_line1'].' '.$data['consignee_detail']['address_line2'].'<br> '.$data['consignee_detail']['address_line3'].',</p><br>
            <p>'.$data['consignee_detail']['district'].'</p>
            <p>GST No. : '.$data['consignee_detail']['gst_number'].'</p>
            <p>Phone No. : '.$data['consignee_detail']['phone'].'</p>';

        $shiptoadd = '<p>'.'SHIP TO NAME & ADDRESS'.'</p>
            <p><b>'.$data['consignee_detail']['nick_name'].'</b></p>
            <p>'.$data['consignee_detail']['address_line1'].' '.$data['consignee_detail']['address_line2'].'<br> '.$data['consignee_detail']['address_line3'].',</p><br>
            <p>'.$data['consignee_detail']['district'].'</p>
            <p>GST No. : '.$data['consignee_detail']['gst_number'].'</p>
            <p>Phone No. : '.$data['consignee_detail']['phone'].'</p>';

       
        if ($request->typeid == 1){
            $adresses = '<table width="100%">
                    <tr>
                        <td style="width:50%">'.$conr_add.'</td>
                        <td style="width:50%">'.$consnee_add.'</td>
                    </tr>
                </table>';
            } else if ($request->typeid == 2){
                $adresses = '<table width="100%">
                        <tr>
                            <td style="width:33%">'.$conr_add.'</td>
                            <td style="width:33%">'.$consnee_add.'</td>
                            <td style="width:33%">'.$shiptoadd.'</td>
                        </tr>
                    </table>';
            }
                
            for ($i=1; $i<5; $i++){
                if ($i == 1) {$type='ORIGINAL';} else if ($i == 2){$type='DUPLICATE';} else if ($i == 3){$type='TRIPLICATE';} else if ($i == 4){$type='QUADRUPLE';}

                    $html = '<!DOCTYPE html>
                    <html lang="en">
                        <head>
                            <title>PDF</title>
                            <meta charset="utf-8">
                            <meta name="viewport" content="width=device-width, initial-scale=1">
                            <style>
                                .aa{
                                    border: 1px solid black;
                                    border-collapse: collapse;
                                }
                                .bb{
                                    border: 1px solid black;
                                    border-collapse: collapse;
                                }
                                .cc{
                                    border: 1px solid black;
                                    border-collapse: collapse;
                                }
                                h2.l {
                                    margin-left: 90px;
                                    margin-top: 132px;
                                    margin-bottom: 2px;
                                }
                                p.l {
                                    margin-left: 90px;
                                }
                                img#set_img {
                                    margin-left: 25px;
                                    margin-bottom: 100px;
                                }
                               
                                p {
                                    margin-top: 2px;
                                    margin-bottom: 2px;
                                }
                                h4 {
                                    margin-top: 2px;
                                    margin-bottom: 2px;
                                }
                                body {
                                    font-family: Arial, Helvetica, sans-serif;
                                    font-size: 15px;
                                }
                            </style>
                        </head>
                         
                        <body>
                        <div class="container">
                            <div class="row">';
                            
                            foreach($branch_add as $k => $value){
                            $html .= '<h2>'.$value['name'].'</h2>
                                <table width="100%">
                                    <tr>
                                        <td width="50%">
                                            <p>Plot No. '.$value['address'].'</p>
                                            <p>Pabhat, Zirakpur</p>
                                            <p>'.$value['district'].' - '.$value['postal_code'].', Punjab</p>
                                            <p>GST No. : '.$value['gst_number'].'</p>
                                            <p>Email : '.$value['email'].'</p>
                                            <p>Phone No. : '.$value['phone'].''.'</p>
                                            <br>
                                            <span>
                                                <hr id="s" style="width:100%;">
                                                </hr>
                                            </span>
                                        </td>
                                        <td width="50%">
                                            <h2 class="l">CONSIGNMENT NOTE</h2>
                                            <p class="l">'.$type.'</p>
                                        </td>
                                    </tr>
                                </table></div></div>';
                            }
                            $html .= '<div class="row"><div class="col-sm-6">
                                <table width="100%">
                                <tr>
                            <td width="30%">
                                <p><b>Consignment No.</b></p>
                                <p><b>Consignment Date</b></p>
                                <p><b>Dispatch From</b></p>
                                <p><b>Invoice No.</b></p>
                                <p><b>Invoice Date</b></p>
                                <p><b>Value INR</b></p>
                                <p><b>Vehicle No.</b></p>
                            </td>
                            <td width="30%">
                                <p>'.$data['consignment_no'].'</p>
                                <p> '.$data['consignment_date'].'</p>
                                <p> '.'Karnal'.'</p>
                                <p> '.$data['invoice_no'].'</p>
                                <p> '.$data['invoice_date'].'</p>
                                <p> '.$data['invoice_amount'].'</p>
                                <p> '.$data['vehicle_detail']['regn_no'].'</p>
                            </td>
                            <td width="50%" colspan="3">
                                <img src="img/eternity_solutions.png" id="set_img">
                            </td>
                        </tr>
                    </table>  
                </div>
                <span><hr id="e"></hr></span>
            </div>
            <div class="main">'.$adresses.'</div>
            <span><hr id="e"></hr></span><br>';
            $html .= '<div class="bb">    
                <table class="aa" width="100%">
                    <tr>
                        <th class="cc">Sr.No.</th>
                        <th class="cc">Description</th>
                        <th class="cc">Quantity</th>
                        <th class="cc">Net Weight</th>
                        <th class="cc">Gross Weight</th>
                        <th class="cc">Freight</th>
                        <th class="cc">Payment Terms</th>
                    </tr>';
                    ///
                    foreach($data['consignment_items'] as $k => $dataitem){ 
                        $html .=  '<tr>'.
                                    '<td class="cc">'.$i.'</td>'.
                                    '<td class="cc">'.$dataitem['description'].'</td>'.
                                    '<td class="cc">'.$dataitem['packing_type'].' '.$dataitem['quantity'].'</td>'.
                                    '<td class="cc">'.$dataitem['weight'] .' Kgs.</td>'.
                                    '<td class="cc">'.$dataitem['gross_weight'].' Kgs.</td>'.
                                    '<td class="cc">INR '.$dataitem['freight'].'</td>'.
                                    '<td class="cc">'.$dataitem['payment_type'].'</td>'.
                                    '</tr>';
                        }
                        $html .=  '<tr><td colspan="2" class="cc"><b>TOTAL</b></td>
                            <td class="cc">'.$data['total_quantity'].'</td>
                            <td class="cc">'.$data['total_weight'].' Kgs.</td>
                            <td class="cc">'.$data['total_gross_weight'].' Kgs.</td>
                            <td class="cc"></td>
                            <td class="cc"></td>
                        </tr></table></div><br><br>
                        <span><hr id="e"></hr></span>';

                        $html .= '<div class="nn">
                                <table  width="100%">
                                    <tr>
                                        <td>
                                            <h4><b>Receivers Signature</b></h4>
                                            <p>Received the goods mentioned above in goodcondition.</p>
                                        </td>
                                        <td>
                                        <h4><b>For Eternity Forwarders Pvt. Ltd.</b></h4>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </body>
                    </html>';

                $pdf = \App::make('dompdf.wrapper');
                $pdf->loadHTML($html);
                $pdf->setPaper('A4', 'portrait');
                $pdf->save(public_path().'/consignment-pdf/congn.pdf')->stream('congn.pdf');
                $pdf_name[] = 'congn.pdf';
            }
            $pdfMerger = PDFMerger::init();
            foreach($pdf_name as $pdf){
                // echo'<pre>'; print_r($pdf); die;
                $pdfMerger->addPDF(public_path().'/consignment-pdf/'.$pdf);
            }
            $pdfMerger->merge();
            $pdfMerger->save("all.pdf");
            
            return $pdfMerger->download('all.pdf');      
    }

}
