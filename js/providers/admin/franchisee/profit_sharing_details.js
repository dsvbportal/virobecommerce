$(document).ready(function () {
    var DT = $('#profit_sharing_details_tbl').dataTable({
		 "ordering": false,
		 "bStateSave": true,
        ajax: {
            data: function (d) {
                return $.extend({}, d, $('.panel_controls input,select').serializeObject());
            }
        },
           columns: [
             {
                data: 'from_date',
                name: 'from_date',
            },
			{                
                name: 'company_name',
				class: 'text-left no-wrap',
				data:function(row,type,set){
				 return '<span class=""><b>' + row.company_name +'</b>'+' '+'(' + row.user_code + ')';
				}
            },
			{
                data: 'franchisee_type',
                name: 'franchisee_type',
            },
	        {
                data: 'amount',
                name: 'amount',
            },
			{
				data: 'commission_perc',
				name: 'commission_perc',
				class: 'text-center',
			},
		
			{
                data: 'commission_amount',
                name: 'commission_amount',
                class: 'text-right',
            }, 
			{
                data: 'tax',
                name: 'tax',
                class: 'text-right',
            },
			{
                data: 'net_pay',
                name: 'net_pay',
                class: 'text-right',
            },
        ],
    });
    $('#search').click(function () {
        DT.fnDraw();
    });
	$('#reset').click(function (e) {
		  $("#search_text").val('');
		  $("#from").val('');
		  $("#to").val('');
		  DT.dataTable().fnDraw();
	});
});
	
	
	 
	   
	
	
  
	
 
  