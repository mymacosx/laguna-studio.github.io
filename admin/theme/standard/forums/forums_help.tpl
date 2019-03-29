{include file="$incpath/forums/user_panel_forums.tpl"}

<div class="box_innerhead"><strong>{#Help_General#}</strong></div>
    {foreach from=$faq_categ item=fc}
  <div class="infobox">
    <h2>{$fc->Name}</h2>
    <br />
    <ul>
      {foreach from=$fc->Items item=items}
          <li><a href="index.php?p=forum&amp;action=help&amp;hid={$items->Id}&amp;sub={$items->FaqName|translit}">{$items->FaqName}</a></li>
          {/foreach}
    </ul>
  </div>
{/foreach}
<br />
<br />
