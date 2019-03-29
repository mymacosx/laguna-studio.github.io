{script file="$jspath/jprogressbar.js" position='head'}
<div class="box_innerhead">{#Poll_Archive#}</div>
{foreach from=$polls item=p name=pa}
<script type="text/javascript">
<!-- //
$(document).ready(function() {
    {foreach from=$p->PollItems item=pas}
    $('#progressbar_{$pas->Id}_{$p->Id}').progressBar({
        boxImage: '{$imgpath_page}progressbar.gif',
        barImage: '{$imgpath_page}progress_{$pas->Farbe}.gif',
        showText: true
    });
    {/foreach}
});
//-->
</script>

  <div id="voterdisp_{$pas->Id}_{$p->Id}" class="infobox poll_back">
    {if $p->Aktiv == 1}
      <div class="h2"><a href="index.php?p=poll&amp;area={$area}">{$p->Titel|sanitize}</a></div>
      {else}
      <div class="h2"><a href="index.php?p=poll&amp;id={$p->Id}&amp;name={$p->Titel|translit}&amp;area={$area}">{$p->Titel|sanitize}</a></div>
      {/if}
    <table width="100%" cellspacing="0" cellpadding="1">
      <tr>
        <td width="60%" valign="top">
          {foreach from=$p->PollItems item=pas}
            {if $pas->Perc == 1}
              {assign var=PollVar value=0}
            {else}
              {assign var=PollVar value=$pas->Perc|replace: ',': '.'}
            {/if}
            <div style="margin-top: 5px"><strong>{$pas->Frage|sanitize}</strong>
              <!--  {if $pas->Hits>0}({$PollVar}%){/if} -->
            </div>
            <div style="margin-bottom: 5px"><span id="progressbar_{$pas->Id}_{$p->Id}">{$PollVar|default:0}%</span></div>
          {/foreach}
        </td>
        <td valign="top"><table width="100%" cellpadding="0" cellspacing="0" class="box_inner">
            <tr>
              <td width="150" class="row_left"><strong>{#Poll_Users#}: &nbsp;</strong></td>
              <td class="row_right">{$p->HitsAll}</td>
            </tr>
            <tr>
              <td class="row_left"><strong>{#Poll_Start#}: &nbsp;</strong></td>
              <td class="row_right">{$p->Start|date_format: $lang.DateFormatSimple}</td>
            </tr>
            <tr>
              <td class="row_left"><strong>{#Poll_End#}: &nbsp;</strong></td>
              <td class="row_right">{$p->Ende|date_format: $lang.DateFormatSimple}</td>
            </tr>
            {if $p->Aktiv != 1}
              <tr>
                <td class="row_left"><strong>{#GlobalStatus#}: &nbsp;</strong></td>
                <td class="row_right" nowrap="nowrap">{#Poll_Status_Inactive#}</td>
              </tr>
            {/if}
          </table></td>
      </tr>
    </table>
  </div>
{/foreach}
{if isset($pollNavi)}
  {$pollNavi}
{/if}
