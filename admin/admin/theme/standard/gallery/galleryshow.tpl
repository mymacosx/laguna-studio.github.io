<div class="header">{#Gallery#} - {#Gallery_categoverview#}</div>
<div class="subheaders">
  <a title="{#GlobalAddCateg#}" class="colorbox" href="index.php?do=gallery&amp;sub=addcategory&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/folder_add.png" alt="" border="0" /> {#GlobalAddCateg#}</a>&nbsp;&nbsp;&nbsp;
    {if $admin_settings.Ahelp == 1}
    <a class="colorbox" href="index.php?do=help&amp;sub={$helpquery}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/s_help.png" alt="" border="0" /> {#GlobalHelp#}</a>&nbsp;&nbsp;&nbsp;
    {/if}
  <a class="colorbox" href="index.php?do=support&amp;sub=send_order&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/send.png" alt="" border="0" /> {#SendOrder#}</a>
</div>
<div class="subheaders">
  <form method="post" action="index.php?do=gallery">
    <table width="100%" border="0" cellspacing="0" cellpadding="1">
      <tr>
        <td width="100"><label for="qs">{#Search#}</label></td>
        <td><input style="width: 200px" type="text" class="input" name="q" id="qs" value="{$smarty.request.q|sanitize|replace: 'empty': ''}" /></td>
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
</div>
<form method="post" action="">
  <table width="100%" border="0" cellspacing="0" cellpadding="3">
    <tr class="headers">
      <td width="30" class="headers">&nbsp;</td>
      <td class="headers"><a href="index.php?do=gallery&amp;q={if isset($smarty.request.q)}{$smarty.request.q}{/if}&amp;page={$smarty.request.page|default:1}&amp;sort={$namesort|default:'name_desc'}&amp;pp={$limit}">{#Global_Name#}</a></td>
      <td width="100" align="center" class="headers"><a href="index.php?do=gallery&amp;q={if isset($smarty.request.q)}{$smarty.request.q}{/if}&amp;page={$smarty.request.page|default:1}&amp;sort={$usersort|default:'user_desc'}&amp;subgallery={$smarty.request.subgallery|default:0}&amp;pp={$limit}">{#Global_Author#}</a></td>
      <td width="100" align="center" class="headers"><a href="index.php?do=gallery&amp;q={if isset($smarty.request.q)}{$smarty.request.q}{/if}&amp;page={$smarty.request.page|default:1}&amp;sort={$datesort|default:'date_desc'}&amp;subgallery={$smarty.request.subgallery|default:0}&amp;pp={$limit}">{#Global_Date#}</a></td>
      <td width="100" align="center" class="headers"><a href="index.php?do=gallery&amp;q={if isset($smarty.request.q)}{$smarty.request.q}{/if}&amp;page={$smarty.request.page|default:1}&amp;sort={$activesort|default:'active_desc'}&amp;subgallery={$smarty.request.subgallery|default:0}&amp;pp={$limit}">{#Global_Active#}</a></td>
      <td width="80" align="center" class="headers">{#Global_Actions#}</td>
    </tr>
    {foreach from=$galleries item=g}
      <tr class="{cycle values='second,first'}">
        <td width="30">
          <input type="hidden" name="Categs[{$g->Id}]" value="{$g->Id}" />
          {if $g->Bild}
            <a class="stip" title="{$lang.Gallery_viewInCGals|sanitize}" href="index.php?do=gallery&amp;sub=showincluded&amp;id={$g->Id}"><img src="../uploads/galerie_icons/{$g->Bild}" alt="" width="30" height="23" border="0" align="left" class="gallery_categs_img" /></a>
            {/if}
        </td>
        <td><strong>{$g->Name|sanitize}</strong></td>
        <td width="100" align="center"><a class="colorbox" href="index.php?do=user&amp;sub=edituser&amp;user={$g->Autor}&amp;noframes=1">{$g->User}</a></td>
        <td width="100" align="center">{$g->Datum|date_format: '%d.%m.%Y'}</td>
        <td width="100" align="center">
          <label><input type="radio" name="Aktiv[{$g->Id}]" value="1" {if $g->Aktiv == 1} checked="checked"{/if}/>{#Yes#}</label>
          <label><input type="radio" name="Aktiv[{$g->Id}]" value="0" {if $g->Aktiv == 0} checked="checked"{/if}/>{#No#}</label>
        </td>
        <td width="80">
          <a class="stip" title="{$lang.Gallery_viewInCGals|sanitize}" href="index.php?do=gallery&amp;sub=showincluded&amp;id={$g->Id}"><img class="absmiddle" src="{$imgpath}/folder.png" alt="" border="0" /></a>
          <a class="colorbox stip" title="{$lang.Gallery_editCateg|sanitize}" href="index.php?do=gallery&amp;sub=editcateg&amp;id={$g->Id}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/edit.png" alt="" border="0" /></a>
          <a class="stip" title="{$lang.Gallery_viewGalsInc|sanitize}" href="../index.php?p=gallery&amp;action=showincluded&amp;categ={$g->Id}&amp;name={$g->Name|translit}" target="_blank"><img class="absmiddle" src="{$imgpath}/view.png" alt="" border="0" /></a>
            {if perm('gallery_delete')}
            <a class="stip" title="{$lang.Global_Delete|sanitize}" onclick="return confirm('{#ConfirmGlobal#}{$g->Name|jsspecialchars}');" href="index.php?do=gallery&amp;sub=delcategory&amp;id={$g->Id}&amp;backurl={$backurl}"><img class="absmiddle" src="{$imgpath}/delete.png" alt="" border="0" /></a>
            {/if}
        </td>
      </tr>
    {/foreach}
  </table>
  <input type="submit" class="button" value="{#Save#}" />
  <input name="save" type="hidden" id="save" value="1" />
</form>
{if $GalNavi}
  <br />
  <div class="navi_div"> {$GalNavi} </div>
{/if}
