  $(function () {
	var t = $('#subscriptions');
	t.DataTable({ 
		ordering: false,
		serverSide: true,
		processing: true,
		pagingType: 'input_page',		
		sDom: "t"+"<'col-sm-6 bottom info align'li>r<'col-sm-6 info bottom text-right'p>", 
		oLanguage: {
			"sLengthMenu": "_MENU_",
		},
		ajax: {
			url: $('#my_packages').attr('action'),
			type: 'POST',
			data: function ( d ) {
				d.search_term = $('#search_term').val();
				d.wallet_id = $('#wallet_id').val();
				d.from_date = $('#from_date').val(); 
				d.to_date = $('#to_date').val(); 				
			},
        },
		columns: [
			/* {
				name:'packimg',
				width: '10%',
				data: function (row, type, set) {
                    return '<div class="order-item"><img class="img img-responsive" width="75" src="' + row.package_image_url + '" alt="'+row.package_name +'"/></div>';
                }
			}, */
			/* {
				name:'pack',
				render: function (data, type, row, meta) {
                    return '<h4>'+ row.package_name +' <span class="text-muted">(#'+row.purchase_code+')</span></h4><ul class="list-inline"><li><b>QV:</b> <span class="text-muted">'+row.package_qv+'</span></li><li><b>Total QV:</b> <span class="text-muted">'+row.weekly_capping_qv+'</span></li></ul></span>';
                }
			}, */	
             {
			 data: 'package_name',
			 name: 'package_name',
			 },	
             {
			 data: 'transaction_id',
			 name: 'transaction_id',
			 },				 
			 {
			 data: 'paid_amt',
			 name: 'amount',
			 },
			 {
			 data: 'package_qv',
			 name: 'package_qv',
			 },
			 {
				data: 'purchased_date',
				name: 'purchased_date',
				width: '15%',
			},
			{
				data: 'confirm_date',
				name: 'updated_date',
				width: '15%',
			},			
            {
				data: 'payment_type',
				name: 'payment_type',
			},			
			/* {
			  name: 'status',
			  render: function (data, type, row, meta) {
				return '<label class="label label-'+row.status_class+'">'+row.status_label+'</label>';
			  }
			 }, 
			{
                class: 'text-center',
                orderable: false,
                render: function (data, type, row, meta) {
                    return (row.actions!=undefined)? addDropDownMenu(row.actions, true):'';
                }
            }*/		
		],
		
	});
/* 	 $('#subscriptions').on('click','.search_btn1',function() {

	        var CurEle = $(this);
		    $.ajax({
            type: 'POST',
            url: baseUrl + 'account/package/package_confirm',
			data: {id: CurEle.data('id')},
            success: function (op) {
	     
                    if (op.status == 200) {
                        $('#subscriptions').before('<div class="alert alert alert-success">' + op.msg + '<a href="#" class="close" area-label="close" data-dismiss="alert">&times;</a></div>'); 
                       $('.alert').fadeOut(6000);
                        t.dataTable().fnDraw();
              }
            }
        });
	}); */
	
	$('#search_btn').click(function (e) {
	
		t.dataTable().fnDraw();
	});
	
	$('#reset_btn').click(function (e) {
		$('input,select',$(this).closest('form')).val('');
        t.dataTable().fnDraw();
    });
	
	/*t.on('processing.dt',function( e, settings, processing ){
		if (processing){
			 $('body').toggleClass('loaded');
			 console.log('sdfg');
		}else {
			$('body').toggleClass('loaded');
			console.log('3453');
		}
	});*/
 
});
