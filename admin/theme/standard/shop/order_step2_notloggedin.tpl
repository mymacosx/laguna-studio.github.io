{if $guest_order == 1}
  {assign var=bwidth value="33"}
{else}
  {assign var=bwidth value="50"}
{/if}
{if $smarty.request.inf == 'regcode'}
  <div class="shop_reginfbox"> {#RegE_infEmailCode#} </div>
  <br />
{/if}
<table width="100%" cellspacing="0" cellpadding="0">
  <tr>
    {if $guest_order == 1}
      <td class="shop_step2_optionboxes_header" style="width: {$bwidth}%">{#Shop_v_opt#} 1 </td>
      <td>&nbsp;&nbsp;</td>
    {/if}
    <td class="shop_step2_optionboxes_header" style="width: {$bwidth}%">{#Shop_v_opt#} {if $guest_order == 1}2{else}1{/if}</td>
    <td>&nbsp;&nbsp;</td>
    <td class="shop_step2_optionboxes_header" style="width: {$bwidth}%">{#Shop_v_opt#} {if $guest_order == 1}3{else}2{/if}</td>
  </tr>
  <tr>
    {if $guest_order == 1}
      <td valign="top">
        <div class="shop_step2_optionboxes_body"><strong>{#Shop_v_opt1_t#}</strong>
          <br />
          <br />
          {#Shop_v_opt_2#}
          <br />
          <br />
          <form method="get" action="index.php">
            <div align="center">
              <input type="submit" class="button" value="{#GlobalNext#}" />
            </div>
            <input type="hidden" name="p" value="shop" />
            <input type="hidden" name="area" value="{$area}" />
            <input type="hidden" name="action" value="shoporder" />
            <input type="hidden" name="subaction" value="step2" />
            <input type="hidden" name="order" value="guest" />
          </form>
          </p>
        </div></td>
      <td>&nbsp;&nbsp;</td>
    {/if}
    <td valign="top">
      <div class="shop_step2_optionboxes_body">
        <strong>{#Shop_v_opt2_t#}</strong>
        <br />
        <br />
        {#Shop_v_opt2#}
        <br />
        <br />
        {if $login_false == 1}
          <div class="error_box"> {#Shop_v_opt2_e#} </div>
        {/if}
        <form method="post" action="index.php">
          <table width="100%" cellspacing="1" cellpadding="0">
            <tr>
              <td><label for="l_s_login_email">{#Email#}&nbsp;</label></td>
              <td><input name="s_login_email" id="l_s_login_email" class="input" style="width: 100px" type="text" value="" /></td>
            </tr>
            <tr>
              <td><label for="l_s_login_pass">{#Pass#}&nbsp;</label></td>
              <td><input name="s_login_pass" id="l_s_login_pass" class="input" style="width: 100px" type="password" value="" /></td>
            </tr>
            <tr>
              <td>&nbsp;</td>
              <td>
                <input name="s_staylogged" type="checkbox" value="1" checked="checked" class="absmiddle" />
                <span class="tooltip stip" title="{$lang.PassCookieT|tooltip}">{#PassCookieHelp#}</span>
                <br  />
                <input type="submit" class="button" value="{#Login_Button#}" />
                <input type="hidden" name="p" value="shop" />
                <input type="hidden" name="area" value="{$area}" />
                <input type="hidden" name="action" value="shoporder" />
                <input type="hidden" name="subaction" value="step2" />
                <input type="hidden" name="s_login" value="1" />
                {if isset($smarty.request.inf) && $smarty.request.inf == 'regcode'}
                  <input type="hidden" name="inf" value="regcode" />
                {/if}
              </td>
            </tr>
          </table>
        </form>
      </div></td>
    <td>&nbsp;</td>
    <td valign="top">
      <div class="shop_step2_optionboxes_body">
        <strong>{#Shop_v_opt3_t#}</strong>
        <br />
        <br />
        {#Shop_v_opt3#}
        <br />
        <br />
        <form method="post" action="index.php">
          <div align="center">
            <input type="submit" class="button" value="{#GlobalNext#}" />
          </div>
          <input type="hidden" name="p" value="shop" />
          <input type="hidden" name="area" value="{$area}" />
          <input type="hidden" name="action" value="shoporder" />
          <input type="hidden" name="subaction" value="step2" />
          <input type="hidden" name="register" value="new" />
        </form>
      </div>
    </td>
  </tr>
</table>
<br />
