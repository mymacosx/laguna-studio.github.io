<strong>{#Shop_couponcodes_co#} </strong>{$row->Erstellt|date_format: '%d.%m.%y'}
{if $row->Eingeloest > 1}
  <br />
  <strong>{#Shop_couponcodes_ro#} </strong>
  {$row->Eingeloest|date_format: '%d.%m.%Y, %H:%M'}
{/if}
