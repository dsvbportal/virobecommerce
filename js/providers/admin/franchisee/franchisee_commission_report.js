$(document).ready(function () {
    var DT = $('#franchisee_commission_list').dataTable({
		"ordering": false,
        ajax: {
            data: function (d) {
                return $.extend({}, d, $('.panel_controls input,select').serializeObject());
            }
        },
       columns: [
                {
                    data: 'created_date',
                    name: 'created_date',
                    class: 'text-center',
                 },
				 {
                    data: 'receiver',
                    name: 'receiver',
                    render: function (data, type, row, meta) {
                        return row.receiver_fullname+' - '+row.franchisee_location+' '+row.franchisee_type_name;
                    }
                 },
				 {
                    name: 'from_user_code',
                    data: function (row, type, set) {
                        var uname = row.from_full_name;
                        if (row.to_user_code !== null && row.to_user_code != '') {
                            uname += '<br />(' + row.to_user_code + ')';
                        }
                        if (row.district_name !== undefined && row.district_name !== null && row.district_name != '') {
                            uname += '<br />District : ' + row.district_name;
                        }
                        return new String(uname);
                    }
                 },
				 {
                    data: 'to_user_code',
                    name: 'to_user_code',
                    render: function (data, type, row, meta) {
                        return row.to_full_name + '<br /> (' + row.to_user_code + ')';
                    }
                 },
				 {
                    data: 'remark',
                    name: 'remark',
                    render: function (data, type, row, meta) {
                        var transaction_details = '';
                        if (row.remark !== undefined && row.remark != '') {
                            transaction_details = row.remark + '<br />';
                        }
                        transaction_details += 'Transaction Id: ' + row.transaction_id;
                        return new String(transaction_details);
                    }
                },
				{
                    data: 'amount',
                    name: 'amount',
                    class: 'text-right no-wrap',

                },
				{
                    data: 'commission_amount',
                    name: 'commission_amount',
                    class: 'text-right no-wrap',
                },
				{                    
                    name: 'status_name',
                    class: 'text-center',
					data: 'status_name',
					render : function(data, type, row, meta){
						return '<div class = "label '+row.status_label+'">'+row.status_name+'</div>';
					}
                },
				{
                    data: 'confirmed_date',
                    name: 'confirmed_date',
                    class: 'text-center',
                },
        ],
    });
	
	 $('#searchbtn').click(function (e) {
        DT.fnDraw();
     });
	
      $('#resetbtn').click(function (e) {
        $('input,select', $(this).closest('form')).val('');
        DT.fnDraw();
    });
		
});
	