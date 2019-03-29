<form action="index.php?p=showforum" method="post" name="fp" id="fp">
  {strip}
    <select class="input" name="fid" style="width: 330px">
      {foreach from=$categories_dropdown item=category}
        <optgroup label="{$category->title|sslash}">
          {foreach from=$category->forums item=forum_dropdown}
            {if $forum_dropdown->category_id == 0}
              <option style="color: #000; font-weight: bold; font-style: italic" value="{$forum_dropdown->id}" disabled="disabled">{$forum_dropdown->visible_title|sslash} </option>
            {else}
              <option value="{$forum_dropdown->id}" {if isset($smarty.request.fid) && $smarty.request.fid == $forum_dropdown->id} selected="selected" {/if}>{$forum_dropdown->visible_title|sslash} </option>
            {/if}
          {/foreach}
        </optgroup>
      {/foreach}
    </select>
  {/strip}
  &nbsp;
  <input type="submit" class="button" value="{#GotoButton#}" />
</form>
