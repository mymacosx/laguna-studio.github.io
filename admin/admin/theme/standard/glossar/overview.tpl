<div class="header">{#Glossar#}</div>
<div class="subheaders">
  <a class="colorbox stip" title="{$lang.Glossar_new|sanitize}" href="index.php?do=glossar&amp;sub=add&amp;type=0&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/add.png" alt="" border="0" /> {#Glossar_new#}</a>&nbsp;&nbsp;&nbsp;
  <a class="colorbox_small stip" title="{$lang.Glossar_new|sanitize}" href="index.php?do=glossar&amp;sub=add&amp;type=1&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/add.png" alt="" border="0" /> {#Glossar_link#}</a>&nbsp;&nbsp;&nbsp;
  <a class="colorbox_small stip" title="{$lang.Links_add|sanitize}" href="index.php?do=glossar&amp;sub=add&amp;type=2&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/add.png" alt="" border="0" /> {#Links_add#}</a>&nbsp;&nbsp;&nbsp;
    {if $admin_settings.Ahelp == 1}
    <a class="colorbox" href="index.php?do=help&amp;sub={$helpquery}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/s_help.png" alt="" border="0" /> {#GlobalHelp#}</a>&nbsp;&nbsp;&nbsp;
    {/if}
  <a class="colorbox" href="index.php?do=support&amp;sub=send_order&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/send.png" alt="" border="0" /> {#SendOrder#}</a>
</div>
<div class="subheaders">
  <form method="post" action="index.php?do=glossar">
    <table width="100%" border="0" cellspacing="0" cellpadding="1">
      <tr>
        <td width="100"><label for="qs">{#Search#}&nbsp;</label></td>
        <td><input style="width: 200px" type="text" class="input" name="q" id="qs" value="{$smarty.request.q|sanitize|replace: 'empty': ''}" /></td>
      </tr>
      <tr>
        <td><label for="dr">{#DataRecords#}&nbsp;</label></td>
        <td>
          <input class="input" style="width: 50px" type="text" name="pp" id="dr" value="{$limit}" />&nbsp;
          <label></label>
          <input type="submit" class="button" value="{#Search#}" />
        </td>
      </tr>
    </table>
  </form>
</div>
<form method="post" action="" name="kform">
  <table width="100%" border="0" cellspacing="0" cellpadding="3">
    <tr class="headers">
      <td width="100" class="headers"><a href="index.php?do=glossar{if !empty($smarty.request.q)}&amp;q={$smarty.request.q|urlencode}{/if}&amp;page={$smarty.request.page|default:1}&amp;sort={$namesort|default:'name_desc'}&amp;pp={$limit}">{#Global_Name#}</a></td>
      <td width="80" align="center" class="headers"><a href="index.php?do=glossar{if !empty($smarty.request.q)}&amp;q={$smarty.request.q|urlencode}{/if}&amp;page={$smarty.request.page|default:1}&amp;sort={$hitssort|default:'hits_desc'}&amp;pp={$limit}">{#Global_Hits#}</a></td>
      <td width="200" align="center" class="headers"><a href="index.php?do=glossar{if !empty($smarty.request.q)}&amp;q={$smarty.request.q|urlencode}{/if}&amp;page={$smarty.request.page|default:1}&amp;sort={$typesort|default:'type_desc'}&amp;pp={$limit}">{#Global_Type#}</a></td>
      <td width="100" align="center" class="headers"><a href="index.php?do=glossar{if !empty($smarty.request.q)}&amp;q={$smarty.request.q|urlencode}{/if}&amp;page={$smarty.request.page|default:1}&amp;sort={$activesort|default:'active_desc'}&amp;pp={$limit}">{#Global_Active#}</a></td>
      <td width="10" class="headers"><img class="absmiddle" src="{$imgpath}/delete.png" alt="" border="0" /></td>
      <td class="headers">{#Global_Actions#}</td>
    </tr>
    {foreach from=$items item=g}
      <tr class="{cycle values='second,first'}">
        <td><input type="text" name="Wort[{$g->Id}]" class="input" style="width: 200px" value="{$g->Wort|sanitize}" /></td>
        <td align="center">{$g->Hits}</td>
        <td align="center">
          {if $g->Typ == 0}
            {#Glossar#}
          {else}
            <label><input type="radio" name="Typ[{$g->Id}]" value="1" {if $g->Typ == 1} checked="checked"{/if}/>{#Hiden_Link#}</label>
            <label><input type="radio" name="Typ[{$g->Id}]" value="2" {if $g->Typ == 2} checked="checked"{/if}/>{#Links_Link#}</label>
            {/if}
        </td>
        <td align="center">
          <label><input type="radio" name="Aktiv[{$g->Id}]" value="1" {if $g->Aktiv == 1} checked="checked"{/if}/>{#Yes#}</label>
          <label><input type="radio" name="Aktiv[{$g->Id}]" value="0" {if $g->Aktiv == 0} checked="checked"{/if}/>{#No#}</label>
        </td>
        <td align="center"><input class="stip" title="{$lang.DelInfChekbox|sanitize}" name="del[{$g->Id}]" type="checkbox" value="1" /></td>
        <td><a class="{if $g->Typ != 0}colorbox_small{else}colorbox{/if} stip" title="{$lang.Edit|sanitize}" href="index.php?do=glossar&amp;sub=edit&amp;id={$g->Id}&amp;type={$g->Typ}&amp;noframes=1&amp;langcode=1"><img class="absmiddle" src="{$imgpath}/edit.png" alt="" border="0" /></a></td>
      </tr>
    {/foreach}
  </table>
  <br />
  {if !empty($Navi)}
    <div class="navi_div"> {$Navi} </div>
  {/if}
  <input name="save" type="hidden" id="save" value="1" />
  <input type="submit" class="button" value="{#Save#}" />
</form>
