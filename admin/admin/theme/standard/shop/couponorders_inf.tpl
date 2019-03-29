{if $c->Multi}
<script type="text/javascript">
<!-- //
$(document).ready(function(){
    $('#custom_{$c->Id}').colorbox({
        width: "300px",
        inline: true,
        href: "#conhelp_{$c->Id}"
    });
});
//-->
</script>

<a style="cursor: pointer" class="stip" title="{#Shop_ordersS#}" id="custom_{$c->Id}"><img src="{$imgpath}/orders.png" class="absmiddle" alt="" /></a>
<div style="display: none">
  <div id="conhelp_{$c->Id}" style="padding: 20px">
    {foreach from=$c->Multi item=m}
      <a title="{#Schet#} №{$m->Id}" class="colorbox" href="index.php?do=shop&amp;sub=edit_order&amp;id={$m->Id}&amp;status={$m->Status}&amp;noframes=1">{#Schet#} №{$m->Id}</a>
      <br />
    {/foreach}
  </div>
</div>
{/if}
