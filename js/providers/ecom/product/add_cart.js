$(document).ready(function () {  	
    $(document).on('keyup', '#option-product-qty', function (e) {
        e.preventDefault();
        var rowid = $(this).attr("datacontent-rowid");
        var value =  parseInt($(this).val());
        if (value > 0){
            update_cart_quatity(value,rowid);
        } else {
            return false;
        }
    });
    $(document).on('click', '#img_url', function (e) {
        e.preventDefault();
        var id = $(this).attr("data-content");
        window.location.href="product/category/product?pid="+id;

    });

    $(document).on('click', '.custom_btn_up', function (e) {
        e.preventDefault();
        var rowid = $(this).closest('td').find('#option-product-qty').attr("datacontent-rowid");
        var value =  parseInt($(this).closest('td').find('#option-product-qty').val());
         value = value + 1;
        if (value > 0){
            update_cart_quatity(value,rowid);
        } else {
            return false;
        }
    });
    $(document).on('click', '.custom_btn_down', function (e) {
        e.preventDefault();
        var rowid = $(this).closest('td').find('#option-product-qty').attr("datacontent-rowid");
        var value =  parseInt($(this).closest('td').find('#option-product-qty').val());
        value = value - 1;
        if (value > 0){
            update_cart_quatity(value,rowid);
        } else {
            return false;
        }
    });
	
	$('.cart_details_row').on('click', '#place_order', function(e){		
		CURELE = $(this);
		$(this).attr('disabled', true);		
	});
});
$("#cartItems").on('change',':input', function () {
	var rowid = $(this).closest('td').find('#option-product-qty').attr("datacontent-rowid");
	var qty = $(this).val();
	if(parseInt(qty) > 0){
		update_cart_quatity(qty,rowid);
	}else{
		$(this).val(1);
	}
});
function update_cart_quatity(value,rowid){
	
    $.ajax({
        url:'product/update-cart-qty',
        data:{ 'qty':value,'rowid':rowid },
        dataType:"json",
        type:'post',
        beforeSend:function(){
			$("#cartItems :input").attr('disabled',true);
            $('.alert').remove();
        },
        success: function (op) {          
		$("#cartItems :input").attr('disabled',false);
            my_cart_list();
            if(op.error_quantity){
                $('.cart_quatity_error').before("<div class='alert alert-danger'>"+op.msg+"<a href='#' class='close' area-label='close'data-dismiss='alert'>×</a></div>");
            }
        }
    });
}

/*  Not Used */
/* function get_cart_details() {
    var url=$('#center_column').attr('ctr_url');   
    $.ajax({
        type: 'POST',
        url: 'cart-items',
        beforeSend:function(){
            $('.alert').remove();
        },
        success: function (op) {            
            if(op.cart_details){
                cartItems = '<table class="table table-bordered table-responsive cart_summary"><thead><tr><th>Product</th><th>Description</th><th>Qty</th><th>Sub Total</th></tr></thead><tbody>';
                $.each(op.cart_details, function (index, value) {
                    if(value.options.colour){
                        var clr = '<p>colour:' + value.options.colour + '</p>';

                    } else {
                        var clr ='';
                    }
                    if(value.options.product_size){
                        var siz = '<p>size:' + value.options.product_size + '</p>';

                    } else {
                        var siz ='';
                    }
                    cartItems += '<tr><td class="cart_product"><a href="#" id="img_url" data-content="' + value.id + '"><img class="img-responsive" src="' + value.options.imgs + '"alt="image"></a></td><td><p class="seller_name">' + value.name + '</p>seller:' + value.options.seller +clr+ siz +'</br><a class="remove_cart_btn"data-content="' + value.rowId + '"data-content-id="' + value.id + '" href="#">&times Remove</a></td><td class="qty"><div class=""><input class="form-control input-sm" id="option-product-qty" onkeypress="return isNumberKey(event)" name="product_qty" type="text" datacontent-rowid="' + value.rowId + '" data-value=""value="' + value.qty + '"></div><div class="btn-plus"><a href="#" class="custom_btn_up"><i class="fa fa-caret-up"></i></a><a href="#" class="custom_btn_down"><i class="fa fa-caret-down"></i></a></div></td><td>' + value.sub_total_innumber + '</td></tr>';

                })
                cartItems += '<tr><td colspan="2" rowspan="4"></td><td colspan="1">Total products</td><td colspan="1">' + op.cart_count + '</td></tr>';
              if(op.tax){
                   cartItems += '<tr><td colspan="1">Tax(gst)</td><td colspan="1">' + op.tax + '</td></tr>';
               }
              if(op.discount){
                    cartItems += '<tr><td colspan="1">Discounts</td><td colspan="1">' + op.discount + '</td></tr>';
             }
                cartItems += '<tr><td colspan="1"><strong>Total</strong></td><td colspan="1"><strong>' + op.total + '</strong></td></tr>';
                cartItems += '<tr><td colspan=""></td><td align="center"><a href="'+url+'"><button class="btn btn-warning" >PLACE ORDER</button></a></td></tr></tbody></table>';
                $('.cart_details_row').html(cartItems);
            } else {
                $('.cart_details_row').html('');
                $('.cart_quatity_error').before("<div class='alert alert-danger'>"+ $cart_list_not_avalable+"<a href='#' class='close' area-label='close'data-dismiss='alert'>×</a></div>");
            }
        }
    });
} */


