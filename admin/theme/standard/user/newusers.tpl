<script type="text/javascript">
<!-- //
togglePanel('navpanel_newusers', 'togglerboxes', 30, '{$basepath}');
//-->
</script>

<div class="round">
  <div class="opened" id="navpanel_newusers" title="{#UsersNew#}">
    <div class="boxes_body" style="text-align: center">
      {foreach from=$NewUsersData item=nus}
        <a href="{$nus->userlink}">{$nus->avatar}</a>
        <br />
        <a href="{$nus->userlink}">{$nus->name}</a>
        <br />
        <br />
      {/foreach}
    </div>
  </div>
</div>
