<select onchange="eval(this.options[this.selectedIndex].value);">
  {foreach from=$sections item=sw}
    <option value="location.href='index.php?do=login&amp;action=sectionswitch&amp;id={$sw->Id}'"{if $sw->Id == $area} selected="selected"{/if}>{$sw->Name}</option>
  {/foreach}
</select>