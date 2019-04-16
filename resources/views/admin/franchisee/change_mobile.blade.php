<div  id="change_mobile"  class="panel panel-default" style="display:none">
    <div class="panel-heading">
        <button class="btn btn-danger btn-sm close_btn pull-right"> <i class="fa fa-times"></i> Close</button>
        <h4 class="panel-title"><i class="fa fa-gear"></i> Change Mobile</h4>
    </div>
    <div class="panel-body">
        <div class="col-sm-12">
            <form action="{{route('admin.franchisee.change-mobile')}}" method="post" class="form-horizontal form-bordered" id="change_mobile_form" autocomplete="off" novalidate="novalidate">
                <input type="hidden" class="form-control" id="uname_mobile" value="">
                <input type="hidden" class="form-control" id="account_id_mobile" value="">
                <fieldset>
                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="">{{trans('admin/franchisee.current_mobile')}}</label>
                        <div class="col-sm-4">
							<div class="input-group">
								<span class="input-group-addon">+91</span>								
								<input type="text" name="old_mobile" id="old_mobile" class="form-control" disabled="disabled">								
							</div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3">{{trans('admin/franchisee.new_mobile')}}</label>
						<div class="col-sm-4">
							<div class="input-group">
								<span class="input-group-addon">+91</span>								
								<input type="text" name="new_mobile" id="new_mobile" class="form-control valid" data-err-msg-to="#new_mobile_error"  placeholder="Enter New Mobile Number" value="">
							</div>
							<div id="new_mobile_error"></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-3 col-sm-offset-3 fieldgroup">
                            <button id="update_member_mobile" class="btn btn-primary"><i class="fa fa-save"></i> Update Mobile</button>
                        </div>
                    </div>
                </fieldset>
            </form>
        </div>
    </div>
</div>
