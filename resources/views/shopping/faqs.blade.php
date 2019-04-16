@extends('ecom.layouts.content_page')
@section('pagetitle')
FAQ'S
@stop
@section('contents')
<style>
    dl.list1 {
        padding-left:0;
    }
    dl.list1 dd{
        margin-left:0;
        margin-bottom:10px;
        border:1px solid #ddd;
        background:#eee;
        padding:10px 15px;
        border-radius:5px;
        list-style:none;
    }
</style>
<div id="contact" class="page-content page-contact">
    <div id="message-box-conact"></div>
    <div class="row">
        <div class="col-sm-12">    

            <div class="contact-form-box">
                 <div class="panel-group" id="accordion">
                    <?php $i = 1; ?>
			  @if(isset($faqs) && !empty($faqs)) 	
              @foreach($faqs as $content)
              <div class="panel panel-default">
               <a data-toggle="collapse" data-parent="#accordion" href="#collapse{{$i}}"> 
                <div class="panel-heading">
                  <h4 class="panel-title">
                    {{$content->questions}}
                  </h4>
                </div></a>
                <div id="collapse{{$i}}" class="panel-collapse collapse">
                  <div class="panel-body">{!!$content->answers!!}</div>
                  
                </div>
              </div>
               <?php $i++; ?>
               @endforeach
               @endif
            </div>             
            </div>
        </div>
       
    </div>
</div>
@stop
