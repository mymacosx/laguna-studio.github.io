<div class="header">{#Newsletter_archive#}</div>
<div class="subheaders">
  {if $admin_settings.Ahelp == 1}
    <a class="colorbox" href="index.php?do=help&amp;sub={$helpquery}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/s_help.png" alt="" border="0" /> {#GlobalHelp#}</a>&nbsp;&nbsp;&nbsp;
    {/if}
  <a class="colorbox" href="index.php?do=support&amp;sub=send_order&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/send.png" alt="" border="0" /> {#SendOrder#}</a>
</div>
<div class="subheaders">
  <form method="post" action="index.php?do=newsletter&amp;sub=archive">
    <select name="sys" class="input">
      <option value="one"{if isset($smarty.request.sys) && $smarty.request.sys == 'one'} selected="selected"{/if}>{#Sections_newsletter#}</option>
      <option value="later"{if isset($smarty.request.sys) && $smarty.request.sys == 'later'} selected="selected"{/if}>{#NewsletterLater#}</option>
      <option value="more"{if isset($smarty.request.sys) && $smarty.request.sys == 'more'} selected="selected"{/if}>{#NewsletterMore#}</option>
    </select>
    <select class="input" name="typ">
      <option value="all" {if $smarty.request.typ == 'all'}selected="selected"{/if}>{#Global_ShowAll#}</option>
      <option value="abos" {if $smarty.request.typ == 'abos'}selected="selected"{/if}>{#Newsletter_toAbos#}</option>
      <option value="groups" {if $smarty.request.typ == 'groups'}selected="selected"{/if}>{#Newsletter_toGroups#}</option>
    </select>
    {#DataRecords#} <input class="input" name="pp" type="text" id="pp" value="{$limit}" size="2" maxlength="3" />
    <input type="submit" class="button" value="{#Global_Show#}" />
  </form>
</div>
<form action="" method="post" name="kform" id="kform">
  <table width="100%" cellpadding="3" cellspacing="0" border="0" class="tableborder">
    <tr>
      <td class="headers">{#Newsletter_NlSubject#}</td>
      <td width="250" class="headers" align="center">{#Global_Type#}</td>
      <td width="100" class="headers" align="center">{#Global_Date#}</td>
      <td width="70" class="headers" align="center">{#Global_Actions#}</td>
      <td width="120" class="headers">
        <label><input name="allbox" type="checkbox" id="d2" onclick="multiCheck();" value="" /> {#Global_SelAll#}</label>
      </td>
    </tr>
    {foreach from=$items item=i}
      <tr class="{cycle values="second, first"}">
        <td><strong>{$i->Titel|sanitize}</strong></td>
        <td width="250" align="center" nowrap="nowrap">
          {if $i->Typ == 'abos'}
            {#Newsletter_toAbos#}
          {else}
            {#Newsletter_toGroups#}
          {/if}
        </td>
        <td width="100" align="center">
          {if $i->Datum > 0}
            {$i->Datum|date_format: $lang.DateFormat}
          {else}
            ----------
          {/if}
        </td>
        <td width="70" align="center"><a class="colorbox stip" title="{$lang.Global_Overview|sanitize}" href="index.php?do=newsletter&amp;sub=view&amp;id={$i->Id}&amp;sys={$smarty.request.sys}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/view.png" alt="" border="0" /></a></td>
        <td width="120"><label><input name="del[{$i->Id}]" type="checkbox" id="d" value="1" />{#Global_Delete#}</label></td>
      </tr>
    {/foreach}
  </table>
  <input type="hidden" name="delete" value="1" />
  <input type="submit" class="button" value="{#Global_Save_Del#}" />
</form>
<br />
{if !empty($Navi)}
  <div class="navi_div"> {$Navi} </div>
{/if}
