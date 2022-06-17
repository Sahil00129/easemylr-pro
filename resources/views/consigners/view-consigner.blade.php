@extends('layouts.main')
@section('content')

<div class="layout-px-spacing">
    <div class="row layout-top-spacing">
        <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing">
            <div class="widget-content widget-content-area br-6">
                <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
                    <!-- <div class="breadcrumb-title pe-3"><h5>Consigner Details</h5></div> -->
                    <!-- <div class="col-md-9 text-right">
                        <a href="{{url($prefix.'/consigners/'.Crypt::encrypt($getconsigner->id).'/edit')}}" class="btn my-3" href="" style="background:#fff;" title="Edit Consigner"><i class="fa fa-edit m-0"></i></a>
                    </div> -->
                </div>
                <div class="col-lg-12 col-12 layout-spacing">
                    <div class="statbox widget box box-shadow">
                        <table class="table table-striped">
                            <tbody>
                                <tr>
                                    <th scope="row">Consigner Nick Name</th>
                                    <td>{{isset($getconsigner->nick_name)?ucfirst($getconsigner->nick_name):'-'}} </td>
                                </tr>
                                <tr>
                                    <th scope="row">Consigner Legal Name</th>
                                    <td>{{isset($getconsigner->legal_name)?ucfirst($getconsigner->legal_name):'-'}} </td>
                                </tr>
                                <tr>
                                    <th scope="row">GSTNO.</th>
                                    <td>{{isset($getconsigner->gst_number)?ucfirst($getconsigner->gst_number):'-'}}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Contact Name</th>
                                    <td>{{isset($getconsigner->contact_name)?ucfirst($getconsigner->contact_name):'-'}}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Mobile No.</th>
                                    <td>{{isset($getconsigner->phone)?ucfirst($getconsigner->phone):'-'}}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Location</th>
                                    <td>
                                        {{isset($getconsigner->GetBranch->name) ? ucfirst($getconsigner->GetBranch->name) : "-" }}
                                    </td>                                       
                                </tr>
                                <tr>
                                    <th scope="row">Email ID</th>
                                    <td>{{isset($getconsigner->email)? $getconsigner->email:'-'}}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Address</th>
                                    <td>{{isset($getconsigner->address)?ucfirst($getconsigner->address):'-'}} </td>
                                </tr>
                                <!-- <tr>
                                    <th scope="row">Address Line 2</th>
                                    <td>{{isset($getconsigner->address_line2)?ucfirst($getconsigner->address_line2):'-'}}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Address Line 3</th>
                                    <td>{{isset($getconsigner->address_line3)?ucfirst($getconsigner->address_line3):'-'}}</td>
                                </tr> -->
                                <tr>
                                    <th scope="row">City</th>
                                    <td>{{isset($getconsigner->city) ? ucfirst($getconsigner->city):'-'}} </td>
                                </tr>
                                <tr>
                                    <th scope="row">District</th>
                                    <td>{{isset($getconsigner->district)?ucfirst($getconsigner->district):'-'}} </td>
                                </tr>
                                <tr>
                                    <th scope="row">Pincode</th>
                                    <td>{{isset($getconsigner->postal_code) ? ucfirst($getconsigner->postal_code):'-'}}</td>
                                </tr>
                                <tr>
                                    <th scope="row">State</th>
                                    <td>{{isset($getconsigner->GetState->name) ? ucfirst($getconsigner->GetState->name) : "-" }}</td>
                                </tr>
                                <!-- <tr>
                                    <th scope="row">Status</th>
                                    <td>
                                        <?php// if($getconsigner->status == 1){
                                            // echo "Active";
                                        // }else if($getconsigner->status == 0){
                                            // echo "Deactive";
                                        // } else{ ?>
                                                {{$getconsigner->status ?? "-"}}
                                        <?php// } ?>
                                    </td>
                                </tr>   -->
                                    
                            </tbody>
                        </table>  
                        <a href="{{url($prefix.'/consigners/'.Crypt::encrypt($getconsigner->id).'/edit')}}" class="btn btn-primary" title="Edit User"> <i class="fa fa-pencil-square-o" aria-hidden="true"></i> Edit</a>
                        <a class="btn btn-primary" href="{{url($prefix.'/consigners') }}"> Back</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection