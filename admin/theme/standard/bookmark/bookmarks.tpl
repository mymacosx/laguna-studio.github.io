{if $loggedin && get_active('social_bookmarks')}
<script type="text/javascript">
<!-- //
togglePanel('navpanel_bookmarks', 'togglerboxes', 30, '{$basepath}');
//-->
</script>

<div class="round">
  <div class="opened" id="navpanel_bookmarks" title="{#Bookmarks#}">
    <div class="boxes_body">
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td class="navigation_box_bg">
            <div align="center"><a id="bookmark_link" onclick="toggleContent('bookmark_link','bookmark_content');" href="javascript: void(0);"><h4>{#ThisBookmark#}</h4></a></div>
            <div class="status" style="display: none" id="bookmark_content">
              {#TheName#}
              <br />
              <form action="index.php?p=bookmark" method="post">
                <input type="hidden" name="document" value="{page_link|base64encode}" />
                <input name="document_name" type="text" class="input" value="" size="25" />&nbsp;
                <input class="button" type="submit" value="{#Global_Add#}" />
              </form>
            </div>
            <br />
            {if $bookmarks}
              <form action="index.php?p=bookmark" method="post">
                <table width="100%" cellpadding="0" cellspacing="0">
                  {foreach from=$bookmarks item=bm}
                    <tr>
                      <td><a href="{$bm->document}">{$bm->doc_name|truncate: 25: "": true|sanitize}</a></td>
                      <td align="right"><input style="margin: 0px" type="checkbox" name="del_bookmark[{$bm->id}]" value="{$bm->id}" /></td>
                    </tr>
                  {/foreach}
                </table>
                <br />
                <div align="center">
                  <input type="hidden" name="action" value="delete" />
                  <input type="hidden" name="backurl" value="{page_link|base64encode}" />
                  <input type="submit" class="button" value="{#BookmarkDel#}" style="width: 130px" />
                </div>
              </form>
            {else}
              <div align="center"> {#NoBookmarks#} </div>
            {/if}
          </td>
        </tr>
      </table>
    </div>
  </div>
</div>
{/if}
