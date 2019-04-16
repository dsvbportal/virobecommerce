var page_current = 1;
var page_total;
var move_nxt;
jQuery.fn.extend({
    loadProducts: function (options){
        options = (options !== undefined) ? options : {};
        var _this 			= this;
        _this.xhr 			= false;
        _this.searchTerm 	= '';
        _this.currentPage 	= 1;
        _this.totalPages 	= 0;
        _this.sort_by 		= 'POPULARITY';
        _this.brand_id 		= null;
        _this.caregory_id 	= null;
        _this.totalProducts = 0;
        _this.products 	    = [];
        _this.brands 	    = [];
        _this.categories    = [];
        _this.properties    = [];       
        _this.options = {
            productsPerPage: 0,
            url: '',
            data: {},
            success: null
        };

        $.extend(_this.options, options);

        _this.printCategory = function (e, c) {	
			if(String(c.category) !== 'All'){
				var li = $('<li>').append('<span>').append($('<a>').attr({href: c.url, class: 'change-category', 'data-category': c.url_str}).text(c.category));
			}else{
				var li = $('<li>');
			}
			
			e.append(li);
			if (c.children != undefined) {
				var sub = $('<ul>');
				$.each(c.children, function (k, se) {
					_this.printCategory(sub, se);
				});
				li.append(sub);
			}
						
        };
        _this.updateContent = function () {
            $('.categories-list').removeClass('ajax-loader');
            var listedProducts = (_this.currentPage * _this.options.productsPerPage);
            listedProducts = (_this.totalProducts > listedProducts) ? listedProducts : _this.totalProducts;
            $('.page-heading-title').html([_this.data.title, $('<small>').attr({}).append()]);
            document.title = _this.data.title;
            if (_this.data.breadcrums != undefined) {
                $('.breadcrumb').empty();
                
                    $('.breadcrumb').append($('<ul>',{class:'d-flex align-items-center'}).append(function(){
						$bread = [];
						$.each(_this.data.breadcrums, function (k, e) {
							$bread.push($('<li>').append($('<a>',{'href':e.url}).attr({'data-category': e.url_str}).text(e.title)))
								/* if (k < _this.data.breadcrums.length - 1)
								$('.breadcrumb').append($('<span>').attr('class', 'navigation-pipe')); */
						})
						return $bread;
                   
                }));
            }
            if(_this.data.categories != undefined) {
		        $('.categories-list').empty();	
					console.log(_this.data.categories);	
					
                $.each(_this.data.categories, function (k, e) {                     
					_this.printCategory($('.categories-list'), e);					
					/* if(k != 0){	
						_this.printCategory($('.categories-list'), e);
					} */
                });				
            }
            $('#filters').loadFilters(_this.data.filters);
            $('#browse-tags').loadTags(_this.data.tags);
        };
        _this.loadProducts = function (empty, withFilters) {
			
            empty = (empty != undefined) ? empty : false;
            withFilters 				  = (withFilters != undefined) ? withFilters : false;
            _this.options.data.page 	  = _this.currentPage;
	        _this.options.data.searchTerm = _this.searchTerm;
            _this.options.data.brand_id   = _this.brand_id;
            _this.options.data.tag 		  = $('#browse-tags a.active').data('tag');
            _this.options.data.sort_by    = $('#sort_by').val();
			_this.options.data.category_slug = $('#searchCategory').attr('data-category');
			//load_caterories();               
	        var temp = {};	
            if (withFilters) {
			    $.extend(temp, _this.options.data, $.getURLParams());
            }
            else {
                var url_data = $.getURLParams();
                $.extend(temp, _this.options.data, {spath: url_data.spath});
            }
            if (_this.xhr && _this.xhr.readyState !== 4) {
                _this.xhr.abort();
            }
            _this.xhr = $.ajax({
                url: window.location.BASE + 'product/browse',
                data: temp,
                success: function (data) {
                    move_nxt = data.move_next;
                    if (move_nxt == false) {
                        preventCall = true;
                    } else {
                        preventCall = false;
                    }
					//console.log(data);
                    _this.data = data;
                    _this.totalProducts = data.totalProducts;
                    _this.totalPages = data.totalPages;
                    page_current 	 = data.page;
                    page_total 		 = data.totalPages;
                    _this.options.productsPerPage = data.productsPerPage;
                    _this.updateContent();
                    _this.products = (empty) ? (data.products != undefined) ? data.products :'' : _this.products.concat(data.products);		
				    $.each(_this.products, function (k, e) {
						PRODUCT_LIST[e.code] = e;
					});				
                    _this.print();
                }
            });
        };

        _this.print = function () {
            _this.printProducts();
            _this.printPage();
            _this.loadEvents();
        };
        _this.printProducts = function () {
            _this.empty();
            if(_this.products.length > 0){				
                $('#left_filter_datas').show();
                for (var i in _this.products) {
                    _this.append(_this.layout(_this.products[i]));
				}
            }else {
                $('#left_filter_datas').hide();
			    return _this.append($('<li>').attr({class: 'col-sx-12 col-sm-12'}).append($('<div>').attr({class: 'product-container text-center text-muted',style: 'padding: 10px;'}).append($('<b>').append('No Product Found.'))));
			}	            
        };
        _this.layout = function (product) {
            return $('<div>').attr({class: 'col-lg-4 col-md-4 col-sm-6 col-6'}).append(
                    $('<div>').attr('class', 'single-product').append(
                    [
			            $('<div>').attr('class', 'pro-img').append(
                                [
                                    $('<a>').attr({href: product.url}).append(
                                        $('<img>').attr({class: 'img-responsive', alt: product.name, title: product.name, 'data-src': product.imgs[0].img_path, 'src': product.imgs[0].img_path}),
									
                                      ),
									 /*$('<a>').attr({href: '#'}).append({class:'quick_view', 'data-toggle':'modal','data-target':'#myModal'}).html('<i class="lnr lnr-magnifier"></i>'),		*/
									 
                                  
                                ]),
						$('<div>').attr({class:'pro-content'}).append($('<div>').attr({class:'pro-info'}).append([
							$('<h4>').append($('<a>').attr({'href':product.url}).text(product.name)),
							$('<p>').append($('<span>').attr({class:'price'}).text(product.price)).append($('<del>').attr({class:'prev-price'}).text(product.mrp_price)),
							$('<div>').attr({class:'label-product l_sale'}).text(product.off_per).append($('<div>').attr({class:'symbol-percent'})).text('%'),
						]),
						$('<div>').attr({class:'pro-actions'}).append($('<div>').attr({class:'actions-primary'}).append($('<a>').attr({href: product.add_cart_url, title: 'Add to Cart', class: 'add-to-cart-btn', id: 'btn_add_cart','data-id': product.code}).text('+ Add To Cart'))).append($('<div>').attr({class:'actions-secondary'}).append([
							$('<a>').attr({href:'#'}).html('<i class="lnr lnr-sync"></i>').append($('<span>').text('Add To Compare')),
							$('<a>').attr({class:'add-to-wish-list','data-id': product.code,'data-category':product.category_url_str, 'data-product':product.product_slug}).html('<i class="lnr lnr-heart"></i>').append($('<span>').text('Add to WishList')),
						])))
                    ])
                    );
        };
        _this.printPage = function () {
            $('.pagination').empty();
            $('.pagination').append([
                $('<li>').append([
                    $('<a>').attr({'href': '#'}).append([
                        'More...'
                    ]).click(function (e) {
                        e.preventDefault();
                        _this.nextPage();
                    })
                ])
            ]);
        };
        _this.goToPage = function (p) {
            if (p >= 1 && _this.totalPages <= p) {
                _this.currentPage = p;
                _this.loadProducts(false, true);
            }
        };
        _this.nextPage = function () {
            if (_this.totalPages >= _this.currentPage + 1) {
                _this.currentPage += 1;
                _this.loadProducts(false, true);
            }
        };
        _this.previousPage = function () {
            _this.currentPage - 1;
            if ((_this.currentPage - 1) >= 1) {
                _this.currentPage -= 1;
                _this.loadProducts(false, true);
            }
        };
        _this.loadEvents = function () {
            //$('img', _this).unveil();
            $('#filters input[type="checkbox"],#filters input[type="number"], #sortby', $(document.body)).off('change').on('change', function () {
		       var data = $('#filter-form').serializeArray();				
			   var url_data = $.getURLParams();				
			    data = data.filter(function (n) {				
                    return n.value;
                });				
                data.push({name: 'spath', value: url_data.spath});			
                data.push({name: 'sort_by', value: $('#sortby').val()});			
				window.location.AddToUrl(document.title, $.param(data));				
                _this.loadProducts(true, true);
            });
            $('#price-range-min', $(document.body)).off('change,stepUp,stepDown').on('change,stepUp,stepDown', function () {				
                $('#price-range-max').prop('min', parseInt($(this).val()) + 1);
            });
            $('#price-range-max', $(document.body)).off('change,stepUp,stepDown').on('change,stepUp,stepDown', function () {				
                $('#price-range-min').prop('max', parseInt($(this).val()) - 1);
            });
            $('.browse-tag', $(document.body)).off('click').on('click', function (e) {
                e.preventDefault();
                $(this).addClass('active');
                _this.loadProducts(true, true);
            });
			$('.change-category', $(document.body)).off('click').on('click', function (e) {
                e.preventDefault();
		        var CurEle = $(this);				
				$('#searchCategory').attr('data-category',CurEle.attr('data-category'));
				//$('#searchCategory').attr('data-category',$(this).attr('data-category'));
                window.location.ChangeUrl(CurEle.text(), CurEle.attr('href'));
                _this.loadProducts(true,true);
            });			
           /*$('.change-category .change-category,.change-category', $(document.body)).off('click').on('click', function (e) {
                e.preventDefault();
                var CurEle = $(this);
                window.location.ChangeUrl(CurEle.text(), CurEle.attr('href'));
                _this.loadProducts(true,true);
            }); */			
            $('#search-products', $(document.body)).off('submit').on('submit', function (e) {
                e.preventDefault();		
                window.location.ChangeUrl($('#searchCategory option:selected').text(), $('#searchCategory option:selected').data('url'));
                _this.searchTerm = $('#searchTerm', $('#search-products')).val();	
                _this.loadProducts(true);
            });
        };
        _this.loadProducts(true, true);
        return _this;
    },
    loadTags: function (tags) {
        var _this = $(this);		
        _this.empty();
        if (tags != null && tags != undefined && tags.length > 0) {		
			$('#columns #tags').show();
            $.each(tags, function (k, tag) {
                _this.append($('<a>', {class: 'browse-tag', 'data-tag': tag, href: '#'}).append($('<span>', {class: 'level' + (Math.floor(Math.random() * 5) + 1)}).append(tag)))
            });
        }else{			
			$('#columns #tags').hide();
		}
    },
    appendProducts: function (empty,withFilters,page) {
        var _this = this;
          var options=[];
        options.data=[];
        empty = (empty != undefined) ? empty : false;
        withFilters 				  = (withFilters != undefined) ? withFilters : false;
         options.data.page 	  = page;
         options.data.searchTerm = '';
         options.data.brand_id   = null;
         options.data.tag 		  = $('#browse-tags a.active').data('tag');
         options.data.sort_by    = $('#sort_by').val();
         options.data.category_slug = $('#searchCategory').attr('data-category');
        var temp = {};
        if (withFilters) {
            $.extend(temp,options.data, $.getURLParams());
        }
        else {
            var url_data = $.getURLParams();
            $.extend(temp, options.data, {spath: url_data.spath});
        }
        console.log(temp);
         if (_this.xhr && _this.xhr.readyState !== 4) {
             _this.xhr.abort();
         }
        _this.xhr = $.ajax({
            url: window.location.BASE + 'product/browse',
            data: temp,
            dataType:'json',
            success: function (data) {
               // products.product = data.products;
                move_nxt = data.move_next;
                if (move_nxt == false) {
                    preventCall = true;
                } else {
                    preventCall = false;
                }
                page_current = data.page;
                page_total = data.totalPages;
                $.each(data.products, function (k, e) {
                    PRODUCT_LIST[e.code] = e;
                });
                for (var i in data.products) {
                    _this.append(_this.layout(data.products[i]));
                        i = i++;
                }


            }
        });
    },
   layout:function (product) {
            return $('<li>').attr({class: 'col-sx-12 col-sm-4'}).append(
                $('<div>').attr('class', 'product-container').append(
                    [
                        $('<div>').attr('class', 'left-block').append(
                            [
                                $('<a>').attr({href: product.url}).append(
                                    $('<img>').attr({class: 'img-responsive', alt: product.name, title: product.name, 'data-src': product.imgs[0].img_path, 'src': product.imgs[0].img_path})
                                ),
                                $('<div>').attr({class: 'quick-view'}).append([
                                    (UserDetails != undefined) ? $('<a>').attr({href: '#', title: 'Add to my wishlist', class: 'heart add-to-wish-list', 'data-id': product.code,'data-category':product.category_url_str, 'data-product':product.product_slug}) : '',
                                    /* $('<a>').attr({href: '#', title: 'Add to compare', class: 'compare'}),
                                     $('<a>').attr({href: '#', title: 'Quick view', class: 'search'}) */
                                ]),
                                $('<div>').attr({class: 'add-to-cart'}).append(
                                    $('<a>').attr({href: product.add_cart_url, title: 'Add to Cart', class: 'add-to-cart-btn', id: 'btn_add_cart','data-id': product.code}).html('Add to Cart')
                                )
                            ]),
                        $('<div>').attr('class', 'right-block').append(
                            [
                                $('<h5>').attr({'class': 'product-name'}).append($('<a>').attr({'href': product.url}).text(product.name)),
                                $('<div>').attr({'class': 'product-star'}).append(function () {
                                    var stars = [];
                                    if (product.rating_count == null) {
                                        product.rating_count = 0;
                                    }
                                    for (var i = 1; i <= parseInt(product.avg_rating); i ++) {
                                        stars.push($('<i>').attr('class', 'fa fa-star'));
                                    }
                                    for (var i = 1; i <= 5 - parseInt(product.avg_rating); i ++) {
                                        stars.push($('<i>').attr('class', 'fa fa-star-o'));
                                    }
                                    return stars;
                                }),
                                $('<div>').attr({'class': 'content_price'}).append(function () {
                                    var price = [];
                                    price.push($('<span>').attr('class', 'price product-price').html(product.price));
                                    if (product.off_per != undefined) {
                                        price.push($('<span>').attr('class', 'price old-price').html(product.mrp_price));
                                        price.push($('<span>').attr('class', 'price off-per').html(product.off_per));
                                    }
                                    return price;
                                }),
                                $('<div>').attr({'class': 'info-orther'}).append(
                                    [
                                        $('<p>').html('Item Code: ' + product.code),
                                        $('<p>').attr('class', 'availability').html('<span>In stock</span>')
                                    ])
                            ])
                    ])
            );
    },
    loadFilters: function (filters) {		
	
        var _this = $(this);
        _this.empty();
        _this.addFilter = function (e) {
            var filter = '';
            switch (e.type)
            {
                case Constants.FILTER_TYPE.CHECKBOX:
                    filter = $('<div>').attr({class: 'sidebar-categorie mb-40'}).append(
						    $('<ul>').attr({class: 'sidbar-style'}).append(function () {
                        var options = [];		
                        $.each(e.options, function (k, option) {						
                            options.push(
								$('<li>').attr({class:'form-check'})
								.append(
									[
										$('<input>').attr({class:'form-check-input',type: 'checkbox', name: 'f[' + e.name + '][' + option.id + ']', checked: option.checked, disabled: option.disabled, id: 'filter_' + e.name + '_' + option.id, value: option.id}),
										$('<label>').attr({class:'form-check-label', for : 'filter_' + e.name + '_' + option.id}).append([
											$('<span>').attr({class: 'button'}),
											option.name
										])
									])
							);
                        });					
                        return options;
                    }));
                    break;
                case Constants.FILTER_TYPE.RANGE:
                    filter = $('<div>').attr({class: 'layered-content'}).append([
                        $('<div>', {class: 'input-group'}).append([
						    $('<span>', {class: 'input-group-addon'}).html(e.options.currency_symbol),
                            $('<input>', {type: 'number', class: 'form-control', name: 'f[price][min]', id: 'price-range-min', min: e.options.min, max: e.options.max, step: e.options.step, value: e.options.value_min}),
                            $('<span>', {class: 'input-group-addon'}).html('-'),
                            $('<input>', {type: 'number', class: 'form-control', name: 'f[price][max]', id: 'price-range-max', min: e.options.min, max: e.options.max, step: e.options.step, value: e.options.value_max}),
                        ])
                    ]);
                    break;
                case Constants.FILTER_TYPE.COLOR:
                    filter = $('<div>').attr({class: 'layered-content'})
                            .append(
                                    $('<ul>').attr({class: 'check-box-list filter-color'}).append(function () {
                                var options = [];
                                $.each(e.options, function (k, option) {
                                    options.push(
                                            $('<li>')
                                            .append(
                                                    [
                                                        $('<input>').attr({type: 'checkbox', name: 'f[' + e.name + '][' + option.id + ']', checked: option.checked, disabled: option.disabled, id: 'filter_' + e.name + '_' + option.id, value: option.id}),
                                                        $('<label>').attr({for : 'filter_' + e.name + '_' + option.id, 'style': 'background:' + option.name + ';'}).append([
                                                            $('<span>').attr({class: 'button'})
                                                        ])
                                                    ])
                                            );
                                });
                                return options;
                            }));
                    break;
            }
            return filter;
        };
        if (filters != undefined || filters != null) {
            $.each(filters, function (k, e) {
                _this.append(
                        [
							$('<h3>').attr({class:'sidebar-title'}).text(e.title),
                            _this.addFilter(e)
                        ]);
            });
        }
    }});
	
$(document).ready(function () {
    var p = $('.product-list').loadProducts(false, true);

    //var p = $('.product-list').loadFilters();
    /* $.ajax({
        url: window.location.API.BASE + 'get-page-data',
        data: {page: 'browse-products'},
        success: function (data) {
            window.TSP.data = data;
            $('#cart-block').updateCart(window.TSP.data.my_cart);
            $(document.body).loadMenus({menus: window.TSP.data.menus});
            if ($('#trademark-list').length) {
                $('#trademark-list').loadPayments({payments: window.TSP.data.payment_types});
            }
            $('#searchCategory').loadSelect({
                url: window.location.API.CUSTOMER + 'main-categories',
                key: 'category_id',
                value: 'category',
                optionData: [{key: 'url', value: 'url'}],
                firstOptionSelectable: true,
                firstOption: {key: '', value: 'All Categories'},
                selected: '',
                values: window.TSP.data.main_categories
            });
        }
    }); */	
	
	/* Add to Cart */
	$('#view-product-list').on('click','.add-to-cart-btn',function (e) {
        e.preventDefault();
        CURELE = $(this);
        var code = 	CURELE.attr('data-id');	
		var product = PRODUCT_LIST[code];
        $.ajax({
            type: 'POST',
            url: CURELE.attr('href'),
            data: {supplier_product_code: product.code,product_qty: '1',select_size:''},
            success: function (data) {	
				my_cart_list();
				notif({msg: data.msg, type: 'success', position: 'right'}); 
            }
        });
    });
	
	/* Add and Renove to wishlist  */
	$('#view-product-list').on('click','.add-to-wish-list',function (e) {
        e.preventDefault();	
        CURELE = $(this);		
        var code = 	CURELE.attr('data-id');	  	
		var url = (CURELE.hasClass('bg-pink')) ? 'product/remove-to-wishlist/' : 'product/add-to-wishlist/';	
		var rowId = (CURELE.attr('data-row_id'))? CURELE.attr('data-row_id'):'';			
		var category = (CURELE.attr('data-category'))? CURELE.attr('data-category'):'';			
		var product = (CURELE.attr('data-product'))? CURELE.attr('data-product'):'';	
        $.ajax({
            type: 'POST',          
            url: url+code,        
            data: {id:code,category:category,product:product},        
            success: function (op) {							
				if(CURELE.hasClass('bg-pink')){				
					CURELE.removeClass('bg-pink').addClass('bg-gray');				
				}else{					
					$.each( op.wishlist, function( key, value ) {				    
						CURELE.attr('data-row_id',key);				  
					});	
					CURELE.removeClass('bg-gray').addClass('bg-pink');					
				}			
            }
        });
    });
	
	/* $('#view-product-list').on('click','.add-to-wish-list',function (e) {
        e.preventDefault();	
        CURELE = $(this);		
        var code = 	CURELE.attr('data-id');	
        alert(code);
        $.ajax({
            type: 'POST',
            //url: CURELE.attr('href'),
            url: 'product/remove-to-wishlist/'+code,
           // data: {product_code:''},
            success: function (op) {
				alert(op.msg);
				CURELE.css({'background-color':'rgba(0,0,0,0.4)'});
				
            }
        });
    }); */
    $(window).scroll(function () {
       var el = document.querySelector('div');
        var st = $(this).scrollTop();
        var lastScrollTop = 0;
        if ($(window).scrollTop() >= ($(document).height() - $(window).height()) * 0.7) {

            if ((move_nxt == true) && (st > lastScrollTop)) {
                if (preventCall == false) {
                    preventCall = true;
                    page = parseInt(page_current) + 1;
                    if(parseInt(page)<= parseInt(page_total)){
                        // $('.product-list').loadProducts('',page);
                        $('.product-list').appendProducts(true,true,page);
                    }
                }
            }
        }
    });
});
var st = $(this).scrollTop();
var lastScrollTop = 0;
if ($(window).scrollTop() >= ($(document).height() - $(window).height()) * 0.7) {
    if ((move_nxt == true) && (st > lastScrollTop)) {
        if (preventCall == false) {
            preventCall = true;
            page = parseInt(page) + 1;
            $('#dealLists').loadDeals(page);
        }
    }
    }