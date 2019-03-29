{script file="$jspath/jrating.js" position='head'}
{foreach from=$Entries item=e}
  <div class="{cycle name='gb' values='links_list,links_list_second'}">
    <div class="links_list_title">
      <h3>
        {if !empty($e->Sprache) && $Downloadssettings->Flaggen == 1}
          <img class="absmiddle" src="{$imgpath}/flags/{$e->Sprache}.png" alt="" />&nbsp;
        {/if}
        <a title="{$e->Name|sanitize}" href="{$e->Link_Details}">{$e->Name|sanitize}</a>
      </h3>
    </div>
    {if !empty($e->Bild)}
      <a href="{$e->Link_Details}"><img class="links_list_img" src="uploads/downloads/{$e->Bild}" align="right" alt="" /></a>
      {/if}
      {$e->Beschreibung|truncate: 550}
    <br />
    <div class="links_list_foot">
      <table align="center">
        <tr>
          <td>{#Added#}{$e->Datum|date_format: $lang.DateFormatSimple}&nbsp;&nbsp; </td>
          <td>{#DownloadsNum#}: {$e->Hits}</td>
          {if $Downloadssettings->Wertung == 1}
            <td>&nbsp;&nbsp;{#Rating_Rating#}</td>
            <td>
              <input name="starrate{$e->Id}" type="radio" value="1" class="star" disabled="disabled" {if $e->Wertung == 1}checked="checked"{/if} />
              <input name="starrate{$e->Id}" type="radio" value="2" class="star" disabled="disabled" {if $e->Wertung == 2}checked="checked"{/if} />
              <input name="starrate{$e->Id}" type="radio" value="3" class="star" disabled="disabled" {if $e->Wertung == 3}checked="checked"{/if} />
              <input name="starrate{$e->Id}" type="radio" value="4" class="star" disabled="disabled" {if $e->Wertung == 4}checked="checked"{/if} />
              <input name="starrate{$e->Id}" type="radio" value="5" class="star" disabled="disabled" {if $e->Wertung == 5}checked="checked"{/if} />
            </td>
          {/if}
        </tr>
      </table>
      <table align="center">
        <tr>
          <td>
            <a href="{$e->Link_Details}"><img class="absmiddle" src="{$imgpath_page}arrow_right_small.png" alt="" /></a>
            <a href="{$e->Link_Details}">{#MoreDetails#}</a> &nbsp;&nbsp;
            {if !empty($e->Link_Categ)}
              <a href="{$e->Link_Categ}"><img class="absmiddle" src="{$imgpath_page}folder_small.png" alt="" /></a>
              <a href="{$e->Link_Categ}">{$e->KategName}</a>
            {/if}
            {if $Downloadssettings->Kommentare == 1}
              &nbsp;&nbsp;<a href="{$e->Link_Details}#comments"><img class="absmiddle" src="{$imgpath_page}comment_small.png" alt="" /></a>
              <a href="{$e->Link_Details}#comments">{#Comments#} ({$e->CCount})</a>
            {/if}
          </td>
        </tr>
      </table>
    </div>
  </div>
{/foreach}
<br />
{if !empty($Navi)}
  {$Navi}
{/if}
