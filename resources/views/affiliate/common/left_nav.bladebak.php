<!-- Left side column. contains the logo and sidebar -->
<aside class="main-sidebar">
	<!-- sidebar: style can be found in sidebar.less -->
	<section class="sidebar">            
		<!-- sidebar menu: : style can be found in sidebar.less -->
		<ul class="sidebar-menu">
			<li class="header">MAIN NAVIGATION</li>
			<li><a href="{{route('aff.dashboard')}}"><i class="fa fa-book"></i> <span>Overview</span></a></li>
			<li class="treeview">
			  <a href="#"><i class="fa fa-dashboard"></i> <span>Commissions</span><span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span></a>
			  <ul class="treeview-menu">
				<li><a href="{{route('aff.profile')}}"><i class="fa fa-circle-o"></i>Customer Commissions</a></li>
				<li><a href="{{route('aff.profile.kyc')}}"><i class="fa fa-circle-o"></i>Affiliate Commissions</a></li>
			  </ul>
			</li>
			<li class="treeview">
			  <a href="#"><i class="fa fa-dashboard"></i> <span>Organization</span><span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span></a>
			  <ul class="treeview-menu">
				<li><a href="{{route('aff.referrals.mydirects')}}"><i class="fa fa-circle-o"></i>Directs Affiliate</a></li>			
				<li><a href="{{route('aff.referrals.myteam')}}"><i class="fa fa-circle-o"></i>Group Affiliate</a></li>
				<li><a href="{{route('aff.referrals.my_geneology')}}"><i class="fa fa-circle-o"></i>Geneaology</a></li>
			  </ul>
			</li>
			<li class="treeview">
			  <a href="#"><i class="fa fa-dashboard"></i> <span>Orders</span><span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span></a>
			  <ul class="treeview-menu">
				<li><a href="{{route('aff.package.browse')}}"><i class="fa fa-circle-o"></i>Order Report</a></li>
				<li><a href="{{route('aff.package.upgrade_history')}}"><i class="fa fa-circle-o"></i>Activate Package</a></li>
				<li><a href="{{route('aff.package.my_packages')}}"><i class="fa fa-circle-o"></i>My Packages</a></li>            
			  </ul>
			</li>
			<li class="treeview">
			  <a href="#"><i class="fa fa-dashboard"></i> <span>Affiliate Tools </span><span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span></a>
			  <ul class="treeview-menu">
				<li><a href="{{route('aff.referrals.refer-and-earn')}}"><i class="fa fa-circle-o"></i>Refer & Earn</a></li>
				<li><a href="{{route('aff.package.upgrade_history')}}"><i class="fa fa-circle-o"></i>Add Lead</a></li>
				<li><a href="{{route('aff.package.my_packages')}}"><i class="fa fa-circle-o"></i>View Lead</a></li>            
				<li><a href="{{route('aff.package.my_packages')}}"><i class="fa fa-circle-o"></i>Graph</a></li> 
			  </ul>
			</li>
			<li class="treeview">
			  <a href="#"><i class="fa fa-dashboard"></i> <span>Reports</span><span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span></a>
			  <ul class="treeview-menu">            
				<li><a href="{{route('aff.reports.personal_commission')}}"><i class="fa fa-circle-o"></i> App Install Report</a></li>
				<li><a href="{{route('aff.reports.ambassador_bonus')}}"><i class="fa fa-circle-o"></i> Traffic Report</a></li>
				<li><a href="{{route('aff.reports.fast_start')}}"><i class="fa fa-circle-o"></i> Payment Report</a></li>            
				<li><a href="{{route('aff.reports.team_bonus')}}"><i class="fa fa-circle-o"></i> Rank Performance Report</a></li>  
			  </ul>
			</li>
			<li class="treeview">
			  <a href="#"><i class="fa fa-dashboard"></i> <span>Support & Help</span><span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span></a>
			  <ul class="treeview-menu">            
				<li><a href="{{route('aff.reports.personal_commission')}}"><i class="fa fa-circle-o"></i> Download Application Form</a></li>
				<li><a href="{{route('aff.reports.ambassador_bonus')}}"><i class="fa fa-circle-o"></i> Place a Request</a></li>
				<li><a href="{{route('aff.reports.fast_start')}}"><i class="fa fa-circle-o"></i> Downloads</a></li>            
				<li><a href="{{route('aff.reports.team_bonus')}}"><i class="fa fa-circle-o"></i> FAQs</a></li>  
				<li><a href="{{route('aff.reports.fast_start')}}"><i class="fa fa-circle-o"></i> Updates</a></li>            
				<li><a href="{{route('aff.reports.team_bonus')}}"><i class="fa fa-circle-o"></i> Product Price List</a></li> 
			  </ul>
			</li>
			<li class="treeview">
			  <a href="#"><i class="fa fa-dashboard"></i> <span>My Account</span><span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span></a>
			  <ul class="treeview-menu">            
				<li><a href="{{route('aff.profile')}}"><i class="fa fa-circle-o"></i>My Profile</a></li>
				<li><a href="{{route('aff.profile.kyc')}}"><i class="fa fa-circle-o"></i>KYC Verification</a></li> 
				<li><a href="{{route('aff.settings.change_pwd')}}"><i class="fa fa-circle-o"></i>Change Password</a></li>
				<li><a href="{{route('aff.settings.securitypin')}}"><i class="fa fa-circle-o"></i>Change Security Password</a></li>
				<li><a href="{{route('aff.settings.change_email')}}"><i class="fa fa-circle-o"></i>Change Email</a></li>
				<li><a href="{{route('aff.settings.payouts')}}"><i class="fa fa-circle-o"></i>Payout Settings</a></li>
			  </ul>
			</li>
			<li class="treeview">
			  <a href="#"><i class="fa fa-dashboard"></i> <span>Transactions</span><span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span></a>
			  <ul class="treeview-menu">            
				<li><a href="{{route('aff.ranks.myrank')}}"><i class="fa fa-circle-o"></i>Transaction Details</a></li>
				<li><a href="{{route('aff.ranks.history')}}"><i class="fa fa-circle-o"></i>Payout History</a></li>
				<li><a href="{{route('aff.ranks.eligibilities')}}"><i class="fa fa-circle-o"></i>Request Payout</a></li>
			  </ul>
			</li>
		</ul>
	</section>
	<!-- /.sidebar -->
</aside>