{script file="$jspath/jprogressbar.js" position='head'}
<script type="text/javascript">
<!-- //
$(document).ready(function() {
    {foreach from=$polls_once item=pas}
    $('#progressbar_{$pas->Id}').progressBar({
        boxImage: '{$imgpath_page}progressbar.gif',
        barImage: '{$imgpath_page}progress_{$pas->Farbe}.gif',
        showText: true
    });
    {/foreach}
});
//-->
</script>

<div class="h2">{$Question|sanitize}</div>
<br />
<br />
{foreach from=$polls_once item=pas}
  {if $pas->Perc == 1}
    {assign var=PollVar value=0}
  {else}
    {assign var=PollVar value=$pas->Perc|replace: ',': '.'}
  {/if}
  <div style="margin-top: 5px"><strong>{$pas->Frage|sanitize}</strong>
    <!--  {if $pas->Hits>0}({$PollVar}%){/if} -->
  </div>
  <div style="margin-bottom: 5px"><span id="progressbar_{$pas->Id}">{$PollVar|default:0}%</span></div>
{/foreach}
