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
            Name: { required: true }
        },
        messages: { }
    });

  $('#container-options').tabs({
        selected: {$smarty.post.current_tabs|default:0},
	select: function(event, ui) {
	   $('#current_tabs').val(ui.index);
	}
    });
});
//-->
</script>

<div id="container-options">
  <ul>
    <li><a href="#opt-1"><span>{#News_tab_gen#}</span></a></li>
    <li><a href="#opt-2"><span>{#Global_Inline#}</span></a></li>
  </ul>
  <form method="post" action="" name="sysform" id="sysform">
    <div id="opt-1">
      <fieldset>
        <legend>{#Faq_Sub#}</legend>
        <select class="input" style="width: 400px" name="Kategorie">
          {foreach from=$categs item=fc}
            <option value="{$fc->Id}" {if isset($smarty.request.categ) && $fc->Id == $smarty.request.categ}selected="selected"{/if}>{$fc->visible_title}</option>
          {/foreach}
        </select>
      </fieldset>
      <fieldset>
        <legend><label for="t1">{#Global_Name#} ({$language.name.1})</label></legend>
        <input name="Name" type="text" class="input" id="t1" style="width: 400px" value="{$res->Name_1|default:''}" />
      </fieldset>
      <fieldset>
        <legend>{#Content_text#}</legend>
        {$Beschreibung}
      </fieldset>
    </div>
    <div id="opt-2">
      {assign var='inline_table' value='faq'}
      {assign var='fieldname' value=$field_inline|default:''}
      {include file="$incpath/screenshots/load.tpl"}
    </div>
    <input type="hidden" id="current_tabs" name="current_tabs" value="{$smarty.post.current_tabs|default:0}" />
    <input type="submit" class="button" value="{#Save#}" />
    <input type="button" onclick="closeWindow();" class="button" value="{#Close#}" />
    <input name="save" type="hidden" id="save" value="1" />
    <input type="hidden" name="langcode" value="{$smarty.request.langcode|default:1}" />
  </form>
</div>
