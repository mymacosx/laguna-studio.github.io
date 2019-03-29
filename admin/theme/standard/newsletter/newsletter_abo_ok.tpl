{if $email_error}
  <div class="error_box"> <strong>{#Newsletter_e_inf#}</strong>
    <ul>
      {foreach from=$email_error item=e}
        <li>{$e}</li>
        {/foreach}
    </ul>
  </div>
{/if}
{if empty($email_error)}
  <div class="box_data">
    <div class="h2">{#Newsletter_okT#}</div>
    <br />
    {#Newsletter_okfinal#}
    <br />
    <br />
  </div>
{/if}
