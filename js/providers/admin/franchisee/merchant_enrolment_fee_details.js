$(document).ready(function () {
    var DT = $('#merchant_enrolment_fee_details').dataTable({
		 "ordering": false,
		 "bStateSave": true,
        ajax: {
            data: function (d) {
                return $.extend({}, d, $('.panel_controls input,select').serializeObject());
            }
        },
           columns: [
		    {
                data: 'created_on',
                name: 'created_on',
            },
			{                
                name: 'store_name',
				class: 'text-left no-wrap',
				data:function(row,type,set){
				 return '<span class="">Merchant: <b>' + row.store_name +'</b>'+' '+'(' + row.store_code + ')<br><span class=""><i class="fa fa-map-marker"></i>'+' '+''+ row.address+ '</span>';
				}
            }, 
			{
                data: 'state',
                name: 'state',
            },
			{
                data: 'district',
                name: 'district',
            },
			{
                data: 'commission_amount',
                name: 'commission_amount',
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
	
	
	 
	   
	
	
  
	
 
  