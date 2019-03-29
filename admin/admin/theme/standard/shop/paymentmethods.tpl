<div class="header">{#Shop_payment_title#}</div>
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
        <td width="200" class="headers" nowrap="nowrap">{#Global_Name#}</td>
        <td width="140" align="center" class="headers" nowrap="nowrap">{#Global_Active#}</td>
        <td width="100" align="center" class="headers" nowrap="nowrap">{#Global_Position#}</td>
        <td width="130" align="center" class="headers" nowrap="nowrap"><img class="absmiddle stip" title="{$lang.Shop_paymentmethod_lgInf|sanitize}" src="{$imgpath}/help.png" alt="" /> {#Shop_paymentmethod_lg#}</td>
        <td class="headers">{#Global_Actions#}</td>
      </tr>
      {foreach from=$methods item=m}
        <tr class="{cycle values='first,second'}">
          <td>{$m->Name_1|sanitize}</td>
          <td align="center">
            <label><input type="radio" name="Aktiv[{$m->Id}]" value="1" {if $m->Aktiv == 1}checked="checked"{/if} /> {#Yes#}</label>
            <label><input type="radio" name="Aktiv[{$m->Id}]" value="0" {if $m->Aktiv == 0}checked="checked"{/if} /> {#No#}</label>
          </td>
          <td align="center"><input name="Position[{$m->Id}]" type="text" class="input" value="{$m->Position}" size="4" maxlength="3" /></td>
          <td align="center"><input style="width: 50px" name="MaxWert[{$m->Id}]" type="text" class="input" value="{$m->MaxWert}"></td>
          <td><a class="colorbox stip" title="{$lang.Shop_payment_edit|sanitize}"  href="index.php?do=shop&amp;sub=editpaymentmethod&amp;Id={$m->Id}&amp;lc=1&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/edit.png" alt="" border="" /></a></td>
        </tr>
      {/foreach}
    </table>
  </div>
  <br />
  <input type="submit" class="button" value="{#Save#}" />
  <input name="save" type="hidden" id="save" value="1" />
</form>
