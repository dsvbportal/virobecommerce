$(function () {
    var t = $('#tds_deducted_tbl');
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
            url: $('#tds_deductedfrm').attr('action'),
            type: 'POST',
			data: function (d) {
                return $.extend({}, d, $('#tds_deductedfrm input,select').serializeObject());
            }         
        },
        columns: [
            {
                data: 'created_on',
                name: 'created_on',
            },
            {
                data: 'remark',
                name: 'remark',
            },
            {
                data: 'wallet',
                name: 'wallet',
                class: 'no-wrap',
            },          
			{
                name: 'amount',
                class: 'text-right no-wrap',
                data: 'amount',
            },
			{
                name: 'tax',
                class: 'text-right no-wrap',
                data: 'tax',
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
        $('input,select', $(this).closest('form')).val('');
        t.dataTable().fnDraw();
    });

    /*t.on('processing.dt',function( e, settings, processing ){
     if (processing){
     $('body').toggleClass('loaded');
     console.log('sdfg');
     }else {
     $('body').toggleClass('loaded');
     console.log('3453');
     }
     });*/



});


