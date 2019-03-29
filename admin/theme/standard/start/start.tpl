{if $DisplayStartText != '0'}
  {$DisplayStartText}
{/if}
{if $OnlyStartText != 1}
  {$topcontent}
  {$toparticle}
  {$topnews}
  {$lastarticles}
  {$news}
  <br style="clear: both" />
  {$NewShopProducts}
  {$NewForumPosts}
  {$NewForumThreads}
  {$NewGalleries}
  {$NewProducts}
  {$NewCheats}
  {if get_active('new_links_downloads')}
    <table width="100%" cellspacing="0" cellpadding="0">
      <tr>
        <td width="50%" valign="top">{$NewDownloads}</td>
        <td width="30">&nbsp;&nbsp;&nbsp;</td>
        <td width="50%" valign="top">{$NewLinks}</td>
      </tr>
    </table>
  {/if}
{/if}
