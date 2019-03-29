{if isset($AktiveLink) && $AktiveLink == 1}
    {if $smarty.request.p == 'showforums' || $smarty.request.p == 'showforum' || $smarty.request.p == 'showtopic' || $smarty.request.p == 'forum' || $smarty.request.p == 'forums' || $smarty.request.p == 'members' || $smarty.request.p == 'newpost' || $smarty.request.p == 'pn' || $smarty.request.p == 'addpost' || $smarty.request.p == 'user'}
      <div class="infobox">
        <div class="h3"><img src="{$imgpath_forums}reklama.png" alt="" class="absmiddle" />&nbsp;{#Reklama#}</div>
        <br />
        {$Sape_links}
        {$Linkfeed_links}
        {$setlinks_links}
        {$mainlink_links}
        {$trustlink_links}
      </div>
    {else}
<script type="text/javascript">
<!-- //
togglePanel('navpanel_advertising', 'togglerboxes', 30, '{$basepath}');
//-->
</script>

      <div class="round">
        <div class="opened" id="navpanel_advertising" title="{#Reklama#}">
          <div class="boxes_body">
            {$Sape_links}
            {$Linkfeed_links}
            {$setlinks_links}
            {$mainlink_links}
            {$trustlink_links}
          </div>
        </div>
      </div>
    {/if}
{/if}
