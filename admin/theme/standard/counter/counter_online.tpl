<strong>{#Forums_OnlineUsers#}</strong> {$UserOnline},
<strong>{#Forums_OnlineGuests#}</strong> {$GuestsOnline}
<br />
{foreach from=$userOnlineLinks item=uo name=useronline}
  {if $loggedin}
    <a id="user_link_{$uo.Uid}" onmouseover="toggleContent('user_link_{$uo.Uid}', 'user_content_{$uo.Uid}');" href="index.php?p=user&amp;id={$uo.Uid}&amp;area={$area}">{$uo.Benutzername|sanitize}</a>{if !$smarty.foreach.useronline.last}, {/if}
    <div class="status" style="display: none" id="user_content_{$uo.Uid}">{$uo.Link}</div>
  {else}
    <a href="index.php?p=user&amp;id={$uo.Uid}&amp;area={$area}">{$uo.Benutzername|sanitize}</a>{if !$smarty.foreach.useronline.last}, {/if}
  {/if}
{/foreach}
<br />
<strong>{#Forums_OnlineBot#}</strong> {$BotOnline}
<br />
{foreach from=$botOnlineLinks item=bot name=botonline}
  {if $loggedin}
    <a id="bot_link_{$bot.BotsId}" onmouseover="toggleContent('bot_link_{$bot.BotsId}', 'bot_content_{$bot.BotsId}');" href="javascript: void(0);">{$bot.Benutzername|sanitize}{if $bot.CountBotName > 1}({$bot.CountBotName}){/if}</a>{if !$smarty.foreach.botonline.last}, {/if}
    <div class="status" style="display: none" id="bot_content_{$bot.BotsId}">{$bot.Link}</div>
  {else}
    <span class="nolinks">{$bot.Benutzername|sanitize}{if $bot.CountBotName > 1}({$bot.CountBotName}){/if}</span>{if !$smarty.foreach.botonline.last}, {/if}
  {/if}
{/foreach}
