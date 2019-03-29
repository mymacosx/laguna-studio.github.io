<div class="shop_contents_box">
  {$p.Beschreibung|autowords}
  {if !empty($p.BeschreibungLang)}
    {$p.BeschreibungLang|autowords}
  {/if}
  {if !empty($article_pages)}
    <br />
    <br />
    {$article_pages}
  {/if}
  <div class="product_details_specs">
    <table width="100%" cellspacing="0" cellpadding="0">
      {if intval($p.GewichtF) > 0}
        <tr>
          <td nowrap="nowrap" class="shop_specification_left">{#Shop_ProdWeight#}: </td>
          <td class="shop_specification_right">
            {#Shop_ProdWeightca#} {$p.GewichtF}
            {if !empty($p.GewichtRaw)}
              ({$p.GewichtRaw} {#Shop_ProdWeightRaw#})
            {/if}
          </td>
        </tr>
      {/if}
      {if $p.Abmessungen}
        <tr>
          <td nowrap="nowrap" class="shop_specification_left">{#Shop_ProdHBL#}: </td>
          <td class="shop_specification_right">{$p.Abmessungen|sanitize}{#Shop_ProdHBLC#} </td>
        </tr>
      {/if}
      {if !empty($p.EAN_Nr)}
        <tr>
          <td nowrap="nowrap" class="shop_specification_left">EAN: </td>
          <td class="shop_specification_right">{$p.EAN_Nr|sanitize}</td>
        </tr>
      {/if}
      {if !empty($p.ISBN_Nr)}
        <tr>
          <td nowrap="nowrap" class="shop_specification_left">ISBN: </td>
          <td class="shop_specification_right">{$p.ISBN_Nr|sanitize}</td>
        </tr>
      {/if}
      {if $p.man->Id}
        <tr>
          <td nowrap="nowrap" class="shop_specification_left">{#Manufacturer#}: </td>
          <td class="shop_specification_right"><a href="index.php?p=shop&amp;action=showproducts&amp;man={$p.man->Id}">{$p.man->Name}</a></td>
        </tr>
      {/if}
      {if !empty($p.PrCountry)}
        <tr>
          <td nowrap="nowrap" class="shop_specification_left">{#PrCountry#}: </td>
          <td class="shop_specification_right">{$p.PrCountry|sanitize}</td>
        </tr>
      {/if}
      {if $det_spez.Spez_1 && $p.Spez_1}
        <tr>
          <td nowrap="nowrap" class="shop_specification_left">{$det_spez.Spez_1|specialchars}: </td>
          <td class="shop_specification_right">{$p.Spez_1|cleantoentities|default:'-'|autowords}</td>
        </tr>
      {/if}
      {if $det_spez.Spez_2 && $p.Spez_2}
        <tr>
          <td nowrap="nowrap" class="shop_specification_left">{$det_spez.Spez_2|specialchars}: </td>
          <td class="shop_specification_right">{$p.Spez_2|cleantoentities|default:'-'|autowords}</td>
        </tr>
      {/if}
      {if $det_spez.Spez_3 && $p.Spez_3}
        <tr>
          <td nowrap="nowrap" class="shop_specification_left">{$det_spez.Spez_3|specialchars}: </td>
          <td class="shop_specification_right">{$p.Spez_3|cleantoentities|default:'-'|autowords}</td>
        </tr>
      {/if}
      {if $det_spez.Spez_4 && $p.Spez_4}
        <tr>
          <td nowrap="nowrap" class="shop_specification_left">{$det_spez.Spez_4|specialchars}: </td>
          <td class="shop_specification_right">{$p.Spez_4|cleantoentities|default:'-'|autowords}</td>
        </tr>
      {/if}
      {if $det_spez.Spez_5 && $p.Spez_5}
        <tr>
          <td nowrap="nowrap" class="shop_specification_left">{$det_spez.Spez_5|specialchars}: </td>
          <td class="shop_specification_right">{$p.Spez_5|cleantoentities|default:'-'|autowords}</td>
        </tr>
      {/if}
      {if $det_spez.Spez_6 && $p.Spez_6}
        <tr>
          <td nowrap="nowrap" class="shop_specification_left">{$det_spez.Spez_6|specialchars}: </td>
          <td class="shop_specification_right">{$p.Spez_6|cleantoentities|default:'-'|autowords}</td>
        </tr>
      {/if}
      {if $det_spez.Spez_7 && $p.Spez_7}
        <tr>
          <td nowrap="nowrap" class="shop_specification_left">{$det_spez.Spez_7|specialchars}: </td>
          <td class="shop_specification_right">{$p.Spez_7|cleantoentities|default:'-'|autowords}</td>
        </tr>
      {/if}
      {if $det_spez.Spez_8  && $p.Spez_8}
        <tr>
          <td nowrap="nowrap" class="shop_specification_left">{$det_spez.Spez_8|specialchars}: </td>
          <td class="shop_specification_right">{$p.Spez_8|cleantoentities|default:'-'|autowords}</td>
        </tr>
      {/if}
      {if $det_spez.Spez_9  && $p.Spez_9}
        <tr>
          <td nowrap="nowrap" class="shop_specification_left">{$det_spez.Spez_9|specialchars}: </td>
          <td class="shop_specification_right">{$p.Spez_9|cleantoentities|default:'-'|autowords}</td>
        </tr>
      {/if}
      {if $det_spez.Spez_10 && $p.Spez_10}
        <tr>
          <td nowrap="nowrap" class="shop_specification_left">{$det_spez.Spez_10|specialchars}: </td>
          <td class="shop_specification_right">{$p.Spez_10|cleantoentities|default:'-'|autowords}</td>
        </tr>
      {/if}
      {if $det_spez.Spez_11 && $p.Spez_11}
        <tr>
          <td nowrap="nowrap" class="shop_specification_left">{$det_spez.Spez_11|specialchars}: </td>
          <td class="shop_specification_right">{$p.Spez_11|cleantoentities|default:'-'|autowords}</td>
        </tr>
      {/if}
      {if $det_spez.Spez_12 && $p.Spez_12}
        <tr>
          <td nowrap="nowrap" class="shop_specification_left">{$det_spez.Spez_12|specialchars}: </td>
          <td class="shop_specification_right">{$p.Spez_12|cleantoentities|default:'-'|autowords}</td>
        </tr>
      {/if}
      {if $det_spez.Spez_13 && $p.Spez_13}
        <tr>
          <td nowrap="nowrap" class="shop_specification_left">{$det_spez.Spez_13|specialchars}: </td>
          <td class="shop_specification_right">{$p.Spez_13|cleantoentities|default:'-'|autowords}</td>
        </tr>
      {/if}
      {if $det_spez.Spez_14 && $p.Spez_14}
        <tr>
          <td nowrap="nowrap" class="shop_specification_left">{$det_spez.Spez_14|specialchars}: </td>
          <td class="shop_specification_right">{$p.Spez_14|cleantoentities|default:'-'|autowords}</td>
        </tr>
      {/if}
      {if $det_spez.Spez_15 && $p.Spez_15}
        <tr>
          <td nowrap="nowrap" class="shop_specification_left">{$det_spez.Spez_15|specialchars}: </td>
          <td class="shop_specification_right">{$p.Spez_15|cleantoentities|default:'-'|autowords}</td>
        </tr>
      {/if}
      {if $shopsettings->Zeige_ErschienAm == 1}
        <tr>
          <td nowrap="nowrap" class="shop_specification_left">{#Shop_releasedProductDetail#}: </td>
          <td class="shop_specification_right">{$p.Erstellt|date_format: $lang.DateFormatSimple}</td>
        </tr>
      {/if}
    </table>
  </div>
</div>
