$(function () {
    var t = $('#faststart_bouns_list');
    t.dataTable({
        ordering: false,
        serverSide: true,
        processing: true,
        ajax: {
            url: $('#form_faststart_bonus').attr('action'),
            type: 'POST',
            data: function (d) {
                d.from_date = $('#from_date').val();
                d.to_date = $('#to_date').val();
            },
        },
        columns: [
            {
                data: 'from_uname',
                name: 'from_uname',
            },
            {
                data: 'package_name',
                name: 'package_name',
            },
            {
                data: 'created_date',
                name: 'created_date',
                class: 'text-center'
            },
            {
                data: 'Famount',
                name: 'Amount',
                class: 'text-right no-wrap'
            },
            {
                data: 'qv',
                name: 'qv',
                class: 'text-right no-wrap'
            },
            {
                data: 'earnings_qv',
                name: 'earnings_qv',
                class: 'text-right no-wrap'
            },
            {
                data: 'commission',
                name: 'commission',
                class: 'text-right no-wrap'
            },
            {
                data: 'tax',
                name: 'tax',
                class: 'text-right no-wrap'
            },
            {
                data: 'ngo_wallet_amt',
                name: 'ngo_wallet_amt',
                class: 'text-right no-wrap'
            },
            {
                data: 'net_pay',
                name: 'net_pay',
                class: 'text-right no-wrap'
            },
        ],
    });
    $('#searchbtn').click(function (e) {
        t.dataTable().fnDraw();
    });
    $('#resetbtn').click(function (e) {
        $('input,select', $(this).closest('form')).val('');
        t.dataTable().fnDraw();
    });
});

