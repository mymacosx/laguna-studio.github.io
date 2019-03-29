{if (!empty($PollResultsSmall) || !empty($PollAnswersSmall)) && $smarty.request.p != 'poll'}
<script type="text/javascript">
<!-- //
togglePanel('navpanel_poll', 'togglerboxes', 30, '{$basepath}');
//-->
</script>

<div class="round">
  <div class="opened" id="navpanel_poll" title="{#Poll_Name#}">
    <div class="boxes_body poll_back_small">
      <div id="pollform">
        {include file="$incpath/poll/pollout_small_raw.tpl"}
      </div>
    </div>
  </div>
</div>
{/if}
