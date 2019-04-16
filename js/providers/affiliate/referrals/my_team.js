$(function () {
//	alert(baseUrl);
	var t = $('#downlinelist');
		
	t.DataTable({
		ordering:true,
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
                    var str = '<span class=""><b>'+row.full_name+' </b>(' + row.user_code + ')</span>';
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
					 return '<span class="">('+row.sponsor_code+')</span>'+row.sponsor_uname; 					 
				}      
            },
			{    
                name: 'upline_code',
                class: 'text-left',   
				render: function (data, type, row, meta) {
					 str= '<span class="">('+row.upline_code+')</span>'+row.upline_uname; 		
                     str+= '<br><span class="">Generation: ' + row.generation + '</span>';	
                    return str;					  
				}
            },
			{    
			    data: 'rank',
                name: 'rank',
                class: 'text-left',                
            },	
			{
				data: 'qv',
				name: 'qv',
				class: 'text-right no-wrap',
			 },
			 {
				data: 'activated_on',
				name: 'activated_on',
			  },
			 {    
                name: 'status',
                class: 'text-left',
				render: function (data, type, row, meta) {
          	          return '<span class="label label-'+ row.status_class + '">' + row.status + '</span>';					 
				}      
             },
			 
					
		/*   {
				data: 'signedup_on',
				name: 'signedup_on',
			  },
			  {				
				name: 'generation',
				class: 'text-center',
				render: function (data, type, row, meta) {
					return row.generation
				}
			  },  
			  {
				name: 'location',
				class: 'text-left',
				render: function (data, type, row, meta) {
					 if(row.location!=='') return '<span class="text-muted"><i class="fa fa-map-marker"></i>'+row.location+'</span>'; 
					 else return'';  	
				}
			  }, 			 
			  {
				data: 'sponsor_uname',
				name: 'sponsor',
				class: 'text-left no-wrap',
			  }, 
		      {
				data: 'signedup_on',
				name: 'signedup_on',
			   }, 
			   {    
                name: 'sponsor_code',
                class: 'text-left',
				render: function (data, type, row, meta) {
					 return row.sponsor_uname+' <span class="text-muted">('+row.sponsor_code+')</span>'; 					 
				}      
              },
	          {    
			    data: 'prorank',
                name: 'prorank',
                class: 'text-left',                
              },	 
			  {
				data: 'cv',
				name: 'cv',
				class: 'text-right no-wrap',
			  }, */
		
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
