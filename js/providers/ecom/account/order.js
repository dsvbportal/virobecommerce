$(document).ready(function () {


    $(document).on('click','.close_detail', function (e) {
        e.preventDefault();
        $('#order_details_row').hide();
         $('#list_div').show();
    });
 /*   $('#order-details').on('click','.close_detail', function(e){
        e.preventDefault();
             alert('sdds');
    });*/
 /*   $("#order-details").delegate(".close_detail", "click", function (){
        alert('jkjjj');
    });*/
	
    var DT = $('#data_table_order_list').dataTable({
        ordering: false,
        serverSide: true,
        processing: true,
        pagingType: 'input_page',
        sDom: "t" + "<'col-sm-6 bottom info align'li>r<'col-sm-6 info bottom text-right'p>",
        oLanguage: {
            "sLengthMenu": "_MENU_",
        },
        ajax: {
            url: 'account/my-orders-search',
            type: 'POST',
			//data: $('#order_listfrm').serialize(),
			data: function ( d ) { 
				return $.extend({}, d, $('input,select', '#order_listfrm').serializeObject());				
			},
            /* data: function (d) {
                d.phrase = $('#phrase').val();
                d.from_date = $('.from_date').val();
                d.to_date = $('.to_date').val();
            }, */
        },
        columns: [
            {
                data: 'order_date',
                name: 'order_date',
				render: function (data, type, row, meta) {					
                    return '<span>'+ row.remark +'</span><br><span>For Order #'+ row.order_code +'</span><br><span>'+ row.order_date +'</span>';
                }
            },
            {
                data: 'net_pay',
                name: 'net_pay',
            },
            {
                data: 'status',
                name: 'status',
                render: function (data, type, row, meta) {
                    return '<span class="label label-' + row.status_class + '">' + row.status + '</span>';
                }
            },           
            {
                orderable: false,
                class: 'text-center',
                render: function (data, type, row, meta) {                   
                    return addDropDownMenu(row.actions, true);
                }
            }
        ],
        responsive: {
            details: {
                display: $.fn.dataTable.Responsive.display.modal({
                    header: function (row) {
                        var data = row.data();
                        return data.full_name + ": " + data.user_code;
                    }
                }),
                renderer: $.fn.dataTable.Responsive.renderer.tableAll({
                    tableClass: 'table'
                })
            }
        }
    });

    $('#search').click(function (e) {
        e.preventDefault();
        DT.fnDraw();
    });
	
    $('#reset').click(function (e) {
        e.preventDefault();     
        $('#order_listfrm input').val('');     
    });
	
	$(document).on('click', '.view_details', function (e) {
        e.preventDefault();
		var CURELE= $(this);
        $.ajax({
			url: CURELE.attr('href'),              
			dataType: "json",
			type: 'post',
			beforeSend: function () {
				$('.alert').remove();
			},
			success: function (op) {
				if (op.order_details) {
                    var data =op.order_details;
                    $('#list_div').hide();
                    $('#order_details_row').show();
                    $('#ord-details', '#order-details').html('<span><strong>' + data.details.remark + '</strong></span><br><div class="row"><div class="col-md-6">Order Number<br><strong>' + data.details.order_code.value + '</strong><br>' + data.details.order_date + '</div><div class="col-md-6">Amount<br><strong>' + data.details.amount.value + '</strong><br><span class="label label-' + data.details.status_class + '">' + data.details.status + '</span></div></div>');
                    $('#shipping-address').html('<p><strong>Shipping Information</strong></p><div class="row"><div class="col-md-6">' + data.details.address1 + '</div></div>');
                    $('#order_id_fee').val(data.details.order_id);
                    if ((data.details.payment_details != undefined) && (data.details.payment_details != '')) {
                        var row = '';
                        $.each(data.details.payment_details, function (index, fld) {
                            if (fld.label != '') {
                                row = row + '<tr><td>' + fld.label + '</td><td class="text-right">' + fld.value + '</td></tr>';
                            } else {
                                row = row + '<tr><td>' + fld.value + '</td></tr>';
                            }
                        });
                        $('#pay-details').html('<p><strong>Payment Details</strong></p><div class="row"><div class="col-md-12"><table class="table">' + row + '</table></div></div>');
                    }
                    if ((data.details.order_items != undefined) && (data.details.order_items != '')) {
                        var row = '';
                        $.each(data.details.order_items, function (index, fld) {
                            if (fld.product_name) {
                                row = row + '<tr><td class="text-left">' + fld.product_name + '</td><td class="text-right">' + fld.qty + '</td><td class="text-right">' + fld.price + '</td></tr>';
                            }
                        });
                        $('#item-details').html('<p><strong>Product Items</strong></p><div class="row"><div class="col-md-12"><table class="table"><tr><th>Name</th class="text-right"><th>Quantity</th><th class="text-right">Price</th></tr>' + row + '</table></div></div>');
                    }
                    if ((data.details.bill_details != undefined) && (data.details.bill_details != '')) {
                        var rows = '';
                        $.each(data.details.bill_details, function (index, fld) {
                            rows = rows + '<div class="row"><div class="col-md-6"><strong>' + fld.label + ' :</strong><br></div><div class="col-md-6">' + fld.value + '</div></div>';
                        });
                        $('#net-details').html(rows);
                    }
/*
                    $('#net-details').html('<div class="row"><div class="col-md-6"><strong>' + data.details.bill_amount.label + ' :</strong><br></div><div class="col-md-6">' + data.details.bill_amount.value + '</div></div><div class="row"><div class="col-md-6"><strong>' + data.details.tax.label + ' :</strong><br></div><div class="col-md-6">' + data.details.tax.value + '</div></div><div class="row"><div class="col-md-6"><strong>' + data.details.shipping_charges.label + ' :</strong><br></div><div class="col-md-6">' + data.details.shipping_charges.value + '</div></div><div class="row"><div class="col-md-6"><strong>' + data.details.net_pay.label + ' :</strong><br></div><div class="col-md-6">' + data.details.net_pay.value + '</div></div>');
*/

                }
			}
		});
    });


    $(document).on('click', '.cancel', function (e) {
        e.preventDefault();
		var CURELE= $(this);
		$.ajax({
			url: CURELE.attr('href'),			
			dataType: "json",
			type: 'post',
			beforeSend: function () {
				$('.alert').remove();
			},
			success: function (op) {                   
				$('.order_details_row').before("<div class='alert alert-success'>" + op.msg + "<a href='#' class='close' area-label='close'data-dismiss='alert'>×</a></div>");
				DT.fnDraw();
			},
			error: function (jqXHR, exception, op) {
				$('.order_details_row').before("<div class='alert alert-danger'>" + jqXHR.responseJSON.msg + "<a href='#' class='close' area-label='close'data-dismiss='alert'>×</a></div>");
			},
		});
    });
    $("#rateBox").rate({
        length: 5,
        value: 4,
        readonly: false,
        size: '20px',
        textList: ['Poor', 'Average', 'Good', 'Very Good', 'Excellent'],
        selectClass: 'fxss_rate_select',
        incompleteClass: 'fxss_rate_no_all_select',
        customClass: 'custom_class',
        callback: function(object){
            $('#rating').val(object.index+1);
        }
    });
    $('#rate-now-form').on('submit', function (e) {
        e.preventDefault();
        CURFORM=$(this);
        $.ajax({
            url: CURFORM.attr('action'),
            data: CURFORM.serializeObject(),
            beforeSend:function(){
                $('.alert').remove();
            },
            success: function (op) {
                console.log(op);
                if(op){
                    $('#feedback_submit').hide();
                    $('#feedback_txt').val('');
                }

            }
        });
    });
});