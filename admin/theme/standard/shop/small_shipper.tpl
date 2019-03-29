{if !empty($ShipperAll)}
<script type="text/javascript">
<!-- //
togglePanel('navpanel_shipper', 'togglerboxes', 30, '{$basepath}');
//-->
</script>

<div class="round">
  <div class="opened" id="navpanel_shipper" title="{#Shop_shipping_method#}">
    <div align="center" class="boxes_body">
      {foreach from=$ShipperAll item=SA}
        <img class="stip" title="{$SA->Text|tooltip:100}" src="{$baseurl}/uploads/shop/shipper_icons/{$SA->Icon}" alt="" />
      {/foreach}
    </div>
  </div>
</div>
{/if}
