<script type="text/javascript">
<!-- //
function switchImage(data) {
    document.getElementById('img').innerHTML = '<img src="' + data + '" alt="" border="0" />';
}
//-->
</script>

<div class="shop_popdiv">
  <div class="shop_popdiv_header">{$title_html}</div>
  <table width="100%" border="0" cellpadding="0" cellspacing="0">
    <tr>
      <td valign="top" class="shop_popdiv_content"><div class="shop_pop_content_overflow" style="overflow: hidden"> <span id="img"> <img class="shop_productimage_left" src="{$prod_image}" alt="" /> </span> </div></td>
      <td width="150" align="center" valign="top">
        <div class="shop_popdiv_images_container">
          {foreach from=$images item=im}
          <div class="shop_popdiv_images"><a href="{$im.Bild_Normal}" onclick="switchImage('{$im.Bild_Normal}'); return false;"><img src="{$im.Bild}" alt="" /></a></div>
          <img src="{$im.Bild_Normal}" alt="" style="display: none" />
          {/foreach}
        </div>
      </td>
    </tr>
  </table>
</div>
