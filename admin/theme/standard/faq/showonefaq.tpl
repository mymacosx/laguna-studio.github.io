<script type="text/javascript">
<!-- //
$(document).ready(function() {
    $('.user_pop').colorbox({ height: "600px", width: "550px", iframe: true });
});
//-->
</script>

<div class="box_innerhead">{#Faq#}</div>
{if $categs}
  <div class="newsarchive_jump">
    {#GotoArchive#}&nbsp;
    <select class="input" onchange="eval(this.options[this.selectedIndex].value);
      selectedIndex = 0;">
      <option value="location.href = 'index.php?p=faq&amp;area={$area}'" {if empty($smarty.request.faq_id)}selected="selected"{/if}>{#AllCategs#}</option>
      {foreach from=$categs item=fc}
        <option value="location.href = 'index.php?p=faq&amp;action=display&amp;faq_id={$fc->Id}&amp;area={$area}&amp;name={$fc->visible_title|translit}'" {if $fc->Id == $faq->Kategorie}selected="selected"{/if}>{$fc->visible_title|sanitize}</option>
      {/foreach}
    </select>
  </div>
{/if}
<br />
<div class="topcontent">
  <div class="faq_innerhead">{$faq->Name|sanitize}</div>
  {$faq->text}
  <br />
</div>
<br />
<br />
{if  permission('faq_sent')}
  <h4><a class="user_pop" href="index.php?p=faq&amp;action=mail&amp;faq_id=0&amp;area={$area}"><img border="0" src="{$imgpath}/faq/help.png" class="absmiddle" alt="{#New_Guest#}" /> {#New_Guest#}</a></h4>
  <br />
{/if}
