{if !empty($NewCheatsEntries)}
  <div class="box_innerhead">{#New_Cheats#}</div>
  {foreach from=$NewCheatsEntries item=e}
    <div class="{cycle name='gb' values='links_list,links_list_second'}">
      <div class="links_list_title">
        {if !empty($e->Sprache) && $CheatSettings->Flaggen == 1}
          <img class="absmiddle" src="{$imgpath}/flags/{$e->Sprache}.png" alt="" />&nbsp;
        {/if}
        <h3><a title="{$e->Name|sanitize}" href="{$e->Link_Details}">{$e->Name|sanitize}</a></h3>
      </div>
      {if !empty($e->Bild)}
        <a href="{$e->Link_Details}"><img class="links_list_img" src="uploads/cheats/{$e->Bild}" align="right" alt="" /></a>
        {/if}
        {$e->Beschreibung|truncate: 550}
      <br style="clear: both" />
    </div>
  {/foreach}
{/if}
