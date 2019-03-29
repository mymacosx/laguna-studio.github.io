{assign var='langcode' value=$smarty.request.langcode|default:1}
<script type="text/javascript">
<!-- //
{include file="$incpath/other/jsvalidate.tpl"}
$.validator.setDefaults({
    submitHandler: function() {
        document.forms['sysform'].submit();
    }
});

$(document).ready(function() {
    $('#sysform').validate({
        ignore: '#container-options',
        rules: {
	    Name: { required: true },
	    Url_Direct: { },
	    Size_Direct: { number: true }
        },
        messages: {
	    Url: { url: '{#InserURL#}' }
	}
    });

    $('#container-options').tabs({
        selected: {$smarty.post.current_tabs|default:0},
	select: function(event, ui) {
	   $('#current_tabs').val(ui.index);
	}
    });
});

function changeLang(langcode) {
    if(confirm('{#Global_changeLangDoc#}')) {
       location.href = 'index.php?do=faq&sub=edit&id={$smarty.request.id}&noframes=1&langcode=' + langcode;
    } else {
        document.getElementById('l_{$langcode}').selected=true;
    }
}
//-->
</script>

<div class="header_inf">
  <form onsubmit="" method="post" action="">
    <select class="input" onchange="eval(this.options[this.selectedIndex].value);" name="langcode" id="langcode">
      <option id="l_1" value="changeLang(1);" {if $langcode == 1}selected="selected"{/if}>{#Faq_edit_in#} - {$language.name.1|upper} - {#Shop_articles_editinb#}</option>
      <option id="l_2" value="changeLang(2);" {if $langcode == 2}selected="selected"{/if}>{#Faq_edit_in#} - {$language.name.2|upper} - {#Shop_articles_editinb#}</option>
      <option id="l_3" value="changeLang(3);" {if $langcode == 3}selected="selected"{/if}>{#Faq_edit_in#} - {$language.name.3|upper} - {#Shop_articles_editinb#}</option>
    </select>
    <img class="absmiddle" src="{$imgpath}/arrow_right.png" alt="" />
  </form>
</div>
<div id="container-options">
  <ul>
    <li><a href="#opt-1"><span>{#News_tab_gen#}</span></a></li>
    <li><a href="#opt-2"><span>{#Global_Inline#}</span></a></li>
  </ul>
  <form method="post" action="" name="sysform" id="sysform">
    <div id="opt-1">
      <fieldset>
        <legend>{#Faq_Sub#} ({$language.name.$langcode})</legend>
        <select class="input" style="width: 400px" name="Kategorie">
          {foreach from=$categs item=fc}
            <option value="{$fc->Id}" {if $fc->Id == $smarty.request.categ}selected="selected"{/if}>{$fc->visible_title}</option>
          {/foreach}
        </select>
      </fieldset>
      <fieldset>
        <legend><label for="t1">{#Global_Name#} ({$language.name.$langcode})</label></legend>
        <input name="Name" type="text" class="input" id="t1" style="width: 400px" value="{$res->Name_1}" />
      </fieldset>
      <fieldset>
        <legend>{#Content_text#} ({$language.name.$langcode})</legend>
        {$Beschreibung}
      </fieldset>
    </div>
    <div id="opt-2">
      {assign var='inline_table' value='faq'}
      {assign var='fieldname' value=$field_inline}
      {include file="$incpath/screenshots/load.tpl"}
    </div>
    <input type="hidden" id="current_tabs" name="current_tabs" value="{$smarty.post.current_tabs|default:0}" />
    <input type="submit" class="button" value="{#Save#}" />
    <input type="button" onclick="closeWindow(true);" class="button" value="{#Close#}" />
    <input name="save" type="hidden" id="save" value="1" />
    <input type="hidden" name="langcode" value="{$langcode}" />
  </form>
</div>
