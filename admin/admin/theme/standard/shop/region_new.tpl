<script type="text/javascript">
<!-- //
{include file="$incpath/other/jsvalidate.tpl"}
$.validator.setDefaults({
    submitHandler: function() {
        document.forms['regions'].submit();
    }
});

$(document).ready(function() {
    $('#regions').validate( {
	rules: {
            Code: { required: true, minlength: 2 },
            Name: { required: true }
        }
    });
});
//-->
</script>

<div class="header">{#Global_Add#}</div>
<form id="regions" name="regions" action="" method="post">
  <div class="maintable">
    <table width="100%" border="0" cellpadding="3" cellspacing="0" class="tableborder">
      <tr class="firstrow">
        <td class="headers">{#RegionCode#}</td>
        <td class="headers">{#Global_Name#}</td>
        <td class="headers stip" title="{$lang.Shop_Settings_ShippFreeInf|sanitize}">{#Shop_Settings_ShippingFree#} <img  class="absmiddle" src="{$imgpath}/help.png" alt="" /></td>
        <td align="center" class="headers">{#Settings_countries_taxable#}</td>
        <td class="headers">{#Global_Active#}</td>
      </tr>
      <tr class="{cycle values='first,second'}">
        <td width="40"><input class="input" name="Code" id="Code" type="text" value="{$smarty.request.Code}" size="10" maxlength="2" /></td>
        <td width="250"><input class="input" name="Name" id="Name" type="text" value="{$smarty.request.Name}" size="40" /></td>
        <td width="160" class="stip" title="{$lang.Shop_Settings_ShippFreeInf|sanitize}">
          <input class="input" name="VersandFreiAb" type="text" value="{$smarty.request.VersandFreiAb|default: 0}" style="width: 100px" />
        </td>
        <td width="180" align="center">
          <label><input type="radio" name="Ust" value="1" {if empty($smarty.request.Ust) || $smarty.request.Ust == 1}checked="checked"{/if} />{#Shop_Settings_priceB#}</label>
          <label><input type="radio" name="Ust" value="2" {if isset($smarty.request.Ust) && $smarty.request.Ust == 2}checked="checked"{/if} />{#Shop_Settings_priceN#}</label>
        </td>
        <td width="90">
          <label><input type="radio" name="Aktiv" value="1" {if empty($smarty.request.Aktiv) || $smarty.request.Aktiv == 1}checked="checked"{/if} />{#Yes#}</label>
          <label><input type="radio" name="Aktiv" value="2" {if isset($smarty.request.Aktiv) && $smarty.request.Aktiv == 2}checked="checked"{/if} />{#No#}</label>
        </td>
      </tr>
    </table>
  </div>
  <br />
  <input name="save" type="hidden" id="save" value="1" />
  <input name="page" type="hidden" id="page" value="{$smarty.request.page}" />
  <input type="submit" value="{#Save#}" class="button" />
  <input type="button" onclick="closeWindow(true);" class="button" value="{#Close#}" />
</form>
