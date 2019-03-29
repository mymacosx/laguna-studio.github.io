<div class="header">{#InsertContent#}</div>
<div class="subheaders">
  {if $admin_settings.Ahelp == 1}
    <a class="colorbox" href="index.php?do=help&amp;sub={$helpquery}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/s_help.png" alt="" border="0" /> {#GlobalHelp#}</a>&nbsp;&nbsp;&nbsp;
    {/if}
  <a class="colorbox" href="index.php?do=support&amp;sub=send_order&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/send.png" alt="" border="0" /> {#SendOrder#}</a>
</div>
<div class="subheaders">
  <form method="post" action="index.php?do=insert&amp;sub=overview">
    <table width="100%" border="0" cellspacing="0" cellpadding="1">
      <tr>
        <td width="100"><label for="qs">{#Search#}</label></td>
        <td><input style="width: 200px" type="text" class="input" name="q" id="qs" value="{$smarty.request.q|sanitize|replace: 'empty': ''}" /></td>
      </tr>
      <tr>
        <td width="100"><label for="dr">{#DataRecords#}</label></td>
        <td>
          <input class="input" style="width: 50px" type="text" name="pp" id="dr" value="{$limit}" />
          <label></label>
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
      <td width="40" align="center" class="headers"><a href="index.php?do=insert&amp;sub=overview&amp;q={if isset($smarty.request.q)}{$smarty.request.q}{/if}&amp;page={$smarty.request.page|default:1}&amp;sort={$idsort|default:'id_desc'}&amp;pp={$limit}">ID</a></td>
      <td width="400" align="center" class="headers"><a href="index.php?do=insert&amp;sub=overview&amp;q={if isset($smarty.request.q)}{$smarty.request.q}{/if}&amp;page={$smarty.request.page|default:1}&amp;sort={$markersort|default:'marker_desc'}&amp;pp={$limit}">{#InsertMarker#}</a></td>
      <td width="180" align="center" class="headers"><a href="index.php?do=insert&amp;sub=overview&amp;q={if isset($smarty.request.q)}{$smarty.request.q}{/if}&amp;page={$smarty.request.page|default:1}&amp;sort={$namesort|default:'name_desc'}&amp;pp={$limit}">{#InsertKey#}</a></td>
      <td width="180" align="center" nowrap="nowrap" class="headers">{#InsertName#}</td>
      <td width="90" align="center" nowrap="nowrap" class="headers"><a href="index.php?do=insert&amp;sub=overview&amp;q={if isset($smarty.request.q)}{$smarty.request.q}{/if}&amp;page={$smarty.request.page|default:1}&amp;sort={$activesort|default:'active_asc'}&amp;pp={$limit}">{#Global_Active#}</a></td>
      <td width="1" align="center" class="headers"><img class="absmiddle" src="{$imgpath}/delete.png" alt="" border="0" /></td>
      <td class="headers">{#Global_Actions#}</td>
    </tr>
    {foreach from=$collections item=g}
      <tr class="{cycle values='second,first'}">
        <td width="40" align="center">{$g->Id}</td>
        <td width="400"><textarea class="input" cols="" rows="" style="width: 400px; height: 16px" onclick="focusArea(this, 60);" name="Marker[{$g->Id}]">{$g->Marker}</textarea></td>
        <td width="180"><input type="text" class="input" style="width: 180px" name="Name[{$g->Id}]" value="{$g->Name}" /></td>
        <td width="180"><input type="text" class="input" style="width: 180px" value="{ldelim}$insert.{$g->Name}{rdelim}" readonly="readonly" /></td>
        <td width="90" align="center" nowrap="nowrap">
          <label><input type="radio" name="Active[{$g->Id}]" value="1" {if $g->Active == 1} checked="checked"{/if}/>{#Yes#}</label>
          <label><input type="radio" name="Active[{$g->Id}]" value="0" {if $g->Active == 0} checked="checked"{/if}/>{#No#}</label>
        </td>
        <td align="center"><input class="stip" title="{$lang.DelInfChekbox|sanitize}" name="del[{$g->Id}]" type="checkbox" value="1" /></td>
        <td>
          <a class="colorbox stip" title="{$lang.Edit|sanitize}" href="index.php?do=insert&amp;sub=edit&amp;id={$g->Id}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/edit.png" alt="" border="0" /></a>
          <a class="colorbox stip" title="{$lang.Edit|sanitize} + CKEditor" href="index.php?do=insert&amp;sub=edit&amp;id={$g->Id}&amp;html=1&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/edit.png" alt="" border="0" /></a>
        </td>
      </tr>
    {/foreach}
  </table>
  <input class="button" type="submit" value="{#Save#}" />
  <input name="save" type="hidden" id="save" value="1" />
</form>
<br />
{if !empty($Navi)}
  <div class="navi_div"> {$Navi} </div>
{/if}
<br />
