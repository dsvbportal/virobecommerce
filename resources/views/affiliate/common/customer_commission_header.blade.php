<section class="content-header">
  <h1>Customer Commission</h1>
  <ol class="breadcrumb">
	<li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
	<li>Commission</li>
	<li class="active">Customer Commission</li>
  </ol>
</section>
<section class="content-nav">
  <ul class="nav nav-pills">
	<li {!!($current_route_name=='aff.reports.personal_commission')? 'class="active"':''!!}><a href="{{route('aff.reports.personal_commission')}}">Personal Commission Bonus</a></li>
	@if($userSess->can_sponsor)
	<li {!! ($current_route_name=='aff.reports.ambassador_bonus')? 'class="active"':'' !!}><a href="{{route('aff.reports.ambassador_bonus')}}">Ambassador Bonus</a></li>
	@endif
  </ul>
</section>