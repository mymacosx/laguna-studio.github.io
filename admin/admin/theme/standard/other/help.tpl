{if isset($all_help) && $all_help == 1}
  <div class="header"> {#GlobalNavHelp#}</div>
  <div class="subheaders">
    {if $admin_settings.Ahelp == 1}
      <a class="colorbox" href="index.php?do=help&amp;sub={$helpquery}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/s_help.png" alt="" border="0" /> {#GlobalHelp#}</a>&nbsp;&nbsp;&nbsp;
      {/if}
    <a class="colorbox" href="index.php?do=support&amp;sub=send_order&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/send.png" alt="" border="0" /> {#SendOrder#}</a>
  </div>
  {foreach from=$help item=h}
    <div class="sysinfos">
      {$h}
    </div>
    <br />
  {/foreach}
{else}
  <div class="sysinfos">
    {$help}
  </div>
{/if}
