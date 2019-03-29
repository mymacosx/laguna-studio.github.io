{result type='script' format='file' position='head'} {* вывод файлов скриптов *}
{if $browser == 'ie8' || $browser == 'ie7' || $browser == 'ie6'}
{script file="$jspath/jquery-1.11.1.js" position='head' priority='1000'}
{else}
{script file="$jspath/jquery-2.1.1.js" position='head' priority='1000'}
{/if}
{script file="$jspath/jpatch.js" position='head' priority='1000'}
{script file="$jspath/jtabs.ui.js" position='head' priority='1000'}
{script file="$jspath/jtooltip.js" position='head' priority='800'}
{script file="$jspath/jcolorbox.js" position='head' priority='800'}
{script file="$jspath/jtoggle.js" position='head' priority='800'}
{script file="$jspath/jcookie.js" position='head' priority='800'}
{script file="$jspath/jblock.js" position='head' priority='800'}
{script file="$jspath/jform.js" position='head' priority='800'}
{script file="$jspath/jtextcopy.js" position='head' priority='800'}
{script file="$jspath/functions.js" position='head' priority='800'}

{result type='script' format='code' position='head'} {* вывод кода скриптов *}
<script type="text/javascript">
<!-- //
$(document).ready(function() {
    $('.stip').tooltip();
    $('.colorbox').colorbox({ height: "98%", width: "90%", iframe: true });
    $('.colorbox_small').colorbox({ height: "95%", width: "80%", iframe: true });
    $('body').textcopy({ text: "{#MoreDetails#}" });
    $('a').on('click focus', function() {
        $(this).blur();
    });
});
//-->
</script>
{result type='code' format='code' position='head'}   {* вывод кода *}
