$(function () {
    var t = $('#earned_commission_store_details');
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
            url: $('#earned_store_details').attr('action'),
            type: 'POST',
			data: function (d) {
                return $.extend({}, d, $('#earned_store_details input,select').serializeObject());
            }         
        },
        columns: [
            {
                data: 'created_on',
                name: 'created_on',
            },
			{                
                name: 'store_name',
				class: 'text-left no-wrap',
				data:function(row,type,set){
				 return '<span class="">Merchant Store: <b>' + row.store_name +'</b>(' + row.store_code + ')<br><span class=""><i class="fa fa-map-marker"></i>'+ row.address+ '</span>';
				}
            }, 
			{
                data: 'state',
                name: 'state',
            },
			{
                data: 'district',
                name: 'district',
            },
			{
                data: 'commission_amount',
                name: 'commission_amount',
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


