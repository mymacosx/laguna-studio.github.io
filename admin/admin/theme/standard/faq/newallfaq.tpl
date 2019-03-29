<div class="header">{#NewSendFaq#}</div>
<div class="subheaders">
  <a title="{#Global_NewCateg#}" class="colorbox_small" href="index.php?do=faq&amp;sub=addcateg&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/folder_add.png" alt="" border="0" /> {#Global_NewCateg#}</a>&nbsp;&nbsp;&nbsp;
  <a title="{#Faq_new#}" class="colorbox" href="index.php?do=faq&amp;sub=new&amp;noframes=1&amp;langcode=1"><img class="absmiddle" src="{$imgpath}/add.png" alt="" border="0" /> {#Faq_new#}</a>&nbsp;&nbsp;&nbsp;
    {if $admin_settings.Ahelp == 1}
    <a class="colorbox" href="index.php?do=help&amp;sub={$helpquery}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/s_help.png" alt="" border="0" /> {#GlobalHelp#}</a>&nbsp;&nbsp;&nbsp;
    {/if}
  <a class="colorbox" href="index.php?do=support&amp;sub=send_order&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/send.png" alt="" border="0" /> {#SendOrder#}</a>
</div>
<table width="100%" border="0" cellpadding="3" cellspacing="0" class="tableborder">
  <tr class="second">
    <td class="headers">{#GlobalQuest#}</td>
    <td width="110" align="center" class="headers">{#Global_Email#}</td>
    <td width="110" align="center" class="headers">{#Global_DateTill#}</td>
    <td width="40" class="headers">{#Global_Actions#}</td>
  </tr>
  {foreach from=$newfaq item=item}
    <tr class="{cycle values='second,first'}">
      <td>{$item->Name|sanitize}</td>
      <td align="center"><a href="mailto: {$item->Sender}">{$item->Sender}</a></td>
      <td align="center">{$item->Datum|date_format: "%d.%m.%Y - %H:%M"}</td>
      <td>
        <a class="colorbox stip" title="{$lang.Faq_edit|sanitize}" href="index.php?do=faq&amp;sub=editsendfaq&amp;categ={$item->Kategorie}&amp;id={$item->Id}&amp;noframes=1&amp;langcode=1"><img class="absmiddle" src="{$imgpath}/edit.png" alt="" border="0" /></a>
        <a class="stip" title="{$lang.Global_Delete|sanitize}" onclick="return confirm('{#ConfirmGlobal#}{$item->Name|jsspecialchars}');" href="index.php?do=faq&amp;sub=delete&amp;id={$item->Id}&amp;langcode=1&amp;backurl={$backurl}"><img class="absmiddle" src="{$imgpath}/delete.png" alt="" border="0" /></a>
      </td>
    </tr>
  {/foreach}
</table>
