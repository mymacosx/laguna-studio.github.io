{script file="$jspath/jvalidate.js" position='head'}
<script type="text/javascript">
<!-- //
{include file="$incpath/other/jsvalidate.tpl"}
$(document).ready(function() {
    $('#ajaxcp').validate({
        rules: { },
        messages: { },
        submitHandler: function(form) {
            $(form).ajaxSubmit({
                target: '#ajaxcpresult',
                timeout: 6000,
                clearForm: false,
                resetForm: false
            });
        }
    });
});
//-->
</script>

{if !empty($coupon_hersteller)}
    <div class="shop_data_forms_headers">{#Manufacturers#} <span style="font-weight: normal">{#ShopCouponInfo#}</span></div>
    <ul>
      {foreach name=man from=$coupon_hersteller item=man}
          <li style="float: left; width: 49%;">
            <a target="_blank" style="text-decoration: none;" href="index.php?p=manufacturer&amp;area={$area}&amp;action=showdetails&amp;id={$man.Id}&amp;name={$man.Name|translit}">{$man.Name|sanitize}</a>
          </li>
          {if $smarty.foreach.man.iteration % 2 == 0}
          </ul><div style="clear:both;"></div><ul>
          {/if}
      {/foreach}
    </ul>
    <br />
{/if}
<p align="center"><strong>{#ShopCouponQuestOk#}</strong></p>
<form  id="ajaxcp" name="ajaxcp" action="{$baseurl}/index.php?action=ajaxcoupondel&p=shop">
  <p align="center">
    <input type="submit" class="button" value="{#Shop_del_coupon#}" />
  </p>
</form>
