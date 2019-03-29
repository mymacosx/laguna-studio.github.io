<div class="header">{#Shop_shippingready_title#}</div>
<div class="subheaders">
  {if $admin_settings.Ahelp == 1}
    <a class="colorbox" href="index.php?do=help&amp;sub={$helpquery}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/s_help.png" alt="" border="0" /> {#GlobalHelp#}</a>&nbsp;&nbsp;&nbsp;
    {/if}
  <a class="colorbox" href="index.php?do=support&amp;sub=send_order&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/send.png" alt="" border="0" /> {#SendOrder#}</a>
</div>
<form method="post" action="" name="kform">
  <div class="maintable">
    <table width="100%" border="0" cellpadding="3" cellspacing="0" class="tableborder">
      <tr>
        <td width="100" class="headers">&nbsp;</td>
        <td width="100" class="headers">{$language.name.1}</td>
        <td width="100" class="headers">{$language.name.2}</td>
        <td width="100" class="headers">{$language.name.3}</td>
        <td class="headers"><img class="absmiddle" src="{$imgpath}/delete.png" alt="" border="0" /></td>
      </tr>
      {foreach from=$items item=sr}
        <tr class="{cycle values='first,second'}">
          <td>{#Shop_shippingready_ready#}</td>
          <td><input class="input" type="text" name="Lieferzeit_1[{$sr->Id}]" value="{$sr->Lieferzeit_1|sanitize}" /></td>
          <td><input class="input" type="text" name="Lieferzeit_2[{$sr->Id}]" value="{$sr->Lieferzeit_2|sanitize}" /></td>
          <td><input class="input" type="text" name="Lieferzeit_3[{$sr->Id}]" value="{$sr->Lieferzeit_3|sanitize}" /></td>
          <td><label class="stip" title="{$lang.Global_Delete|sanitize}"><input name="Del[{$sr->Id}]" type="checkbox" id="Del[]" value="1" /></label></td>
        </tr>
      {/foreach}
    </table>
  </div>
  <input type="submit" name="button" class="button" value="{#Save#}" />
  <input name="save" type="hidden" id="save" value="1" />
</form>
<fieldset>
  <legend>{#Global_Datasheet#}</legend>
  <form method="post" name="kforma" action="">
    <table width="100%" border="0" cellspacing="0" cellpadding="4">
      <tr>
        <td width="100" class="headers">&nbsp;</td>
        <td width="100" class="headers">{$language.name.1}</td>
        <td width="100" class="headers">{$language.name.2}</td>
        <td class="headers">{$language.name.3}</td>
      </tr>
      <tr class="second">
        <td>{#Shop_shippingready_ready#}</td>
        <td><input class="input" type="text" name="Lieferzeit_1" /></td>
        <td><input class="input" type="text" name="Lieferzeit_2" /></td>
        <td><input class="input" type="text" name="Lieferzeit_3" /></td>
      </tr>
    </table>
    <input type="submit" name="button" class="button" value="{#Save#}" />
    <input name="new" type="hidden" value="1" />
  </form>
</fieldset>
