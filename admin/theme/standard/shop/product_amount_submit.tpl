{if $not_on_store == 1}
    <div class="shop_lowamount">
      <h3>{$p.VMsg|sanitize}</h3>
    </div>
{else}
    <div class="shop_product_vars">
      <table width="100%" cellpadding="0" cellspacing="1">
        <tr>
          <td width="150">
            {if $p.EinzelBestellung != 1}
              {if $p.MinBestellung != 0}
                {#Shop_min_order#} {$p.MinBestellung}
                <br />
              {/if}
              {if $p.MaxBestellung != 0}
                {#Shop_max_order#} {$p.MaxBestellung}
              {else}
                {if $p.MaxBestellung == 0 && $p.MinBestellung == 0}
                  {#Shop_amount#}
                {/if}
              {/if}
            {/if}
            <input type="hidden" name="ajax" value="1" />
            <noscript>
            <input type="hidden" name="ajax" value="0" />
            </noscript>
          </td>
          <td>
            {if $p.EinzelBestellung == 1}
              <input class="input" name="dis_amount" type="text" style="width: 40px; margin-right: 5px" value="1" maxlength="1" disabled="disabled" />
              {if $not_on_store != 1}
                {if $p.Preis > 0}
                  <button {if $not_possible_to_buy == 1}disabled="disabled"{/if} class="shop_buttons_big" type="submit"><img src="{$imgpath}/shop/basket_simple.png" alt="" /> {#Shop_toBasket#}</button>
                  {/if}
                <button {if $not_possible_to_buy == 1}disabled="disabled"{/if} class="shop_buttons_big_second" onclick="document.getElementById('to_mylist').value = '1';" type="submit"><img src="{$imgpath}/shop/wishlist.png" alt="" />{#Shop_WishList#}</button>
                <input type="hidden" name="mylist" id="to_mylist" value="0" />
              {/if}
            {else}
              <label>
                <input {if $not_possible_to_buy == 1}disabled="disabled"{/if} class="input" name="amount" type="text" style="width: 40px; margin-right: 5px" value="{if $p.MinBestellung != 0}{$p.MinBestellung}{else}1{/if}" maxlength="5" />
              </label>
              {if $not_on_store != 1}
                {if $p.Preis > 0}
                  <button {if $not_possible_to_buy == 1}disabled="disabled"{/if} class="shop_buttons_big" type="submit"><img src="{$imgpath}/shop/basket_simple.png" alt="" /> {#Shop_toBasket#}</button>
                  {/if}
                <button {if $not_possible_to_buy == 1}disabled="disabled"{/if} class="shop_buttons_big_second" onclick="document.getElementById('to_mylist').value = '1';" type="submit"><img src="{$imgpath}/shop/wishlist.png" alt="" />{#Shop_WishList#}</button>
                <input type="hidden" name="mylist" id="to_mylist" value="0" />
              {/if}
            {/if}
          </td>
        </tr>
      </table>
      {if $not_possible_to_buy != 1}
        <input type="hidden" name="action" value="to_cart" />
        <input type="hidden" name="redir" value="{page_link|urldecode}" />
        <input type="hidden" name="product_id" value="{$p.Id}" />
      {/if}
    </div>
{/if}

