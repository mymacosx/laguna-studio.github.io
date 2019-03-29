<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="{$langcode}" lang="{$langcode}" dir="ltr">
<head>
{include file="$incpath/other/header_head.tpl"}
{include file="$incpath/other/header_scripts.tpl"}
</head>
<body>
  {result type='script' format='file' position='body_start'} {* вывод файлов скриптов *}
  {result type='script' format='code' position='body_start'} {* вывод кода скриптов *}
  {result type='code' format='code' position='body_start'}   {* вывод кода *}

  <div id="body">
    <div class="body_padding">
      <div id="page_main">
        <div class="quicknavicontainer">{$quicknavi}</div>
        <div class="langchooser">
          {$langchooser}
          {if empty($langchooser)}
            {include file="$incpath/navi/mini_nav.tpl"}
          {/if}
        </div>
        <div class="menuline">&nbsp;</div>
        <div id="startcontentcontents">
          {if get_active('shop')}
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td id="header_shop"><a href="index.php?area={$area}"><img id="logo_shop" src="{$imgpath}/page/logo.png" alt="" /></a></td>
                <td id="header_basket" valign="top">{$basket_small}</td>
              </tr>
            </table>
          {else}
            <div id="header"><a href="index.php?area={$area}"><img id="logo" src="{$imgpath}/page/logo.png" alt="" /></a></div>
            {/if}
          <div style="clear: both"></div>
          <div id="contents_left">
            <div class="leftright_content">
              <div id="leftnavi">
                {$shop_search_small}
                {$shop_navigation}
                {include file="$incpath/shop/basket_saved_small.tpl"}
                {$curreny_selector}
                {$ShopInfoPanel}
                {$small_topseller}
                {$status_legend}
                {$payment_images}
                {include file="$incpath/shop/small_shipper.tpl"}
                {include file="$incpath/other/outlinks.tpl"}
              </div>
            </div>
          </div>
          <div id="contents_middle_2colums">
            <div class="shop_content">
              <div class="location">
                {$headernav}
              </div>
              {$content}
            </div>
          </div>
          <div class="clear"></div>
        </div>
        <div class="foot"> {#copyright_text#} | {version} | <a href="index.php?p=imprint">{#Imprint#}</a> </div>
      </div>
    </div>
  </div>
  {include file="$incpath/other/google.tpl"}

  {result type='code' format='code' position='body_end'}   {* вывод кода *}
  {result type='script' format='file' position='body_end'} {* вывод файлов скриптов *}
  {result type='script' format='code' position='body_end'} {* вывод кода скриптов *}
</body>
</html>
