$(function () {
	
    var t = $('#star_bonus_commission');
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
            url: $('#star_bonus_details').attr('action'),
            type: 'POST',
			data: function (d) {
                return $.extend({}, d, $('#star_bonus_details input,select').serializeObject());
            }
			//data: $('#star_bonus_details').serialize(),          
        },
        columns: [
            {
                data: 'bonus_date',
                name: 'bonus_date',
                class: 'text-left',
            },
            {
                data: 'rank',
                name: 'rank',
                class: 'text-left',
            },
            {
                data: 'commission',
                name: 'commission',
                class: 'text-right',
            },
			{
                data: 'tax',
                name: 'tax',
                class: 'text-right',
            },
			{
                data: 'vi_help', 
                name: 'vi_help',
                class: 'text-right',
            },
			{
                data: 'net_pay', 
                name: 'net_pay',
                class: 'text-right',
            },
            {
                data: 'status',
                name: 'status',
                class: 'text-center',
                render: function (data, type, row, meta) {
                    return '<span class="label label-' + row.status_class + '">' + row.status + '</span>';
                }
            },
        ],
		responsive: {
            details: {
                display: $.fn.dataTable.Responsive.display.modal( {
                    header: function ( row ) {
                        var data = row.data();
                        return data.bonus_date;
                    }
                } ),
                renderer: $.fn.dataTable.Responsive.renderer.tableAll( {
                    tableClass: 'table'
                } )
            }
        }
    });

    $('#searchbtn').click(function (e) {
        t.dataTable().fnDraw();
    });

    $('#resetbtn').click(function (e) {
        $('input,select', $(this).closest('form')).val('');
        t.dataTable().fnDraw();
    });
});
