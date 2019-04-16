$(function () {
    var t = $('#transactionlist');
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
            url: $('#transaction_log').attr('action'),
            type: 'POST',
            data: function (d) {
                d.search_term = $('#search_term').val();
                d.from = $('#from').val();
                d.to = $('#to').val();
                d.wallet_id = $('#wallet_id').val();
            },
        },
        columns: [
            {
                name: 'logo',
                class: 'col-sm-2 text-center',
                data: function (row, type, set) {
                    return '<img class="img img-responsive img-thumbnail" width="50%" src="' + row.logo + '" alt="' + row.store_name + '"/> ';
                }
            },
			{
                data: 'order_date',
                name: 'order_date',
                class: 'no-wrap',
            },
            {
                data: 'store_name',
                name: 'store_name',
            },
            {
                name: 'bill_amount',
                class: 'text-right no-wrap',
                data: 'bill_amount',
            },
			{
                name: 'order_type',
                class: 'text-right no-wrap',
                data: 'order_type',
            },
			{
                data: 'status',
                name: 'status',
                class: 'text-center',
                render: function (data, type, row, meta) {
			         active = '<span class="label label-'+row.status_class+'">'+row.status+'</span>';
					 return active;
                }
            },
        ],
        responsive: {
            details: {
                display: $.fn.dataTable.Responsive.display.modal({
                    header: function (row) {
                        var data = row.data();
                        return data[0].span;
                    }
                }),
                renderer: $.fn.dataTable.Responsive.renderer.tableAll({
                    tableClass: 'table'
                })
            }
        },
        order: [[0, 'DESC']]
    });

    $('#search_btn').click(function (e) {
        t.dataTable().fnDraw();
    });

    $('#reset_btn').click(function (e) {
        $('input,select', $(this).closest('form')).val('');
        t.dataTable().fnDraw();
    });

});


