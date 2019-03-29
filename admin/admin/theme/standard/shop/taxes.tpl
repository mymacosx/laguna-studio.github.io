<div class="header">{#Shop_taxes_title#}</div>
<div class="subheaders">
  {if $admin_settings.Ahelp == 1}
    <a class="colorbox" href="index.php?do=help&amp;sub={$helpquery}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/s_help.png" alt="" border="0" /> {#GlobalHelp#}</a>&nbsp;&nbsp;&nbsp;
    {/if}
  <a class="colorbox" href="index.php?do=support&amp;sub=send_order&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/send.png" alt="" border="0" /> {#SendOrder#}</a>
</div>
<form method="post" action="">
  <div class="maintable">
    <table width="100%" border="0" cellpadding="3" cellspacing="0" class="tableborder">
      <tr>
        <td width="200" class="headers">{#Global_Name#}</td>
        <td class="headers">{#Shop_payment_PR#}</td>
      </tr>
      {foreach from=$taxes item=t}
        <tr class="{cycle values='first,second'}">
          <td><input name="Name[{$t->Id}]" type="text" class="input" value="{$t->Name|sanitize}" size="30" /></td>
          <td><input name="Wert[{$t->Id}]" type="text" class="input" value="{$t->Wert|numf}" size="5" maxlength="5" /> %</td>
        </tr>
      {/foreach}
    </table>
  </div>
  <input type="submit" class="button" value="{#Save#}" />
  <input name="save" type="hidden" id="save" value="1" />
</form>
