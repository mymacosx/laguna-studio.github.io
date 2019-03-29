<div class="header">{#Shop_units_title#}</div>
<div class="subheaders">
  {if $admin_settings.Ahelp == 1}
    <a class="colorbox" href="index.php?do=help&amp;sub={$helpquery}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/s_help.png" alt="" border="0" /> {#GlobalHelp#}</a>&nbsp;&nbsp;&nbsp;
    {/if}
  <a class="colorbox" href="index.php?do=support&amp;sub=send_order&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/send.png" alt="" border="0" /> {#SendOrder#}</a>
</div>
<form method="post" action="" name="kform">
  <table id="tablesorter" class="tablesorter" width="100%" border="0" cellspacing="0" cellpadding="4">
    <tr>
      <td class="headers" width="180">{#Shop_units_em#} ({$language.name.1})</td>
      <td class="headers" width="180">{#Shop_units_em#} ({$language.name.2})</td>
      <td class="headers" width="180">{#Shop_units_em#} ({$language.name.3})</td>
      <td class="headers"><label class="stip" title="{$lang.Global_SelAll|sanitize}"><input name="allbox" type="checkbox" id="d" onclick="multiCheck();" value="" /><img class="absmiddle" src="{$imgpath}/delete.png" alt="" border="0" /></label></td>
    </tr>
    {foreach from=$units item=u}
      <tr class="{cycle values='first,second'}">
        <td align="center" class="row_spacer">
          <input type="text" class="input" style="width: 70px" name="Titel_1[{$u->Id}]" value="{$u->Titel_1|sanitize}" />
          /
          <input type="text" class="input" style="width: 70px" name="Mz_1[{$u->Id}]" value="{$u->Mz_1|sanitize}" />
        </td>
        <td align="center" class="row_spacer">
          <input type="text" class="input" style="width: 70px" name="Titel_2[{$u->Id}]" value="{$u->Titel_2|sanitize}" />
          /
          <input type="text" class="input" style="width: 70px" name="Mz_2[{$u->Id}]" value="{$u->Mz_2|sanitize}" />
        </td>
        <td align="center" class="row_spacer">
          <input type="text" class="input" style="width: 70px" name="Titel_3[{$u->Id}]" value="{$u->Titel_3|sanitize}" />
          /
          <input type="text" class="input" style="width: 70px" name="Mz_3[{$u->Id}]" value="{$u->Mz_3|sanitize}" />
        </td>
        <td><label class="stip" title="{$lang.Global_Delete|sanitize}"><input class="absmiddle" name="Del[{$u->Id}]" type="checkbox" id="Del[]" value="1" /><img class="absmiddle" src="{$imgpath}/delete.png" alt="" border="0" /></label></td>
      </tr>
    {/foreach}
  </table>
  <input type="submit" class="button" value="{#Save#}" />
  <input name="save" type="hidden" id="save" value="1" />
</form>
<fieldset>
  <legend>{#Global_Datasheet#}</legend>
  <form method="post" name="kforma" action="">
    <table width="100%" border="0" cellspacing="0" cellpadding="4">
      <tr>
        <td width="60" class="headers">&nbsp;</td>
        <td width="180" class="headers">{#Shop_units_em#} ({$language.name.1})</td>
        <td width="180" class="headers">{#Shop_units_em#} ({$language.name.2})</td>
        <td class="headers">{#Shop_units_em#} ({$language.name.3})</td>
      </tr>
      <tr class="second">
        <td>{#Shop_units_simple#}</td>
        <td>
          <input class="input" type="text" style="width: 70px" name="Titel_1" />
          <input class="input" type="text" style="width: 70px" name="Mz_1" />
        </td>
        <td>
          <input class="input" type="text" style="width: 70px" name="Titel_2" />
          <input class="input" type="text" style="width: 70px" name="Mz_2" />
        </td>
        <td>
          <input class="input" type="text" style="width: 70px" name="Titel_3" />
          <input class="input" type="text" style="width: 70px" name="Mz_3" />
        </td>
      </tr>
    </table>
    <input type="submit" name="button" class="button" value="{#Save#}" />
    <input name="new" type="hidden" value="1" />
  </form>
</fieldset>
