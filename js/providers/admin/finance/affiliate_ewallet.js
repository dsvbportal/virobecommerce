$(document).ready(function () {
	
    var DT = $('#ewallet_list').dataTable({
        ajax: {
            data: function (d) {
                return $.extend({}, d, $('.panel_controls input,select').serializeObject());
            }
        },
		ordering: false,
        columns: [
            {
                data: 'full_name',
                name: 'full_name',
				class: 'text-left',
				render: function (data, type, row, meta) {
				var txt = '';
				var txt='<span class="text-muted">Uname: </span><b>'+row.username+'</b><br><span class="text-muted">Full Name: </span><b>'+row.full_name+'</b>';
				return txt;
				 }
            },
			 {
                data: 'currency_code',
                name: 'currency_code',
				 class: 'text-left',
            },
			{
                data: 'tot_credit',
                name: 'tot_credit',
				 class: 'text-right',
            },
			{
                data: 'tot_debit',
                name: 'tot_debit',
				 class: 'text-right',
            },
			{
                data: 'current_balance',
                name: 'current_balance',
				 class: 'text-right',
            },
			{
                data: 'wallet',
                name: 'wallet',
				 class: 'text-center',
            },
        ],
		"preDrawCallback": function(settings ) {			
			 $("#exportbtn,#printbtn").hide();
		   },
		drawCallback: function (e, settings) {
			if(e.json.recordsFiltered>0){
				$("#exportbtn,#printbtn").show();
			}
		}
    });
	
	$('#search').click(function () {
		DT.fnDraw();
	});  

	$('#resetbtn').click(function (e) {
		$('input,select', $(this).closest('form')).val('');
		DT.fnDraw();
	});
	
});
	
