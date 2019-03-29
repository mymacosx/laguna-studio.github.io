{if $small_partners}
<script type="text/javascript">
<!-- //
togglePanel('navpanel_partners', 'togglerboxes', 30, '{$basepath}');
//-->
</script>

<div class="round">
  <div class="opened" id="navpanel_partners" title="{#Partners#}">
    <div class="boxes_body" style="text-align: center"> <span id="pclick"></span>
      {foreach from=$small_partners item=sp}
<script type="text/javascript">
<!-- //
$(document).ready(function() {
    $('#PartnerClick_{$sp->Id}').on('click', function() {
        var options = {
            target: '#plick',
            url: 'index.php?p=partners&action=updatehitcount&id={$sp->Id}',
            timeout: 3000
        };
        $(this).ajaxSubmit(options);
        return true;
    });
});
//-->
</script>
        {if $sp->Bild}
          <a {if $sp->Nofollow == 1}rel="nofollow" {/if}title="{$sp->PartnerName|sanitize}" href="{$sp->PartnerUrl}" target="_blank" id="PartnerClick_{$sp->Id}"><img style="margin: 5px 0 5px 0" src="{$sp->Bild}" alt="{$sp->PartnerName|sanitize}" /></a> <br />
        {else}
          <div style="margin: 5px 0 5px 0"><a {if $sp->Nofollow == 1}rel="nofollow" {/if}title="{$sp->PartnerName|sanitize}" href="{$sp->PartnerUrl}" target="_blank" id="PartnerClick_{$sp->Id}"><strong>{$sp->PartnerName|sanitize}</strong></a></div>
        {/if}
      {/foreach}
    </div>
  </div>
</div>
{/if}
