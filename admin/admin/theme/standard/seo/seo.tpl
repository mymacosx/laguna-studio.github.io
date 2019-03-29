<div class="header">{#Description#}</div>
<div class="subheaders">
  {if $admin_settings.Ahelp == 1}
    <a class="colorbox" href="index.php?do=help&amp;sub={$helpquery}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/s_help.png" alt="" border="0" /> {#GlobalHelp#}</a>&nbsp;&nbsp;&nbsp;
    {/if}
  <a class="colorbox" href="index.php?do=support&amp;sub=send_order&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/send.png" alt="" border="0" /> {#SendOrder#}</a>
</div>
<table width="600" border="0" cellpadding="0" cellspacing="3">
  {if perm('seo_imex')}
    <tr>
      <td>
        <form method="post" action="index.php?do=seo&amp;sub=import_d" enctype="multipart/form-data">
          <input name="file_description" type="file" id="file_description" size="30" />
          <input class="button" style="width: 110px" type="submit" value="{#Imp_button#}" />
        </form>
      </td>
      <td>
        <input type="button" style="width: 110px" class="button" onclick="location.href = 'index.php?do=seo&amp;sub=export_d';" value="{#Export_button#}" />
        {if perm('seo_del')}
          <input type="button" style="width: 100px" class="button" onclick="location.href = 'index.php?do=seo&amp;sub=delall_d';" value="{#DelAll#}" />
        {/if}
      </td>
    </tr>
  {/if}
  <form method="post" action="index.php?do=seo&amp;sub=description">
    <tr>
      <td>
        {#Search#}: &nbsp;&nbsp;<input style="width: 165px" class="input" type="text" name="q" id="q" value="{$smarty.request.q|default:''}" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        {#DataRecords#}: &nbsp;&nbsp;<input class="input" style="width: 30px" type="text" name="pp" id="pp" value="{$limit}" />
      </td>
      <td>
        <input name="Senden" style="width: 110px" type="submit" class="button" value="{#Global_search_b#}" />
        <input type="button" style="width: 100px" class="button" onclick="location.href = 'index.php?do=seo&amp;sub=description';" value="{#ButtonReset#}" />
      </td>
    </tr>
  </form>
</table>
<table width="100%" border="0" cellspacing="1" cellpadding="5" class="tableborder">
  <tr>
    <td class="headers"><a href="index.php?do=seo&amp;sub=description&amp;sort={$textsort|default:'text_desc'}&amp;pp={$limit}&amp;page={$smarty.request.page|default:1}{if !empty($smarty.request.q)}&amp;q={$smarty.request.q|urlencode}{/if}">{#Soderzanie#}</a></td>
    <td width="10%" class="headers">{#Global_Actions#}</td>
  </tr>
  {foreach from=$items item=item}
    <tr class="{cycle values='second,first'}">
      <td>{$item->Text|sanitize}</td>
      <td nowrap="nowrap" width="10%">
        {if perm('seo_edit')}
          <a class="stip" title="{$lang.Edit|sanitize}" href="index.php?do=seo&amp;sub=edit_show_d&amp;id={$item->Id}&amp;pp={$limit}&amp;page={$smarty.request.page}{if !empty($smarty.request.q)}&amp;q={$smarty.request.q|urlencode}{/if}#2"><img class="absmiddle" src="{$imgpath}/edit.png" alt="" border="0" /></a>&nbsp;
            {if $item->Aktiv == 1}
            <a class="stip" title="{$lang.Global_Active|sanitize}" href="index.php?do=seo&amp;sub=aktiv_d&amp;type=0&amp;id={$item->Id}&amp;pp={$limit}&amp;page={$smarty.request.page}{if !empty($smarty.request.q)}&amp;q={$smarty.request.q|urlencode}{/if}"><img class="absmiddle" src="{$imgpath}/opened.png" alt="" border="0" /></a>&nbsp;
            {else}
            <a class="stip" title="{$lang.Global_Inactive|sanitize}" href="index.php?do=seo&amp;sub=aktiv_d&amp;type=1&amp;id={$item->Id}&amp;pp={$limit}&amp;page={$smarty.request.page}{if !empty($smarty.request.q)}&amp;q={$smarty.request.q|urlencode}{/if}"><img class="absmiddle" src="{$imgpath}/closed.png" alt="" border="0" /></a>&nbsp;
            {/if}
          {/if}
          {if perm('seo_del')}
          <a class="stip" title="{$lang.Global_Delete|sanitize}" href="index.php?do=seo&amp;sub=del_d&amp;id={$item->Id}&amp;pp={$limit}&amp;page={$smarty.request.page}{if !empty($smarty.request.q)}&amp;q={$smarty.request.q|urlencode}{/if}"><img class="absmiddle" src="{$imgpath}/delete.png" alt="" border="0" /></a>
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
  {if perm('seo_add')}
    <div class="header">{if isset($error) && $error == 1}<a id="2"></a>{/if}{#Global_Add#}</div>
    <form action="index.php?do=seo&amp;sub=new_d&amp;pp={$limit}&amp;page={$smarty.request.page}{if !empty($smarty.request.q)}&amp;q={$smarty.request.q|urlencode}{/if}#2" method="post">
      <table width="100%" border="0" cellpadding="5" cellspacing="1">
        <tr>
          <td width="120" class="row_left">{#Soderzanie#}</td>
          <td class="row_left"><textarea name="text" cols="" rows="8" style="width: 60%">{if isset($error) && $error == 1}{$smarty.request.text}{/if}</textarea></td>
        </tr>
        <tr>
          <td width="120">&nbsp;</td>
          <td><input name="Senden" type="submit" class="button" value="{#Save#}" /></td>
        </tr>
      </table>
    </form>
  </div>
{/if}
{else}
  {if perm('seo_edit')}
    <div class="header"><a id="2"></a>{#Edit#}</div>
    <form action="index.php?do=seo&amp;sub=edit_d&amp;pp={$limit}&amp;page={$smarty.request.page}{if !empty($smarty.request.q)}&amp;q={$smarty.request.q|urlencode}{/if}#2" method="post">
      <table width="100%" border="0" cellpadding="5" cellspacing="1">
        <tr>
          <td width="120" class="row_left">{#Soderzanie#}</td>
          <td class="row_left"><textarea name="text_edit" cols="" rows="8" style="width: 100%">{if isset($error) && $error == 1}{$smarty.request.text_edit|default:''}{else}{$row->Text}{/if}</textarea></td>
        </tr>
        <tr>
          <td width="120">&nbsp;</td>
          <td><input type="hidden" name="id" value="{$smarty.request.id|default:''}" />
            <input name="Senden" type="submit" class="button" value="{#Save#}" /></td>
        </tr>
      </table>
    </form>
  {/if}
{/if}