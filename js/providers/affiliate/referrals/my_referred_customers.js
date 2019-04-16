$(function () {
//	alert(baseUrl);
	var t = $('#downlinelist');
		
	t.DataTable({
		ordering:false,
		serverSide: true,
		processing: true,
		pagingType: 'input_page',		
		sDom: "t"+"<'col-sm-6 bottom info align'li>r<'col-sm-6 info bottom text-right'p>", 
		oLanguage: {
			"sLengthMenu": "_MENU_",
		},
		ajax: {
		    url: $('#downline_form').attr('action'),
			type: 'POST',
            data: function (d) {
                return $.extend({}, d, $('#downline_form input,select').serializeObject());
            }
        },	
		columns: [		
		    {   
			    data: 'firstname', 
                name: 'firstname',
                class: 'text-left',                
		    },			
			{   
			    data: 'signedup_on', 
                name: 'signedup_on',
                class: 'text-left',                
		    },
			{   
			    data: 'country', 
                name: 'country',
                class: 'text-left',                
		    }, 
			{   
			    data: 'sales_tot', 
                name: 'sales_tot',
                class: 'text-right',                
		    },	
			{   
			    data: 'cv_tot', 
                name: 'cv_tot',
                class: 'text-right',                
		    },	
		],
		responsive: {
            details: {
                display: $.fn.dataTable.Responsive.display.modal( {
                    header: function ( row ) {
                        var data = row.data();
                        return data.firstname;
                    }
                } ),
                renderer: $.fn.dataTable.Responsive.renderer.tableAll( {
                    tableClass: 'table'
                } )
            }
        }
	});
	
	$('#searchbtn').click(function (e) {
		t.dataTable().fnDraw();
	});
		
	$('#resetbtn').click(function (e) {	
        $('input,select', $(this).closest('form')).not('[type=checkbox]').val('');	   
        $('input:checkbox').not('[value=FirstName]').removeAttr('checked');
        t.dataTable().fnDraw();
    });
});
