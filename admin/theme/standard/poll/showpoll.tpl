{if $Inactive == 1}
  <div class="h2">{#Poll_Ianactive#}</div>
{else}
  <div class="box_innerhead">{#Poll_Name#}</div>
  <div class="infobox poll_back">
    <div id="pollform1"> {$CPoll} </div>
  </div>
  <div class="box_innerhead">{#Poll_Infos#}</div>
  <div class="infobox poll_back">
    <table width="100%" cellpadding="0" cellspacing="0" class="box_inner">
      <tr>
        <td width="150" class="row_left">{#Poll_Users#}</td>
        <td class="row_right">{$PollRes->HitsAll}</td>
      </tr>
      <tr>
        <td class="row_left">{#Poll_Start#}</td>
        <td class="row_right">{$PollRes->Start|date_format: $lang.DateFormatSimple}</td>
      </tr>
      <tr>
        <td class="row_left">{#Poll_End#}</td>
        <td class="row_right">{$PollRes->Ende|date_format: $lang.DateFormatSimple}</td>
      </tr>
      {if $PollRes->Aktiv != 1}
        <tr>
          <td class="row_left">{#GlobalStatus#}</td>
          <td class="row_right" nowrap="nowrap">{#Poll_Status_Inactive#}</td>
        </tr>
      {/if}
    </table>
  </div>
  {if $PollRes->Kommentare == 1}
    {if $PollRes->Aktiv != 1}
      {assign var=noComment value=1}
    {/if}
    {$GetComments|default:''}
  {/if}
{/if}
