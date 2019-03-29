{if $NewDownloadsEntries}
  <div class="box_innerhead">{#Downloads_new#}</div>
  {foreach from=$NewDownloadsEntries item=e}
    <div class="{cycle name='gb7' values='links_list_newstart,links_list_newstart_second'}" style="height: 135px">
      <div class="links_list_title">
        <h3>
          {if !empty($e->Sprache) && $Downloadssettings->Flaggen == 1}
            <img class="absmiddle" src="{$imgpath}/flags/{$e->Sprache}.png" alt="" />&nbsp;
          {/if}
          <a title="{$e->Name|sanitize}" href="{$e->Link_Details}">{$e->Name|truncate: 25|sanitize}</a>
        </h3>
      </div>
      {assign var=maxlength_this value=200}
      {if !empty($e->Bild)}
        {assign var=maxlength_this value=120}
        <a href="{$e->Link_Details}"><img class="links_list_img" src="uploads/downloads/{$e->Bild}" align="right" alt="" /></a>
        {/if}
      <div class="justify">{$e->Beschreibung|truncate: $maxlength_this}</div>
      <br />
    </div>
  {/foreach}
{/if}
