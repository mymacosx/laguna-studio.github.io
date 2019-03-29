<div class="box_innerhead">{#Roadmap#}</div>
{foreach from=$items item=item}
  <div class="{cycle name='gb' values='links_list,links_list_second'}">
    <div class="links_list_title">
      <h3>{$item.Name}</h3>
    </div>
    {if $item.Edit} {#LastChange#}: {$item.Edit|date_format: '%d-%m-%Y, %H:%M'} {/if}
    <table class="progress">
      <tr>
        <td class="closed" style="width: {$item.Closed}%;"></td>
        <td class="open" style="width: {$item.Open}%;"></td>
      </tr>
    </table>
    <span style="float: right; margin-top: -29px; margin-right: 10px;">{$item.Closed}%</span>
    <span style="color: #666"><i>
        {#ClosedTickets#}: <a href="index.php?p=roadmap&amp;action=display&amp;rid={$item.Id}&amp;closed=1&amp;area={$area}&amp;name={$item.Name|translit}" title="{#ClosedTickets#}"><strong>{$item.NumFertig}</strong></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        {#OpenTickets#}: <a href="index.php?p=roadmap&amp;action=display&amp;rid={$item.Id}&amp;closed=0&amp;area={$area}&amp;name={$item.Name|translit}" title="{#OpenTickets#}"><strong>{$item.NumUFertig}</strong></a></i></span>
    <div class="links_list_foot">
      {$item.Beschreibung}
      <br />
    </div>
  </div>
{/foreach}
