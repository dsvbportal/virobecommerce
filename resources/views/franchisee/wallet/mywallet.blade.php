@extends('franchisee.layout.dashboard')
@section('title',trans('franchisee/general.my_wallet'))
@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>{{trans('franchisee/general.my_wallet')}}</h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> {{trans('franchisee/general.dashboard')}}</a></li>
        <li>{{trans('franchisee/general.finance')}}</li>
        <li class="active">{{trans('franchisee/general.my_wallet')}}</li>
      </ol>
    </section>
    <!-- Main content -->
    <section class="content">
		<!-- Small boxes (Stat box) -->
		<?php $colorArr = ['6'=>'bg-maroon-active','4'=>'bg-yellow-active','5'=>'bg-green-active']?>
		<div class="row">  
	 @if(!empty($balInfo))
		   @foreach($balInfo as $balance)
		    <div class="col-lg-3 col-xs-6">
           <!-- small box -->
			    <div class="small-box <?php echo $colorArr[$balance->wallet_id]?> wallet-box">
					<div class="inner">
					   <h3>{{$balance->current_balance}}</h3>
					   <h4>{{$balance->wallet_name}}</h4>
					</div>
					<div class="inner trans">
					   <p><span>{{trans('franchisee/wallet/wallet_balance.tot_credit')}} :</span>{{$balance->tot_credit}}</p>
					   <p><span>{{trans('franchisee/wallet/wallet_balance.tot_debit')}}  :</span>{{$balance->tot_debit}}</p>
					</div>
					<div class="icon">
					   <i class="fa fa-money"></i>
					</div>
			    </div>
            </div>
			@endforeach
		    @endif
		</div>
		<!-- /.row -->
    </section>
    <!-- /.content -->
@stop
@section('scripts')
@stop