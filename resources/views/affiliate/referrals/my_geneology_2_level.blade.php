@extends('affiliate.layout.dashboard')
@section('title',"My Geneology")
@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>My Geneology</h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li >Referrals</li>
        <li class="active">My Geneology</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-8">
            <div class="panel">
                <div class="panel-heading"><h4 class="panel-title">Genealogy View</div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-6 tree_icon_desc">
							<span><img src="{{asset('resources/assets/themes/affiliate/img/Active.svg')}}">  Active</span>                            
                            <span><img src="{{asset('resources/assets/themes/affiliate/img/Disabled.svg')}}"> Blocked</span>
                            <span><i class="fa fa-plus"></i> Open</span>
                        </div>
                        <div class="col-md-6">
                            <form id="geneologyFrm" action="{{route('aff.referrals.geneology.search')}}">
                                <input type="hidden" id="curuser" value="{{$userSess->uname}}" />
                                <div class="input-group input-info">
                                    <input id="uname" type="text" class="form-control" value="{{$userSess->uname}}" >
                                    <span class="input-group-btn">
                                        <button class="btn btn-success" id="searchBtn" type="button">Search</button>
                                    </span>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="gtree" id="gtree1" style="display:none">
                        <ul>
                            <li>
                                <a data-id="" id="t1" class="gtempty"></a>
                                <ul>
                                    <li><a data-id="" id="t11" class="gtempty"></a></li>
                                    <li><a data-id="" id="t12" class="gtempty"></a></li>
                                    <li><a data-id="" id="t13" class="gtempty"></a></li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                    <div class="gtree" id="gtree2" style="display:none">
                        <ul>
                            <li>
                                <a data-id="" id="t2" class="gtempty"></a>
                                <ul>
                                    <li><a data-id="" id="t21" class="gtempty"></a></li>
                                    <li><a data-id="" id="t22" class="gtempty"></a></li>
                                    <li><a data-id="" id="t23" class="gtempty"></a></li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                    <div class="gtree" id="gtree3" style="display:none">
                        <ul>
                            <li>
                                <a data-id="" id="t3" class="gtempty"></a>
                                <ul>
                                    <li><a data-id="" id="t31" class="gtempty"></a></li>
                                    <li><a data-id="" id="t32" class="gtempty"></a></li>
                                    <li><a data-id="" id="t33" class="gtempty"></a></li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4" id="user_info">
            <h4><i class="fa fa-user"></i><b id="tree_acfullname">Suresh Kumar</b><span>Username: <b id="tree_acuname">sureshkumar</b></span></h4>
            <div class='clearfix'></div>
            <table class="table table-stripped">
                <tr><th width="35%">Sponsor ID</th><td id="tree_acsponser_uname"></td></tr>
                <tr><th>Upline ID</th><td id="tree_acupline_uname"></td></tr>
                <tr><th>Self QV</th><td id="tree_acqv"></td></tr>
                <tr><th>Self CV</th><td  id="tree_accv"></td></tr>
                <tr><th>Joining Date</th><td id="tree_acsignedup_on"></td></tr>
            </table>
            <div class="col-md-12">
                <table class="table table-striped">
                    <tr><th></th><th class="text-center bg-grey">Aff.Counts</th><th class="text-center bg-grey">QV</th><th class="text-center bg-grey">CV</th></tr>
                    <tr><th  class="bg-aqua">IG</th><td class="af1G_cnts text-center"></td><td class="af1G_qv text-center"></td><td class="af1G_cv text-center"></td></tr>
                    <tr><th class="bg-aqua">2G</th><td class="af2G_cnts text-center"></td><td class="af2G_qv text-center"></td><td class="af2G_cv text-center"></td></tr>
                    <tr><th class="bg-aqua">3G</th><td class="af3G_cnts text-center"></td><td class="af3G_qv text-center"></td><td class="af3G_cv text-center"></td></tr>
                </table>
            </div>
        </div>
    </div>
</section>
<!-- /.content -->
@stop
@section('scripts')
<script src="{{asset('js/providers/affiliate/referrals/my_geneology.js')}}"></script>
@stop



