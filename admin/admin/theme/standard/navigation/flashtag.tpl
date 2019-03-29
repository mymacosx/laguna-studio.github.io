<div class="header">{#Flashtag#}</div>
<div class="subheaders">
  {if $admin_settings.Ahelp == 1}
    <a class="colorbox" href="index.php?do=help&amp;sub={$helpquery}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/s_help.png" alt="" border="0" /> {#GlobalHelp#}</a>&nbsp;&nbsp;&nbsp;
    {/if}
  <a class="colorbox" href="index.php?do=support&amp;sub=send_order&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/send.png" alt="" border="0" /> {#SendOrder#}</a>
</div>
<div class="subheaders">
  <form method="post" action="index.php?do=navigation&amp;sub=flashtag">
    <table border="0" cellspacing="3" cellpadding="0">
      <tr>
        <td>{#Search#}: &nbsp;</td>
        <td width="130"><input style="width: 120px" class="input" type="text" name="q" id="q" value="{if isset($smarty.request.q)}{$smarty.request.q}{/if}" /></td>
        <td>{#DataRecords#}: &nbsp;</td>
        <td width="50"><input class="input" style="width: 30px" type="text" name="pp" id="pp" value="{$limit}" /></td>
        <td><input name="Senden" type="submit" class="button" value="{#Global_search_b#}" />&nbsp;&nbsp;</td>
        <td><input type="button" class="button" onclick="location.href = 'index.php?do=navigation&amp;sub=flashtag';" value="{#ButtonReset#}" />&nbsp;&nbsp;</td>
          {if perm('navigation_edit')}
          <td><input type="button" class="button" onclick="location.href = 'index.php?do=navigation&amp;sub=delall_ft';" value="{#DelAll#}" /></td>
          {/if}
      </tr>
    </table>
  </form>
</div>
<div class="subheaders">
  <table width="100%" border="0" cellspacing="1" cellpadding="5" class="tableborder">
    <tr>
      <td width="30%" class="headers"><a href="index.php?do=navigation&amp;sub=flashtag&amp;sort={$namesort|default:'name_desc'}&amp;pp={$limit}&amp;page={$smarty.request.page|default:1}{if !empty($smarty.request.q)}&amp;q={$smarty.request.q|urlencode}{/if}">{#NaviHrefTitle#}</a></td>
      <td width="30%" class="headers"><a href="index.php?do=navigation&amp;sub=flashtag&amp;sort={$sizesort|default:'size_desc'}&amp;pp={$limit}&amp;page={$smarty.request.page|default:1}{if !empty($smarty.request.q)}&amp;q={$smarty.request.q|urlencode}{/if}">{#GlobalSize#}</a></td>
      <td width="30%" class="headers"><a href="index.php?do=navigation&amp;sub=flashtag&amp;sort={$docsort|default:'doc_desc'}&amp;pp={$limit}&amp;page={$smarty.request.page|default:1}{if !empty($smarty.request.q)}&amp;q={$smarty.request.q|urlencode}{/if}">{#Links_Link#}</a></td>
      <td width="10%" class="headers">{#Global_Actions#}</td>
    </tr>
    {foreach from=$items item=item}
      <tr class="{cycle values='second,first'}">
        <td>{$item->Title|sanitize}</td>
        <td>{$item->Size}</td>
        <td>{$item->Dokument|sanitize}</td>
        <td nowrap="nowrap" width="10%">
          <a class="stip" title="{$lang.Edit|sanitize}" href="index.php?do=navigation&amp;sub=edit_show_ft&amp;id={$item->Id}&amp;pp={$limit}&amp;page={$smarty.request.page}{if !empty($smarty.request.q)}&amp;q={$smarty.request.q|urlencode}{/if}#2"><img class="absmiddle" src="{$imgpath}/edit.png" alt="" border="0" /></a>&nbsp;
            {if $item->Aktiv == 1}
            <a class="stip" title="{$lang.Global_Active|sanitize}" href="index.php?do=navigation&amp;sub=aktiv_ft&amp;type=0&amp;id={$item->Id}&amp;pp={$limit}&amp;page={$smarty.request.page}{if !empty($smarty.request.q)}&amp;q={$smarty.request.q|urlencode}{/if}"><img class="absmiddle" src="{$imgpath}/opened.png" alt="" border="0" /></a>&nbsp;
            {else}
            <a class="stip" title="{$lang.Global_Inactive|sanitize}" href="index.php?do=navigation&amp;sub=aktiv_ft&amp;type=1&amp;id={$item->Id}&amp;pp={$limit}&amp;page={$smarty.request.page}{if !empty($smarty.request.q)}&amp;q={$smarty.request.q|urlencode}{/if}"><img class="absmiddle" src="{$imgpath}/closed.png" alt="" border="0" /></a>&nbsp;
            {/if}
          <a class="stip" title="{$lang.Global_Delete|sanitize}" href="index.php?do=navigation&amp;sub=del_ft&amp;id={$item->Id}&amp;pp={$limit}&amp;page={$smarty.request.page}{if !empty($smarty.request.q)}&amp;q={$smarty.request.q|urlencode}{/if}"><img class="absmiddle" src="{$imgpath}/delete.png" alt="" border="0" /></a>
        </td>
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
    <form action="index.php?do=navigation&amp;sub=new_ft&amp;pp={$limit}&amp;page={$smarty.request.page}{if !empty($smarty.request.q)}&amp;q={$smarty.request.q|urlencode}{/if}#2" method="post">
      <table width="100%" border="0" cellpadding="5" cellspacing="1">
        <tr>
          <td width="300" nowrap="nowrap">{#NaviHrefTitle#}: &nbsp; <input style="width: 200px" class="input" type="text" name="title_add" id="title_add" value="{if $error == 1}{$smarty.request.title_add}{/if}" /></td>
          <td width="100" nowrap="nowrap">{#GlobalSize#}: &nbsp;
            <select class="input" name="size_add">
              <option value="5" {if $error == 1 && $smarty.request.size_add == 5}selected="selected"{/if}>5</option>
              <option value="6" {if $error == 1 && $smarty.request.size_add == 6}selected="selected"{/if}>6</option>
              <option value="7" {if $error == 1 && $smarty.request.size_add == 7}selected="selected"{/if}>7</option>
              <option value="8" {if $error == 1 && $smarty.request.size_add == 8}selected="selected"{/if}>8</option>
              <option value="9" {if $error == 1 && $smarty.request.size_add == 9}selected="selected"{/if}>9</option>
              <option value="10" {if $error == 1 && $smarty.request.size_add == 10}selected="selected"{else}selected="selected"{/if}>10</option>
              <option value="11" {if $error == 1 && $smarty.request.size_add == 11}selected="selected"{/if}>11</option>
              <option value="12" {if $error == 1 && $smarty.request.size_add == 12}selected="selected"{/if}>12</option>
              <option value="13" {if $error == 1 && $smarty.request.size_add == 13}selected="selected"{/if}>13</option>
              <option value="14" {if $error == 1 && $smarty.request.size_add == 14}selected="selected"{/if}>14</option>
              <option value="15" {if $error == 1 && $smarty.request.size_add == 15}selected="selected"{/if}>15</option>
              <option value="16" {if $error == 1 && $smarty.request.size_add == 16}selected="selected"{/if}>16</option>
              <option value="17" {if $error == 1 && $smarty.request.size_add == 17}selected="selected"{/if}>17</option>
              <option value="18" {if $error == 1 && $smarty.request.size_add == 18}selected="selected"{/if}>18</option>
              <option value="19" {if $error == 1 && $smarty.request.size_add == 19}selected="selected"{/if}>19</option>
              <option value="20" {if $error == 1 && $smarty.request.size_add == 20}selected="selected"{/if}>20</option>
            </select>
          </td>
          <td nowrap="nowrap">{#Links_Link#}: &nbsp; <input style="width: 200px" class="input" type="text" name="url_add" id="url_add" value="{if $error == 1}{$smarty.request.url_add}{else}http://{/if}" /></td>
        </tr>
        <tr>
          <td colspan="3"><input name="Senden" type="submit" class="button" value="{#Save#}" /></td>
        </tr>
      </table>
    </form>
  </div>
{else}
  <div class="header"><a id="2"></a>{#Edit#}</div>
  <div class="subheaders">
    <form action="index.php?do=navigation&amp;sub=edit_ft&amp;pp={$limit}&amp;page={$smarty.request.page}{if !empty($smarty.request.q)}&amp;q={$smarty.request.q|urlencode}{/if}#2" method="post">
      <table width="100%" border="0" cellpadding="5" cellspacing="1">
        <tr>
          <td width="300" nowrap="nowrap">{#NaviHrefTitle#}: &nbsp; <input style="width: 200px" class="input" type="text" name="title_edit" id="title_edit" value="{if $error == 1}{$smarty.request.title_edit}{else}{$row->Title}{/if}" /></td>
          <td width="100" nowrap="nowrap">{#GlobalSize#}: &nbsp;
            <select class="input" name="size_edit">
              <option value="5" {if $error == 1}{if $smarty.request.size_edit ==  5}selected="selected"{/if}{else}{if $row->Size == 5}selected="selected"{/if}{/if}>5</option>
              <option value="6" {if $error == 1}{if $smarty.request.size_edit == 6}selected="selected"{/if}{else}{if $row->Size == 6}selected="selected"{/if}{/if}>6</option>
              <option value="7" {if $error == 1}{if $smarty.request.size_edit == 7}selected="selected"{/if}{else}{if $row->Size == 7}selected="selected"{/if}{/if}>7</option>
              <option value="8" {if $error == 1}{if $smarty.request.size_edit == 8}selected="selected"{/if}{else}{if $row->Size == 8}selected="selected"{/if}{/if}>8</option>
              <option value="9" {if $error == 1}{if $smarty.request.size_edit == 9}selected="selected"{/if}{else}{if $row->Size == 9}selected="selected"{/if}{/if}>9</option>
              <option value="10" {if $error == 1}{if $smarty.request.size_edit == 10}selected="selected"{/if}{else}{if $row->Size == 10}selected="selected"{/if}{/if}>10</option>
              <option value="11" {if $error == 1}{if $smarty.request.size_edit == 11}selected="selected"{/if}{else}{if $row->Size == 11}selected="selected"{/if}{/if}>11</option>
              <option value="12" {if $error == 1}{if $smarty.request.size_edit == 12}selected="selected"{/if}{else}{if $row->Size == 12}selected="selected"{/if}{/if}>12</option>
              <option value="13" {if $error == 1}{if $smarty.request.size_edit == 13}selected="selected"{/if}{else}{if $row->Size == 13}selected="selected"{/if}{/if}>13</option>
              <option value="14" {if $error == 1}{if $smarty.request.size_edit == 14}selected="selected"{/if}{else}{if $row->Size == 14}selected="selected"{/if}{/if}>14</option>
              <option value="15" {if $error == 1}{if $smarty.request.size_edit == 15}selected="selected"{/if}{else}{if $row->Size == 15}selected="selected"{/if}{/if}>15</option>
              <option value="16" {if $error == 1}{if $smarty.request.size_edit == 16}selected="selected"{/if}{else}{if $row->Size == 16}selected="selected"{/if}{/if}>16</option>
              <option value="17" {if $error == 1}{if $smarty.request.size_edit == 17}selected="selected"{/if}{else}{if $row->Size == 17}selected="selected"{/if}{/if}>17</option>
              <option value="18" {if $error == 1}{if $smarty.request.size_edit == 18}selected="selected"{/if}{else}{if $row->Size == 18}selected="selected"{/if}{/if}>18</option>
              <option value="19" {if $error == 1}{if $smarty.request.size_edit == 19}selected="selected"{/if}{else}{if $row->Size == 19}selected="selected"{/if}{/if}>19</option>
              <option value="20" {if $error == 1}{if $smarty.request.size_edit == 20}selected="selected"{/if}{else}{if $row->Size == 20}selected="selected"{/if}{/if}>20</option>
            </select>
          </td>
          <td nowrap="nowrap">{#Links_Link#}: &nbsp; <input style="width: 200px" class="input" type="text" name="url_edit" id="url_edit" value="{if $error == 1}{$smarty.request.url_edit}{else}{$row->Dokument}{/if}" /></td>
        </tr>
        <tr>
          <td colspan="3">
            <input name="Senden" type="submit" class="button" value="{#Save#}" />
            <input type="hidden" name="id" value="{$smarty.request.id}" />
          </td>
        </tr>
      </table>
    </form>
  </div>
{/if}
