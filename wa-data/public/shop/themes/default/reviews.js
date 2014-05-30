$(function() {
    /**
     * Hotkey combinations
     * {Object}
     */
    var hotkeys = {
        'alt+enter': {
            ctrl:false, alt:true, shift:false, key:13
        },
        'ctrl+enter': {
            ctrl:true, alt:false, shift:false, key:13
        },
        'ctrl+s': {
            ctrl:true, alt:false, shift:false, key:17
        }
    };

    var form_wrapper = $('#product-reivew-form');
    var form = form_wrapper.find('form');
    var content = $('.content .reviews');

    var input_rate = form.find('input[name=rate]');
    if (input_rate.length < 0) {
        input_rate = $('<input name="rate" type="hidden" value=0>').appendTo(form);
    }
    form.find('a.rate').rateWidget({
        onUpdate: function(rate) {
            input_rate.val(rate);
        }
    });

    content.off('click', '.review-reply, .write-review a').on('click', '.review-reply, .write-review a', function() {
        var self = $(this);
        var item = self.parents('li:first');
        var parent_id = parseInt(item.attr('data-id'), 10) || 0;
        prepareAddingForm.call(self, form, parent_id);
        return false;
    });

    addHotkeyHandler('textarea', 'ctrl+enter', addReview);
    form.submit(function() {
        addReview();
        return false;
    });

    function addReview() {
        $.post(
            location.href.replace(/\/#\/[^#]*|\/#|\/$/g, '') + '/add/',
            form.serialize(),
            function (r) {
                if (r.status == 'fail') {
                    clear(form, false);
                    showErrors(form, r.errors);
                    return;
                }
                if (r.status != 'ok' || !r.data.html) {
                    if (console) {
                        console.error('Error occured.');
                    }
                    return;
                }
                var html = r.data.html;
                var parent_id = parseInt(r.data.parent_id, 10) || 0;
                var parent_item = parent_id ? form.parents('li:first') : content;
                var ul = $('ul:first', parent_item);
                if (parent_id) {
                    ul.show().append(html);
                } else {
                    ul.show().prepend(html);
                }
                $('.review-count').text(r.data.review_count_str);
                clear(form, true);
                content.find('.write-review a').click();
                if (typeof success === 'function') {
                    success(r);
                }
            },
        'json')
        .error(function(r) {
            if (console) {
                console.error(r.responseText ? 'Error occured: ' + r.responseText : 'Error occured.');
            }
        });
    };

    function showErrors(form, errors) {
        for (var i = 0, n = errors.length, errs = errors[i]; i < n; errs = errors[++i]) {
            for (var name in errs) {
                $('[name='+name+']', form).after($('<em class="errormsg"></em>').
                    text(errs[name])).addClass('error');
            }
        }
    };

    function clear(form, clear_inputs) {
        clear_inputs = typeof clear_inputs === 'undefined' ? true : clear_inputs;
        $('.errormsg', form).remove();
        $('.error',    form).removeClass('error');
        $('.wa-captcha-refresh', form).click();
        if (clear_inputs) {
            $('input[name=captcha], textarea', form).val('');
            $('input[name=rate]', form).val(0);
            $('input[name=title]', form).val('');
            $('.rate', form).trigger('clear');
        }
    };

    function prepareAddingForm(form, review_id)
    {
        var self = this; // clicked link
        if (review_id) {
            self.parents('.actions:first').after(form_wrapper);
            $('.rate ', form).trigger('clear').parents('.review-field:first').hide();
        } else {
            self.parents('.write-review').after(form_wrapper);
            form.find('.rate').parents('.review-field:first').show();
        }
        clear(form, false);
        $('input[name=parent_id]', form).val(review_id);
    };

    function addHotkeyHandler(item_selector, hotkey_name, handler) {
        var hotkey = hotkeys[hotkey_name];
        form.off('keydown', item_selector).on('keydown', item_selector,
            function(e) {
                if (e.keyCode == hotkey.key &&
                    e.altKey  == hotkey.alt &&
                    e.ctrlKey == hotkey.ctrl &&
                    e.shiftKey == hotkey.shift)
                {
                    return handler();
                }
            }
        );
    };
});