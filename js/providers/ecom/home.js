$(document).ready(function () {
  				
	/* Get Page Data */	

	window.TSP.data.services = [{title: 'Free Shipping', desc: 'On order over $200', img: '', url: '#'},
        {title: '30-day return', desc: 'Moneyback guarantee', img:'' , url: '#'},
        {title: '24/7 support', desc: 'Online consultations', img: '', url: '#'},
        {title: 'SAFE SHOPPING', desc: 'Safe Shopping Guarantee', img: '', url: '#'}];
		
/* 	window.TSP.data.services = [{title: 'Free Shipping', desc: 'On order over $200', img: 'imgs/40/40/assets/data/s1.png', url: '#'},
        {title: '30-day return', desc: 'Moneyback guarantee', img: 'imgs/40/40/assets/data/s2.png', url: '#'},
        {title: '24/7 support', desc: 'Online consultations', img: 'imgs/40/40/assets/data/s3.png', url: '#'},
        {title: 'SAFE SHOPPING', desc: 'Safe Shopping Guarantee', img: 'imgs/40/40/assets/data/s4.png', url: '#'}]; */
		
    /* $('#services').addServices(window.TSP.data.services); */
    $.ajax({
        url: window.location.BASE + 'get-page-data',
        data: {page: 'home'},
        success: function (data) {		
            window.TSP.data = data;		
            //$('#cart-block').updateCart(window.TSP.data.my_cart);            
             $(document.body).loadMenus({menus: window.TSP.data.menus});                        
				/* if ($('#trademark-list').length) {
					$('#trademark-list').loadPayments({payments: window.TSP.data.payment_types});
				}*/		
			if(window.TSP.data.sliders !== null && window.TSP.data.sliders !== ''){
				$(document.body).addSlider({page: 'home', featuredSlider: 'div#slider-container', imgSlider: 'div#img-slider', sliders: window.TSP.data.sliders.slider});
			}  
			
            $('#searchCategory').loadSelect({
                url: window.location.BASE + 'main-categories',              
                key: 'url_str',  //'url_str'
                value: 'category',
                optionData: [{key: 'url', value: 'url'}],
                firstOptionSelectable: true,
                firstOption: {key: '', value: 'All Categories'},
                selected: '',
                values: window.TSP.data.main_categories
            });				
			
			var selectCat = $('#searchCategory').attr('data-category');	     
			if(selectCat != ''){			
                var cat = '';			
				$("#searchCategory > option").each(function() {						
					if(this.value == selectCat){                    			
						cat= this.value;
						$("#searchCategory").find("option[value=" + this.value +"]").attr('selected', true);
						$('#search-products #select2-searchCategory-container').attr('title',this.text).text(this.text);
					}
				});	
				if(cat == ''){				
					$('#search-products #select2-searchCategory-container').attr('title','All Categories').text('All Categories');		
					$('#searchCategory option:selected').removeAttr('selected');                  				
				}

			}else {	
			    $('#search-products #select2-searchCategory-container').attr('title','All Categories').text('All Categories');
			}			
        }
    });		
	
	$('#search-products').on('click', '.btn-search', function(e){
		e.preventDefault();	
		//var category 	= ($('#search-products #searchCategory').val()) ? $('#searchCategory').val() : '';	
		var searchterm  = $('#search-products #searchTerm').val(); 	
		var category    = ($('#searchCategory option:selected').val() != '')?$('#searchCategory option:selected').val(): $('#searchCategory').attr('data-category');
		if(searchterm == undefined){			
			searchterm = ''; 
		}
		if(category != '' && category != undefined && category != 'all'){
			window.location.href = window.location.BASE+'product/'+category +'?searchTerm='+searchterm;
		}else {		
		    window.location.href = window.location.BASE+'product/category?searchTerm='+searchterm;
            //return false;
		}		
	}); 
	
	$('#left_column .inner-desc').on('click', '#logout', function (e) {
        e.preventDefault();
        var Curele=$(this);
        $.ajax({
            url: Curele.attr('href'),           
            success: function (op) {

				console.log(op);
			   window.location.href =op.url;
            }
        });
    });	 
	
	/* Contact Us */
    $('#contact_usfrm').validate({
        errorElement: 'div',
        errorClass: 'help-block',
        focusInvalid: false,        
        errorPlacement: function (error, element) {
            if (element.parent().hasClass('input-group')) {
                error.insertAfter(element.parent());
            } else {
                error.insertAfter(element);
            }
        },       
        rules: {
            subject: {required: true},
            email: {required: true,email: true},
            order_reference: {required: true},
            message: {required: true},
        },
        messages: {
            subject:{
              required:'Please enter subject',
            },
            email:{
              required:'Please enter email',
              email:'Please enter valid email address'
            }
        },
        submitHandler: function (form, event) {
        event.preventDefault();               
        if ($(form).valid()) { 

                   CURFORM = $('#contact_usfrm');
                   $.ajax({
                      url: CURFORM.attr('action'),
                      data: CURFORM.serialize(),
                     dataType:'json' ,
                      success: function (op)
                      {   
                         $('#contact_usfrm')[0].reset();
                      }
                   });
              }
         }
    });   
	my_cart_list();

    $(document).on('click', '.remove_cart_btn', function (e) {
        e.preventDefault();
        var row_id=$(this).attr('data-content');
        var data_id=$(this).attr('data-content-id');
        $.ajax({
            url:'cart-items-remove',
            data:{'row_id':row_id},
            dataType:"json",
            type:'post',
            success: function (data) {
                if(data.status == 200){
                    $('#data_'+data_id).remove();                		
                    my_cart_list();	
                }
            }
        });
    });   
	
	/* Search Product */
	var searchList;
	var	SPR= $('#search-products');
	SPR.on('keyup', '#searchTerm', function (e) { 
        e.preventDefault();	  
		clearTimeout(searchList);
			var searchterm = $('#search-products #searchTerm').val();
            var category = $('#searchCategory').val();	
			if (searchterm.length >= 2) {	              		
				if(category == '' || category == undefined){
                    category = searchterm;
					searchterm = '';
				}			
				$('#search-options-list').empty();					
				$.ajax({				
					url: $('#search-products').attr('data-search'),
					data: {category: category, searchterm: searchterm},  
					success: function (op) {
						$('#search-options-list').empty();			
                        /* $('#search-options-list').html('<option class="list-group-item" value="Mob">SamSung</option><option class="list-group-item" value="Mob">Nokia</option><option class="list-group-item" value="Mob">Sony</option>'); */		
                        if (op.data !== '') {
                            $.each(op.data, function (k, row) {   
                                $('#search-options-list').append([
								    $('<li>', {class: 'list-group-item'}).append([
									    $('<a>', {class: 'product_category' ,href: row.url}).append([$('<i>', {class: 'fa fa-search'}),'  '+ row.category]),
									]),
                                ]);
                            });
                        }                           
						$('#search-options-list').show();
                        $('#search-options-list').focus();
                    }
				}); 
			}
		//}, 1000);      
    });	 
	
	SPR.on('focusout','#searchTerm',function(e){
		e.preventDefault();
		$('#search-options-list').fadeOut('slow');				
	});
	
	var SEARCHTXT = '';
	$('#search-options-list').on('click', '.product_category', function (e) { 
       // e.preventDefault();		 
        var Curele=$(this);
		SEARCHTXT = Curele.text();
		$('#searchTerm').val(SEARCHTXT);
		$('#search-options-list').hide();	
        SEARCHTXT = '';		
    });	
});

/* AddToCard List */
 function my_cart_list(){
	$.ajax({
		type: 'POST',
		url: 'cart-items',		
		success: function (op) {
	        $('.cart_total_quatity').text(op.cart_count);
			$('.total_cart_sum').text(op.cart_sub_total_numeric);
            if(op.cart_count > 0){

				$('.cart-block').show();
				cartItems = '<li>';
				$.each(op.cart_details,function(index,value){
					/* cartItems +='<ul><li class="product-info" id="data_'+value.id+'"><div class="p-left"><img class="img-responsive" src="'+value.options.imgs+'"alt="p10"> </div><div class="p-right"><p class="p-name">'+value.name+'</p><p>quantity:<span class="p-rice">'+value.qty+'</span></p><p class="p-rice">'+value.subtotal+'</p></div><div class="remove_cart_btn" data-content="'+value.rowId+'"data-content-id="'+value.id+'"><a href="#" class="btn btn-link">&times Remove</a></button></div></li></ul>'; */
					
						cartItems +='<div class="single-cart-box" id="data_'+value.id+'"><div class="cart-img"><img src="'+value.options.imgs+'"><span class="pro-quantity">'+value.qty+'</span></div><div class="cart-content"><h6><a href="#">'+value.name+'</a></h6><span class="cart-price">'+value.subtotal+'</span></div><a class="del-icone remove_cart_btn" data-content="'+value.rowId+'" data-content-id="'+value.id+'" href="#"><i class="ion-close"></i></a></div>';
				})
				cartItems +='<div class="cart-footer"><div class="cart-actions text-center"><a class="cart-checkout" href="'+window.location.BASE+'product/cart-items-view">Checkout</a></div></div></li>';
				$('.cart-block-list').html(cartItems);
				
				//console.log($('.cart_details_row').length);				
				if($('.cart_details_row').length>0){
					var url = $('#center_column').attr('ctr_url');
					if(op.cart_details){
						cartItems = '<table class="cart_summary"><thead><tr><th>Product</th><th>Description</th><th>Qty</th><th>Sub Total</th></tr></thead><tbody>';
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
							cartItems += '<tr><td class="cart_product product-thumbnail"><a href="#" id="img_url" data-content="' + value.id + '"><img class="img-responsive" src="' + value.options.imgs + '"alt="image"></a></td><td class="product-name"><p class="seller_name">' + value.name + '</p>' + value.options.seller +clr+ siz +'</br><a class="remove_cart_btn"data-content="' + value.rowId + '"data-content-id="' + value.id + '" href="#">&times Remove</a></td><td class="product-quantity qty"><input class="input-sm" id="option-product-qty" onkeypress="return isNumberKey(event)" name="product_qty" type="number" datacontent-rowid="' + value.rowId + '" data-value=""value="' + value.qty + '"></td><td align="right">' + value.subtotal + '</td></tr>';
						})
						cartItems += '<tr><td colspan="2" rowspan="4"></td><td colspan="1" align="right">Total products</td><td colspan="1" align="right"><strong>' + op.cart_count + '</strong></td></tr>';
						if(op.cart_total){
							cartItems += '<tr><td colspan="1" align="right">Total Order Value</td><td colspan="1" align="right"><strong>' + op.cart_total + '</strong></td></tr>';
						}
						if(op.cart_tax){
							cartItems += '<tr><td colspan="1" align="right">Total Tax(gst)</td><td colspan="1" align="right"><strong>' + op.cart_tax + '</strong></td></tr>';
						}
						if(op.cart_shipping_charge){
							cartItems += '<tr><td colspan="1" align="right">Total Shipping Charges</td><td colspan="1" align="right"><strong>' + op.cart_shipping_charge + '</strong></td></tr>';
						}
						if(op.discount>0){
							cartItems += '<tr><td colspan="1" align="right">Total Discount</td><td colspan="1" align="right"><strong>' + op.cart_tax + '</strong></td></tr>';
						}
						cartItems += '<tr> <td colspan="2"></td><td align="right"><strong>Grand Total</strong></td><td colspan="1" align="right" class="product-subtotal">' + op.cart_price_sub_total + '</td></tr>';
						cartItems += '<tr><td colspan="3"></td><td class="wc-proceed-to-checkout"><a href="'+url+'" id="place_order">Proceed to Checkout</a></td></tr></tbody></table>';
						$('.cart_details_row').html(cartItems);
					} /*else {
						$('.cart_details_row').html('');
						$('.cart_quatity_error').before("<div class='alert alert-danger'>"+ $cart_list_not_avalable+"<a href='#' class='close' area-label='close'data-dismiss='alert'>×</a></div>");
					}*/
				}
            } else{
           		$('.cart-block').hide();				
					$('.cart_details_row').html('');
					$('.cart_quatity_error').before("<div class='alert alert-danger'>Your cart is empty</div>");
			}
		},	
	});
		
	/* Reset Password Using Reset Link*/	
    $('#password-resetfrm').on('submit', function (e) {
        e.preventDefault();			
        CURFORM = $(this);
		var url = $(location).attr('href');
        arr = url.split("/");		
        $.ajax({
		    headers: {token: arr[arr.length - 1]},
            url: CURFORM.attr('action'),
            data: CURFORM.serialize(),
			beforeSend:function(){
			    $('input[type="submit"]',CURFORM).attr('disabled',true);			
			},
            success: function (op) {
			    CURFORM.resetForm();				
			    $('input[type="submit"]',CURFORM).attr('disabled',false);	
                $('#restoken', CURFORM).val('');
                $('#newpwd,#conf_newpwd', CURFORM).attr('type', 'password');
                $('.pwdHS', CURFORM).find('i').removeClass().addClass('fa fa-eye-slash');
                window.location.assign(op.url);                           
            },
			error: function (jqXHR, exception, op) {
				CURFORM.not('#restoken').val('');
				$( "input:not(:checked)" )
				$('input[type="submit"]',CURFORM).attr('disabled',false);				
			},
        });
    });	
}