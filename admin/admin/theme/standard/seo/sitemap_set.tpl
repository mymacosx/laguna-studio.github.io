<div class="header">{#Sitemap#}</div>
<table width="100%" border="0" cellpadding="4" cellspacing="0">
  {foreach key=key from=$data item=map}
    {if $key == 1}
      {assign var=key value=""}
    {/if}
    <tr>
      <td class="row_left" width="400">{#KompSitemap#}</td>
      <td class="row_right"><a href="{$baseurl}/sitemap{$key}.xml.gz">{$baseurl}/sitemap{$key}.xml.gz</a></td>
    </tr>
    <tr>
      <td class="row_left" width="400">{#OrgSitemap#}</td>
      <td class="row_right"><a class="colorbox" href="{$baseurl}/sitemap{$key}.xml">{$baseurl}/sitemap{$key}.xml</a></td>
    </tr>
    <tr>
      <td colspan="2">
        <div style="padding: 5px; margin: 5px; border: 1px solid #ccc; height: 300px; overflow: auto;">{$map}</div>
      </td>
    </tr>
    <tr>
      <td colspan="2">&nbsp;</td>
    </tr>
  {/foreach}
  <tr>
    <td class="row_left" width="400">{#NewsSitemap#}</td>
    <td class="row_right"><a class="colorbox" href="{$baseurl}/news.xml">{$baseurl}/news.xml</a></td>
  </tr>
</table>
