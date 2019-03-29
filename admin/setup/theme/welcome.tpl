<div class="headers"> {#Step1_Inf#} {$Version}</div>
<div class="eula" id="eula"> Сайт проекта: <a href="http://www.status-x.ru/" target="_blank">www.Status-X.ru</a><br />
    <a href="http://www.status-x.ru/forums/" target="_blank">На форуме</a> Вы сможете найти ответы на интересующие Вас вопросы <br />
    <br />
    Распространяется в соответствии с лицензией GPL v.2<br />
    Текст лицензии на <a target="_blank" href="{$setupdir}/licence.ru.txt">русском</a> и <a target="_blank" href="{$setupdir}/licence.en.txt">английском</a> языке. <br />
    <br />
    <strong>Основные технические требования к хостингу: </strong> <br />
    Веб-сервер Apache с модулем rewrite <br />
    Версия PHP - 5.2.0 и выше <br />
    База данных MySQL - 4.1.3 и выше <br />
    Поддержка zlib <br />
    Поддержка gd <br />
    Поддержка iconv <br />
    Поддержка mbstring <br />
    Поддержка spl <br />
    Поддержка mysqli <br />
    Поддержка локали UTF-8 в PHP <br />
    Объем памяти, выделяемый на выполнение скриптов, не менее 16 Мб <br />
    Права для PHP-скриптов на создание и удаление директорий и файлов <br />
    Возможность загрузки файлов на сервер через PHP-скрипты <br />
    <br />
    safe_mode: Выключено <br />
    register_globals: Выключено <br />
    magic_quotes_gpc: Выключено <br />
    magic_quotes_runtime: Выключено <br />
    magic_quotes_sybase: Выключено <br />
    <br />
    При наличии привилегий на создание баз, база будет создана автоматически инсталятором, в противном случае, Вам необходимо создать базу данных с помощью phpMyAdmin, кодировка базы utf8<br />
    <br />
    <strong>Для указанных ниже файлов и папок необходимо установить права на запись CHMOD 777 или CHMOD 755, если инсталятору не удастся сделать это автоматически.</strong><br />
    config/db.config.php<br />
    temp/cache/<br />
    temp/private/<br />
    temp/compiled/<br />
    temp/compiled/1/<br />
    temp/compiled/2/<br />
    temp/compiled/3/<br />
    temp/compiled/1/main/<br />
    temp/compiled/2/main/<br />
    temp/compiled/3/main/<br />
    temp/compiled/1/admin/<br />
    temp/compiled/2/admin/<br />
    temp/compiled/3/admin/<br />
    uploads/<br />
    uploads/articles/<br />
    uploads/forum/<br />
    uploads/attachments/<br />
    uploads/avatars/<br />
    uploads/cheats/<br />
    uploads/cheats_files/<br />
    uploads/content/<br />
    uploads/downloads/<br />
    uploads/downloads_files/<br />
    uploads/galerie/<br />
    uploads/galerie_icons/<br />
    uploads/screenshots/<br />
    uploads/links/<br />
    uploads/manufacturer/<br />
    uploads/media/<br />
    uploads/partner/<br />
    uploads/products/<br />
    uploads/videos/<br />
    uploads/shop/<br />
    uploads/shop/customerfiles/<br />
    uploads/shop/files/<br />
    uploads/shop/icons/<br />
    uploads/shop/icons_categs/<br />
    uploads/shop/navi_categs/<br />
    uploads/shop/payment_icons/<br />
    uploads/shop/shipper_icons/<br />
    uploads/user/<br />
    uploads/user/gallery/<br />
    <br />
    Поддержать развитие проекта можно на этой <a href="http://www.status-x.ru/donate/" target="_blank" >странице</a><br />
</div>
<div class="button_steps">
    <form method="post" action="{$setupdir}/setup.php">
        <input type="hidden" name="step" value="1" />
        <input type="submit" value="{#EulaOk#}" />
    </form>
</div>
