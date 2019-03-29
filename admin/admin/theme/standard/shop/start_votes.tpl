{if perm('shop') && admin_active('shop') && !empty($votes)}
<script type="text/javascript">
<!-- //
$(document).ready(function() {
    toggleCookie('votes_navi', 'votes_open', 30, '{$basepath}');
});
//-->
</script>

<div class="header">
  <div id="votes_navi" class="navi_toggle"><img class="absmiddle" src="{$imgpath}/toggle.png" alt="" /></div>
  <img class="absmiddle" src="{$imgpath}/votes.png" alt="" /> {#ShopStartVotes#}
</div>
<div id="votes_open" class="sysinfos">
  <div class="maintable">
    <table width="100%" border="0" cellspacing="0" cellpadding="3">
      {foreach from=$votes item=o}
        <tr class="{cycle values='second,first'}">
          <td width="110">
            {#ShopNewVotes#}: <strong>{$o.count}</strong>
          </td>
          <td>
            <a class="colorbox stip" title="{$lang.Shop_articles_edit}" href="index.php?do=shop&amp;sub=edit_article&amp;id={$o.Produkt}&amp;noframes=1">{$o.Titel|slice: 60: '...'|sanitize}</a>
          </td>
          <td width="20" nowrap="nowrap">
            <a class="colorbox stip" title="{$lang.Shop_prodvotes}" href="index.php?do=shop&amp;sub=prodvotes&amp;id={$o.Produkt}&amp;name={$o.Titel|sanitize}&amp;noframes=1"><img src="{$imgpath}/edit.png" alt="" border="0" /></a>
          </td>
        </tr>
      {/foreach}
    </table>
  </div>
</div>
{/if}
