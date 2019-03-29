{if $last_post_array}
  <div class="box_innerhead">{#Forums_lastPosts#}</div>
  {foreach from=$last_post_array item=x}
    <div class="{cycle name='gb6' values='links_list_newstart,links_list_newstart_second'}">
      <div style="float: left">
        <h4><a href="{$x->LpLink}"><img class="absmiddle" src="{$imgpath_forums}newpost_startpage.png" alt="" /></a> <a href="{$x->LpLink}" title="{$x->LpTitle|truncate: 100|sanitize}">{$x->LpTitle|truncate:45|sanitize}</a></h4>
      </div>
      <div style="float: right;padding: 8px"> {$x->Datum|date_format: $lang.DateFormat} </div>
      <br style="clear: both" />
    </div>
  {/foreach}
{/if}
