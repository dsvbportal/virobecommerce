$(function () {
	var t = $('#ranks_table');
	t.dataTable({
		ordering:false,
		serverSide: true,
		processing: true,
		ajax: {
			type: 'POST',
			data: function ( d ) {
				d.from_date = $('#from_date').val(); 
				d.to_date 	= $('#to_date').val(); 
				d.terms 	= $('#terms').val(); 
			},
        }, 
	columns: [	
		{
			data: 'created_on',
			name: 'created_on',
			class:'details'
		},
		{
			data: 'fullname',
			name: 'fullname',
			class:'details'
		},	
		{
			data: 'uname',
			name: 'uname',
			
		},
		{
			data: 'user_code',
			name: 'user_code',
		},
		{
			data: 'rank',
			name: 'rank',
		},
		{
			data: 'country',
			name: 'country',
		},
		{
			data: 'gen_1',
			name: 'gen_1',
			'class':'text-right'
		},
		{
			data: 'gen_2',
			name: 'gen_2',
			'class':'text-right'
		},
		{
			data: 'gen_3',
			name: 'gen_3',
			'class':'text-right'
		},	
	
	],
	"rowCallback": function( row, rdata ) {		
		  $('td:eq(0),td:eq(1)', row).attr('data-account',rdata.account_id);
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
		var account_id = $(this).attr('data-account');
		$.ajax({
			url: 'admin/commission/rank_log',
			type: 'POST',
			data: {'account_id':account_id},
			success:function(op){
				if(op.log !=''){
					var content = '';
					$.each(op.log,function(index,val){
						content = content+'<tr><td>'+val.created_on+'</td>'+'<td>'+val.rank+'</td></tr>';
					})
				}
				$('#log_table tbody').html(content);
				$('#myModal').modal('show');
			}
        }); 
	})
	

	$('#details').on('click','.back',function(e){
		e.preventDefault();
		$('#report').show();
		$('#details').hide();
	});
	
	
});
