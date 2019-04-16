$(function () {

	var t = $('#personal_commission');
	t.dataTable({
		ordering:false,
		serverSide: true,
		processing: true,
		ajax: {
			/* url: $('#personal_customer_commission').attr('action'), */
			type: 'POST',
			data: function ( d ) {
			d.from_date = $('#from_date').val(); 
			d.to_date = $('#to_date').val(); 
			},
        }, 
	 columns: [		
		{
			data: 'confirm_date',
			name: 'confirm_date',
			'class':'details',
		},
		{
			data: 'total_cv',
			name: 'total_cv',
		},
		{
			data: 'slab',
			name: 'slab',
		},
		
		{
			data: 'earnings',
			name: 'earnings',
			class: 'text-right',
		},
		
		{
			data: 'commission',
			name: 'commission',
			class: 'text-right',
		},
		
		{
			data: 'tax',
			name: 'tax',
			class: 'text-right',
		},
		{
			data: 'ngo_wallet',
			name: 'ngo_wallet',
			class: 'text-right',
		},			
		{
			data: 'net_pay',
			name: 'net_pay',
			class: 'text-right',
		},	        
		{
			name: 'status',
			data:function(row,type,set){
			  return '<label class="label label-'+row.status_dispclass+'">'+row.status+'</label>';
			}				
		},
	],
	"rowCallback": function( row, rdata ) {		
		  $('td:eq(0)', row).attr('data-period',rdata.confirm_date);
	  }
 });

	
	$('#searchbtn').click(function (e) {

		t.dataTable().fnDraw();
	});
	
	$('#resetbtn').click(function (e) {
		$('input,select',$(this).closest('form')).val('');
        t.dataTable().fnDraw();
    });
	
	t.on('click','.details',function(e){
		e.preventDefault();
		var date = $(this).attr('data-period');
		$.ajax({
			url: 'affiliate/customer-commission/personal_bonus_details',
			type: 'POST',
			data: {'date':date},
			success:function(op){
				if(op.status=='ok'){
					$('#report').hide();
					$('#details').html(op.content);
					$('#details').show();
				}
			}
        }); 
	})
	$('#details').on('click','.back',function(e){
		e.preventDefault();
		$('#report').show();
		$('#details').hide();
	});
	
	
});
