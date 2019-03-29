<script type="text/javascript">
<!-- //
function show_tell_form() {
    if (document.getElementById('seen_tell').selected == true) {
        document.getElementById('seen_tell_form').style.display = '';
    } else {
        document.getElementById('seen_tell_form').style.display = 'none';
    }
}
//-->
</script>

<div class="shop_headers">{#Shop_detailLastSeen#}</div>
{if $send == 1}
  <div class="shop_contents_box"> {#Shop_tellOk#} </div>
  <br />
{/if}
{if $nocheckbox == 1}
  <div class="shop_contents_box"> {#Shop_noSelection#} </div>
  <br />
{/if}
<div class="shop_contents_box">
  {if $is_seen_products != 1}
    {#Shop_noSeenProducts#}
  {else}
    <form method="post" action"index.php?p=shop&amp;area={$area}&amp;action=showseenproducts" onsubmit="return check_shop_tellform();">
          {foreach from=$seen_products_array item=p name=pro}
            <div class="shop_tabs_items">
              <div style="width: 50px; float: left; text-align: center">
                <input name="prodid[{$p.Id}]" type="checkbox" value="{$p.Kategorie}" />
              </div>
              <div class="shop_tabs_items_left"><a class="stip" title="{$p.Beschreibung|tooltip:500}" href="{$p.ProdLink}{if isset($smarty.request.blanc) && $smarty.request.blanc == 1}&amp;blanc=1{/if}"><img class="shop_productimage_left" src="{$p.Bild_Klein}" alt="" /></a></div>
              <div class="shop_tabs_items_right stip" title="{$p.Beschreibung|tooltip:500}">
                <a title="{$p.Titel|sanitize}" href="{$p.ProdLink}{if isset($smarty.request.blanc) && $smarty.request.blanc == 1}&amp;blanc=1{/if}">{$p.Titel|sanitize}</a>
                <br />
                <strong><small>{$p.Kategorie_Name|sanitize}</small></strong>
                <br />
                {if $shopsettings->PreiseGaeste == 1 || $loggedin}
                  {if $p.Preis > 0}
                    <strong>{$p.Preis|numformat} {$currency_symbol}</strong>
                    {if $no_nettodisplay != 1}
                      {if $price_onlynetto != 1}
                        <span class="shop_subtext">({#Shop_icludes#} {$p.product_ust}% {#Shop_vat#})</span>
                      {/if}
                      {if $price_onlynetto == 1 && !empty($p.price_ust_ex)}
                        <span class="shop_subtext">({#Shop_exclVat#} {$p.product_ust}% {#Shop_vat#})</span>
                      {/if}
                    {/if}
                  {else}
                    <strong>{#Zvonite#}</strong>
                  {/if}
                {else}
                  <strong>{#Shop_prices_justforUsers#}</strong>
                {/if}
              </div>
              <div class="clear"></div>
            </div>
          {/foreach}
          <br />
          <br />
          <div class="shop_headers">{#Shop_selectedProducts#}</div>

<script type="text/javascript">
<!-- //
function check_shop_tellform() {
    if (document.getElementById('seen_tell').selected == true) {
        if (document.getElementById('pte').value == '' || document.getElementById('pte').value.indexOf('@') == -1) {
            alert('{#Shop_tellNoRE#}');
            document.getElementById('pte').focus();
            return false;
        }
        if (document.getElementById('ptn').value == '') {
            alert('{#Shop_tellNoNA#}');
            document.getElementById('ptn').focus();
            return false;
        }
        if (document.getElementById('ptr').value == '' || document.getElementById('ptr').value.indexOf('@') == -1) {
            alert('{#Comment_NoEmail#}');
            document.getElementById('ptr').focus();
            return false;
        }
        if (document.getElementById('ptrn').value == '') {
            alert('{#Comment_NoAuthor#}');
            document.getElementById('ptrn').focus();
            return false;
        }
    }
}
  //-->
</script>

          <select class="input" style="width: 200px" onchange="show_tell_form();" name="subaction" id="subaction">
            <option value="del">{#Delete#}</option>
            <option value="merge">{#Merge#}</option>
            <option id="seen_tell" value="sendfriend">{#Shop_tellFriend#}</option>
          </select>
          <div id="seen_tell_form" style="display: none">
            <br />
            <strong>{#Shop_tellReciever#}</strong>
            <br />
            <input name="prod_tell_email" type="text" class="input" id="pte" style="width: 200px" maxlength="50" value="{$smarty.request.prod_tell_email|escape: html}" />
            <br />
            <strong>{#Shop_tellRecieverName#}</strong>
            <br />
            <input name="prod_tell_name" type="text" class="input" id="ptn" style="width: 200px" maxlength="50" value="{$smarty.request.prod_tell_name|escape: html}" />
            <br />
            <strong>{#SendEmail_Email#}</strong>
            <br />
            <input name="prod_tell_remail" type="text" class="input" id="ptr" style="width: 200px" value="{$smarty.request.prod_tell_remail|escape: html|default:$smarty.session.login_email}" maxlength="50" />
            <br />
            <strong>{#Contact_myName#}</strong>
            <br />
            <input name="prod_tell_rname" type="text" class="input" id="ptrn" style="width: 200px" value="{$smarty.request.prod_tell_rname|escape: html|default:$smarty.session.user_name}" maxlength="50" />
            <br />
            <br />
          </div>
          <input  type="submit" class="button" value="{#ButtonSend#}" />
          <br />
    </form>
    <br />
  {/if}
</div>
