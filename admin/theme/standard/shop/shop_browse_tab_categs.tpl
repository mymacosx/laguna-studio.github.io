<div class="shop_subcateg">
  <table width="100%" cellpadding="0" cellspacing="1">
    <tr>
      {foreach from=$sub_categs item=subcateg}
        {assign var=break_count value=$break_count+1}
        <td style="text-align: center;">
          {if $subcateg->Bild_Kategorie}
            <a class="shop_subcategs stip" title="{$subcateg->Name|tooltip}" href="index.php?p=shop&amp;action=showproducts&amp;cid={$subcateg->catid}&amp;page=1&amp;limit={$smarty.request.limit|default:$plim}&amp;t={$subcateg->Name|translit}"><img style="margin-bottom: 5px" src="uploads/shop/icons_categs/{$subcateg->Bild_Kategorie}" alt="" /></a>
            {/if}
            {if !$subcateg->Bild_Kategorie}
            <h3><a class="shop_subcategs" href="index.php?p=shop&amp;action=showproducts&amp;cid={$subcateg->catid}&amp;page=1&amp;limit={$smarty.request.limit|default:$plim}&amp;t={$subcateg->Name|translit}">{$subcateg->Name}{* ({$subcateg->NumCount})*}</a></h3>
            {/if}
        </td>
        {if $break_count % 3 == 0}
        </tr>
        <tr>
        {/if}
      {/foreach}
    </tr>
  </table>
</div>
