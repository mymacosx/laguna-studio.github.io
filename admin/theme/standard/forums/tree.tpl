<div class="padding5" style="height: auto;">
  {foreach from=$treeview|default:'' item=leaf name=treeview}
    {if $smarty.foreach.treeview.iteration % 2 == 0 && $smarty.foreach.treeview.iteration != 0}
      {assign var=switch value=$smarty.foreach.treeview.iteration/2}
      <br />
      {section name=treespace loop=$smarty.foreach.treeview.iteration}
        {if $smarty.section.treespace.iteration == $switch}
          <img src="{$imgpath_forums}forum_vspacer.gif" alt="" />
        {/if}
        {if $smarty.section.treespace.iteration < $switch}
          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        {/if}
        {if ($smarty.section.treespace.iteration - $switch) == 1}
          <img src="{$imgpath_forums}forum_hspacer.gif" alt="" />
        {/if}
      {/section}
      &nbsp;{$leaf|sslash}
    {else}
      {if $smarty.foreach.treeview.iteration != 1}
        {#PageSep#}{$leaf|sslash}
      {else}
        {$leaf|sslash}
      {/if}
    {/if}
  {/foreach}
</div>
<br />
