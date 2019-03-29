<div class="header">{#Forums_Del_Topics#}</div>
<div class="subheaders">
  {if $admin_settings.Ahelp == 1}
    <a class="colorbox" href="index.php?do=help&amp;sub={$helpquery}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/s_help.png" alt="" border="0" /> {#GlobalHelp#}</a>&nbsp;&nbsp;&nbsp;
    {/if}
  <a class="colorbox" href="index.php?do=support&amp;sub=send_order&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/send.png" alt="" border="0" /> {#SendOrder#}</a>
</div>
<form name="delForm" id="delForm" action="" method="post">
  {if $smarty.post.delete == 1}
    <div class="subheaders">
      {if $match == 0}
        {#Forums_delT_nd#}
      {else}
        {$lang.Forums_delT_cd|replace: "__MATCH__": "$match"}
      {/if}
    </div>
  {/if}
  <table width="100%" border="0" cellpadding="5" cellspacing="0">
    <tr>
      <td width="180" class="row_left">{#Forums_delT_delOlder#}</td>
      <td class="row_right">
        <select style="width: 150px" class="input" name="date">
          <option value="0"></option>
          <option value="1" {if $smarty.post.date == 1}selected="selected"{/if}>{#Forums_delT_1d#}</option>
          <option value="7" {if $smarty.post.date == 7}selected="selected"{/if}>{#Forums_delT_1w#}</option>
          <option value="14" {if $smarty.post.date == 14}selected="selected"{/if}>{#Forums_delT_2w#}</option>
          <option value="30" {if $smarty.post.date == 30}selected="selected"{/if}>{#Forums_delT_1m#}</option>
          <option value="90" {if $smarty.post.date == 90}selected="selected"{/if}>{#Forums_delT_3m#}</option>
          <option value="180" {if $smarty.post.date == 180}selected="selected"{/if}>{#Forums_delT_6m#}</option>
          <option value="365" {if $smarty.post.date == 365}selected="selected"{/if}>{#Forums_delT_1y#}</option>
          <option value="730" {if $smarty.post.date == 730}selected="selected"{/if}>{#Forums_delT_2y#}</option>
        </select>
      </td>
    </tr>
    <tr>
      <td width="180" class="row_left">{#Forums_delT_rLess#}</td>
      <td class="row_right">
        <select style="width: 150px" class="input" name="reply_count">
          <option></option>
          {section name=rc1 loop=9 step=1}
            <option value="{$smarty.section.rc1.index+1}" {if $smarty.request.reply_count == $smarty.section.rc1.index+1}selected="selected"{/if}>{$smarty.section.rc1.index+1}</option>
          {/section}
          {section name=rc2 loop=200 step=10}
            <option value="{$smarty.section.rc2.index+10}" {if $smarty.request.reply_count == $smarty.section.rc2.index+10}selected="selected"{/if}>{$smarty.section.rc2.index+10}</option>
          {/section}
        </select>
      </td>
    </tr>
    <tr>
      <td width="180" class="row_left">{#Forums_delT_hLess#}</td>
      <td class="row_right">
        <select style="width: 150px" class="input"name="hits_count">
          <option></option>
          {section name=rc2 loop=90 step=10}
            <option value="{$smarty.section.rc2.index+10}" {if $smarty.request.hits_count == $smarty.section.rc2.index+10}selected="selected"{/if}>{$smarty.section.rc2.index+10}</option>
          {/section}
          {section name=rc2 loop=2000 step=100}
            <option value="{$smarty.section.rc2.index+100}" {if $smarty.request.hits_count == $smarty.section.rc2.index+100}selected="selected"{/if}>{$smarty.section.rc2.index+100}</option>
          {/section}
        </select>
      </td>
    </tr>
    <tr>
      <td width="180" class="row_left">{#Forums_delT_tClosed#}</td>
      <td class="row_right"><input name="topic_closed" type="checkbox" value="1" {if $smarty.post.topic_closed == 1}checked="checked"{/if} /></td>
    </tr>
    <tr>
      <td width="180" class="row_left">{#Forums_delT_ao#}</td>
      <td class="row_right">
        <label><input name="verkn" type="radio" value="OR" {if $smarty.post.verkn == 'OR' || !$smarty.post.del}checked="checked"{/if} />{#Forums_delT_or#}</label>
        <label><input name="verkn" type="radio" value="AND" {if $smarty.post.verkn == 'AND'}checked="checked"{/if} />{#Forums_delT_and#}</label>
      </td>
    </tr>
    <tr>
      <td width="180" class="row_left">{#Forums_delT_self#}</td>
      <td class="row_right">
        {strip}
          <select style="width: 150px" class="input" name="fid" id="fid">
            {foreach from=$forums item=f}
              <optgroup label="{$f->title|sanitize}"></optgroup>
              {foreach from=$f->forums item=fo}
                <option value="{$fo->id}">{$fo->title|sanitize}</option>
              {/foreach}
            {/foreach}
          </select>
        {/strip}
      </td>
    </tr>
  </table>
  <input class="button" type="button" onclick="if (confirm('{#Forums_delT_Csubmit#}')) document.forms['delForm'].submit();" value="{#Forums_delT_submit#}" />
  <input name="delete" type="hidden" id="delete" value="1" />
</form>
