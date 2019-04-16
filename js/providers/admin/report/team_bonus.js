$(function () {
    var t = $('#team_bonus');
    var t = $('#team_bonus').dataTable({
		  ordering: false,
        serverSide: true,
        processing: true,
        ajax: {
            url: $('#form_team_bonus').attr('action'),
            type: 'POST',
            data: function (d) {
                d.search_term = $('#search_term').val();
                d.from_date = $('#from_date').val();
                d.to_date = $('#to_date').val();
                var filterchk = [];
                $('#chkbox :checked').each(function () {
                    filterchk.push($(this).val());
                });
                d.filterchk = filterchk;
            },
        },
        columns: [
            {
                data: 'date_for',
                name: 'date_for',
                class: 'text-center no-wrap details',
		    },
			{
                data: 'uname',
                name: 'uname',
            },
			{
                data: 'user_code',
                name: 'user_code',
                class: 'text-right no-wrap'
            },
            {
                data: 'clubpoint',
                name: 'clubpoint',
                class: 'text-right no-wrap'
            },
            {
                data: 'earnings',
                name: 'earnings',
                class: 'text-right no-wrap'
            },
            {
                data: 'income',
                name: 'income',
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
                data: 'paidinc',
                name: 'paidinc',
                class: 'text-right no-wrap'
            },
            {
                name: 'Status',
                class: 'text-center',
                data: function (row, type, set) {
                    return '<label class="label label-' + row.status_dispclass + '">' + row.status + '</label>';
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
	
	t.on('click','.details',function(e){
		e.preventDefault();
		var id = $(this).attr('data-period');
		$.ajax({
			url: 'affiliate/affiliate-commission/team-bonus-details',
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
	})

});
