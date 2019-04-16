@extends('member.layouts.dashboard')
@section('pagetitle')
Products 
@stop
@section('layoutContent')

{{ HTML::style('supports/member/rating/scripts/rateit.css') }}
<div class="pageheader">
    <h2><i class="fa fa-shopping-cart"></i>Redemptions </h2>
    <div class="breadcrumb-wrapper">
     <span class="label">You are here:</span>
      <ol class="breadcrumb">
      <li><a href="user/dashboard">Dashboard</a></li>
      <li><a href="user/products/list">Redemptions</a></li>
       <li class="active">Products</li>
      </ol>
    </div>
</div>
<div class="contentpanel" >
    <div class="panel panel-default" id="cart_details">
        <div class="panel-body">
            <div class="row">
               
                <div class="col-sm-4">
                    <input type="text" class="form-control filters" id="search_term" placeholder="Search">
                </div>
                <div class="col-sm-2">
                    <input type="button" class="filters btn btn-primary btn-sm" id="search_btn" value="Search">
                </div>
                <div class="col-sm-1 ">
                    <span class="hidden btn btn-warning btn-sm" id="products_display">
                        <i class="fa fa-list" id="list_processing"></i> Browse All Products
                    </span>
                </div>
                <div class="col-sm-5"> </div>
                <div class="col-sm-1 pull-right">
                    <span class="btn btn-warning btn-sm" id="shopping_cart" >
                        <i class="fa fa-shopping-cart" id="cart_processing"></i> Cart
                        &nbsp;<span id="item_count">
                            <span class="badge" >
                                <?php
                                if (Session::has('product_count'))
                                {
                                    echo Session::get('product_count');
                                }
                                else
                                {
                                    echo '0';
                                }
                                ?>
                            </span>
                        </span>
                    </span>
                </div>
                <div class="col-sm-1 pull-right">
                    <div class="btn-group mr5">
                        <button type="button" class="btn btn-sm btn-primary dropdown-toggle" data-toggle="dropdown">
                          Filters <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu" role="menu">
                          <li id="latest_products"><a> Latest</a></li>
                          <li id="popular_products"><a> Popular</a></li>
                          <li id="price_order_min"><a> Price (Min to Max)
                            <span class="fa fa-caret-up"></span></a>
                          </li>
                          <li id="price_order_max"><a> Price (Max to Min)
                            <span class="fa fa-caret-down"></span></a>
                          </li>
                          <!--li id="price_order_max" style="display:none;"><a> <i class="fa fa-list"></i> &nbsp;&nbsp; Price 
                            <span class="fa fa-caret-up"></span></a>
                          </li-->
                        </ul>
                    </div>
                </div>
                <!--div class="col-sm-1">
                    <span class="filters btn btn-warning btn-sm" id="latest_products">
                        <i class="fa fa-list"></i> Latest
                    </span>
                </div>
                <div class="col-sm-1">
                    <span class="filters btn btn-warning btn-sm" id="popular_products">
                        <i class="fa fa-list"></i> Popular
                    </span>
                </div>
                <div class="col-sm-1">
                    <span class="filters btn btn-warning btn-sm" id="price_order_min">
                        <i class="fa fa-list"></i> Price
                        <span class="fa fa-caret-down"></span>
                    </span>
                    <span class="filters btn btn-warning btn-sm" id="price_order_max" style="display:none;">
                        <i class="fa fa-list"></i> Price
                        <span class="fa fa-caret-up"></span>
                    </span>
                </div-->
                
               
                
                <div class="col-sm-1 pull-right">
                </div>
            </div>
        </div>
    </div>
    <div class="row" id="page_content">
        <span id="product_details"></span>
        <div class="col-sm-9">
            <div id="msg"></div>
            <div class="row filemanager" id="prod" style="margin-top:-15px;">
                <table class="" id="product_list">
                </table>
            </div>
        </div>
        <div class="col-sm-3" id="wall">
            
            @if (!empty($wallet))
                @if ($wallet->current_balance > 0)
                <div class="alert alert-warning">    
                      <input type="hidden" value="<?php echo round($wallet->current_balance);?>" id="wal_balance" 
                      class="wall_bal">                
                      <h5>Wallet Balance: <span id="wallet"><?php echo round($wallet->current_balance) . ' Points'; ?></span><h5>
                </div>
                <div class="mb20"></div>
                 @endif
               @endif
            
           <div class="panel panel-default"> 
            <div class="fm-sidebar panel-body" >
              
                <h5 class="subtitle"><strong>Categories</strong></h5>
                @if(!empty($category))
                <div style="height:100px; overflow:auto;">
                @foreach($category as $val)                
            &nbsp;&nbsp;&nbsp;<input type="checkbox" id="search" name="category" value="{{$val->category_id}}"> &nbsp;&nbsp;&nbsp;{{$val->category_name}} <br>
                @endforeach
                </div>
                @endif
              
                <div class="mb30"></div>
                <h5 class="subtitle"><strong>Brands</strong></h5>
                @if(!empty($brand))
                <div style="height:100px; overflow:auto;">
                @foreach($brand as $val)
                &nbsp;&nbsp;&nbsp;<input type="checkbox" id="search" name="brand" value="{{$val->brand_id}}"> &nbsp;&nbsp;&nbsp;{{$val->brand_name}} 
                <br>
                @endforeach
                </div>
                @endif
                <div class="mb1"></div>
            </div>
           </div>
        </div><!-- col-sm-3 -->
        <input type="hidden" id="cat_id" name="cat_id">
        <input type="hidden" id="brand_id" name="brand_id" >
        <input type="hidden" id="order_term" name="order_term" value="{{$order_term or ''}}">
    </div>
    <div id="s_cart"></div>
</div>
<div class="modal fade" id="products" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" style="width: 650px;">
        <div class="modal-content">
            <div class="modal-body">
            </div>
        </div>
    </div>
</div>
{{'<script>'.((isset($product_id)?'var product_id = '.$product_id.';':'var product_id = "";')).'</script>'}}
@stop
@section('scripts')
{{ HTML::script('supports/member/products.js') }}
{{ HTML::script('supports/member/rating/scripts/jquery.rateit.js') }}
@stop
