<select class="input" name="where" style="width: 110px">
  <option value="all">{#Page_Search_All#}</option>
  {if get_active('News') && permission('news')}
    <option value="news" {if isset($smarty.get.where) && $smarty.get.where == 'news'}selected="selected" {/if}> - {#Newsarchive#}</option>
  {/if}
  {if get_active('articles') && permission('articles')}
    <option value="articles" {if isset($smarty.get.where) && $smarty.get.where == 'articles'}selected="selected" {/if}> - {#Gaming_articles#}</option>
  {/if}
  {if get_active('shop')}
    <option value="shop" {if isset($smarty.get.where) && $smarty.get.where == 'shop'}selected="selected" {/if}> - {#Shop#}</option>
  {/if}
  {if get_active('content')}
    <option value="content" {if isset($smarty.get.where) && $smarty.get.where == 'content'}selected="selected" {/if}> - {#Page_Search_Content#}</option>
  {/if}
  {if get_active('faq')}
    <option value="faq" {if isset($smarty.get.where) && $smarty.get.where == 'faq'}selected="selected" {/if}> - {#Faq#}</option>
  {/if}
  {if get_active('downloads') && permission('downloads')}
    <option value="downloads" {if isset($smarty.get.where) && $smarty.get.where == 'downloads'}selected="selected" {/if}> - {#Downloads#}</option>
  {/if}
  {if get_active('links') && permission('links')}
    <option value="links" {if isset($smarty.get.where) && $smarty.get.where == 'links'}selected="selected" {/if}> - {#Links#}</option>
  {/if}
  {if get_active('gallery') && permission('gallery')}
    <option value="gallery" {if isset($smarty.get.where) && $smarty.get.where == 'gallery'}selected="selected" {/if}> - {#Page_Search_Gallery#}</option>
  {/if}
  {if get_active('products') && permission('products')}
    <option value="products" {if isset($smarty.get.where) && $smarty.get.where == 'products'}selected="selected" {/if}> - {#Products#}</option>
  {/if}
  {if get_active('manufacturer') && permission('manufacturer')}
    <option value="manufacturer" {if isset($smarty.get.where) && $smarty.get.where == 'manufacturer'}selected="selected" {/if}> - {#Manufacturers#}</option>
  {/if}
  {if get_active('cheats') && permission('cheats')}
    <option value="cheats" {if isset($smarty.get.where) && $smarty.get.where == 'cheats'}selected="selected" {/if}> - {#Gaming_cheats#}</option>
  {/if}
</select>
