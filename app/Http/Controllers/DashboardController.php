<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ConsignmentNote;
use App\Models\ConsignmentItem;
use App\Models\Role;
use Carbon\Carbon;
use DB;
use Auth;

class DashboardController extends Controller
{
    public $prefix;
    public $title;
    public $segment;

    public function __construct()
    {
      $this->title =  "Dashboard";
      $this->segment = \Request::segment(2);
    }
    public function index()
    {
        $this->prefix = request()->route()->getPrefix();
        $query = ConsignmentNote::query();
        $authuser = Auth::user();
        ////
        $role_id = Role::where('id','=',$authuser->role_id)->first();
        $regclient = explode(',',$authuser->regionalclient_id);
        $cc = explode(',',$authuser->branch_id);
        if($authuser->role_id ==4){
            $gettoday_lr = $query->where('user_id',$authuser->id)
                            ->whereDate('created_at', '=', date('Y-m-d'))
                            ->where('status', '1')
                            ->count();
            $getcurrentmonth_lr = $query->where('created_at', '>=', date('Y-m-01'))
                            ->where('user_id',$authuser->id)
                            ->where('status', 1)
                            ->count();

            $today_weightlifted = DB::table('consignment_items')->select('consignment_items.*', 'consignment_notes.id as consignment_id')
                            ->join('consignment_notes', 'consignment_notes.id', '=', 'consignment_items.consignment_id')
                            ->where('consignment_items.created_at', '>=', date('Y-m-d'))
                            ->where('consignment_items.status', '=', 1)
                            ->where('consignment_notes.user_id',$authuser->id)
                            ->sum('weight');
            $gettoday_weightlifted = $today_weightlifted/1000;

            $monthly_weightlifted = DB::table('consignment_items')->select('consignment_items.*', 'consignment_notes.id as consignment_id')
                            ->join('consignment_notes', 'consignment_notes.id', '=', 'consignment_items.consignment_id')
                            ->where('consignment_items.created_at', '>=', date('Y-m-01'))
                            ->where('consignment_items.status', '=', 1)
                            ->where('consignment_notes.user_id',$authuser->id)
                            ->sum('weight');
            $getmonthly_weightlifted = $monthly_weightlifted/1000;

            $today_gross_weightlifted = DB::table('consignment_items')->select('consignment_items.*', 'consignment_notes.id as consignment_id')
                            ->join('consignment_notes', 'consignment_notes.id', '=', 'consignment_items.consignment_id')
                            ->where('consignment_items.created_at', '>=', date('Y-m-d'))
                            ->where('consignment_items.status', '=', 1)
                            ->where('consignment_notes.user_id',$authuser->id)
                            ->sum('gross_weight');
                            // ->get();
                            // dd($today_gross_weightlifted/1000);
            $gettoday_gross_weightlifted = $today_gross_weightlifted/1000;

            $monthly_gross_weightlifted = DB::table('consignment_items')->select('consignment_items.*', 'consignment_notes.id as consignment_id')
                            ->join('consignment_notes', 'consignment_notes.id', '=', 'consignment_items.consignment_id')
                            ->where('consignment_items.created_at', '>=', date('Y-m-01'))
                            ->where('consignment_items.status', '=', 1)
                            ->where('consignment_notes.user_id',$authuser->id)
                            ->sum('gross_weight');
            $getmonthly_gross_weightlifted = $monthly_gross_weightlifted/1000;

            $getLatestLr = ConsignmentNote::select('*')->with('ConsigneeDetail')->where('user_id',$authuser->id)->orderby('id', 'DESC')->limit(5)->get();
            $Lrsimplify = json_decode(json_encode($getLatestLr), true);

        }elseif($authuser->role_id !=1 || $authuser->role_id !=4){
            if ($authuser->role_id == $role_id->id) {
                $gettoday_lr = $query->whereIn('branch_id',$cc)
                        ->whereDate('created_at', '=', date('Y-m-d'))
                        ->where('status', '1')
                        ->count();
                $getcurrentmonth_lr = $query->where('created_at', '>=', date('Y-m-01'))
                                ->whereIn('branch_id',$cc)
                                ->where('status', 1)
                                ->count();

                $today_weightlifted = DB::table('consignment_items')->select('consignment_items.*', 'consignment_notes.id as consignment_id')
                                ->join('consignment_notes', 'consignment_notes.id', '=', 'consignment_items.consignment_id')
                                ->where('consignment_items.created_at', '>=', date('Y-m-d'))
                                ->where('consignment_items.status', '=', 1)
                                ->whereIn('consignment_notes.branch_id',$cc)
                                ->sum('weight');
                $gettoday_weightlifted = $today_weightlifted/1000;

                $monthly_weightlifted = DB::table('consignment_items')->select('consignment_items.*', 'consignment_notes.id as consignment_id')
                                ->join('consignment_notes', 'consignment_notes.id', '=', 'consignment_items.consignment_id')
                                ->where('consignment_items.created_at', '>=', date('Y-m-01'))
                                ->where('consignment_items.status', '=', 1)
                                ->whereIn('consignment_notes.branch_id',$cc)
                                ->sum('weight');
                $getmonthly_weightlifted = $monthly_weightlifted/1000;

                $today_gross_weightlifted = DB::table('consignment_items')->select('consignment_items.*', 'consignment_notes.id as consignment_id')
                                ->join('consignment_notes', 'consignment_notes.id', '=', 'consignment_items.consignment_id')
                                ->where('consignment_items.created_at', '>=', date('Y-m-d'))
                                ->where('consignment_items.status', '=', 1)
                                ->whereIn('consignment_notes.branch_id',$cc)
                                ->sum('gross_weight');
                $gettoday_gross_weightlifted = $today_gross_weightlifted/1000;

                $monthly_gross_weightlifted = DB::table('consignment_items')->select('consignment_items.*', 'consignment_notes.id as consignment_id')
                                ->join('consignment_notes', 'consignment_notes.id', '=', 'consignment_items.consignment_id')
                                ->where('consignment_items.created_at', '>=', date('Y-m-01'))
                                ->where('consignment_items.status', '=', 1)
                                ->whereIn('consignment_notes.branch_id',$cc)
                                ->sum('gross_weight');
                $getmonthly_gross_weightlifted = $monthly_gross_weightlifted/1000;

                $getLatestLr = ConsignmentNote::select('*')->with('ConsigneeDetail')->whereIn('branch_id',$cc)->orderby('id', 'DESC')->limit(5)->get();
                $Lrsimplify = json_decode(json_encode($getLatestLr), true);
            }
        }else{
            $gettoday_lr = $query->whereDate('created_at', '=', Carbon::today())
                        ->where('status', '1')
                        ->count();
            $getcurrentmonth_lr = ConsignmentNote::where('created_at', '>=', date('Y-m-01'))
                        ->where('status', 1)
                        ->count();

            $today_weightlifted = ConsignmentItem::where('created_at', '>=', date('Y-m-d'))
                        ->where('status', '=', 1)
                        ->sum('weight');
            $gettoday_weightlifted = $today_weightlifted/1000; 

            $monthly_weightlifted = ConsignmentItem::where('created_at', '>=', date('Y-m-01'))
                        ->where('status', '=', 1)
                        ->sum('weight');
            $getmonthly_weightlifted = $monthly_weightlifted/1000;

            $today_gross_weightlifted = ConsignmentItem::where('created_at', '>=', date('Y-m-d'))
                        ->where('status', '=', 1)
                        ->sum('gross_weight'); 
            $gettoday_gross_weightlifted = $today_gross_weightlifted/1000; 
            
            $monthly_gross_weightlifted = ConsignmentItem::where('created_at', '>=', date('Y-m-01'))
                        ->where('status', '=', 1)
                        ->sum('gross_weight');
            $getmonthly_gross_weightlifted = $monthly_gross_weightlifted/1000;

            $getLatestLr = ConsignmentNote::select('*')->with('ConsigneeDetail')->orderby('id', 'DESC')->limit(5)->get();
            $Lrsimplify = json_decode(json_encode($getLatestLr), true);
        }
        //=========================== Activity Logs ======================================//
    
        
        


        return view('dashboard',['prefix'=>$this->prefix,'title'=>$this->title,'gettoday_lr'=>$gettoday_lr,'gettoday_weightlifted'=>$gettoday_weightlifted,'gettoday_gross_weightlifted'=>$gettoday_gross_weightlifted,'getcurrentmonth_lr'=>$getcurrentmonth_lr,'getmonthly_weightlifted'=>$getmonthly_weightlifted,'getmonthly_gross_weightlifted'=>$getmonthly_gross_weightlifted,'Lrsimplify' => $Lrsimplify]);
    }

    public function ForbiddenPage(Request $request)
    {
        return view('forbidden');
    }
    
}
