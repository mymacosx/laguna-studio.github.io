{if perm('shop') && admin_active('shop')}
  <a class="menuitem submenuheader" href="#"><img class="absmiddle" src="{$imgpath}/shoppingcart.png" alt="" /> {#Global_Shop#}</a>
  <div class="submenu">
    <ul>
      {if perm('shop_settings')}
        <li><a class="{if isset($smarty.request.sub) && $smarty.request.sub == 'settings'}nav_subs_active{else}nav_subs{/if}" href="index.php?do=shop&amp;sub=settings">{#Global_Settings#}</a></li>
        <li><a class="{if isset($smarty.request.sub) && $smarty.request.sub == 'regions'}nav_subs_active{else}nav_subs{/if}" href="index.php?do=shop&amp;sub=regions">{#Settings_countries_title#}</a></li>
        <li><a class="{if isset($smarty.request.sub) && $smarty.request.sub == 'infomsg'}nav_subs_active{else}nav_subs{/if}" href="index.php?do=shop&amp;sub=infomsg">{#ShopInfoM#}</a></li>
        <li><a class="{if isset($smarty.request.sub) && $smarty.request.sub == 'shopinfos'}nav_subs_active{else}nav_subs{/if}" href="index.php?do=shop&amp;sub=shopinfos">{#Shop_Infos#}</a></li>
        {/if}
        {if perm('edit_order')}
        <li><a title="" class="{if isset($smarty.request.sub) && $smarty.request.sub == 'orders'}nav_subs_active{else}nav_subs{/if}" href="index.php?do=shop&amp;sub=orders">{#Shop_ordersS#}</a></li>
        {/if}
        {if perm('shop_settings')}
        <li><a title="{#Shop_showmoney_title#}" class="colorbox {if isset($smarty.request.sub) && $smarty.request.sub == 'showmoney'}nav_subs_active{else}nav_subs{/if}" href="index.php?do=shop&amp;sub=showmoney&amp;noframes=1">{#Shop_showmoney_title#}</a></li>
        {/if}
        {if perm('shop_articleedit')}
        <li><a class="{if isset($smarty.request.sub) && $smarty.request.sub == 'articles'}nav_subs_active{else}nav_subs{/if}" href="index.php?do=shop&amp;sub=articles">{#Shop_articlesS#}</a></li>
        {/if}
        {if perm('shop_articlenew')}
        <li><a title="{#Shop_articles_addnew#}" class="colorbox {if isset($smarty.request.sub) && $smarty.request.sub == 'new'}nav_subs_active{else}nav_subs{/if}" href="index.php?do=shop&amp;sub=new&amp;noframes=1">{#Shop_articles_addnew#}</a></li>
        {/if}
        {if perm('shop_catdeletenew')}
        <li><a class="{if isset($smarty.request.sub) && $smarty.request.sub == 'categories'}nav_subs_active{else}nav_subs{/if}" href="index.php?do=shop&amp;sub=categories">{#Global_Categories#}</a></li>
        {/if}
        {if perm('shop_addons')}
        <li><a class="{if isset($smarty.request.sub) && $smarty.request.sub == 'categories_addons'}nav_subs_active{else}nav_subs{/if}" href="index.php?do=shop&amp;sub=categories_addons">{#Shop_CategoriesAddons#}</a></li>
        {/if}
        {if perm('shop_paymentmethods')}
        <li><a class="{if isset($smarty.request.sub) && $smarty.request.sub == 'paymentmethods'}nav_subs_active{else}nav_subs{/if}" href="index.php?do=shop&amp;sub=paymentmethods">{#Shop_payment_title#}</a></li>
        {/if}
        {if perm('shop_shipper')}
        <li><a class="{if isset($smarty.request.sub) && $smarty.request.sub == 'shipper'}nav_subs_active{else}nav_subs{/if}" href="index.php?do=shop&amp;sub=shipper">{#Shop_shipper_title#}</a></li>
        {/if}
        {if perm('shop_settings')}
        <li><a class="{if isset($smarty.request.sub) && $smarty.request.sub == 'tracking'}nav_subs_active{else}nav_subs{/if}" href="index.php?do=shop&amp;sub=tracking">{#Shop_Tracking#}</a></li>
        {/if}
        {if perm('shop_taxes')}
        <li><a class="{if isset($smarty.request.sub) && $smarty.request.sub == 'taxes'}nav_subs_active{else}nav_subs{/if}" href="index.php?do=shop&amp;sub=taxes">{#Shop_taxes_title#}</a></li>
        {/if}
        {if perm('shop_shippingready')}
        <li><a class="{if isset($smarty.request.sub) && $smarty.request.sub == 'shippingready'}nav_subs_active{else}nav_subs{/if}" href="index.php?do=shop&amp;sub=shippingready">{#Shop_shippingready_title#}</a></li>
        {/if}
        {if perm('shop_availability')}
        <li><a class="{if isset($smarty.request.sub) && $smarty.request.sub == 'availabilities'}nav_subs_active{else}nav_subs{/if}" href="index.php?do=shop&amp;sub=availabilities">{#Shop_availabilities_title#}</a></li>
        {/if}
        {if perm('shop_couponcodes')}
        <li><a class="{if isset($smarty.request.sub) && $smarty.request.sub == 'couponcodes'}nav_subs_active{else}nav_subs{/if}" href="index.php?do=shop&amp;sub=couponcodes">{#Shop_couponcodes_title#}</a></li>
        {/if}
        {if perm('shop_units')}
        <li><a class="{if isset($smarty.request.sub) && $smarty.request.sub == 'units'}nav_subs_active{else}nav_subs{/if}" href="index.php?do=shop&amp;sub=units">{#Shop_units_title#}</a></li>
        {/if}
        {if perm('shop_groupsettings')}
        <li><a class="{if isset($smarty.request.sub) && $smarty.request.sub == 'groupsettings'}nav_subs_active{else}nav_subs{/if}" href="index.php?do=shop&amp;sub=groupsettings">{#Shop_groupsettings#}</a></li>
        {/if}
        {if perm('shop_settings')}
        <li><a class="{if isset($smarty.request.do) && $smarty.request.do == 'shopimport'}nav_subs_active{else}nav_subs{/if}" href="index.php?do=shopimport">{#Import_art#}</a></li>
        <li><a class="{if isset($smarty.request.sub) && $smarty.request.sub == 'yamarket'}nav_subs_active{else}nav_subs{/if}" href="index.php?do=shop&amp;sub=yamarket">{#YaMarket#}</a></li>
        {/if}
    </ul>
  </div>
{/if}
