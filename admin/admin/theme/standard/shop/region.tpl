<div class="header">{#Settings_countries_title#}</div>
<div class="subheaders">
  {if $admin_settings.Ahelp == 1}
    <a class="colorbox" href="index.php?do=help&amp;sub={$helpquery}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/s_help.png" alt="" border="0" /> {#GlobalHelp#}</a>&nbsp;&nbsp;&nbsp;
    {/if}
  <a class="colorbox" href="index.php?do=support&amp;sub=send_order&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/send.png" alt="" border="0" /> {#SendOrder#}</a>
</div>
<form action="" method="post">
  <div class="maintable">
    <table width="100%" border="0" cellpadding="3" cellspacing="0" class="tableborder">
      <tr class="firstrow">
        <td class="headers">{#RegionCode#}</td>
        <td class="headers">{#Global_Name#}</td>
        <td class="headers stip" title="{$lang.Shop_Settings_ShippFreeInf|sanitize}">{#Shop_Settings_ShippingFree#} <img  class="absmiddle" src="{$imgpath}/help.png" alt="" /></td>
        <td align="center" class="headers">{#Settings_countries_taxable#}</td>
        <td class="headers">{#Global_Active#}</td>
        <td class="headers">&nbsp;</td>
      </tr>
      {foreach from=$items item=c}
        <tr class="{cycle values='first,second'}">
          <td width="80"><input class="input" name="Code[{$c->Id}]" type="text" value="{$c->Code}" size="10" maxlength="2" /></td>
          <td width="250"><input class="input" name="Name[{$c->Id}]" type="text" value="{$c->Name}" size="40" /></td>
          <td width="160" class="stip" title="{$lang.Shop_Settings_ShippFreeInf|sanitize}">
            <input class="input" name="VersandFreiAb[{$c->Id}]" type="text" value="{$c->VersandFreiAb}" style="width: 100px" />
          </td>
          <td width="220" align="center">
            <label><input type="radio" name="Ust[{$c->Id}]" value="1" {if $c->Ust == 1}checked="checked"{/if} />{#Shop_Settings_priceB#}</label>
            <label><input type="radio" name="Ust[{$c->Id}]" value="2" {if $c->Ust != 1}checked="checked"{/if} />{#Shop_Settings_priceN#}</label>
          </td>
          <td width="120">
            <label><input type="radio" name="Aktiv[{$c->Id}]" value="1" {if $c->Aktiv == 1}checked="checked"{/if} />{#Yes#}</label>
            <label><input type="radio" name="Aktiv[{$c->Id}]" value="2" {if $c->Aktiv != 1}checked="checked"{/if} />{#No#}</label>
          </td>
          <td>
            <a class="stip" title="{$lang.Global_Delete}" onclick="return confirm('{#ConfirmGlobal#}{$c->Name|jsspecialchars}');" href="index.php?do=shop&amp;sub=del_region&amp;id={$c->Id}&amp;backurl={$backurl}"><img class="absmiddle" src="{$imgpath}/delete.png" alt="" border="0" /></a>
          </td>
        </tr>
      {/foreach}
    </table>
  </div>
  <br />
  <input name="save" type="hidden" id="save" value="1" />
  <input name="page" type="hidden" id="page" value="{$smarty.request.page}" />
  <input type="submit" value="{#Save#}" class="button" />
  <input type="button" onclick="newWindow('index.php?do=shop&sub=add_region&noframes=1', '90%', 500);" class="button" value="{#Global_Add#}" />
</form>
<div class="navi_div"><strong>{#GoPagesSimple#}</strong>
  <form method="get" action="index.php">
    <input type="text" class="input" style="width: 25px; text-align: center" name="page" value="{$smarty.request.page|default:'1'}" />
    <input type="hidden" name="do" value="shop" />
    <input type="hidden" name="sub" value="regions" />
    <input type="submit" class="button" value="{#GoPagesButton#}" />
  </form>
  &nbsp;&nbsp;
  {if !empty($navi)}
    <strong>{#GoPages#}</strong>
    {$navi}
  {/if}
</div>
