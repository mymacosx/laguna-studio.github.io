{include file="$incpath/forums/user_panel_forums.tpl"}

<div class="box_innerhead"><a href="index.php?p=forum&amp;action=help"><strong>{#Help_General#}</strong></a></div>
<br />
<table width="100%" cellpadding="0" cellspacing="0">
  <tr>
    <td valign="top">
      <h2>{$row_faq->FaqName|sanitize}</h2>
      <br />
      <br />
      {$row_faq->FaqText}
    </td>
    <td width="40">&nbsp;&nbsp;</td>
    <td width="300" valign="top">
      {foreach from=$faq_categ item=fc}
        <div class="infobox">
          <h3>{$fc->Name}</h3>
          <br />
          <ul>
            {foreach from=$fc->Items item=items}
              <li><a style="{if $items->Id == $smarty.request.hid}text-decoration: none{/if}" href="index.php?p=forum&amp;action=help&amp;hid={$items->Id}&amp;sub={$items->FaqName|translit}">{$items->FaqName}</a></li>
              {/foreach}
          </ul>
        </div>
      {/foreach}
    </td>
  </tr>
</table>
<br />
