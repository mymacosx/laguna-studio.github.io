{if empty($smarty.request.noframes)}
<script type="text/javascript">
<!-- //
{if $admin_settings.Navi_Anime == 1}
$(document).ready(function() {
    toggleCookie('navielements', 'left_open', 30, '{$basepath}');
});
{/if}
ddaccordion.init({
    headerclass: 'submenuheader',
    contentclass: 'submenu',
    revealtype: 'click',
    mouseoverdelay: 200,
    collapseprev: true,
    defaultexpanded: [],
    onemustopen: false,
    animatedefault:false,
    persiststate: true,
    toggleclass: ['', ''],
    togglehtml: ['suffix', '<img src="{$imgpath}/plus.gif" class="statusicon" />', '<img src="{$imgpath}/minus.gif" class="statusicon" />'],
    animatespeed: 'fast',
    oninit: function(headers, expandedindices) { },
    onopenclose: function(header, index, state, isuseractivated) { }
});
//-->
</script>

<td valign="top" class="left" id="left_open">
  <div class="glossymenu">
    {include file="$incpath/navielements/settings.tpl"}
    {include file="$incpath/navielements/stats.tpl"}
    {include file="$incpath/navielements/theme.tpl"}
    {include file="$incpath/navielements/seo.tpl"}
    {include file="$incpath/navielements/shop.tpl"}
    {include file="$incpath/navielements/manufacturer.tpl"}
    {include file="$incpath/navielements/forums.tpl"}
    {include file="$incpath/navielements/usergroups.tpl"}
    {include file="$incpath/navielements/users.tpl"}
    {include file="$incpath/navielements/content.tpl"}
    {include file="$incpath/navielements/news.tpl"}
    {include file="$incpath/navielements/articles.tpl"}
    {include file="$incpath/navielements/cheats.tpl"}
    {include file="$incpath/navielements/linksdownloads.tpl"}
    {include file="$incpath/navielements/gallery.tpl"}
    {include file="$incpath/navielements/roadmap.tpl"}
    {include file="$incpath/navielements/media.tpl"}
    {include file="$incpath/navielements/insert.tpl"}
    {include file="$incpath/navielements/codewidgets.tpl"}
    {include file="$incpath/navielements/navi.tpl"}
    {include file="$incpath/navielements/contact.tpl"}
    {include file="$incpath/navielements/newsletter.tpl"}
    {include file="$incpath/navielements/products.tpl"}
    {include file="$incpath/navielements/polls.tpl"}
    {include file="$incpath/navielements/banners.tpl"}
    {include file="$incpath/navielements/faq.tpl"}
    {include file="$incpath/navielements/other.tpl"}
    {include file="$incpath/settings/navi_modul.tpl"}
  </div>
</td>
{/if}
