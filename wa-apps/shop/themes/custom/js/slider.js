var shift_timer = null;
var shift_time = 2000;	// время смены сообщений
var show_time = 5500;	// время показа сообщений
function shift_left ()
{
		var size = parseInt($('#lenta').css('width'));
		var position = parseInt ($('#lenta-inside').css('left')) - size;
		var number = Math.abs(Math.ceil(position/size));
        if (!number)
    {
     number=1;
    }
		if (number > 7)
		{
			$('#lenta-inside').css('left', '0');
			number = 1;
		}
		var next = number;
		next = next > 6? 0: next;
		position = -number*size;
		$('#lenta-inside').animate({left: position+'px'}, shift_time);	
		$('.curent').removeClass('curent');
		$('#navigation a:eq('+next+')').addClass('curent');
}

$(function (){
	// определяем ширину блока слайдера
	var w = $('#lenta img').width();
	$('#lenta').width(w);
	// устанавливаем позиции элементов слайдера
	$('#item2').css('left', w+'px');
	$('#item3').css('left', 2*w+'px');
	$('#item4').css('left', 3*w+'px');
	$('#item5').css('left', 4*w+'px');
 	$('#item6').css('left', 5*w+'px');
 	$('#item7').css('left', 6*w+'px');
	$('#item1a').css('left', 7*w+'px');
	// определяем высоту блока слайдера
	$('#lenta').height ($('#item1').height ());
	// устанавливаем обработчики щелчка навигации
	$('#navigation a').click(function(){
			clearInterval(shift_timer);
			var size = parseInt($('#lenta').css('width'));
			var number = parseInt (this.toString().substr(-1,1)) - 1;
			var next = number;
			next = next > 6? 0: next;
			position = -number*size;
			$('#lenta-inside').animate({left: position+'px'}, shift_time);	
			$('.curent').removeClass('curent');
			$('#navigation a:eq('+next+')').addClass('curent');
			shift_timer = setInterval("shift_left()", show_time+shift_time);
			return false;
		});
	// запускаем сдвиг ленты влево
	shift_timer = setInterval("shift_left()", show_time+shift_time);
});
