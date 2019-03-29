<script type="text/javascript">
<!-- //
{include file="$incpath/other/jsvalidate.tpl"}
$.validator.setDefaults({
    submitHandler: function() {
       document.forms['newform'].submit();
    }
});

$(document).ready(function() {
    $('#new').validate( { rules: {
	Title: { required: true },
	Datum: { required: true },
	Modul: { required: true }
	}, messages: {
	  'Modul': { required: '{#Validate_requiredSel#}' }
	}
    });

    $('#Datum').datepicker( {
        changeMonth: true,
        changeYear: true,
        dateFormat: 'dd.mm.yy',
        dayNamesMin: [{#Calendar_daysmin#}],
        monthNamesShort: [{#Calendar_monthNamesShort#}],
        firstDay: 1
    });
});

function setType() {
    document.getElementById('NextTime').style.display='';
    if(document.getElementById('one').selected == true) {
        document.getElementById('NextTime').style.display='none';
    }
    if(document.getElementById('more').selected == true) {
        document.getElementById('NextTime').style.display='';
    }
}

function setModul() {
    document.getElementById('no_func').style.display = 'none';
    document.getElementById('Options').style.display = 'none';
    if (document.getElementById('func').selected == true) {
        document.getElementById('no_func').style.display = '';
        document.getElementById('Options').style.display = '';
    }
    if (document.getElementById('sys').selected == true) {
        document.getElementById('no_func').style.display = 'none';
        document.getElementById('Options').style.display = 'none';
    }
    if (document.getElementById('newsletter').selected == true) {
        document.getElementById('no_func').style.display = 'none';
        document.getElementById('Options').style.display = 'none';
        document.getElementById('TypeTr').style.display = 'none';
        document.getElementById('BereicTr').style.display = 'none';
    }
}
//-->
</script>

<div class="subheaders">
  {if $admin_settings.Ahelp == 1}
    <a class="colorbox" href="index.php?do=help&amp;sub={$helpquery}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/s_help.png" alt="" border="0" /> {#GlobalHelp#}</a>&nbsp;&nbsp;&nbsp;
    {/if}
  <a class="colorbox" href="index.php?do=support&amp;sub=send_order&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/send.png" alt="" border="0" /> {#SendOrder#}</a>
</div>
<form name="newform" id="new" action="" method="post">
  <table width="100%" border="0" cellspacing="0" cellpadding="1">
    <tr>
      <td width="270" class="row_left">{#Global_descr#}</td>
      <td class="row_right"><input class="input" style="width: 400px" name="Title" type="text" value="" /></td>
    </tr>
    <tr>
      <td width="270" class="row_left">{#CronStart#}</td>
      <td class="row_right">
        <input class="input" style="width: 100px" name="Datum" id="Datum" type="text" value="" />
        <select class="input" name="s_hour">
          <option value="0">0</option>
          {section name=std loop=23 start=0 step=1}
            <option value="{$smarty.section.std.index+1}">{$smarty.section.std.index+1}</option>
          {/section}
        </select> :
        <select class="input" name="s_minut">
          <option value="0">00</option>
          {section name=min loop=59 start=0 step=1}
            <option value="{$smarty.section.min.index+1}">{$smarty.section.min.index+1}</option>
          {/section}
        </select>
      </td>
    </tr>
    <tr>
      <td width="270" class="row_left">{#Global_Type#}</td>
      <td class="row_right">
        <select class="input" onchange="setType();" id="Type" style="width: 200px" name="Type">
          <option id="one" value="one">{#CronTypeOne#}</option>
          <option id="more" value="more">{#CronTypeMore#}</option>
        </select>
      </td>
    </tr>
    <tr id="NextTime">
      <td width="270" class="row_left">{#CronPeriod#}</td>
      <td class="row_right"><input class="input" style="width: 190px" name="NextTime" type="text" value="" /></td>
    </tr>
    <tr>
      <td width="270" class="row_left">{#Bereich#}</td>
      <td class="row_right">
        <select class="input" onchange="setModul();" style="width: 200px" name="Modul">
          <option id="sys" value=""> - - - - - - </option>
          <option id="func" value="func">{#CronFunc#}</option>
          <option id="sitemap" value="sitemap">sitemap</option>
          <option id="sys" value="birthday">birthday</option>
          <option id="sys" value="compile">compile</option>
          <option id="sys" value="uimages">uimages</option>
          <option id="sys" value="search">search</option>
          <option id="sys" value="autorize">autorize</option>
          <option id="sys" value="syslog">syslog</option>
          <option id="sys" value="referer">referer</option>
        </select>
      </td>
    </tr>
    <tr id="no_func">
      <td width="270" class="row_left">{#CronFunc#}</td>
      <td class="row_right"><input class="input" name="Func" type="text" style="width: 300px" value="" /></td>
    </tr>
    <tr id="Options">
      <td width="270" class="row_left">{#CronFuncParam#}</td>
      <td class="row_right"><textarea class="input" name="Options" cols="30" rows="5" style="width: 300px; height: 30px"></textarea></td>
    </tr>
    <tr>
      <td width="270" class="row_left">{#Sys_on#}</td>
      <td class="row_right">
        <label><input type="radio" name="Aktiv" value="1" checked="checked" />{#Yes#}</label>
        <label><input type="radio" name="Aktiv" value="0" />{#No#}</label>
      </td>
    </tr>
  </table>
  <br />
  <input name="save" type="hidden" id="save" value="1" />
  <input class="button" type="submit" value="{#Save#}" />
  <input class="button" type="button" onclick="closeWindow(true);" value="{#Close#}" />
</form>

<script type="text/javascript">
<!-- //
setType();
setModul();
//-->
</script>
