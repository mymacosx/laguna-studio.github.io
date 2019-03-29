<div class="box_innerhead">{#Products#}</div>
<div class="links_list_title">
  <h3>{$res.Name|sanitize}</h3>
</div>
{if !empty($res.Bild) && $smarty.request.artpage<2}
  <img class="links_list_img" src="uploads/products/{$res.Bild}" align="right" alt="" />
{/if}
{$res.Inhalt} <br style="clear: both" />
{if $article_pages}
  <br />
  {$article_pages}
{/if} <br style="clear: both" />
<div class="download_link_infbox">
  <table width="100%" cellspacing="0" cellpadding="2">
    {if $res.ManLink}
      <tr>
        <td class="row_left" width="150">{#Manufacturer#}: &nbsp;</td>
        <td class="row_right">{$res.ManLink}</td>
      </tr>
    {/if}
    {if $res.PubLink}
      <tr>
        <td width="150" class="row_left">{#Products_publisher#}: &nbsp;</td>
        <td class="row_right">{$res.PubLink}</td>
      </tr>
    {/if}
    {if $res.Genre}
      <tr>
        <td class="row_left">{#Global_Categ#}: &nbsp;</td>
        <td class="row_right">{$res.Genre}</td>
      </tr>
    {/if}
    {if $res.Datum_Veroffentlichung>1}
      <tr>
        <td width="150" class="row_left">{#Date#}: &nbsp;</td>
        <td class="row_right">{$res.Datum_Veroffentlichung|date_format: $lang.DateFormatSimple}</td>
      </tr>
    {/if}
    {if !empty($res.Preis)}
      <tr>
        <td width="150" class="row_left">{#Products_price#}: &nbsp;</td>
        <td class="row_right">{$res.Preis|sanitize}</td>
      </tr>
    {/if}
    {if $res.Adresse}
      <tr>
        <td width="150" class="row_left">{#Imprint#}: &nbsp;</td>
        <td class="row_right">{$res.Adresse|sslash}</td>
      </tr>
    {/if}
    {if $res.Shopurl}
      <tr>
        <td width="150" class="row_left">{#Products_buyat#}: &nbsp;</td>
        <td class="row_right"><a href="{$res.Shopurl|sanitize}" target="_blank">{$res.Shop|sanitize}</a></td>
      </tr>
    {/if}
    {if $ProductLinks}
      <tr>
        <td width="150" valign="top" class="row_left">{#Products_links#}: &nbsp;</td>
        <td valign="top" class="row_right">
          {foreach from=$ProductLinks item=a}
            <a href="{$a->Link}" target="_blank">{$a->Name}</a>
            <br />
          {/foreach}
        </td>
      </tr>
    {/if}
    {if $res.Wertung && !empty($RatingUrl)}
      <tr>
        <td valign="top" class="row_left">{#Rating_Rating#}: </td>
        <td valign="top" class="row_right">
          <input name="starrate_x" type="radio" value="1" class="star" disabled="disabled" {if $res.Wertung == 1}checked="checked"{/if} />
          <input name="starrate_x" type="radio" value="2" class="star" disabled="disabled" {if $res.Wertung == 2}checked="checked"{/if} />
          <input name="starrate_x" type="radio" value="3" class="star" disabled="disabled" {if $res.Wertung == 3}checked="checked"{/if} />
          <input name="starrate_x" type="radio" value="4" class="star" disabled="disabled" {if $res.Wertung == 4}checked="checked"{/if} />
          <input name="starrate_x" type="radio" value="5" class="star" disabled="disabled" {if $res.Wertung == 5}checked="checked"{/if} />
        </td>
      </tr>
    {/if}
  </table>
</div>
{$IncludedGalleries|default:''}
{$RatingForm|default:''}
{$GetComments|default:''}
