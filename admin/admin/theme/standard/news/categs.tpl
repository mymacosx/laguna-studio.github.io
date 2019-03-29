<div class="header">{#News_Categs#}</div>
<div class="subheaders">
  <a title="{#Global_NewCateg#}" class="colorbox_small" href="index.php?do=news&amp;sub=addcateg&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/folder_add.png" alt="" border="0" /> {#Global_NewCateg#}</a>&nbsp;&nbsp;&nbsp;
    {if $admin_settings.Ahelp == 1}
    <a class="colorbox" href="index.php?do=help&amp;sub={$helpquery}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/s_help.png" alt="" border="0" /> {#GlobalHelp#}</a>&nbsp;&nbsp;&nbsp;
    {/if}
  <a class="colorbox" href="index.php?do=support&amp;sub=send_order&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/send.png" alt="" border="0" /> {#SendOrder#}</a>
</div>
<table width="100%" border="0" cellspacing="0" cellpadding="3">
  <tr class="headers">
    <td class="headers">{#Global_Name#}</td>
    <td class="headers">{#Navigation_doc#}</td>
    <td class="headers">{#Global_Actions#}</td>
  </tr>
  {foreach from=$newscategs item=g}
    <tr class="{cycle values='second,first'}">
      <td>
        {if $g->Parent_Id == 0}
          <strong>{$g->Name|sanitize}</strong>
        {else}
          {$g->visible_title|sanitize}
        {/if}
      </td>
      <td><a target="_blank" href="../index.php?p=newsarchive&amp;area={$area}&amp;catid={$g->Id}&amp;name={$g->Name|translit}">index.php?p=newsarchive&amp;area={$area}&amp;catid={$g->Id}&amp;name={$g->Name|translit}</a></td>
      <td>
        <a class="colorbox_small stip" title="{$lang.Global_CategEdit|sanitize}" href="index.php?do=news&amp;sub=editcateg&amp;id={$g->Id}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/edit.png" alt="" border="0" /></a>
          {if perm('news_category')}
          <a onclick="return confirm('{#ConfirmGlobal#}{$g->Name|jsspecialchars}');" href="index.php?do=news&amp;sub=delcateg&amp;id={$g->Id}&amp;backurl={$backurl}"><img class="absmiddle" src="{$imgpath}/delete.png" alt="" border="0" /></a>
          {/if}
      </td>
    </tr>
  {/foreach}
</table>
