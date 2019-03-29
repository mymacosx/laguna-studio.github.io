<script type="text/javascript">
<!-- //
togglePanel('navpanel_payment', 'togglerboxes', 30, '{$basepath}');
//-->
</script>

<div class="round">
  <div class="opened" id="navpanel_payment" title="{#Shop_payment_images_inf#}">
    <div class="boxes_body">
      {foreach from=$payment item=payments}
        {if $payments->Icon == ''}
          <a class="colorbox_small" href="index.php?p=misc&amp;do=payment_info&amp;id={$payments->Id}">{$payments->Name|sanitize}</a>
          <br />
        {else}
          <div align="center">
            <a class="colorbox_small stip" title="{$payments->Name|tooltip}" href="index.php?p=misc&amp;do=payment_info&amp;id={$payments->Id}"><img src="{$baseurl}/uploads/shop/payment_icons/{$payments->Icon}" alt="" border="0" /></a>
          </div>
        {/if}
      {/foreach}
    </div>
  </div>
</div>
