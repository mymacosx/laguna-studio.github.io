<div class="header">{#Sitemap#} - {#Global_Settings#}</div>
<div class="subheaders">
  {if $admin_settings.Ahelp == 1}
    <a class="colorbox" href="index.php?do=help&amp;sub={$helpquery}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/s_help.png" alt="" border="0" /> {#GlobalHelp#}</a>&nbsp;&nbsp;&nbsp;
    {/if}
  <a class="colorbox" href="index.php?do=support&amp;sub=send_order&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/send.png" alt="" border="0" /> {#SendOrder#}</a>
</div>
<form method="post" action="index.php?do=seo&amp;sub=sitemap_save">
  <table width="100%" border="0" cellspacing="4" cellpadding="0">
    <tr>
      <td align="center" valign="middle"><img class="absmiddle stip" title="{$lang.AreaInf|sanitize}" src="{$imgpath}/help.png" alt="" /> <strong>{#Sections#}</strong></td>
      <td align="center" valign="middle"><img class="absmiddle stip" title="{$lang.NewsInf|sanitize}" src="{$imgpath}/help.png" alt="" /> <strong>{#News#}</strong></td>
      <td align="center" valign="middle"><img class="absmiddle stip" title="{$lang.NewsInf|sanitize}" src="{$imgpath}/help.png" alt="" /> <strong>{#Articles#}</strong></td>
    </tr>
    <tr>
      <td align="center" valign="middle">
        <select style="width: 250px" name="areas[]" size="5" multiple="multiple">
          {foreach from=$areas item=area}
            <option value="{$area->Id}" {if in_array($area->Id, $areas_form)}selected="selected"{/if}>{$area->Name}</option>
          {/foreach}
        </select>
      </td>
      <td align="center" valign="middle">
        <select style="width: 250px" name="news[]" size="5" multiple="multiple">
          {foreach from=$news_cats item=news}
            <option value="{$news->Id}" {if in_array($news->Id, $news_form)}selected="selected"{/if}>{$news->Name}</option>
          {/foreach}
          <option value="" {if empty($news_form)}selected="selected"{/if}> ---------- {#Sys_off#} ---------- </option>
        </select>
      </td>
      <td align="center" valign="middle">
        <select style="width: 250px" name="articles[]" size="5" multiple="multiple">
          {foreach from=$articles_cats item=articles}
            <option value="{$articles->Id}" {if in_array($articles->Id, $articles_form)}selected="selected"{/if}>{$articles->Name}</option>
          {/foreach}
          <option value="" {if empty($articles_form)}selected="selected"{/if}> ---------- {#Sys_off#} ---------- </option>
        </select>
      </td>
    </tr>
  </table>
  <br />
  <table width="100%" border="0" cellspacing="0" cellpadding="3">
    <tr class="{cycle values='first,second'}">
      <td width="80" class="headers">{#Bereich#}</td>
      <td width="90" align="center" class="headers">{#Sys_on#}</td>
      <td width="95" class="headers" nowrap="nowrap"><img class="absmiddle stip" title="{$lang.PriorityInf|sanitize}" src="{$imgpath}/help.png" alt="" /> {#Priority#}</td>
      <td width="150" class="headers" nowrap="nowrap"><img class="absmiddle stip" title="{$lang.ChangesInf|sanitize}" src="{$imgpath}/help.png" alt="" /> {#Changes#}</td>
    </tr>
    {foreach from=$items item=item}
      <tr class="{cycle values='second,first'}">
        <td class="row_spacer"><strong>{$item->title}</strong></td>
        <td class="row_spacer">
          <input type="radio" name="aktiv[{$item->Id}]" value="1" {if $item->active == 1}checked="checked"{/if} />{#Yes#}
          <input type="radio" name="aktiv[{$item->Id}]" value="0" {if $item->active != 1}checked="checked"{/if} />{#No#}
        </td>
        <td class="row_spacer">
          <select name="prio[{$item->Id}]" id="prio[{$item->Id}]" style="width: 100px;">
            <option value="1.0" {if $item->prio == '1.0'}selected="selected"{/if}>1.0</option>
            <option value="0.9" {if $item->prio == '0.9'}selected="selected"{/if}>0.9</option>
            <option value="0.8" {if $item->prio == '0.8'}selected="selected"{/if}>0.8</option>
            <option value="0.7" {if $item->prio == '0.7'}selected="selected"{/if}>0.7</option>
            <option value="0.6" {if $item->prio == '0.6'}selected="selected"{/if}>0.6</option>
            <option value="0.5" {if $item->prio == '0.5'}selected="selected"{/if}>0.5</option>
            <option value="0.4" {if $item->prio == '0.4'}selected="selected"{/if}>0.4</option>
            <option value="0.3" {if $item->prio == '0.3'}selected="selected"{/if}>0.3</option>
            <option value="0.2" {if $item->prio == '0.2'}selected="selected"{/if}>0.2</option>
            <option value="0.1" {if $item->prio == '0.1'}selected="selected"{/if}>0.1</option>
            <option value="0.0" {if $item->prio == '0.0'}selected="selected"{/if}>0.0</option>
          </select>
        </td>
        <td class="row_spacer">
          <select name="change[{$item->Id}]" style="width: 150px;">
            <option value="always" {if $item->changef == 'always'}selected="selected"{/if}>{#always#}</option>
            <option value="hourly" {if $item->changef == 'hourly'}selected="selected"{/if}>{#hourly#}</option>
            <option value="daily" {if $item->changef == 'daily'}selected="selected"{/if}>{#daily#}</option>
            <option value="weekly" {if $item->changef == 'weekly'}selected="selected"{/if}>{#weekly#}</option>
            <option value="monthly" {if $item->changef == 'monthly'}selected="selected"{/if}>{#monthly#}</option>
            <option value="yearly" {if $item->changef == 'yearly'}selected="selected"{/if}>{#yearly#}</option>
            <option value="never" {if $item->changef == 'never'}selected="selected"{/if}>{#never#}</option>
          </select>
        </td>
      </tr>
    {/foreach}
  </table>
  <br />
  <input type="submit" class="button" value="{#Save#}" />
</form>
<input type="button" class="button" onclick="location.href = 'index.php?do=seo&sub=sitemap_archive';" value="{#Gen_Sitemap#}" />
<br />
<br />
