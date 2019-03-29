<div class="popup_header">
  <h2>{$title_html}</h2>
</div>
<div class="popup_content" style="padding: 5px">
  <div class="popup_box">
    <div class="autowords_text"> {$text} </div>
  </div>
</div>
<br />
<div align="center">
  <form>
    <input type="button" class="button" onclick="print();" value="{#Print#}" />&nbsp;
    <input type="button" class="button" onclick="closeWindow();" value="{#WinClose#}" />
  </form>
</div>
<br />
{include file="$incpath/other/outlinks_no_style.tpl"}
