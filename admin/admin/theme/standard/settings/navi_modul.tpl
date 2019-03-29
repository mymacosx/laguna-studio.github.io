{foreach from=$modul_navi item=modul}
  {assign var=tpl value=$modul.Modul}
  {include file="$tpl"}
{/foreach}
