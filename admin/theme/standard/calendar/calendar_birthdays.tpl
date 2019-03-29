<div class="box_innerhead">{#Birthdays_Today#}</div>
<div class="box_data">
  {if !empty($birthdays)}
    {foreach from=$birthdays item=bd}
      <img class="absmiddle" src="{$imgpath}/calendar/birthday.png" alt="" /> <a class="calendarEventLink" href="index.php?p=user&amp;id={$bd.Id}&amp;area={$area}">{$bd.Benutzername|truncate: '20'} ({$bd.Age})</a><br />
    {/foreach}
  {else}
    {#Birthdays_Today_No#}
  {/if}
</div>
