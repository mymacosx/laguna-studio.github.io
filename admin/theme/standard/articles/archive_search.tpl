<div id="archive_search" class="infobox">
  <form method="post" action="index.php?p=articles&amp;area={$area}">
    <table width="100%" cellspacing="0" cellpadding="1">
      <tr>
        <td width="100"><label for="arcs">{#SearchT#}</label>&nbsp;</td>
        <td><input style="width: 90%" id="arcs" type="text" name="q_news" class="input" value="{$smarty.request.q_news|default:''|escape: html}" /></td>
      </tr>
      <tr>
        <td width="100"><label for="arccat">{#Global_Categ#}</label>&nbsp;</td>
        <td>
          <select class="input" style="width: 150px" name="catid" id="arccat">
            <option value="0">{#AllCategs#}</option>
            {foreach from=$dropdown item=dd}
              <option value="{$dd->Id}" {if isset($smarty.request.catid) && $smarty.request.catid == $dd->Id}selected="selected"{/if}>{$dd->visible_title}</option>
            {/foreach}
          </select>
        </td>
      </tr>
      <tr>
        <td><label for="arcrecords">{#Results#}</label>&nbsp;</td>
        <td>
          <input name="limit" id="arcrecords" type="text" class="input" value="{$news_limit}" size="2" maxlength="2" />
          <input type="hidden" name="st" value="or" />
          <input type="hidden" name="page" value="1" />&nbsp;
          <input class="button" type="submit" value="{#StartSearch#}" />
        </td>
      </tr>
    </table>
  </form>
</div>
