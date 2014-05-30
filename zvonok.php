<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8">

<?php

if (isset($_POST['enter']))
{
   $send=TRUE;
   $to_mail = "info@fonarik.com"; // ваш email
   $date = date("Y-m-d");
   $ip = $_SERVER['REMOTE_ADDR'];
   $name =  $_POST['name'];
   $phones = $_POST['phones'];
   $text = $_POST['text'];
   if($_POST['phone'] == "yes") { 
   if ($_POST['name'] ==""){
    $err01 = "Вы не <b>Представились</b>!";
    $send = FALSE;
    }
   if ($_POST['phones'] =="") {
    $err02 ="<BR>Вы не ввели <b>Телефон</b>";
    $send = FALSE;
    }
   if ($send){
     $sendphone = "<div><B>Как только мы увидим вашу заявку так сразу же позвоним Вам</B>
     <BR>
     <a href='javascript: self.close ()'>Закрыть окно</a>
     </div>";
     $message="
     Имя - $name
     Телефон - $phones
     Коментарий - $text
     IP - $ip
     Дата - $date
   ";
    mail("$to_mail","Заказ звонка","$message","From: phone@zakaz-zvonok.ru\n"."Content-type: text/plain; charset=utf-8");
    }
    else if (!$send) {
    $sendphone = "<div  style='padding: 14 0 0 15px; color: #838383;'>";
    $sendphone .= "$err01"; 
	$sendphone .= "$err02";
	$sendphone .= "<BR><BR>Вернитесь <a href='zvonok.php'>назад</a> и повторите попытку";
    $sendphone .= "</div>";
    }
 }
} 
?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
<title>Заказ обратного звонка</title>
<script type="text/javascript">
var _gaq = _gaq || [];
_gaq.push (['_setAccount', 'UA-6681162-5']);
_gaq.push (['_addOrganic', 'images.yandex.ru', 'text']);
_gaq.push (['_addOrganic', 'blogs.yandex.ru', 'text']);
_gaq.push (['_addOrganic', 'video.yandex.ru', 'text']);
_gaq.push (['_addOrganic', 'yandex.ru', 'query']);
_gaq.push (['_addOrganic', 'go.mail.ru', 'q']);
_gaq.push (['_addOrganic', 'mail.ru', 'q']);
_gaq.push (['_addOrganic', 'google.com.ua', 'q']);
_gaq.push (['_addOrganic', 'images.google.ru', 'q']);
_gaq.push (['_addOrganic', 'maps.google.ru', 'q']);
_gaq.push (['_addOrganic', 'rambler.ru', 'words']);
_gaq.push (['_addOrganic', 'nova.rambler.ru', 'query']);
_gaq.push (['_addOrganic', 'nova.rambler.ru', 'words']);
_gaq.push (['_addOrganic', 'gogo.ru', 'q']);
_gaq.push (['_addOrganic', 'nigma.ru', 's']);
_gaq.push (['_addOrganic', 'search.qip.ru', 'query']);
_gaq.push (['_addOrganic', 'webalta.ru', 'q']);
_gaq.push (['_addOrganic', 'sm.aport.ru', 'r']);
_gaq.push (['_addOrganic', 'meta.ua', 'q']);
_gaq.push (['_addOrganic', 'search.bigmir.net', 'z']);
_gaq.push (['_addOrganic', 'search.i.ua', 'q']);
_gaq.push (['_addOrganic', 'index.online.ua', 'q']);
_gaq.push (['_addOrganic', 'web20.a.ua', 'query']);
_gaq.push (['_addOrganic', 'search.ukr.net', 'search_query']);
_gaq.push (['_addOrganic', 'search.com.ua', 'q']);
_gaq.push (['_addOrganic', 'search.ua', 'q']);
_gaq.push (['_addOrganic', 'poisk.ru', 'text']);
_gaq.push (['_addOrganic', 'go.km.ru', 'sq']);
_gaq.push (['_addOrganic', 'liveinternet.ru', 'ask']);
_gaq.push (['_addOrganic', 'gde.ru', 'keywords']);
_gaq.push (['_addOrganic', 'affiliates.quintura.com', 'request']);
_gaq.push (['_addOrganic', 'akavita.by', 'z']);
_gaq.push (['_addOrganic', 'search.tut.by', 'query']);
_gaq.push (['_addOrganic', 'all.by', 'query']);
_gaq.push (['_trackPageview']);
(function() {
var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
})();
</script>
</head>
<body>
<center>
<div style="padding: 20px;"> 

<font style="font-size: 20px;">Заказ обратного звонка:</font><Br><Br>    

<? if(isset($sendphone)) { echo $sendphone; } else { ?>
<table border=0>
<form method=post onSubmit="if(!document.getElementById('phone').value){ alert(document.getElementById('phone').value + 'Введите номер!'); return false;}">
<input type="hidden" name="enter" value="1"/>
<tr>
<td align=right><font class="olive">Представтесь</font></td>
<td><input type="text" class="login" name="name" size=20/></td>
</tr>
<tr>
<td align=right><font class="olive">Телефон:</font></td>
<td><input name="phones" class="login" type="text" size=20/></td>
</tr>    
<tr><td colspan=2 align=center>Сообщение : <BR><textarea name="text" cols="30" rows="5"></textarea></td></tr>    
<tr>
<td colspan=2 align=center>
<input type="submit" value="отправить" onclick="_gaq.push(['_trackEvent', 'button2', 'callback']);"/>
<input type="hidden" name="phone" value="yes"/>
<input type="hidden" name="ip" value="<?php echo $_SERVER['REMOTE_ADDR'];?>"/>
<input type="hidden" name="data" value="<?php echo date("H:i d.m.Y");?>"/>
</td>
</tr>    
</form>
</table>
<? } ?>
</div>
</html>