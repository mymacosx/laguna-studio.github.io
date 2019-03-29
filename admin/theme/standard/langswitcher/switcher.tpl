{foreach from=$languages item=lang name=ls}
  {if $langcode == $lang}
    <span class="flag_active">{$lang}&nbsp;<img class="absmiddle stip" title="{$lang}" src="{$imgpath}/flags/{$lang}.png" alt="" />&nbsp;</span>
  {else}
    <a href="index.php?lang={$lang}&amp;lredirect={page_link|base64encode}&amp;rand={$smarty.now}">&nbsp;<img class="absmiddle stip" title="{$lang}" src="{$imgpath}/flags/{$lang}.png" alt="" /></a>
    {if !$smarty.foreach.ls.last}
     &nbsp;
    {/if}
  {/if}
{/foreach}
