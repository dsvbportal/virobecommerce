$(document).ready(function () {
    var DT = $('#profit_sharing_tbl').dataTable({
		 "ordering": false,
		 "bStateSave": true,
        ajax: {
            data: function (d) {
                return $.extend({}, d, $('.panel_controls input,select').serializeObject());
            }
        },
           columns: [
            {
                data: 'from_date',
                name: 'from_date',
            },
        	{                
                name: 'company_name',
				class: 'text-left no-wrap',
				data:function(row,type,set){
				 return '<span class=""><b>' + row.company_name +'</b>'+' '+'(' + row.user_code + ')';
				}
            },
			{
                data: 'franchisee_type',
                name: 'franchisee_type',
            },
			{
                data: 'commission_amount',
                name: 'commission_amount',
                class: 'text-right',
            }, 
			{
                data: 'tax',
                name: 'amount',
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
                    action_buttons += '<li><a href="' + document.location.BASE + 'admin/franchisee/profit_sharing_details/'+row.account_id+'/'+row.from_date+'" target="_blank class="edit_btn" data-category_id">Details</a></li>';
					action_buttons += '</ul></div>';
                    return action_buttons;
					
                }
            }, 
        ],
    });
    $('#search').click(function () {
        DT.fnDraw();
    });
	$('#reset').click(function (e) {
		  $("#search_text").val('');
		  $("#from").val('');
		  $("#to").val('');
		  DT.dataTable().fnDraw();
	});
});
	
	
	 
	   
	
	
  
	
 
  