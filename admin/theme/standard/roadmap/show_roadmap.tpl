<style type="text/css">
  .dl_a_1 { background: #fdc; border-color: #e88; color: #a22; padding: 5px; }
  .dl_b_1 { background: #fed; border-color: #e99; color: #a22; padding: 5px; }
  .dl_a_2 { background: #ffb; border-color: #eea; color: #880; padding: 5px; }
  .dl_b_2 { background: #ffd; border-color: #dd8; color: #880; padding: 5px; }
  .dl_a_3 { background: #fbfbfb; border-color: #ddd; color: #444; padding: 5px; }
  .dl_b_3 { background: #f6f6f6; border-color: #ccc; color: #333; padding: 5px; }
  .dl_a_4 { background: #e7ffff; border-color: #cee; color: #099; padding: 5px; }
  .dl_b_4 { background: #dff; border-color: #bee; color: #099; padding: 5px; }
  .dl_a_5 { background: #e7eeff; border-color: #cde; color: #469; padding: 5px; }
  .dl_b_5 { background: #dde7ff; border-color: #cde; color: #469; padding: 5px; }
  .dl_a_6 { background: #f0f0f0; border-color: #ddd; color: #888; padding: 5px; }
  .dl_b_6 { background: #f0f0f0; border-color: #ddd; color: #888; padding: 5px; }
</style>

<div class="box_innerhead">{$name}</div>
<div class="infobox">
  <table width = "100%" border="0" cellpadding="0" cellspacing="0">
    <tr>
      <td><h4>{$name}</h4></td>
      <td align="right">
        {#GlobalStatus#}:
        <form method="post">
          <select name="closed" onchange="location.href = 'index.php?p=roadmap&amp;action=display&amp;rid={$smarty.request.rid}&amp;closed={if $smarty.request.closed == 1}0{else}1{/if}&amp;area={$area}&amp;name={$name|translit}'">
            <option value="1" {if $smarty.request.closed == 1}selected{/if}>{#ClosedTickets#}</option>
            <option value="0" {if $smarty.request.closed == 0}selected{/if}>{#OpenTickets#}</option>
          </select>
        </form>
      </td>
    </tr>
  </table>
</div>
<table width = "100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td align="center"><strong>{#Description#}</strong></td>
    <td align="center"><strong>{#Date#}</strong></td>
    <td align="center"><strong>{#GlobalAutor#}</strong></td>
    <td align="center"><strong>{#Prio#}</strong></td>
  </tr>
  {foreach from=$items item=item}
    <tr>
      <td class="dl_{cycle name="1" values="a,b"}_{$item.pr}">{$item.Beschreibung}</td>
      <td align="center" class="dl_{cycle name="2" values="a,b"}_{$item.pr}">{$item.Datum|date_format: '%d.%m.%Y'}</td>
      <td align="center" class="dl_{cycle name="3" values="a,b"}_{$item.pr}">{$item.Benutzer}</td>
      <td align="center" class="dl_{cycle name="4" values="a,b"}_{$item.pr}">{$item.prio}</td>
    </tr>
  {/foreach}
</table>
<br />
<br />
