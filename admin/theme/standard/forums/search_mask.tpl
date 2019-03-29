{include file="$incpath/forums/user_panel_forums.tpl"}
<div class="box_innerhead"><strong>{#Search#}</strong></div>
<div class="box_data">{#SearchHelp#}</div>
<form action="index.php" method="post">
  <input type="hidden" name="p" value="forum" />
  <input type="hidden" name="action" value="xsearch" />
  <fieldset>
    <legend>{#Forums_Header_search_key#}</legend>
    <input class="input" type="text" name="pattern" size="50" style="width: 300px" />&nbsp;
    <select class="input" name="type">
      <option value="-1">{#Forums_TypeNorm#}</option>
      <option value="1">{#Forums_Sticky#}</option>
      <option value="2">{#Forums_Announcement#}</option>
    </select>&nbsp;
    <label><input type="radio" name="search_post" value="2" checked="checked" />{#ForumsSearchPost#}</label>
    <label><input type="radio" name="search_post" value="0" />{#Forums_Field_search_in_title#} </label>&nbsp;&nbsp;
    <label><input type="radio" name="search_post" value="1" />{#Forums_Field_search_in_post#}</label>&nbsp;&nbsp;
  </fieldset>
  <fieldset>
    <legend>{#ForumsSearchUser#}</legend>
    <input class="input" type="text" name="user_name" size="50" style="width: 300px" />&nbsp;
    <select class="input" name="user_opt">
      <option value="2">{#ForumsSearchUserOld#}</option>
      <option value="1">{#ForumsSearchUserFull#}</option>
    </select>&nbsp;
  </fieldset>
  <table width="100%">
    <tr>
      <td>
        <fieldset style="height: 120px">
          <legend> {#Forums_Header_search_forums#} </legend>
          {strip}
            <select name="search_in_forums[]" class="input" size="5" style="width: 500px; height: 110px" id="search_in_forums" multiple="multiple">
              <option value="0" selected="selected">{#Forums_SearchAllForums#}</option>
              {foreach from=$forums_dropdown item=category}
                <optgroup label="{$category->title|sslash}">
                  {foreach from=$category->forums item=forum_dropdown}
                    {if $forum_dropdown->category_id == 0}
                      <option style="color: #000; font-weight: bold; font-style: italic" value="{$forum_dropdown->id}" disabled="disabled">{$forum_dropdown->visible_title|sslash} </option>
                    {else}
                      <option value="{$forum_dropdown->id}">{$forum_dropdown->visible_title|sslash} </option>
                    {/if}
                  {/foreach}
                </optgroup>
              {/foreach}
            </select>
          {/strip}
        </fieldset>
      </td>
      <td>
        <fieldset style="height: 120px">
          <legend> {#Forums_Header_search_date#} </legend>
          <select class="input" name="date">
            <option value="0">{#Forums_AnyDate#}</option>
            <option value="1" {if isset($smarty.post.period) && $smarty.post.period == 1}selected{/if}>{#Forums_OptYesterday#}</option>
            <option value="7" {if isset($smarty.post.period) && $smarty.post.period == 2}selected{/if}>{#Forums_OptLast_week#}</option>
            <option value="14" {if isset($smarty.post.period) && $smarty.post.period == 5}selected{/if}>{#Forums_OptLast_2_weeks#}</option>
            <option value="30" {if isset($smarty.post.period) && $smarty.post.period == 10}selected{/if}>{#Forums_OptLast_month#}</option>
            <option value="90" {if isset($smarty.post.period) && $smarty.post.period == 20}selected{/if}>{#Forums_OptLast_3_months#}</option>
            <option value="180" {if isset($smarty.post.period) && $smarty.post.period == 30}selected{/if}>{#Forums_OptLast_6_months#}</option>
            <option value="365" {if isset($smarty.post.period) && $smarty.post.period == 40}selected{/if}>{#Forums_OptLast_year#}</option>
          </select>
          <br />
          <br />
          <label><input type="radio" name="b4after" value="0" checked="checked" />{#Forums_AndNewer#}</label>
          <br />
          <label><input type="radio" name="b4after" value="1" />{#Forums_AndOlder#} </label>
        </fieldset>
      </td>
      <td>
        <fieldset style="height: 120px">
          <legend> {#Forums_Header_search_sort#} </legend>
          <select class="input" name="search_sort">
            <option value="1">{#SortBy#} {#Forums_Sort_subject#}</option>
            <option value="2">{#SortBy#} {#Forums_Sort_posts#}</option>
            <option value="3">{#SortBy#} {#Forums_Author#}</option>
            <option value="4">{#SortBy#} {#Forums_Sort_forum#}</option>
            <option value="5">{#SortBy#} {#Forums_Field_hits#}</option>
            <option value="6">{#SortBy#} {#Forums_Field_date#}</option>
          </select>
          <br />
          <br />
          <label><input type="radio" name="ascdesc" value="DESC" checked="checked" />{#Forums_SortDesc#}</label>
          <br />
          <label><input type="radio" name="ascdesc" value="ASC" />{#Forums_SortAsc#}</label>
        </fieldset>
      </td>
    </tr>
  </table>
  <br />
  <p align="center">
    <input type="submit" class="button" style="width: 120px; font-size: 120%" value="{#Search#}" />
  </p>
</form>
{include file="$incpath/forums/forums_footer.tpl"}
