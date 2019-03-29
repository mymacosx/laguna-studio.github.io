{assign var='langcode' value=$smarty.request.langcode|default:1}
<script type="text/javascript">
function changeLang(langcode) {
    if(confirm('{#Global_changeLangDoc#}')) {
        if (langcode === 1) {
            location.href='index.php?do=shop&sub=edit_categ&id={$smarty.request.id}&parent={$smarty.request.parent}&noframes=1&langcode=1';
        } else {
            location.href='index.php?do=shop&sub=name_text&id={$smarty.request.id}&noframes=1&langcode=' + langcode;
        }
    } else {
        document.getElementById('l_{$langcode}').selected = true;
    }
}
//-->
</script>

<div class="popbox">
  <div class="header">{#Global_CategEdit#} ({$language.name.$langcode})</div>
  <div class="header_inf">
    <form method="post" action="">
      <select class="input" onchange="eval(this.options[this.selectedIndex].value);" name="langcode" id="langcode">
        <option id="l_1" value="changeLang(1);" {if $langcode == 1}selected="selected"{/if}>{#Shop_variants_editlang#} - {$language.name.1|upper} - {#Shop_articles_editinb#}</option>
        <option id="l_2" value="changeLang(2);" {if $langcode == 2}selected="selected"{/if}>{#Shop_variants_editlang#} - {$language.name.2|upper} - {#Shop_articles_editinb#}</option>
        <option id="l_3" value="changeLang(3);" {if $langcode == 3}selected="selected"{/if}>{#Shop_variants_editlang#} - {$language.name.3|upper} - {#Shop_articles_editinb#}</option>
      </select>
      <img class="absmiddle" src="{$imgpath}/arrow_right.png" alt="" />
    </form>
  </div>
  <div id="content_popup">
    <form method="post" action="" name="s">
      <fieldset>
        <legend>{#Global_name#}  ({$language.name.$langcode})</legend>
        <input name="Name_{$langcode}" type="text" class="input" size="40" value="{$row->Name|sanitize}" />
      </fieldset>
      <fieldset>
        <legend>{#Global_descr#}  ({$language.name.$langcode})</legend>
        {$Editor}
        <p>
          <input class="button" type="submit" id="s" value="{#Save#}" />
          <input class="button" type="button" onclick="closeWindow();" value="{#Close#}" />
        </p>
      </fieldset>
      <input type="hidden" name="id" value="{$smarty.request.id}" />
      <input type="hidden" name="langcode" value="{$langcode}" />
      <input name="save" type="hidden" id="save" value="1" />
    </form>
  </div>
</div>
