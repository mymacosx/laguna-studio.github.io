<script type="text/javascript">
<!-- //
$(document).ready(function() {
    $('#reg_from, #reg_to, #la_from, #la_to').datepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat: 'dd.mm.yy',
        dayNamesMin: [{#Calendar_daysmin#}],
        monthNamesShort: [{#Calendar_monthNamesShort#}],
        firstDay: 1
    });
});

function checkmulti() {
    if(document.getElementById('deltrue').selected == true) {
	if(confirm('{#User_multiaction_delc#}')) {
	    return true;
        } else {
	    return false;
        }
    }
    if(document.getElementById('sendmessage').selected == true &&  document.getElementById('message_subject').value == '') {
	alert('{#User_multiaction_message_ns#}');
	document.getElementById('message_subject').focus();
	return false;
    }
    if(document.getElementById('sendmessage').selected == true &&  document.getElementById('message_text').value == '') {
	alert('{#User_multiaction_message_nt#}');
	document.getElementById('message_text').focus();
	return false;
    }
    if(document.getElementById('changegroup').selected == true && document.getElementById('changegroup_sel').selectedIndex == 0) {
	alert('{#User_multiaction_group_no#}');
	document.getElementById('changegroup_sel').focus();
	return false;
    }
}
function getformelem() {
    if(document.getElementById('changegroup').selected == true) {
	document.getElementById('changegroup_sel').style.display = '';
	document.getElementById('sendmessage_elems').style.display = 'none';
    } else if(document.getElementById('sendmessage').selected == true) {
	document.getElementById('sendmessage_elems').style.display = '';
	document.getElementById('changegroup_sel').style.display = 'none';
    } else {
	document.getElementById('changegroup_sel').style.display = 'none';
	document.getElementById('sendmessage_elems').style.display = 'none';
    }
}
//-->
</script>

<div class="header">{#User_nameS#} - {#Global_Overview#}</div>
<div id="login_form_users">
  <div class="subheaders">
    {if $admin_settings.Ahelp == 1}
      <a class="colorbox" href="index.php?do=help&amp;sub={$helpquery}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/s_help.png" alt="" border="0" /> {#GlobalHelp#}</a>&nbsp;&nbsp;&nbsp;
      {/if}
    <a class="colorbox" href="index.php?do=support&amp;sub=send_order&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/send.png" alt="" border="0" /> {#SendOrder#}</a>
  </div>
  <div class="subheaders">
    <form method="post" action="" {if $smarty.session.UserSearch}onsubmit="return checkmulti();"{/if}>
      <input type="hidden" name="do" value="user" />
      <input type="hidden" name="sub" value="showusers" />
      <table border="0" cellspacing="0" cellpadding="2">
        <tr>
          <td width="110"><label for="name">{#User_search_sw#}</label></td>
          <td width="150"><input style="width: 120px" class="input" type="text" name="name" id="name" value="{$smarty.request.name}" /></td>
          <td align="right"><label for="group">{#Groups_Name#}</label></td>
          <td>
            <select style="width: 124px" class="input" name="group" id="group">
              <option value="">{#All_Grupp#}</option>
              {foreach from=$groups item=g}
                {if $g->Id != 2}
                  <option value="{$g->Id}" {if $g->Id == $smarty.request.group}selected="selected"{/if}>{$g->Name_Intern|sanitize}</option>
                {/if}
              {/foreach}
            </select>
          </td>
        </tr>
        <tr>
          <td width="110"><label for="reg_from">{#User_search_rfrom#}</label></td>
          <td><input class="input" style="width: 120px" readonly="readonly" type="text" name="regfrom" id="reg_from" value="{$smarty.request.regfrom|sanitize}" /></td>
          <td align="right">{#User_search_rtill#}</td>
          <td>
            <input class="input" style="width: 120px" readonly="readonly" type="text" name="regtill" id="reg_to" value="{$smarty.request.regtill|sanitize}" />
            <input type="hidden" name="page" value="1" />
            <input name="startsearch" type="hidden" id="startsearch" value="1" />
          </td>
        </tr>
        <tr>
          <td width="110"><label for="la_from">{#User_search_lafrom#}</label></td>
          <td><input class="input" style="width: 120px" readonly="readonly" type="text" name="lastonlinefrom" id="la_from" value="{$smarty.request.lastonlinefrom|sanitize}" /></td>
          <td align="right">{#User_search_latill#}</td>
          <td><input class="input" style="width: 120px" readonly="readonly" type="text" name="lastonlinetill" id="la_to" value="{$smarty.request.lastonlinetill|sanitize}" /></td>
        </tr>
        <tr>
          <td width="110">{#Global_Status#}</td>
          <td>
            <select name="aktiv" class="input" style="width: 124px">
              <option value="" {if !isset($smarty.request.aktiv) || $smarty.request.aktiv == ''}selected="selected"{/if}></option>
              <option value="1" {if isset($smarty.request.aktiv) && $smarty.request.aktiv == '1'}selected="selected"{/if}>{#Global_Active#}</option>
              <option value="0" {if isset($smarty.request.aktiv) && $smarty.request.aktiv == '0'}selected="selected"{/if}>{#Global_Inactive#}</option>
            </select>
          </td>
          <td align="right">{#DataRecords#}</td>
          <td>
            <input type="text" class="input" name="limit" style="width: 45px" value="{$limit}" />
            <input class="button" type="submit" onclick="document.getElementById('del_innerhtml').innerHTML='';" value="{#Global_search_b#}" />
          </td>
        </tr>
      </table>
      <div id="del_innerhtml">
        {if $smarty.session.UserSearch}
          <fieldset>
            <legend>
              <label for="mf">{#User_multiaction_inf#}</label>
            </legend>
            {if $action_done == 1}
              <h3>{#GlobalOk#}</h3>
              <br />
            {/if}
            {strip}
              <select id="mf" onchange="return getformelem();" style="width: 250px" class="input" name="uaction">
                <option value="">{#User_multiaction_delsel#}</option>
                <optgroup label="{#User_multiaction_setst#}">
                  <option value="activate">{#User_multi_activate#}</option>
                  <option value="deactivate">{#User_multi_deactivate#}</option>
                  <option value="changegroup" id="changegroup">{#User_multiaction_setgroup#}</option>
                </optgroup>
                <optgroup label="{#User_multiaction_setother#}">
                  <option id="sendmessage" value="sendmessage">{#User_multiaction_sendmessage#}</option>
                  <option id="deltrue" value="delete">{#User_multi_del#}</option>
                </optgroup>
              </select>
            {/strip}
            <select class="input" name="newgroup" id="changegroup_sel" style="display: none">
              <option value="">{#User_multi_groupsel#}</option>
              {foreach from=$groups item=g}
                {if $g->Id != 2}
                  <option value="{$g->Id}" {if $g->Id == $u->Gruppe}selected="selected"{/if}>{$g->Name_Intern|sanitize}</option>
                {/if}
              {/foreach}
            </select>
            <div id="sendmessage_elems" style="display: none">
              <br />
              <label for="message_subject"><strong>{#User_multiaction_message_s#}</strong></label>
              <br />
              <input class="input" style="width: 350px" type="text" name="message_subject" id="message_subject" />
              <br />
              <label for="message_text"><strong>{#User_multiaction_message_t#}</strong></label>
              <br />
              <textarea cols="" rows="" class="input" style="width: 350px; height: 100px" name="message_text" id="message_text">{#User_multiaction_message_def#}</textarea>
              <br />
            </div>
            <input class="button" type="submit" value="{#User_multiaction_delb#}" />
          </fieldset>
        {/if}
      </div>
    </form>
  </div>
  <form method="post" action="" name="kform">
    <div class="maintable">
      <table width="100%" border="0" cellspacing="0" cellpadding="3">
        <tr class="">
          <td width="30" class="headers">Id</td>
          <td class="headers"><a href="{$ordstr}{$name_s|default:'&amp;sort=name_asc'}">{#Global_Name#}</a></td>
          <td width="150" align="center" class="headers"><a href="{$ordstr}{$username_s|default:'&amp;sort=username_asc'}">{#User_username#}</a></td>
          <td width="140" class="headers"><a href="{$ordstr}{$usergroup_s|default:'&amp;sort=usergroup_asc'}">{#Groups_Name#}</a></td>
          <td width="100" align="center" class="headers"><a href="{$ordstr}{$regdate_s|default:'&amp;sort=regdate_desc'}">{#User_reggedon#}</a></td>
          <td width="100" align="center" class="headers"><a href="{$ordstr}{$lastonline_s|default:'&amp;sort=lastonline_desc'}">{#User_lastonline#}</a></td>
          <td width="100" align="center" class="headers">{#Global_Actions#}</td>
          <td width="10" align="center" class="headers"><label class="stip" title="{$lang.Global_SelAll|sanitize}"><input name="allbox" type="checkbox" id="d" onclick="multiCheck();" value="" /></label></td>
        </tr>
        {foreach from=$user item=u}
          <tr class="{cycle values='second,first'}">
            <td width="30" class="row_spacer">{$u->Id}</td>
            <td class="row_spacer">
              <a class="colorbox stip" title="{$lang.User_edit|sanitize}" href="index.php?do=user&amp;sub=edituser&amp;user={$u->Id}&amp;noframes=1"><strong>{if !$u->Vorname}<em>{#Edit#}</em>{else}{$u->Vorname|truncate: '15': '.'}{/if} {$u->Nachname|sanitize}</strong></a>
              <br />
              <small>{$u->Email}</small>
              <input type="hidden" name="deldetails[{$u->Id}]" value="{$u->Benutzername|sanitize} ({$u->Email})" />
            </td>
            <td width="150" align="center" class="row_spacer">{$u->Benutzername|sanitize}</td>
            <td width="140" class="row_spacer">
              <select class="input" style="width: 130px" name="Gruppe[{$u->Id}]">
                {foreach from=$groups item=g}
                  {if $g->Id != 2}
                    <option value="{$g->Id}" {if $g->Id == $u->Gruppe}selected="selected"{/if} {if $u->Id == 1}disabled="disabled"{/if}>{$g->Name_Intern|sanitize}</option>
                  {/if}
                {/foreach}
              </select>
            </td>
            <td width="100" align="center" class="row_spacer"> {$u->Regdatum|date_format: "%d.%m.%y"} </td>
            <td width="100" align="center" class="row_spacer">{if $u->Zuletzt_Aktiv>0}{$u->Zuletzt_Aktiv|date_format: "%d.%m.%y, %H:%M"}{else}-{/if} </td>
            <td width="100" class="row_spacer" nowrap="nowrap">
              <a class="colorbox stip" title="{$lang.User_edit|sanitize}" href="index.php?do=user&amp;sub=edituser&amp;user={$u->Id}&amp;noframes=1"><img src="{$imgpath}/edit.png" alt="" border="0" /></a>
              <a class="colorbox stip" title="{$lang.SiteMapUser|sanitize}" href="index.php?do=stats&amp;sub=user_map&amp;user={$u->Id}&amp;noframes=1"><img src="{$imgpath}/stats.png" alt="" border="0" /></a>
              <a class="colorbox stip" title="{$lang.Shop_edit_orderDownloads|sanitize}" href="index.php?do=shop&amp;sub=user_downloads&amp;user={$u->Id}&amp;name={$u->Nachname|sanitize}&amp;noframes=1"><img src="{$imgpath}/download{if !$u->downloads}_none{/if}.png" alt="" border="0" /></a>
                {if $shop_aktiv == 1}
                  {if $u->orders < 1}
                  <img class="stip" title="{$lang.Shop_has_noorders|sanitize}" src="{$imgpath}/orders_none.png" alt="" border="0" />
                {else}
                  <a class="colorbox stip" title="{$lang.Shop_has_orders|sanitize}" href="index.php?do=shop&amp;sub=orders&amp;search=1&amp;page=1&amp;query={$u->Id}&amp;only_id=1&amp;noframes=1"><img src="{$imgpath}/orders.png" alt="" border="0" /></a>
                  {/if}
                {/if}
              <a class="stip" title={if $u->Aktiv != 1}"{$lang.User_unlock|sanitize}"{else}"{$lang.User_lock|sanitize}"{/if} href="index.php?do=user&amp;sub=openclose&amp;openclose={if $u->Aktiv != 1}open{else}close{/if}&amp;user={$u->Id}&amp;backurl={$backurl}"><img src="{$imgpath}/{if $u->Aktiv != 1}closed{else}opened{/if}.png" alt="" border="0" /></a>
            </td>
            <td width="10" align="center" class="row_spacer">
              {if $u->Id == 1}&nbsp;
              {else}
                <input class="stip" title="{$lang.DelInfChekbox|sanitize}" name="del[{$u->Id}]" id="x_{$u->Id}" type="checkbox" value="1" />
                <input type="hidden" name="user[{$u->Id}]" value="{$u->Id}" />
              {/if}
            </td>
          </tr>
        {/foreach}
      </table>
    </div>
    <input type="hidden" name="quicksave" value="1" />
    <input class="button" type="submit" value="{#Save#}" />
  </form>
  {if !empty($pages)}
    <div class="navi_div">
      <strong>{#GoPagesSimple#}</strong>
      <form method="get" action="index.php">
        <input type="text" class="input" style="width: 25px; text-align: center" name="page" value="{$smarty.request.page|default:'1'}" />
        <input type="hidden" name="do" value="user" />
        <input type="hidden" name="sub" value="showusers" />
        <input type="hidden" name="name" value="{$smarty.request.name|default:''}" />
        <input type="hidden" name="group" value="{$smarty.request.group|default:''}" />
        <input type="hidden" name="regfrom" value="{$smarty.request.regfrom|default:''}" />
        <input type="hidden" name="regtill" value="{$smarty.request.regtill|default:''}" />
        <input type="hidden" name="lastonlinefrom" value="{$smarty.request.lastonlinefrom|default:''}" />
        <input type="hidden" name="lastonlinetill" value="{$smarty.request.lastonlinetill|default:''}" />
        <input type="hidden" name="aktiv" value="{$smarty.request.aktiv|default:''}" />
        <input type="hidden" name="limit" value="{$smarty.request.limit|default:'15'}" />
        <input type="submit" class="button" value="{#GoPagesButton#}" />
      </form>
      &nbsp;&nbsp;
      <strong>{#GoPages#}</strong>
      {$pages}
    </div>
  {/if}
</div>
