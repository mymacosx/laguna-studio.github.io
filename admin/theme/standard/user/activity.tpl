<div class="box_innerhead"><strong>{#UserActivity#}</strong></div>
<div class="infobox">
  <table width="100%" border="0" cellspacing="0" cellpadding="4">
    {foreach from=$posts item=p}
      {if !empty($p.forum_title)}
        <tr>
          <td><strong>{#GlobalMessage#} / <a href="index.php?p=showforums">{#Forums_Title#}</a> / <a {if $p.enc == 1}class="stip" title="{$p.message|tooltip:160}" {/if} href="{$p.link}">{$p.forum_title|sslash|sanitize}</a></strong></td>
          <td nowrap="nowrap" align="right"><small>({#Gaming_articles_from#}: {$p.date|date_format: "%d.%m.%y, %H:%M"})</small></td>
        </tr>
      {else}
        <tr>
          <td><strong>{#Global_Comment#} {if !empty($p.sec_link)}/ <a href="{$p.sec_link}">{$p.sec_name|sslash|sanitize}</a> {/if}/ <a class="stip" title="{$p.Eintrag|tooltip:160}" href="{$p.link}">{$p.sec_title|sslash|sanitize}</a></strong></td>
          <td nowrap="nowrap" align="right"><small>({#Gaming_articles_from#}: {$p.date|date_format: "%d.%m.%y, %H:%M"})</small></td>
        </tr>
      {/if}
    {/foreach}
  </table>
  {if !$posts}
    <small>{#NoAktivity#}</small>
  {/if}
</div>
