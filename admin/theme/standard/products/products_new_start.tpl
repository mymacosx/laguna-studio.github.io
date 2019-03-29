{if !empty($NewProductEntries)}
  <div class="box_innerhead">{#Products_new#}</div>
  {foreach from=$NewProductEntries item=res}
    <div class="{cycle name='gb8' values='links_list_newstart,links_list_newstart_second'}">
      <div class="links_list_title">
        <h3><a title="{$res->Name|sanitize}" href="index.php?p=products&amp;area={$area}&amp;action=showproduct&amp;id={$res->Id}&amp;name={$res->Name|translit}">{$res->Name|sanitize}</a></h3>
      </div>
      {if !empty($res->Bild)}
        <a href="index.php?p=products&amp;area={$area}&amp;action=showproduct&amp;id={$res->Id}&amp;name={$res->Name|translit}"><img class="links_list_img" src="uploads/products/{$res->Bild}" align="right" alt="" /></a>
        {/if}
        {$res->Beschreibung|truncate: 550}
      <br style="clear: both" />
    </div>
  {/foreach}
{/if}
