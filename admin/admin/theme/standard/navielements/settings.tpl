{if perm('settings')}
  <a class="menuitem submenuheader" href="#"><img class="absmiddle" src="{$imgpath}/settings.png" alt="" /> {#Global_Settings#}</a>
  <div class="submenu">
    <ul>
      <li><a class="{if isset($smarty.request.sub) && $smarty.request.sub == 'admin_global'}nav_subs_active{else}nav_subs{/if}" href="index.php?do=settings&amp;sub=admin_global">{#Admin_Global#}</a></li>
      <li><a class="{if isset($smarty.request.sub) && $smarty.request.sub == 'global'}nav_subs_active{else}nav_subs{/if}" href="index.php?do=settings&amp;sub=global">{#Settings_general#}</a></li>
      <li><a class="{if isset($smarty.request.sub) && $smarty.request.sub == 'money'}nav_subs_active{else}nav_subs{/if}" href="index.php?do=settings&amp;sub=money">{#MoneySite#}</a></li>
      <li><a class="{if isset($smarty.request.sub) && $smarty.request.sub == 'sectionsettings'}nav_subs_active{else}nav_subs{/if}" href="index.php?do=settings&amp;sub=sectionsettings">{#Global_SettingsSections#}</a></li>
      <li><a class="{if isset($smarty.request.sub) && $smarty.request.sub == 'widgets'}nav_subs_active{else}nav_subs{/if}" href="index.php?do=settings&amp;sub=widgets">{#Global_Widgets#}</a></li>
      <li><a class="{if isset($smarty.request.sub) && $smarty.request.sub == 'languages'}nav_subs_active{else}nav_subs{/if}" href="index.php?do=settings&amp;sub=languages">{#Settings_languages#}</a></li>
      <li><a class="{if isset($smarty.request.sub) && $smarty.request.sub == 'adminlanguages'}nav_subs_active{else}nav_subs{/if}" href="index.php?do=settings&amp;sub=adminlanguages">{#Settings_languages_a#}</a></li>
        {if perm('lang_edit')}
        <li><a class="{if isset($smarty.request.sub) && $smarty.request.sub == 'lang_edit'}nav_subs_active{else}nav_subs{/if}" href="index.php?do=settings&amp;sub=lang_edit">{#SettingsLangEdit#}</a></li>
        {/if}
      <li><a class="{if isset($smarty.request.sub) && $smarty.request.sub == 'sectionsdisplay'}nav_subs_active{else}nav_subs{/if}" href="index.php?do=settings&amp;sub=sectionsdisplay">{#Sections#}</a></li>
        {if $smarty.session.benutzer_id == 1}
        <li><a class="{if isset($smarty.request.sub) && $smarty.request.sub == 'phpedit'}nav_subs_active{else}nav_subs{/if}" href="index.php?do=settings&amp;sub=phpedit">{#ConfPhp#}</a></li>
        <li><a class="{if isset($smarty.request.sub) && $smarty.request.sub == 'htaccess'}nav_subs_active{else}nav_subs{/if}" href="index.php?do=settings&amp;sub=htaccess">{#HtaccessSettings#}</a></li>
        {/if}
      <li><a class="{if isset($smarty.request.sub) && $smarty.request.sub == 'secure'}nav_subs_active{else}nav_subs{/if}" href="index.php?do=settings&amp;sub=secure">{#SecureSettings#}</a></li>
      <li><a class="{if isset($smarty.request.sub) && $smarty.request.sub == 'cron'}nav_subs_active{else}nav_subs{/if}" href="index.php?do=settings&amp;sub=cron">{#Scheduler#}</a></li>
      <li><a class="{if isset($smarty.request.do) && $smarty.request.do == 'expimp'}nav_subs_active{else}nav_subs{/if}" href="index.php?do=expimp">{#Admin_ExpImp#}</a></li>
      <li><a class="{if isset($smarty.request.sub) && $smarty.request.sub == 'logs'}nav_subs_active{else}nav_subs{/if}" href="?do=settings&amp;sub=logs">{#Admin_Logs#}</a></li>
    </ul>
  </div>
{/if}
