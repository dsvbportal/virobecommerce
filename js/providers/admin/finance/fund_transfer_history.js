$(document).ready(function () {
	
    var DT = $('#fund_transfer_history_list').dataTable({
		"ordering": false,
        ajax: {
            data: function (d) {
                return $.extend({}, d, $('.panel_controls input,select').serializeObject());
            }
        },
       columns: [
           {
                "data": "created_on",
                "name": "created_on",
                "class": "text-left",
            },
			{
                "data": "transaction_id",
                "name": "transaction_id",
                "class": "text-left"

            },
			{
                "data": "trans_from",
                "name": "trans_from",
                "class": "text-left",
				"render":function (data, type, row, meta){
					return new String(row.trans_from+'('+row.fuser_code+')'+'<br><span class="small text-muted">'+row.from_acc_roll+'</span>');
				}
            },
            {
                "data": "trans_to",
                "name": "trans_to",
                "class": "text-left",
				"render":function (data, type, row, meta){
					return new String(row.trans_to+'('+row.tuser_code+')'+'<br><span class="small text-muted">'+row.to_acc_roll+'</span>');
				}
            },
			{
                "data": "code",
                "name": "code",
                "class": "text-left",
            },
			{
                "data": "wallet_name",
                "name": "wallet_name",
                "class": "text-left",
            },
			{
                "data": "amount",
                "name": "amount",
                "class": "text-right",
            },
			{
                "data": "handleamt",
                "name": "handleamt",
                "class": "text-right",
            },
            {
                "data": "paidamt",
                "name": "paidamt",
                "class": "text-right",
            },
			{
                "data": "status_id",
                "name": "status_id",
                "class": "text-center",
                "render": function (data, type, row, meta) {
                    status = '<span class="label label-' + row.statusCls + '">' + row.status + '</span>';
                    return new String(status);
                }
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
	
	 $('#review_table').on('click', '.change_status', function (e) {
        e.preventDefault();
        curLine = $(this);
        $.ajax({
            url: curLine.attr('href'),
            data: {id: curLine.attr('rel'), status: curLine.attr('data-status')},
            type: "POST",
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
	