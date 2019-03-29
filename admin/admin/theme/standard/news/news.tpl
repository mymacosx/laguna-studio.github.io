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
	if(!confirm('{#News_multiaction_deleteC#}')) {
          return false;
        }
    }
}
//-->
</script>

<div class="header">{#News#}</div>
<div class="subheaders">
  {if $admin_settings.Ahelp == 1}
    <a class="colorbox" href="index.php?do=help&amp;sub={$helpquery}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/s_help.png" alt="" border="0" /> {#GlobalHelp#}</a>&nbsp;&nbsp;&nbsp;
    {/if}
  <a class="colorbox" href="index.php?do=support&amp;sub=send_order&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/send.png" alt="" border="0" /> {#SendOrder#}</a>
</div>
{if !$newscategs}
  <div class="info_red"> {#GlobalNoCateg#} </div>
{else}
  <div class="subheaders">
    <form onsubmit="return checkmform();" method="post" action="index.php?do=news&sub=overview">
      <table width="100%" border="0" cellpadding="2" cellspacing="0">
        <tr>
          <td width="100">{#Search#}</td>
          <td width="150"><input style="width: 130px" class="input" type="text" name="q" value="{if isset($smarty.request.q)}{$smarty.request.q}{/if}" /></td>
          <td width="80" align="right">{#Global_Categ#} </td>
          <td>
            <select  style="width: 124px" class="input" name="categ">
              <option value="0">{#News_allC#}</option>
              {foreach from=$newscategs item=dd}
                <option value="{$dd->Id}" {if $smarty.request.categ == $dd->Id}selected="selected"{/if}>{$dd->visible_title} </option>
              {/foreach}
            </select>
          </td>
        </tr>
        <tr>
          <td>{#Global_Status#}</td>
          <td>
            <select  style="width: 134px" class="input" name="aktiv">
              <option value="">{#Global_All#}</option>
              <option value="1" {if $smarty.request.aktiv == 1}selected="selected"{/if}>{#Global_Opened#}</option>
              <option value="0" {if $smarty.request.aktiv == 0}selected="selected"{/if}>{#Global_Closed#}</option>
            </select></td>
          <td align="right">{#Global_Hits#}</td>
          <td>
            <input name="hits_from" type="text" class="input" style="width: 45px" value="{$smarty.request.hits_from|default:'0'}" maxlength="8" /> -
            <input name="hits_to" type="text" class="input" style="width: 45px" value="{$smarty.request.hits_to|default:'10000'}" maxlength="8" />
            <input name="startsearch" type="hidden" id="startsearch" value="1" />
          </td>
        </tr>
        <tr>
          <td>{#Global_DateTill#}</td>
          <td><input class="input" style="width: 130px" type="text" name="date_till" id="date_till" readonly="readonly" value="{$smarty.request.date_till|sanitize}" /></td>
          <td align="right">{#DataRecords#}</td>
          <td>
            <input type="text" class="input" name="pp" style="width: 45px" value="{$limit}" />
            <input class="button" type="submit" onclick="document.getElementById('multi').value='';
                document.getElementById('del_innerhtml').style.display = 'none';" value="{#Global_search_b#}" />
          </td>
        </tr>
      </table>
      {if $smarty.session.NewsSearch && perm('news_multioptions') && $news}
        <div id="del_innerhtml">
          <fieldset>
            <legend><label for="mf">{#News_multiaction#}</label></legend>
              {if $multi_done == 1}
              <h3>{#GlobalOk#}</h3>
              <br />
            {/if}
            <select onchange="checkcategmove();" class="input" style="width: 250px" name="multiaction">
              <option value="open">{#News_multiaction_open#}</option>
              <option value="close">{#News_multiaction_close#}</option>
              <option id="movecateg" value="movecateg">{#Content_MultiAction_MoveNewCateg#}</option>
              <option id="mdel" value="delete">{#News_multiaction_delete#}</option>
            </select>
            <input type="hidden" name="multi" id="multi" value="1" />
            <span id="movecateg_select" style="display: none">
              <select style="width: 124px" class="input" name="newcateg">
                {foreach from=$newscategs item=dd}
                  <option value="{$dd->Id}">{$dd->visible_title} </option>
                {/foreach}
              </select>
            </span>
            <input class="button" type="submit" onclick="document.getElementById('startsearch').value = '';" value="{#News_multiaction_button#}" />
          </fieldset>
        </div>
      {/if}
    </form>
  </div>
  {if $news}
    <form method="post" action="" name="kform">
      <table width="100%" border="0" cellspacing="0" cellpadding="3">
        <tr class="{cycle values='first,second'}">
          <td width="150" class="headers"><a href="{$ordstr}{$name_s|default:'&amp;sort=name_asc'}">{#Global_Name#}</a></td>
          <td width="80" align="center" class="headers"><a href="{$ordstr}{$username_s|default:'&amp;sort=username_desc'}">{#Global_Author#}</a></td>
          <td width="50" align="center" class="headers"><a href="{$ordstr}{$date_s|default:'&amp;sort=date_desc'}">{#Global_Created#}</a></td>
          <td width="50" align="center" class="headers"><a href="{$ordstr}{$start_s|default:'&amp;sort=start_desc'}">{#Global_Published#}</a></td>
          <td width="50" align="center" class="headers"><a href="{$ordstr}{$end_s|default:'&amp;sort=end_desc'}">{#Global_PubEnd#}</a></td>
          <td width="50" align="center" class="headers"><a href="{$ordstr}{$hits_s|default:'&amp;sort=hits_desc'}">{#Global_Hits#}</a></td>
          <td width="50" class="headers">{#Global_Categ#}</td>
          <td width="50" class="headers">{#GlobalTops#}</td>
          <td class="headers">{#Global_Actions#}</td>
        </tr>
        {foreach from=$news item=n}
          <tr class="{cycle values='second,first'}">
            <td class="row_spacer">
              {if $n->Aktiv == 0}
                <span style="text-decoration: line-through; font-weight: bold">{$n->Titel1}</span>
              {else}
                <a class="stip" title="{$n->Titel1|sanitize}" href="../index.php?p=news&amp;newsid={$n->Id}" target="_blank"><strong>{$n->Titel1|slice: 30: '...'|sanitize}</strong></a>
                  {/if}
              <input type="hidden" name="nid[{$n->Id}]" value="{$n->Id}" />
            </td>
            <td align="center" nowrap="nowrap" class="row_spacer"><a class="colorbox stip" title="{$lang.User_edit|sanitize}" href="index.php?do=user&amp;sub=edituser&amp;user={$n->Autor}&amp;noframes=1">{$n->User}</a></td>
            <td align="center" nowrap="nowrap" class="row_spacer">{$n->Zeit|date_format: "%d.%m.%y"}</td>
            <td align="center" nowrap="nowrap" class="row_spacer">{$n->ZeitStart|date_format: "%d.%m.%y"}</td>
            <td align="center" nowrap="nowrap" class="row_spacer">{if $n->ZeitEnde>0}{$n->ZeitEnde|date_format: "%d.%m.%y"}{else}-{/if}</td>
            <td align="center" class="row_spacer">{$n->Hits}</td>
            <td class="row_spacer" nowrap="nowrap">
              <select  style="width: 150px" class="input" name="Kategorie[{$n->Id}]">
                {foreach from=$newscategs item=dd}
                  <option value="{$dd->Id}" {if $n->Kategorie == $dd->Id}selected="selected"{/if}>{$dd->visible_title} </option>
                {/foreach}
              </select>
            </td>
            <td class="row_spacer">
              <select class="input stip" title="{$lang.News_TopnewsInf|sanitize}" style="width: 50px" name="Topnews[{$n->Id}]">
                <option value="1" {if $n->Topnews == 1}selected="selected"{/if}>{#Yes#} </option>
                <option value="0" {if $n->Topnews == 0}selected="selected"{/if}>{#No#} </option>
              </select>
            </td>
            <td class="row_spacer" nowrap="nowrap">
              {if perm('news_edit')}
                {if $n->Autor!=$smarty.session.benutzer_id && (!perm('news_edit_all'))}
                  <img src="{$imgpath}/edit_no.png" alt="" border="0" />
                {else}
                  <a class="colorbox stip" title="{$lang.Edit|sanitize}" href="index.php?do=news&amp;sub=editnews&amp;id={$n->Id}&amp;noframes=1&amp;langcode=1"><img class="absmiddle" src="{$imgpath}/edit.png" alt="" border="0" /></a>
                  {/if}
                {/if}
                {if perm('news_openclose')}
                <a class="stip" title={if $n->Aktiv != 1}"{$lang.News_multiaction_open|sanitize}"{else}"{$lang.News_multiaction_close|sanitize}"{/if} href="index.php?do=news&amp;sub=openclose&amp;openclose={if $n->Aktiv != 1}1{else}0{/if}&amp;id={$n->Id}&amp;backurl={$backurl}"><img class="absmiddle" src="{$imgpath}/{if $n->Aktiv != 1}closed{else}opened{/if}.png" alt="" border="0" /></a>
                {/if}
                {if perm('comments') && admin_active('comments')}
                  {if $n->Comments>=1}
                  <a class="colorbox stip" title="{$lang.Global_Comments|sanitize}" href="index.php?do=comments&amp;where=news&amp;object={$n->Id}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/message.png" alt="" border="0" /></a>
                  {else}
                  <img class="absmiddle stip" title="{$lang.Global_Comments|sanitize}" src="{$imgpath}/message_no.png" alt="" border="0" />
                {/if}
              {/if}
              {if $n->Wertung>=1 && perm('del_rating')}
                <a class="stip" title="{$lang.DelRating|sanitize}" href="index.php?do=news&amp;sub=delrating&amp;id={$n->Id}&amp;backurl={$backurl}"><img class="absmiddle" src="{$imgpath}/votes_del.png" alt="" border="0" /></a>
                {else}
                <img class="absmiddle stip" title="{$lang.DelRating|sanitize}" src="{$imgpath}/votes_del_no.png" alt="" border="0" />
              {/if}
              {if perm('news_delete')}
                {if $n->Autor!=$smarty.session.benutzer_id && (!perm('news_delete_all'))}
                {else}
                  <a class="stip" title="{$lang.News_multiaction_delete|sanitize}" onclick="return confirm('{#ConfirmGlobal#}{$n->Titel1|jsspecialchars}');" href="index.php?do=news&amp;sub=delete&amp;id={$n->Id}&amp;backurl={$backurl}"><img class="absmiddle" src="{$imgpath}/delete.png" alt="" border="0" /></a>
                  {/if}
                {/if}
            </td>
          </tr>
        {/foreach}
      </table>
      <input type="hidden" name="quicksave" value="1" />
      <input class="button" type="submit" value="{#Save#}" />
    </form>
  {else}
    <h3>{#News_empty#}</h3>
  {/if}
  <br />
  {if !empty($Navi)}
    <div class="navi_div"> {$Navi} </div>
  {/if}
{/if}
