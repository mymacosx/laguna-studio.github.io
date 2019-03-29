{if !empty($ShopMsg->ShopName) && $ShopMsg->TitelZeigen == 1}
  <h3>{$ShopMsg->ShopName}</h3>
{/if}
{if !empty($ShopMsg->InfoText) && $ShopMsg->MeldungZeigen == 1}
  {$ShopMsg->InfoText}
{/if}
