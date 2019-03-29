{if $last_thread_array}
  <div class="box_innerhead">{#Forums_lastTopics#}</div>
  {foreach from=$last_thread_array item=x}
    <div class="{cycle name='gb6' values='links_list_newstart,links_list_newstart_second'}">
      <div style="float: left">
        <h4><a href="{$x->tlink}"><img class="absmiddle" src="{$imgpath_forums}newpost_startpage.png" alt="" /></a> <a href="{$x->tlink}" title="{$x->title|truncate: 100|sanitize}">{$x->title|truncate: 45: '...': true|sanitize}</a></h4>
      </div>
      <div style="float: right;padding: 8px"> {$x->datum|date_format: $lang.DateFormat} </div>
      <br style="clear: both" />
    </div>
  {/foreach}
{/if}
