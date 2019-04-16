@extends('admin.common.layout')
@section('title','Create Channel Partner')
@section('layoutContent')
<div class="row">
	<div class="col-md-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">Update Channel Partner Access Location</h4>
			</div>
			<div class="panel-body">
				@if(session()->has('flmsg'))
				<div class="alert alert-success">{{session()->get('flmsg')}}</div>
				@endif
				<div id="access_form" {!! (request()->has('uname'))? 'class="hiddens"':'' !!} >
                    <form name="search_user" id="search_user" action="{{route('admin.franchisee.check')}}" method="post" class='form-horizontal form-validate'>
                        <div class="form-group">
                            <label for="textfield" class="control-label col-sm-2">Channel Partner Account ID:</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control" name="uname" id="uname" value="{{request()->get('uname')}}">
                                <div id="uname_status"></div>
                            </div>
							<div class="col-sm-7 check-btn">
                                <button id="check" class="btn btn-primary">Search</button>
                            </div>
                        </div>                        
                    </form>
                </div>
				<hr style="clear:both" width="100%">
				<div id="access_edit">
                </div>
			</div>
		</div>
	</div>
</div>
@stop
@section('scripts')
<script>
    $(document).ready(function () {
		var CHKFRM  = $('#search_user');		
		
		
        CHKFRM.on('click','#check',function (e) {
            e.preventDefault();
            var uname = $('#uname',CHKFRM).val();
            if (uname != '') {
                $.ajax({
                    dataType: 'json',
                    type: 'post',
                    data: {uname: uname},
                    url: CHKFRM.attr('action'),
                    beforeSend: function (data) {
                        $('#check',CHKFRM).text('Processing..');
                    },
                    success: function (data) {
                        if (data.status == 'ok') {
                            $('#uname_status').html('');
                            $('#access_edit #user_id').val(data.user_id);
                            $('#access_edit').css('display', 'block');
                            $('.fld').css('display', 'block');
                            $('#check').text('Check Channel Partner');
                            $('#access_edit').html(data.content);
                            $('#edit_access' + data.scope).css('display', 'block');
                            //$('#access_form').hide();
                           // $("#state").change();
                        } else if (data.status == 'not_avail') {
                            $('#uname_status').html('Channel Partner Not Avaliable');
                            $('#access_edit').css('display', 'none');
                            $('#check').text('Search');
                        }
                    },
                    error: function (data) {
                        alert("something Went Wrong");
                    }
                })
            } else {
                alert("Please Enter validate UserName");
            }
        })

        $('#uname').on('keyup', function () {
            $('#access_edit').html('');
        }).on('mouseup', function () {
            $('#access_edit').html('');
        });

    });

</script>
@stop