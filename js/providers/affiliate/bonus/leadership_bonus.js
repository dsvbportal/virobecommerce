$(function () {
    var t = $('#leadership_bonus_commission');
    t.DataTable({
        ordering: false,
        serverSide: true,
        processing: true,
        pagingType: 'input_page',
        sDom: "t" + "<'col-sm-6 bottom info align'li>r<'col-sm-6 info bottom text-right'p>",
        oLanguage: {
            "sLengthMenu": "_MENU_",
        },
        responsive: {
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
        },
        ajax: {
            url: $('#leadership_bonus_details').attr('action'),
            type: 'POST',
            data: function (d) {
                d.from_date = $('#from_date').val();
                d.to_date = $('#to_date').val();
            },
        },
        "fnRowCallback" : function(nRow, aData, iDisplayIndex){      
		   var oSettings = this.fnSettings();
		   $("td:first", nRow).html(oSettings._iDisplayStart+iDisplayIndex +1);
		   return nRow;
        },
		columns: [
			{
                name: 'sino',
                class: 'text-center no-wrap',
				render:function(data, type, row, meta) {
					return '';
				}
            },	
			 {
                name: 'date_for',
                class: 'text-center no-wrap details',				
				data: function (row, type, set) {
                    return '<b class="text-info">' + row.date_for + '</b>';
                }
		    },		
           
			/*
			{
                data: 'leftbinpnt',
                name: 'leftbinpnt',
                class: 'text-right no-wrap'
            },
			{
                data: 'rightbinpnt',
                name: 'rightbinpnt',
                class: 'text-right no-wrap'
            },
			{
                data: 'leftclubpoint',
                name: 'leftclubpoint',
                class: 'text-right no-wrap'
            },
			{
                data: 'rightclubpoint',
                name: 'rightclubpoint',
                class: 'text-right no-wrap'
            },
			
			{
                data: 'totleftbinpnt',
                name: 'totleftbinpnt',
                class: 'text-right no-wrap'
            },
			{
                data: 'totrightbinpnt',
                name: 'totrightbinpnt',
                class: 'text-right no-wrap'
            },
			*/
            {
                data: 'clubpoint',
                name: 'clubpoint',
                class: 'text-center no-wrap'
            },
            {
                data: 'earnings',
                name: 'earnings',
                class: 'text-center no-wrap'
            },
            {
                data: 'income',
                name: 'income',
                class: 'text-right no-wrap'
            },
           /* {
                data: 'tax',
                name: 'tax',
                class: 'text-right no-wrap'
            },*/
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
           /* {
                name: 'Status',
                class: 'text-center',
                data: function (row, type, set) {
                    return '<label class="label label-' + row.status_dispclass + '">' + row.status + '</label>';
                }
            }*/
        ],
		createdRow: function( row, rdata, dataIndex ) {
			$( row ).find('td:eq(1)').attr('data-bid', rdata.bid);
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
		var id = $(this).attr('data-bid');
		$.ajax({
			url: 'affiliate/affiliate-commission/leadership-bonus-details',
			type: 'POST',
			data: {'id':id},
			success:function(op){
				if(op.status == 'ok'){
					$.each(op.details,function(fld,val){
						$('#bonus_details .'+fld).text(val);
					});
					$('#myModal .modal-header h4 span').text(op.details.date_for);
					$('#myModal').modal();
				}
			}
        }); 
	})


});
