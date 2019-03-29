<div class="box_innerhead">{#Downloads#}</div>
{if $Entries}
  {include file="$incpath/downloads/search.tpl"}
{else}
  {include file="$incpath/downloads/search.tpl"}
  <div class="box_data h3">{#Links_SNotFound#}</div>
{/if}
{$Results}
