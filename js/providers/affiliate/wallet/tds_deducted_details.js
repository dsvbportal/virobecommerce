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
                name: 'amount',
                class: 'text-right no-wrap',
                data: 'amount',
              },
			  {
                name: 'tax',
                class: 'text-right no-wrap',
                data: 'tax',
            },
			
			 {
                name: 'statementline',
                class: 'text-right no-wrap',
                data: 'statementline',
            },
			{
                orderable: false,
                class: 'text-center',
                render: function (data, type, row, meta) {
                    return addDropDownMenu(row.actions, true);                 
                }
            },
          /*  {
                data: 'remark',
                name: 'remark',
            },
            {
                data: 'wallet',
                name: 'wallet',
                class: 'no-wrap',
            },          
			
			 */
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
$('#tds_deducted_tbl').on('click','.details',function(e){
	e.preventDefault();
		var  url = $(this).attr('href');
		$.ajax({
		  url:url,
		  dataType:'json',
		  type:'post',
		  success:function(op){
			 if(op.status == 'ok'){
				$('#details').html(op.content);
				$('#details').css('display','block');
				$('#report').css('display','none');
			 }else{
				 $('#msg').html('<div class="alert alert-danger"><a class="close" data-dismiss="alert" area-label="close">&times;</a>'+op.msg+'</div>');
			 } 
		  },

		})
	})

   $('#details').on('click','.back',function(e){
		$('#report').css('display','block');
		$('#details').css('display','none');
	});
	
});


