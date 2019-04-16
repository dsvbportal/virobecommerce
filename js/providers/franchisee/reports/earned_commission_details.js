$(function () {
    var t = $('#earned_commission_tbl_details');

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
            url: $('#earned_commissionfrm_details').attr('action'),
            type: 'POST',
			data: function (d) {
                return $.extend({}, d, $('#earned_commissionfrm_details input,select').serializeObject());
            }         
        },
	
        columns: [
		
            {
                data: 'created_date',
                name: 'created_date',
            },
			{    
                name: 'to_user_code',
                class: 'text-left',
				render: function (data, type, row, meta) {
					 return '<span class="">'+row.to_name+'</span>('+row.to_user_code+')'; 					 
				}      
            },	
			{
                data: 'transferred_amount',
                name: 'transferred_amount',
				class: 'text-right',
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


