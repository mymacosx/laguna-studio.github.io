<div class="box_innerhead">{$navi_title|default:"Навигация"}</div>
<ul>
  <li><a href="index.php?p=sitemap&amp;action=full&amp;area={$area}">{#SitemapFull#}</a></li>
  {assign var="ST" value=0}
  {assign var="SS" value=0}
  {foreach from=$SiteNavigation item=navi}{assign var="ST" value=$ST+1}
  <li>
    <a target="{$navi->target|default:'_self'}" href="{$navi->document|escape: "html"}">{$navi->title}</a>
  {if !count($navi->sub_navi)}
  </li>
  {/if}
  {if count($navi->sub_navi)}
    {assign var="ST" value=0}
    <ul>
      {foreach from=$navi->sub_navi item=sub_navi}
        {assign var="SS" value=$SS+1}
        <li><a target="{$sub_navi->target|default:'_self'}" href="{$sub_navi->document|escape: "html"}">{$sub_navi->title}</a>
        {if !count($sub_navi->sub_navi)}
        </li>
        {/if}
          {if count($sub_navi->sub_navi)}
            <ul>
              {foreach from=$sub_navi->sub_navi item=sub_sub_navi}
                <li><a target="{$sub_sub_navi->target|default:'_self'}" href="{$sub_sub_navi->document|escape: "html"}">{$sub_sub_navi->title}</a></li>
              {/foreach}
            </ul>
            </li>
          {/if}
      {/foreach}
     </ul>
    </li>
  {/if}
  {/foreach}
</ul>
