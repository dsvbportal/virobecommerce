@extends('ecom.layouts.layout')

@section('page-content')


<!-- content -->

<div class="contentpanel" >
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-default">
                <div class="panel-body">    
					<br>

					<form class="form-horizontal" id="update_email" action="#" method="post" autocomplete="off" align="text-center">
						 


                             
                  <div class="col-sm-2">
              </div>
              <div class="col-sm-8">
               
                   @if(!empty($rs))

                         @if(!empty($rs['status']==200))
            
                        <div class="alert alert-success">
                         <strong>Success!</strong>{{$rs['msg']}}.
                            </div>
                            @else
                             <div class="alert alert-danger">
                          <strong>Sorry!</strong>{{$rs['msg']}}.
                            </div>

                        @endif                       

                      @endif     
                       
              </div>
              <div class="col-sm-2 ">
                
            </div>
						
						
					</form>
                </div>
            </div>
        </div>
    </div>
</div>





<!-- content -->


@section('scripts')
<!-- script src="{{asset('validate/lang/login')}}" charset="utf-8"></script-->
<script type="text/javascript" src="{{asset('js/providers/ecom/account/profile.js')}}"></script> 
@stop

@stop