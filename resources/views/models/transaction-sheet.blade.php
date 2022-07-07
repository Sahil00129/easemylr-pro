<!-- xtra Large modal -->

<!-- Modal -->
<div class="modal fade bd-example-modal-xl" id="save-draft" tabindex="-1" role="dialog" aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
      </div>
      <div class="modal-body">
       <form id="updt_vehicle" method="post">

                    <input type="hidden" class="form-control" id="transaction_id" name="transaction_id"
                        value="">
                    <div class="form-row mb-0">
                        <div class="form-group col-md-6">
                            <label for="location_name">Vehicle No.</label>

                            <select class="form-control my-select2" id="vehicle_no" name="vehicle_id" tabindex="-1">
                                <option value="">Select vehicle no</option>
                                @foreach($vehicles as $vehicle)
                                <option value="{{$vehicle->id}}">{{$vehicle->regn_no}}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="exampleFormControlInput2">Vehicle Type</label>
                            <select class="form-control my-select2" id="vehicle_type" name="vehicle_type" tabindex="-1">
                                <option value="">Select vehicle type</option>
                                @foreach($vehicletypes as $vehicle)
                                <option value="{{$vehicle->id}}">{{$vehicle->name}}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-row mb-0">
                        <div class="form-group col-md-6">
                            <label for="exampleFormControlInput2">Driver Name</label>


                            <select class="form-control my-select2" id="driver_id" name="driver_id" tabindex="-1">
                                <option value="">Select driver</option>
                                @foreach($drivers as $driver)
                                <option value="{{$driver->id}}">{{ucfirst($driver->name) ?? '-'}}-{{$driver->phone ??
                                    '-'}}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="exampleFormControlInput2">Transporter Name</label>


                            <input type="text" class="form-control" id="Transporter" name="transporter_name" value="">

                        </div>

      </div>
      <div class="table-responsive">
                            <table id="save-DraftSheet" class="table table-hover" style="width:100%; text-align:left; border: 1px solid #c7c7c7;">
                                <thead>
                                    <tr>
                                        <th>Consignment No</th>
                                        <th>Consignment Date</th>
                                        <th>Consignee Name</th>
                                        <th>city</th>
                                        <th>Pin Code</th>
                                        <th>Number Of Boxes</th>
                                        <th>Net Weight</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                            <div class="row">
                                <div class="col-sm-12">
                                    <table>
                                        <tr>
                                            <td width: 131px;>
                                                <label>Total :</label>
                                            </td>
                                            <td width: 131px;>
                                                <label id="total"></label>
                                            </td>
                                        </tr>
                                    </table> 
                                </div>
                            </div>
                           
                        </div>
      
                </div>

      <div class="modal-footer">
      <button class="btn" data-dismiss="modal"><i class="flaticon-cancel-12"></i> Discard</button>
      <button type="submit" class="btn btn-primary">Save</button>
                                </form>
      </div>
    </div>
  </div>
</div>
<!-- ================================================================================== -->

<div class="modal fade bd-example-modal-xl" id="opm" tabindex="-1" role="dialog" aria-labelledby="myExtraLargeModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myExtraLargeModalLabel">Delivery Run Sheet</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="feather feather-x">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
            <div class="modal-body">
                <!-- <button type="button" id="print" class="btn btn-primary commonButton">
                    <i class="fas fa-save"></i>&nbsp;Print
                </button> -->
                <div id="www">
                <div class="row">
                    <div class="col-sm-12">
                        
                        <div>

                        </div>



                        <div class="table-responsive">
                            <table id="sheet" class="table table-hover" style="width:100%; text-align:left; border: 1px solid #c7c7c7;">
                                <thead>
                                    <tr>
                                        <th>Consignment No</th>
                                        <th>Consignment Date</th>
                                        <th>Consignee Name</th>
                                        <th>city</th>
                                        <th>Pin Code</th>
                                        <th>Number Of Boxes</th>
                                        <th>Net Weight</th>
                                    </tr>
                                </thead>
                                <tbody id="suffle">

                                </tbody>
                            </table>
                            <div class="row">
                                <div class="col-sm-12">
                                    <table>
                                        <tr>
                                            <td width: 131px;>
                                                <label>Total :</label>
                                            </td>
                                            <td width: 131px;>
                                                <label id="total"></label>
                                            </td>
                                        </tr>
                                    </table> 
                                </div>
                            </div>
                           
                        </div>
                    </div>
                </div>
                </div>
     </div>

</div>
