$(function () {	
    var t = $('#log_table');
    var DT = t.dataTable({
        ajax: {
            url: $('#form').attr('action'),
			type: 'POST',
            data: function (d) {
                return $.extend({}, d, $('input,select', '#form').serializeObject());
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
			    data: 'wallet',
                name: 'wallet',
                class: 'text-left',
              
            },
			{
			    data: 'CR_Fpaidamt',
                name: 'cr_fpaidamt',
               class: 'text-right',
              
            },
			{ 
			    data: 'DR_Fpaidamt',
                name: 'dr_fpaidamt',
                class: 'text-right',
            },
			{
				data: 'Fcurrent_balance',
                name: 'current_balance',
                class: 'text-right',
            },
         
        ],
     
    });
    t.on('click', '.actions', function (e) {
        e.preventDefault();
        addDropDownMenuActions($(this), function (op) {
            if (op.details != undefined && op.details != null) {
                $('#transactions-details table').empty();
                $.each(op.details, function (k, e) {
                    $('#transactions-details table').append($('<tr>').append([$('<th>').append(e.label), $('<td>').append(e.value)]));
                });
                $('#transactions-list').hide();
                $('#transactions-details').show();
            }
        });
    });
    $('#transactions-details').on('click', '#back', function (e) {
        e.preventDefault();
        $('#transactions-details').hide();
        $('#transactions-list').show();
    });

	
	$('#searchbtn').click(function () {
        DT.fnDraw();
    });
     $('#resetbtn').click(function (e) {
			$('#type,#currency_id,#wallet_id,#search_text,#from_date,#to_date').val('');
			 DT.fnDraw();
		});
	
    /* $('#review_table').on('click', '.change_status', function (e) {
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
    }); */
});
