$(function () {
    var t = $('#supplier_list');
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
            url: $('#retailers_listfrm').attr('action'),
            type: 'POST',
			data: function (d) {
                return $.extend({}, d, $('#retailers_listfrm input,select').serializeObject());
            }         
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
			    data: 'full_name',
                name: 'full_name',
                class: 'text-left',
                render: function (data, type, row, meta) {  		
				
				 var str = '<b>'+row.full_name+'</b> (#'+row.mrcode + ')';
					str+= '<br><span class="text-muted"><i class="fa fa-map-marker"></i> ' + row.address + '</span>';
					return str;
                }
              }, 
			  {   
			    data: 'region_fr',
                name: 'region_fr',
                class: 'text-left',
                render: function (data, type, row, meta) {  	
                 if(row.region_fr!=''){				
                 var str = '<span class="">'+row.region_fr.full_name+' </span>';
				  str+= '<br><span class=""><i class="fa fa-mobile"></i>'+row.region_fr.phonecode+''+ row.region_fr.mobile + '</span>';
			    }else{
					var str='';
				}
				  return str;
                }
              }, 
				
			  {   
			    data: 'state_fr',
                name: 'state_fr',
                class: 'text-left',
                render: function (data, type, row, meta) {  		 
                if(row.state_fr!=''){				
                  var str = '<span class="">'+row.state_fr.full_name+' </span>';
				  str+= '<br><span class=""><i class="fa fa-mobile"></i>'+row.state_fr.phonecode+''+row.state_fr.mobile + '</span>';
				}
				else{
				    var str ='';
				}
				 return str;
                }
              },
			  {   
			    data: 'district_fr',
                name: 'district_fr',
                class: 'text-left',
                render: function (data, type, row, meta) {  
               if(row.district_fr!=''){				
                  var str = '<span class="">' +row.district_fr.full_name+' </span><br><span class=""><i class="fa fa-mobile"></i>'+row.district_fr.phonecode+''+row.district_fr.mobile + '</span>';
				/*  str+= '<hr style="margin:0;height:1px" /><span class="text-muted"><i class="fa fa-map-marker"></i> '+row.district_fr.access_location+'</span>';*/
		        }
				else{
				  var str='';
				}
				  return str;
                }
              },
			  {   
			    data: 'city_fr',
                name: 'city_fr',
                class: 'text-left',
                render: function (data, type, row, meta) {  		
			  if(row.city_fr!=''){
				 var str = '<span class="">'+row.city_fr.full_name+' </span>';
				  str+= '<br><span class=""><i class="fa fa-mobile"></i>'+ row.city_fr.phonecode +''+row.city_fr.mobile + '</span>';
				}	
			   else{
					 var str = '';
				   }					   
				  return str;
                }
              },
			  {   
			    data: 'created_on',
                name: 'created_on',
                class: 'text-left',
				width:'12%',
              },
			  {
                name: 'status',
                class: 'text-left',
                data: function (row, type, set) {
                    return '<span class="label label-'+row.status_class+'">' + row.status + '</span> ';
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

    $('#searchbtn').click(function (e) {
        t.dataTable().fnDraw();
    });

    $('#resetbtn').click(function (e) {
		$('input,select',$(this).closest('form')).val('');
        t.dataTable().fnDraw();
    });
	

});


