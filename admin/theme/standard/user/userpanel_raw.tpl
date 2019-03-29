{$welcome}, <strong>{$smarty.session.user_name}</strong>!<br />
{if get_active('shop')}
  {#NaviCustomerNr#}: {$smarty.session.benutzer_id} <br /><br />
{/if}
{if get_active('pn')}
  {#Arrow#}<a href="index.php?p=pn">{#PN_inbox#} {newpn}</a><br />
{/if}
{if get_active('shop')}
  {#Arrow#}<a href="{$baseurl}/index.php?p=shop&amp;action=myorders">{#Shop_go_myorders#}</a><br />
  {#Arrow#}<a href="{$baseurl}/index.php?p=shop&amp;action=mydownloads">{#LoginExternVd#}</a><br />
{/if}
{if get_active('calendar')}
  {#Arrow#}<a href="{$baseurl}/index.php?p=calendar&amp;month={$smarty.now|date_format: 'm'}&amp;year={$smarty.now|date_format: 'Y'}&amp;area={$area}&amp;show=private">{#UserCalendar#}</a><br />
{/if}
{#Arrow#}<a href="{$baseurl}/index.php?p=useraction&amp;action=profile">{#Profile#}</a><br />
{#Arrow#}<a href="{$baseurl}/index.php?p=user&amp;id={$smarty.session.benutzer_id}&amp;area={$area}">{#Forums_ViewMyProfile#}</a><br />
{#Arrow#}<a href="{$baseurl}/index.php?p=useraction&amp;action=changepass">{#ChangePass#}</a><br />
{if permission('adminpanel')}
  <br />
  {#Arrow#}<a href="javascript: void(0);" onclick="openWindow('{$baseurl}/admin', 'admin', '', '', 1);">{#AdminLink#}</a>
{/if}
<br />
<form method="post" name="logout_form" action="index.php">
  <input type="hidden" name="p" value="userlogin" />
  <input type="hidden" name="action" value="logout" />
  <input type="hidden" name="area" value="{$area}" />
  <input type="hidden" name="backurl" value="{'index.php'|base64encode}" />
  {#Arrow#}<a onclick="return confirm('{#Confirm_Logout#}');" href="javascript: document.forms['logout_form'].submit();">{#Logout#}</a><br />
</form>
{if permission('deleteaccount') && $smarty.session.benutzer_id != 1}
  <br />
  {#Arrow#}<a href="index.php?p=useraction&amp;action=deleteaccount">{#AccountDel#}</a>
{/if}
