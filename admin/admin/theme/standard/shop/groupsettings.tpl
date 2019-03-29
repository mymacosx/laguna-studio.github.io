<div class="header">{#Shop_groupsettings#}</div>
<div class="subheaders">
  {if $admin_settings.Ahelp == 1}
    <a class="colorbox" href="index.php?do=help&amp;sub={$helpquery}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/s_help.png" alt="" border="0" /> {#GlobalHelp#}</a>&nbsp;&nbsp;&nbsp;
    {/if}
  <a class="colorbox" href="index.php?do=support&amp;sub=send_order&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/send.png" alt="" border="0" /> {#SendOrder#}</a>
</div>
<form method="post" action="">
  <table id="tablesorter" class="tablesorter" width="100%" border="0" cellspacing="0" cellpadding="3">
    <tr>
      <td class="headers" width="150">{#Shop_usergroup#}</td>
      <td class="headers" width="130">{#Shop_reduction#}</td>
      <td class="headers" width="100">{#Shop_customertype#}</td>
      <td class="headers">&nbsp;</td>
    </tr>
    {foreach from=$groups item=m}
      <tr class="{cycle values='first,second'}">
        <td>{$m->Name_Intern|sanitize}</td>
        <td align="center">
          <input type="hidden" name="Gruppe[{$m->Id}]" value="{$m->Id}" />
          <input name="Rabatt[{$m->Id}]" type="text" class="input" value="{$m->Rabatt|numf}" size="8" /> %
        </td>
        <td>
          <label><input type="radio" name="ShopAnzeige[{$m->Id}]" value="b2c" {if $m->ShopAnzeige == 'b2c'}checked="checked"{/if} />{#B2C_Group#}</label>
          <label><input type="radio" name="ShopAnzeige[{$m->Id}]" value="b2b" {if $m->ShopAnzeige == 'b2b'}checked="checked"{/if} />{#B2B_Group#}</label>
        </td>
        <td>&nbsp;</td>
      </tr>
    {/foreach}
  </table>
  <input class="button" type="submit" value="{#Save#}" />
  <input name="save" type="hidden" id="save" value="1" />
</form>
