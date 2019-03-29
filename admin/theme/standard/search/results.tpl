{if $smarty.request.where == 'all'}
  {if $numall >= 1}
    {$countall_text} <br style="clear: both" />
    <div class="infobox"> {if $count_news >= 1}
      <div class="h3"><a href="index.php?q={$smarty.get.q}&amp;where=news&amp;p=search"><img class="absmiddle" src="{$imgpath_page}search_small.png" alt="" /></a>&nbsp;<a href="index.php?q={$smarty.get.q|urlencode}&amp;where=news&amp;p=search">{#Page_Search_RNews#}- {$count_news}</a></div>
      <br />
    {/if}
    {if $count_articles >= 1}
      <div class="h3"><a href="index.php?q={$smarty.get.q}&amp;where=articles&amp;p=search"><img class="absmiddle" src="{$imgpath_page}search_small.png" alt="" /></a>&nbsp;<a href="index.php?q={$smarty.get.q|urlencode}&amp;where=articles&amp;p=search">{#Page_Search_RArticles#}- {$count_articles}</a></div>
      <br />
    {/if}
    {if $count_content >= 1}
      <div class="h3"><a href="index.php?q={$smarty.get.q}&amp;where=content&amp;p=search"><img class="absmiddle" src="{$imgpath_page}search_small.png" alt="" /></a>&nbsp;<a href="index.php?q={$smarty.get.q|urlencode}&amp;where=content&amp;p=search">{#Page_Search_RContent#}- {$count_content}</a></div>
      <br />
    {/if}
    {if $count_faq >= 1}
      <div class="h3"><a href="index.php?q={$smarty.get.q}&amp;where=faq&amp;p=search"><img class="absmiddle" src="{$imgpath_page}search_small.png" alt="" /></a>&nbsp;<a href="index.php?q={$smarty.get.q|urlencode}&amp;where=faq&amp;p=search">{#Page_Search_RFaq#}- {$count_faq}</a></div>
      <br />
    {/if}
    {if $count_downloads >= 1}
      <div class="h3"><a href="index.php?q={$smarty.get.q}&amp;where=downloads&amp;p=search"><img class="absmiddle" src="{$imgpath_page}search_small.png" alt="" /></a>&nbsp;<a href="index.php?q={$smarty.get.q|urlencode}&amp;where=downloads&amp;p=search">{#Page_Search_RDownloads#}- {$count_downloads}</a></div>
      <br />
    {/if}
    {if $count_links >= 1}
      <div class="h3"><a href="index.php?q={$smarty.get.q}&amp;where=links&amp;p=search"><img class="absmiddle" src="{$imgpath_page}search_small.png" alt="" /></a>&nbsp;<a href="index.php?q={$smarty.get.q|urlencode}&amp;where=links&amp;p=search">{#Page_Search_RLinks#}- {$count_links}</a></div>
      <br />
    {/if}
    {if $count_galleries >= 1}
      <div class="h3"><a href="index.php?q={$smarty.get.q}&amp;where=gallery&amp;p=search"><img class="absmiddle" src="{$imgpath_page}search_small.png" alt="" /></a>&nbsp;<a href="index.php?q={$smarty.get.q|urlencode}&amp;where=gallery&amp;p=search">{#Page_Search_RGals#}- {$count_galleries}</a></div>
      <br />
    {/if}
    {if $count_shoparticles >= 1}
      <div class="h3"><a href="index.php?q={$smarty.get.q}&amp;where=shop&amp;p=search"><img class="absmiddle" src="{$imgpath_page}search_small.png" alt="" /></a>&nbsp;<a href="index.php?q={$smarty.get.q|urlencode}&amp;where=shop&amp;p=search">{#Page_Search_RShop#}- {$count_shoparticles}</a></div>
      <br />
    {/if}
    {if $count_products >= 1}
      <div class="h3"><a href="index.php?q={$smarty.get.q}&amp;where=products&amp;p=search"><img class="absmiddle" src="{$imgpath_page}search_small.png" alt="" /></a>&nbsp;<a href="index.php?q={$smarty.get.q|urlencode}&amp;where=products&amp;p=search">{#Page_Search_RProducts#}- {$count_products}</a></div>
      <br />
    {/if}
    {if $count_manufacturer >= 1}
      <div class="h3"><a href="index.php?q={$smarty.get.q}&amp;where=manufacturer&amp;p=search"><img class="absmiddle" src="{$imgpath_page}search_small.png" alt="" /></a>&nbsp;<a href="index.php?q={$smarty.get.q|urlencode}&amp;where=manufacturer&amp;p=search">{#Page_Search_RManufacturer#}- {$count_manufacturer}</a></div>
      <br />
    {/if}
    {if $count_cheats >= 1}
      <div class="h3"><a href="index.php?q={$smarty.get.q}&amp;where=cheats&amp;p=search"><img class="absmiddle" src="{$imgpath_page}search_small.png" alt="" /></a>&nbsp;<a href="index.php?q={$smarty.get.q|urlencode}&amp;where=cheats&amp;p=search">{#Page_Search_RCheats#}- {$count_cheats}</a></div>
      <br />
    {/if}
  </div>
{else}
  <div class="h3">{#Page_Search_Nothing#}</div>
{/if}
{else}
  {if get_active('cheats') && !empty($cheatitems) && $smarty.request.where == 'cheats'}
    <div class="box_innerhead">{#Page_Search_FoundIn#}{#Gaming_cheats#}</div>
    {foreach from=$cheatitems item=art}
      <div class="{cycle name='ne' values='srow_second,srow_first'}">
        <div class="h3">{$art->num}. &nbsp; <a class="title_result_search" href="index.php?p=cheats&amp;action=showcheat&amp;area={$art->Sektion}&amp;plattform={$art->Plattform}&amp;id={$art->Id}&amp;name={$art->Titel|translit}">{$art->Titel|sanitize}</a></div>
        <div class="search_allresults">
          {$art->words}
          <br />
          {$art->erg}
          <br />
        </div>
      </div>
    {/foreach}
    {if !empty($pages)}
      <br style="clear: both" />
      {$pages}
    {/if}
  {/if}
  {if get_active('manufacturer') && !empty($manufactureritems) && $smarty.request.where == 'manufacturer'}
    <div class="box_innerhead">{#Page_Search_FoundIn#} {#Manufacturers#}</div>
    {foreach from=$manufactureritems item=art}
      <div class="{cycle name='ne' values='srow_second,srow_first'}">
        <div class="h3">{$art->num}. &nbsp; <a class="title_result_search" href="index.php?p=manufacturer&amp;area={$art->Sektion}&amp;action=showdetails&amp;id={$art->Id}&amp;name={$art->Titel|translit}">{$art->Titel|sanitize}</a></div>
        <div class="search_allresults">
          {$art->words}
          <br />
          {$art->erg}
          <br />
        </div>
      </div>
    {/foreach}
    {if !empty($pages)}
      <br style="clear: both" />
      {$pages}
    {/if}
  {/if}
  {if get_active('products') && !empty($productitems) && $smarty.request.where == 'products'}
    <div class="box_innerhead">{#Page_Search_FoundIn#}{#Products#}</div>
    {foreach from=$productitems item=art}
      <div class="{cycle name='ne' values='srow_second,srow_first'}">
        <div class="h3">{$art->num}. &nbsp; <a class="title_result_search" href="index.php?p=products&amp;area={$art->Sektion}&amp;action=showproduct&amp;id={$art->Id}&amp;name={$art->Titel|translit}">{$art->Titel|sanitize}</a></div>
        <div class="search_allresults">
          {$art->words}
          <br />
          {$art->erg}
          <br />
        </div>
      </div>
    {/foreach}
    {if !empty($pages)}
      <br style="clear: both" />
      {$pages}
    {/if}
  {/if}
  {if get_active('shop') && !empty($shopitems) && $smarty.request.where == 'shop'}
    <div class="box_innerhead">{#Page_Search_FoundIn#}{#Shop#}</div>
    {foreach from=$shopitems item=art}
      <div class="{cycle name='ne' values='srow_second,srow_first'}">
        <div class="h3">{$art->num}. &nbsp; <a class="title_result_search" href="index.php?p=shop&amp;action=showproduct&amp;id={$art->Id|translit}&amp;cid={$art->Kategorie|translit}&amp;pname={$art->Titel|translit}">{$art->Titel|sanitize}</a></div>
        <div class="search_allresults">
          {$art->words}
          <br />
          {$art->erg}
          <br />
        </div>
      </div>
    {/foreach}
    {if !empty($pages)}
      <br style="clear: both" />
      {$pages}
    {/if}
  {/if}
  {if get_active('gallery') && !empty($galleryitems) && $smarty.request.where == 'gallery'}
    <div class="box_innerhead">{#Page_Search_FoundIn#}{#Links#}</div>
    {foreach from=$galleryitems item=art}
      <div class="{cycle name='ne' values='srow_second,srow_first'}">
        <div class="h3">{$art->num}. &nbsp; <a class="title_result_search" href="index.php?p=gallery&amp;action=showgallery&amp;id={$art->Id}&amp;categ={$art->Kategorie}&amp;name={$art->Titel|translit}&amp;area={$art->Sektion}">{$art->Titel|sanitize}</a></div>
        <div class="search_allresults">
          {$art->words}
          <br />
          {$art->erg}
          <br />
        </div>
      </div>
    {/foreach}
    {if !empty($pages)}
      <br style="clear: both" />
      {$pages}
    {/if}
  {/if}
  {if get_active('links') && !empty($linkitems) && $smarty.request.where == 'links'}
    <div class="box_innerhead">{#Page_Search_FoundIn#}{#Links#}</div>
    {foreach from=$linkitems item=art}
      <div class="{cycle name='ne' values='srow_second,srow_first'}">
        <div class="h3">{$art->num}. &nbsp; <a class="title_result_search" href="index.php?p=links&amp;action=showdetails&amp;area={$art->Sektion}&amp;categ={$art->Kategorie}&amp;id={$art->Id}&amp;name={$art->Titel|translit}">{$art->Titel|sanitize}</a></div>
        <div class="search_allresults">
          {$art->words}
          <br />
          {$art->erg}
          <br />
        </div>
      </div>
    {/foreach}
    {if !empty($pages)}
      <br style="clear: both" />
      {$pages}
    {/if}
  {/if}
  {if get_active('downloads') && !empty($downloaditems) && $smarty.request.where == 'downloads'}
    <div class="box_innerhead">{#Page_Search_FoundIn#}{#Downloads#}</div>
    {foreach from=$downloaditems item=art}
      <div class="{cycle name='ne' values='srow_second,srow_first'}">
        <div class="h3">{$art->num}. &nbsp; <a class="title_result_search" href="index.php?p=downloads&amp;action=showdetails&amp;area={$art->Sektion}&amp;categ={$art->Kategorie}&amp;id={$art->Id}&amp;name={$art->Titel|translit}">{$art->Titel|sanitize}</a></div>
        <div class="search_allresults">
          {$art->words}
          <br />
          {$art->erg}
          <br />
        </div>
      </div>
    {/foreach}
    {if !empty($pages)}
      <br style="clear: both" />
      {$pages}
    {/if}
  {/if}
  {if get_active('faq') && !empty($faqitems) && $smarty.request.where == 'faq'}
    <div class="box_innerhead">{#Page_Search_FoundIn#}{#Faq#}</div>
    {foreach from=$faqitems item=art}
      <div class="{cycle name='ne' values='srow_second,srow_first'}">
        <div class="h3">{$art->num}. &nbsp; <a class="title_result_search" href="index.php?p=faq&amp;action=display&amp;faq_id={$art->Kategorie}&amp;area={$art->Sektion}&amp;name={$art->Titel|translit}#faq{$art->Id}">{$art->Titel|sanitize}</a></div>
        <div class="search_allresults">
          {$art->words}
          <br />
          {$art->erg}
          <br />
        </div>
      </div>
    {/foreach}
    {if !empty($pages)}
      <br style="clear: both" />
      {$pages}
    {/if}
  {/if}
  {if get_active('News') && !empty($newsitems) && $smarty.request.where == 'news'}
    <div class="box_innerhead">{#Page_Search_FoundIn#}{#Newsarchive#}</div>
    {foreach from=$newsitems item=art}
      <div class="{cycle name='ne' values='srow_second,srow_first'}">
        <div class="h3">{$art->num}. &nbsp; <a class="title_result_search" href="index.php?p=news&amp;area={$art->Sektion}&amp;newsid={$art->Id}&amp;name={$art->Titel|translit}">{$art->Titel|sanitize}</a></div>
        <div class="search_allresults">
          {$art->words}
          <br />
          {$art->erg}
          <br />
        </div>
      </div>
    {/foreach}
    {if !empty($pages)}
      <br style="clear: both" />
      {$pages}
    {/if}
  {/if}
  {if get_active('articles') && !empty($articleitems) && $smarty.request.where == 'articles'}
    <div class="box_innerhead">{#Page_Search_FoundIn#}{#Gaming_articles#}</div>
    {foreach from=$articleitems item=art}
      <div class="{cycle name='ne' values='srow_second,srow_first'}">
        <div class="h3">{$art->num}. &nbsp; <a class="title_result_search" href="index.php?p=articles&amp;area={$art->Sektion}&amp;action=displayarticle&amp;id={$art->Id}&amp;name={$art->Titel|translit}">{$art->Titel|sanitize}</a></div>
        <div class="search_allresults">
          {$art->words}
          <br />
          {$art->erg}
          <br />
        </div>
      </div>
    {/foreach}
    {if !empty($pages)}
      <br style="clear: both" />
      {$pages}
    {/if}
  {/if}
  {if !empty($contentitems) && $smarty.request.where == 'content'}
    <div class="box_innerhead">{#Page_Search_FoundIn#}{#Page_Search_Content#}</div>
    {foreach from=$contentitems item=art}
      <div class="{cycle name='ne' values='srow_second,srow_first'}">
        <div class="h3">{$art->num}. &nbsp; <a class="title_result_search" href="index.php?p=content&amp;id={$art->Id}&amp;name={$art->Titel|translit}&amp;area={$art->Sektion}">{$art->Titel|sanitize}</a></div>
        <div class="search_allresults">
          {$art->words}
          <br />
          {$art->erg}
          <br />
        </div>
      </div>
    {/foreach}
    {if !empty($pages)}
      <br style="clear: both" />
      {$pages}
    {/if}
  {/if}
  {/if}
