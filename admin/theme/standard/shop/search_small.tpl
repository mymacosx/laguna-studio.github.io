{script file="$jspath/jsuggest.js" position='head'}
<script type="text/javascript">
<!-- //
$(document).ready(function() {
    $('#qs').suggest('{$baseurl}/lib/ajax.php?action=shop&key=' + Math.random(), {
        onSelect: function() {
            document.forms['shopssform'].submit();
        }
    });
});
togglePanel('navpanel_shopsearch', 'togglerboxes', 30, '{$basepath}');
//-->
</script>

<div class="round">
  <div class="opened" id="navpanel_shopsearch" title="{#Search#}">
    <div class="boxes_body">
      <form method="post" id="shopssform" name="shopssform" action="{$shop_search_small_action}">
        <input type="hidden" name="cid" value="0" />
        <input type="hidden" name="man" value="0" />
        <input name="shop_q" id="qs" type="text" class="input" style="width: 105px" value="{if  isset($smarty.request.shop_q) && $smarty.request.shop_q != 'empty'}{$smarty.request.shop_q|escape: html}{/if}" maxlength="150" />
        <input type="submit" class="shop_buttons_big" style="width: 50px" value="{#Search#}" />
        <input type="hidden" name="p" value="shop" />
        <input type="hidden" name="action" value="showproducts" />
      </form>
      <br />
      {#Arrow#}<a href="{$baseurl}/index.php?exts=1&amp;s=1&amp;area={$smarty.session.area}&amp;p=shop&amp;action=showproducts">{#ExtendedSearch#}</a>
      <br />
      <br />
      {#Arrow#}<a href="{$baseurl}/index.php?p=shop&amp;action=mylist">{#Shop_mylist#}</a>
      {if $loggedin}
        <br />
        {#Arrow#}<a href="" onclick="mergeProduct('','','{$baseurl}/','');return false;">{#Shop_mergeListsMy#}</a>
      {/if}
    </div>
  </div>
</div>
