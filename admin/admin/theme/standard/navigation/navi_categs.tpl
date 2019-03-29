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
	    Position: { required: true, range: [1,50] }
	},
	messages: { }
    });
});
//-->
</script>

<div class="header">{#Navigation_list#}</div>
<div class="subheaders">
  {if $admin_settings.Ahelp == 1}
    <a class="colorbox" href="index.php?do=help&amp;sub={$helpquery}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/s_help.png" alt="" border="0" /> {#GlobalHelp#}</a>&nbsp;&nbsp;&nbsp;
    {/if}
  <a class="colorbox" href="index.php?do=support&amp;sub=send_order&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/send.png" alt="" border="0" /> {#SendOrder#}</a>
</div>
<form action="" method="post">
  <table  width="100%" border="0" cellspacing="0" cellpadding="4">
    <tr>
      <td width="100" class="headers">{$language.name.1}</td>
      <td width="100" class="headers">{$language.name.2}</td>
      <td width="100" class="headers">{$language.name.3}</td>
      <td width="100" class="headers">{#Navigation_tag#}</td>
      <td width="5" align="center" class="headers">{#Global_Position#}</td>
      <td width="100" align="center" class="headers">{#Global_Active#}</td>
      <td class="headers">{#Global_Actions#}</td>
    </tr>
    {foreach from=$navis item=c}
      {assign var=pos value=$pos+1}
      <tr class="{cycle values='second,first'}">
        <td width="100"><input type="hidden" name="nid[{$c->Id}]" value="{$c->Id}" />
          <input class="input" name="Name_1[{$c->Id}]" type="text" value="{$c->Name_1|sanitize}" style="width: 100px" /></td>
        <td width="100"><input class="input" name="Name_2[{$c->Id}]" type="text" value="{$c->Name_2|sanitize}" style="width: 100px" /></td>
        <td width="100"><input class="input" name="Name_3[{$c->Id}]" type="text" value="{$c->Name_3|sanitize}" style="width: 100px" /></td>
        <td width="100"><strong>&#123;navigation id={$c->Id}&#125;</strong></td>
        <td width="5" align="center"><input class="input" name="Position[{$c->Id}]" type="text" size="3" maxlength="3" value="{$c->Position}" /></td>
        <td align="center">
          <label><input type="radio" name="Aktiv[{$c->Id}]" value="1" {if $c->Aktiv == 1}checked="checked"{/if} /> {#Yes#}</label>
          <label><input type="radio" name="Aktiv[{$c->Id}]" value="0" {if $c->Aktiv == 0}checked="checked"{/if} /> {#No#}</label>
        </td>
        <td>
          <a class="stip" title="{$lang.Edit|sanitize}" href="index.php?do=navigation&amp;sub=edit&amp;id={$c->Id}"><img class="absmiddle" src="{$imgpath}/edit.png" alt="" border="" /></a>
          <a class="stip" title="{$lang.Global_Delete|sanitize}" href="javascript: void(0);" onclick="if (confirm('{#Navigation_navdelc#}')) location.href = 'index.php?do=navigation&amp;sub=deletenavi&amp;id={$c->Id}';"><img class="absmiddle" src="{$imgpath}/delete.png" alt="" border="0" /></a>
        </td>
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
  <div class="header">{#Navigation_newnav#}</div>
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
