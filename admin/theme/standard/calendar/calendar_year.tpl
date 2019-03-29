<div class="box_innerhead">{#Calendar_yearView#}</div>
{include file="$incpath/calendar/calendar_actions.tpl"}
<table width="100%" cellspacing="0" cellpadding="0">
  <tr>
    <td><a href="index.php?p=calendar&amp;area={$area}&amp;action=displayyear&amp;show={$showtype}&amp;year={$year_prev}">&lt;&lt; {$year_prev}</a></td>
    <td><div align="center">
        <form method="post" action="index.php?sy=1&amp;p=calendar&amp;area={$area}&amp;action=displayyear">
          <select name="year">
            {section name=year loop=$startYear+10 name=year start=$startYear}
              <option value="{$smarty.section.year.index}" {if $Year == $smarty.section.year.index}selected="selected"{/if}>{$smarty.section.year.index}</option>
            {/section}
          </select>&nbsp;
          <select name="show" style="width: 103px">
            <option value="public" {if $showtype == 'public'}selected="selected"{/if}>{#Calendar_public#}</option>
            {if $loggedin}
              <option value="private" {if $showtype == 'private'}selected="selected"{/if}>{#Calendar_private#}</option>
            {/if}
          </select>&nbsp;
          <input style="width: 70px" name="submit" type="submit" class="button" value="{#Calendar_jumpB#}" />
        </form>
      </div></td>
    <td><div align="right"><a href="index.php?p=calendar&amp;area={$area}&amp;action=displayyear&amp;show={$showtype}&amp;year={$year_next}">{$year_next} &gt;&gt;</a></div></td>
  </tr>
</table>
{$years}
{include file="$incpath/calendar/calendar_actions.tpl"}
