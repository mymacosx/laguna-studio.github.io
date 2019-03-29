{include file="$incpath/header/style.tpl"}
{include file="$incpath/header/jquery.tpl"}
<script type="text/javascript" src="{$jspath}/jcookie.js"></script>
<script type="text/javascript" src="{$jspath}/jtoggle.js"></script>
<script type="text/javascript" src="{$jspath}/jvalidate.js"></script>
<script type="text/javascript" src="{$jspath}/jsuggest.js"></script>
<script type="text/javascript" src="{$jspath}/jblock.js"></script>
<script type="text/javascript" src="{$jspath}/jform.js"></script>
<script type="text/javascript" src="{$jspath}/ddaccordion.js"></script>
<script type="text/javascript">
<!-- //
$(document).ready(function() {
    $('.colorbox').colorbox({ height: '97%', width: '90%', iframe: true, fastIframe: false });
    $('.colorbox_small').colorbox({ height: '70%', width: '60%', iframe: true, fastIframe: false });
    $('.stip').tooltip();
    $('#com').show();
    setTimeout(function() {
        $('#com_loader').hide();
    }, 500);
});
//-->
</script>
