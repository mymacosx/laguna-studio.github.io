<div class="header">
  {if $navigation}
    {$navigation}
  {else}
    <a href="index.php?do=gallery">{#Gallery#}</a> - <a href="index.php?do=gallery&amp;sub=showincluded&amp;id={$smarty.request.id}">{$res->Name_1|sanitize}</a>
  {/if}
</div>
<div class="subheaders">
  <a title="{#Gallery_addNew#}" class="colorbox" href="index.php?do=gallery&amp;sub=addgallery&amp;id={$smarty.request.id}&amp;subg=false&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/folder_add.png" alt="" border="0" /> {#Gallery_addNew#}</a>&nbsp;&nbsp;&nbsp;
    {if $admin_settings.Ahelp == 1}
    <a class="colorbox" href="index.php?do=help&amp;sub={$helpquery}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/s_help.png" alt="" border="0" /> {#GlobalHelp#}</a>&nbsp;&nbsp;&nbsp;
    {/if}
  <a class="colorbox" href="index.php?do=support&amp;sub=send_order&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/send.png" alt="" border="0" /> {#SendOrder#}</a>
</div>
<div class="subheaders">
  <form action="index.php?do=gallery&amp;sub=showincluded&amp;id={$smarty.request.id}" method="post"name="kform" id="kform">
    <table width="100%" border="0" cellspacing="0" cellpadding="1">
      <tr>
        <td width="100"><label for="qs">{#Search#}</label></td>
        <td width="100"><input class="input" style="width: 150px" type="text" name="q" id="qs" value="{$smarty.request.q|replace: 'empty': ''}" /></td>
        <td width="100" align="right"><label for="sg">{#Gallery_sIn#} </label></td>
        <td>
          <select class="input" style="width: 200px" name="subgallery" id="sg">
            <option value="0">-</option>
            {foreach from=$gallery_dd item=item}
              {if $item->Parent_Id == 0}
                <option value="{$item->Id}" {if $smarty.request.subgallery == $item->Id}selected="selected"{/if}>{$item->visible_title}</option>
              {else}
                <option value="{$item->Id}" {if $smarty.request.subgallery == $item->Id}selected="selected"{/if}>{$item->visible_title}</option>
              {/if}
            {/foreach}
          </select>
        </td>
      </tr>
      <tr>
        <td><label for="dr">{#DataRecords#}</label></td>
        <td>
          <input class="input" style="width: 50px" type="text" name="pp" id="dr" value="{$limit}">
          <input name="Senden" type="submit" class="button" value="{#Search#}" />
        </td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
    </table>
  </form>
</div>
<form action="" method="post"name="kform" id="kform">
  <table width="100%" border="0" cellpadding="3" cellspacing="0" class="tableborder">
    <tr>
      <td class="headers"><a href="index.php?do=gallery&amp;sub=showincluded&amp;id={$smarty.request.id}&amp;q={if isset($smarty.request.q)}{$smarty.request.q}{/if}&amp;page={$smarty.request.page|default:1}&amp;sort={$namesort|default:'name_desc'}&amp;subgallery={$smarty.request.subgallery|default:0}&amp;pp={$limit}">{#Global_Name#}</a></td>
      <td width="50" align="center" class="headers"><a href="index.php?do=gallery&amp;sub=showincluded&amp;id={$smarty.request.id}&amp;q={if isset($smarty.request.q)}{$smarty.request.q}{/if}&amp;page={$smarty.request.page|default:1}&amp;sort={$usersort|default:'user_desc'}&amp;subgallery={$smarty.request.subgallery|default:0}&amp;pp={$limit}">{#Global_Author#}</a></td>
      <td width="50" align="center" class="headers"><a href="index.php?do=gallery&amp;sub=showincluded&amp;id={$smarty.request.id}&amp;q={if isset($smarty.request.q)}{$smarty.request.q}{/if}&amp;page={$smarty.request.page|default:1}&amp;sort={$datesort|default:'date_desc'}&amp;subgallery={$smarty.request.subgallery|default:0}&amp;pp={$limit}">{#Global_Date#}</a></td>
      <td width="100" align="center" class="headers"><a href="index.php?do=gallery&amp;sub=showincluded&amp;id={$smarty.request.id}&amp;q={if isset($smarty.request.q)}{$smarty.request.q}{/if}&amp;page={$smarty.request.page|default:1}&amp;sort={$activesort|default:'active_desc'}&amp;subgallery={$smarty.request.subgallery|default:0}&amp;pp={$limit}">{#Global_Active#}</a></td>
      <td width="50" align="center" class="headers"><a href="index.php?do=gallery&amp;sub=showincluded&amp;id={$smarty.request.id}&amp;q={if isset($smarty.request.q)}{$smarty.request.q}{/if}&amp;page={$smarty.request.page|default:1}&amp;sort={$imgsort|default:'img_desc'}&amp;subgallery={$smarty.request.subgallery|default:0}&amp;pp={$limit}">{#Gallery_sIm#}</a></td>
      <td width="80" align="center" class="headers">{#Global_Actions#}</td>
    </tr>
    {foreach from=$gallery item=item}
      <tr class="{cycle values='second,first'}">
        <td>
          <input type="hidden" name="galleryid[{$item->Id}]" value="{$item->Id}" />
          {$item->visible_title}
          <strong><a href="index.php?do=gallery&amp;sub=showincluded&amp;id={$smarty.request.id}&amp;subgallery={$item->Id}">{$item->Name_1|sanitize}</a></strong>
        </td>
        <td width="50" align="center"><a class="colorbox" href="index.php?do=user&sub=edituser&amp;user={$item->Autor}&amp;noframes=1">{$item->User}</a></td>
        <td width="50" align="center">{$item->Datum|date_format: '%d.%m.%y'}</td>
        <td width="100" align="center" nowrap="nowrap">
          <label><input type="radio" name="Aktiv[{$item->Id}]" value="1" {if $item->Aktiv == 1} checked="checked"{/if}/>{#Yes#}</label>
          <label><input type="radio" name="Aktiv[{$item->Id}]" value="0" {if $item->Aktiv == 0} checked="checked"{/if}/>{#No#}</label>
        </td>
        <td width="50" align="center">
          {if $item->Bilder > 0}
            {$item->Bilder}
          {else}-{/if}
        </td>
        <td nowrap="nowrap">
          <a class="colorbox stip" title="{$lang.Edit|sanitize}" href="index.php?do=gallery&amp;sub=editgallery&amp;id={$item->Id}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/edit.png" alt="" border="0" /></a>
          <a class="colorbox stip" title="{$lang.Gallery_ShowImages|sanitize}" href="index.php?do=gallery&amp;sub=editimages&amp;id={$item->Id}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/image.png" alt="" border="0" /></a>
          <a class="colorbox stip" title="{$lang.Gallery_AddImages|sanitize}" href="index.php?do=gallery&amp;sub=addimages&amp;id={$item->Id}&amp;gid={$smarty.request.id}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/image_add.png" alt="" border="0" /></a>
          <a class="colorbox stip" title="{$lang.Gallery_addNewSub|sanitize}" href="index.php?do=gallery&amp;sub=addgallery&amp;id={$smarty.request.id}&amp;subg={$item->Id}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/folder_add.png" alt="" border="0" /></a>
          <a class="stip" title="{$lang.Gallery_viewGalsInc|sanitize}" href="../index.php?p=gallery&amp;action=showgallery&amp;id={$item->Id}&amp;categ={$item->Kategorie}&amp;name={$item->Name_1|translit}" target="_blank"><img class="absmiddle" src="{$imgpath}/view.png" alt="" border="0" /></a>
            {if perm('gallery_delete')}
            <a class="stip" title="{$lang.Global_Delete|sanitize}" onclick="return confirm('{#ConfirmGlobal#}{$item->Name_1|jsspecialchars}');" href="index.php?do=gallery&amp;sub=gallerydel&amp;id={$item->Id}&amp;backurl={$backurl}"><img class="absmiddle" src="{$imgpath}/delete.png" alt="" border="0" /></a>
            {/if}
        </td>
      </tr>
      {if $item->subGalleries}
        {foreach from=$item->subGalleries item=sg}
          <tr class="{cycle values='second,first'}">
            <td class="subgalleries">
              <input type="hidden" name="galleryid[{$sg->Id}]" value="{$sg->Id}" />
              {$sg->Expander}&nbsp;<a href="index.php?do=gallery&amp;sub=showincluded&amp;id={$smarty.request.id}&amp;subgallery={$sg->Id}">{$sg->LinkName}</a>
            </td>
            <td width="50" align="center" class="subgalleries"><a class="colorbox" href="index.php?do=user&amp;sub=edituser&amp;user={$sg->Autor}&amp;noframes=1">{$sg->User}</a></td>
            <td width="50" align="center" class="subgalleries">{$sg->Datum|date_format: '%d.%m.%y'}</td>
            <td width="100" align="center" class="subgalleries">
              <label><input type="radio" name="Aktiv[{$sg->Id}]" value="1" {if $sg->Aktiv == 1} checked="checked"{/if}/>{#Yes#}</label>
              <label><input type="radio" name="Aktiv[{$sg->Id}]" value="0" {if $sg->Aktiv == 0} checked="checked"{/if}/>{#No#}</label>
            </td>
            <td width="50" align="center" class="subgalleries">{if $sg->Bilder > 0}{$sg->Bilder}{else}-{/if}</td>
            <td>
              <a class="colorbox stip" title="{$lang.Edit|sanitize}" href="index.php?do=gallery&amp;sub=editgallery&amp;id={$sg->Id}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/edit.png" alt="" border="0" /></a>
              <a class="colorbox stip" title="{$lang.Gallery_ShowImages|sanitize}" href="index.php?do=gallery&amp;sub=editimages&amp;id={$sg->Id}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/image.png" alt="" border="0" /></a>
              <a class="colorbox stip" title="{$lang.Gallery_AddImages|sanitize}" href="index.php?do=gallery&amp;sub=addimages&amp;id={$sg->Id}&amp;gid={$smarty.request.id}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/image_add.png" alt="" border="0" /></a>
              <a class="colorbox stip" title="{$lang.Gallery_addNewSub|sanitize}" href="index.php?do=gallery&amp;sub=addgallery&amp;id={$smarty.request.id}&amp;subg={$sg->Id}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/folder_add.png" alt="" border="0" /></a>
              <a class="stip" title="{$lang.Gallery_viewGalsInc|sanitize}" href="../index.php?p=gallery&amp;action=showgallery&amp;id={$sg->Id}&amp;categ={$sg->Kategorie}&amp;name={$sg->Name_1|translit}" target="_blank"><img class="absmiddle" src="{$imgpath}/view.png" alt="" border="0" /></a>
                {if perm('gallery_delete')}
                <a class="stip" title="{$lang.Global_Delete|sanitize}" onclick="return confirm('{#ConfirmGlobal#}{$sg->Expander|jsspecialchars} {$sg->LinkName|jsspecialchars}');" href="index.php?do=gallery&amp;sub=gallerydel&amp;id={$sg->Id}&amp;backurl={$backurl}"><img class="absmiddle" src="{$imgpath}/delete.png" alt="" border="0" /></a>
                {/if}
            </td>
          </tr>
        {/foreach}
      {/if}
    {/foreach}
  </table>
  <input name="save" type="hidden" id="save" value="1" />
  <input name="Senden" type="submit" class="button" value="{#Save#}" />
</form>
<br />
{if !empty($Navi)}
  <div class="navi_div"> {$Navi} </div>
{/if}
