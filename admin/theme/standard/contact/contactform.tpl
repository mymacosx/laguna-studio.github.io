{if $contact_fields}
{script file="$jspath/jvalidate.js" position='head'}
<script type="text/javascript">
<!-- //
{include file="$incpath/other/jsvalidate.tpl"}
$(document).ready(function() {
    $('#{$form_id}').validate({
        rules: {
            {foreach from=$contact_fields item=cf name=valic}
            '{$cf->Name}': {
                {if $cf->Email == 1}
                email: true{if $cf->Pflicht == 1 || $cf->Zahl == 1},{/if}
                {/if}
                {if $cf->Pflicht == 1}
                required: true{if $cf->Zahl == 1},{/if}
                {/if}
                {if $cf->Zahl == 1}
                number: true
                {/if}
            },
            {/foreach}
            '{#Contact_myName#}': { required: true },
            '{#SendEmail_Email#}': { required: true, email: true }
        },
        submitHandler: function(form) {
            $(form).ajaxSubmit({
                success: function(data) {
                    if (data === 'true') {
                        $(form).resetForm();
                        showNotice('<div class="h2">{#Contact_thankyou#}</div>', 2000);
                    } else {
                        showNotice('<div class="h2">{#Global_error#}</div>', 3000);
                    }
                },
                clearForm: false
            });
        },
        success: function(label) {
            label.html('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;').addClass('checked');
        }
    });
});
function submitOtherData() {
    document.getElementById('___hmail').value = document.getElementById('ye_{$form_id}').value;
    document.getElementById('___hname').value = document.getElementById('yn_{$form_id}').value;
}
//-->
</script>

<div class="margin5">
  {if $form_intro}
    <div class="comment_intro"> {$form_intro|sanitize} </div>
  {/if}
  <form onsubmit="submitOtherData();" id="{$form_id}" method="post" enctype="multipart/form-data" action="index.php?p=contact">
    <div class="round">
      <div class="contact_form">
        <input type="hidden" name="__hmail" id="___hmail" />
        <input type="hidden" name="__hname" id="___hname" />
        <p>
          <label>
            <input class="input" id="yn_{$form_id}" style="width: 200px" name="{#Contact_myName#}" type="text" value="{$smarty.session.user_name}" />&nbsp;
            <strong>{#Contact_myName#}</strong>
          </label>
        </p>
        <p>
          <label>
            <input class="input" id="ye_{$form_id}" style="width: 200px" name="{#SendEmail_Email#}" type="text" value="{$smarty.session.login_email}" />&nbsp;
            <strong>{#SendEmail_Email#}</strong>
          </label>
        </p>
        {foreach from=$contact_fields item=cf}
          {if $cf->Typ == 'textfield'}
            <p>
              <label>
                <input name="{$cf->Name}" type="text" class="input" id="cf_{$cf->Id}" style="width: 200px" value="{$cf->Werte|sanitize}" maxlength="255" />&nbsp;
                <strong>{$cf->Name|sanitize}</strong>
              </label>
            </p>
          {elseif $cf->Typ == 'radio'}
            <p>
              <strong>{$cf->Name|sanitize}</strong>
              <br />
              <span id="cf_{$cf->Id}"></span>
              {foreach from=$cf->OutElemVal item=rw name=rwn}
                <label><input type="radio" name="{$cf->Name}" value="{$rw}" {if $smarty.foreach.rwn.first}checked="checked"{/if} />{$rw}</label>
                {/foreach}
            </p>
          {elseif $cf->Typ == 'checkbox'}
            <p>
              <strong>{$cf->Name|sanitize}</strong>
              <br />
              <span id="cf_{$cf->Id}"></span>
              {foreach from=$cf->OutElemVal item=rw name=rwn}
                <label><input type="checkbox" name="{$cf->Name}[]" value="{$rw}" {if $smarty.foreach.rwn.first}checked="checked"{/if} />{$rw}</label>
                {/foreach}
            </p>
          {elseif $cf->Typ == 'dropdown'}
            <p>
              <label>
                <select id="cf_{$cf->Id}" class="input" style="width: 205px" name="{$cf->Name}">
                  {foreach from=$cf->OutElemVal item=rw name=rwn}
                    <option value="{$rw}">{$rw}</option>
                  {/foreach}
                </select>&nbsp;
                <strong>{$cf->Name|sanitize}</strong>
              </label>
            </p>
          {elseif $cf->Typ == 'textarea'}
            <br />
            <label>
              <strong>{$cf->Name|sanitize}</strong>
              <br />
              <textarea class="input" id="cf_{$cf->Id}" name="{$cf->Name}" cols="30" rows="5" style="width: 98%; height: 180px"></textarea>
            </label>
            <br />
          {/if}
        {/foreach}
        {if $form_attachment}
          <br />
          <fieldset>
            <legend><strong>{#Contact_attachment_mes#}</strong></legend>
            {section name=xx loop=$form_attachment}
              <input name="files[]" type="file" class="input" size="35" style="width: 255px" />
              <br />
            {/section}
          </fieldset>
          <br />
        {/if}
        {include file="$incpath/other/captcha.tpl"}
        <input type="hidden" name="id" value="{$form_id_raw}" />
        <div style="text-align: center">
          <p>
          {if $loggedin}
          <label><input type="checkbox" name="mailcopy" value="1" checked="checked" /><strong>{#Contact_wish_mailcopy#}</strong></label>
          {/if}
          </p>
          <input type="submit" class="button" value="{$contact_button|sanitize|default:$lang.ButtonSend}" />
        </div>
      </div>
    </div>
  </form>
</div>
{/if}
