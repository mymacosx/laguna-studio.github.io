<div class="shop_payment_stepdiv">
  <div class="shop_payment_headers">
    <form method="post" name="step1_f" action="index.php">
      <input type="hidden" name="p" value="shop" />
      <input type="hidden" name="area" value="{$area}" />
      <input type="hidden" name="action" value="shoporder" />
      <input type="hidden" name="subaction" value="step1" />
      {if isset($smarty.request.order) && $smarty.request.order == 'guest'}
        <input type="hidden" name="order" value="guest" />
      {/if}
    </form>
    {if $smarty.session.shopstep>=1} <a style="cursor: pointer;text-decoration: none" href="javascript: document.forms['step1_f'].submit();"> {/if}
      <div class="{if $smarty.session.shopstep == 1}shop_payment_steps shop_steps_text_active{else}shop_payment_steps shop_steps_text{/if}">
        <span class="{if $smarty.session.shopstep == 1}shop_steps_title_big_active{else}shop_steps_title_big{/if}">1.</span>
        <span class="{if $smarty.session.shopstep == 1}shop_steps_title_active{else}shop_steps_title{/if}">{#Shop_step_1#}</span>
        <br />
        {#Shop_step_1_inf#}
      </div>
      {if $smarty.session.shopstep>=1} </a> {/if}
  </div>
  <div class="shop_payment_headers">
    <form method="post" name="step2_f" action="index.php">
      <input type="hidden" name="p" value="shop" />
      <input type="hidden" name="area" value="{$area}" />
      <input type="hidden" name="action" value="shoporder" />
      <input type="hidden" name="subaction" value="step2" />
      {if isset($smarty.request.order) && $smarty.request.order == 'guest'}
        <input type="hidden" name="order" value="guest" />
      {/if}
    </form>
    {if $smarty.session.shopstep>=2} <a style="cursor: pointer;text-decoration: none" href="javascript: document.forms['step2_f'].submit();"> {/if}
      <div class="{if $smarty.session.shopstep == 2}shop_payment_steps shop_steps_text_active{else}shop_payment_steps shop_steps_text{/if}">
        <span class="{if $smarty.session.shopstep == 2}shop_steps_title_big_active{else}shop_steps_title_big{/if}">2.</span>
        <span class="{if $smarty.session.shopstep == 2}shop_steps_title_active{else}shop_steps_title{/if}">{#Shop_step_2#}</span>
        <br />
        {#Shop_step_2_inf#}
      </div>
      {if $smarty.session.shopstep>=2} </a> {/if}
  </div>
  <div class="shop_payment_headers">
    <form method="post" name="step3_f" action="index.php">
      <input type="hidden" name="p" value="shop" />
      <input type="hidden" name="area" value="{$area}" />
      <input type="hidden" name="action" value="shoporder" />
      <input type="hidden" name="subaction" value="step3" />
      <input type="hidden" name="payment_id" value="{$smarty.request.payment_id}" />
      <input type="hidden" name="versand_id" value="{$smarty.request.versand_id}" />
      {if isset($smarty.request.order) && $smarty.request.order == 'guest'}
        <input type="hidden" name="order" value="guest" />
      {/if}
    </form>
    {if $smarty.session.shopstep>=3} <a style="cursor: pointer;text-decoration: none" href="javascript: document.forms['step3_f'].submit();"> {/if}
      <div class="{if $smarty.session.shopstep == 3}shop_payment_steps shop_steps_text_active{else}shop_payment_steps shop_steps_text{/if}">
        <span class="{if $smarty.session.shopstep == 3}shop_steps_title_big_active{else}shop_steps_title_big{/if}">3.</span>
        <span class="{if $smarty.session.shopstep == 3}shop_steps_title_active{else}shop_steps_title{/if}">{#Shop_step_3#}</span>
        <br />
        {#Shop_step_3_inf#}
      </div>
      {if $smarty.session.shopstep>=3} </a> {/if}
  </div>
  <div class="shop_payment_headers">
    <div class="{if $smarty.session.shopstep == 4}shop_payment_steps shop_steps_text_active{else}shop_payment_steps shop_steps_text{/if}">
      <span class="{if $smarty.session.shopstep == 4}shop_steps_title_big_active{else}shop_steps_title_big{/if}">4.</span>
      <span class="shop_steps_title">{#Shop_step_4#}</span>
      <br />
      {#Shop_step_4_inf#}
    </div>
  </div>
  <div class="shop_payment_headers">
    <div class="{if $smarty.session.shopstep == 'final'}shop_payment_steps shop_steps_text_active{else}shop_payment_steps shop_steps_text{/if}">
      <span class="{if $smarty.session.shopstep == 'final'}shop_steps_title_big_active{else}shop_steps_title_big{/if}">5.</span>
      <span class="shop_steps_title">{#Shop_step_5#}</span>
      <br />
      {#Shop_step_5_inf#}
    </div>
  </div>
</div>
<div class="clear"></div>
<br />
