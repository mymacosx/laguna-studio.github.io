{if (!empty($p.Frei_1) || !empty($p.Frei_2) || !empty($p.Frei_3))}
  <div class="shop_product_vars"><strong>{#Konfiguration#}</strong><br />
    <table width="100%" cellpadding="0" cellspacing="1">
      {if $p.Frei_1}
        <tr>
          <td width="150"><label for="lfree_1">{$p.Frei_1}</label></td>
          <td>
            <input name="free_1" id="lfree_1" type="text" class="input" style="width: 200px" />
            {if $p.Frei_1_Pflicht == 1}
              <strong>{#Shop_product_configM#}</strong>
            {/if}
          </td>
        </tr>
      {/if}
      {if $p.Frei_2}
        <tr>
          <td width="150"><label for="lfree_2">{$p.Frei_2}</label></td>
          <td>
            <input name="free_2" id="lfree_2" type="text" class="input" style="width: 200px" />
            {if $p.Frei_2_Pflicht == 1}
              <strong>{#Shop_product_configM#}</strong>
            {/if}
          </td>
        </tr>
      {/if}
      {if $p.Frei_3}
        <tr>
          <td width="150"><label for="lfree_3">{$p.Frei_3}</label></td>
          <td>
            <input name="free_3" id="lfree_3" type="text" class="input" style="width: 200px" />
            {if $p.Frei_3_Pflicht == 1}
              <strong>{#Shop_product_configM#}</strong>
            {/if}
          </td>
        </tr>
      {/if}
    </table>
  </div>
{/if}
