<div class="box_innerhead">{#Info#}</div>
<div class="infobox">
  {if $allready == 1}
    {#RegE_AllreadyRegistered#}
  {else}
    {if $settings.Reg_Typ == 'norm'}
      {#Reg_Inf_Auto#}
    {else}
      {#Reg_Inf_Email#}
    {/if}
  {/if}
</div>
<br />
