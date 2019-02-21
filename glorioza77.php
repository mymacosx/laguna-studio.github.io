<?php
function change_input_text_mini ($text=''){   return htmlspecialchars( ( str_replace(chr(0), '', $text) )); } # Минимальная очистка входящего текста - нет mysql_escape_string
$name = isset($_POST['name'])?change_input_text_mini($_POST['name']):'';
$phone = isset($_POST['phone'])?change_input_text_mini($_POST['phone']):'';
$mess = isset($_POST['mess'])?change_input_text_mini($_POST['mess']):'';
$result = 0;
            $to = "<span id="cloak9d6f0359317de6c5262e50ca62241ca3"><a href="mailto:glorioza77@mail.ru">glorioza77@mail.ru</a></span><script type="text/javascript">
                document.getElementById('cloak9d6f0359317de6c5262e50ca62241ca3').innerHTML = '';
                var prefix = 'ma' + 'il' + 'to';
                var path = 'hr' + 'ef' + '=';
                var addy9d6f0359317de6c5262e50ca62241ca3 = 'glorioza77' + '@';
                addy9d6f0359317de6c5262e50ca62241ca3 = addy9d6f0359317de6c5262e50ca62241ca3 + 'mail' + '.' + 'ru';
                var addy_text9d6f0359317de6c5262e50ca62241ca3 = 'glorioza77' + '@' + 'mail' + '.' + 'ru';document.getElementById('cloak9d6f0359317de6c5262e50ca62241ca3').innerHTML += '<a ' + path + '\'' + prefix + ':' + addy9d6f0359317de6c5262e50ca62241ca3 + '\'>'+addy_text9d6f0359317de6c5262e50ca62241ca3+'<\/a>';
        </script>"; //ВАША ПОЧТА
            $subject = "Заявка с сайта!";
            $text =  "\nИмя - $name
            Телефон - $phone
            Комментарий - $mess
            ";
            $header.= "Content-type: text/html; charset=utf-8\r\n";
            $header .= "MIME-Version: 1.0\r\n";
            $sending = mail($to, $subject, $text, $header);
            if($sending) $result = 1;
            //echo $result;
if($result == 1) {
header('Location: Страница с сообщением об успешной доставке'); 
}else{
header('Location: Страница с сообщением о неудачной доставке');
}
?>