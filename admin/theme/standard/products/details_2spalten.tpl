<div class="box_innerhead">{#Products#}</div>
<div class="links_list_title">
  <h3>{$res.Name|sanitize}</h3>
</div>
<table width="100%" cellspacing="0" cellpadding="0">
  <tr>
    <td valign="top">{$res.Inhalt}</td>
    <td width="25" valign="top">&nbsp;</td>
    <td width="140" valign="top">
      {if !empty($res.Bild) && $smarty.request.artpage<10}
        <img class="links_list_img" src="uploads/products/{$res.Bild}" alt="" />
      {/if}
      <div class="download_link_infbox">
        {if $res.ManLink}
          <div class="download_link_infheader">{#Manufacturer#}</div>
          {$res.ManLink}
        {/if}
        {if $res.PubLink}
          <div class="download_link_infheader">{#Products_publisher#}</div>
          {$res.PubLink}
        {/if}
        {if $res.Genre}
          <div class="download_link_infheader">{#Global_Categ#}</div>
          {$res.Genre}
        {/if}
        {if $res.Datum_Veroffentlichung>1}
          <div class="download_link_infheader">{#Date#}</div>
          {$res.Datum_Veroffentlichung|date_format: $lang.DateFormatSimple}
        {/if}
        {if !empty($res.Preis)}
          <div class="download_link_infheader">{#Products_price#}</div>
          {$res.Preis|sanitize}
        {/if}
        {if $res.Adresse}
          <div class="download_link_infheader">{#Imprint#}</div>
          {$res.Adresse|sslash}
        {/if}
        {if $res.Shopurl}
          <div class="download_link_infheader">{#Products_buyat#}</div>
          <a href="{$res.Shopurl|sanitize}" target="_blank">{$res.Shop|sanitize}</a>
        {/if}
        {if $ProductLinks}
          <div class="download_link_infheader">{#Products_links#}</div>
          {foreach from=$ProductLinks item=a}
            <a href="{$a->Link}" target="_blank">{$a->Name}</a>
            <br />
          {/foreach}
        {/if}
        {if $res.Wertung && !empty($RatingUrl)}
          <div class="download_link_infheader">{#Rating_Rating#}</div>
          <input name="starrate_x" type="radio" value="1" class="star" disabled="disabled" {if $res.Wertung == 1}checked="checked"{/if} />
          <input name="starrate_x" type="radio" value="2" class="star" disabled="disabled" {if $res.Wertung == 2}checked="checked"{/if} />
          <input name="starrate_x" type="radio" value="3" class="star" disabled="disabled" {if $res.Wertung == 3}checked="checked"{/if} />
          <input name="starrate_x" type="radio" value="4" class="star" disabled="disabled" {if $res.Wertung == 4}checked="checked"{/if} />
          <input name="starrate_x" type="radio" value="5" class="star" disabled="disabled" {if $res.Wertung == 5}checked="checked"{/if} />
          <br style="clear: both" />
        {/if}
      </div>
    </td>
  </tr>
</table>
<br style="clear: both" />
{if !empty($article_pages)}
  <br />
  {$article_pages}
{/if}
<br style="clear: both" />
{$IncludedGalleries|default:''}
{$RatingForm|default:''}
{$GetComments|default:''}
