<div class="box_innerhead"><strong>{#UserVisits#}</strong></div>
<div class="infobox">
  <table>
    <tr>
      {assign var='n' value=-1}
      {foreach from=$items item=item}
        {assign var='n' value=$n+1}
        {if $n == $avatar_line}
        </tr>
        <tr><td>&nbsp;</td></tr>
        <tr>
          {assign var='n' value=0}
        {/if}
        <td width="{$avatar_visits+14}" height="80" valign="bottom" style="text-align: center">
          <a title="{$item.Name}" href="index.php?p=user&amp;id={$item.Id}&amp;area={$area}">{$item.Avatar}</a>
          <br />
          <a href="index.php?p=user&amp;id={$item.Id}&amp;area={$area}">{$item.Name}</a>
          <br />
        </td>
      {/foreach}
    </tr>
  </table>
</div>
