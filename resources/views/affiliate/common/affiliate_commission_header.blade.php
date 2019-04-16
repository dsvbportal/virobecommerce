<section class="content-header">
  <h1>Affiliate Commission</h1>
  <ol class="breadcrumb">
	<li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
	<li>Commission</li>
	<li class="active">Affiliate Commission</li>
  </ol>
</section>
<section class="content-nav">
  <ul class="nav nav-pills">
	<li {!!($current_route_name=='aff.reports.fast_start_bonus')? 'class="active"':''!!} ><a href="{{route('aff.reports.fast_start_bonus')}}">Fast Start Bonus</a></li>
	<li {!!($current_route_name=='aff.reports.team_bonus_bonus')? 'class="active"':''!!}><a href="{{route('aff.reports.team_bonus_bonus')}}">Team Commission</a></li>
	<li {!!($current_route_name=='aff.reports.leadership_bonus')? 'class="active"':''!!}><a href="{{route('aff.reports.leadership_bonus')}}">Leadership Bonus</a></li>
	<li {!!($current_route_name=='aff.reports.car_bonus')? 'class="active"':''!!}><a href="{{route('aff.reports.car_bonus')}}">Car Bonus</a></li>
	<li {!!($current_route_name=='aff.reports.star_bonus')? 'class="active"':''!!}><a href="{{route('aff.reports.star_bonus')}}">Star Bonus</a></li>
	<li {!!($current_route_name=='aff.reports.survival_bonus')? 'class="active"':''!!}><a href="{{route('aff.reports.survival_bonus')}}">Survival Bonus</a></li>	
  </ul>
</section>