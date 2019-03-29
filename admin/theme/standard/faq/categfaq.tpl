<script type="text/javascript">
<!-- //
$(document).ready(function() {
    $('.user_pop').colorbox({ height: "600px", width: "550px", iframe: true });
});
//-->
</script>

{if empty($categs)}
  <div class="h3">{#Faq_nothing#}</div>
{else}
  <div class="box_innerhead">{#Faq#}</div>
  {foreach from=$categs item=item}
    <h3><a href="index.php?p=faq&amp;action=display&amp;faq_id={$item->Id}&amp;area={$area}&amp;name={$item->visible_title|translit}">{$item->visible_image}</a></h3>
  {/foreach}
{/if}
<br />
<br />
{if permission('faq_sent')}
  <div class="h4"><a class="user_pop" href="index.php?p=faq&amp;action=mail&amp;faq_id=0&amp;area={$area}"><img border="0" src="{$imgpath}/faq/help.png" class="absmiddle" alt="{#New_Guest#}" /> {#New_Guest#}</a></div>
  <br />
{/if}
