<form method="post" action="">
  <div class="box_innerhead">{#ExtendedSearch#}</div>
  <div class="infobox">
    <table width="100%" cellspacing="0" cellpadding="0">
      <tr>
        <td width="120"><label for="l_q">{#Shop_search_e_keyword#}</label></td>
        <td><input id="l_q" name="shop_q" type="text" class="input" value="{if $smarty.request.shop_q != 'empty'}{$smarty.request.shop_q|escape: html}{/if}" style="width: 155px" maxlength="35" /></td>
        <td width="20">&nbsp;</td>
        <td width="120">{#Shop_Offers#}</td>
        <td><label><input style="vertical-align: middle" type="checkbox" name="offers" value="1" {if $smarty.request.offers == 1}checked="checked" {/if}/> {#Shop_searchJustOffers#}</label></td>
      </tr>
      <tr>
        <td width="120"><label for="l_cid">{#Global_Categ#}</label></td>
        <td><select class="input" style="width: 160px" name="cid" id="l_cid">
            <option value="0">- {#AllCategs#}</option>
            {foreach from=$MySearchCategs item=mysc}
              <option value="{$mysc->Id}" {if $mysc->parent_id == 0}class="shop_selector_back"{else}class="shop_selector_subs"{/if} {if isset($smarty.request.cid) && $smarty.request.cid == $mysc->Id}selected="selected" {/if}>{$mysc->Entry|sanitize}</option>
              {if $mysc->Sub1}
                {foreach from=$mysc->Sub1 item=sub1}
                  <option value="{$sub1->Id}" {if isset($smarty.request.cid) && $smarty.request.cid == $sub1->Id}selected="selected" {/if}>-&nbsp;{$sub1->Entry|sanitize}</option>
                  {if $sub1->Sub2}
                    {foreach from=$sub1->Sub2 item=sub2}
                      <option value="{$sub2->Id}" {if isset($smarty.request.cid) && $smarty.request.cid == $sub2->Id}selected="selected" {/if}>-&nbsp;-&nbsp;{$sub2->Name|sanitize}</option>
                      {if $sub2->Sub3}
                        {foreach from=$sub2->Sub3 item=sub3}
                          <option value="{$sub3->Id}" {if isset($smarty.request.cid) && $smarty.request.cid == $sub3->Id}selected="selected" {/if}>-&nbsp;-&nbsp;-&nbsp;{$sub3->Name|sanitize}</option>
                          {if $sub3->Sub4}
                            {foreach from=$sub3->Sub4 item=sub4}
                              <option value="{$sub4->Id}" {if isset($smarty.request.cid) && $smarty.request.cid == $sub4->Id}selected="selected" {/if}>-&nbsp;-&nbsp;-&nbsp;-&nbsp;{$sub4->Name|sanitize}</option>
                            {/foreach}
                          {/if}
                        {/foreach}
                      {/if}
                    {/foreach}
                  {/if}
                {/foreach}
              {/if}
            {/foreach}
          </select>
        </td>
        <td width="20">&nbsp;</td>
        <td width="120"><label for="l_manu">{#Manufacturer#}</label></td>
        <td>
          <select class="input" style="width: 160px" name="man" id="l_manu">
            <option value="0"> - {#Shop_allManuf#}</option>
            {foreach from=$shop_manufaturers item=man}
              <option value="{$man.Id}" {if $smarty.request.man == $man.Id}selected="selected" {/if}>{$man.Name|sanitize}</option>
            {/foreach}
          </select>
        </td>
      </tr>
      <tr>
        <td width="120"><label for="l_pf">{#Shop_search_e_pfrom#}</label></td>
        <td><input name="pf" id="l_pf" type="text" class="input" value="{$smarty.request.pf|default:'1.00'}" style="width: 155px" maxlength="15" /></td>
        <td width="20">&nbsp;</td>
        <td width="120"><label for="l_pt">{#Shop_search_e_ptill#}</label></td>
        <td><input name="pt" id="l_pt" type="text" class="input" value="{$smarty.request.pt|default:'1000.00'}" style="width: 155px" maxlength="15" /></td>
      </tr>
      <tr>
        <td width="120"><label></label></td>
        <td><input id="small_search_button" type="submit" class="button" value="{#StartSearch#}" /></td>
        <td width="20">&nbsp;</td>
        <td width="120">&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
    </table>
    <div class="error_box" id="small_search_hidden_div" style="display: none">{#Shop_searchInf#}</div>
    <input type="hidden" name="p" value="shop" />
    <input type="hidden" name="action" value="showproducts" />
    <input type="hidden" name="list" value="{$smarty.request.list|default:$shopsettings->Sortable_Produkte}" />
    <input type="hidden" name="limit" value="{$smarty.request.limit|default:$shopsettings->Produkt_Limit_Seite}" />
    <input type="hidden" name="s" value="1" />
    <input type="hidden" name="page" value="{$smarty.request.page|default:'1'}" />
    <input type="hidden" name="avail" value="{$smarty.request.avail|default:'0'}" />
  </div>
</form>
