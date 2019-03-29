{if $ugroup == 1}
  <div class="infobox"><strong>{#AccountDelAdmin#}</strong></div>
{else}
  {if isset($CurrPassWrong) && $CurrPassWrong == 1}
    <div class="error_box">{#AccountDelPassWrong#}</div>
  {/if}
  <form method="post" action="index.php?p=useraction&amp;action=deleteaccount">
    <div class="infobox">{#AccountDelInf#}</div>
    <div class="box_innerhead">{#AccountDelReason#}</div>
    <div class="infobox">
      <select class="input" name="DelReason">
        {foreach from=$DelReasons item=Reason name=R}
          <option value="{$Reason}">{$Reason}</option>
        {/foreach}
      </select>
    </div>
    <div class="box_innerhead">{#AccountDelMessage#}</div>
    <div class="infobox">
      <textarea name="ReasonMessage" cols="" rows="" class="input" style="height: 150px; width: 98%">{if isset($smarty.post.ReasonMessage)}{$smarty.post.ReasonMessage|sanitize}{/if}</textarea>
    </div>
    <div class="box_innerhead">{#AccountDelFinal#}</div>
    <div class="infobox">{#AccountDelPass#}: &nbsp;
      <input name="PassCurr" type="text" class="input" style="width: 100px" />
    </div>
    <input type="hidden" name="subaction" value="delfinal" />
    <p align="center">
      <input type="submit" class="button" value="{#AccountDelFinalButton#}" />
    </p>
  </form>
{/if}
