<script type="text/javascript">
<!-- //
{include file="$incpath/other/jsvalidate.tpl"}
$.validator.setDefaults({
    submitHandler: function() {
	document.forms['new'].submit();
    }
});

$(document).ready(function() {
    $('#new').validate({
	rules: {
	    Name_1: { required: true, minlength: 2 },
	    Position: { required: true, range: [1,50] },
	    Dokument: { required: true }
	},
	messages:{}
    });
});
//-->
</script>

<div class="header">{#Quicknavi#}</div>
<div class="subheaders">
  {if $admin_settings.Ahelp == 1}
    <a class="colorbox" href="index.php?do=help&amp;sub={$helpquery}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/s_help.png" alt="" border="0" /> {#GlobalHelp#}</a>&nbsp;&nbsp;&nbsp;
    {/if}
  <a class="colorbox" href="index.php?do=support&amp;sub=send_order&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/send.png" alt="" border="0" /> {#SendOrder#}</a>
</div>
<form action="" method="post">
  <table  width="100%" border="0" cellspacing="0" cellpadding="4">
    <tr>
      <td width="70" class="headers">{$language.name.1}</td>
      <td width="70" class="headers">{$language.name.2}</td>
      <td width="70" class="headers">{$language.name.3}</td>
      <td width="150" class="headers">{#Navigation_doc#}</td>
      <td width="5" class="headers">{#LoginSection#}</td>
      <td width="5" align="center" class="headers">{#Global_Position#}</td>
      <td width="5" align="center" class="headers">{#Global_Target#}</td>
      <td width="5" align="center" class="headers">{#Global_Active#}</td>
      <td class="headers"><img class="absmiddle" src="{$imgpath}/delete.png" alt="" border="0" /></td>
    </tr>
    {foreach from=$navis item=c}
      {assign var=pos value=$pos+1}
      <tr class="{cycle values='second,first'}">
        <td>
          <input type="hidden" name="nid[{$c->Id}]" value="{$c->Id}" />
          <input class="input" name="Name_1[{$c->Id}]" type="text" value="{$c->Name_1|sanitize}" style="width: 80px" />
        </td>
        <td><input class="input" name="Name_2[{$c->Id}]" type="text" value="{$c->Name_2|sanitize}" style="width: 80px" /></td>
        <td><input class="input" name="Name_3[{$c->Id}]" type="text" value="{$c->Name_3|sanitize}" style="width: 80px" /></td>
        <td><input class="input" name="Dokument[{$c->Id}]" type="text" value="{$c->Dokument|sanitize}" style="width: 150px" /></td>
        <td width="5" align="center">
          <select  style="width: 100px" class="input" name="Gruppe[{$c->Id}]">
            <option value="">-</option>
            {foreach from=$aGroups key=k item=v}
              <option {if $c->Gruppe == $k}selected="selected"{/if} value="{$k}">{$v}</option>
            {/foreach}
          </select>
        </td>
        <td width="5" align="center"><input class="input" name="Position[{$c->Id}]" type="text" size="3" maxlength="3" value="{$c->Position}" /></td>
        <td align="center">
          <select  style="width: 100px" class="input" name="Ziel[{$c->Id}]">
            <option value="_self" {if $c->Ziel == '_self'}selected="selected"{/if}>{#Navigation_self#}</option>
            <option value="_blank" {if $c->Ziel == '_blank'}selected="selected"{/if}>{#Navigation_new#}</option>
          </select>
        </td>
        <td align="center">
          <select  style="width: 50px" class="input" name="Aktiv[{$c->Id}]">
            <option value="1" {if $c->Aktiv == 1}selected="selected"{/if}>{#Yes#}</option>
            <option value="0" {if $c->Aktiv == 0}selected="selected"{/if}>{#No#}</option>
          </select>
        </td>
        <td><input class="stip" title="{$lang.DelInfChekbox|sanitize}" name="del[{$c->Id}]" type="checkbox" value="1" /></td>
      </tr>
    {/foreach}
  </table>
  <input type="submit" class="button" value="{#Save#}" />
  <input name="save" type="hidden" id="save" value="1" />
</form>
<br />
<br />
<form method="post" action="" autocomplete="off" name="new" id="new">
  {assign var=newpos value=$pos+1}
  <div class="header">{#Quicknavi_new#}</div>
  <table width="100%" border="0" cellpadding="5" cellspacing="0">
    <tr>
      <td width="150" class="row_left">{#Global_Name#} ({$language.name.1})</td>
      <td class="row_right"><input class="input" name="Name_1" type="text"size="30" /></td>
    </tr>
    <tr>
      <td width="150" class="row_left">{#Global_Name#}  ({$language.name.2})</td>
      <td class="row_right"><input class="input" name="Name_2" type="text"size="30" /></td>
    </tr>
    <tr>
      <td width="150" class="row_left">{#Global_Name#}  ({$language.name.3})</td>
      <td class="row_right"><input class="input" name="Name_3" type="text"size="30" /></td>
    </tr>
    <tr>
      <td class="row_left">{#Navigation_doc#}</td>
      <td class="row_right"><input class="input" name="Dokument" type="text"size="30" /></td>
    </tr>
    <tr>
      <td class="row_left">{#Global_Position#}</td>
      <td class="row_right"><input name="Position" type="text" class="input"size="4" maxlength="3" value="{$newpos}" /></td>
    </tr>
    <tr>
      <td class="row_left">{#Global_Active#}</td>
      <td class="row_right">
        <label><input name="Aktiv" type="radio" value="1" checked="checked" />{#Yes#}</label>
        <label><input type="radio" name="Aktiv" value="0" />{#No#}</label>
      </td>
    </tr>
  </table>
  <br />
  <input type="submit" class="button" value="{#Save#}" />
  <input name="new" type="hidden" id="new" value="1" />
</form>
