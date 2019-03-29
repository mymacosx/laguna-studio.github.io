{if !empty($NewGalleryEntries)}
  <div class="box_innerhead">{#Gallery_NameNew#}</div>
  {foreach from=$NewGalleryEntries item=gex}
    <div class="{cycle name='gb4' values='links_list,links_list_second'}">
      <div>
        <h3><a href="{$gex->Link}">{$gex->Name|truncate:60|sanitize}</a></h3>
      </div>
      {if $gex->Img}
        <a href="{$gex->Link}"><img class="links_list_img" align="right" src="{$gex->Img}" alt="{$gex->GalName|default:''|sanitize}" /></a>
      {/if}
      <div class="justify">{$gex->Text|truncate: 400}</div>
      <br style="clear: both" />
    </div>
  {/foreach}
  <div style="clear: both"></div>
{/if}
