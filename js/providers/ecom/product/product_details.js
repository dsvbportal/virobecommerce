$(document).ready(function () {
    setup_main_images();
    $(document).on('click', '.sliding_img', function (e) {
        e.preventDefault();
        var img = $(this).attr("data-image");
    //    var img_zoom = $(this).attr("data-zoom-image-d");
        $('#product-img').attr('src',img);
      //  $('#product-img').attr('data-zoom-image',img_zoom);
        $('.main_img').attr('href',img);

    });

    function setup_main_images(){
        var img = $(".sliding_img").first().attr("data-image");
        //var img_zoom = $(".img_main_anchor").first().data("zoom-image-d");
        $('#product-img').attr('src',img);
        //  $('#product-img').attr('data-zoom-image',img_zoom);
        $('.main_img').attr('href',img);

    }

    $(document).on('change', '#option-product-qty', function (e) {
        e.preventDefault();
        stock_control();
    })

    $('#option-product-qty').on('keyup', function (e) {
        e.preventDefault();
        stock_control();
    });
    $(document).on('click', '.product_colour_select', function (e) {
        e.preventDefault();
        $(".product_colour_select").removeClass("active");
        $(this).addClass( "active" );
        var pc_id=$(this).attr('data-content');
        if(pc_id){
            $('#product_colour_id').val(pc_id);
        } else {
            $('#product_colour_id').val('');
        }
        var pc_id1=$(this).attr('data-content-val');
        if(pc_id1){
            $('#product_colour').val(pc_id1);
        } else {
            $('#product_colour').val('');
        }
    });
    $(document).on('change', '#select_size', function () {
        var sz_id=$(this).find(':selected').attr('data-content')
        if(sz_id){
            $('#select_size_id').val(sz_id);
        } else {
            $('#select_size_id').val('');
        }
    });

    function stock_control(){

        var max_value = $('#option-product-qty').attr('data-value');
        var current_value = $('#option-product-qty').val();
        if(parseInt(current_value) > parseInt(max_value)){
            $('.button-group').hide();
        } else {
            $('.button-group').show();
        }

    }
    var add_cart_form = $('#add_cart_form');

    $(document).on('click','#btn_add_cart', function (e) {
        e.preventDefault();
        add_cart_form.trigger('submit');
    });
    add_cart_form.on('submit', function (e) {
           //console.log(add_cart_form.attr('action'));
        e.preventDefault();
        CURFORM=add_cart_form;
        $.ajax({
            type: 'POST',
            url: add_cart_form.attr('action'),
            data: add_cart_form.serialize(),
            success: function (data) {
                console.log(data);
               // my_cart_list();
            }
        });
    });


    $(document).on('click', '.wishlist', function (e) {

        e.preventDefault();
        CURFORM=add_cart_form;
        wishlist=$(this);
        console.log(wishlist);
        var product=wishlist.attr('product');
        var category=wishlist.attr('category');
        var id=wishlist.attr('product_id');         
        $.ajax({
            url:wishlist.attr('href'),
            data:{'product':product,'id':id,'category':category},
            dataType:"json",
            type:'post',
            beforeSend: function(){
                    $('.alert').hide();                                                   
                    },
            success: function (op) {
               // $("i").removeClass("fa fa-heart-o");
               // $("i").addClass('fa fa-heart');
                wishlist.find('i').removeClass('fa fa-heart-o');
                wishlist.find('i').addClass('fa fa-heart');
                wishlist.removeClass('wishlist');
                wishlist.removeAttr('href');
                wishlist.find('#wishlist_txt').remove();                            
            }
        });

    });


    $(document).on('click', '.del_wishlist', function (e) {
        e.preventDefault();
        delBtn = $(this);
        // alert($('#del_wishlist').attr('href'));
        var row_id= delBtn.attr('row_id');

        $.ajax({
            url:delBtn.attr('href'),
            data:{'row_id': row_id},
            dataType:"json",
            type:'post',
            success: function (op) {
                if(op['status']==200){
                    delBtn.closest('tr').remove();
                    if($('.table-wishlist tbody tr').length == 0){

                        $('.table-wishlist').remove();
                        $('#msg_append').append('<div class="alert alert-danger" role="alert">Wishlist Is Empty!</div>');
                    }}

            }
        });


    });

    $('.rowss').find('.addcart_fromwishlist').on('click', function (e) {
        e.preventDefault();
        CURELE = $(this);
        var code = 	CURELE.attr('data-id');
        var row_id = 	CURELE.attr('row_id');
        $.ajax({
            type: 'POST',
            url: CURELE.attr('href'),
            data: {supplier_product_code:code,product_qty: '1',select_size:''},
            success: function (data) {
                $.ajax({
                    url:CURELE.attr('row_delete'),
                    data:{'row_id': row_id},
                    dataType:"json",
                    type:'post',
                    success: function (op) {
                        if(op['status']==200){
                            CURELE.closest('tr').remove()
                            notif({msg: data.msg, type: 'success', position: 'right'});
                            if($('.table-wishlist tbody tr').length == 0){
                                $('.table-wishlist').remove();
                                $('.msg_empty').append('<div class="alert alert-danger" role="alert">Wishlist Is Empty!</div>');
                            }}
                    }
                });
            }
        });
    });


    function zoom_image_click() {

        $('.zoomContainer').remove();
        $('#elevate-zoomr').removeData('elevateZoom');

        jQuery("#product-zoom").elevateZoom({
            gallery: 'more-vies',
            lensSize: 200,
            cursor: 'pointer',
            galleryActiveClass: 'active',
            imageCrossfade: true,
            scrollZoom: true,
            responsive: true
        });
    }
});
