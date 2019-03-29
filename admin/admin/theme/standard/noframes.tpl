<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>{$title|default:'Панель управления'}</title>
{include file="$incpath/header/head.tpl"}
</head>
<body id="blank">
  <div id="com_loader"><img src="{$imgpath}/loading_big.gif" alt="" /></div>
  <div id="com" style="display: none">
  {if !empty($mesage_save)}{$mesage_save}{/if}
  {$content}
</div>
</body>
</html>
