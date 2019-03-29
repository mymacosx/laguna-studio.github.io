{if $externGals}
  <div class="box_innerhead">{#Gallery_extern#}</div>
  <div style="text-align: center">
    {foreach from=$externGals item=gex}
      {if $gex->ICount>0}
        <div style="clear: both; text-align: center"><strong><a href="{$gex->Link}">{$gex->GalName|truncate: 25|sanitize}</a></strong></div>
        <div style="padding: 2px"><a href="{$gex->Link}"><img src="{$gex->Img}" alt="{$gex->GalName|sanitize}" /></a></div>
      {/if}
    {/foreach}
  </div>
  <div style="clear: both"></div>
{/if}
