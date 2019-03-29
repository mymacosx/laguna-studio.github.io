<script type="text/javascript">
<!-- //
$(document).ready(function() {
    $('#TimeStart, #TimeEnd').datepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat: 'dd.mm.yy',
        dayNamesMin: [{#Calendar_daysmin#}],
        monthNamesShort: [{#Calendar_monthNamesShort#}],
        firstDay: 1
    });
});
//-->
</script>

<div class="header">{#User_nameS#} - {#Banned#}</div>
<div class="subheaders">
  {if $admin_settings.Ahelp == 1}
    <a class="colorbox" href="index.php?do=help&amp;sub={$helpquery}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/s_help.png" alt="" border="0" /> {#GlobalHelp#}</a>&nbsp;&nbsp;&nbsp;
    {/if}
  <a class="colorbox" href="index.php?do=support&amp;sub=send_order&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/send.png" alt="" border="0" /> {#SendOrder#}</a>
</div>
<div class="subheaders">
  <form method="post" action="index.php?do=banned">
    <table border="0" cellspacing="3" cellpadding="0">
      <tr>
        <td>{#Search#}: &nbsp;</td>
        <td width="140"><input style="width: 120px" class="input" type="text" name="q" id="q" value="{if isset($smarty.request.q)}{$smarty.request.q}{/if}" /></td>
        <td>{#Gallery_sIn#}: &nbsp;</td>
        <td width="120"><select style="width: 100px" class="input" name="seltab" id="group">
            <option value="all" {if $smarty.request.seltab == 'all'} selected="selected"{/if}>{#Global_All#}</option>
            <option value="1" {if $smarty.request.seltab == 1} selected="selected"{/if}>{#Global_Id#}</option>
            <option value="2" {if $smarty.request.seltab == 2} selected="selected"{/if}>{#User_username#}</option>
            <option value="3" {if $smarty.request.seltab == 3} selected="selected"{/if}>{#Global_Email#}</option>
            <option value="4" {if $smarty.request.seltab == 4} selected="selected"{/if}>{#Stats_IP_Adress#}</option>
            <option value="5" {if $smarty.request.seltab == 5} selected="selected"{/if}>{#Links_broken_Reason#}</option>
          </select>
        </td>
        <td>{#DataRecords#}: &nbsp;</td>
        <td width="50"><input class="input" style="width: 30px" type="text" name="pp" id="pp" value="{$limit}" /></td>
        <td><input name="Senden" type="submit" class="button" value="{#Global_search_b#}" />&nbsp;&nbsp;</td>
        <td><input type="button" class="button" onclick="location.href='index.php?do=banned';" value="{#ButtonReset#}" /></td>
      </tr>
    </table>
  </form>
</div>
<div class="subheaders">
  <table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr class="{cycle values='first,second'}">
      <td width="20" align="center" class="headers"><a href="index.php?do=banned&amp;sort={$User_idsort|default:'id_desc'}&amp;pp={$limit}&amp;page={$smarty.request.page|default:1}{if !empty($smarty.request.q)}&amp;q={$smarty.request.q|urlencode}{/if}">{#Global_Id#}</a></td>
      <td width="80" align="center" class="headers"><a href="index.php?do=banned&amp;sort={$Namesort|default:'name_desc'}&amp;pp={$limit}&amp;page={$smarty.request.page|default:1}{if !empty($smarty.request.q)}&amp;q={$smarty.request.q|urlencode}{/if}">{#User_username#}</a></td>
      <td width="80" align="center" class="headers"><a href="index.php?do=banned&amp;sort={$Emailsort|default:'mail_desc'}&amp;pp={$limit}&amp;page={$smarty.request.page|default:1}{if !empty($smarty.request.q)}&amp;q={$smarty.request.q|urlencode}{/if}">{#Global_Email#}</a></td>
      <td width="80" align="center" class="headers"><a href="index.php?do=banned&amp;sort={$Ipsort|default:'ip_desc'}&amp;pp={$limit}&amp;page={$smarty.request.page|default:1}{if !empty($smarty.request.q)}&amp;q={$smarty.request.q|urlencode}{/if}">{#Stats_IP_Adress#}</a></td>
      <td align="center" class="headers"><a href="index.php?do=banned&amp;sort={$Resonsort|default:'res_desc'}&amp;pp={$limit}&amp;page={$smarty.request.page|default:1}{if !empty($smarty.request.q)}&amp;q={$smarty.request.q|urlencode}{/if}">{#Links_broken_Reason#}</a></td>
      <td width="60" align="center" class="headers"><a href="index.php?do=banned&amp;sort={$Typesort|default:'typ_desc'}&amp;pp={$limit}&amp;page={$smarty.request.page|default:1}{if !empty($smarty.request.q)}&amp;q={$smarty.request.q|urlencode}{/if}">{#Global_Type#}</a></td>
      <td width="70" align="center" class="headers"><a href="index.php?do=banned&amp;sort={$TimeStartsort|default:'tst_desc'}&amp;pp={$limit}&amp;page={$smarty.request.page|default:1}{if !empty($smarty.request.q)}&amp;q={$smarty.request.q|urlencode}{/if}">{#Global_Published#}</a></td>
      <td width="70" align="center" class="headers"><a href="index.php?do=banned&amp;sort={$TimeEndsort|default:'tend_desc'}&amp;pp={$limit}&amp;page={$smarty.request.page|default:1}{if !empty($smarty.request.q)}&amp;q={$smarty.request.q|urlencode}{/if}">{#Global_PubEnd#}</a></td>
      <td width="60" align="center" class="headers">{#Global_Actions#}</td>
    </tr>
    {foreach from=$banned item=abn}
      <tr class="{cycle values='second,first'}">
        <td width="20" align="center" class="row_spacer">{$abn->User_id|default:'---'}</td>
        <td width="80" align="center" class="row_spacer">{$abn->Name|sanitize|default:'---'}</td>
        <td width="80" align="center" class="row_spacer">{$abn->Email|sanitize|default:'---'}</td>
        <td width="80" align="center" class="row_spacer">{if !empty($abn->Ip)}<a class="colorbox" href="http://www.status-x.ru/webtools/whois/{$abn->Ip}/">{$abn->Ip}</a>{else}---{/if}</td>
        <td align="center" class="row_spacer">{$abn->Reson|sanitize|default:'---'}</td>
        <td width="60" align="center" class="row_spacer">{if $abn->Type == 'bann'}{#BannAdd#}{else}{#Automatically#}{/if}</td>
        <td width="70" align="center" class="row_spacer">{$abn->TimeStart|date_format: '%d-%m-%Y'}</td>
        <td width="70" align="center" class="row_spacer">{$abn->TimeEnd|date_format: '%d-%m-%Y'}</td>
        <td width="60" align="center" class="row_spacer"><a class="stip" title="{$lang.Edit|sanitize}" href="index.php?do=banned&amp;sub=show&amp;id={$abn->Id}&amp;pp={$limit}&amp;page={$smarty.request.page}{if !empty($smarty.request.q)}&amp;q={$smarty.request.q|urlencode}{/if}#2"><img class="absmiddle" src="{$imgpath}/edit.png" alt="" border="0" /></a>&nbsp;
            {if $abn->Aktiv == 1}
            <a class="stip" title="{$lang.Global_Active|sanitize}" href="index.php?do=banned&amp;sub=aktiv&amp;type=0&amp;id={$abn->Id}&amp;pp={$limit}&amp;page={$smarty.request.page}{if !empty($smarty.request.q)}&amp;q={$smarty.request.q|urlencode}{/if}"><img class="absmiddle" src="{$imgpath}/closed.png" alt="" border="0" /></a>&nbsp;
            {else}
            <a class="stip" title="{$lang.Global_Inactive|sanitize}" href="index.php?do=banned&amp;sub=aktiv&amp;type=1&amp;id={$abn->Id}&amp;pp={$limit}&amp;page={$smarty.request.page}{if !empty($smarty.request.q)}&amp;q={$smarty.request.q|urlencode}{/if}"><img class="absmiddle" src="{$imgpath}/opened.png" alt="" border="0" /></a>&nbsp;
            {/if}
          <a class="stip" title="{$lang.Global_Delete|sanitize}" href="index.php?do=banned&amp;sub=del&amp;id={$abn->Id}&amp;pp={$limit}&amp;page={$smarty.request.page}{if !empty($smarty.request.q)}&amp;q={$smarty.request.q|urlencode}{/if}"><img class="absmiddle" src="{$imgpath}/delete.png" alt="" border="0" /></a></td>
      </tr>
    {/foreach}
    {if !empty($navi)}
      <tr>
        <td class="first" colspan="9"> {$navi} </td>
      </tr>
    {/if}
  </table>
</div>
<br />
{if $vkl != 1}
  <div class="header">{if $error == 1}<a id="2"></a>{/if}{#Global_Add#}</div>
  <div class="subheaders">
    <form action="index.php?do=banned&amp;sub=new&amp;pp={$limit}&amp;page={$smarty.request.page}{if !empty($smarty.request.q)}&amp;q={$smarty.request.q|urlencode}{/if}#2" method="post">
      <table border="0" cellspacing="5" cellpadding="1">
        <tr>
          <td align="center" class="headers" colspan="2">{#BannedType#}</td>
          <td width="270" align="center" class="headers">{#Links_broken_Reason#}</td>
          <td width="120" align="center" class="headers">{#Global_Published#}</td>
          <td width="120" align="center" class="headers">{#Global_PubEnd#}</td>
        </tr>
        <tr>
          <td>{#Global_Id#}: </td>
          <td><input name="User_id" type="text" value="{if $error == 1}{$smarty.request.User_id}{/if}" /></td>
          <td width="270" rowspan="4"><textarea name="Reson" cols="" rows="7" style="width: 100%">{if $error == 1}{$smarty.request.Reson}{/if}</textarea></td>
          <td width="120"><input name="TimeStart" type="text" id="TimeStart" style="width: 90px" value="{$smarty.now|date_format: '%d.%m.%Y'}" /></td>
          <td width="120"><input name="TimeEnd" type="text" id="TimeEnd" style="width: 90px" value="{if $error == 1}{$smarty.request.TimeEnd}{/if}" /></td>
        </tr>
        <tr>
          <td>{#User_username#}: </td>
          <td><input name="Name" type="text" value="{if $error == 1}{$smarty.request.Name}{/if}" /></td>
          <td colspan="2" rowspan="3">&nbsp;</td>
        </tr>
        <tr>
          <td>{#Global_Email#}: </td>
          <td><input name="Email" type="text" value="{if $error == 1}{$smarty.request.Email}{/if}" /></td>
        </tr>
        <tr>
          <td>{#Stats_IP_Adress#}: </td>
          <td><input name="Ip" type="text" value="{if $error == 1}{$smarty.request.Ip}{/if}" /></td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td colspan="4"><input name="Senden" type="submit" class="button" value="{#Save#}" />
          </td>
        </tr>
      </table>
    </form>
  </div>
{else}
  <div class="header"><a id="2"></a>{#Edit#}</div>
  <div class="subheaders">
    <form action="index.php?do=banned&amp;sub=new&amp;pp={$limit}&amp;page={$smarty.request.page}{if !empty($smarty.request.q)}&amp;q={$smarty.request.q|urlencode}{/if}#2" method="post">
      <table border="0" cellspacing="5" cellpadding="1">
        <tr>
          <td align="center" class="headers" colspan="2">{#BannedType#}</td>
          <td width="270" align="center" class="headers">{#Links_broken_Reason#}</td>
          <td width="120" align="center" class="headers">{#Global_Published#}</td>
          <td width="120" align="center" class="headers">{#Global_PubEnd#}</td>
        </tr>
        <tr>
          <td>{#Global_Id#}: </td>
          <td><input name="User_id" type="text" value="{$row->User_id|sanitize}" /></td>
          <td width="270" rowspan="4"><textarea name="Reson" cols="" rows="7" style="width: 100%">{if $error == 1}{$smarty.request.Reson}{else}{$row->Reson|sanitize}{/if}</textarea></td>
          <td width="120"><input name="TimeStart" type="text" id="TimeStart" style="width: 90px" value="{if $error == 1}{$smarty.request.TimeStart}{else}{$row->TimeStart|date_format: '%d.%m.%Y'}{/if}" /></td>
          <td width="120"><input name="TimeEnd" type="text" id="TimeEnd" style="width: 90px" value="{if $error == 1}{$smarty.request.TimeEnd}{else}{$row->TimeEnd|date_format: '%d.%m.%Y'}{/if}" /></td>
        </tr>
        <tr>
          <td>{#User_username#}: </td>
          <td><input name="Name" type="text" value="{if $error == 1}{$smarty.request.Name}{else}{$row->Name|sanitize}{/if}" /></td>
          <td colspan="2" rowspan="3">&nbsp;</td>
        </tr>
        <tr>
          <td>{#Global_Email#}: </td>
          <td><input name="Email" type="text" value="{if $error == 1}{$smarty.request.Email}{else}{$row->Email|sanitize}{/if}" /></td>
        </tr>
        <tr>
          <td>{#Stats_IP_Adress#}: </td>
          <td><input name="Ip" type="text" value="{if $error == 1}{$smarty.request.Ip}{else}{$row->Ip|sanitize}{/if}" /></td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td colspan="4"><input name="edit" type="hidden" value="1" />
            <input name="Senden" type="submit" class="button" value="{#Save#}" />
          </td>
        </tr>
      </table>
    </form>
  </div>
{/if}
