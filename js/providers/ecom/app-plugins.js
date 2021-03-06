$.fn.extend({	
    loadMenus: function (data) {
        var _this = $(this);
        _this.menus = {};
        _this = $.extend({}, _this, data);
        _this.loadData = function () {			
            if (_this.menus == {}) {
                $.ajax({
                    //url: window.location.API.CUSTOMER + 'get-menus',
                    url: window.location.BASE+'/get-menus',
                    success: function (op) {
                        _this.menus = op.menus.menu;
                        _this.print();
                    }
                });
            }
            else {
                _this.print();
            }
        };
        _this.print = function () {					
            //_this.find('ul#header_catalogue').empty().append(_this.addHeaderCatalogueMenu(_this.menus.header_catalogue));
            _this.find('#header_primary').empty().append(_this.addHeaderHeaderPrimaryMenu(_this.menus.header_primary));
            _this.find('div#footer_primary').empty().append(_this.addFooterPrimaryMenu(_this.menus.footer_primary));
            _this.find('div#footer_account').empty().append(_this.addFooterAccountMenu(_this.menus.footer_account));
            _this.find('div#footer_support').empty().append(_this.addFooterSupportMenu(_this.menus.footer_support));
            //_this.find('div#footer_catalogue').empty().append(_this.addFooterCatalogue(_this.menus.footer_catalogue));
           // _this.find('div#footer_secondry').empty().append(_this.addFooterSecondryMenu(_this.menus.footer_secondry));
            $('ul#header_primary>.dropdown>.dropdown-toggle').dropdown();
        };
        _this.addHeaderHeaderPrimaryMenu = function (menus) {
            var m = [];
            if (menus !== undefined && menus.length) {
                $.each(menus, function (k, menu) {
                    if (menu.group !== undefined) {
						 m.push($('<li>').append([
							$('<a>',{'href':menu.url}).append(
							$('<span>').append($('<img>',{src:menu.images})),menu.title,$('<i>', {class: 'fa fa-angle-right'})),
							$('<ul>',{class:'ht-dropdown megamenu megamenu-two'}).append(
							function () {
									var arr = [];
									$.each(menu.group, function (kg, g) {
											arr.push($('<li>',{class:'single-megamenu'}).append($('<ul>').append($('<li>',{class:'menu-tile','text':g.title}).append(function(){
													var gr = [];
													if (g.links !== undefined) {
														$.each(g.links, function (kl, l) {
															gr.push($('<li>').append($('<a>', {href: l.url}).append(l.title)));
														});
													}
													return gr;
												})
											)));
									})
									return arr;
								}
							)
						 ]));
						 
						 
						 
						 
                        /* m.push($('<li>', {class: 'dropdown'}).append([
                            $('<a>', {class: 'dropdown-toggle', href: menu.url}).text(menu.title),
                            $('<ul>', {class: 'dropdown-menu mega_dropdown', role: 'menu', style: 'width: auto;'}).append(
								function () {
									var arr = [];
									$.each(menu.group, function (kg, g) {
										arr.push($('<li>', {class: 'block-container col-sm-3'}).append(
												$('<ul>', {class: 'block'}).append(function () {
											var gr = [];
											gr.push($('<li>', {class: 'link_container group_header'}).append($('<a>', {class: 'block', href: g.url}).text(g.title)));
											if (g.links !== undefined) {
												$.each(g.links, function (kl, l) {
													gr.push($('<li>', {class: 'link_container'}).append($('<a>', {href: l.url}).append(l.title)));
												});
											}
										
											return gr;
										})));
									});
									return arr;
								})
                        ])); */
                    }
                    else {
/*
                    <i class="fa fa-angle-right" aria-hidden="true"></i>
*/
                        m.push($('<li>').append($('<a>', {href: menu.url}).append($('<span>').append([$('<img>', {src: menu.images})]),menu.title ) ));
                    }
                });
            }
			console.log(m);
            return m;
        };
        _this.addHeaderCatalogueMenu = function (menus) {
            var m = [];
            if (menus !== undefined && menus.length) {
                $.each(menus, function (k, menu) {
                    if (menu.group !== undefined && menu.group != null) {
                        m.push($('<li>').append([$('<a>', {class: 'parent', href: menu.url}).append([$('<img>', {clas: 'icon-menu hello2'}), menu.title]), $('<div>', {class: 'vertical-dropdown-menu'}).append($('<div>', {class: 'vertical-groups col-sm-12'}).append($('<div>', {class: 'mega-group col-sm-4'}).append(function () {
                                var arr = [];
                                $.each(menu.group, function (kg, g) {
                                    arr.push($('<h4>', {class: 'mega-group-header'}).append($('<span>').append(g.title)));
                                    $.each(menu.group, function (kl, l) {
                                        arr.push($('<ul>', {class: 'group-link-default'}).append($('<li>').append($('<a>', {href: l.url}).append(l.title))));
                                    });
                                });
                                return arr;
                            })))]));
                    }
                    else if (menu.chiled !== undefined && menu.chiled != null) {
                        m.push($('<li>').append($('<a>', {href: menu.url}).append([$('<img>', {class: 'icon-menu hello1'}), menu.category]) , $('<div>', {class: 'vertical-dropdown-menu'}).append($('<div>', {class: 'vertical-groups col-sm-12'}).append($('<div>', {class: 'mega-group col-sm-4'}).append(function () {
                                var arr = [];
                                $.each(menu.chiled, function (kg, g) {
                                    arr.push($('<h4>', {class: 'mega-group-header'}).append($('<span>').append(g.category)));
                                    $.each(g.chiled, function (kl, l) {
                                        arr.push($('<ul>', {class: 'group-link-default'}).append($('<li>').append($('<a>', {href: l.url}).append([$('<img>', {class: 'icon-menu'}), l.category]))));
                                    });
                                });

                                return arr;
                            })))
                        ));
                    }
                    else {                      
                        m.push($('<li>').append($('<a>', {href: menu.url_str}).append([$('<img>', {clas: 'icon-menu'}), menu.category])));
                        //m.push($('<li>').append($('<a>', {href: menu.url}).append([$('<img>', {clas: 'icon-menu'}), menu.title])));
                    }
                });
            }
            return m;
        };
        _this.addFooterPrimaryMenu = function (menus) {
            var m = [];
            if (menus !== undefined && menus.length) {
                $.each(menus, function (k, menu) {
                   
				    m.push($('<div>', {class: 'footer-content col-md-6'}).append($('<h4>', {class: 'footer-title'}).text(menu.title),$('<ul>', {class: 'footer-list'}).append(function () {
                        var ml = [];
                        $.each(menu.normal, function (lk, l) {
                            ml.push($('<li>').append($('<a>', {href: l.url}).append(l.title)));
                        });
                        return ml;
                    })));
                });
            }
            return m;
        };
        _this.addFooterAccountMenu = function (menus) {
            var m = [];
            if (menus !== undefined && menus.length) {
                $.each(menus, function (k, menu) {
                    m.push($('<div>', {class: 'introduce-title'}).text(menu.title));
                    m.push($('<ul>', {class: 'introduce-list'}).append(function () {
                        var ml = [];
                        $.each(menu.normal, function (lk, l) {
                            ml.push($('<li>').append($('<a>', {href: l.url}).append(l.title)));
                        });
                        return ml;
                    }));
                });
            }
            return m;
        };
        _this.addFooterSupportMenu = function (menus) {
            var m = [];
            if (menus !== undefined && menus.length) {
                $.each(menus, function (k, menu) {
                     m.push($('<h3>', {class: 'footer-title'}).text(menu.title));
				    m.push($('<div>', {class: 'footer-content'}).append($('<ul>', {class: 'footer-list'}).append(function () {
                        var ml = [];
                        $.each(menu.normal, function (lk, l) {
                            ml.push($('<li>').append($('<a>', {href: l.url}).append(l.title)));
                        });
                        return ml;
                    })));
                });
            }
            return m;
        };
        _this.addFooterCatalogue = function (menus) {
            var m = [];
            if (menus !== undefined && menus.length) {
                $.each(menus, function (k, menu) {
                    m.push($('<div>', {class: 'col-sm-12'}).text(menu.title));
                    m.push($('<ul>', {class: 'trademark-list'}).append(function () {
                        var ml = [];
                        ml.push($('<li>', {class: 'trademark-text-tit'}).append($('<a>', {href: ml.url}).append([ml.title, ':'])));
                        $.each(menu.normal, function (lk, l) {
                            ml.push($('<li>').append($('<a>', {href: l.url}).append(l.title)));
                        });
                        return ml;
                    }));
                });
            }
            return m;
        };
        _this.addFooterSecondryMenu = function (menus) {
            var m = [];
            if (menus !== undefined && menus.length) {
                m.push($('<div>', {class: 'col-sm-12'}));
                m.push($('<ul>', {class: 'trademark-list'}).append(function () {
                    var ml = [];
                    $.each(menus, function (lk, l) {
                        ml.push($('<li>').append($('<a>', {href: l.url}).append(l.title)));
                    });
                    return ml;
                }));
            }
            return m;
        };
        _this.loadData();
        return _this;
    },
    loadPayments: function (data) {
        var _this = $(this);
        _this.payments = [];
        _this = $.extend({}, _this, data);
        _this.loadData = function () {
            if (_this.payments.length <= 0) {
                $.ajax({
                    url: window.location.API.CUSTOMER + 'get-payment-types',
                    success: function (op) {
                        _this.payments = op.payment_types !== undefined && op.payment_types.length ? op.payment_types : [];
                        _this.print();
                    }
                });
            }
            else {
                _this.print();
            }
        };
        _this.print = function () {
            _this.empty();
            _this.append($('<li>', {id: 'payment-methods'}).text(_this.attr('title')));
            for (var p in _this.payments) {
                _this.append($('<li>').append($('<a>', {hred: '#'}).append($('<img>', {src: _this.payments[p].img, alt: _this.payments[p].title, title: _this.payments[p].title}))));
            }
        };
        _this.loadData();
        return _this;
    },
    addSlider: function (data) {		
        var _this = $(this);
        _this.sliders = [];		
        _this = $.extend(_this, data);
        _this.print = function () {
            _this.find(_this.featuredSlider).empty();
            _this.find(_this.imgSlider).empty();
			
            //console.log(_this.sliders); 

            $.each(_this.sliders, function (k, slider) {
                if (slider.slider_type == Constants.SLIDER_TYPE.FEATURED) {
                    _this.find(_this.featuredSlider).append(_this.printFeaturedSlider(slider));
                }
                else if (slider.slider_type == Constants.SLIDER_TYPE.IMG) {
                    _this.find(_this.imgSlider).append(_this.printImgSlider(slider));
                }
            });
            $(".owl-carousel").each(function (index, el) {
                var config = $(this).data();
                config.navText = ['<i class="fa fa-angle-left"></i>', '<i class="fa fa-angle-right"></i>'];
                config.smartSpeed = "300";
                if ($(this).hasClass('owl-style2')) {
                    config.animateOut = "fadeOut";
                    config.animateIn = "fadeIn";
                }
                $(this).owlCarousel(config);
            });
           /*  $('#contenhomeslider').bxSlider(
                    {
                        nextText: '<i class="fa fa-angle-right"></i>',
                        prevText: '<i class="fa fa-angle-left"></i>',
                        auto: true,
                    }
            ); */
        };
        _this.printFeaturedSlider = function (slider) {			
            return $('<div>').attr({class: 'category-featured'}).append([
                $('<nav>').attr({class: 'navbar nav-menu nav-menu-green show-brand'}).append([
                    $('<div>').attr({class: 'container'}).append([
                        $('<div>').attr({class: 'navbar-brand'}).append([
                            $('<a>').attr({class:''}).append([
                                slider.title
                            ])
                        ])
                    ])
                ]),
                $('<div>').attr({class: 'product-featured clearfix'}).append([
                    /*$('<div>').attr({class: 'banner-featured text-center'}).append([
                     $('<div>').attr({class: 'featured-text'}).append($('<span>').append(slider.title)),
                     $('<div>').attr({class: 'banner-img'}).append($('<a>').attr({}).append($('<img>').attr({src: slider.img_path}))),
                     $('<h2>').append(slider.title)
                     ]),*/
                    $('<div>').attr({class: 'product-featured-content'}).append([
                        $('<div>').attr({class: 'product-featured-list'}).append([
                            $('<ul>').attr({class: 'product-list owl-carousel', 'data-dots': 'false', 'data-loop': 'true', 'data-nav': 'true', 'data-margin': '0', 'data-autoplayTimeout': '1000', 'data-autoplayHoverPause': 'true', 'data-responsive': '{"0":{"items":1},"600":{"items":3},"1000":{"items":5}}'}).append(function () {
                                var blocks = [];
                                $.each(slider.blocks, function (k, block) {
                                    blocks.push(_this.printFeaturedBlock(block));
                                });
                                return blocks;
                            })
                        ])
                    ])
                ])
            ]);
        };
        _this.printFeaturedBlock = function (block) {
            return $('<li>').append([
                $('<a>').attr({href: block.url}).append([
                    $('<div>').attr({class: 'left-block'}).append([
                        $('<a>').attr({class: '', href: block.url}).append($('<img>').attr({src: block.img_path, alt: block.title}))
                    ]),
                    $('<div>').attr({class: 'right-block'}).append([
                        $('<h4>').attr({class: 'product-name text-center'}).append([
                            $('<a>').attr({class: ''}).append(block.subtitle)
                        ]),
                        $('<h3>').attr({class: 'product-name text-center'}).append([
                            $('<a>').attr({class: ''}).append(block.title)
                        ]),
                        $('<h5>').attr({class: 'product-name text-center'}).append([
                            $('<a>').attr({class: ''}).append(block.description)
                        ])
                    ])
                ])
            ]);
        };
        _this.printImgSlider = function (slider) {
            return $('<ul>').attr({id: 'contenhomeslider'}).append(function () {
                var blocks = [];
                $.each(slider.blocks, function (k, block) {
                    blocks.push(_this.printImgBlock(block));
                });
                return blocks;
            });
        };
        _this.printImgBlock = function (block) {			
            return $('<li>').append([
                $('<a>').attr({href: block.url}).append([
                    $('<img>').attr({src: block.img_path, alt: block.title})
                ])
            ]);
        };
        _this.loadSliders = function () {
            if (_this.sliders == undefined || _this.sliders.length <= 0) {
                $.groupAjax({
                    url: window.location.API.CUSTOMER + 'get-sliders',
                    data: {page: _this.page},
                    success: function (data) {
                        _this = $.extend(_this, data);
                        _this.print();
                    }
                });
            }
            else {
                _this.print();
            }
        };
        _this.loadSliders();
    },
    addServices: function (services) {
        var _this = $(this);
        _this.services = services || [];
        _this.print = function () {
            _this.empty().append($('<div>', {class: 'container'}).append(
                    $('<div>', {class: 'service'}).append(function () {
                var s = [];
                for (var i in _this.services) {
                    s.push(_this.addService(_this.services[i]));
                }
                return s;
            })));
        };
        _this.addService = function (service) {
            return $('<div>', {class: 'col-xs-6 col-sm-3 service-item'}).append([
                $('<div>', {class: 'icon'}).append([
                    $('<img>', {src: service.img})
                ]),
                $('<div>', {class: 'info'}).append([
                    $('<a>', {href: service.url}).append($('<h3>').text(service.title)),
                    $('<span>').append(service.desc)
                ])
            ]);
        };
        _this.print();
        return _this;
    },
    updateCart: function (data) {
        var _this = this;
        _this.empty();
        /*_this.cartBlock = $('<div>').attr({class: 'cart-block'}).append(
         [
         $('<div>').attr({class: 'cart-block-content'}).append(
         [
         $('<h5>').attr({class: 'cart-title'}).append(data.cart_count + ' Items in my cart'),
         $('<div>').attr({class: 'cart-block-list'}).append(function () {
         var ul = $('<ul>');
         for (var i in data.cart_content) {
         ul.append(
         $('<li>').attr({class: 'product-info'}).append(
         [
         $('<div>').attr({class: 'p-left'}).append([
         $('<a>').attr({class: 'remove_link'}),
         $('<a>').attr({'href': 'remove_link'}).append($('<img>').attr({class: 'img-responsive', 'src': data.cart_content[i].options.imgs[0].img_path})),
         ]),
         $('<div>').attr({class: 'p-right'}).append([
         $('<p>').attr({class: 'p-name'}).append(data.cart_content[i].name),
         $('<p>').attr({class: 'p-rice'}).append('Price: ' + data.cart_content[i].price),
         $('<p>').append('Qty: ' + data.cart_content[i].qty),
         $('<p>').attr({class: 'p-rice'}).append('Sub Total: ' + data.cart_content[i].subtotal)
         ])
         ])
         );
         }
         return ul;
         }),
         $('<div>').attr({class: 'toal-cart'}).append(
         [
         $('<div>', {class: 'row'}).append([
         $('<span>', {class: 'col-xs-6'}).append('Sub Total'),
         $('<span>', {class: 'col-xs-6 toal-price text-right'}).append(data.cart_price_sub_total),
         ]),
         $('<div>', {class: 'row'}).append([
         $('<span>', {class: 'col-xs-6'}).append('Tax'),
         $('<span>', {class: 'col-xs-6 toal-price text-right'}).append(data.cart_tax),
         ]),
         $('<div>', {class: 'row'}).append([
         $('<span>', {class: 'col-xs-6'}).append('Shipping Charge'),
         $('<span>', {class: 'col-xs-6 toal-price text-right'}).append(data.cart_shipping_charge),
         ]),
         $('<div>', {class: 'row'}).append([
         $('<span>', {class: 'col-xs-6'}).append('Total'),
         $('<span>', {class: 'col-xs-6 toal-price text-right'}).append(data.cart_total),
         ]),
         ]),
         $('<div>').attr({class: 'cart-buttons'}).append($('<a>').attr({class: 'btn-check-out', href: window.location.BASE + 'checkout'}).append('Checkout'))
         ])
         ]);*/
        _this.append(
                [
                    $('<a>').attr({class: 'cart-link', href: window.location.BASE + 'my-cart'}).append(
                            [
                                /*                                $('<span>').attr({class: 'title'}).append('Shopping cart'),
                                 $('<span>').attr({class: 'total'}).append(data.cart_count + ' items - ' + data.cart_total),*/
                                $('<span>').attr({class: 'notify'}).append(data.cart_count)
                            ]),
                            /*_this.cartBlock*/
                ]
                );
        /*$('.shopping-cart-box-ontop-content').append(_this.cartBlock);*/
        if ($('#step-summary').length) {
            $('#step-summary').updateCartSummary(data);
        }
    },
    updateCartSummary: function (data) {
        var _this = this;
        _this.empty();
        _this.append(
                [
                    $('<table>').attr({class: 'table table-bordered table-responsive cart_summary'}).append([
                        $('<thead>').append($('<tr>').append([
                            $('<th>').attr({class: 'cart_product'}).append('Product'),
                            $('<th>').append('Description'),
                            $('<th>').append('Avail.'),
                            $('<th>').append('Unit price'),
                            $('<th>').append('Qty'),
                            $('<th>').append('Total')
                        ])),
                        $('<tbody>').append(function () {
                            var trs = [];
                            for (var i in data.cart_content) {
                                trs.push($('<tr>').append(
                                        [
                                            $('<td>').attr({class: 'cart_product'}).append([
                                                $('<a>').attr({'href': 'remove_link'}).append($('<img>').attr({class: 'img-responsive', alt: 'Product', 'src': data.cart_content[i].options.imgs[0].img_path})),
                                            ]),
                                            $('<td>').attr({class: 'cart_description'}).append([
                                                $('<p>').attr({class: 'product-name'}).append(data.cart_content[i].name),
                                                $('<small>').attr({class: 'cart_ref'}).append(),
                                                $('<p>').append('Qty: ' + data.cart_content[i].qty),
                                                $('<a>').attr({href: '#', class: ' btn btn-danger btn-xs remove-from-cart', 'data-rowid': i}).append('Delete item')
                                            ]),
                                            $('<td>').attr({class: 'cart_avail'}).append([
                                                $('<span>').attr({class: 'label label-success'}).append(data.cart_content[i].options.stock_status),
                                            ]),
                                            $('<td>').attr({class: 'price'}).append(data.cart_content[i].price),
                                            $('<td>').attr({class: 'qty'}).append([
                                                $('<input>').attr({class: 'form-control input-sm change-cart-item-qty', type: 'number', value: data.cart_content[i].qty, 'data-rowid': i}).append(),
                                                $('<a>').attr({href: '#', class: 'increase-qty'}).append($('<i>').attr({class: 'fa fa-caret-up'})),
                                                $('<a>').attr({href: '#', class: 'decrease-qty'}).append($('<i>').attr({class: 'fa fa-caret-down'})),
                                            ]),
                                            $('<td>').attr({class: 'price text-right'}).append(data.cart_content[i].subtotal),
                                        ]));
                            }
                            return trs;
                        }),
                        $('<tfoot>').append([
                            $('<tr>').append([
                                $('<td>').attr({colspan: 3, rowspan: 4}),
                                $('<td>', {colspan: 2}).append('Total'),
                                $('<td>').append(data.cart_price_sub_total),
                            ]),
                            $('<tr>').append([
                                $('<td>', {colspan: 2}).append('Tax'),
                                $('<td>').append(data.cart_tax),
                            ]),
                            $('<tr>').append([
                                $('<td>', {colspan: 2}).append('Shipping Charges'),
                                $('<td>').append(data.cart_shipping_charge),
                            ]),
                            $('<tr>').append([
                                $('<td>').append($('<strong>').text('Total')),
                                $('<td>', {class: 'text-right'}).append($('<strong>').text(data.cart_count)),
                                $('<td>').append($('<strong>').text(data.cart_total)),
                            ]),
                        ]),
                    ]),
                    $('<div>').attr({class: 'cart_navigation'}).append([
                        $('<a>').attr({class: 'prev-btn'}).html('Continue shopping'),
                        $('<a>').attr({class: 'next-btn', href: window.location.BASE + 'checkout' != document.location.href ? window.location.BASE + 'checkout' : '#', disabled: (data.cart_count > 0) ? false : true}).html('Proceed to Checkout')
                    ])
                ]);
    },
    loadOrderItems: function (sub_order_code) {
        var _this = this;
        _this.empty();
        $.ajax({
            url: window.location.API.CUSTOMER + 'my-orders-details',
            data: {sub_order_code: sub_order_code},
            success: function (data) {
                $.each(data.order_details, function (k, item) {
                    $('#' + k).html(item);
                });
                if (data.order_details.cancel_order_url != undefined && data.order_details.cancel_order_url) {
                    $('.cancel-order').attr('href', data.order_details.cancel_order_url);
                }
                else {
                    $('.cancel-order').remove();
                }
                if (data.order_details.return_order_url != undefined && data.order_details.return_order_url != '' && data.order_details.return_order_url)
                {
                    $('.return-order').attr('href', data.order_details.return_order_url);
                }
                else {
                    $('.return-order').remove();
                }
                $.each(data.data, function (k, item) {
                    _this.append(
                            $('<li>').attr({class: 'row'}).append(
                            [
                                $('<div>').attr({class: 'col-sm-1 text-center'}).append([
                                    $('<img>').attr({class: 'img img-responsive', src: item.imgs[0].img_path})
                                ]),
                                $('<div>').attr({class: 'col-sm-4'}).append([
                                    $('<h2>').append(item.product_name),
                                    $('<p>').append('Brand: ' + item.brand_name),
                                    $('<p>').append('Price: ' + item.price + ', Qty: ' + item.qty)
                                ]),
                                $('<div>').attr({class: 'col-sm-3'}).append([
                                    item.statusInfo,
                                    $('<strong>').append(item.status),
                                    $('<p>').append(item.status_msg),
                                    (item.cancel_url != undefined && item.cancel_url) ? $('<a>').attr({class: ' btn btn-xs btn-danger cancel-order-item', href: item.cancel_url, title: 'Cancel'}).append(['Cancel']) : '',
                                    (item.return_url != undefined && item.return_url != '' && item.return_url) ? $('<a>').attr({class: ' btn btn-xs btn-warning return-order-item', href: item.return_url, title: 'Return'}).append(['Return']) : ''
                                ]),
                                $('<div>').attr({class: 'col-sm-2 text-center'}).append([
                                    $('<p>').append(item.delivery_date),
                                ]),
                                $('<div>').attr({class: 'col-sm-2 text-right'}).append([
                                    $('<p>').append(item.sub_total)
                                ])
                            ])
                            );
                });
            }
        });
    },
    initPayment: function (data) {
        var _this = this;
        _this.modes = {};
        _this.getPaymentModes = function () {
            $.ajax({
                url: window.location.API.CUSTOMER + 'get-paymodes',
                success: function (data) {
                    _this.modes = data.payment_modes;
                    _this.UpdatePaymentMode();
                }
            });
        };
        _this.UpdatePaymentMode = function () {
            _this.empty();
            _this.append(function () {
                var modes = [];
                if (_this.modes.length > 0) {
                    for (var i in  _this.modes) {
                        modes.push(
                                $('<div>').attr({class: 'radio'})
                                .append(
                                        $('<label>')
                                        .append([
                                            $('<input>').attr({type: 'radio', name: 'paymode_id', value: _this.modes[i].id}),
                                            _this.modes[i].name
                                        ])
                                        )
                                );
                    }
                }
                else {
                    _this.append($('<p>').attr({class: 'text-danger'}).text('No Payment Modes to Proceed'));
                }
                return modes;
            });
        };
        if (_this.length) {
            _this.getPaymentModes();
            return _this;
        }
        else {
            return false;
        }
    },
/* 	ecomUrl: function (apiUrl = '',addUrl = '') {		
		var curUrl = window.location.BASE;	
		apiUrl = (apiUrl != null) ? apiUrl:'';
		if(apiUrl.indexOf('?') != -1){
			 curUrl = curUrl + addUrl + apiUrl.substring(apiUrl.indexOf('product/'));
             		
		}
		else if(apiUrl.indexOf('pay_gyft') != -1){
			curUrl = apiUrl.replace('http://localhost/pay_gyft/',curUrl);		   
		}
		else{			
			if(addUrl != null && addUrl != ''){
				curUrl = curUrl + addUrl +'/'+ apiUrl;
			}else{
				curUrl = curUrl + apiUrl;
			}            
		} 
		return curUrl;
    } */
});
