{if isset($Baskets)}
<script type="text/javascript">
<!-- //
togglePanel('navpanel_savedbasket', 'togglerboxes', 30, '{$basepath}');
//-->
</script>

<div class="round">
  <div class="opened" id="navpanel_savedbasket" title="{#Shop_savedBasketInf#}">
    <div class="boxes_body">
      {if $Bcc->Bcount > 1}
        <strong>{#Shop_savedBasketMsgInf1#}</strong>
      {else}
        <strong>{#Shop_savedBasketMsgInf2#}</strong>
      {/if}
      <br />
      <br />
      <span class="bull">&bull;</span> <a href="index.php?p=shop&amp;action=showsavedbaskets">{#Shop_savedBasketLink1#}</a>
    </div>
  </div>
</div>
{/if}
