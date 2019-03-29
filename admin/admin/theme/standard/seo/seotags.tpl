<div class="header">{#Seotags#}</div>
<div class="subheaders">
  <a class="colorbox" title="{#Global_Add#}" href="index.php?do=seo&amp;sub=add_seotags&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/add.png" alt="" border="0" /> {#Global_Add#}</a>&nbsp;&nbsp;&nbsp;
    {if $admin_settings.Ahelp == 1}
    <a class="colorbox" href="index.php?do=help&amp;sub={$helpquery}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/s_help.png" alt="" border="0" /> {#GlobalHelp#}</a>&nbsp;&nbsp;&nbsp;
    {/if}
  <a class="colorbox" href="index.php?do=support&amp;sub=send_order&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/send.png" alt="" border="0" /> {#SendOrder#}</a>
</div>
<table width="100%" border="0" cellspacing="0" cellpadding="3">
  <form method="post" action="index.php?do=seo&amp;sub=seotags">
    <tr>
      <td width="430">
        {#Search#}: <input style="width: 255px" class="input" type="text" name="q" id="q" value="{$smarty.request.q|default:''}" />&nbsp;&nbsp;
        {#DataRecords#}: <input class="input" style="width: 30px" type="text" name="pp" id="pp" value="{$limit}" />
      </td>
      <td>
        <input name="Senden" style="width: 110px" type="submit" class="button" value="{#Global_search_b#}" />
        <input type="button" style="width: 100px" class="button" onclick="location.href = 'index.php?do=seo&amp;sub=seotags';" value="{#ButtonReset#}" />
        <input type="button" class="button" onclick="location.href = 'index.php?do=seo&amp;sub=del_all_seotags';" value="{#DelAll#}" />
      </td>
    </tr>
  </form>
</table>
<table width="100%" border="0" cellspacing="0" cellpadding="3">
  <tr class="headers">
    <td nowrap="nowrap" width="20%" class="headers"><a href="index.php?do=seo&amp;sub=seotags&amp;sort={$pagesort|default:'page_desc'}&amp;pp={$limit}&amp;page={$smarty.request.page|default:1}{if !empty($smarty.request.q)}&amp;q={$smarty.request.q|urlencode}{/if}">{#Global_Page#}</a></td>
    <td width="15%" align="center" nowrap="nowrap" class="headers">&lt;canonical&gt;</td>
    <td width="15%" align="center" nowrap="nowrap" class="headers">&lt;title&gt;</td>
    <td width="20%" align="center" nowrap="nowrap" class="headers">&lt;keywords&gt;</td>
    <td width="20%" align="center" nowrap="nowrap" class="headers">&lt;description&gt;</td>
    <td width="10%" align="center" nowrap="nowrap" class="headers">{#Global_Actions#}</td>
  </tr>
  {foreach from=$items item=g}
    <tr class="{cycle values='second,first'}">
      <td>
        {if $g->page == 'home'}
          <a class="stip colorbox" title="{#GoPagesButton#}: {$baseurl}/" href="{$baseurl}/">HOME</a>
        {else}
          <a class="stip colorbox" title="{#GoPagesButton#}: {$baseurl}/{$g->page}" href="{$baseurl}/{$g->page}">{$g->page}</a>
        {/if}
      </td>
      <td>
        {if !empty($g->canonical)}
          <a class="stip colorbox" title="{#GoPagesButton#}: {$baseurl}/{$g->canonical}" href="{$baseurl}/{$g->canonical}">{$g->canonical|slice: 20: '...'}</a>
        {else}
          ----------
        {/if}
      </td>
      <td>
        {if !empty($g->title)}
          {$g->title|sanitize}
        {else}
          ----------
        {/if}
      </td>
      <td>
        {if !empty($g->keywords)}
          {$g->keywords|sanitize}
        {else}
          ----------
        {/if}
      </td>
      <td>
        {if !empty($g->description)}
          {$g->description|sanitize}
        {else}
          ----------
        {/if}
      </td>
      <td nowrap="nowrap" width="10%"><a class="colorbox stip" title="{$lang.Edit|sanitize}" href="index.php?do=seo&amp;sub=edit_seotags&amp;id={$g->id}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/edit.png" alt="" border="0" /></a>&nbsp;
          {if $g->aktiv == 1}
          <a class="stip" title="{$lang.Global_Active|sanitize}" href="index.php?do=seo&amp;sub=aktiv_seotags&amp;type=0&amp;id={$g->id}&amp;pp={$limit}&amp;page={$smarty.request.page}{if !empty($smarty.request.q)}&amp;q={$smarty.request.q|urlencode}{/if}"><img class="absmiddle" src="{$imgpath}/opened.png" alt="" border="0" /></a>&nbsp;
          {else}
          <a class="stip" title="{$lang.Global_Inactive|sanitize}" href="index.php?do=seo&amp;sub=aktiv_seotags&amp;type=1&amp;id={$g->id}&amp;pp={$limit}&amp;page={$smarty.request.page}{if !empty($smarty.request.q)}&amp;q={$smarty.request.q|urlencode}{/if}"><img class="absmiddle" src="{$imgpath}/closed.png" alt="" border="0" /></a>&nbsp;
          {/if}
        <a class="stip" title="{$lang.Global_Delete|sanitize}" href="index.php?do=seo&amp;sub=del_seotags&amp;id={$g->id}&amp;pp={$limit}&amp;page={$smarty.request.page}{if !empty($smarty.request.q)}&amp;q={$smarty.request.q|urlencode}{/if}"><img class="absmiddle" src="{$imgpath}/delete.png" alt="" border="0" /></a></td>
    </tr>
  {/foreach}
  {if !empty($navi)}
    <tr>
      <td class="first" colspan="5">{$navi}</td>
    </tr>
  {/if}
</table>
