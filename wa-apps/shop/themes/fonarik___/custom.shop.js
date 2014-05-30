$(document).ready(function () {

  
    $("form.addtocart").submit(function () {
        var f = $(this);
        $.post(f.attr('action'), f.serialize(), function (response) {
            if (response.status == 'ok') {
                var cart_total = $(".cart-total");
				
                f.children('input[type=submit]').val('Добавлено');
				cart_total.html(response.data.total);	
               
            }
        }, "json");
        return false;
    });

});
