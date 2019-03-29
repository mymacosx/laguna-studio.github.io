{script file="$jspath/jvalidate.js" position='head'}
<script type="text/javascript">
<!-- //
{include file="$incpath/other/jsvalidate.tpl"}
$(document).ready(function() {
    $('#ajaxcp').validate({
        rules: {
          coupon: { required: true }
        },
        messages: {
            coupon: { required: '' }
        },
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

<p align="center"><strong>{#ShopCouponQuest#}</strong></p>
{if $c_error}
  <div class="round">
    <div class="error_box">{$c_error}</div>
  </div>
{/if}
<form autocomplete="off" id="ajaxcp" method="post" name="ajaxcp" action="{$baseurl}/index.php?action=ajaxcoupon&p=shop">
  <p align="center">
    <input class="input" type="text" name="coupon" value="" /> &nbsp;
    <input class="button" type="submit" value="{#ShopCouponQuestB#}" />
  </p>
</form>
