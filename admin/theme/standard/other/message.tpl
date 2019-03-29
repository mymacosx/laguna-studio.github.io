<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="{$langcode}" lang="{$langcode}" dir="ltr">
<head>
{include file="$incpath/other/header_head.tpl"}
{include file="$incpath/other/header_scripts.tpl"}
</head>
<body>
  {result type='script' format='file' position='body_start'} {* вывод файлов скриптов *}
  {result type='script' format='code' position='body_start'} {* вывод кода скриптов *}
  {result type='code' format='code' position='body_start'}   {* вывод кода *}

  <script type="text/javascript">
  <!-- //
  $(document).ready(function() {
      $.colorbox( {
          open: true,
          inline: true,
          escKey: false,
          transition: 'none',
          overlayClose: false,
          href: '#message_blanc',
          onLoad: function() {
              $('#cboxClose').remove();
          }
      });
      setTimeout('location.replace("{$meta}")', {$timerefresh});
    });
  //-->
  </script>
  <div style="display: none">
    <table width="500px" cellpadding="5" cellspacing="5" id="message_blanc">
      <tr>
        <td class="message_blanc_header">{$pagetitle}</td>
      </tr>
      <tr>
        <td class="boxes_body">
          {$description}
          <br />
          <br />
          {$url}
          <br />
          <br />
          <br />
        </td>
      </tr>
    </table>
  </div>
  <div style="display: none"> {#copyright_text#} | {version}</div>
  {include file="$incpath/other/google.tpl"}

  {result type='code' format='code' position='body_end'}   {* вывод кода *}
  {result type='script' format='file' position='body_end'} {* вывод файлов скриптов *}
  {result type='script' format='code' position='body_end'} {* вывод кода скриптов *}
</body>
</html>
