<div class="header">{#Forums_Del_Ratings#}</div>
<div class="subheaders">
  {if $admin_settings.Ahelp == 1}
    <a class="colorbox" href="index.php?do=help&amp;sub={$helpquery}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/s_help.png" alt="" border="0" /> {#GlobalHelp#}</a>&nbsp;&nbsp;&nbsp;
    {/if}
  <a class="colorbox" href="index.php?do=support&amp;sub=send_order&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/send.png" alt="" border="0" /> {#SendOrder#}</a>
</div>
<div class="subheaders"> {#Forums_Del_RatingInf#} </div>
<form name="delForm" id="delForm" action="" method="post">
  {if $smarty.request.delete == 1}
    <div class="info_green"> {#GlobalOk#} </div>
  {/if}
  {strip}
    <select name="fid_r[]" size="10" multiple="multiple" class="input" style="width: 450px">
      {foreach from=$forums item=f}
        <optgroup label="{$f->title|sanitize}"></optgroup>
        {foreach from=$f->forums item=fo}
          <option value="{$fo->id}">{$fo->title|sanitize} {$fo->id}</option>
        {/foreach}
      {/foreach}
    </select>
  {/strip}
  <br />
  <br />
  <input class="button" type="button" onclick="if (confirm('{#Forums_delT_Csubmit#}')) document.forms['delForm'].submit();" value="{#Forums_delT_submit#}" />
  <input name="delete" type="hidden" id="delete" value="1" />
</form>
