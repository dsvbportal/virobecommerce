$(function () {
    var t = $('#hist_table');
	    var DT = t.dataTable({
        bPagenation: true,
        bProcessing: true,
        bFilter: false,
        bAutoWidth: false,
        oLanguage: {
            sSearch: "<span>Search:</span> ",
            sInfo: "Showing <span>_START_</span> to <span>_END_</span> of <span>_TOTAL_</span> entries",
            sLengthMenu: "_MENU_ <span>entries per page</span>"
        },
        bDestroy: true,
        bSort: true,
        processing: true,
        serverSide: true,
        sDom: "t" + "<'col-sm-6 bottom info 'li>r<'col-sm-6 info bottom text-right'p>",
        ajax: {
            url: $('#form').attr('action'),
            type: 'POST',
            data: function (d) {
                return $.extend({}, d, $('input,select', '#form').serializeObject());
            },
        },
        columns: [
            {
                data: "created_on",
                name: "created_on",
                class: "text-center",
                render: function (data, type, row, meta) {
                    return new String(row.created_on).dateFormat("dd-mmm-yyyy H:M:s");
                }
            },
            {
                data: "transaction_id",
                name: "transaction_id",
                class: "text-right"

            },
            {
                data: "username",
                name: "username",
                class: "text-left"

            },
            {
                data: "wallet_name",
                name: "wallet_name",
                class: "text-left",
            },
            {
                data: "amount",
                name: "amount",
                class: "text-right",
            },
            {
                data: "handleamt",
                name: "handleamt",
                class: "text-right",
            },
            {
                data: "paidamt",
                name: "paidamt",
                class: "text-right",
            },
            {
                data: "added_by",
                name: "added_by",
                class: "text-right",
            },
            {
                data: "status_id",
                name: "status_id",
                class: "text-center",
                render: function (data, type, row, meta) {
                    status = '<span class="label label-' + row.statusCls + '">' + row.status + '</span>';
                    return new String(status);
                }
            },
        ],
    });
    $('#form').on('submit', function (e) {
        DT.fnDraw();
    });
    $('#searchbtn').click(function (e) {
        DT.fnDraw();
    });
    $('#resetbtn').click(function (e) {
        $('input,select', $(this).closest('form')).val('');
        DT.fnDraw();
    });
    $('#review_table').on('click', '.change_status', function (e) {
        e.preventDefault();
        curLine = $(this);
        $.ajax({
            url: curLine.attr('href'),
            data: {id: curLine.attr('rel'), status: curLine.attr('data-status')},
            type: 'POST',
            dataType: 'JSON',
            success: function (res) {
                if (res.status == 'ok')
                {
                    curLine.closest('tr').hide();
                    DT.fnDraw();
                    $('#status_msg').html('<div class="alert alert-success">' + res.contents + '</div>').fadeOut(9000);
                } else {
                    $('#status_msg').html('<div class="alert alert-success">' + res.contents + '</div>').fadeOut(9000);
                }
            },
            error: function () {
                alert('Something went wrong');
                return false;
            }
        });
    });
});
