<table width="200" cellspacing="0" cellpadding="4">
  <tr valign="top">
    <td align="left" nowrap="nowrap">
      <form method="post" name="logout_form_up" action="index.php">
        <input type="hidden" name="p" value="userlogin" />
        <input type="hidden" name="action" value="logout" />
        <input type="hidden" name="backurl" value="{page_link|base64encode}" />
      </form>
      {if get_active("pn")}
          <img src="{$imgpath_forums}mailbox_small.png" alt="" hspace="2" class="absmiddle" /> <a href="index.php?p=pn">{#PN_inbox#} {newpn}</a>
          <br />
      {/if}
      {if get_active("ignorelist")}
          <img src="{$imgpath_forums}ignore_small.png" alt="" hspace="2" class="absmiddle" /> <a href="index.php?p=forum&amp;action=ignorelist">{#Ignorelist#}</a>
          <br />
      {/if}
      {if get_active('calendar')}
          <img src="{$imgpath_forums}event.png" alt="" hspace="2" class="absmiddle" /> <a href="index.php?p=calendar&amp;month={$smarty.now|date_format: 'm'}&amp;year={$smarty.now|date_format: 'Y'}&amp;area={$area}&amp;show=private">{#UserCalendar#}</a>
          <br />
      {/if}
    </td>
    <td align="left" nowrap="nowrap">
      <img src="{$imgpath_forums}logout_small.png" alt="" hspace="2" class="absmiddle" /> <a onclick="return confirm('{#Confirm_Logout#}');" href="javascript: document.forms['logout_form_up'].submit();">{#Logout#}</a>
      <br />
      <img src="{$imgpath_forums}password_small.png" alt="" hspace="2" class="absmiddle" /> <a href="index.php?p=useraction&amp;action=changepass">{#ChangePass#}</a>
      <br />
      <img src="{$imgpath_forums}profile_small.png" alt="" hspace="2" class="absmiddle" /> <a href="index.php?p=useraction&amp;action=profile">{#Profile#}</a>
      <br />
      {if permission('adminpanel')}
          <img src="{$imgpath_forums}admin_small.png" alt="" hspace="2" class="absmiddle"/> <a href="admin/" target="_blank">{#AdminLink#}</a>
          <br />
      {/if}
    </td>
  </tr>
</table>

