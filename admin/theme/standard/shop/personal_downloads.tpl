<div class="padding5">
  <div class="box_innerhead">{#Shop_personalDownloads#}</div>
  {if !$exists}
    <div class="h2">{#Error#}</div>
    {#Shop_personalDownloadsE#}
  {else}
    <div class="infobox">
      <table width="100%" border="0" cellspacing="0" cellpadding="5">
        <tr>
          <td class="iter_head" width="1">&nbsp;</td>
          <td class="iter_head"><strong>{#Shop_mydownloads_filename#}</strong></td>
          <td class="iter_head"><strong>{#Description#}</strong></td>
          <td class="iter_head"><strong>{#GlobalSize#}</strong></td>
          <td class="iter_head"><strong>{#AddedOn#}</strong></td>
        </tr>
        {foreach from=$downloads item=d}
          <tr class="{cycle name='myo' values='iter_first,iter_second'}">
            <td width="1">
              {if $d->not_exists != 1}
                <a href="index.php?p=misc&amp;do=mypersonaldownloads&amp;oid={$smarty.get.oid}&amp;dl=1&amp;id={$d->Id}">
                {/if}
                <img class="absmiddle" src="{$imgpath}/shop/download_xsmall.png" alt="" border="0" />
                {if $d->not_exists != 1}
                </a>
              {/if}
            </td>
            <td>
              {if $d->not_exists == 1}
                <span style="text-decoration: line-through">{$d->Datei|slice: 50: '...'}</span>
              {else}
                <a href="index.php?p=misc&amp;do=mypersonaldownloads&amp;oid={$smarty.get.oid}&amp;dl=1&amp;id={$d->Id}">{$d->Datei|slice: 45: '...'}</a>
              {/if}
            </td>
            <td>{$d->Beschreibung}</td>
            <td>{$d->size}</td>
            <td>{$d->Datum|date_format: '%d.%m.%Y'}</td>
          </tr>
        {/foreach}
      </table>
    </div>
  {/if}
</div>
