<div class="header">{#SiteMapUser#}</div>
<form method="post" action="index.php?do=stats&amp;sub=user_map{$noframes}">
  <table width="100%" border="0" cellspacing="0" cellpadding="1">
    <tr>
      <td width="100"><label for="qs">{#Search#}</label></td>
      <td><input style="width: 200px" type="text" class="input" name="q" id="qs" value="{$smarty.request.q|default:''|sanitize}" /></td>
    </tr>
    <tr>
      <td><label for="dr">{#DataRecords#}</label></td>
      <td>
        <input class="input" style="width: 50px" type="text" name="pp" id="dr" value="{$limit}" />
        <input type="submit" class="button" value="{#Search#}" />
      </td>
    </tr>
  </table>
  <label for="dr"></label>
</form>
<form method="post" action="">
  <table width="100%" border="0" cellspacing="0" cellpadding="3">
    <tr class="headers">
      <td class="headers"><a href="index.php?do=stats&amp;sub=user_map&amp;q={$smarty.request.q|default:''}&amp;page={$smarty.request.page|default:1}&amp;sort={$namesort|default:'name_desc'}&amp;pp={$limit}{$noframes}">{#Global_Page#}</a></td>
      <td width="90" align="center" class="headers"><a href="index.php?do=stats&amp;sub=user_map&amp;q={$smarty.request.q|default:''}&amp;page={$smarty.request.page|default:1}&amp;sort={$datesort|default:'date_desc'}&amp;pp={$limit}{$noframes}">{#Global_Date#}</a></td>
      <td width="100" align="center" class="headers"><a href="index.php?do=stats&amp;sub=user_map&amp;q={$smarty.request.q|default:''}&amp;page={$smarty.request.page|default:1}&amp;sort={$ossort|default:'os_desc'}&amp;pp={$limit}{$noframes}">{#GlobalSystem#}</a></td>
      <td width="120" align="center" class="headers"><a href="index.php?do=stats&amp;sub=user_map&amp;q={$smarty.request.q|default:''}&amp;page={$smarty.request.page|default:1}&amp;sort={$browsersort|default:'browser_desc'}&amp;pp={$limit}{$noframes}">{#Stats_Ref_Agent#}</a></td>
      <td width="100" align="center" class="headers"><a href="index.php?do=stats&amp;sub=user_map&amp;q={$smarty.request.q|default:''}&amp;page={$smarty.request.page|default:1}&amp;sort={$ipsort|default:'ip_desc'}&amp;pp={$limit}{$noframes}">I.P.</a></td>
    </tr>
    {foreach from=$referer item=g}
      <tr class="{cycle values='second,first'}">
        <td>
          <strong><a class="stip" title="{$g->Url|sanitize}" onclick="window.open('{$g->Url}');" href="javascript: void(0);">{$g->Url|slice: 100: '...'|sanitize}</a></strong>
        </td>
        <td width="90" align="center">{$g->Datum_Int|date_format: $lang.DateFormat}</td>
        <td width="120" align="center"><a href="index.php?do=stats&amp;sub=user_map&amp;q={$g->Os}{$noframes}">{$g->Os}</a></td>
        <td width="120" align="center"><a href="index.php?do=stats&amp;sub=user_map&amp;q={$g->Ua}{$noframes}">{$g->Ua}</a></td>
        <td width="100" align="center"><a href="index.php?do=stats&amp;sub=user_map&amp;q={$g->IPAdresse}{$noframes}">{$g->IPAdresse}</a></td>
      </tr>
    {/foreach}
  </table>
  <br />
  {if !empty($Navi)}
    <div class="navi_div"> {$Navi} </div>
    <br />
  {/if}
  <input type="button" onclick="closeWindow();" class="button" value="{#Close#}" />
</form>

