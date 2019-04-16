$(function () {
    var t = $('#earned_commission_tbl');
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
            url: $('#earned_commissionfrm').attr('action'),
            type: 'POST',
			data: function (d) {
                return $.extend({}, d, $('#earned_commissionfrm input,select').serializeObject());
            }         
        },
        columns: [
            {
                data: 'from_date',
                name: 'from_date',
            },
        	{
                data: 'commission_type',
                name: 'commission_type',
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

			{
                class: 'text-center',
                orderable: false,
                render: function (data, type, row, meta) {
			
                    var json = $.parseJSON(meta.settings.jqXHR.responseText);
                    var action_buttons = '';
                    var action_buttons = '<div class="btn-group">';
                    action_buttons += '<button type="button" class="btn btn-xs btn-primary dropdown-toggle" data-toggle="dropdown"><i class="fa fa-gear" aria-hidden="true"></i> <span class="caret"></span></button>';
                    action_buttons += '<ul class="dropdown-menu pull-right" role="menu">';
                    action_buttons += '<li><a href="' + document.location.BASE + 'channel-partner/reports/commission/'+row.fct_code+'/'+row.from_date+'" target="_blank class="edit_btn" data-category_id">Details</a></li>';
					action_buttons += '</ul></div>';
                    return action_buttons;
					
                }
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
	

	
/*	$('#earned_commission_tbl').on('click','.details',function(e){
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
	}) */
});


