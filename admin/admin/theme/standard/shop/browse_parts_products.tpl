<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>{$title}</title>
{include file="$incpath/header/style.tpl"}
{include file="$incpath/header/jquery.tpl"}
</head>
<body class="row_left">
<script type="text/javascript">
<!-- //
function setElem(s,i) {
    parent.document.getElementById('ers').innerHTML = parent.document.getElementById('ers').innerHTML + '<input type="checkbox" name="ersatzteile[]" value="' + i + '" checked="checked">' + s + '<br />';
}
//-->
</script>

  {foreach from=$categs_ass item=categs}
    {foreach from=$categs->prods item=p}
      <input type="hidden" id="hidden_prod2_{$p->Id}" value="{$p->Titel}" />
    {/foreach}
  {/foreach}
  <form>
    <table border="0" cellspacing="0" cellpadding="1">
      <tr>
        <td>
          {strip}
            <select style="width: 385px;" onchange="eval(this.options[this.selectedIndex].value);">
              <optgroup label="{#Shop_articles_art_seltcatiframe#}">
                <option></option>
              </optgroup>
              {foreach from=$categs_ass item=categs}
                <option style="{if $categs->Bold == 1}font-weight: bold;{/if}" {if isset($smarty.request.c) && $smarty.request.c == $categs->catid}selected="selected"{/if} value="location.href = 'index.php?noout=1&noframes=1&do=shop&sub=get_prods_parts&c={$categs->catid}';">{$categs->Expander}{$categs->Name}</option>
              {/foreach}
            </select>
          {/strip}
        </td>
      </tr>
      <tr>
        <td>
          {if !empty($prods_all)}
            <select name="proditem" size="30" id="proditem" style="width: 385px;" ondblclick="setElem(document.getElementById('hidden_prod2_' + this.options[this.selectedIndex].value).value,this.options[this.selectedIndex].value);">
              {foreach from=$prods_all item=proditem}
                <option value="{$proditem->Id}">{$proditem->Titel}</option>
              {/foreach}
            </select>
          {/if}
        </td>
      </tr>
    </table>
    <input type="button" class="button" style="width: 385px" value="{#Shop_articles_art_selectfileiframe#}" onclick="setElem(document.getElementById('hidden_prod2_' + document.getElementById('proditem').options[document.getElementById('proditem').selectedIndex].value).value, document.getElementById('proditem').options[document.getElementById('proditem').selectedIndex].value);"/>
  </form>
</body>
</html>
