{script file="$jspath/jrating.js" position='head'}
{foreach from=$Entries item=e}
<script type="text/javascript">
<!-- //
$(document).ready(function() {
    $('#linkextern_{$e->Id}').on('click', function() {
        var options = {
            target: '#plick',
            url: 'index.php?action=updatehitcount&p=links&id={$e->Id}',
            timeout: 3000
        };
        $(this).ajaxSubmit(options);
        return true;
    });
});
//-->
</script>

<div class="{cycle name='gb' values='links_list,links_list_second'}">
  <div class="links_list_title">
    <h3>
      {if !empty($e->Sprache) && $Linksettings->Flaggen == 1}
        <img class="absmiddle" src="{$imgpath}/flags/{$e->Sprache}.png" alt="" />&nbsp;
      {/if}
      <a title="{$e->Name|sanitize}" href="{$e->Link_Details}" id="linkextern_{$e->Id}">{$e->Name|sanitize}</a>
    </h3>
  </div>
  {if !empty($e->Bild)}
    <a href="{$e->Link_Details}"><img class="links_list_img" src="uploads/links/{$e->Bild}" align="right" alt="" /></a>
    {/if}
    {$e->Beschreibung|truncate: 550}
  <br />
  <div class="links_list_foot">
    <table align="center">
      <tr>
        <td>{#Added#}{$e->Datum|date_format: $lang.DateFormatSimple}&nbsp;&nbsp; </td>
        <td>{#Global_Hits#}: {$e->Hits}</td>
        {if $Linksettings->Wertung == 1}
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
            <a href="{$e->Link_Categ}">{$e->KategName}</a> &nbsp;&nbsp;
          {/if}
          {if $Linksettings->Kommentare == 1}
            <a href="{$e->Link_Details}#comments"><img class="absmiddle" src="{$imgpath_page}comment_small.png" alt="" /></a>
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
