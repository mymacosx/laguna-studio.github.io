<div class="header">{#S_vivod#}</div>
<div class="subheaders">
  {if $admin_settings.Ahelp == 1}
    <a class="colorbox" href="index.php?do=help&amp;sub={$helpquery}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/s_help.png" alt="" border="0" /> {#GlobalHelp#}</a>&nbsp;&nbsp;&nbsp;
    {/if}
  <a class="colorbox" href="index.php?do=support&amp;sub=send_order&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/send.png" alt="" border="0" /> {#SendOrder#}</a>
</div>
<div class="subheaders">
  <form method="post" action="index.php?do=phrases">
    <table border="0" cellspacing="3" cellpadding="0">
      <tr>
        <td>{#Search#}: &nbsp;</td>
        <td width="130"><input style="width: 120px" class="input" type="text" name="q" id="q" value="{if isset($smarty.request.q)}{$smarty.request.q}{/if}" /></td>
        <td>{#DataRecords#}: &nbsp;</td>
        <td width="50"><input class="input" style="width: 30px" type="text" name="pp" id="pp" value="{$limit}" /></td>
        <td><input name="Senden" type="submit" class="button" value="{#Global_search_b#}" />&nbsp;&nbsp;</td>
        <td><input type="button" class="button" onclick="location.href = 'index.php?do=phrases';" value="{#ButtonReset#}" /></td>
      </tr>
    </table>
  </form>
</div>
<div class="subheaders">
  <table width="100%" border="0" cellspacing="1" cellpadding="5" class="tableborder">
    <tr>
      <td width="25%" class="headers"><a href="index.php?do=phrases&amp;sort={$namesort|default:'name_desc'}&amp;pp={$limit}&amp;page={$smarty.request.page|default:1}{if !empty($smarty.request.q)}&amp;q={$smarty.request.q|urlencode}{/if}">{#Global_Name#}</a></td>
      <td width="65%" class="headers"><a href="index.php?do=phrases&amp;sort={$phrasesort|default:'phrase_desc'}&amp;pp={$limit}&amp;page={$smarty.request.page|default:1}{if !empty($smarty.request.q)}&amp;q={$smarty.request.q|urlencode}{/if}">{#Soderzanie#}</a></td>
      <td width="10%" class="headers">{#Global_Actions#}</td>
    </tr>
    {foreach from=$items item=item}
      <tr class="{cycle values='second,first'}">
        <td><strong>{$item->name|sanitize}</strong></td>
        <td>{$item->phrase|sanitize}</td>
        <td nowrap="nowrap" width="10%">
          <a class="stip" title="{$lang.Edit|sanitize}" href="index.php?do=phrases&amp;sub=show&amp;id={$item->id}&amp;pp={$limit}&amp;page={$smarty.request.page}{if !empty($smarty.request.q)}&amp;q={$smarty.request.q|urlencode}{/if}#2"><img class="absmiddle" src="{$imgpath}/edit.png" alt="" border="0" /></a>&nbsp;
            {if $item->active == 1}
            <a class="stip" title="{$lang.Global_Active|sanitize}" href="index.php?do=phrases&amp;sub=aktiv&amp;type=0&amp;id={$item->id}&amp;pp={$limit}&amp;page={$smarty.request.page}{if !empty($smarty.request.q)}&amp;q={$smarty.request.q|urlencode}{/if}"><img class="absmiddle" src="{$imgpath}/opened.png" alt="" border="0" /></a>&nbsp;
            {else}
            <a class="stip" title="{$lang.Global_Inactive|sanitize}" href="index.php?do=phrases&amp;sub=aktiv&amp;type=1&amp;id={$item->id}&amp;pp={$limit}&amp;page={$smarty.request.page}{if !empty($smarty.request.q)}&amp;q={$smarty.request.q|urlencode}{/if}"><img class="absmiddle" src="{$imgpath}/closed.png" alt="" border="0" /></a>&nbsp;
            {/if}
          <a class="stip" title="{$lang.Global_Delete|sanitize}" href="index.php?do=phrases&amp;sub=del&amp;id={$item->id}&amp;pp={$limit}&amp;page={$smarty.request.page}{if !empty($smarty.request.q)}&amp;q={$smarty.request.q|urlencode}{/if}"><img class="absmiddle" src="{$imgpath}/delete.png" alt="" border="0" /></a></td>
      </tr>
    {/foreach}
    {if !empty($navi)}
      <tr>
        <td class="first" colspan="4"> {$navi} </td>
      </tr>
    {/if}
  </table>
</div>
<br />
{if $vkl != 1}
  <div class="header">{if $error == 1}<a id="2"></a>{/if}{#Global_Add#}</div>
  <div class="subheaders">
    <form action="index.php?do=phrases&amp;sub=new&amp;pp={$limit}&amp;page={$smarty.request.page}{if !empty($smarty.request.q)}&amp;q={$smarty.request.q|urlencode}{/if}#2" method="post">
      <table width="100%" border="0" cellpadding="5" cellspacing="1">
        <tr>
          <td width="120" class="row_left">{#Global_Name#}</td>
          <td class="row_left"><input name="Name" type="text" value="{if $error == 1}{$smarty.request.Name}{/if}" size="40" /></td>
        </tr>
        <tr>
          <td width="120" class="row_left">{#Soderzanie#}</td>
          <td class="row_left"><textarea name="phrase" cols="" rows="8" style="width: 100%">{if $error == 1}{$smarty.request.phrase}{/if}</textarea></td>
        </tr>
        <tr>
          <td width="120">&nbsp;</td>
          <td><input name="Senden" type="submit" class="button" value="{#Save#}" /></td>
        </tr>
      </table>
    </form>
  </div>
{else}
  <div class="header"><a id="2"></a>{#Edit#}</div>
  <div class="subheaders">
    <form action="index.php?do=phrases&amp;sub=edit&amp;pp={$limit}&amp;page={$smarty.request.page}{if !empty($smarty.request.q)}&amp;q={$smarty.request.q|urlencode}{/if}#2" method="post">
      <table width="100%" border="0" cellpadding="5" cellspacing="1">
        <tr>
          <td width="120" class="row_left">{#Global_Name#}</td>
          <td class="row_left"><input name="Name" type="text" value="{if $error == 1}{$smarty.request.Name}{else}{$row->name}{/if}" size="40" /></td>
        </tr>
        <tr>
          <td width="120" class="row_left">{#Soderzanie#}</td>
          <td class="row_left"><textarea name="text_edit" cols="" rows="8" style="width: 100%">{if $error == 1}{$smarty.request.text_edit}{else}{$row->phrase}{/if}</textarea></td>
        </tr>
        <tr>
          <td width="120">&nbsp;</td>
          <td>
            <input type="hidden" name="id" value="{$smarty.request.id}" />
            <input name="Senden" type="submit" class="button" value="{#Save#}" />
          </td>
        </tr>
      </table>
    </form>
  </div>
{/if}
