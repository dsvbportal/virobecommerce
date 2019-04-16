<div class="col-md-3 left_col">
          <div class="left_col scroll-view">
            <div class="navbar nav_title">
              <div class="img-responsive text-center logopng">
              <img src="{{asset('resources/assets/admin/new_theme/img/logo.png')}}">
              </div>
            </div>

            <div class="clearfix"></div>


            <div class="profile clearfix">
              <div class="profile_pic">

              </div>
              <div class="profile_info pull-right">
                <span>Admin@virob.com</span>
              </div>
            </div>
            <!-- sidemenus starts -->
            <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
              <div class="menu_section">
            
                <ul class="nav side-menu">
                  <li><a href="{{URL::to('admin/dashboard')}}"><i class="fa fa-tachometer" aria-hidden="true"></i> Dashboard </a>
                  </li>

                  <li><a><i class="fa fa-edit"></i> Affiliate Management <span class="fa fa-chevron-down"></span></a>

					<ul class="nav child_menu">
                    <li><a href="{{route('admin.aff.create')}}">Create Root Affiliate</a></li>
                    <li><a href="{{route('admin.aff.manage_root_affiliate')}}">Manage Root Affiliate</a></li>
			        <li><a href="{{route('admin.aff.free_affiliate')}}">Free Affiliate</a></li>
                    <li><a href="{{route('admin.aff.view')}}">Manage Affiliate</a></li>
					<li><a href="{{route('admin.aff.verification')}}">Manage Affiliate KYC</a></li>
				    <li><a href="{{route('admin.aff.qlogin')}}" id="quick-login-modal">Quick Login</a></li>
                    <li><a href="#">Affiliate Activity Logs</a></li>
                    <li><a href="{{route('admin.aff.activation_mail')}}">Resend Activation Mail</a></li>
                </ul>
                  </li>
                  <li><a><i class="fa fa-desktop"></i>Franchisee Management <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
						<li><a href="{{route('admin.franchisee.create')}}">Create Franchisee</a></li>
						<li><a href="{{route('admin.franchisee.list')}}">Manage Franchisee</a></li>
						<li><a href="{{route('admin.franchisee.access.edit')}}">Franchisee Mapping</a></li>
						<li><a href="{{route('admin.franchisee.activity')}}">Franchisee Activity Log</a></li>
						<li><a href="{{route('admin.franchisee.quick-login')}}">Quick Login</a></li>
						<li><a href="{{route('admin.franchisee.kyc-verification')}}">Manage Franchisee KYC</a></li>
						<li><a href="{{route('admin.franchisee.fundtransfer_commission')}}">Franchisee Commission</a></li>
					</ul>
                  </li>
                  <li><a><i class="fa fa-table"></i>Finance Management <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                          <li><a class="load-content" href="{{route('admin.finance.fund-transfer.to_member')}}">Fund Credit & Debit</a></li>
                    <?php /*<li><a class="load-content" href="{{route('admin.finance.fund-transfer.to_member')}}">Member Credit & Debit</a></li>
					<li><a class="load-content" href="{{route('admin.finance.fund-transfer.to_affiliate')}}">Affiliate Credit & Debit</a></li>
                    <li><a class="load-content" href="{{route('admin.finance.fund-transfer.dsa')}}">DSA credit & debit</a></li> */?>
                    <li><a class="load-content" href="{{route('admin.finance.fund-transfer-history')}}">Member Fund Transfer history</a></li>
                    <li><a class="load-content" href="{{route('admin.finance.admin-credit-debit-history')}}">Admin Credit & Debits log</a></li>
                    <li><a class="load-content" href="{{route('admin.finance.pg-transcation')}}">Payment Gateway Transactions</a></li>
                    <li><a class="load-content" href="{{route('admin.finance.wallet-transcation')}}">Wallet Transcation</a></li>
                    <li><a class="load-content" href="{{route('admin.finance.transaction-log')}}">Transaction</a></li>
                    </ul>
                  </li>
                  <li><a><i class="fa fa-bar-chart-o"></i> Withdrawals <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                                          <li><a class="load-content" href="{{route('admin.withdrawals.list')}}">Manage Withdrawals</a><li>

                    <li><a href="{{route('admin.withdrawals.history',['status'=>'history'])}}">Withdrawal History</a></li>
                    </ul>
                  </li>
                  <li><a><i class="fa fa-clone"></i>Commission Reports <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                     <li><a href="#">Personal Cusotmer Commission Report</a></li>
                    <li><a href="{{route('admin.commission.ambassador')}}">Ambassador Bonus Report</a></li>
                    <li><a href="{{route('admin.commission.faststart_bonus')}}">Fast Track Bonus Report</a></li>
                    <li><a href="{{route('admin.commission.team')}}">Team Commission Report</a></li>
                    <li><a href="{{route('admin.commission.leadership')}}">Leadership Bonus Report</a></li>
					<li><a href="{{route('admin.commission.car')}}">Car Bonus Report</a></li>
                    <li><a href="{{route('admin.commission.star')}}">Star Bonus Report</a></li>
                    <li><a href="{{route('admin.commission.ranks')}}">Ranking Report</a></li>

                    </ul>
                  </li>


                   <li><a><i class="fa fa-file-text-o" aria-hidden="true"></i>Sales Report <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                      <li><a href="{{route('admin.report.purchase_history')}}">Packages Report</a></li>
                    <li><a href="#">Upgrade Report</a></li>
                    <li><a href="#">Commission Volume Report</a></li>
                    <li><a href="{{route('admin.report.qualified_volume')}}">Qualified Volume Report</a></li>
                    <li><a href="#">Joining Report</a></li>


                    </ul>
                  </li>


                   <li><a><i class="fa fa-suitcase" aria-hidden="true"></i>Wallet Report <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                     <li><a href="{{route('admin.finance.ewallet')}}">eWallet Balance Report</a></li>
                    <li><a href="">Funds Added Report</a></li>
                    <li><a href="">Payout Report</a></li>
                         </ul>
                         </li>


                    <li><a><i class="fa fa-bookmark-o" aria-hidden="true"></i>Tax Report <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                      <li><a href="demo">GST Report</a></li>
                      <li><a href="demo">TDS Report</a></li>
                         </ul>
                         </li>

                  <li><a><i class="fa fa-download" aria-hidden="true"></i>Download <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                      <li><a href="demo">Upload Document</a></li>
                      <li><a href="demo">Manage Downloads</a></li>
                         </ul>
                         </li>

                   <li><a><i class="fa fa-users" aria-hidden="true"></i>Support Management <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                      <li><a href="demo">Support Tickets</a></li>
                         </ul>
                         </li>

                   <li><a><i class="fa fa-user-plus" aria-hidden="true"></i>Sub-Admin Management <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                      <li><a href="demo">Create Sub-Admin</a></li>
                      <li><a href="demo">Manage Sub-Admin</a></li>
                      <li><a href="demo">Admin Types</a></li>
                      <li><a href="demo">Admin Department</a></li>
                         </ul>
                         </li>

                  <li><a><i class="fa fa-wrench" aria-hidden="true"></i>
Settings <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                      <li><a href="demo">Withdrawal Settings</a></li>
                      <li><a href="demo">NGO Wallet Deduction</a></li>
                      <li><a href="demo">Social Media Links</a></li>
                      <li><a href="demo">Manage Tax</a></li>
                      <li><a href="demo">Sign up</a></li>
                      <li><a href="demo">Buy/Upgrade Package</a></li>
                         </ul>
                         </li>


                   <li><a><i class="fa fa-shield" aria-hidden="true"></i>
Security Settings <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                      <li><a href="demo">Two Factor Authentication</a></li>
                      <li><a href="demo">IP Adress Security</a></li>
                    	<li><a href="{{route('admin.settings.change-pwd')}}">Change Password</a></li>
                         </ul>
                         </li>

                </ul>

              </div>
              <div class="menu_section">
                <ul class="nav side-menu">
                  <li><a href="javascript:void(0)"> <span class="label label-success pull-right"></span></a></li>
                </ul>
              </div>
              </div>
              </div>
            </div>
