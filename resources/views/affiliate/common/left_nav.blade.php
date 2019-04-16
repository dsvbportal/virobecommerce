<!-- Left side column. contains the logo and sidebar -->
<aside class="main-sidebar">
	<!-- sidebar: style can be found in sidebar.less -->
	<section class="sidebar">
		<!-- sidebar menu: : style can be found in sidebar.less -->
		<ul class="sidebar-menu">
			<li class="header">MAIN NAVIGATION</li>
			@if($userSess->can_sponsor)
			<li><a href="{{route('aff.dashboard')}}"><i class="material-icons">home</i> <span>Dashboard</span></a></li>
			@endif
			<li class="treeview">
			  <a href="#"><i class="material-icons">perm_identity</i> <span>My Account</span><span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span></a>
			  <ul class="treeview-menu">
				<li><a href="{{route('aff.profile')}}"><i class="fa fa-circle-o"></i>View/Edit Profile</a></li>
				<!--<li><a href="{{route('aff.profile.kyc')}}"><i class="fa fa-circle-o"></i>KYC Verification</a></li>-->
			  </ul>
			</li>
			<li class="treeview">
			  <a href="#"><i class="material-icons">shopping_cart</i> <span>SAP</span><span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span></a>
			  <ul class="treeview-menu">
				<li><a href="{{route('aff.package.browse')}}"><i class="fa fa-circle-o"></i>Buy SAP</a></li>
				@if($userSess->can_sponsor)
				<li><a href="{{route('aff.package.my_packages')}}"><i class="fa fa-circle-o"></i>My SAP</a></li>
				@endif
				<li><a href="{{route('aff.package.purchase-history')}}"><i class="fa fa-circle-o"></i>Purchase History</a></li>
			  </ul>
			</li>
			
			<li class="treeview">
			  <a href="#"><i class="material-icons">redeem</i> <span>Referrals</span><span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span></a>
			  <ul class="treeview-menu">
				<li><a href="{{route('aff.referrals.my_referred_customers')}}"><i class="fa fa-circle-o"></i>My Referred Customers</a></li>
				@if($userSess->can_sponsor)
				<li><a href="{{route('aff.referrals.mydirects')}}"><i class="fa fa-circle-o"></i>My Referred Affiliates</a></li>
				<li><a href="{{route('aff.referrals.myteam')}}"><i class="fa fa-circle-o"></i>Team Generation Report</a></li>
				<?php /* <li><a href="{{route('aff.referrals.my_geneology')}}"><i class="fa fa-circle-o"></i>Team Generation View</a></li>*/?>
				@endif
				<li><a href="{{route('aff.referrals.refer-and-earn')}}"><i class="fa fa-circle-o"></i>Refer & Earn</a></li>
			  </ul>
			</li>
			<li class="treeview">
			  <a href="#"><i class="material-icons">account_balance_wallet</i><span>Wallet</span><span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span></a>
			  <ul class="treeview-menu">

				<li><a href="{{route('aff.wallet.balance')}}"><i class="fa fa-circle-o"></i>My Wallet</a></li>
				@if($userSess->can_sponsor)
				<li><a href="{{route('aff.wallet.fundtransfer')}}"><i class="fa fa-circle-o"></i>Fund Transfer</a></li>
				<li><a href="{{route('aff.wallet.fundtransfer.history')}}"><i class="fa fa-circle-o"></i>Transfer History</a></li>
				@endif
				<li><a href="{{route('aff.wallet.transactions')}}"><i class="fa fa-circle-o"></i>Transactions</a></li>
				</ul>
			</li>
			<li class="treeview">
			  <a href="#"><i class="material-icons">move_to_inbox</i><span>Withdrawals</span><span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span></a>
			  <ul class="treeview-menu">
				<li><a href="{{route('aff.withdrawal.requst')}}"><i class="fa fa-circle-o"></i>Withdraw</a></li>
				<li><a href="{{route('aff.withdrawal.history',['status'=>'pending'])}}"><i class="fa fa-circle-o"></i>Withdrawal History</a></li>
			  </ul>
			</li>
			<li class="treeview">
			  <a href="#"><i class="fa fa-percent" aria-hidden="true"></i> <span>Commissions</span><span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span></a>
			  <ul class="treeview-menu">
				<li><a href="{{route('aff.reports.personal_commission')}}"><i class="fa fa-circle-o"></i>Customer Commissions</a></li>
				@if($userSess->can_sponsor)
				<li><a href="{{route('aff.reports.fast_start_bonus')}}"><i class="fa fa-circle-o"></i>Affiliate Commissions</a></li>
				@endif
			  </ul>
			</li>
			<li><a href="{{route('aff.reports.tds-deducted-report')}}"><i class="material-icons">description</i> <span>Tax Report</span></a></li>
			@if($userSess->can_sponsor)
			<li><a href="{{route('aff.ranks.myrank')}}"><i class="fa fa-trophy" aria-hidden="true"></i> <span>My Rank</span></a></li>
			@endif
			<?php /*
			<li class="treeview">
			  <a href="#"><i class="fa fa-file-text-o" aria-hidden="true"></i> <span>Reports</span><span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span></a>
			  <ul class="treeview-menu">
				<li><a data-href=""><i class="fa fa-circle-o"></i> App Install Report</a></li>
				<li><a data-href=""><i class="fa fa-circle-o"></i> Traffic Report</a></li>
				<li><a data-href=""><i class="fa fa-circle-o"></i> Payment Report</a></li>
				<li><a data-href=""><i class="fa fa-circle-o"></i> Rank Performance Report</a></li>
				<li><a data-href=""><i class="fa fa-circle-o"></i> TDS Deducted Report</a></li>
			  </ul>
			</li>
			*/?>
			<li class="treeview">
			  <a href="#"><i class="fa fa-headphones" aria-hidden="true"></i> <span>Support & Help</span><span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span></a>
			  <ul class="treeview-menu">
				<li><a data-href=""><i class="fa fa-circle-o"></i> Tickets</a></li>
				<li><a data-href="#"><i class="fa fa-circle-o"></i> FAQs</a></li>
				<li><a href="{{route('aff.support.downloads')}}"><i class="fa fa-circle-o"></i> Downloads</a></li>
				<li><a href="{{route('aff.support.announcements')}}"><i class="fa fa-circle-o"></i> Announcements</a></li>
			  </ul>
			</li>
		</ul>
	</section>
	<!-- /.sidebar -->
</aside>
