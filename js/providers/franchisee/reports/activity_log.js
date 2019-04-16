$(function () {
    var t = $('#activity_log_tbl_details');

    t.DataTable({
        ordering: false,
        serverSide: true,
        processing: true,
        pagingType: 'input_page',
        sDom: "t" + "<'col-sm-6 bottom info align'li>r<'col-sm-6 info bottom text-right'p>",
        oLanguage: {
            "sLengthMenu": "_MENU_",
        },		 
        ajax: {
            url: $('#activity_log_history').attr('action'),
            type: 'POST',
			data: function (d) {
                return $.extend({}, d, $('#activity_log_history input,select').serializeObject());
            }         
        },
	
        columns: [
		
            {
                data: 'account_log_time',
                name: 'account_log_time',
            },
            {
                data: 'account_login_ip',
                name: 'account_login_ip',
            },
			{
                data: 'device_info',
                name: 'device_info',
            },
			{
                data: 'device_info',
                name: 'device_info',
            },
        ],
        responsive: {
            details: {
                display: $.fn.dataTable.Responsive.display.modal( {
                    header: function ( row ) {
                        var data = row.data();
                        return data.created_date;
                    }
                } ),
                renderer: $.fn.dataTable.Responsive.renderer.tableAll( {
                    tableClass: 'table'
                } )
            }
        }     
    });

    $('#search_btn').click(function (e) {
        t.dataTable().fnDraw();
    });

    $('#reset_btn').click(function (e) {
        $('input,select', $(this).closest('form')).val('');
        t.dataTable().fnDraw();
    });

});


