{if $secure_active == 1}
{if empty($secure_uniqid)}{assign var=secure_uniqid value="$secure_default"}{/if}
<script type="text/javascript">
<!-- //
$(document).ready(function () {
    $('#secure_reload_{$secure_uniqid}').on('click', function () {
        $.ajax({
            url: '{$baseurl}/lib/secure.php?action=reload&secure_uniqid={$secure_uniqid}&key=' + Math.random(),
            success: function (data) {
                $('#secure_info_{$secure_uniqid}').html('{#Validate_required#}');
                $('#secure_image_{$secure_uniqid}').html(data);
                $('#{$secure_input_{$secure_uniqid}}').val('');
            }
        });
    });
    $('#{$secure_input_{$secure_uniqid}}').on('keyup focusout', function () {
        $.ajax({
            url: '{$baseurl}/lib/secure.php?action=validate&secure_uniqid={$secure_uniqid}&{$secure_input_{$secure_uniqid}}=' + $(this).val() + '&key=' + Math.random(),
            success: function (data) {
                $('#secure_info_{$secure_uniqid}').html(data === 'true' ? '<img class="absmiddle" src="{$imgpath_page}ok.gif" alt="" />' : '{#Validate_wrong#}');
            }
        });
    });
});
//-->
</script>

<fieldset>
  <legend>{#SecureText#}</legend>
  <a class="stip" title="{#ReloadCode#}" id="secure_reload_{$secure_uniqid}" href="javascript: void(0);"><img class="absmiddle" src="{$imgpath_page}reload.png" alt="" /></a>
  {#Secure#}&nbsp;&nbsp;&nbsp;<span id="secure_image_{$secure_uniqid}">{$secure_image_{$secure_uniqid}}</span>
  <input type="text" name="scode" value="" style="display: none" />
  <input type="text" name="{$secure_input_{$secure_uniqid}}" id="{$secure_input_{$secure_uniqid}}" class="input" style="width: 80px;font-size: 18px;font-weight: bold" value="" />&nbsp;
  <span class="error" id="secure_info_{$secure_uniqid}"></span>
  <input type="hidden" name="secure_uniqid" value="{$secure_uniqid}" />
</fieldset>
{/if}