$(function () {

	var t = $('#ammb_commission');
	t.DataTable({
		ordering:false,
		serverSide: true,
		processing: true,
		pagingType: 'input_page',		
		sDom: "t"+"<'col-sm-6 bottom info align'li>r<'col-sm-6 info bottom text-right'p>", 
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
			url: $('#form').attr('action'),
			type: 'POST',
			data: function ( d ) {
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
			name: 'confirm_date',
			data: 'confirm_date',
		},
		{
			data: 'total_earnings',
			name: 'total_earnings',
			class: 'text-right',
		},
		{
			data: 'earnings',
			name: 'earnings',
			class: 'text-right',
		},
		{
			data: 'commission',
			name: 'commission',
			class: 'text-right',
		},
		{
			data: 'ngo_wallet',
			name: 'ngo_wallet',
			class: 'text-right',
		},
		{
			data: 'net_pay',
			name: 'net_pay',
			'class': 'text-right',
		},	        		
		/*  {
			name: 'status',
			class:'details',
			data:function(row,type,set){
			  return '<button class="btn btn-sm btn-info">Details</label>';
			}				
		}, */ 
	],
	"preDrawCallback": function(settings ) {			
			 $("#exportbtn,#printbtn").hide();
		   },
		drawCallback: function (e, settings) {
			if(e.json.recordsFiltered>0){
				$("#exportbtn,#printbtn").show();
			}
		},
	responsive: {
		details: {
			display: $.fn.dataTable.Responsive.display.modal( {
				header: function ( row ) {
					var data = row.data();
					return data.confirm_date;
				}
			} ),
			renderer: $.fn.dataTable.Responsive.renderer.tableAll( {
				tableClass: 'table'
			} )
		}
	},
	 "rowCallback": function( row, rdata ) {		
		  $('td:eq(8)', row).attr('data-period',rdata.confirm_date);
	  }
 });

	
	$('#searchbtn').click(function (e) {

		t.dataTable().fnDraw();
	});
	
	$('#resetbtn').click(function (e) {
		$('input,select',$(this).closest('form')).val('');
        t.dataTable().fnDraw();
    });
	
	t.on('click','.details',function(e){
		e.preventDefault();
		var date = $(this).attr('data-period');
		$.ajax({
			url: 'affiliate/customer-commission/monthly_bonus_details',
			type: 'POST',
			data: {'date':date},
			success:function(op){
				if(op.status=='ok'){
					$('#report').hide();
					$('#details').html(op.content);
					$('#details').show();
				}
			}
        }); 
	})
	$('#details').on('click','.back',function(e){
		e.preventDefault();
		$('#report').show();
		$('#details').hide();
	});
	
});
