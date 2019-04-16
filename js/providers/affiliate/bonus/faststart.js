$(function () {
    var t = $('#faststart_bouns_list');
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
            url: $('#form_faststart_bonus').attr('action'),
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
                data: 'created_date',
                name: 'created_date',
                class: 'text-center'
            },
            {                
                name: 'from_uname',
				data:function(row,type,set){
					return '<b>'+ row.full_name + '</b> ('+row.from_user_code+')';
				}
            },
            {                
                name: 'package_name',
				class: 'text-left no-wrap',
				data:function(row,type,set){
				 return '<b>'+ row.package_name + '</b><br><span class="text-muted">Price:<b>' + row.Famount + '</b></span><span class="text-muted">QV:<b>' + row.qv + '</b></span>';
				}
            },
            {
                data: 'earnings',
                name: 'earnings',
                class: 'text-right no-wrap'
            },
            {
                data: 'commission',
                name: 'commission',
                class: 'text-right no-wrap'
            },            
            {
                data: 'ngo_wallet_amt',
                name: 'ngo_wallet_amt',
                class: 'text-right no-wrap'
            },
            {
                data: 'net_pay',
                name: 'net_pay',
                class: 'text-right no-wrap'
            },           
        ],
    });
    $('#searchbtn').click(function (e) {
        t.dataTable().fnDraw();
    });
    $('#resetbtn').click(function (e) {
        $('input,select', $(this).closest('form')).val('');
        t.dataTable().fnDraw();
    });
});

