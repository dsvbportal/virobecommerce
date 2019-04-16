$(function () {
	$("#wallet_id").trigger("change");
    var t = $('#transactionlist');
    t.DataTable({
        sort: false,
        serverSide: true,
        processing: true,
        pagingType: 'input_page',
        sDom: "t" + "<'col-sm-6 bottom info align'li>r<'col-sm-6 info bottom text-right'p>",
        oLanguage: {
            "sLengthMenu": "_MENU_",
        },
        ajax: {
            url: document.location.AFFILIATE + 'wallet/transactions',
            type: 'POST',
            data: function (d) {
                d.search_term = $('#search_term').val();
                d.from = $('#from').val();
                d.to = $('#to').val();
                d.wallet_id = $('#wallet_id').val();
                //d.currency_id= $('#currency_id').val();
            },
        },
        columns: [
            {
                data: 'created_on',
                name: 'created_on',
            },
			{
                data: 'wallet',
                name: 'wallet',
                class: 'no-wrap',
            },
            {
                data: 'remark',
                name: 'remark',
            },
            {
                name: 'cr_fpaidamt',
                class: 'text-right no-wrap',
                data: 'CR_Fpaidamt',
            },
			{
                name: 'dr_fpaidamt',
                class: 'text-right no-wrap',
                data: 'DR_Fpaidamt',
            },
			
        ],
       responsive: {
            details: {
                display: $.fn.dataTable.Responsive.display.modal( {
                    header: function ( row ) {
                        var data = row.data();
                        return data.created_on;
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
        $('input', $(this).closest('form')).val('');
        t.dataTable().fnDraw();
    });
});
 

 $(document).on('change','#wallet_id', function (e) {
		e.preventDefault();
		CURFORM = $("#transaction_log");
		var wallet_id=$("#wallet_id").val();
             $.ajax({
                 url: $(this).attr('data-url'),
                 type: "POST",
                 data: {'wallet': wallet_id},
                  dataType: "JSON",
                  beforeSend: function () {
                    },
               success: function (data) {							
					   console.log(data);
					   if(data.balance!=''){
					     $("#current_balance").html(data.balance.current_balance);
					   }
					   else{
						     $("#current_balance").html('');
					   }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
						data = $.parseJSON(jqXHR.responseText);					
					},
                });
    });
              
