<br />
{if get_active('whosonline')}
    <div class="infobox">
      <div class="h3"><img src="{$imgpath_forums}users.png" alt="" class="absmiddle" />&nbsp;{#Forums_WhosOnline#}</div>
      <br />
      {useronline}
    </div>
{/if}
<div class="infobox">
  <div class="h3"><img src="{$imgpath_forums}stats.png" alt="" class="absmiddle" />&nbsp;{#Forums_Stats#}</div>
  <br />
  {forumstats}
</div>
<div class="infobox">
  <div class="h3"><img src="{$imgpath_forums}birthday.png" alt="" class="absmiddle" />&nbsp;{#Birthdays_Today#}</div>
  <br />
  {birthdays}
</div>

