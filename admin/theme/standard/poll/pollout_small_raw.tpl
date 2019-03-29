{script file="$jspath/jprogressbar.js" position='head'}
<script type="text/javascript">
<!-- //
$(document).ready(function() {
    {foreach from=$PollResultsSmall item=pas}
    $('#progressbar_{$pas->Id}').progressBar({
        boxImage: '{$imgpath_page}progressbar.gif',
        barImage: '{$imgpath_page}progress_{$pas->Farbe}.gif',
        showText: true
    });
    {/foreach}
});
//-->
</script>

{if $PollAllreadySmall == 1}
  {if isset($Extern)}
    <div class="h2">
    {/if}
    <strong>{$PollTitleSmall|sanitize}</strong>
    {if isset($Extern)}
    </div>
  {/if}
  <br />
  <br />
  {foreach from=$PollResultsSmall item=pas}
    {if $pas->Perc == 1}
      {assign var=PollVar value=0}
    {else}
      {assign var=PollVar value=$pas->Perc|replace: ',': '.'}
    {/if}
    <div style="margin-top: 5px"><strong>{$pas->Frage|sanitize}</strong>
      <!--  {if $pas->Hits > 0}({$PollVar}%){/if} -->
    </div>
    <div style="margin-bottom: 5px"><span id="progressbar_{$pas->Id}">{$PollVar|default:0}%</span></div>
  {/foreach}
{else}
<script type="text/javascript">
<!-- //
$(document).ready(function() {
    var options = {
        target: '#pollform{$Extern}',
        timeout: 3000
    };
    $('#poll_form_1{$Extern}').submit(function() {
        $(this).ajaxSubmit(options);
        return false;
    });
});
//-->
</script>
  <form method="post" action="{$baseurl}/index.php?vote=1&amp;p=poll&amp;action=smallpoll{if isset($Extern)}&amp;intern=1{/if}" name="poll_form{$Extern}" id="poll_form_1{$Extern}">
    {if isset($Extern)}
      <div class="h2">
      {/if}
      <strong>{$PollTitleSmall}</strong>
      {if isset($Extern)}
      </div>
    {/if}
    <br />
    <br />
    {foreach from=$PollAnswersSmall item=pas}
      {if $PollRes->Multi == 1}
        <label><input type="checkbox" name="polloption[{$pas->Id}]" value="{$pas->Id}" /> {$pas->Frage|sanitize}</label>
        <br />
      {else}
        <label><input type="radio" name="polloption" value="{$pas->Id}" /> {$pas->Frage|sanitize}</label>
        <br />
      {/if}
    {/foreach}
    {$out}
    <br />
    <p align="center">
      {if $PollPermSmall == 1}
        <input type="submit" class="button" value="{#Poll_button#}" />
      {else}
        <small>{#Poll_noAccess#}</small>
      {/if}
    </p>
  </form>
{/if}
{if !isset($Extern) && $PollRes->Kommentare == 1}
  <br />
  {#Arrow#}<a href="{$baseurl}/index.php?p=poll&amp;area={$area}">{#Comments#}</a>
{/if}
<br />
{#Arrow#}<a href="{$baseurl}/index.php?p=poll&amp;action=archive&amp;area={$area}">{#Poll_Archive#}</a>
