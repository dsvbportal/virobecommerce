$(document).ready(function () {
    var DT = $('#purchase_upgrade_histroy').dataTable({
		"ordering": false,
        ajax: {
            data: function (d) {
                return $.extend({}, d, $('.panel_controls input,select').serializeObject());
            }
        },
       columns: [
            {
                data: 'create_date',
                name: 'create_date',
            },
			{                
                name: 'purchase_code',
				render: function (data, type, row, meta) {
					return '<b>'+row.package_name+'</b> ('+row.purchase_code+')';
				}
            },
			{
                data: 'paid_amt',
                name: 'paid_amt',
                class: 'text-right no-wrap',
            },
            {
                name: 'package_qv',
                class: 'text-right',
                data: 'package_qv',
            },
			{
                name: 'affiliate',
                class: 'text-left',
                render: function (data, type, row, meta) {
					return '<b>'+row.fullname+'</b> ('+row.user_code+')';
				}
            },
			{
                name: 'paymode',
                class: 'text-left',
                data: 'payment_type',
            },
			{
                name: 'status',
                class: 'text-left no-wrap',
                data: 'status',
            },
            {
                class: 'text-center',
                orderable: false,
                render: function (data, type, row, meta) {
                    return addDropDownMenu(row.actions, true);
                }
            }
			 
        ],
    });
	
	 $('#search_btn').click(function (e) {
        DT.fnDraw();
     });
	
      $('#reset_btn').click(function (e) {
        $('input,select', $(this).closest('form')).val('');
        DT.fnDraw();
    });
	
	 DT.on('click', '.pkactivate_btn', function (e) {
		e.preventDefault();
       CURFORM = $("#upgrade_history");
        var CurEle = $(this);
        $.ajax({
            type: 'POST',
            url: $(this).attr('href'),
            data: {id: CurEle.data('id')},
            success: function (op) {
                       CURFORM.data('errmsg-fld','#purchase_upgrade_histroy');
	                   CURFORM.data('errmsg-placement','before');
                       DT.fnDraw();
                },
            error: function (jqXHR, textStatus, errorThrown) {	
                                             						
              }

        });
    });
	
});
	