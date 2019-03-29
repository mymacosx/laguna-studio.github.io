<script type="text/javascript">
<!-- //
{include file="$incpath/other/jsvalidate.tpl"}
$.validator.setDefaults({
    submitHandler: function() {
	document.forms['kform'].submit();
    }
});

$(document).ready(function() {
    $('#kform').validate({
	rules: {
            {if $new == 1}
            Name1: { required: true },
            {/if}
            E_Titel1: { required: true },
            E_Anlage: { required: true,range: [0,10] },
            E_Email: { required: true, email: true },
            E_Email2: { email: true }
        },
	messages: { }
    });
});
//-->
</script>

<form method="post" name="kform" id="kform" autocomplete="off" action="">
  <div class="subheaders"><strong>{#Global_props#}</strong></div>
  <table width="100%" border="0" cellspacing="0" cellpadding="3">
    <tr>
      <td width="250" class="row_left">{#Global_Name#} ({$language.name.1})</td>
      <td class="row_right"><input style="width: 400px" class="input" type="text" name="E_Titel1" value="{$res->Titel1|sanitize}" /></td>
    </tr>
    <tr>
      <td class="row_left">{#Global_Name#} ({$language.name.2})</td>
      <td class="row_right"><input style="width: 400px" class="input" type="text" name="E_Titel2" value="{$res->Titel2|sanitize}" /></td>
    </tr>
    <tr>
      <td class="row_left">{#Global_Name#} ({$language.name.3})</td>
      <td class="row_right"><input style="width: 400px" class="input" type="text" name="E_Titel3" value="{$res->Titel3|sanitize}" /></td>
    </tr>
    <tr>
      <td width="250" class="row_left">{#Content_text#} ({$language.name.1})</td>
      <td class="row_right"><textarea style="width: 400px" class="input" rows="5" name="E_Intro1">{$res->Intro1|sanitize}</textarea></td>
    </tr>
    <tr>
      <td class="row_left">{#Content_text#} ({$language.name.2})</td>
      <td class="row_right"><textarea style="width: 400px" class="input" rows="5" name="E_Intro2">{$res->Intro2|sanitize}</textarea></td>
    </tr>
    <tr>
      <td class="row_left">{#Content_text#} ({$language.name.3})</td>
      <td class="row_right"><textarea style="width: 400px" class="input" rows="5" name="E_Intro3">{$res->Intro3|sanitize}</textarea></td>
    </tr>
    <tr>
      <td class="row_left">{#Global_Active#}</td>
      <td class="row_right">
        <label><input type="radio" name="E_Aktiv" value="1" {if $res->Aktiv == 1 || $new == 1} checked="checked"{/if}/>{#Yes#}</label>
        <label><input type="radio" name="E_Aktiv" value="0" {if $res->Aktiv == 0 && $new != 1} checked="checked"{/if}/>{#No#}</label>
      </td>
    </tr>
    <tr>
      <td class="row_left">{#ContactForms_allowedAtt#}</td>
      <td class="row_right"><input name="E_Anlage" type="text" class="input" style="width: 40px" value="{$res->Anlage|default:0}" maxlength="2" /></td>
    </tr>
    <tr>
      <td class="row_left"><span class="stip" title="{$lang.ContactForms_emailInf|sanitize}"><img class="absmiddle" src="{$imgpath}/help.png" alt="" /></span> {#Global_Email#} </td>
      <td class="row_right"><input style="width: 400px" class="input" type="text" name="E_Email" value="{$res->Email|sanitize}" /></td>
    </tr>
    <tr>
      <td class="row_left">
        <span class="stip" title="{$lang.ContactForms_email2Inf|sanitize}"><img class="absmiddle" src="{$imgpath}/help.png" alt="" /></span>
        {#Global_Email#} {#Option#}
      </td>
      <td class="row_right"><input style="width: 400px" class="input" type="text" name="E_Email2" value="{$res->Email2|sanitize}" /></td>
    </tr>
    <tr>
      <td class="row_left"><span class="stip" title="{$lang.ContactForms_buttonInf|sanitize}"><img class="absmiddle" src="{$imgpath}/help.png" alt="" /></span> {#ContactForms_button#}</td>
      <td class="row_right"><input style="width: 400px" class="input" type="text" name="Button_Name" value="{$res->Button_Name|sanitize}" /></td>
    </tr>
    <tr>
      <td class="row_left"><span class="stip" title="{$lang.ContactForms_groupsInf|sanitize}"><img class="absmiddle" src="{$imgpath}/help.png" alt="" /></span> {#Groups_Name#}</td>
      <td class="row_right">
        <select name="E_Gruppen[]" size="5" multiple="multiple" class="input" style="width: 200px">
          {foreach from=$groups item=g}
            <option value="{$g->Id}" {if isset($new) && $new == 1}selected="selected"{else}{if in_array($g->Id, $res->Groups)}selected="selected"{/if}{/if}>{$g->Name_Intern|sanitize}</option>
          {/foreach}
        </select>
      </td>
    </tr>
  </table>
  {if $new != 1}
    <br />
    <div class="subheaders"><strong>{#ContactFormFEdit#}</strong></div>
    <table width="100%" border="0" cellspacing="0" cellpadding="3">
      <tr class="headers">
        <td class="headers">{#ContactFormFt#} ({$language.name.1})</td>
        <td class="headers">{$language.name.2}</td>
        <td class="headers">{$language.name.3}</td>
        <td class="headers">{#Global_Type#}</td>
        <td nowrap="nowrap" class="headers">{#Global_Values#} <span class="stip" title="{$lang.ContactForms_valInf|sanitize}"><img class="absmiddle" src="{$imgpath}/help.png" alt="" /></span></td>
        <td nowrap="nowrap" class="headers">{#Global_Must#} <span class="stip" title="{$lang.ContactForms_mustInf|sanitize}"><img class="absmiddle" src="{$imgpath}/help.png" alt="" /></span></td>
        <td nowrap="nowrap" class="headers">{#ContactForms_number#} <span class="stip" title="{$lang.ContactForms_numInf|sanitize}"><img class="absmiddle" src="{$imgpath}/help.png" alt="" /></span></td>
        <td nowrap="nowrap" class="headers">{#Global_Email#} <span class="stip" title="{$lang.ContactForms_mailInf|sanitize}"><img class="absmiddle" src="{$imgpath}/help.png" alt="" /></span></td>
        <td class="headers">{#Global_Position#}</td>
        <td align="center" class="headers"><input class="stip" title="{$lang.Global_SelAll|sanitize}" name="allbox" type="checkbox" id="d" onclick="multiCheck();" value="" /></td>
      </tr>
      {foreach from=$fields item=c}
        <tr class="{cycle values='second,first'}">
          <td><input class="input" style="width: 100px" name="Name1[{$c->Id}]" type="text" value="{$c->Name1|sanitize}" /></td>
          <td><input class="input" style="width: 100px" name="Name2[{$c->Id}]" type="text" value="{$c->Name2|sanitize}" /></td>
          <td><input class="input" style="width: 100px" name="Name3[{$c->Id}]" type="text" value="{$c->Name3|sanitize}" /></td>
          <td>
            <select class="input" style="width: 120px" name="Typ[{$c->Id}]" id="Typ[{$c->Id}]">
              <option value="textfield" {if $c->Typ == 'textfield'} selected="selected"{/if}>{#ContactForms_tfo#}</option>
              <option value="textarea" {if $c->Typ == 'textarea'} selected="selected"{/if}>{#ContactForms_tfm#}</option>
              <option value="dropdown" {if $c->Typ == 'dropdown'} selected="selected"{/if}>{#ContactForms_dd#}</option>
              <option value="checkbox" {if $c->Typ == 'checkbox'} selected="selected"{/if}>{#ContactForms_chb#}</option>
              <option value="radio" {if $c->Typ == 'radio'} selected="selected"{/if}>{#ContactForms_rad#}</option>
            </select>
          </td>
          <td><input class="input" style="width: 150px" name="Werte[{$c->Id}]" type="text" id="Werte{$c->Id}" value="{$c->Werte}" /></td>
          <td>
            <select  class="input" style="width: 50px" name="Pflicht[{$c->Id}]">
              <option value="1" {if $c->Pflicht == 1} selected="selected"{/if}>{#Yes#}</option>
              <option value="0" {if $c->Pflicht == 0} selected="selected"{/if}>{#No#}</option>
            </select>
          </td>
          <td>
            <select  class="input" style="width: 50px" name="Zahl[{$c->Id}]">
              <option value="1" {if $c->Zahl == 1} selected="selected"{/if}>{#Yes#}</option>
              <option value="0" {if $c->Zahl == 0} selected="selected"{/if}>{#No#}</option>
            </select>
          </td>
          <td>
            <select  class="input" style="width: 50px" name="Email[{$c->Id}]">
              <option value="1" {if $c->Email == 1} selected="selected"{/if}>{#Yes#}</option>
              <option value="0" {if $c->Email == 0} selected="selected"{/if}>{#No#}</option>
            </select>
          </td>
          <td><input name="Posi[{$c->Id}]" type="text" class="input" value="{$c->Posi}" size="2" maxlength="2" /></td>
          <td><input class="stip" title="{$lang.DelInfChekbox|sanitize}" name="del[{$c->Id}]" type="checkbox" value="1" /></td>
        </tr>
      {/foreach}
    </table>
    <input type="submit" class="button" value="{#Global_Save_Del#}" />
    <input name="save" type="hidden" id="save" value="1" />
    <input type="button" onclick="parent.location.href = parent.location;" class="button" value="{#Close#}" />
  </form>
  <br />
  <br />
{/if}
<div class="subheaders"><strong>{#ContactForms_newfield#}</strong></div>
<form method="post" action="">
  <table width="100%" border="0" cellspacing="0" cellpadding="3">
    <tr class="headers">
      <td class="headers">{#ContactFormFt#} ({$language.name.1})</td>
      <td class="headers">{$language.name.2}</td>
      <td class="headers">{$language.name.3}</td>
      <td class="headers">{#Global_Type#}</td>
      <td nowrap="nowrap" class="headers">{#Global_Values#} <span class="stip" title="{$lang.ContactForms_valInf|sanitize}"><img class="absmiddle" src="{$imgpath}/help.png" alt="" /></span></td>
      <td nowrap="nowrap" class="headers">{#Global_Must#} <span class="stip" title="{$lang.ContactForms_mustInf|sanitize}"><img class="absmiddle" src="{$imgpath}/help.png" alt="" /></span></td>
      <td nowrap="nowrap" class="headers">{#ContactForms_number#} <span class="stip" title="{$lang.ContactForms_numInf|sanitize}"><img class="absmiddle" src="{$imgpath}/help.png" alt="" /></span></td>
      <td nowrap="nowrap" class="headers">{#Global_Email#} <span class="stip" title="{$lang.ContactForms_mailInf|sanitize}"><img class="absmiddle" src="{$imgpath}/help.png" alt="" /></span></td>
      <td class="headers">{#Global_Position#}</td>
    </tr>
    <tr class="second">
      <td><input name="Name1" type="text" class="input" id="Name1" style="width: 100px" value="{#ContactForms_ndef#}" /></td>
      <td><input name="Name2" type="text" class="input" id="Name2" style="width: 100px" value="{#ContactForms_ndef#}" /></td>
      <td><input name="Name3" type="text" class="input" id="Name3" style="width: 100px" value="{#ContactForms_ndef#}" /></td>
      <td>
        <select class="input" style="width: 120px" name="Typ" id="Typ">
          <option value="textfield">{#ContactForms_tfo#}</option>
          <option value="textarea" {if $new == 1}selected="selected"{/if}>{#ContactForms_tfm#}</option>
          <option value="dropdown">{#ContactForms_dd#}</option>
          <option value="checkbox">{#ContactForms_chb#}</option>
          <option value="radio">{#ContactForms_rad#}</option>
        </select>
      </td>
      <td><input class="input" style="width: 150px" name="Werte" type="text" /></td>
      <td>
        <select  class="input" style="width: 50px" name="Pflicht">
          <option value="1" {if $new == 1}selected="selected"{/if}>{#Yes#}</option>
          <option value="0" {if $new != 1}selected="selected"{/if}>{#No#}</option>
        </select>
      </td>
      <td>
        <select  class="input" style="width: 50px" name="Zahl">
          <option value="1">{#Yes#}</option>
          <option value="0" selected="selected">{#No#}</option>
        </select>
      </td>
      <td>
        <select  class="input" style="width: 50px" name="Email">
          <option value="1">{#Yes#}</option>
          <option value="0" selected="selected">{#No#}</option>
        </select>
      </td>
      <td><input name="Posi" type="text" class="input" value="{$c->Posi|default:1}" size="2" maxlength="2" /></td>
    </tr>
  </table>
  <input type="submit" class="button" value="{#Save#}" {if $new == 1}onclick="window.opener.location.reload();"{/if} />
  <input name="new" type="hidden" value="1" />
  <input type="button" onclick="parent.location.href = parent.location;" class="button" value="{#Close#}" />
</form>
