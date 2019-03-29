{if isset($smarty.request.do) && $smarty.request.do == 'news'}
  {assign var=fckElem value='News'}
{elseif isset($smarty.request.do) && $smarty.request.do == 'content'}
  {assign var=fckElem value='Content'}
{elseif isset($smarty.request.do) && $smarty.request.do == 'articles'}
  {assign var=fckElem value='Inhalt'}
{elseif isset($smarty.request.do) && $smarty.request.do == 'shop'}
  {assign var=fckElem value='Beschreibung2'}
{/if}
<script type="text/javascript">
<!-- //
$(document).ready(function() {
    $('.ExtElems').colorbox({ inline: true, href: '#dialog' });
});
function addYoutube(id, url, name) {
    var text = prompt(url, '');
    if (text) {
        if (text.indexOf('youtu.be') !== -1) {
            var param = text.match(/^http[s]*:\/\/youtu\.be\/([a-z0-9_-]+)/i);
        } else {
            var param = text.match(/^http[s]*:\/\/www\.youtube\.com\/watch\?.*?v=([a-z0-9_-]+)/i);
        }
        if (param && param.length === 2) {
            if (typeof name !== 'undefined' && name !== '') {
                var video = prompt(name, '');
            }
            if (video) {
                return insertEditor(id, '[youtube:' + video + ']' + param[1] + '[/youtube]<br />');
            } else {
                return insertEditor(id, '[youtube]' + param[1] + '[/youtube]<br />');
            }
        }
    }
    alert('{#Validate_url#}');
    return false;
}
//-->
</script>

<input href="#" title="{#ExtElemsT#}" class="ExtElems button" type="button" value="{#ExtElems#}" />
<div style="display: none">
  <div id="dialog" align="center" class="subheaders" style="margin-top: 15px">
    {if $cforms}
      <select style="width: 250px" class="input" onchange="eval(this.options[this.selectedIndex].value); selectedIndex = 0;">
        <option value="">- {#Content_insertContactForms#} -</option>
        {foreach from=$cforms item=cf}
          <option value="insertEditor('{$fckElem}','[CONTACT:{$cf->Id}]')">{$cf->Titel1}</option>
        {/foreach}
      </select>
      <br /><br />
    {/if}
    {if $ContentLinks}
      <select style="width: 250px" class="input" onchange="eval(this.options[this.selectedIndex].value); selectedIndex = 0;">
        <option value="">- {#Content_insertContentLinks#} -</option>
        {foreach from=$ContentLinks item=cn}
          <option value="insertEditor('{$fckElem}',' &lt;a href=&quot;index.php?p=content&amp;id={$cn->Id}&amp;name={$cn->Titel1|translit}&amp;area={$cn->Sektion}&quot;&gt;{$cn->Titel1|sanitize}&lt;/a&gt; ')">{$cn->Titel1|sanitize}</option>
        {/foreach}
      </select>
      <br /><br />
    {/if}
    {if $ContentVideos}
      <select style="width: 250px" class="input" onchange="eval(this.options[this.selectedIndex].value); selectedIndex = 0;">
        <option value="">- - {#VideoNew#} - -</option>
        {foreach from=$ContentVideos item=cv}
          <option value="insertEditor('{$fckElem}','[VIDEO:{$cv->Id}]')">[VIDEO:{$cv->Id}] ({$cv->Name|sanitize})</option>
        {/foreach}
      </select>
      <br /><br />
    {/if}
    {if $ContentAudios}
      <select style="width: 250px" class="input" onchange="eval(this.options[this.selectedIndex].value); selectedIndex = 0;">
        <option value="">- - {#AudioNew#} - -</option>
        {foreach from=$ContentAudios item=ca}
          <option value="insertEditor('{$fckElem}','[AUDIO:{$ca->Id}]')">[AUDIO:{$ca->Id}] ({$ca->Name|sanitize})</option>
        {/foreach}
      </select>
      <br /><br />
    {/if}
    <select style="width: 250px" class="input" onchange="eval(this.options[this.selectedIndex].value); selectedIndex = 0;">
      <option value="">- - {#VideoNew#} Youtube - -</option>
      <option value="addYoutube('{$fckElem}','{#YoutubePromt#}','{#MyvYtPeV#}')">{#YoutomT#}</option>
      <option value="addYoutube('{$fckElem}','{#YoutubePromt#}')">{#YoutooT#}</option>
    </select>
    <br /><br />
    {if $CodeWidgetsAll}
      <select style="width: 250px" class="input" onchange="eval(this.options[this.selectedIndex].value); selectedIndex = 0;">
        <option value="">- - {#CodeWidgets#} - -</option>
        {foreach from=$CodeWidgetsAll item=cb}
          <option value="insertEditor('{$fckElem}','[CODEWIDGET:{$cb->Id}]')">[CODEWIDGET:{$cb->Id}] ({$cb->Name|sanitize})</option>
        {/foreach}
      </select>
      <br /><br />
    {/if}
    {if admin_active('highlighter')}
      <select style="width: 250px" class="input" onchange="eval(this.options[this.selectedIndex].value); selectedIndex = 0;">
        <option value="">- - {#AddHighlight#} - -</option>
        <option value="insertEditor('{$fckElem}','[sx_code lang=php] PHP [/sx_code]')">{#AddHighlight#} PHP</option>
        <option value="insertEditor('{$fckElem}','[sx_code lang=html] HTML [/sx_code]')">{#AddHighlight#} HTML</option>
        <option value="insertEditor('{$fckElem}','[sx_code lang=js] Javascript [/sx_code]')">{#AddHighlight#} Javascript</option>
        <option value="insertEditor('{$fckElem}','[sx_code lang=css] CSS [/sx_code]')">{#AddHighlight#} CSS</option>
        <option value="insertEditor('{$fckElem}','[sx_code lang=mysql] MySQL [/sx_code]')">{#AddHighlight#} MySQL</option>
        <option value="insertEditor('{$fckElem}','[sx_code lang=java] Java [/sx_code]')">{#AddHighlight#} Java</option>
        <option value="insertEditor('{$fckElem}','[sx_code lang=delphi] Delphi [/sx_code]')">{#AddHighlight#} Delphi</option>
      </select>
    {/if}
  </div>
</div>
