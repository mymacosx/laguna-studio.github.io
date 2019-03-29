<div class="header">{#Faq#}</div>
<div class="subheaders">
  <a title="{#Faq_new#}" class="colorbox" href="index.php?do=faq&amp;sub=new&amp;noframes=1&amp;langcode=1"><img class="absmiddle" src="{$imgpath}/add.png" alt="" border="0" /> {#Faq_new#}</a>&nbsp;&nbsp;&nbsp;
    {if $admin_settings.Ahelp == 1}
    <a class="colorbox" href="index.php?do=help&amp;sub={$helpquery}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/s_help.png" alt="" border="0" /> {#GlobalHelp#}</a>&nbsp;&nbsp;&nbsp;
    {/if}
  <a class="colorbox" href="index.php?do=support&amp;sub=send_order&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/send.png" alt="" border="0" /> {#SendOrder#}</a>
</div>
{if !$categs}
  <div class="info_red">{#GlobalNoCateg#}</div>
{else}
  <form action="" method="post" name="kform" id="kform">
    <table width="100%" border="0" cellpadding="3" cellspacing="0" class="tableborder">
      {foreach from=$categs item=cat}
        <tr class="second">
          <td class="headers">{$cat->visible_title|sanitize}</td>
          <td width="50" align="center" class="headers">{#Global_Author#}</td>
          <td width="150" align="center" class="headers">{#Global_Categ#}</td>
          <td width="50" align="center" class="headers">{#Global_Active#}</td>
          <td width="10" align="center" class="headers">{#Global_Position#}</td>
          <td width="60" class="headers">{#Global_Actions#}</td>
        </tr>
        {foreach from=$faq item=item}
          {if $cat->Id == $item->Kategorie}
            <tr class="{cycle values='second,first'}">
              <td><a target="_blank" href="../index.php?p=faq&amp;action=faq&amp;fid={$item->Id}&amp;area={$area}&amp;name={$item->visible_title|translit}">{$item->visible_title|sanitize}</a></td>
              <td align="center"><a class="colorbox" href="index.php?do=user&amp;sub=edituser&amp;user={$item->Benutzer}&amp;noframes=1">{$item->User}</a></td>
              <td width="150" align="center">
                <select  style="width: 150px" class="input" name="Kategorie[{$item->Id}]">
                  {foreach from=$categs item=dd}
                    <option value="{$dd->Id}" {if $item->Kategorie == $dd->Id}selected="selected"{/if}>{$dd->visible_title} </option>
                  {/foreach}
                </select>
              </td>
              <td align="center">
                <select class="input" name="Aktiv[{$item->Id}]">
                  <option value="1" {if $item->Aktiv == 1}selected="selected"{/if}>{#Yes#}</option>
                  <option value="0" {if $item->Aktiv == 0}selected="selected"{/if}>{#No#}</option>
                </select>
              </td>
              <td align="center"><input class="input" type="text" value="{$item->Position}" name="Position[{$item->Id}]" size="3" maxlength="5" /></td>
              <td>
                <a class="colorbox stip" title="{$lang.Faq_edit|sanitize}" href="index.php?do=faq&amp;sub=edit&amp;categ={$item->Kategorie}&amp;id={$item->Id}&amp;noframes=1&amp;langcode=1"><img class="absmiddle" src="{$imgpath}/edit.png" alt="" border="0" /></a>
                <a class="colorbox stip" title="{$lang.Faq_new|sanitize}" href="index.php?do=faq&amp;sub=new&amp;categ={$item->Kategorie}&amp;id={$item->Id}&amp;noframes=1&amp;langcode=1"><img class="absmiddle" src="{$imgpath}/add.png" alt="" border="0" /></a>
                <a class="stip" title="{$lang.Global_Delete|sanitize}" onclick="return confirm('{#ConfirmGlobal#}{$item->visible_title|jsspecialchars}');" href="index.php?do=faq&amp;sub=delete&amp;id={$item->Id}&amp;langcode=1&amp;backurl={$backurl}"><img class="absmiddle" src="{$imgpath}/delete.png" alt="" border="0" /></a>
              </td>
            </tr>
          {/if}
        {/foreach}
      {/foreach}
    </table>
    {if $faq}
      <input name="Senden" type="submit" class="button" value="{#Save#}" />
      <input name="save" type="hidden" id="save" value="1" />
    {/if}
  </form>
{/if}
