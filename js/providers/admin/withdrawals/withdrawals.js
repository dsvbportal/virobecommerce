$(document).ready(function () {
    var DT = $('#withdrawal_list').dataTable({
        ajax: {
            data: function (d) {
                return $.extend({}, d, $('.panel_controls input,select').serializeObject());
            }
        },
		ordering: false,
        columns: [
            {
                data: 'created_on',
                name: 'created_on',
                class: 'text-left',
                
            },
		    {
				data: 'uname',
				name: 'uname',
				class: 'text-left',
				render: function (data, type, row, meta) {
				var txt = '';
				var txt='<span class=""><b>'+row.fullname+'</b>('+ row.uname +')<br></span>';
				return txt;
				 }
			},
			{
                data: 'country',
                name: 'country',
                class: 'text-left',
                
            },
			{
                data: 'payment_type',
                name: 'payment_type',
                class: 'text-left',
                
            },
			{
                data: 'code',
                name: 'code',
                class: 'text-left',
                
            },
			{
                data: 'amount',
                name: 'amount',
                class: 'text-left',
                
            },
			{
                data: 'handleamt',
                name: 'handleamt',
                class: 'text-right',
                
            },
			{
                data: 'paidamt',
                name: 'paidamt',
                class: 'text-right',
                
            },
			{
                data: 'expected_on',
                name: 'expected_on',
               
            },
			{
                data: 'updated_on',
                name: 'updated_on',
                
            },
			{
                name: 'payment_status',
                class: 'text-left',
                data: function (row, type, set) {
                    return '<span class="label label-'+row.status_class+'">' + row.payment_status + '</span> ';
                }
            },
			{
                orderable: false,
                class: 'text-center',
                render: function (data, type, row, meta) {
                    return addDropDownMenu(row.actions, true);                 
                }
            }
			
        ]
    });
       $('#search').click(function () {
           DT.fnDraw();
       });
	   $('#resetbtn').click(function (e) {
		 $('input,select', $(this).closest('form')).val('');
		 DT.fnDraw();
	     });
		 
	$('#withdrawal_list').on('click', '.change_status', function (e) {
		e.preventDefault();
		var CurEle = $(this);
		$("#change_status_model .modal-title").html('Withdrawals Confirm');
		$('#change_status_form #update_status').val(CurEle.data('status'));
		$('#change_status_form #withdrawal_id').val(CurEle.data('withdrawal_id'));
		$('#change_status_model form input[type="submit"]').removeAttr('disabled');
		$('#change_status_model form input[type="submit"]').val($(this).text());
		$("#change_status_model").modal();
	    });
		
		
		
		$('#details').on('click','#cancel-req',function(e){
		e.preventDefault();
		CURFORM = $('#withdrawal_pending')
			var trans_id = $(this).attr('rel');
			if(confirm('Are you sure cancel this withdrawal')){
				$.ajax({
				  url:'admin/withdrawals/cancel',
				  data:{'trans_id':trans_id},
				  dataType:'json',
				  type:'post',
				  success:function(op){
					 if(op.status == 200){
						$('#report').css('display','block');
						$('#details').css('display','none');
						 DT.fnDraw();
					 }else{
						 $('#msg').html('<div class="alert alert-danger"><a class="close" data-dismiss="alert" area-label="close">&times;</a>'+op.msg+'</div>');
					 }
				  },

				})
			}
		});
		
		$('#details').on('click','.back',function(e){
			$('#report').css('display','block');
			$('#details').css('display','none');
		});
	
		$('#withdrawal_list').on('click','.details',function(e){
			e.preventDefault();
			var  url = $(this).attr('href');
			$.ajax({
			  url:url,
			 // data:{'trans_id':trans_id},
			  dataType:'json',
			  type:'post',
			  success:function(op){
				 if(op.status == 'ok'){
					$('#details').html(op.content);
					$('#details').css('display','block');
					$('#report').css('display','none');
				 }else{
					 $('#msg').html('<div class="alert alert-danger"><a class="close" data-dismiss="alert" area-label="close">&times;</a>'+op.msg+'</div>');
				 }
			  },

			})
		})
   });

	$("#change_status_form").on('submit', function (e) {
            event.preventDefault();
	     	CURFORM = $("#change_status_form");
                $.ajax({
                    url: $("#change_status_form").attr('action'),
					data:{
					"withdrawal_id": $('#withdrawal_id').val(),
					"status":$('#update_status').val(),
					"msg": $('textarea#msg').val(),
					},
                    dataType: 'JSON',
                    type: 'POST',
                    beforeSend: function () {
                        $('.alert,div.help-block').remove();
                     /*   $('#confirm_withdraw_details').attr('disabled', true); */
                    },
                    success: function (res) {
					    $("#msg").val('');
						 $('#change_status_model').modal('hide');
						 $("#users-list-panel").show();
                         CURFORM.data('errmsg-fld','#list');
	                   	 CURFORM.data('errmsg-placement','before');
					    $("#withdrawal_list").dataTable().fnDraw();
                      },
                      error: function (jqXHR, textStatus, errorThrown) {	
                              						
                    }
                }); 
         });
	
	  $('#withdrawal_list').on('click', '.withdraw_process', function (e) {
         e.preventDefault();
        var CurEle = $(this);
        $.ajax({
            data: {status: CurEle.data('status'), withdraw_id: CurEle.data('withdrawal_id')},
            url: CurEle.attr('href'),
            dataType: 'JSON',
            type: 'POST',
            beforeSend: function () {
                $('body').toggleClass('loaded');
                $('.alert,div.help-block').remove();
            },
            success: function (res) {
                  if (res.status == 200) {
				    $("#withdrawal_list").dataTable().fnDraw();
                   $('#list').before('<div class="col-sm-12 alert alert-success"><a href="#" class="close" area-label="close" data-dismiss="alert">&times;</a>' + res.msg + '</div>');
                    $('.alert').fadeOut(7000); 
                }  
            },
             error: function (jqXHR, textStatus, errorThrown) {	
                              						
            }
        });
    }); 
	
	
	
	
	
	 
	   
	 
	
  
	


 
