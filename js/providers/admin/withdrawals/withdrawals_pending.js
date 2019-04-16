$(document).ready(function () {
    var DT = $('#withdrawal_pending_list').dataTable({
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
                    return '<span class="label label-info">' + row.payment_status + '</span> ';
                }
            },
			
			
        ]
    });
       $('#search').click(function () {
           DT.fnDraw();
       });
	   $('#resetbtn').click(function (e) {
		$('input,select', $(this).closest('form')).val('');
		DT.fnDraw();
	});
});

	
	
	
	
	
	 
	   
	 
	
  
	


 
