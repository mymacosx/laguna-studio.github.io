<script type="text/javascript">
<!-- //
{include file="$incpath/other/jsvalidate.tpl"}
$.validator.setDefaults({
    submitHandler: function() {
        document.forms['pollform'].submit();
    }
});

$(document).ready(function() {
    $('#Start, #Ende').datepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat: 'dd.mm.yy',
        dayNamesMin: [{#Calendar_daysmin#}],
        monthNamesShort: [{#Calendar_monthNamesShort#}],
        firstDay: 1
    });

    $('#pollform').validate({
	rules: {
	    'Gruppen[]': { required: true },
	    Titel_1: { required: true },
	    Start: { required: true },
	    Ende: { required: true }
	},
	messages: {
	  'Gruppen[]': { required: '{#Validate_requiredSel#}' }
	}
    });
});
//-->
</script>

<div class="subheaders">{#Global_props#}</div>
<form method="post" name="pollform" id="pollform" action="">
  <table width="100%" border="0" cellspacing="0" cellpadding="3">
    <tr>
      <td width="180" class="row_left">{#Global_Name#} ({$language.name.1})</td>
      <td class="row_right"><input style="width: 200px" class="input" type="text" name="Titel_1" value="{$res->Titel_1|sanitize}" /></td>
    </tr>
    <tr>
      <td class="row_left">{#Global_Name#} ({$language.name.2})</td>
      <td class="row_right"><input style="width: 200px" class="input" type="text" name="Titel_2" value="{$res->Titel_2|sanitize}" /></td>
    </tr>
    <tr>
      <td class="row_left">{#Global_Name#} ({$language.name.3})</td>
      <td class="row_right"><input style="width: 200px" class="input" type="text" name="Titel_3" value="{$res->Titel_3|sanitize}" /></td>
    </tr>
    <tr>
      <td class="row_left"><span class="stip" title="{$lang.Polls_groupsInf|sanitize}"><img class="absmiddle" src="{$imgpath}/help.png" alt="" /></span> {#Groups_Name#}</td>
      <td class="row_right">
        <select name="Gruppen[]" size="5" multiple="multiple" class="input" style="width: 200px">
          {foreach from=$groups item=g}
            <option value="{$g->Id}" {if $new == 1}selected="selected"{else}{if in_array($g->Id,$res->Groups)}selected="selected"{/if}{/if}>{$g->Name_Intern|sanitize}</option>
          {/foreach}
        </select>
      </td>
    </tr>
    <tr>
      <td class="row_left"> {#Global_Published#}</td>
      <td class="row_right"><input class="input" style="width: 65px" type="text" name="Start" id="Start" value="{$res->Start|date_format: "%d.%m.%Y"}" readonly="readonly" /></td>
    </tr>
    <tr>
      <td class="row_left">{#Global_PubEnd#}</td>
      <td class="row_right"><input class="input" style="width: 65px" type="text" name="Ende" id="Ende" value="{$res->Ende|date_format: "%d.%m.%Y"}" readonly="readonly" /></td>
    </tr>
    <tr>
      <td class="row_left">{#Polls_amulti#}</td>
      <td class="row_right">
        <label><input type="radio" name="Multi" value="1" {if $res->Multi == 1} checked="checked"{/if}/>{#Yes#}</label>
        <label><input type="radio" name="Multi" value="0" {if $res->Multi == 0} checked="checked"{/if}/>{#No#}</label>
      </td>
    </tr>
    <tr>
      <td class="row_left">{#Polls_acomments#}</td>
      <td class="row_right">
        <label><input type="radio" name="Kommentare" value="1" {if $res->Kommentare == 1} checked="checked"{/if}/>{#Yes#}</label>
        <label><input type="radio" name="Kommentare" value="0" {if $res->Kommentare == 0} checked="checked"{/if}/>{#No#}</label>
      </td>
    </tr>
  </table>
  <input type="submit" class="button" value="{if $new == 1}{#Polls_new#}{else}{#Save#}{/if}" />
  <input name="{if $new == 1}save{else}update_settings{/if}" type="hidden" value="1" />
  <input type="button" onclick="parent.location.href = parent.location;" class="button" value="{#Close#}" />
</form>
{if $new != 1}
  {if $items}
    <div class="subheaders">{#Polls_editQuests#}</div>
    <form method="post" action="" name="kform">
      <table width="100%" border="0" cellspacing="0" cellpadding="3">
        <tr class="headers">
          <td width="120" class="headers">{$language.name.1}</td>
          <td width="120" class="headers">{$language.name.2}</td>
          <td width="120" class="headers">{$language.name.3}</td>
          <td width="80" class="headers">{#Polls_color#}</td>
          <td width="100" class="headers">{#Global_Position#}</td>
          <td class="headers"><label><input name="allbox" type="checkbox" id="d" onclick="multiCheck();" value="" />{#Global_SelAll#}</label></td>
        </tr>
        {foreach from=$items item=c}
          <tr class="{cycle values='second,first'}">
            <td>
              <input type="hidden" name="poll[{$c->Id}]" value="{$c->Id}" />
              <input class="input" style="width: 120px" type="text" name="Frage_1[{$c->Id}]" value="{$c->Frage_1|sanitize}" />
            </td>
            <td><input class="input" style="width: 120px" type="text" name="Frage_2[{$c->Id}]" value="{$c->Frage_2|sanitize}" /></td>
            <td><input class="input" style="width: 120px" type="text" name="Frage_3[{$c->Id}]" value="{$c->Frage_3|sanitize}" /></td>
            <td>
              <select class="input" name="Farbe[{$c->Id}]" id="Farbe">
                <option value="blau" {if $c->Farbe == 'blau'}selected="selected"{/if}>{#Color_blue#}</option>
                <option value="grau" {if $c->Farbe == 'grau'}selected="selected"{/if}>{#Color_grey#}</option>
                <option value="gruen" {if $c->Farbe == 'gruen'}selected="selected"{/if}>{#Color_green#}</option>
                <option value="mintgruen" {if $c->Farbe == 'mintgruen'}selected="selected"{/if}>{#Color_mintgreen#}</option>
                <option value="orange" {if $c->Farbe == 'orange'}selected="selected"{/if}>{#Color_orange#}</option>
                <option value="rot" {if $c->Farbe == 'rot'}selected="selected"{/if}>{#Color_red#}</option>
                <option value="dunkelrot" {if $c->Farbe == 'dunkelrot'}selected="selected"{/if}>{#Color_darkred#}</option>
                <option value="gelb" {if $c->Farbe == 'gelb'}selected="selected"{/if}>{#Color_yellow#}</option>
              </select>
            </td>
            <td><input name="Position[{$c->Id}]" type="text" class="input" style="width: 35px" value="{$c->Position}" maxlength="2" /></td>
            <td><label><input name="del[{$c->Id}]" type="checkbox" id="d" value="1" />{#Global_Delete#}</label></td>
          </tr>
        {/foreach}
      </table>
      <input type="submit" class="button" value="{#Save#}" />
      <input name="update" type="hidden" value="1" />
    </form>
  {/if}
  <div class="subheaders">{#Polls_addSelectable#}</div>
  <form method="post" action="">
    <table width="100%" border="0" cellspacing="0" cellpadding="3">
      <tr class="headers">
        <td width="120" class="headers">{$language.name.1}</td>
        <td width="120" class="headers">{$language.name.2}</td>
        <td width="120" class="headers">{$language.name.3}</td>
        <td width="80" class="headers">{#Polls_color#}</td>
        <td width="100" class="headers">{#Global_Position#}</td>
        <td class="headers">&nbsp;</td>
      </tr>
      <tr class="{cycle values='second'}">
        <td>
          <input type="hidden" name="poll[{$c->Id}]" value="{$c->Id}" />
          <input name="Frage_1" type="text" class="input" id="Frage_1" style="width: 120px" />
        </td>
        <td><input name="Frage_2" type="text" class="input" id="Frage_2" style="width: 120px" /></td>
        <td><input name="Frage_3" type="text" class="input" id="Frage_3" style="width: 120px" /></td>
        <td>
          <select class="input" name="Farbe" id="Farbe">
            <option value="blau">{#Color_blue#}</option>
            <option value="grau">{#Color_grey#}</option>
            <option value="gruen">{#Color_green#}</option>
            <option value="mintgruen">{#Color_mintgreen#}</option>
            <option value="orange">{#Color_orange#}</option>
            <option value="rot">{#Color_red#}</option>
            <option value="rot">{#Color_darkred#}</option>
            <option value="gelb">{#Color_yellow#}</option>
          </select>
        </td>
        <td><input name="Position" type="text" class="input" id="Position" style="width: 35px" value="{$c->Position}" maxlength="2" /></td>
        <td>&nbsp;</td>
      </tr>
    </table>
    <input name="new" type="hidden" value="1" />
    <input type="submit" class="button" value="{#Global_Add#}" />
  </form>
{/if}
