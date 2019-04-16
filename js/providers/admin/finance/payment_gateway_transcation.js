$(function () {	
    var t = $('#payment_gateway_list');
    var DT = t.dataTable({
        ajax: {
            url: $('#pg_transcation_form').attr('action'),
			type: 'POST',
            data: function (d) {
                return $.extend({}, d, $('input,select', '#pg_transcation_form').serializeObject());
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
                data: 'remark',
                name: 'remark',
                class: 'text-left',
                render: function (data, type, row, meta) {
                    return '<span class="">' + row.remark + '</span><br><span class=""><b>Trans ID</b>: #' + row.transaction_id + '</span><br>';
                }

            },
			{
			    data: 'payment_type',
                name: 'payment_type',
                class: 'text-left',
              
            },
			{
			    data: 'CR_Fpaidamt',
                name: 'cr_fpaidamt',
                class: 'text-right',
              
            },
			/* {
			    data: 'Fcurrent_balance',
                name: 'current_balance',
                class: 'text-right',
               
            }, */
        ],
      
    });

	$('#searchbtn').click(function () {
        DT.fnDraw();
    });
     $('#resetbtn').click(function (e) {
		$('#type,#currency_id,#wallet_id,#search_text,#from_date,#to_date').val('');
	     DT.fnDraw();
	});
	

});
