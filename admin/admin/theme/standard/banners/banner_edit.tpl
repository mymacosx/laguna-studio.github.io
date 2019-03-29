<script type="text/javascript">
<!-- //
{include file="$incpath/other/jsvalidate.tpl"}
$.validator.setDefaults({
    submitHandler: function() {
	document.forms['bannf'].submit();
    }
});

$(document).ready(function() {
    $('#bannf').validate({
	rules: {
	    Name: { required: true },
	    HTML_Code: { required: true },
	    Anzeigen: { required: true, number: true },
	    Anzeigen_Max: { required: true, number: true }
	},
	messages: { }
    });
});
//-->
</script>

<div class="subheaders">{#BannersInf#}</div>
<form method="post" action="" name="bannf" id="bannf">
  <table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td width="150" class="row_left">{#Global_Name#}</td>
      <td class="row_right"><input class="input" style="width: 200px" type="text" name="Name" value="{$res->Name|sanitize}" /></td>
    </tr>
    <tr>
      <td class="row_left">{#BannersCode#}</td>
      <td class="row_right"><div onclick="javascript: void(0);" id="bann">{$res->HTML_Code}</div>
        <textarea cols="" rows="" name="HTML_Code" class="input" style="font-family: 'Courier New', Courier, monospace; font-size: 12px; width: 600px; height: 150px" onchange="document.getElementById('bann').innerHTML=this.value;" onclick="document.getElementById('bann').innerHTML=this.value;" onkeydown="document.getElementById('bann').innerHTML= this.value;">{$res->HTML_Code|sanitize}</textarea></td>
    </tr>
    <tr>
      <td class="row_left"> {#Global_Categ#}</td>
      <td class="row_right">
        <select name="Kategorie" class="input" style="width: 100px">
          {foreach from=$banner_categs item=c}
            <option value="{$c->Id|sanitize}" {if $c->Id == $res->Kategorie}selected="selected" {/if}>{$c->Name|sanitize}</option>
          {/foreach}
        </select>
      </td>
    </tr>
    <tr>
      <td class="row_left">{#Global_Active#}</td>
      <td class="row_right">
        <select name="Aktiv" class="input" style="width: 100px">
          <option value="1" {if $res->Aktiv == 1}selected="selected" {/if}>{#Yes#}</option>
          <option value="0" {if $res->Aktiv == 0}selected="selected" {/if}>{#No#}</option>
        </select>
      </td>
    </tr>
    <tr>
      <td class="row_left">{#BannersWeight#}</td>
      <td class="row_right">
        <select name="Gewicht" class="input" style="width: 100px">
          <option value="1" {if $res->Gewicht == 1}selected="selected"{/if}>1</option>
          <option value="2" {if $res->Gewicht == 2}selected="selected"{/if}>2</option>
          <option value="3" {if $res->Gewicht == 3}selected="selected"{/if}>3</option>
        </select>
      </td>
    </tr>
    <tr>
      <td class="row_left">{#BannersViews#}</td>
      <td class="row_right"><input type="text" class="input" name="Anzeigen" style="width: 40px" value="{$res->Anzeigen}" /></td>
    </tr>
    <tr>
      <td class="row_left">{#BannersViewsMax#}</td>
      <td class="row_right"><input type="text" class="input" name="Anzeigen_Max" style="width: 40px" value="{$res->Anzeigen_Max}" /></td>
    </tr>
  </table>
  <input name="save" type="hidden" id="save" value="1" />
  <input type="submit" class="button" value="{#Save#}" />
  <input type="button" onclick="closeWindow(true);" class="button" value="{#Close#}" />
</form>
