<img class="format_buttons stip" title="{$lang.Format_Tip_Bold|tooltip}" onclick="addCode('b');" src="{$imgpath}/comment/text_bold.png" alt="" />
<img class="format_buttons stip" title="{$lang.Format_Tip_Italic|tooltip}" onclick="addCode('i');" src="{$imgpath}/comment/text_kursive.png" alt="" />
<img class="format_buttons stip" title="{$lang.Format_Tip_Underline|tooltip}" onclick="addCode('u');" src="{$imgpath}/comment/text_underline.png" alt="" />
<img class="format_buttons stip" title="{$lang.Format_Tip_Line|tooltip}" onclick="addCode('s');" src="{$imgpath}/comment/text_line.png" alt="" />
<img class="format_buttons stip" title="{$lang.Format_Tip_Center|tooltip}" onclick="addCode('center');" src="{$imgpath}/comment/text_center.png" alt="" />
<img class="format_buttons stip" title="{$lang.Format_Tip_Right|tooltip}" onclick="addCode('right');" src="{$imgpath}/comment/text_right.png" alt="" />
<img class="format_buttons stip" title="{$lang.Format_Tip_Left|tooltip}" onclick="addCode('left');" src="{$imgpath}/comment/text_left.png" alt="" />
<img class="format_buttons stip" title="{$lang.Format_Tip_Justify|tooltip}" onclick="addCode('justify');" src="{$imgpath}/comment/text_justify.png" alt="" />
<img class="format_buttons stip" title="{$lang.Format_Tip_List|tooltip}" onclick="addCode('list');" src="{$imgpath}/comment/text_list.png" alt="" />
<img class="format_buttons stip" title="{$lang.Format_Tip_Hide|tooltip}" onclick="addCode('hide');" src="{$imgpath}/comment/hide.png" alt="" />
<img class="format_buttons stip" title="{$lang.Format_Tip_Reg|tooltip}" onclick="addCode('reg');" src="{$imgpath}/comment/reg.png" alt="" />
<img class="format_buttons stip" title="{$lang.Format_Tip_Spoiler|tooltip}" onclick="addCode('spoiler');" src="{$imgpath}/comment/spoiler.png" alt="" />
{if $ugroup == 1}
  <img class="format_buttons stip" title="{$lang.Format_Tip_Mod|tooltip}" onclick="addCode('mod');" src="{$imgpath}/comment/mod.png" alt="" />
{/if}
{if $settings.SysCode_Links == 1}
  <img class="format_buttons stip" title="{$lang.Format_Tip_Url|tooltip}" onclick="addCode('url');" src="{$imgpath}/comment/text_link.png" alt="" />
{/if}
<img class="format_buttons stip" title="{$lang.Format_Tip_High|tooltip}" onclick="addCode('highlight');" src="{$imgpath}/comment/text_highlight.png" alt="" />
{if $settings.SysCode_Email == 1}
  <img class="format_buttons stip" title="{$lang.Format_Tip_Email|tooltip}" onclick="addCode('mail');" src="{$imgpath}/comment/text_email.png" alt="" />
{/if}
{if $settings.SysCode_Bild == 1}
  <img class="format_buttons stip" title="{$lang.Format_Tip_Image|tooltip}" onclick="addCode('img');" src="{$imgpath}/comment/text_image.png" alt="" />
{/if}
<img class="format_buttons stip" title="{$lang.Format_text_enter_youtubeTip|tooltip}" onclick="addCode('video');" src="{$imgpath}/comment/youtube.gif" alt="" />
<img class="format_buttons stip" title="{$lang.Format_Tip_Quote|tooltip}" onclick="addCode('quote');" src="{$imgpath}/comment/text_quote.png" alt="" />
<img class="format_buttons stip" title="{$lang.Format_Tip_Code|tooltip}" onclick="addCode('code');" src="{$imgpath}/comment/text_code.png" alt="" />
<img class="format_buttons stip" title="{$lang.Format_Tip_Php|tooltip}" onclick="addCode('php');" src="{$imgpath}/comment/text_php.png" alt="" />
<img class="format_buttons stip" title="{$lang.Format_Tip_CloseAll|tooltip}" onclick="closeCodes();" src="{$imgpath}/comment/text_close.png" alt="" />
<br />
<select name="font" class="input" style="height: 2em" onchange="loadSelect('[face=' + this.form.font.options[this.form.font.selectedIndex].value + ']', '[/face]');
    this.selectedIndex = 0;">
  <option value="0" selected="selected"> {#Format_FontFace#} </option>
  {foreach from=$listfonts item=fonts}
    <option style="font-family: {$fonts.font}" value="{$fonts.font}"> {$fonts.fontname} </option>
  {/foreach}
</select>
&nbsp;&nbsp;
<select name="size" class="input" style="height: 2em" onchange="loadSelect('[size=' + this.form.size.options[this.form.size.selectedIndex].value + ']', '[/size]');
    this.selectedIndex = 0;">
  <option  value="0" selected="selected"> {#Format_FontSize#} </option>
  {foreach from=$sizedropdown item=size}
    <option style="font-size: {$size.css_size}pt" value="{$size.css_size}"> {$size.size}pt </option>
  {/foreach}
</select>
&nbsp;&nbsp;
<select name="color" class="input" style="height: 2em" onchange="loadSelect('[color=' + this.form.color.options[this.form.color.selectedIndex].value + ']', '[/color]');
    this.selectedIndex = 0;">
  <option value="0" selected="selected"> {#Format_FontColor#} </option>
  {foreach from=$colordropdown item=color}
    <option style="color: {$color.color}" value="{$color.color}"> {$color.fontcolor} </option>
  {/foreach}
</select>
