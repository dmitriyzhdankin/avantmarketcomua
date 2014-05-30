$(function () {

    // add to cart block: services
    $(".services input:checkbox").click(function () {
        var obj = $('select[name="service_variant[' + $(this).closest('tr').data('id') + '][' + $(this).val() + ']"]');
        if (obj.length) {
            if ($(this).is(':checked')) {
                obj.removeAttr('disabled');
            } else {
                obj.attr('disabled', 'disabled');
            }
        }
    });


    $(".cart a.delete").click(function () {
        var tr = $(this).closest('tr');
        $.post('delete/', {id: tr.data('id')}, function (response) {
            tr.remove();
            $(".cart-total").html(response.data.total);
        }, "json");
    });

    $(".cart input.qty").change(function () {
        if ($(this).val() > 0) {
            var tr = $(this).closest('tr');
            if ($(this).val()) {
                $.post('save/', {id: tr.data('id'), quantity: $(this).val()}, function (response) {
                    tr.find('.item-total').html(response.data.item_total);
                    $(".cart-total").html(response.data.total);
                }, "json");
            }
        } else {
            $(this).val(1);
        }
    });

    $(".cart .services input:checkbox").change(function () {
        var div = $(this).closest('div');
        if ($(this).is(':checked')) {
           var parent_id = $(this).closest('tr').data('id')
           var data = {parent_id: parent_id, service_id: $(this).val()};
           var variants = $('select[name="service_variant[' + parent_id + '][' + $(this).val() + ']"]');
           if (variants.length) {
               data['service_variant_id'] = variants.val();
           }
           $.post('add/', data, function(response) {
               div.data('id', response.data.id);
               $(".cart-total").html(response.data.total);
           }, "json");
        } else {
           $.post('delete/', {id: div.data('id')}, function (response) {
               div.data('id', null);
               $(".cart-total").html(response.data.total);
           }, "json");
        }
    });

    $(".cart .services select").change(function () {
        $.post('save/', {id: $(this).closest('div').data('id'), 'service_variant_id': $(this).val()}, function (response) {
            $(".cart-total").html(response.data.total);
        }, "json");
    });
});