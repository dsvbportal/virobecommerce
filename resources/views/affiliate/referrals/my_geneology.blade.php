@extends('affiliate.layout.dashboard')
@section('title',"My Genealogy")
@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>Generation View</h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li >Referrals</li>
        <li class="active">Generation View</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="panel">
                <div class="panel-body" style="padding-bottom:45px;">
                    <div class="row">

                        <form id="geneologyFrm" action="{{route('aff.referrals.geneology.search')}}">
                            <input type="hidden" id="curuser" value="{{$userSess->uname}}" />
                             <div class="col-md-6">
                            <div class="input-group input-info">
                                <input id="uname" type="text" class="form-control" value="{{$userSess->uname}}" >
                                <span class="input-group-btn">
                                    <button class="btn btn-success" id="searchBtn" type="button"><i class="fa fa-search"></i> Search</button>
                                </span>
                            </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group text-left">
                                    <button class="btn btn-warning" id="refreshBtn" type="button"><i class="fa fa-refresh"></i> Reset Search</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="generation_panel">
                    <div class="gtree" id="gtree1" style="display:none">
                        <ul>
                            <li>
                                <a data-id="" id="t1" class="gtempty"></a>
                                <div class="col-md-3 col-sm-12" id="user_info" style="display: none;">
								<div id="tree_acuname">Account ID: <b></b></div>
							<div class="col-md-12">
								<table class="table table-stripped">
								  <tr>
									<th>Sponsor ID</th>
									<td id="tree_acsponser_uname">-</td>
									<th>Upline ID</th>
									<td id="tree_acupline_uname">-</td>
								  </tr>
								  <tr>
									<th>Self QV</th>
									<td id="tree_acqv">-</td>
									<th>Self CV</th>
									<td id="tree_accv">-</td>
								  </tr>
								  <tr>
									<th>Joining Date</th>
									<td colspan="3" id="tree_acsignedup_on">-</td>
									</tr>
								</table>
							</div>
							<div class="col-md-12">
								<table class="table table-striped">
									<tr><th></th><th class="text-center bg-grey">Aff.Counts</th><th class="text-center bg-grey">QV</th><th class="text-center bg-grey">CV</th></tr>
									<tr><th  class="bg-aqua">IG</th><td class="af1G_cnts text-center">-</td><td class="af1G_qv text-center">-</td><td class="af1G_cv text-center">124,580</td></tr>
									<tr><th class="bg-aqua">2G</th><td class="af2G_cnts text-center">-</td><td class="af2G_qv text-center">-</td><td class="af2G_cv text-center">124,580</td></tr>
									<tr><th class="bg-aqua">3G</th><td class="af3G_cnts text-center">-</td><td class="af3G_qv text-center">-</td><td class="af3G_cv text-center">124,580</td></tr>
								</table>
							</div>
						</div>
                                <ul>
                                    <li>
                                        <a data-id="" id="t11" class="gtempty"></a>
                                        <ul>
                                            <li><a data-id="" id="t111" class="gtempty"></a></li>
                                            <li><a data-id="" id="t112" class="gtempty"></a></li>
                                            <li><a data-id="" id="t113" class="gtempty"></a></li>
                                        </ul>
                                    </li>
                                    <li>
                                        <a data-id="" id="t12" class="gtempty"></a>
                                        <ul>
                                            <li><a data-id="" id="t121" class="gtempty"></a></li>
                                            <li><a data-id="" id="t122" class="gtempty"></a></li>
                                            <li><a data-id="" id="t123" class="gtempty"></a></li>
                                        </ul>
                                    </li>
                                    <li>
                                        <a data-id="" id="t13" class="gtempty"></a>
                                        <ul>
                                            <li><a data-id="" id="t131" class="gtempty"></a></li>
                                            <li><a data-id="" id="t132" class="gtempty"></a></li>
                                            <li><a data-id="" id="t133" class="gtempty"></a></li>
                                        </ul>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                    <div class="gtree" id="gtree2" style="display:none">
                        <ul>
                            <li>
                                <a data-id="" id="t2" class="gtempty"></a>
                                <ul>
                                    <li>
                                        <a data-id="" id="t21" class="gtempty"></a>
                                        <ul>
                                            <li><a data-id="" id="t211" class="gtempty"></a></li>
                                            <li><a data-id="" id="t212" class="gtempty"></a></li>
                                            <li><a data-id="" id="t213" class="gtempty"></a></li>
                                        </ul>
                                    </li>
                                    <li>
                                        <a data-id="" id="t22" class="gtempty"></a>
                                        <ul>
                                            <li><a data-id="" id="t221" class="gtempty"></a></li>
                                            <li><a data-id="" id="t222" class="gtempty"></a></li>
                                            <li><a data-id="" id="t223" class="gtempty"></a></li>
                                        </ul>
                                    </li>
                                    <li>
                                        <a data-id="" id="t23" class="gtempty"></a>
                                        <ul>
                                            <li><a data-id="" id="t231" class="gtempty"></a></li>
                                            <li><a data-id="" id="t232" class="gtempty"></a></li>
                                            <li><a data-id="" id="t233" class="gtempty"></a></li>
                                        </ul>
                                    </li>
                                </ul>
                            </li>
                        </ul>

                    </div>
                    <div class="gtree" id="gtree3" style="display:none">
                        <ul>
                            <li>
                                <a data-id="" id="t3" class="gtempty"></a>
                                <ul>
                                    <li>
                                        <a data-id="" id="t31" class="gtempty"></a>
                                        <ul>
                                            <li><a data-id="" id="t311" class="gtempty"></a></li>
                                            <li><a data-id="" id="t312" class="gtempty"></a></li>
                                            <li><a data-id="" id="t313" class="gtempty"></a></li>
                                        </ul>
                                    </li>
                                    <li>
                                        <a data-id="" id="t32" class="gtempty"></a>
                                        <ul>
                                            <li><a data-id="" id="t321" class="gtempty"></a></li>
                                            <li><a data-id="" id="t322" class="gtempty"></a></li>
                                            <li><a data-id="" id="t323" class="gtempty"></a></li>
                                        </ul>
                                    </li>
                                    <li>
                                        <a data-id="" id="t33" class="gtempty"></a>
                                        <ul>
                                            <li><a data-id="" id="t331" class="gtempty"></a></li>
                                            <li><a data-id="" id="t332" class="gtempty"></a></li>
                                            <li><a data-id="" id="t333" class="gtempty"></a></li>
                                        </ul>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>

                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 tree_icon_desc">
        <div class="icon"><img src="{{asset('resources/assets/themes/affiliate/dist/img/Active.png')}}"> Active </div>
				<div class="icon"><img src="{{asset('resources/assets/themes/affiliate/dist/img/open.png')}}"> Open  </div>
				<div class="icon"><img src="{{asset('resources/assets/themes/affiliate/dist/img/Blocked.png')}}"> Blocked</div>
    </div>

    </div>
</section>
<!-- /.content -->
@stop
@section('scripts')
<script src="{{asset('js/providers/affiliate/referrals/my_geneology.js')}}"></script>
@stop
