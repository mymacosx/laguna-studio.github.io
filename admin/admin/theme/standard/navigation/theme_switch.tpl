<select onchange="eval(this.options[this.selectedIndex].value);">
  {foreach from=$themes item=th}
    <option value="location.href='index.php?do=login&amp;action=themeswitch&amp;theme={$th}'"{if $th == $theme} selected="selected"{/if}>{$th}</option>
  {/foreach}
</select>