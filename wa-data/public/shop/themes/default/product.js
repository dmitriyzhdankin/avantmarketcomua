$(function () {
    // sku select
    $("#product-skus input:radio").click(function () {
        $(".add2cart .price").html($(this).data('price'));
        if ($(this).data('image-id')) {
            $("#product-image-" + $(this).data('image-id')).click();
        }
    });

    // product images
    $("#product-gallery a").click(function () {
        var img = $(this).find('img');
        var src = img.attr('src').replace(/96x96/, '750x0');
        $('<img src="' + src + '">').load(function () {
            $("#product-image").attr('src', src);
        });
        return false;
    });

    // add to cart block: services
    $(".cart .services input:checkbox").click(function () {
        var obj = $('select[name="service_variant[' + $(this).val() + ']"]');
        if (obj.length) {
            if ($(this).is(':checked')) {
                obj.removeAttr('disabled');
            } else {
                obj.attr('disabled', 'disabled');
            }
        }
    });

    // compare block
    $("a.compare-add").click(function () {
        var compare = $.cookie('shop_compare');
        if (compare) {
            compare += ',' + $(this).data('product');
        } else {
            compare = '' + $(this).data('product');
        }
        if (compare.split(',').length > 1) {
            var url = $("#compare-link").attr('href').replace(/compare\/.*$/, 'compare/' + compare + '/');
            $("#compare-link").attr('href', url).show().find('span.count').html(compare.split(',').length);
        }
        $.cookie('shop_compare', compare, { expires: 30, path: '/'});
        $(this).hide();
        $("a.compare-remove").show();
        return false;
    });
    $("a.compare-remove").click(function () {
        var compare = $.cookie('shop_compare');
        if (compare) {
            compare = compare.split(',');
        } else {
            compare = [];
        }
        var i = $.inArray($(this).data('product') + '', compare);
        if (i != -1) {
            compare.splice(i, 1)
        }
        if (compare.length < 2) {
            $("#compare-link").hide();
        }
        if (compare) {
            $.cookie('shop_compare', compare.join(','), { expires: 30, path: '/'});
        } else {
            $.cookie('shop_compare', null);
        }
        $(this).hide();
        $("a.compare-add").show();
        return false;
    });
});