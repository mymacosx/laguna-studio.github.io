<div class="header">{#Ping#}</div>
<div class="subheaders">
  {if $admin_settings.Ahelp == 1}
    <a class="colorbox" href="index.php?do=help&amp;sub={$helpquery}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/s_help.png" alt="" border="0" /> {#GlobalHelp#}</a>&nbsp;&nbsp;&nbsp;
    {/if}
  <a class="colorbox" href="index.php?do=support&amp;sub=send_order&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/send.png" alt="" border="0" /> {#SendOrder#}</a>
</div>
<form method="post" action="index.php?do=seo&amp;sub=ping">
  <table border="0" cellspacing="3" cellpadding="0">
    <tr>
      <td>{#Search#}: &nbsp;</td>
      <td width="130"><input style="width: 120px" class="input" type="text" name="q" id="q" value="{$smarty.request.q|default:''}" /></td>
      <td>{#DataRecords#}: &nbsp;</td>
      <td width="50"><input class="input" style="width: 30px" type="text" name="pp" id="pp" value="{$limit}" /></td>
      <td><input name="Senden" type="submit" class="button" value="{#Global_search_b#}" />&nbsp;&nbsp;</td>
      <td><input type="button" class="button" onclick="location.href = 'index.php?do=seo&amp;sub=ping';" value="{#ButtonReset#}" />&nbsp;&nbsp;</td>
        {if perm('seo_p_del')}
        <td><input type="button" class="button" onclick="location.href = 'index.php?do=seo&amp;sub=delall_p';" value="{#DelAll#}" /></td>
        {/if}
    </tr>
  </table>
</form>
<table width="100%" border="0" cellspacing="1" cellpadding="5" class="tableborder">
  <tr>
    <td class="headers"><a href="index.php?do=seo&amp;sub=ping&amp;sort={$textsort|default:'text_desc'}&amp;pp={$limit}&amp;page={$smarty.request.page|default:1}{if !empty($smarty.request.q)}&amp;q={$smarty.request.q|urlencode}{/if}">{#Links_Link#}</a></td>
    <td width="80" class="headers">{#Global_Actions#}</td>
  </tr>
  {foreach from=$items item=item}
    <tr class="{cycle values='second,first'}">
      <td>{$item->Dokument|sanitize}</td>
      <td nowrap="nowrap" width="80">
        {if perm('seo_p_edit')}
          <a class="stip" title="{$lang.Edit|sanitize}" href="index.php?do=seo&amp;sub=edit_show_p&amp;id={$item->Id}&amp;pp={$limit}&amp;page={$smarty.request.page}{if !empty($smarty.request.q)}&amp;q={$smarty.request.q|urlencode}{/if}#2"><img class="absmiddle" src="{$imgpath}/edit.png" alt="" border="0" /></a>&nbsp;
            {if $item->Aktiv == 1}
            <a class="stip" title="{$lang.Global_Active|sanitize}" href="index.php?do=seo&amp;sub=aktiv_p&amp;type=0&amp;id={$item->Id}&amp;pp={$limit}&amp;page={$smarty.request.page}{if !empty($smarty.request.q)}&amp;q={$smarty.request.q|urlencode}{/if}"><img class="absmiddle" src="{$imgpath}/opened.png" alt="" border="0" /></a>&nbsp;
            {else}
            <a class="stip" title="{$lang.Global_Inactive|sanitize}" href="index.php?do=seo&amp;sub=aktiv_p&amp;type=1&amp;id={$item->Id}&amp;pp={$limit}&amp;page={$smarty.request.page}{if !empty($smarty.request.q)}&amp;q={$smarty.request.q|urlencode}{/if}"><img class="absmiddle" src="{$imgpath}/closed.png" alt="" border="0" /></a>&nbsp;
            {/if}
          {/if}
          {if perm('seo_p_del')}
          <a class="stip" title="{$lang.Global_Delete|sanitize}" href="index.php?do=seo&amp;sub=del_p&amp;id={$item->Id}&amp;pp={$limit}&amp;page={$smarty.request.page}{if !empty($smarty.request.q)}&amp;q={$smarty.request.q|urlencode}{/if}"><img class="absmiddle" src="{$imgpath}/delete.png" alt="" border="0" /></a>
          {/if}
      </td>
    </tr>
  {/foreach}
  {if !empty($navi)}
    <tr>
      <td class="first" colspan="4"> {$navi} </td>
    </tr>
  {/if}
</table>
<br />
{if !isset($vkl) || $vkl != 1}
  {if perm('seo_p_add')}
    <div class="header">{if isset($error) && $error == 1}<a name="2"></a>{/if}{#Global_Add#}</div>
    <form action="index.php?do=seo&amp;sub=new_p&amp;pp={$limit}&amp;page={$smarty.request.page}{if !empty($smarty.request.q)}&amp;q={$smarty.request.q|urlencode}{/if}#2" method="post">
      <table width="100%" border="0" cellpadding="5" cellspacing="1">
        <tr>
          <td width="40" class="row_left">{#Links_Link#}</td>
          <td class="row_left"><textarea name="text" cols="" rows="3" style="width: 100%">{if isset($error) && $error == 1}{$smarty.request.text|default:''}{/if}</textarea></td>
        </tr>
        <tr>
          <td width="120">&nbsp;</td>
          <td><input name="Senden" type="submit" class="button" value="{#Save#}" /></td>
        </tr>
      </table>
    </form>
  {/if}
{else}
  {if perm('seo_p_edit')}
    <div class="header"><a name="2"></a>{#Edit#}</div>
    <form action="index.php?do=seo&amp;sub=edit_p&amp;pp={$limit}&amp;page={$smarty.request.page}{if !empty($smarty.request.q)}&amp;q={$smarty.request.q|urlencode}{/if}#2" method="post">
      <table width="100%" border="0" cellpadding="5" cellspacing="1">
        <tr>
          <td width="40" class="row_left">{#Links_Link#}</td>
          <td class="row_left"><textarea name="text_edit" cols="" rows="3" style="width: 100%">{if isset($error) && $error == 1}{$smarty.request.text_edit|default:''}{else}{$row->Dokument}{/if}</textarea></td>
        </tr>
        <tr>
          <td width="120">&nbsp;</td>
          <td><input type="hidden" name="id" value="{$smarty.request.id}" />
            <input name="Senden" type="submit" class="button" value="{#Save#}" /></td>
        </tr>
      </table>
    </form>
  {/if}
{/if}
<br />
<div class="header"><a name="3"></a>{#Ping_Send#}</div>
<form action="index.php?do=seo&amp;sub=send_p&amp;pp={$limit}&amp;page={$smarty.request.page}{if !empty($smarty.request.q)}&amp;q={$smarty.request.q|urlencode}{/if}#3" method="post">
  <table border="0" cellpadding="5" cellspacing="1">
    <tr>
      <td>{#Global_Name#}: </td>
      <td width="210"><input class="input" style="width: 200px" type="text" name="name_p" id="name_p" value="{$smarty.request.name_p|default:''}" /></td>
      <td>{#Links_Link#}: </td>
      <td width="210"><input style="width: 200px" class="input" type="text" name="link_p" id="link_p" value="{$smarty.request.link_p|default:''}" /></td>
      <td><input name="Senden" type="submit" class="button" value="{#Go_Button#}" /></td>
    </tr>
  </table>
</form>
