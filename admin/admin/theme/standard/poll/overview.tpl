<script type="text/javascript">
<!-- //
$(document).ready(function() {
    {foreach from=$polls item=c}
    $('#Start_{$c->Id}').datepicker({ changeMonth: true, changeYear: true, dateFormat: 'dd.mm.yy', dayNamesMin: [{#Calendar_daysmin#}], monthNamesShort: [{#Calendar_monthNamesShort#}], firstDay: 1 });
    $('#Ende_{$c->Id}').datepicker({ changeMonth: true, changeYear: true, dateFormat: 'dd.mm.yy', dayNamesMin: [{#Calendar_daysmin#}], monthNamesShort: [{#Calendar_monthNamesShort#}], firstDay: 1 });
    {/foreach}
});
//-->
</script>

<div class="header">{#Polls#}</div>
<div class="subheaders">
  <a class="colorbox" title="{#Polls_new#}" href="index.php?do=poll&amp;sub=new&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/add.png" alt="" border="0" /> {#Polls_new#}</a>&nbsp;&nbsp;&nbsp;
    {if $admin_settings.Ahelp == 1}
    <a class="colorbox" href="index.php?do=help&amp;sub={$helpquery}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/s_help.png" alt="" border="0" /> {#GlobalHelp#}</a>&nbsp;&nbsp;&nbsp;
    {/if}
  <a class="colorbox" href="index.php?do=support&amp;sub=send_order&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/send.png" alt="" border="0" /> {#SendOrder#}</a>
</div>
<form method="post" action="">
  <table width="100%" border="0" cellspacing="0" cellpadding="3">
    <tr class="headers">
      <td width="220" class="headers">{#Global_Name#}</td>
      <td width="120" align="center" class="headers"> {#Global_Published#}</td>
      <td width="120" align="center" class="headers">{#Global_PubEnd#}</td>
      <td width="120" align="center" class="headers">{#Global_Participant#}</td>
      <td width="120" align="center" class="headers">{#Global_Active#}</td>
      <td width="70" align="center" class="headers">{#Global_Actions#}</td>
    </tr>
    {foreach from=$polls item=c}
      <tr class="{cycle values='second,first'}">
        <td><strong>{$c->Titel_1|sanitize}</strong><input type="hidden" name="poll[{$c->Id}]" value="{$c->Id}"/></td>
        <td align="center" nowrap="nowrap"><input class="input" style="width: 65px" type="text" name="Start[{$c->Id}]" id="Start_{$c->Id}" value="{$c->Start|date_format: "%d.%m.%Y"}" readonly="readonly" /></td>
        <td align="center" nowrap="nowrap"><input class="input" style="width: 65px" type="text" name="Ende[{$c->Id}]" id="Ende_{$c->Id}" value="{$c->Ende|date_format: "%d.%m.%Y"}" readonly="readonly" /></td>
        <td align="center">{$c->Users}</td>
        <td align="center">
          {if $c->Aktiv == 1}
            <a class="stip" title="{$lang.Polls_setInActive|sanitize}" href="index.php?do=poll&amp;sub=openclose&amp;op=close&amp;id={$c->Id}&amp;backurl={$backurl}"><img class="absmiddle" src="{$imgpath}/opened.png" alt="" border="0" /></a>
            {else}
            <a class="stip" title="{$lang.Polls_setActive|sanitize}" onclick="return confirm('{#Polls_setActiveInf#}');" href="index.php?do=poll&amp;sub=openclose&amp;op=open&amp;id={$c->Id}&amp;backurl={$backurl}"><img class="absmiddle" src="{$imgpath}/closed.png" alt="" border="0" /></a>
            {/if}
        </td>
        <td>
          <a class="colorbox stip" title="{$lang.Polls_edit|sanitize}" href="index.php?do=poll&amp;sub=edit&amp;id={$c->Id}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/edit.png" alt="" border="0" /></a>
            {if $c->Users>=1}
            <a class="stip" title="{$lang.Polls_delStats|sanitize}" href="index.php?do=poll&amp;sub=delstats&amp;id={$c->Id}&amp;backurl={$backurl}"><img class="absmiddle" src="{$imgpath}/chart_del.png" alt="" border="0" /></a>
            {else}
            <img class="absmiddle stip" title="{$lang.Polls_delStats|sanitize}" src="{$imgpath}/chart_del_no.png" alt="" border="0" />
          {/if}
          {if perm('comments') && admin_active('comments')}
            {if $c->Comments>=1}
              <a class="colorbox stip" title="{$lang.Global_Comments|sanitize}" href="index.php?do=comments&amp;where=poll&amp;object={$c->Id}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/message.png" alt="" border="0" /></a>
              {else}
              <img class="absmiddle stip" title="{$lang.Global_Comments|sanitize}" src="{$imgpath}/message_no.png" alt="" border="0" />
            {/if}
          {/if}
          <a class="stip" title="{$lang.Polls_delete|sanitize}" onclick="return confirm('{#ConfirmGlobal#}{$c->Titel_1|jsspecialchars}');" href="index.php?do=poll&amp;sub=delete&amp;id={$c->Id}&amp;backurl={$backurl}"><img class="absmiddle" src="{$imgpath}/delete.png" alt="" border="0" /></a>
        </td>
      </tr>
    {/foreach}
  </table>
  <input name="save" type="hidden" id="save" value="1" />
  <input type="submit" class="button" value="{#Save#}" />
</form>
