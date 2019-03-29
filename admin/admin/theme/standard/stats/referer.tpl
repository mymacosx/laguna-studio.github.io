<div class="header">{#Stats_Referer#}</div>
<div class="subheaders">
  {if $admin_settings.Ahelp == 1}
    <a class="colorbox" href="index.php?do=help&amp;sub={$helpquery}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/s_help.png" alt="" border="0" /> {#GlobalHelp#}</a>&nbsp;&nbsp;&nbsp;
    {/if}
  <a class="colorbox" href="index.php?do=support&amp;sub=send_order&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/send.png" alt="" border="0" /> {#SendOrder#}</a>
</div>
<form method="post" action="index.php?do=stats&amp;sub=referer">
  <table width="100%" border="0" cellspacing="0" cellpadding="1">
    <tr>
      <td width="140"><label for="ne">{#ReferEmpty#}</label></td>
      <td>
        <input type="radio" name="noref" id="ne" value="1"{if isset($smarty.request.noref) && $smarty.request.noref == 1} checked="checked"{/if} />{#Yes#}
        <input type="radio" name="noref" id="ne" value="0"{if empty($smarty.request.noref)} checked="checked"{/if} />{#No#}
      </td>
    </tr>
    <tr>
      <td width="140"><label for="ws">{#SearchWords#}</label></td>
      <td>
        <input type="radio" name="words" id="ws" value="1"{if isset($smarty.request.words) && $smarty.request.words == 1} checked="checked"{/if} />{#Yes#}
        <input type="radio" name="words" id="ws" value="0"{if empty($smarty.request.words)} checked="checked"{/if} />{#No#}
      </td>
    </tr>
    <tr>
      <td width="100"><label for="qs">{#Search#}</label></td>
      <td><input style="width: 200px" type="text" class="input" name="q" id="qs" value="{$smarty.request.q|default:''|sanitize}" /></td>
    </tr>
    <tr>
      <td><label for="dr">{#DataRecords#}</label></td>
      <td>
        <input class="input" style="width: 50px" type="text" name="pp" id="dr" value="{$limit}" />
        <input type="submit" class="button" value="{#Global_search_b#}" />&nbsp;&nbsp;
        <input type="button" class="button" onclick="location.href = 'index.php?do=stats&amp;sub=referer';" value="{#ButtonReset#}" />&nbsp;&nbsp;
        <input type="button" class="button" onclick="location.href = 'index.php?do=stats&amp;sub=export_search';" value="{#Stats_Export_Search#}" />&nbsp;&nbsp;
        {if perm('settings')}
          <input type="button" class="button" onclick="if (confirm('{#Stats_Ref_DelC#}')) location.href = 'index.php?do=stats&amp;sub=referer&amp;del=1';" value="{#DelAll#}" />
        {/if}
      </td>
    </tr>
  </table>
  <label for="dr"></label>
</form>
<form method="post" action="">
  <table width="100%" border="0" cellspacing="0" cellpadding="3">
    <tr class="headers">
      <td class="headers"><a href="index.php?do=stats&amp;sub=referer&amp;q={$smarty.request.q|default:''}&amp;page={$smarty.request.page|default:1}&amp;sort={$namesort|default:'name_desc'}&amp;pp={$limit}&amp;noref={$smarty.request.noref|default:'0'}&amp;words={$smarty.request.words|default:'0'}">{#Global_Name#}</a></td>
      <td width="160" align="center" class="headers"><a href="index.php?do=stats&amp;sub=referer&amp;q={$smarty.request.q|default:''}&amp;page={$smarty.request.page|default:1}&amp;sort={$wsort|default:'w_desc'}&amp;pp={$limit}&amp;noref={$smarty.request.noref|default:'0'}&amp;words={$smarty.request.words|default:'0'}">{#SearchWords#}</a></td>
      <td width="90" align="center" class="headers"><a href="index.php?do=stats&amp;sub=referer&amp;q={$smarty.request.q|default:''}&amp;page={$smarty.request.page|default:1}&amp;sort={$datesort|default:'date_desc'}&amp;pp={$limit}&amp;noref={$smarty.request.noref|default:'0'}&amp;words={$smarty.request.words|default:'0'}">{#Global_Date#}</a></td>
      <td width="100" align="center" class="headers"><a href="index.php?do=stats&amp;sub=referer&amp;q={$smarty.request.q|default:''}&amp;page={$smarty.request.page|default:1}&amp;sort={$ossort|default:'os_desc'}&amp;pp={$limit}&amp;noref={$smarty.request.noref|default:'0'}&amp;words={$smarty.request.words|default:'0'}">{#GlobalSystem#}</a></td>
      <td width="120" align="center" class="headers"><a href="index.php?do=stats&amp;sub=referer&amp;q={$smarty.request.q|default:''}&amp;page={$smarty.request.page|default:1}&amp;sort={$browsersort|default:'browser_desc'}&amp;pp={$limit}&amp;noref={$smarty.request.noref|default:'0'}&amp;words={$smarty.request.words|default:'0'}">{#Stats_Ref_Agent#}</a></td>
      <td width="100" align="center" class="headers"><a href="index.php?do=stats&amp;sub=referer&amp;q={$smarty.request.q|default:''}&amp;page={$smarty.request.page|default:1}&amp;sort={$ipsort|default:'ip_desc'}&amp;pp={$limit}&amp;noref={$smarty.request.noref|default:'0'}&amp;words={$smarty.request.words|default:'0'}">I.P.</a></td>
      <td width="100" align="center" class="headers"><a href="index.php?do=stats&amp;sub=referer&amp;q={$smarty.request.q|default:''}&amp;page={$smarty.request.page|default:1}&amp;sort={$usersort|default:'user_desc'}&amp;pp={$limit}&amp;noref={$smarty.request.noref|default:'0'}&amp;words={$smarty.request.words|default:'0'}">{#Global_User#}</a></td>
    </tr>
    {foreach from=$referer item=g}
      <tr class="{cycle values='second,first'}">
        <td>
          {if empty($g->Referer)}
            <small><em>{#Direct_Request#}</em></small>
          {else}
            <strong><a class="stip" title="{$g->Referer|sanitize}" onclick="window.open('{$g->Referer}');" href="javascript: void(0);">{$g->Referer|slice: 100: '...'|sanitize}</a></strong>
            {/if}
        </td>
        <td width="160" align="center"><a href="index.php?do=stats&amp;sub=referer&amp;q={$g->Words|sanitize}&amp;noref={$smarty.request.noref|default:'0'}&amp;words={$smarty.request.words|default:'0'}">{$g->Words|sanitize}</a></td>
        <td width="90" align="center">{$g->Datum_Int|date_format: $lang.DateFormat}</td>
        <td width="120" align="center"><a href="index.php?do=stats&amp;sub=referer&amp;q={$g->Os}&amp;noref={$smarty.request.noref|default:'0'}&amp;words={$smarty.request.words|default:'0'}">{$g->Os}</a></td>
        <td width="120" align="center"><a href="index.php?do=stats&amp;sub=referer&amp;q={$g->Ua}&amp;noref={$smarty.request.noref|default:'0'}&amp;words={$smarty.request.words|default:'0'}">{$g->Ua}</a></td>
        <td width="100" align="center"><a href="index.php?do=stats&amp;sub=referer&amp;q={$g->IPAdresse}&amp;noref={$smarty.request.noref|default:'0'}&amp;words={$smarty.request.words|default:'0'}">{$g->IPAdresse}</a></td>
        <td width="100" align="center"><a href="index.php?do=stats&amp;sub=referer&amp;q={$g->UserName}&amp;noref={$smarty.request.noref|default:'0'}&amp;words={$smarty.request.words|default:'0'}">{$g->UserNames|sanitize}</a></td>
      </tr>
    {/foreach}
  </table>
</form>
<br />
{if !empty($Navi)}
  <div class="navi_div"> {$Navi} </div>
  <br />
{/if}
