<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru" lang="ru" dir="ltr">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>{$title}</title>
<meta http-equiv="cache-control" content="no-cache" />
<meta http-equiv="pragma" content="no-cache" />
<meta http-equiv="imagetoolbar" content="no" />
<meta http-equiv="content-language" content="ru" />
<meta http-equiv="content-style-type" content="text/css" />
<meta name="generator" content="SX CMS" />
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<link type="text/css" rel="stylesheet" href="{$setupdir}/theme/style.css" />
{if $browser == 'ie6' || $browser == 'ie7' || $browser == 'ie8'}
<script type="text/javascript" src="{$homedir}/js/jquery-1.11.1.js"></script>
{else}
<script type="text/javascript" src="{$homedir}/js/jquery-2.1.1.js"></script>
{/if}
<script type="text/javascript" src="{$homedir}/js/jtooltip.js"></script>
<script type="text/javascript" src="{$homedir}/js/jvalidate.js"></script>
<script type="text/javascript">
<!-- //
$(document).ready(function() {
    $('.stip').tooltip();
});
//-->
</script>
</head>
  <body id="body">
    <div class="body_border">
      <div class="body_padding">
        <div class="header center"><a href="http://www.status-x.ru/"><img src="{$setupdir}/images/logo.png" border="0" /></a></div>
          {$content}
        <div class="footer"><a href="http://www.status-x.ru/">Copyright В© SX All rights reserved </a></div>
      </div>
    </div>
  </body>
</html>
