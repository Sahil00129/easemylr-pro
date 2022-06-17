<div class="modal" id="location-updatemodal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- <button type="button" class="close" data-dismiss="modal"><img src="{{asset('assets/images/close-bottle.png')}}" class="img-fluid"></button> -->
            <!-- Modal Header -->
            <form class="general_form" method="POST" action="{{url($prefix.'/locations/update')}}" id="updatelocation">
                @csrf
                <input type="hidden" class="locationid" value="" name="id">
                <!-- <input type="hidden" value="locationpage" name="locationpage"> -->
                <div class="modal-header text-center">
                    <h4 class="modal-title">Location</h4>
                </div>
                <!-- Modal body -->
                <div class="modal-body">
                        <!-- <h5>Wine Style</h5> -->
                        <div class="form-group my-3">
                            <input class="form-control" id="nameup" name="name" placeholder="Enter location name" value="">
                        </div>
                        <div class="form-group my-3">
                            <input class="form-control" id="nick_nameup" name="nick_name" placeholder="Enter nick name" value="">
                        </div>
                        <div class="form-group my-3">
                            <input class="form-control" id="team_idup" name="team_id" placeholder="Enter team id" value="">
                        </div>
                        <div class="form-group my-3">
                            <input class="form-control" id="consignment_noup" name="consignment_no" placeholder="Enter consignment no" value="" maxlength="3">
                        </div>
                </div>
            <!-- Modal footer -->
                <div class="modal-footer">
                    <div class="btn-section w-100 P-0">
                        <button type="submit" id="location_savebtn" class="btn btn-primary btn-modal">Update</button>
                        <a class="btn btn-primary btn-modal" data-dismiss="modal">Cancel</a>
                    </div>
                </div>
            </form> 
        </div>
    </div>
</div>