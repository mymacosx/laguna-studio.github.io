<div class="box_innerhead">{#Sitemap#}</div>
<ul>
  <li><a href="index.php?p=sitemap&amp;action=full&amp;area={$area}" title="{#SitemapFull#}">{#SitemapFull#}</a></li>
  <li><a href="index.php?p=sitemap&amp;area={$area}" title="{#Sitemap#}">{#Sitemap#}</a></li>
  <li><a href="index.php?p=imprint" title="{#Sitemap#}">{#Imprint#}</a></li>
    {if get_active('guestbook')}
    <li><a href="index.php?p=guestbook&amp;area={$area}" title="{#Guestbook_t#}">{#Guestbook_t#}</a></li>
    {/if}
    {if get_active('newsletter')}
    <li><a href="index.php?p=newsletter&amp;area={$area}" title="{#Newsletter#}">{#Newsletter#}</a></li>
    {/if}
    {if get_active('calendar')}
    <li><a href="index.php?p=calendar&amp;area={$area}" title="{#Calendar#}">{#Calendar#}</a></li>
    {/if}
    {if get_active('content')}
    <li><a href="index.php?area={$area}" title="{#PageStatic#}">{#PageStatic#}</a></li>
    <ul>
      {foreach from=$content_kategorien item=cont_cat}
        <li><a href="index.php?area={$cont_cat->Sektion}" title="{$cont_cat->Name|sslash}">{$cont_cat->Name|sslash}</a></li>
        <ul>
          {foreach from=$content item=cont}
            {if $cont_cat->Id == $cont->Kategorie}
              <li><a href="index.php?p=content&amp;id={$cont->Id}&amp;name={$cont->Titel|translit}&amp;area={$cont->Sektion}" title="{$cont->Titel|sslash|sanitize}">{$cont->Titel|sslash|sanitize}</a></li>
              {/if}
            {/foreach}
        </ul>
      {/foreach}
    </ul>
  {/if}
  {if get_active('News')}
    <li><a href="index.php?p=newsarchive&amp;area={$area}" title="{#Newsarchive#}">{#Newsarchive#}</a></li>
    <ul>
      {foreach from=$news_kategorie item=news_cat}
        <li><a href="index.php?p=newsarchive&amp;area={$area}&amp;catid={$news_cat->Id}&amp;name={$news_cat->Name|translit}" title="{$news_cat->Name|sslash|sanitize}">{$news_cat->Name|sslash|sanitize}</a></li>
        <ul>
          {foreach from=$news item=newsid}
            {if $news_cat->Id == $newsid->Kategorie}
              <li><a href="index.php?p=news&amp;area={$newsid->Sektion}&amp;newsid={$newsid->Id}&amp;name={$newsid->Titel|translit}" title="{$newsid->Titel|sslash|sanitize}">{$newsid->Titel|sslash|sanitize}</a></li>
              {/if}
            {/foreach}
        </ul>
      {/foreach}
    </ul>
  {/if}
  {if get_active('articles')}
    <li><a href="index.php?p=articles&amp;area={$area}" title="{#Gaming_articles#}">{#Gaming_articles#}</a></li>
    <ul>
      {foreach from=$artikel_kategorie item=art_cat}
        <li><a href="index.php?p=articles&amp;area={$art_cat->Sektion}&amp;catid={$art_cat->Id}&amp;name={$art_cat->Name|translit}" title="{$art_cat->Name|sslash|sanitize}">{$art_cat->Name|sslash|sanitize}</a></li>
        <ul>
          {foreach from=$artikel item=art}
            {if $art_cat->Id == $art->Kategorie}
              <li><a href="index.php?p=articles&amp;area={$art->Sektion}&amp;action=displayarticle&amp;id={$art->Id}&amp;name={$art->Titel|translit}" title="{$art->Titel|sslash|sanitize}">{$art->Titel|sslash|sanitize}</a></li>
              {/if}
            {/foreach}
        </ul>
      {/foreach}
    </ul>
  {/if}
  {if get_active('roadmap')}
    <li><a href="index.php?p=roadmap&amp;area={$area}" title="{#Roadmap#}">{#Roadmap#}</a></li>
    <ul>
      {foreach from=$roadmap item=roadmaps}
        <li><a href="index.php?p=roadmap&amp;area={$area}">{$roadmaps->Name|sslash|sanitize}</a></li>
        <ul>
          <li><a href="index.php?p=roadmap&amp;action=display&amp;rid={$roadmaps->Id}&amp;closed=0&amp;area={$area}&amp;name={$roadmaps->Name|translit}" title="{$roadmaps->Name|sslash|sanitize}">{#OpenTickets#} - {$roadmaps->Name|sslash|sanitize}</a></li>
          <li><a href="index.php?p=roadmap&amp;action=display&amp;rid={$roadmaps->Id}&amp;closed=1&amp;area={$area}&amp;name={$roadmaps->Name|translit}" title="{$roadmaps->Name|sslash|sanitize}">{#ClosedTickets#} - {$roadmaps->Name|sslash|sanitize}</a></li>
        </ul>
      {/foreach}
    </ul>
  {/if}
  {if get_active('faq')}
    <li><a href="index.php?p=faq&amp;area={$area}" title="{#Faq#}">{#Faq#}</a></li>
    <ul>
      {foreach from=$faq_kategorie item=faq_c}
        {if $faq_c->Parent_Id == 0}
          <li><a href="index.php?p=faq&amp;action=display&amp;faq_id={$faq_c->Id}&amp;area={$area}&amp;name={$faq_c->Name|translit}" title="{$faq_c->Name|sslash|sanitize}">{$faq_c->Name|sslash|sanitize}</a></li>
          <ul>
            {foreach from=$faq_kategorie item=faq_d}
              {if $faq_c->Id == $faq_d->Parent_Id}
                <li><a href="index.php?p=faq&amp;action=display&amp;faq_id={$faq_d->Id}&amp;area={$area}&amp;name={$faq_d->Name|translit}" title="{$faq_d->Name|sslash|sanitize}">{$faq_d->Name|sslash|sanitize}</a></li>
                  {foreach from=$faq_kategorie item=faq_e}
                    {if $faq_d->Id == $faq_e->Parent_Id}
                    <li><a href="index.php?p=faq&amp;action=display&amp;faq_id={$faq_e->Id}&amp;area={$area}&amp;name={$faq_e->Name|translit}" title="{$faq_e->Name|sslash|sanitize}">{$faq_e->Name|sslash|sanitize}</a></li>
                    {/if}
                  {/foreach}
                {/if}
              {/foreach}
          </ul>
        {/if}
      {/foreach}
    </ul>
  {/if}
  {if get_active('poll')}
    <li><a href="index.php?p=poll&amp;area={$area}" title="{#Poll_Name#}">{#Poll_Name#}</a></li>
    <ul>
      <li><a href="index.php?p=poll&amp;action=archive&amp;area={$area}" title="{#Poll_Archive#}">{#Poll_Archive#}</a></li>
      <ul>
        {foreach from=$umfrage item=poll}
          <li><a href="index.php?p=poll&amp;id={$poll->Id}&amp;name={$poll->Titel|translit}&amp;area={$poll->Sektion}" title="{$poll->Titel|sslash|sanitize}">{$poll->Titel|sslash|sanitize}</a></li>
          {/foreach}
      </ul>
    </ul>
  {/if}
  {if get_active('links')}
    <li><a href="index.php?p=links&amp;area={$area}" title="{#Links#}">{#Links#}</a></li>
    <ul>
      {foreach from=$links_kategorie item=links_cat}
        <li><a href="index.php?p=links&amp;area={$links_cat->Sektion}&amp;categ={$links_cat->Id}&amp;name={$links_cat->Name|translit}" title="{$links_cat->Name|sslash|sanitize}">{$links_cat->Name|sslash|sanitize}</a></li>
        <ul>
          {foreach from=$links item=linc}
            {if $links_cat->Id == $linc->Kategorie}
              <li><a href="index.php?p=links&amp;action=showdetails&amp;area={$linc->Sektion}&amp;categ={$linc->Kategorie}&amp;id={$linc->Id}&amp;name={$linc->Name|translit}" title="{$linc->Name|sslash|sanitize}">{$linc->Name|sslash|sanitize}</a></li>
              {/if}
            {/foreach}
        </ul>
      {/foreach}
    </ul>
  {/if}
  {if get_active('cheats')}
    <li><a href="index.php?p=cheats&amp;area={$area}" title="{#Gaming_cheats#}">{#Gaming_cheats#}</a></li>
    <ul>
      {foreach from=$plattformen item=plat}
        <li><a href="index.php?p=cheats&amp;area={$plat->Sektion}&amp;plattform={$plat->Id}&amp;name={$plat->Name|translit}" title="{$plat->Name|sslash|sanitize}">{$plat->Name|sslash|sanitize}</a></li>
        <ul>
          {foreach from=$cheats item=cheat}
            {if $plat->Id == $cheat->Plattform}
              <li><a href="index.php?p=cheats&amp;action=showcheat&amp;area={$cheat->Sektion}&amp;plattform={$cheat->Plattform}&amp;id={$cheat->Id}&amp;name={$cheat->Name|translit}" title="{$cheat->Name|sslash|sanitize}">{$cheat->Name|sslash|sanitize}</a></li>
              {/if}
            {/foreach}
        </ul>
      {/foreach}
    </ul>
  {/if}
  {if get_active('downloads')}
    <li><a href="index.php?p=downloads&amp;area={$area}" title="{#Downloads#}">{#Downloads#}</a></li>
    <ul>
      {foreach from=$downloads_kategorie item=down_cat}
        <li><a href="index.php?p=downloads&amp;area={$down_cat->Sektion}&amp;categ={$down_cat->Id}&amp;name={$down_cat->Name|translit}" title="{$down_cat->Name|sslash|sanitize}">{$down_cat->Name|sslash|sanitize}</a></li>
        <ul>
          {foreach from=$downloads item=down}
            {if $down_cat->Id == $down->Kategorie}
              <li><a href="index.php?p=downloads&amp;action=showdetails&amp;area={$down->Sektion}&amp;categ={$down->Kategorie}&amp;id={$down->Id}&amp;name={$down->Name|translit}" title="{$down->Name|sslash|sanitize}">{$down->Name|sslash|sanitize}</a></li>
              {/if}
            {/foreach}
        </ul>
      {/foreach}
    </ul>
  {/if}
  {if get_active('gallery')}
    <li><a href="index.php?p=gallery&amp;area={$area}" title="{#Gallery_Name#}">{#Gallery_Name#}</a></li>
    <ul>
      {foreach from=$galerie_kategorien item=gal_cat}
        <li><a href="index.php?p=gallery&amp;action=showincluded&amp;categ={$gal_cat->Id}&amp;name={$gal_cat->Name|translit}&amp;area={$gal_cat->Sektion}" title="{$gal_cat->Name|sslash|sanitize}">{$gal_cat->Name|sslash|sanitize}</a></li>
        <ul>
          {foreach from=$galerie item=gal}
            {if $gal_cat->Id == $gal->Kategorie}
              <li><a href="index.php?p=gallery&amp;action=showgallery&amp;id={$gal->Id}&amp;categ={$gal->Kategorie}&amp;name={$gal->Name|translit}&amp;area={$gal->Sektion}" title="{$gal->Name|sslash|sanitize}">{$gal->Name|sslash|sanitize}</a></li>
              {/if}
            {/foreach}
        </ul>
      {/foreach}
    </ul>
  {/if}
  {if get_active('forums')}
    <li><a href="index.php?p=showforums" title="{#Forums_Title#}">{#Forums_Title#}</a></li>
    <ul>
      {foreach from=$f_category item=f_cat}
        <li><a href="index.php?p=showforums&amp;cid={$f_cat->id}&amp;t={$f_cat->title|translit}" title="{$f_cat->title|sslash|sanitize}">{$f_cat->title|sslash|sanitize}</a></li>
        <ul>
          {foreach from=$f_forum item=forum}
            {if $f_cat->id == $forum->category_id}
              <li><a href="index.php?p=showforum&amp;fid={$forum->id}&amp;t={$forum->title|translit}" title="{$forum->title|sslash|sanitize}">{$forum->title|sslash|sanitize}</a></li>
              <ul>
                {foreach from=$f_topic item=topic}
                  {if $forum->id == $topic->forum_id}
                    <li><a href="index.php?p=showtopic&amp;toid={$topic->id}&amp;fid={$topic->forum_id}&amp;t={$topic->title|translit}" title="{$topic->title|sslash|sanitize}">{$topic->title|sslash|sanitize}</a></li>
                    {/if}
                  {/foreach}
              </ul>
            {/if}
          {/foreach}
        </ul>
      {/foreach}
    </ul>
  {/if}
  {if get_active('products')}
    <li><a href="index.php?p=products&amp;area={$area}" title="{#Products#}">{#Products#}</a></li>
    <ul>
      {foreach from=$genre item=gen_cat}
        <li><a href="index.php?p=products&amp;area={$gen_cat->Sektion}" title="{$gen_cat->Name|sslash|sanitize}">{$gen_cat->Name|sslash|sanitize}</a></li>
        <ul>
          {foreach from=$produkte item=produkt}
            {if $gen_cat->Id == $produkt->Genre}
              <li><a href="index.php?p=products&amp;area={$produkt->Sektion}&amp;action=showproduct&amp;id={$produkt->Id}&amp;name={$produkt->Name|translit}" title="{$produkt->Name|sslash|sanitize}">{$produkt->Name|sslash|sanitize}</a></li>
              {/if}
            {/foreach}
        </ul>
      {/foreach}
    </ul>
  {/if}
  {if get_active('manufacturer')}
    <li><a href="index.php?p=manufacturer&amp;area={$area}" title="{#Manufacturers#}">{#Manufacturers#}</a></li>
    <ul>
      {foreach from=$hersteller item=manufacturer}
        <li><a href="index.php?p=manufacturer&amp;area={$area}&amp;action=showdetails&amp;id={$manufacturer->Id}&amp;name={$manufacturer->Name|translit}" title="{$manufacturer->Name|sslash|sanitize}">{$manufacturer->Name|sslash|sanitize}</a></li>
        {/foreach}
    </ul>
  {/if}
  {if get_active('shop')}
    <li><a href="index.php?p=shop&amp;area={$area}" title="{#Shop#}">{#Shop#}</a></li>
    <ul>
      {foreach from=$shop_kategorie item=shop_cat}
        <li><a href="index.php?p=shop&amp;action=showproducts&amp;cid={$shop_cat->Id}&amp;page=1&amp;limit=20&amp;t={$shop_cat->Name|translit}" title="{$shop_cat->Name|sanitize}">{$shop_cat->Name|sanitize}</a></li>
        <ul>
          {foreach from=$shop_produkte item=shop}
            {if $shop_cat->Id == $shop->Kategorie}
              <li><a href="index.php?p=shop&amp;action=showproduct&amp;id={$shop->Id}&amp;cid={$shop->Kategorie}&amp;pname={$shop->Titel|translit}" title="{$shop->Titel|sslash|sanitize}">{$shop->Titel|sslash|sanitize}</a></li>
              {/if}
            {/foreach}
        </ul>
      {/foreach}
    </ul>
  {/if}
</ul>
