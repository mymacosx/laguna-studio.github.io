<div class="header">{#Newsletter_nAbos#}</div>
<div class="subheaders">
  {if $admin_settings.Ahelp == 1}
    <a class="colorbox" href="index.php?do=help&amp;sub={$helpquery}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/s_help.png" alt="" border="0" /> {#GlobalHelp#}</a>&nbsp;&nbsp;&nbsp;
    {/if}
  <a class="colorbox" href="index.php?do=support&amp;sub=send_order&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/send.png" alt="" border="0" /> {#SendOrder#}</a>
</div>
<div class="subheaders">
  <form method="post" action="index.php?do=newsletter&amp;sub=showabos">
    <table width="100%" border="0" cellpadding="2" cellspacing="0">
      <tr>
        <td width="100"><label for="gs">{#Search#}</label></td>
        <td><input style="width: 160px" class="input" type="text" name="q" id="gs" value="{if isset($smarty.request.q)}{$smarty.request.q}{/if}" /></td>
      </tr>
      <tr>
        <td><label for="dr">{#DataRecords#}</label></td>
        <td>
          <input type="text" id="dr" class="input" name="pp" style="width: 45px" value="{$limit}" />
          <input class="button" type="submit" value="{#Global_search_b#}" />
          <input name="startsearch" type="hidden" id="startsearch" value="1" />
        </td>
      </tr>
    </table>
  </form>
</div>
<form name="kform" id="kform" action="" method="post">
  <table width="100%" cellpadding="4" cellspacing="0" border="0" class="tableborder">
    <tr>
      <td class="headers"><a href="{$ordstr}{$email_s|default:'&amp;sort=email_desc'}">{#Global_Email#}</a></td>
      <td width="100" align="center" class="headers"><a href="{$ordstr}{$date_s|default:'&amp;sort=date_asc'}">{#Global_Date#}</a></td>
      <td width="100" align="center" class="headers"><a href="{$ordstr}{$format_s|default:'&amp;sort=format_desc'}">{#Newsletter_nFormat#}</a></td>
      <td width="100" align="center" class="headers"><a href="{$ordstr}{$active_s|default:'&amp;sort=active_asc'}">{#Global_Active#}</a></td>
      <td width="250" align="center" class="headers"><a href="{$ordstr}{$newsletter_s|default:'&amp;sort=newsletter_asc'}">{#Newsletter#}</a></td>
      <td width="100" align="center" class="headers"><label><input name="allbox" type="checkbox" id="d" onclick="multiCheck();" value="" />{#Global_SelAll#}</label></td>
    </tr>
    {foreach from=$items item=abo}
      <tr class="{cycle values='second,first'}">
        <td><a href="mailto: {$abo->Email}">{$abo->Email}</a></td>
        <td width="100" align="center">{$abo->Datum|date_format: '%d.%m.%Y'}</td>
        <td width="100" align="center">
          <select class="input" name="Format[{$abo->Id}]">
            <option value="html"{if $abo->Format == 'html'} selected="selected"{/if}>{#Newsletter_nFormatH#}</option>
            <option value="text"{if $abo->Format == 'text'} selected="selected"{/if}>{#Newsletter_nFormatT#}</option>
          </select>
        </td>
        <td width="100" align="center">
          <select class="input" name="Aktiv[{$abo->Id}]">
            <option value="1"{if $abo->Aktiv == 1} selected="selected"{/if}>{#Yes#}</option>
            <option value="0"{if $abo->Aktiv == 0} selected="selected"{/if}>{#No#}</option>
          </select>
        </td>
        <td width="250" align="center">{$abo->Name}</td>
        <td width="100" align="center"><label><input name="del[{$abo->Id}]" type="checkbox" id="d" value="1" />{#Global_Delete#}</label></td>
      </tr>
    {/foreach}
  </table>
  <input name="save" type="hidden" id="save" value="1" />
  <input class="button" type="submit" value="{#Save#}" />
</form>
<br />
{if !empty($Navi)}
  <div class="navi_div"> {$Navi} </div>
{/if}
