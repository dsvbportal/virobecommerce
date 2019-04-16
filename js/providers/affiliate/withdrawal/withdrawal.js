$(document).ready(function () {
    var DT = $('#withdrawal_list').dataTable({
	    ordering: false,
        serverSide: true,
        processing: true,
        pagingType: 'input_page',
        sDom: "t" + "<'col-sm-6 bottom info align'li>r<'col-sm-6 info bottom text-right'p>",
        oLanguage: {
            "sLengthMenu": "_MENU_",
        },
        ajax: {
			type: 'POST',
            data: function (d) {
                return $.extend({}, d, $('#withdrawal_log input,select').serializeObject());
            }
        },
	
        columns: [
           {
                data: 'created_on',
                name: 'created_on',
                class: 'text-left',                
           },
			{
                data: 'transaction_id',
                name: 'transaction_id',
                class: 'text-left',
                
            },	
		   {
                data: 'amount',
                name: 'amount',
                class: 'text-left',
                
            },
           	{
                data: 'payment_type',
                name: 'payment_type',
                class: 'text-left',
                
            },
			{
                name: 'status',
                class: 'text-left',
                data: function (row, type, set) {
                    return '<span class="label label-'+row.status_class+'">' + row.status + '</span> ';
                }
            },
			{
                orderable: false,
                class: 'text-center',
                render: function (data, type, row, meta) {
                    return addDropDownMenu(row.actions, true);                 
                }
            }
		/*	{
                data: 'code',
                name: 'code',
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
                name: 'status',
                class: 'text-left',
                data: function (row, type, set) {
                    return '<span class="label label-info">' + row.status + '</span> ';
                }
            },
			{
                name: 'details',
                data: function (row, type, set) {
                    return '<a href="'+row.details+'" class="details"><button class="btn btn-info btn-sm">Details</button></a>';
                }
            },
			 */
			
        ]
    });
       $('#search').click(function () {
           DT.fnDraw();
       });
	   $('#resetbtn').click(function (e) {
		$('input,select', $(this).closest('form')).val('');
		DT.fnDraw();
	});
	
	$('#details').on('click','#cancel-req',function(e){
		e.preventDefault();
		var trans_id = $(this).attr('rel');
		if(confirm('Are you sure cancel this withdrawal')){
			$.ajax({
			  url:'affiliate/withdrawal/cancel_request',
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

	
	
	
	
	
	 
	   
	 
	
  
	


 
