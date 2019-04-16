<div class="col-md-12">
          	<div class="box box-primary" id="ambassador_bonus_month_report">
					<div class="box-header with-border">
						<h3 class="box-title">Personal Customer Commission for the Month of  - {{date('M, Y',strtomtime($date))}}</h3>
						<button class="pull-right back btn btn-sm btn-default" class="back"><i class="fa fa-arrow-left"></i> Back</button>
					</div>
					<div class="box-body">
					<table id="commission_details" class="table table-bordered table-striped">
						<thead>
							<tr>                                                    
								 <th>{{trans('affiliate/general.sl_no')}}</th>
								 <th>Member</th>							     
								 <th>CV</th>
								 <th>Date</th>
							</tr>
						</thead>
						<tbody>
							@if(isset($gusers) && !empty($gusers))
								<?php $i=0; ?>
								@foreach($gusers as $user)
									@if(!empty($user))
										<tr>
												<td class="text-center">{{++$i}}</td>												
												<td>{{$user->fullname}}</td>
												<td>{{$user->cv}}</td>
												<td>{{$user->date}}</td>
										</tr>	
									@endif
								@endforeach
							@endif		
						</tbody>
					</table>
					</div>
			</div>
</div>
@include('affiliate.common.datatable_js')
<script>
$(function () {

	var t = $('#commission_details');
	t.DataTable({
		ordering:false,
		serverSide: false,
		processing: true,
		pagingType: 'input_page',		
		sDom: "t"+"<'col-sm-6 bottom info align'li>r<'col-sm-6 info bottom text-right'p>", 
		oLanguage: {
			"sLengthMenu": "_MENU_",
		},
		
	});
 });

</script>