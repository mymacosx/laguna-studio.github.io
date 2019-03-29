<div class="header">{#StatSearch#}</div>
<div class="subheaders">
  {if $admin_settings.Ahelp == 1}
    <a class="colorbox" href="index.php?do=help&amp;sub={$helpquery}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/s_help.png" alt="" border="0" /> {#GlobalHelp#}</a>&nbsp;&nbsp;&nbsp;
    {/if}
  <a class="colorbox" href="index.php?do=support&amp;sub=send_order&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/send.png" alt="" border="0" /> {#SendOrder#}</a>
</div>
<form method="post" action="index.php?do=stats&amp;sub=search">
  <table border="0" cellspacing="3" cellpadding="0">
    <tr>
      <td>{#Search#}: &nbsp;</td>
      <td width="130"><input style="width: 120px" class="input" type="text" name="qs" id="qs" value="{$smarty.request.qs|default:''}" /></td>
      <td>{#DataRecords#}: &nbsp;</td>
      <td width="50"><input class="input" style="width: 30px" type="text" name="pp" id="pp" value="{$limit}" /></td>
      <td><input name="Senden" type="submit" class="button" value="{#Global_search_b#}" />&nbsp;&nbsp;</td>
      <td><input type="button" class="button" onclick="location.href = 'index.php?do=stats&amp;sub=search';" value="{#ButtonReset#}" />&nbsp;&nbsp;</td>
      <td><input type="button" class="button" onclick="location.href = 'index.php?do=stats&amp;sub=search_export';" value="{#Stats_Export_Search#}" />&nbsp;&nbsp;</td>
        {if perm('settings')}
        <td><input type="button" class="button" onclick="location.href = 'index.php?do=stats&amp;sub=allsearchdel';" value="{#DelAll#}" /></td>
        {/if}
    </tr>
  </table>
</form>
<table width="100%" border="0" cellspacing="1" cellpadding="5" class="tableborder">
  <tr>
    <td class="headers"><a href="index.php?do=stats&amp;sub=search&amp;sort={$suchesort|default:'suche_desc'}&amp;pp={$limit}&amp;page={$smarty.request.page|default:1}{if !empty($smarty.request.qs)}&amp;q={$smarty.request.qs|urlencode}{/if}">{#SearchSl#}</a></td>
    <td width="140" align="center" class="headers"><a href="index.php?do=stats&amp;sub=search&amp;sort={$ortsort|default:'ort_desc'}&amp;pp={$limit}&amp;page={$smarty.request.page|default:1}{if !empty($smarty.request.qs)}&amp;q={$smarty.request.qs|urlencode}{/if}">{#SearchPage#}</a></td>
    <td width="120" align="center" class="headers"><a href="index.php?do=stats&amp;sub=search&amp;sort={$datsort|default:'dat_desc'}&amp;pp={$limit}&amp;page={$smarty.request.page|default:1}{if !empty($smarty.request.qs)}&amp;q={$smarty.request.qs|urlencode}{/if}">{#Global_Date#}</a></td>
    <td width="100" align="center" class="headers"><a href="index.php?do=stats&amp;sub=search&amp;sort={$ipsort|default:'ip_desc'}&amp;pp={$limit}&amp;page={$smarty.request.page|default:1}{if !empty($smarty.request.qs)}&amp;q={$smarty.request.qs|urlencode}{/if}">{#Stats_IP_Adress#}</a></td>
    <td width="100" align="center" class="headers"><a href="index.php?do=stats&amp;sub=search&amp;sort={$usersort|default:'user_desc'}&amp;pp={$limit}&amp;page={$smarty.request.page|default:1}{if !empty($smarty.request.qs)}&amp;q={$smarty.request.qs|urlencode}{/if}">{#Global_User#}</a></td>
    <td width="70" align="center" class="headers">{#Global_Actions#}</td>
  </tr>
  {foreach from=$items item=item}
    <tr class="{cycle values='second,first'}">
      <td><strong>{$item->Suche|sanitize}</strong></td>
      <td width="140" align="center">
        {if $item->Suchort == 'articles'}{#Articles#}
        {elseif $item->Suchort == 'news'}{#News#}
        {elseif $item->Suchort == 'calendar'}{#Calendar#}
        {elseif $item->Suchort == 'gallery'}{#Gallery#}
        {elseif $item->Suchort == 'links'}{#Links#}
        {elseif $item->Suchort == 'manufacturer'}{#Manufacturer#}
        {elseif $item->Suchort == 'cheats'}{#Gaming_cheats#}
        {elseif $item->Suchort == 'products'}{#Products#}
        {elseif $item->Suchort == 'content'}{#Content#}
        {elseif $item->Suchort == 'downloads'}{#Downloads#}
        {elseif $item->Suchort == 'shop'}{#Global_Shop#}
        {elseif $item->Suchort == 'faq'}{#Faq#}
        {elseif $item->Suchort == 'forum'}{#Forums_nt#}
        {else}{#AllPages#}
        {/if}
      </td>
      <td width="120" align="center" nowrap="nowrap">{$item->Datum|date_format: '%d-%m-%Y, %H:%M:%S'}</td>
      <td width="100" align="center"><a class="colorbox" href="http://www.status-x.ru/webtools/whois/{$item->Ip}/">{$item->Ip}</a></td>
      <td width="100" align="center" nowrap="nowrap">
        {if $item->UserId > 0}
          <a class="colorbox" href="index.php?do=user&amp;sub=edituser&amp;user={$item->UserId}&amp;noframes=1">{$item->UserNames|sanitize}</a>
        {else}
          {$item->UserNames}
        {/if}
      </td>
      <td width="70" align="center" nowrap="nowrap"><a class="stip" title="{$lang.Global_Delete|sanitize}" href="index.php?do=stats&amp;sub=delsearch&amp;id={$item->Id}&amp;pp={$limit}&amp;page={$smarty.request.page}{if !empty($smarty.request.qs)}&amp;qs={$smarty.request.qs|urlencode}{/if}"><img class="absmiddle" src="{$imgpath}/delete.png" alt="" border="0" /></a></td>
    </tr>
  {/foreach}
  {if !empty($navi)}
    <tr>
      <td class="first" colspan="5"> {$navi} </td>
    </tr>
  {/if}
</table>
