<div class="header">{#Gaming_cheats#}</div>
<div class="subheaders">
  {if $admin_settings.Ahelp == 1}
    <a class="colorbox" href="index.php?do=help&amp;sub={$helpquery}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/s_help.png" alt="" border="0" /> {#GlobalHelp#}</a>&nbsp;&nbsp;&nbsp;
    {/if}
  <a class="colorbox" href="index.php?do=support&amp;sub=send_order&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/send.png" alt="" border="0" /> {#SendOrder#}</a>
</div>
{if !$Categs}
  <div class="info_red"> {#GlobalNoCateg#} </div>
{else}
  <div class="subheaders">
    <form method="post" action="index.php?do=cheats&amp;sub=show">
      <table width="100%" border="0" cellspacing="0" cellpadding="1">
        <tr>
          <td width="100"><label for="qs">{#Search#}</label></td>
          <td width="100"><input style="width: 200px" type="text" class="input" name="q" id="qs" value="{$smarty.request.q|sanitize|replace: 'empty': ''}" /></td>
          <td width="10" align="left">&nbsp;</td>
          <td width="50" align="left" nowrap="nowrap">{#Gaming_plattform#}</td>
          <td>
            <select class="input" name="categ">
              <option value="">{#Global_All#}</option>
              {foreach from=$Categs item=c}
                {if $c->Parent_Id == 0}
                  <option style="font-weight: bold" value="{$c->Id}" {if $c->Id == $smarty.request.categ}selected="selected"{/if}>{$c->Name}</option>
                {else}
                  <option value="{$c->Id}" {if $c->Id == $smarty.request.categ}selected="selected"{/if}>{$c->visible_title}</option>
                {/if}
              {/foreach}
            </select>
          </td>
        </tr>
        <tr>
          <td><label for="dr">{#DataRecords#}</label></td>
          <td>
            <input class="input" style="width: 50px" type="text" name="pp" id="dr" value="{$limit}" />
            <label><input type="submit" class="button" value="{#Search#}" /></label>
          </td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
      </table>
      <label for="dr"></label>
    </form>
  </div>
  <form method="post" action="">
    <table width="100%" border="0" cellspacing="0" cellpadding="3">
      <tr class="headers">
        <td width="220" class="headers"><a href="index.php?do=cheats&amp;sub=show&amp;q={if isset($smarty.request.q)}{$smarty.request.q}{/if}&amp;page={$smarty.request.page|default:1}&amp;sort={$namesort|default:'name_desc'}&amp;broken={$smarty.request.broken}&amp;pp={$limit}">{#Global_Name#}</a></td>
        <td width="50" align="center" nowrap="nowrap" class="headers"><a href="index.php?do=cheats&amp;sub=show&amp;q={if isset($smarty.request.q)}{$smarty.request.q}{/if}&amp;page={$smarty.request.page|default:1}&amp;sort={$datesort|default:'date_desc'}&amp;broken={$smarty.request.broken}&amp;pp={$limit}">{#Global_Date#}</a></td>
        <td width="50" align="center" class="headers"><a href="index.php?do=cheats&amp;sub=show&amp;q={if isset($smarty.request.q)}{$smarty.request.q}{/if}&amp;page={$smarty.request.page|default:1}&amp;sort={$usersort|default:'user_desc'}&amp;broken={$smarty.request.broken}&amp;pp={$limit}">{#Global_Author#}</a></td>
        <td width="50" align="center" class="headers"><a href="index.php?do=cheats&amp;sub=show&amp;q={if isset($smarty.request.q)}{$smarty.request.q}{/if}&amp;page={$smarty.request.page|default:1}&amp;sort={$hitssort|default:'hits_desc'}&amp;broken={$smarty.request.broken}&amp;pp={$limit}">{#Global_Hits#}</a></td>
        <td width="80" align="center" class="headers"><a href="index.php?do=cheats&amp;sub=show&amp;q={if isset($smarty.request.q)}{$smarty.request.q}{/if}&amp;page={$smarty.request.page|default:1}&amp;sort={$categsort|default:'categ_desc'}&amp;broken={$smarty.request.broken}&amp;pp={$limit}">{#Gaming_plattform#}</a></td>
        <td width="50" align="center" class="headers"><a href="index.php?do=cheats&amp;sub=show&amp;q={if isset($smarty.request.q)}{$smarty.request.q}{/if}&amp;page={$smarty.request.page|default:1}&amp;sort={$activesort|default:'active_desc'}&amp;broken={$smarty.request.broken}&amp;pp={$limit}">{#Global_Active#}</a></td>
        <td width="1" align="center" class="headers"><img class="absmiddle" src="{$imgpath}/delete.png" alt="" border="0" /></td>
        <td width="100" class="headers">{#Global_Actions#}</td>
      </tr>
      {foreach from=$Entries item=g}
        <tr class="{cycle values='second,first'}">
          <td><a class="stip" title="{$g->Name_1|sanitize}" href="../index.php?p=cheats&amp;area={$area}&amp;action=showcheat&amp;id={$g->Id}" target="_blank"><strong>{$g->Name_1|slice: 30: '...'|sanitize}</strong></a></td>
          <td align="center" nowrap="nowrap">{$g->DatumUpdate|date_format: '%d.%m.%Y'}</td>
          <td align="center" nowrap="nowrap"><a class="colorbox" href="index.php?do=user&amp;sub=edituser&amp;user={$g->Benutzer}&amp;noframes=1">{$g->User}</a></td>
          <td align="center"><input class="input" style="width: 30px" name="Hits[{$g->Id}]" value="{$g->Hits}" /></td>
          <td align="center">
            <select class="input" style="width: 75px" name="Plattform[{$g->Id}]">
              {foreach from=$Categs item=c}{if $c->Parent_Id == 0}
                  <option style="font-weight: bold" value="{$c->Id}" {if $c->Id == $g->Plattform}selected="selected"{/if}>{$c->Name}</option>
                {else}
                  <option value="{$c->Id}" {if $c->Id == $g->Kategorie}selected="selected"{/if}>{$c->visible_title}</option>
                {/if}
              {/foreach}
            </select>
          </td>
          <td align="center">
            <select class="input" name="Aktiv[{$g->Id}]">
              <option value="1" {if $g->Aktiv == 1}selected="selected"{/if}>{#Yes#}</option>
              <option value="0" {if $g->Aktiv == 0}selected="selected"{/if}>{#No#}</option>
            </select>
          </td>
          <td align="center"><input class="stip" title="{$lang.DelInfChekbox|sanitize}" name="del[{$g->Id}]" type="checkbox" value="1" /></td>
          <td width="100">
            <a class="colorbox stip" title="{$lang.Edit|sanitize}" href="index.php?do=cheats&amp;sub=edit&amp;id={$g->Id}&amp;noframes=1&amp;langcode=1"><img class="absmiddle" src="{$imgpath}/edit.png" alt="" border="0" /></a>
              {if $g->DefektGemeldet}
              <img class="absmiddle stip" title="{$lang.Download_brokenInf|sanitize}" src="{$imgpath}/warning.gif" alt="" border="0" />
            {else}
              <img class="absmiddle stip" title="{$lang.Download_nbrokenInf|sanitize}" src="{$imgpath}/warning_no.png" alt="" border="0" />
            {/if}
            <a class="stip" title="{$lang.Copy|sanitize}" href="index.php?do=cheats&amp;sub=copy&amp;id={$g->Id}&amp;backurl={$backurl}"><img class="absmiddle" src="{$imgpath}/copy.png" alt="" border="0" /></a>
              {if perm('comments') && admin_active('comments')}
                {if $g->Comments>=1}
                <a class="colorbox stip" title="{$lang.Global_Comments|sanitize}" href="index.php?do=comments&amp;where=cheats&amp;object={$g->Id}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/message.png" alt="" border="0" /></a>
                {else}
                <img class="absmiddle stip" title="{$lang.Global_Comments|sanitize}" src="{$imgpath}/message_no.png" alt="" border="0" />
              {/if}
            {/if}
            {if $g->Wertung>=1 && perm('del_rating')}
              <a class="stip" title="{$lang.DelRating|sanitize}" href="index.php?do=cheats&amp;sub=delrating&amp;id={$g->Id}&amp;backurl={$backurl}"><img class="absmiddle" src="{$imgpath}/votes_del.png" alt="" border="0" /></a>
              {else}
              <img class="absmiddle stip" title="{$lang.DelRating|sanitize}" src="{$imgpath}/votes_del_no.png" alt="" border="0" />
            {/if}
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
{/if}
