<div class="shop_navibox">
  <a class="{if isset($smarty.request.start) && $smarty.request.start == 1}shop_navi_first_active{else}shop_navi_first{/if}" href="index.php?p=shop&amp;start=1"><img src="{$imgpath}/shop/navi_img.png" alt="" class="absmiddle" />{#Shop_starterPage#}</a>
  <a class="{if $smarty.request.t == 'liste'}shop_navi_first_active{else}shop_navi_first{/if}" href="index.php?p=shop&amp;action=showproducts&amp;cid=0&amp;page=1&amp;limit={$smarty.request.limit|default:$plim}&amp;t=liste"><img src="{$imgpath}/shop/navi_img.png" alt="" class="absmiddle" />{#Shop_starterShowAll#}</a>
  <div id="shopnavi">
    {foreach from=$MyShopNavi item=sn}
    {if $sn->Parent_Id == 0}
    {assign var=op value=$sn->Id}
    {assign var=firstsecond value=$sn->Id}
    {/if}
    <ul>
      <li class="first"> <a class="{if (isset($smarty.request.cid) && $smarty.request.cid == $sn->Id)  || (isset($smarty.request.navop) && $smarty.request.navop == $sn->Id)  || in_array($op,$navi_current)}shop_navi_first_active{else}shop_navi_first{/if}" href="index.php?p=shop&amp;action=showproducts&amp;cid={$sn->Id}&amp;page=1&amp;limit={$smarty.request.limit|default:$plim}&amp;t={$sn->Entry|translit}"> {$sn->Icon} {$sn->Entry|sanitize} </a> {if !$sn->Sub1}</li>
      {else}
      <ul>
        {/if}
        {foreach from=$sn->Sub1 item=sub1}
        <li class="second" {if !empty($smarty.request.cid) && ($smarty.request.cid == $op || (isset($sub1->navop) && $sub1->navop == $op) || in_array($op,$navi_current))}{else}style="display: none"{/if}> <a {if (isset($smarty.request.cid) && $smarty.request.cid == $sub1->Id) || in_array($sub1->Id,$navi_current)}style="font-weight: bold"{/if} href="index.php?p=shop&amp;action=showproducts&amp;cid={$sub1->Id}&amp;page=1&amp;limit={$smarty.request.limit|default:$plim}&amp;t={$sub1->Entry|translit}">{$sub1->Entry|sanitize}</a> {if !$sub1->Sub2} </li>
        {/if}
        {if $sub1->Sub2}
        <ul>
          {foreach from=$sub1->Sub2 item=sub2}
          <li class="third" {if (isset($smarty.request.cid) && $smarty.request.cid == $sub1->Id) || in_array($sub1->Id,$navi_current)}{else}style="display: none"{/if}> <a {if (isset($smarty.request.cid) && $smarty.request.cid == $sub2->Id) || in_array($sub2->Id,$navi_current)}style="font-weight: bold"{/if} href="index.php?p=shop&amp;action=showproducts&amp;cid={$sub2->Id}&amp;page=1&amp;limit={$smarty.request.limit|default:$plim}&amp;t={$sub2->Name|translit}">{$sub2->Name|sanitize}</a> {if !$sub2->Sub3} </li>
          {/if}
          {if $sub2->Sub3}
          <ul>
            {foreach from=$sub2->Sub3 item=sub3}
            <li class="fourth" {if (isset($smarty.request.cid) && $smarty.request.cid == $sub2->Id) || $smarty.request.parent == $sub2->Id || in_array($sub2->Id,$navi_current)}{else}style="display: none"{/if}> <a {if (isset($smarty.request.cid) && $smarty.request.cid == $sub3->Id) || in_array($sub3->Id,$navi_current)}style="font-weight: bold"{/if} href="index.php?p=shop&amp;action=showproducts&amp;cid={$sub3->Id}&amp;page=1&amp;limit={$smarty.request.limit|default:$plim}&amp;t={$sub3->Name|translit}">{$sub3->Name|sanitize}</a> {if !$sub3->Sub4} </li>
            {/if}
            {if $sub3->Sub4}
            <ul>
              {foreach from=$sub3->Sub4 item=sub4}
              <li class="fifth" {if (isset($smarty.request.cid) && $smarty.request.cid == $sub3->Id) || in_array($sub3->Id,$navi_current)}{else}style="display: none"{/if}> <a {if (isset($smarty.request.cid) && $smarty.request.cid == $sub4->Id) || in_array($sub4->Id,$navi_current)}style="font-weight: bold"{/if} href="index.php?p=shop&amp;action=showproducts&amp;cid={$sub4->Id}&amp;page=1&amp;limit={$smarty.request.limit|default:$plim}&amp;t={$sub4->Name|translit}">{$sub4->Name|sanitize}</a> </li>
              {/foreach}
            </ul>
            {/if}
            {if $sub3->Sub4}
            </li>
            {/if}
            {/foreach}
          </ul>
          {/if}
          {if $sub2->Sub3}
          </li>
          {/if}
          {/foreach}
        </ul>
        {if $sn->Sub1}
        </li>
        {/if}
        {/if}
        {/foreach}
        {$sn->Entry_End|default:''}
        {if !$sn->Sub1}
        {else}
      </ul>
      </li>
      {/if}
    </ul>
    {/foreach}
  </div>
</div>
<div class="shop_navibox">
  <div id="shopnavi_infolinks">
    <a class="{if isset($smarty.request.offers) && $smarty.request.offers == 1}shop_navi_first_active{else}shop_navi_first{/if}" href="index.php?p=shop&amp;action=showproducts&amp;page=1&amp;offers=1{if !empty($smarty.request.cid)}&amp;cid={$smarty.request.cid}{/if}&amp;limit={$smarty.request.limit|default:$plim}"><img src="{$imgpath}/shop/navi_img.png" alt="" class="absmiddle" /> {#Shop_Offers#}</a>
    <a class="{if isset($smarty.request.topseller) && $smarty.request.topseller == 1}shop_navi_first_active{else}shop_navi_first{/if}" href="index.php?p=shop&amp;action=showproducts&amp;page=1&amp;topseller=1{if !empty($smarty.request.cid)}&amp;cid={$smarty.request.cid}{/if}&amp;limit={$smarty.request.limit|default:$plim}"><img src="{$imgpath}/shop/navi_img.png" alt="" class="absmiddle" /> {#Shop_Topseller#}</a>
    <a class="{if isset($smarty.request.lowamount) && $smarty.request.lowamount == 1}shop_navi_first_active{else}shop_navi_first{/if}" href="index.php?p=shop&amp;action=showproducts&amp;page=1&amp;lowamount=1{if !empty($smarty.request.cid)}&amp;cid={$smarty.request.cid}{/if}&amp;limit={$smarty.request.limit|default:$plim}"><img src="{$imgpath}/shop/navi_img.png" alt="" class="absmiddle" /> {#Shop_lowProducts#}</a>
  </div>
</div>
