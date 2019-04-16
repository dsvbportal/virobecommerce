   function addDropDownMenu(arr, text) {
    arr = arr || [];
    text = text || false;
	if(arr.length>0){
		var content = $('<div>', {class: 'btn-group'}).append($('<button>').attr({class: 'btn btn-xs btn-primary dropdown-toggle', 'data-toggle': 'dropdown'})
				.append([$('<i>', {class: 'fa fa-gear'}), $('<span>').attr({class: 'caret'})]),
				$('<ul>').attr({class: 'dropdown-menu pull-right', role: 'menu'}).append(function () {
			var options = [], data = {};
			$.each(arr, function (k, v) {
				data = {};
				if (! v.redirect) {
					v.class = v.class || (v.url ? 'actions' : 'show-modal');
				}
				else {
					data['target'] = v.target || '_blank';
				}
				v.url = v.url || '#';
				v.data = v.data || {};
				$.each(v.data, function (key, val) {
					data['data-' + key] = val;
				});
				options.push($('<li>').append($('<a>', {class: v.class}).attr($.extend({href: v.url}, data)).text(v.label)));
			});
			return options;
		}));
		return text ? content[0].outerHTML : content;
	} else {
		return '';
	}
}
$(function () {
    var t = $('#purchase_upgrade_histroy');
    DT = t.dataTable({
        ordering: false,
        serverSide: true,
        processing: true,
        pagingType: 'input_page',
        sDom: "t" + "<'col-sm-6 bottom info align'li>r<'col-sm-6 info bottom text-right'p>",
        oLanguage: {
            "sLengthMenu": "_MENU_",
        },
        ajax: {
            url: $('#upgrade_history').attr('action'),
            type: 'POST',
            data: function (d) {
                d.search_term = $('#search_term').val();
                d.from_date = $('#from_date').val();
                d.to_date = $('#to_date').val();
            },
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
                data: 'package_name',
                name: 'package_name',
                class: 'text-right no-wrap',
            },
			{
                data: 'transaction_id',
                name: 'transaction_id',
			     class: 'text-center no-wrap',
            },
			{
                data: 'paid_amt',
                name: 'paid_amt',
                class: 'text-right no-wrap',
            },
			{
                data: 'create_date', 
                name: 'create_date',
				class: 'text-left no-wrap',
            },        
			{
                data: 'updated_date',
                name: 'updated_date',
				class: 'text-left no-wrap',
            },       
			{
                name: 'paymode',
                class: 'text-left',
                data: 'payment_type',
            },
			{  
			    data: 'package_qv',
                name: 'package_qv',
                class: 'text-center',               
            },
		 	/*{
                name: 'status',
                class: 'text-left no-wrap',
                data: 'status',

            },*/
           /* {
                orderable: false,
                class: 'text-center',
                render: function (data, type, row, meta) {
                    return addDropDownMenu(row.actions, true);                 
                }
            },*/		

        ],
		"fnRowCallback" : function(nRow, aData, iDisplayIndex){      
		   var oSettings = this.fnSettings();
		   $("td:first", nRow).html(oSettings._iDisplayStart+iDisplayIndex +1);
		   return nRow;
        },
        responsive: {
            details: {
                display: $.fn.dataTable.Responsive.display.modal({
                    header: function (row) {
                        var data = row.data();						
                        return data.purchase_code;
                    }
                }),
                renderer: $.fn.dataTable.Responsive.renderer.tableAll({
                    tableClass: 'table'
                })
            }
        },
    });
    t.on('click', '.search_btn1', function () {
        var CurEle = $(this);
        $.ajax({
            type: 'POST',
            url: baseUrl + 'account/package/package_confirm',
            data: {id: CurEle.data('id')},
            success: function (op) {
                if (op.status == 200) {
                    t.before('<div class="alert alert alert-success">' + op.msg + '<a href="#" class="close" area-label="close" data-dismiss="alert">&times;</a></div>');
                    $('.alert').fadeOut(6000);
                    DT.fnDraw();
                }
                else
                {
                    t.before('<div class="alert alert alert-danger">' + op.msg + '<a href="#" class="close" area-label="close" data-dismiss="alert">&times;</a></div>');
                    $('.alert').fadeOut(6000);
                    DT.fnDraw();
                }
            }
        });
    });


    t.on('click', '.pkactivate_btn', function (e) {
		e.preventDefault();
         CURFORM = $("#upgrade_history");
		   var CurEle = $(this);
        $.ajax({
            type: 'POST',
            url: $(this).attr('href'),
            data: {id: CurEle.data('id')},
            success: function (op) {
                       CURFORM.data('errmsg-fld','#purchase_upgrade_histroy');
	                   CURFORM.data('errmsg-placement','before');
                       DT.fnDraw();
                },
			 error: function (jqXHR, textStatus, errorThrown) {	
              }
        });
    });
    t.on('click', '.pkrefund_btn', function () {
        var CurEle = $(this);
        $.ajax({
            type: 'POST',
            url: document.location.AFFILIATE + 'package/refund/' + CurEle.data('id'),
            dataType: 'json',
            success: function (op) {
                t.before('<div class="alert alert alert-success">' + op.msg + '<a href="#" class="close" area-label="close" data-dismiss="alert">&times;</a></div>');
                $('.alert').fadeOut(6000);
                DT.fnDraw();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                t.before('<div class="alert alert alert-danger">' + (jqXHR.responseJSON.msg || textStatus) + '<a href="#" class="close" area-label="close" data-dismiss="alert">&times;</a></div>');
                $('.alert').fadeOut(6000);
                DT.fnDraw();
            }
        });
    });
    $('#search_btn').click(function (e) {
        DT.fnDraw();
    });
    $('#reset_btn').click(function (e) {
        $('input,select', $(this).closest('form')).val('');
        DT.fnDraw();
    });	
});