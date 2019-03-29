<div class="header">{#Roadmap#} - {#Tickets#}</div>
<div class="edit_tabs_div">
  <div class="edit_tabs"><a href="index.php?do=roadmap">{#Roadmaps#}</a></div>
  <div class="edit_tabs"><a href="index.php?do=roadmap&amp;sub=showtickets&amp;id={$smarty.request.id}&amp;closed=0">{#OpenTickets#}</a></div>
  <div class="edit_tabs"><a href="index.php?do=roadmap&amp;sub=showtickets&amp;id={$smarty.request.id}&amp;closed=1">{#ClosedTickets#}</a></div>
  <div class="edit_tabs"><a title="{#NewTicket#}" class="colorbox" href="index.php?do=roadmap&amp;sub=newticket&amp;id={$smarty.request.id}&amp;noframes=1">{#NewTicket#}</a></div>
  <div style="clear: both"></div>
</div>
<br />
<table width="100%" border="0" cellspacing="0" cellpadding="6">
  <tr>
    <td width="7%" align="center" class="headers">{#Global_Id#}</td>
    <td class="headers">{#Global_descr#}</td>
    <td width="15%" align="center" class="headers">{#Global_Author#}</td>
    <td width="20%" align="center" class="headers">{#Priority#}</td>
    <th colspan="2" class="headers">{#Global_Actions#}</th>
  </tr>
  {foreach from=$items item=item}
    <tr class="{cycle values='second,first'}">
      <td width="7%" align="center" class="row_spacer">{$item.Id}</td>
      <td class="row_spacer">{$item.Beschreibung}</td>
      <td width="15%" align="center" class="row_spacer">{$item.Benutzer}</td>
      <td width="20%" align="center" class="row_spacer">{$item.Prio}</td>
      <td class="row_spacer" width="1%" align="center">
        <a class="colorbox stip" title="{$lang.Edit|sanitize}" href="index.php?do=roadmap&amp;sub=editticket&amp;id={$item.Id}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/edit.png" alt="" border="0" /></a>
        <a class="stip" title="{$lang.Global_Delete|sanitize}" href="index.php?do=roadmap&amp;sub=delticket&amp;id={$item.Id}&amp;rid={$smarty.request.id}&amp;closed={$smarty.request.closed}"><img class="absmiddle" src="{$imgpath}/delete.png" alt="" border="0" /></a>
      </td>
    </tr>
  {/foreach}
</table>
<br />
