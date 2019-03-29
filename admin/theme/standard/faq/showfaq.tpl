<script type="text/javascript">
<!-- //
$(document).ready(function() {
    $('.spoiler_open').on('click', function() {
        var id = $(this).attr('id');
        $('#div_' + id).toggle();
    });
    $('.user_pop').colorbox({ height: "600px", width: "550px", iframe: true });
});
//-->
</script>

<div class="box_innerhead">{#Faq#}</div>
{if $categs}
  <div class="newsarchive_jump">
    {#GotoArchive#}&nbsp;
    <select class="input" onchange="eval(this.options[this.selectedIndex].value); selectedIndex = 0;">
      <option value="location.href = 'index.php?p=faq&amp;area={$area}'" {if empty($smarty.request.faq_id)}selected="selected"{/if}>{#AllCategs#}</option>
      {foreach from=$categs item=fc}
        <option value="location.href = 'index.php?p=faq&amp;action=display&amp;faq_id={$fc->Id}&amp;area={$area}&amp;name={$fc->visible_title|translit}'" {if $fc->Id == $smarty.request.faq_id}selected="selected"{/if}>{$fc->visible_title|sanitize}</option>
      {/foreach}
    </select>
  </div>
{/if}
<h3><img border="0" src="{$imgpath}/faq/folder.png" class="absmiddle" alt="{$cat->Name|sanitize}" /> {$cat->Name|sanitize}</h3>
  {foreach from=$categs item=fcc}
    {if $cat->Id == $fcc->Parent_Id}
    <h4><a href="index.php?p=faq&amp;action=display&amp;faq_id={$fcc->Id}&amp;area={$area}&amp;name={$fcc->visible_title|translit}">{$fcc->visible_image_small}</a></h4>
    {/if}
  {/foreach}
<br />
<br />
{assign var=nofaq value=0}
{foreach from=$faq item=item}
  {if $cat->Id == $item->Kategorie}
    <a href="#faq{$item->Id}"><img border="0" src="{$imgpath}/faq/info.png" class="absmiddle" alt="{$item->Faq|sanitize}" /> <strong>{$item->Faq|sanitize}</strong></a>
    <br />
    {assign var=nofaq value=1}
  {/if}
{/foreach}
{if $nofaq == 1}
  <br />
  <br />
  {foreach from=$faq item=item}
    {if $cat->Id == $item->Kategorie}
      <div class="topcontent">
        <div class="faq_innerhead">
          <a id="faq{$item->Id}" href="index.php?p=faq&amp;action=faq&amp;fid={$item->Id}&amp;area={$area}&amp;name={$item->Faq|translit}">{$item->Faq|sanitize}</a>
        </div>
        <div class="faq_text" id="div_faq_{$item->Id}" style="display: none">
          {$item->text}
          <br />
          <br />
        </div>
        <div align="center">
          <div>
            <a id="faq_{$item->Id}" class="spoiler_open" href="index.php?p=faq&amp;action=faq&amp;fid={$item->Id}&amp;area={$area}&amp;name={$item->Faq|translit}" style="float: left" onclick="return false;">
              <strong>{#FaqReply#}</strong>
            </a>
          </div>
          <div><a style="float: right" href="#"><strong>{#GlobalTop#}</strong></a> </div>
            {if  permission('faq_sent')}
            <a class="user_pop" href="index.php?p=faq&amp;action=mail&amp;faq_id={$cat->Id}&amp;area={$area}"><strong>{#New_Guest#}</strong></a>
            {/if}
        </div>
      </div>
    {/if}
  {/foreach}
  <br />
  <br />
{/if}
{if  permission('faq_sent')}
  <h4><a class="user_pop" href="index.php?p=faq&amp;action=mail&amp;faq_id=0&amp;area={$area}"><img border="0" src="{$imgpath}/faq/help.png" class="absmiddle" alt="{#New_Guest#}" /> {#New_Guest#}</a></h4>
  <br />
{/if}
