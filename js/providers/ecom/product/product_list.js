var categories;
$(function () {
    var category = $('#filtersForm').data('category');
    var length = 10;
    var page = 1;
    var move_nxt = true;
    var preventCall = false;
    var data = [];

    $.fn.extend({
        loadDeals: function (page, filter) {
            page = page || 0;
            filter = filter || 0;
            var _this = $(this);
            _this.deals = {};
            var filterdata = $('#filtersForm').serializeArray();
            filterdata.push({name: 'page', value: page})
            filterdata.push({name: 'sortby', value: $('#sort_by').val()})
            $.ajax({
                url: document.location.USER + 'store/offers/near-by',
                data: filterdata,
                type: 'post',
                dataType: 'json',
                beforeSend: function () {
                    $('.load-more').css('display', 'block');
                },
                success: function (op) {
                    $('.load-more').css('display', 'none');
                    _this.recordsFiltered = op.recordsFiltered;
                    _this.recordsTotal    = op.recordsTotal;
                    move_nxt              = op.move_next;
                    _this.categories      = op.data.categories;
                    _this.deals           = op.data.offers;
                    if (move_nxt == false) {
                        preventCall = true;
                    } else {
                        preventCall = false;
                    }
                    _this.print();
                }
            });

            _this.stores = [];
            _this.print = function () {
                if (filter == true) {
                    $('.deals').remove();
                }
                $.each(_this.deals, function (k, row) {
                    var bought = '';
                    var deal_rating = '';
                    if (parseInt(row.bought) > 0) {
                        bought = '<i class="ico fa fa-shopping-basket mr-10"></i> ' + row.bought;

                    }
                    if (parseInt(row.rating) > 0) {
                        deal_rating = '<span class="label label-warning">' + row.rating + '</span><i class="fa fa-star"></i>';

                    }
                    $('#dealLists').append($('<a>', {class: 'column deals', 'href': row.url,'target':'_blank'}).append($('<div>', {class: 'deal-single panel'}).append([
                        $('<figutre>', {class: 'deal-thumbnail embed-responsive embed-responsive-16by9', 'data-bd-img': row.image, style: 'background-image: url("' + row.image + '");'}).append([
                            $('<div>', {class: 'label-discount left-20 top-15'}).text(row.offer),
                            $('<ul>', {class: 'deal-actions top-15 right-20'}).append([
                                $('<li>').append(deal_rating)
                            ]),
                            $('<div>', {class: 'deal-store-logo'}).append($('<img>', {src: row.merchant_logo, alt: row.business_name, title: row.business_name}))
                        ]),
                        $('<div>', {class: 'content'}).append([
                            $('<div>', {class: 'pr-md-10'}).append([
                                /* $('<div>', {class: 'rating mb-10'}).append([
                                 $('<span>', {class: 'rating-stars rate-allow'}).append(function () {
                                 var stars = [];
                                 for (var i = 1; i <= parseInt(row.rating); i ++) {
                                 stars.push($('<i>').attr('class', 'fa fa-star'));
                                 }
                                 for (var i = 1; i <= 5 - parseInt(row.rating); i ++) {
                                 stars.push($('<i>').attr('class', 'fa fa-star-o'));
                                 }
                                 return stars;
                                 }),
                                 $('<span>', {class: 'rating-reviews'}).append('( ', $('<span>', {class: 'rating-count'}).text(row.rating_count), ' )')
                                 ]), */
                                $('<a>', {href: row.url}).append($('<h3>', {class: 'deal-title mb-10'}).text(row.title)),
                                $('<strike>', {class: 'text-muted'}).append(row.old_price),
                                $('<span>', {class: 'price'}).html('&nbsp;&nbsp;<strong>' + row.new_price),
                                $('<span>', {class: 'pull-right'}).html(bought),
                                $('<span>', {class: 'deal_store'}).append($('<span>', {class: 'text-warning'}), row.store_name + '<br>' + row.formated_address),
                                        /* $('<ul>', {class: 'deal-meta list-inline mb-10 color-mid'}).append([
                                         $('<li>').append([$('<i>', {class: 'ico fa fa-shopping-basket mr-10'}), row.bought])
                                         ]), */
                            ])
                        ])
                    ]))
                            )
                });
                $('#dealfound').html('' + _this.recordsTotal + ' Deals are found ');
            }
        },
        dealFilters: function (filters) {
            var _this = this;
            _this.filters = filters;
            _this.print = function () {
                _this.empty();
                _this.append(function () {
                    var filters = [];
                    $.each(_this.filters, function (k, f) {
                        filters.push(_this.printFilter(f));
                    });
                    return filters;
                });
            };
            _this.printFilter = function (f) {
                return $('<div>', {class: 'widget'}).append([
                    $('<h4>', {class: 'widgettitle'}).text(f.title),
                    $('<ul>', {class: 'list-grouped'}).append(function () {
                        var options = [];
                        $.each(f.values, function (k, v) {
                            switch (f.type) {
                                case 'radio':
                                    options.push($('<li>', {class: (f.ui_type == 'list' ? 'radio' : '')}).append($('<label>', {class: (f.ui_type == 'inline' || f.ui_type == 'inline-icon' ? 'radio-inline' : '')}).append([$('<input>', {type: 'radio', class: 'filter-deals', name: f.filter_name, value: v.value}), v.title])));
                                    break;
                                case 'check':
                                    options.push($('<li>', {class: (f.ui_type == 'list' ? 'checkbox' : '')}).append($('<label>', {class: (f.ui_type == 'inline' || f.ui_type == 'inline-icon' ? 'check-inline' : '')}).append([$('<input>', {type: 'checkbox', class: 'filter-deals', name: f.filter_name, value: v.value}), v.title])));
                                    break;
                            }
                        })
                        return options;
                    })
                ]);
            };
            _this.print();
        },
    });

    $('#dealLists').loadDeals();
    $.ajax({
        url: document.location.USER + 'store/deals/filters' + (category != '' ? '/' + category : ''),
        success: function (op) {
            $('#filtersForm').dealFilters(op.filters);
        }
    });

    $('#filtersForm').on('change', '.filter-deals', function () {
        preventCall = false;
        $('#dealLists').loadDeals(false, true);
    });

    $('#sort_by').change(function (e) {
        e.preventDefault();
        $('#dealLists').loadDeals(false, true);
    })

    $('#deals-list').on('click', '.add-to-fav', function (e) {
        e.preventDefault();
        var CurEle = $(this);
        $.ajax({
            url: document.location.USER + 'my-wishlist/offer/add',
            data: {id: CurEle.data('offer_code')},
            success: function (op) {
                CurEle.removeClass('add-to-fav').find('span').addClass('text-warning');
            }
        });
    });

    $(window).scroll(function () {
        var el = document.querySelector('div');
        var st = $(this).scrollTop();
        var lastScrollTop = 0;
        if ($(window).scrollTop() >= ($(document).height() - $(window).height()) * 0.7) {
            if ((move_nxt == true) && (st > lastScrollTop)) {
                if (preventCall == false) {
                    preventCall = true;
                    page = parseInt(page) + 1;
                    $('#dealLists').loadDeals(page);
                }
            } else {
                $('.load-more').css('display', 'none');
            }
            lastScrollTop = st;
        }
    });

    $('#sort_by').change(function (e) {
        e.preventDefault();

    })
});
