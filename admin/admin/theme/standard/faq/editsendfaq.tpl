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
//-->
</script>

<div id="container-options">
  <ul>
    <li><a href="#opt-1"><span>{#News_tab_gen#}</span></a></li>
    <li><a href="#opt-2"><span>{#Global_Inline#}</span></a></li>
  </ul>
  <form method="post" action="" name="sysform" id="sysform">
    <div id="opt-1">
      {if !empty($res->NewCat)}
        <fieldset>
          <legend>{#FaqCatAutor#}</legend>
          <input name="NewCat" type="text" class="input" style="width: 400px" value="{$res->NewCat}" />&nbsp;&nbsp;&nbsp;
          <a title="{#Global_NewCateg#}" class="colorbox_small" href="index.php?do=faq&amp;sub=addcateg&amp;newcat={$res->NewCat|urlencode}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/folder_add.png" alt="" border="0" /> {#GlobalAddCateg#}</a>
        </fieldset>
      {/if}
      <fieldset>
        <legend>{#Faq_Sub#}</legend>
        <select class="input" style="width: 400px" name="Kategorie">
          {foreach from=$categs item=fc}
            <option value="{$fc->Id}" {if $fc->Id == $smarty.request.categ}selected="selected"{/if}>{$fc->visible_title}</option>
          {/foreach}
        </select>
      </fieldset>
      <fieldset>
        <legend><label for="t1">{#Global_Name#} ({$language.name.1})</label></legend>
        <input name="Name" type="text" class="input" id="t1" style="width: 400px" value="{$res->Name_1}" />
      </fieldset>
      <fieldset>
        <legend>{#Content_text#}</legend>
        {$Beschreibung}
      </fieldset>
      <fieldset>
        <table border="0" cellpadding="0" cellspacing="0">
          <tr>
            <td width="260"><strong>{#FaqMailAutor#}</strong></td>
            <td>
              <input type="radio" name="sendmail" value="1" checked="checked" />{#Yes#}
              <input type="radio" name="sendmail" value="0" />{#No#}
            </td>
          </tr>
        </table>
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
    <input type="hidden" name="autormail" value="{$res->Sender}" />
    <input type="hidden" name="datum" value="{$res->Datum|date_format: "%d.%m.%Y - %H:%M"}" />
    <input name="save" type="hidden" id="save" value="1" />
    <input type="hidden" name="langcode" value="{$smarty.request.langcode|default:1}" />
    <label>
    </label>
  </form>
</div>
