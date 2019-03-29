{if $externGals}
  <div class="box_innerhead">{#Gallery_extern#}</div>
  {foreach from=$externGals item=gex}
    {if $gex->ICount>0}
      <div class="gallery_extern_border">
        <div class="gallery_extern_header"><strong><a href="{$gex->Link}">{$gex->GalName|truncate: 15|sanitize}</a></strong></div>
        <div style="padding: 2px"><a href="{$gex->Link}"><img src="{$gex->Img}" alt="{$gex->GalName|sanitize}" /></a></div>
      </div>
    {/if}
  {/foreach}
  <div style="clear: both"></div>
{/if}
