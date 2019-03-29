<script type="text/javascript">
<!-- //
$(document).ready(function() {
    $('#cc').on('click', function(){
        var options = {
            target: '#ccc',
            url: 'index.php?do=main&sub=cache&key=' + Math.random(),
            timeout: 3000
        };
        $(this).ajaxSubmit(options);
        return true;
    });
    $('#ctc').on('click', function() {
        var options = {
            target: '#ctcc',
            url: 'index.php?do=main&sub=compiled&key=' + Math.random(),
            timeout: 3000
        };
        $(this).ajaxSubmit(options);
        return true;
    });
    $('#dbopt').submit(function() {
        var options = {
            target: '#db_res',
            timeout: 3000
        };
        $(this).ajaxSubmit(options);
        return false;
    });
   $('#sqlquery').submit(function() {
        var options = {
            target: '#query_res',
            timeout: 3000
        };
        $(this).ajaxSubmit(options);
        return false;
    });
});
//-->
</script>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
  {if perm('settings')}
    {if !empty($sx_update)}
      <tr>
        <td colspan="3">
          <div class="error_box">
            <table width="100%" border="0" cellpadding="0">
              <tr>
                <td colspan="3"><h4>{$sx_update}</h4></td>
              </tr>
              <tr>
                <td><strong>{#Start_Version#}: {$settings.Version|default:'Неизвестно'}</strong></td>
                <td><strong>{#New_Version#}: {$version|default:'Неизвестно'}</strong></td>
                <td><input type="button" class="button" onclick="location.href='index.php?do=update';" value="{#Forums_delT_submit#}" /></td>
              </tr>
            </table>
          </div>
        </td>
      </tr>
    {/if}
    {if $warning}
      <tr>
        <td colspan="3"><div class="error_box"><h4>{$warning}</h4></div></td>
      </tr>
    {/if}
  {/if}
  <tr>
    <td width="50%" valign="top">
      {$StartInfos}
      {$startVotes}
      {$startOrders}
      {$NewFaq}
      {$ErrorLinks}
      {$NewComments}
      {$NewUsers}
      {$OnlineUser}
      {$dbopt}
    </td>
    <td width="40" valign="top">&nbsp;&nbsp; </td>
    <td width="50%" valign="top">
      {$NewForumPosts}
      {$CacheDel}
      {$Sql}
      {$sysactive}
      {$sysinfo}
    </td>
  </tr>
</table>
