$(function () {

    var t = $('#car_bonus_commission');
    t.dataTable({
        ordering: false,
        serverSide: true,
        processing: true,
       /*  responsive: {
            details: {
                display: $.fn.dataTable.Responsive.display.modal({
                    header: function (row) {
                        var data = row.data();
                        return data.uname;
                    }
                }),
                renderer: $.fn.dataTable.Responsive.renderer.tableAll({
                    tableClass: 'table'
                })
            }
        }, */
        ajax: {
            url: $('#car_bonus_details').attr('action'),
            type: 'POST',
            data: function (d) {
                d.from_date = $('#from_date').val();
                d.to_date = $('#to_date').val();
            },
        },
        columns: [
            {
                data: 'bonus_date',
                name: 'bonus_date',
                class: 'text-left',
            },
			{
			    data: 'full_name',
                name: 'full_name',
                class: 'text-left',
				render: function (data, type, row, meta) {
                    return '<a href="#" data-account="'+ row.account_id +'" class="rank">' + row.full_name + '</a>';
                }
            },
			{
			    data: 'user_code',
                name: 'user_code',
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
			{
                class: 'text-center',
                orderable: false,
                render: function (data, type, row, meta) {
                    return addDropDownMenu(row.actions, true);                 
                }
            }
        ],
		"rowCallback": function( row, rdata ) {		
		  $('td:eq(0)', row).attr('data-period',rdata.bid);
		}
    });

    $('#searchbtn').click(function (e) {
        t.dataTable().fnDraw();
    });

    $('#resetbtn').click(function (e) {
        $('input,select', $(this).closest('form')).val('');
        t.dataTable().fnDraw();
    });
	
	t.on('click','.rank',function(e){
		e.preventDefault();		
		var account_id = $(this).attr('data-account');
		$.ajax({
			url: t.attr('data-rank-url'),
			type: 'POST',
			data: {'account_id':account_id},
			success:function(op){
				if(op.log !=''){
					var content = '';
					$.each(op.log,function(index,val){
						content = content+'<tr><td>'+val.created_on+'</td>'+'<td>'+val.rank+'</td></tr>';
					})
				}
				$('#log_table tbody').html(content);
				$('#myModal').modal('show');
			}
        }); 
	});	
	
	t.on('click', '.change_status', function (event) {
        event.preventDefault();
		CURELE = $(this);
        if (confirm(CURELE.attr('data-confirm'))) {			
            $.ajax({
                data: {id: CURELE.data('id'), status: CURELE.data('status')},
                url: CURELE.attr('href'),
                success: function (data) {
                    t.dataTable().fnDraw();
                }
            });
        }
    });
	
	/*t.on('click','.details',function(e){
		e.preventDefault();
		var id = $(this).attr('data-period');
		$.ajax({
			url: 'affiliate/affiliate-commission/car-bonus-details',
			type: 'POST',
			data: {'id':id},
			success:function(op){
				if(op.status == 'ok'){
					$.each(op.details,function(fld,val){
						$('#bonus_details .'+fld).text(val);
					});
					$('#myModal').modal();
				}
			}
        }); 
	})*/
});
