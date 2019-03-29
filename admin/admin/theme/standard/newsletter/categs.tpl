<div class="header">{#Newsletter_Categs#}</div>
<div class="subheaders">
  {if $admin_settings.Ahelp == 1}
    <a class="colorbox" href="index.php?do=help&amp;sub={$helpquery}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/s_help.png" alt="" border="0" /> {#GlobalHelp#}</a>&nbsp;&nbsp;&nbsp;
    {/if}
  <a class="colorbox" href="index.php?do=support&amp;sub=send_order&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/send.png" alt="" border="0" /> {#SendOrder#}</a>
</div>
<form name="kform" method="post" action="">
  <table width="100%" cellpadding="4" cellspacing="0" border="0" class="tableborder">
    <tr>
      <td class="headers">{#Global_Name#}</td>
      <td width="100" class="headers">{#Global_descr#}</td>
      <td class="headers">{#Global_Actions#}</td>
    </tr>
    {foreach from=$Categs item=categ}
      <tr class="{cycle values="second,first"}">
        <td width="20%" valign="top"><input class="input" name="Name[{$categ->Id}]" type="text" id="name[{$categ->Id}]" value="{$categ->Name|sanitize}" size="40" /></td>
        <td><textarea cols="" rows="" class="input" id="b" name="Info[{$categ->Id}]" style="width: 300px; height: 50px">{$categ->Info|sanitize}</textarea></td>
        <td valign="top"><a class="stip" title="{$lang.Global_Delete|sanitize}" onclick="return confirm('{#ConfirmGlobal#}{$categ->Name|jsspecialchars}');" href="index.php?do=newsletter&amp;sub=deletecateg&amp;id={$categ->Id}"><img class="absmiddle" src="{$imgpath}/delete.png" alt="" border="0" /></a></td>
      </tr>
    {/foreach}
  </table>
  <input name="save" type="hidden" id="save" value="1" />
  <input type="submit" class="button" value="{#Save#}" />
</form>
<form method="post" action="">
  <fieldset>
    <legend>{#Newsletter_CategNew#}</legend>
    <label for="n"><strong>{#Global_Name#}</strong></label>
    <br />
    <input class="input" id="n" name="Name" type="text" value="" size="40" style="width: 300px" />
    <br />
    <label for="b"><strong>{#Global_descr#}</strong></label>
    <br />
    <textarea cols="" rows="" class="input" id="b" name="Info" style="width: 300px; height: 50px"></textarea>
    <br />
    <input type="hidden" name="new" value="1" />
    <input type="submit" class="button" value="{#Save#}" />
  </fieldset>
</form>
