<script type="text/javascript">
<!-- //
$(document).ready(function() {
    $('#date_till').datepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat: 'dd.mm.yy',
        dayNamesMin: [{#Calendar_daysmin#}],
        monthNamesShort: [{#Calendar_monthNamesShort#}],
        firstDay: 1
    });
});

function checkcategmove() {
    if(document.getElementById('movecateg').selected == true) {
        document.getElementById('movecateg_select').style.display = '';
    } else {
        document.getElementById('movecateg_select').style.display = 'none';
    }
}
function checkmform() {
    if(document.getElementById('mdel').selected == true) {
	if(!confirm('{#Content_multiaction_deleteC#}')) {
            return false;
        }
    }
}
//-->
</script>

<div class="header">{#Products#}</div>
<div class="subheaders">
  {if $admin_settings.Ahelp == 1}
    <a class="colorbox" href="index.php?do=help&amp;sub={$helpquery}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/s_help.png" alt="" border="0" /> {#GlobalHelp#}</a>&nbsp;&nbsp;&nbsp;
    {/if}
  <a class="colorbox" href="index.php?do=support&amp;sub=send_order&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/send.png" alt="" border="0" /> {#SendOrder#}</a>
</div>
{if !$genres}
  <div class="info_red"> {#GlobalNoCateg#} </div>
{else}
  <div class="subheaders">
    <form method="post" action="index.php?do=products&amp;sub=overview">
      <table width="100%" border="0" cellpadding="2" cellspacing="0">
        <tr>
          <td width="100"><label for="gs">{#Search#}</label></td>
          <td width="150"><input style="width: 130px" class="input" type="text" name="q" id="gs" value="{if isset($smarty.request.q)}{$smarty.request.q}{/if}" /></td>
          <td width="80" align="right"><label for="laktiv">{#Global_Status#}</label></td>
          <td>
            <select style="width: 134px" class="input" name="aktiv" id="laktiv">
              <option value="">{#Global_All#}</option>
              <option value="1" {if $smarty.request.aktiv == 1}selected="selected"{/if}>{#Global_Opened#}</option>
              <option value="0" {if $smarty.request.aktiv == 0}selected="selected"{/if}>{#Global_Closed#}</option>
            </select>
          </td>
        </tr>
        <tr>
          <td><label for="lgenre">{#Global_Categ#}</label></td>
          <td>
            <select  style="width: 133px" class="input" name="genre" id="lgenre">
              <option value="0">{#Global_All#}</option>
              {foreach from=$genres item=g}
                <option value="{$n->Id}" {if $smarty.request.genre == $g->Id}selected="selected"{/if}>{$g->Name|sanitize}</option>
              {/foreach}
            </select>
          </td>
          <td align="right"><label for="listop">{#GlobalTops#}</label></td>
          <td>
            <select  style="width: 133px" class="input" name="istop" id="listop">
              <option value="">{#Global_All#}</option>
              <option value="1" {if $smarty.request.istop == 1}selected="selected"{/if}>{#Yes#} </option>
              <option value="0" {if $smarty.request.istop == 0}selected="selected"{/if}>{#No#} </option>
            </select>
          </td>
        </tr>
        <tr>
          <td><label for="date_till">{#Global_DateTill#}</label></td>
          <td><input class="input" style="width: 130px" type="text" name="date_till" id="date_till" readonly="readonly" value="{$smarty.request.date_till|sanitize}" /></td>
          <td align="right"><label for="dr">{#DataRecords#}</label></td>
          <td>
            <input type="text" id="dr" class="input" name="pp" style="width: 45px" value="{$limit}" />
            <input class="button" type="submit" value="{#Global_search_b#}" />
            <input name="startsearch" type="hidden" id="startsearch" value="1" />
          </td>
        </tr>
      </table>
    </form>
  </div>
  {if $products}
    <form method="post" action="" name="kform">
      <table width="100%" border="0" cellspacing="0" cellpadding="3">
        <tr class="{cycle values='first,second'}">
          <td width="150" class="headers"><a href="{$ordstr}{$name_s|default:'&amp;sort=name_asc'}">{#Global_Name#}</a></td>
          <td width="80" align="center" class="headers"><a href="{$ordstr}{$username_s|default:'&amp;sort=username_desc'}">{#Global_Author#}</a></td>
          <td width="50" align="center" class="headers"><a href="{$ordstr}{$date_s|default:'&amp;sort=date_desc'}">{#Global_Created#}</a></td>
          <td width="50" align="center" class="headers"><a href="{$ordstr}{$hits_s|default:'&amp;sort=hits_desc'}">{#Global_Hits#}</a></td>
          <td width="50" align="center" class="headers"><a href="{$ordstr}{$genre_s|default:'&amp;sort=genre_desc'}">{#Global_Categ#}</a></td>
            {if perm('products_openclose')}
            <td width="50" align="center" class="headers"><a href="{$ordstr}{$active_s|default:'&amp;sort=active_desc'}">{#Global_Active#}</a></td>
            {/if}
          <td width="50" align="center" class="headers"><a href="{$ordstr}{$top_s|default:'&amp;sort=top_desc'}">{#GlobalTops#}</a></td>
            {if perm('products_del')}
            <td width="10" align="center" class="headers"><img class="absmiddle" src="{$imgpath}/delete.png" alt="" border="0" /></td>
            {/if}
          <td class="headers">{#Global_Actions#}</td>
        </tr>
        {foreach from=$products item=n}
          <tr class="{cycle values='second,first'}">
            <td class="row_spacer">
              {if $n->Aktiv == 0}
                <span style="text-decoration: line-through; font-weight: bold">{$n->Name1}</span>
              {else}
                <a href="../index.php?p=products&amp;area={$area}&amp;action=showproduct&amp;id={$n->Id}&amp;name={$n->Name1|translit}" target="_blank"><strong>{$n->Name1|sanitize}</strong></a>
                  {/if}
              <input type="hidden" name="nid[{$n->Id}]" value="{$n->Id}" />
            </td>
            <td align="center" nowrap="nowrap" class="row_spacer"><a class="colorbox" href="index.php?do=user&amp;sub=edituser&user={$n->Benutzer}&amp;noframes=1">{$n->User}</a></td>
            <td align="center" nowrap="nowrap" class="row_spacer">{$n->Datum|date_format: "%d.%m.%y"}</td>
            <td align="center" class="row_spacer"><input class="input" style="width: 25px" type="text" name="Hits[{$n->Id}]" value="{$n->Hits}" /></td>
            <td align="center" class="row_spacer">
              <select  style="width: 120px" class="input" name="Genre[{$n->Id}]">
                {foreach from=$genres item=g}
                  <option value="{$g->Id}" {if $n->Genre == $g->Id}selected="selected"{/if}>{$g->Name|sanitize}</option>
                {/foreach}
              </select>
            </td>
            {if perm('products_openclose')}
              <td align="center" class="row_spacer">
                <select  style="width: 50px" class="input" name="Aktiv[{$n->Id}]">
                  <option value="1" {if $n->Aktiv == 1}selected="selected"{/if}>{#Yes#}</option>
                  <option value="0" {if $n->Aktiv == 0}selected="selected"{/if}>{#No#}</option>
                </select>
              </td>
            {/if}
            {if perm('products_openclose')}
              <td align="center" class="row_spacer">
                <select class="input stip" title="{$lang.Products_topsInf|sanitize}" style="width: 50px" name="TopProduct[{$n->Id}]">
                  <option value="1" {if $n->TopProduct == 1}selected="selected"{/if}>{#Yes#} </option>
                  <option value="0" {if $n->TopProduct == 0}selected="selected"{/if}>{#No#} </option>
                </select>
              </td>
              <td align="center" class="row_spacer"><input class="stip" title="{$lang.DelInfChekbox|sanitize}" name="del[{$n->Id}]" type="checkbox" value="1" /></td>
              {/if}
            <td class="row_spacer" nowrap="nowrap">
              {if perm('products_newedit')}
                <a class="colorbox stip" title="{$lang.Products_edit|sanitize}" href="index.php?do=products&amp;sub=edit&amp;id={$n->Id}&amp;noframes=1&amp;langcode=1"><img class="absmiddle" src="{$imgpath}/edit.png" alt="" border="0" /></a>
                <a class="stip" title="{$lang.Copy|sanitize}" href="index.php?do=products&amp;sub=copy&amp;id={$n->Id}&amp;langcode=1&amp;backurl={$backurl}"><img class="absmiddle" src="{$imgpath}/copy.png" alt="" border="0" /></a>
                {/if}
                {if perm('comments') && admin_active('comments')}
                  {if $n->Comments>=1}
                  <a class="colorbox stip" title="{$lang.Global_Comments|sanitize}" href="index.php?do=comments&amp;where=products&amp;object={$n->Id}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/message.png" alt="" border="0" /></a>
                  {else}
                  <img class="absmiddle stip" title="{$lang.Global_Comments|sanitize}" src="{$imgpath}/message_no.png" alt="" border="0" />
                {/if}
              {/if}
              {if $n->Wertung>=1 && perm('del_rating')}
                <a class="stip" title="{$lang.DelRating|sanitize}" href="index.php?do=products&amp;sub=delrating&amp;id={$n->Id}&amp;backurl={$backurl}"><img class="absmiddle" src="{$imgpath}/votes_del.png" alt="" border="0" /></a>
                {else}
                <img class="absmiddle stip" title="{$lang.DelRating|sanitize}" src="{$imgpath}/votes_del_no.png" alt="" border="0" />
              {/if}
            </td>
          </tr>
        {/foreach}
      </table>
      <input type="hidden" name="quicksave" value="1" />
      <input class="button" type="submit" value="{#Save#}" />
    </form>
  {else}
    <h3>{#Products_none#}</h3>
  {/if}
  <br />
  {if !empty($Navi)}
    <div class="navi_div"> {$Navi} </div>
  {/if}
{/if}