<div class="newsarchive_jump">
  {#GotoArchive#}&nbsp;
  <select name="select" onchange="eval(this.options[this.selectedIndex].value);selectedIndex = 0;">
    <option value="location.href = 'index.php?p=articles&amp;area={$area}{$TypArchive|default:''}'" {if empty($smarty.request.catid)}selected="selected"{/if}>{#AllCategs#}</option>
    {foreach from=$dropdown item=dd}
      <option value="location.href = '{$dd->HLink}'" {if isset($smarty.request.catid) && $smarty.request.catid == $dd->Id}selected="selected"{/if}>{$dd->visible_title}</option>
    {/foreach}
  </select>
</div>
{if !empty($Categs)}
  {assign var=lcc value=0}
  {foreach from=$Categs item=c}
    {if $c->Parent_Id == $smarty.request.catid|default:0}
      {assign var=lcc value=$lcc+1}
      <div style="float: left; width: 49%">
        {if $c->LinkCount>=1}
          <a href="{$c->HLink}"><img style="margin-right: 5px" class="absmiddle" src="{$imgpath_page}folder.png" alt="" /></a>
          <a href="{$c->HLink}"><strong>{$c->Name|sanitize} ({$c->LinkCount})</strong></a>
        {else}
          <img style="margin-right: 5px" class="absmiddle" src="{$imgpath_page}folder.png" alt="" /> <strong>{$c->Name|sanitize}</strong>
        {/if}
        <br />
      </div>
      {if $lcc % 2 == 0}
        <div style="clear: both"></div>
      {/if}
    {/if}
  {/foreach}
  <br style="clear: both" />
  <br />
{/if}
