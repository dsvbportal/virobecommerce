$(function () {
 //$('#search').click(function () {
	var t = $('#referralslist');
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
			
			url: $('#form_referrals').attr('action'),
			type: 'POST',
			data: function ( d ) {
				d.generation = $('#generation').val();
				d.from_date = $('#from_date').val(); 
				d.to_date = $('#to_date').val();
                var filterchk = [];
                $('#chkbox :checked').each(function() {
               filterchk.push($(this).val());
			 
      });  
		d.filterchk = filterchk;                  
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
			    data: 'uname',
                name: 'uname',
                class: 'text-left',
                render: function (data, type, row, meta) {                    					
                    var str = '<b>'+row.full_name+'</b>(' + row.user_code + ')';
                    if(row.email !== ''){
						str+= '<br><span class=""><i class="fa fa-envelope"></i> ' + row.email + '</span>';
					}
					if(row.mobile !== ''){
						str+= '<br><span class=""><i class="fa fa-mobile"></i> ' + row.mobile + '</span>';
					}
					return str;
                }
              }, 
			  {
				data: 'country',
				name: 'country',
			  },
			  {    
                name: 'sponsor_code',
                class: 'text-left',
				render: function (data, type, row, meta) {
					 return '<span class="">'+row.sponsor_uname+'</span>('+row.sponsor_code+')'; 					 
				}      
             },			
			 {    
                name: 'upline_code',
                class: 'text-left',   
				render: function (data, type, row, meta) {
					str = '';
					if(row.upline_uname!=''){
					str= '<span class="">'+row.upline_uname+' </span>('+row.upline_code+')'; 		
					}
					if(row.generation!=''){
                    str+= '<br><span class="">Generation: ' + row.generation + '</span>';	
					}
                    return str;					  
				}
             }, 
			 {
				data: 'rank',
				name: 'rank',
				class: 'text-right no-wrap',
			 },
			 {
				data: 'qv',
				name: 'qv',
				class: 'text-right no-wrap',
			 },	
			 {
                    "data": "signedup_on",
                    "name": "signedup_on"
                },
			{
                    "data": "activated_on",
                    "name": "activated_on"
                },
			 {    
                name: 'status',
                class: 'text-left',
				render: function (data, type, row, meta) {
          	          return '<span class="label label-'+ row.status_class + '">' + row.status + '</span>';					 
				}      
             },
		   /* {
				
				name: 'status',
				class: 'text-right no-wrap',
				data: function (row, type, set) {
					return '<span class="label label-'+ row.status_class + '">' + row.status + '</span>';
					
				}
			} */
		    /*  {
				data: 'user_code',
				name: 'user_code',
				class: 'text-right no-wrap',
			  },
			  {
				data: 'full_name',
				name: 'full_name',
			  },
			  {
				data: 'email',
				name: 'email',
			  }, */
			  
			  /* {
				data: 'country',
				name: 'country',
				class: 'text-left', */
				/* render: function (data, type, row, meta) {
					 if(row.location!=='') return '<span class="text-muted"><i class="fa fa-map-marker"></i>'+row.location+'</span>'; 
					 else return'';  	
				} */
			  /* },	
			  {
				data: 'qv',
				name: 'qv',
				class: 'text-right no-wrap',
			  },		
			  {
                data: 'generation',
				name: 'generation',
				class: 'text-right no-wrap',
			  },	 */		
			 /* {    
			    data: 'upline_name',
                name: 'upline_name',
                class: 'text-left',
                render: function (data, type, row, meta) {
					
					if(row.upline_name!==''){
						return '<span class="text-muted">' + row.upline_name + '</span><span class="text-muted">: <b>' + row.generation +'</b></span>';
					} else {
						return ''
					}
                }
            }, */
            /* {    
                name: 'upline_code',
                class: 'text-left',   
				render: function (data, type, row, meta) {
					 return (row.upline_code!='')? row.upline_uname+' <span class="text-muted">('+row.upline_code+')</span>':''; 					 
				}
            },		
			{
				data: 'rank',
				name: 'rank',
				class: 'text-right no-wrap',
			},	 */
					
	   ],
			responsive: {
            details: {
                display: $.fn.dataTable.Responsive.display.modal( {
                    header: function ( row ) {
                        var data = row.data();
                        return data.full_name+": "+data.user_code;
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
		$('input,select',$(this).closest('form')).val('');
        t.dataTable().fnDraw();
    });
	
	
});
