$(function () {
    var t = $('#profit_sharing_details');
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
            url: $('#profit_sharing_form').attr('action'),
            type: 'POST',
			data: function (d) {
                return $.extend({}, d, $('#profit_sharing_form input,select').serializeObject());
            }         
        },
        columns: [
            {
                data: 'from_date',
                name: 'from_date',
            },
	        {
                data: 'amount',
                name: 'amount',
            },
			{
				data: 'commission_perc',
				name: 'commission_perc',
				class: 'text-center',
				render: function (data, type, row, meta) {
				var txt = '';
				var txt='<span class="text-muted"></span>'+row.commission_perc;
				return txt;
				 }
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


