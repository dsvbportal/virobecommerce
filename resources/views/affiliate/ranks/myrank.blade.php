@extends('affiliate.layout.dashboard')
@section('title',"My Rank")
@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>My Rank</h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li>Ranks</li>
        <li class="active">Rank</li>
      </ol>
    </section>
    <!-- Main content -->
    <section class="content">
		<!-- Small boxes (Stat box) -->
		<div class="row">        
			<!-- ./col -->
			<div class="col-md-12" id="report">
             <div class="box box-primary">					
					<div class="box-controls with-border">
	              </div>
                    <div class="box-body">
                    <table id="ammb_commission" class="table table-bordered table-striped">
                       <thead>
                            <tr>                                                    
                                 <th class="text-left">Rank</th>
                                 <th class="text-left">Your Current Rank</th>
								 <th class="text-right">GQV - 1G</th>							     
                                 <th class="text-right">GQV - 2G</th>
                                 <th class="text-right">GQV - 3G</th>
				            </tr>
                        </thead>
                        <tbody>
							@if(!empty($ranks))
								@foreach($ranks as $rank)
									<tr>
										<td>{{$rank->rank}}</td>
										<td>@if(($rank->status ==1) && ($rank->is_verified == 1))<i class="fa fa-star" style="font-size:32px;color:#0ab80a"></i>@endif</td>
										<td align="right">{{$rank->gen_1}}</td>
										<td align="right">{{$rank->gen_2}}</td>
										<td align="right">{{$rank->gen_3}}</td>
									</tr>
								@endforeach
							@endif		
                        </tbody>
                    </table>
					</div>
				</div>
			</div>
			<!-- ./col -->			
		</div>
		<!-- /.row -->
    </section>
    <!-- /.content -->
@stop
@section('scripts')
@stop