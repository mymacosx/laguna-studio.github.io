<script type="text/javascript">
<!-- //
$(document).ready(function() {
    $('#linkextern, #linkextern2').on('click', function() {
        var options = {
            target: '#plick',
            url: 'index.php?action=updatehitcount&p=manufacturer&id={$res->Id}',
            timeout: 3000
        };
        $(this).ajaxSubmit(options);
        return true;
    });
});
//-->
</script>

<div class="box_innerhead">{#Manufacturer#}</div>
<div class="links_list_title">
  <span id="plick"></span>
  <h3><a rel="nofollow" href="{$res->Homepage}" id="linkextern" target="_blank">{$res->Name|sanitize}</a></h3>
</div>
{if !empty($res->Bild)}
  <img class="links_list_img" src="uploads/manufacturer/{$res->Bild}" align="right" alt="" />
{/if}
{$res->Beschreibung}
<br style="clear: both" />
<br />
<div class="download_link_infbox">
  <table width="100%" cellspacing="0" cellpadding="2">
    {if $res->NameLang}
      <tr>
        <td class="row_left" width="110">{#Manufacturer_fullname#}: &nbsp;</td>
        <td class="row_right">{$res->NameLang|sanitize}</td>
      </tr>
    {/if}
    {if $res->Gruendung}
      <tr>
        <td width="110" class="row_left">{#Manufacturer_gr_year#}: &nbsp;</td>
        <td class="row_right">{$res->Gruendung|sanitize}</td>
      </tr>
    {/if}
    {if $res->GruendungLand}
      <tr>
        <td width="110" class="row_left">{#Country#}: &nbsp;</td>
        <td class="row_right">{$res->GruendungLand|sanitize}</td>
      </tr>
    {/if}
    <tr>
      <td width="110" class="row_left">{#Manufacturer_home#}: &nbsp;</td>
      <td class="row_right"><a rel="nofollow" href="{$res->Homepage}" id="linkextern2" target="_blank">{$res->Homepage|sanitize}</a></td>
    </tr>
    {if $res->Personen >= 1}
      <tr>
        <td width="110" class="row_left">{#Manufacturer_worker#}: &nbsp;</td>
        <td class="row_right">{$res->Personen|sanitize}</td>
      </tr>
    {/if}
    {if $res->Adresse}
      <tr>
        <td width="110" class="row_left">{#Imprint#}: &nbsp;</td>
        <td class="row_right">{$res->Adresse|sslash}</td>
      </tr>
    {/if}
    {if $res->Telefonkontakt}
      <tr>
        <td width="110" class="row_left">{#Phone#}: &nbsp;</td>
        <td class="row_right">{$res->Telefonkontakt|sanitize}</td>
      </tr>
    {/if}
  </table>
</div>
{if !empty($Products)}
  <div class="box_innerhead">{#Manufacturer_productsFrom#} {$res->Name|sanitize}</div>
  {foreach from=$Products item=prod}
    <div class="{cycle name='gb' values='links_list_second,links_list'}">
      <div class="links_list_title">
        <h3><a href="index.php?p=shop&amp;area={$area}&amp;action=showproduct&amp;id={$prod->Id}&amp;cid={$prod->Kategorie}&amp;pname={$prod->Titel|translit}">{$prod->Titel|sanitize}</a></h3>
      </div>
        {if !empty($prod->Bild)}
        <div style="float:left; padding-right:10px">
          <a class="stip" title="{$prod->Titel|sanitize}" href="index.php?p=shop&amp;area={$area}&amp;action=showproduct&amp;id={$prod->Id}&amp;cid={$prod->Kategorie}&amp;pname={$prod->Titel|translit}"><img class="absmiddle" src="{$prod->Bild}" alt="" /></a>
        </div>
        {/if}
        {$prod->Beschreibung|truncate: 250}
    </div>
  {/foreach}
{/if}
