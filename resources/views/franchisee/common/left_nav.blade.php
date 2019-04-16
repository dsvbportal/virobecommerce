<!-- Left side column. contains the logo and sidebar -->
<aside class="main-sidebar">
	<!-- sidebar: style can be found in sidebar.less -->
	<section class="sidebar">   
		<div class="userimg-panel">       
			<div class="user-panel">
				<div class=" image">
					<img src="{{isset($userSess->franchisee_logo) ? asset($userSess->franchisee_logo): ''}}"class="img-circle user-img-circle" id="frans_logo">
				</div>
			</div>
			<div class="profile-usertitle">
				<div class="sidebar-userpic-name">{{$userSess->franchisee_name}}</div>
				<div class="profile-usertitle-job "> {{$userSess->uname}}<br />{{$userSess->user_code}}</div>
			</div>
		</div>
		<!-- sidebar menu: : style can be found in sidebar.less -->
		<ul class="sidebar-menu">

			<li><a href="{{route('fr.dashboard')}}"><i class="fa fa-book"></i> <span>Dashboard</span></a></li>
			
			<li class="treeview">
			  <a href="#"><i class="fa fa-dashboard"></i> <span>Account Settings</span><span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span></a>
			  <ul class="treeview-menu">            
				<li><a href="{{route('fr.profile')}}"><i class="fa fa-circle-o"></i>Profile</a></li>
			  </ul>
			</li>
			<li class="treeview">
			  <a href="#"><i class="fa fa-dashboard"></i> <span>Finance</span><span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span></a>
			  <ul class="treeview-menu">            
				<li><a href="{{route('fr.wallet.balance')}}"><i class="fa fa-circle-o"></i>Wallet</a></li>
		<!--	<li><a href="{{route('fr.withdrawal.request')}}"><i class="fa fa-circle-o"></i>Withdraw</a></li>-->
	   		    <li><a href="{{route('fr.withdrawal.history',['status'=>'pending'])}}"><i class="fa fa-circle-o"></i>Withdrawal</a></li>
				<li><a data-href="{{route('fr.withdrawal.create')}}"><i class="fa fa-circle-o"></i>Add Fund</a></li>
				<li><a href="{{route('fr.wallet.fundtransfer')}}"><i class="fa fa-circle-o"></i>Transfer</a></li>
				<li><a href="{{route('fr.wallet.transactions')}}"><i class="fa fa-circle-o"></i>Transactions</a></li>
				<li><a href="{{route('fr.wallet.fundtransfer.history')}}"><i class="fa fa-circle-o"></i>Transfer History</a></li>
			  </ul>
			</li>
			<li>
			<li class="treeview">
			  <a href="#"><i class="fa fa-dashboard"></i> <span>Reports</span><span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span></a>
			  <ul class="treeview-menu">            
				<li><a href="{{route('fr.reports.earned-commission')}}"><i class="fa fa-circle-o"></i>Earnings</a></li>
				<!--li><a href="{{route('fr.withdrawal.create')}}"><i class="fa fa-circle-o"></i>Earned Commission</a></li-->
				<li><a href="{{route('fr.reports.merchant-due')}}"><i class="fa fa-circle-o"></i>Merchant’s Due</a></li>
				<li><a href="{{route('fr.reports.activity_log')}}"><i class="fa fa-circle-o"></i>Activity Log History</a></li>				
			  </ul>
			</li>
			<li class="treeview">
			  <a href="#"><i class="fa fa-dashboard"></i> <span>Manage Merchants</span><span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span></a>
			  <ul class="treeview-menu">            
				<li><a href="{{route('fr.merchants.create')}}"><i class="fa fa-circle-o"></i>Add Merchant</a></li>
				<li><a href="{{route('fr.merchants.kyc')}}"><i class="fa fa-circle-o"></i>Merchant KYC</a></li>
				<li><a href="{{route('fr.merchants.list')}}"><i class="fa fa-circle-o"></i>Manage Merchant</a></li>				
			  </ul>
			</li>
			<li class="treeview">
			  <a href="#"><i class="fa fa-dashboard"></i> <span>Manage Users</span><span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span></a>
			  <ul class="treeview-menu">            
				<li><a href="{{route('fr.user.create')}}"><i class="fa fa-circle-o"></i>Add User</a></li>
				<li><a href="{{route('fr.user.list')}}"><i class="fa fa-circle-o"></i>Manage User</a></li>
			  </ul>
			</li>
		</ul>
	</section>
	<!-- /.sidebar -->
</aside>