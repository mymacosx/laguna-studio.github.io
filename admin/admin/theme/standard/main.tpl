<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>{$title|default:'Панель управления'}</title>
{include file="$incpath/header/head.tpl"}
</head>
<body id="mainwin">
{if perm('notes') && $admin_settings.Aktiv_Notes == 1}
<script type="text/javascript">
<!-- //
$(document).ready(function() {
    var top = '-' + $('#slidedown_content .content').css('height');
    $('#slidedown_top').on('click', function() {
        $('#slidedown_content').animate({ 'top' : 0 }, { queue: false, duration: 500 });
    });
    $('#slidedown_bottom').on('click', function() {
        $('#slidedown_content').animate({ 'top' : top }, { queue: false, duration: 500 });
    });
});
//-->
</script>

{include file="$incpath/notes/notes.tpl"}
{/if}
<div id="slidedown_bottom">
  <div id="com_loader"><img src="{$imgpath}/loading_big.gif" alt="" /></div>
  <div id="com" style="display: none">
    <div class="header_main_info">
      <table width="100%" border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td align="center" width="175"><a class="stip" title="Официальный сайт SX CMS" href="http://www.status-x.ru/" target="_blank"><img src="{$imgpath}/logo.png" border="0" alt="" /></a></td>
          <td nowrap="nowrap" style="color: #fff">
            <a href="index.php">{#StartPage#}</a> &nbsp;|&nbsp;
            <a href="index.php?logout=1">{#Global_Logout#}</a> &nbsp;|&nbsp;
            <a href="{$baseurl}">{#Global_Site#}</a> &nbsp;|&nbsp;
            {if $admin_settings.Navi_Anime == 1}
              <img id="navielements" style="cursor: pointer" class="absmiddle stip" title="{#NaviAnime#}" src="{$imgpath}/navielem.png" alt="" /> &nbsp;|&nbsp;
            {/if}
            {#Global_LoggedInAs#} <strong>{$smarty.session.user_name}</strong> &nbsp;|&nbsp;
            {#Global_CurrSection#} {$section_switch} &nbsp;|&nbsp;
            {#Sections_theme#}: {$theme_switch}
          </td>
        </tr>
      </table>
    </div>
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        {if $admin_settings.Navi == 'left'}
          {include file="$incpath/navielements/navielements.tpl"}
        {/if}
        <td valign="top" width="100%">
          <div class="main">
          {if !empty($mesage_save)}{$mesage_save}{/if}
          {$content}
        </div>
      </td>
      {if $admin_settings.Navi == 'right'}
        {include file="$incpath/navielements/navielements.tpl"}
      {/if}
    </tr>
  </table>
</div>
</div>
</body>
</html>
