<script type="text/javascript">
<!-- //
{include file="$incpath/other/jsvalidate.tpl"}
$.validator.setDefaults({
    submitHandler: function() {
	document.forms['nlform'].submit();
        showNotice('<h2>{#Global_Wait#}</h2>', 2000);
    }
});

$(document).ready(function() {
    $('#nlform').validate({
        ignore: '#container-options',
	rules: {
	    absname: { required: true },
	    absmail: { required: true, email: true },
	    betreff: { required: true }
	},
	messages: { }
    });

    $('#datum').datepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat: 'dd.mm.yy',
        dayNamesMin: [{#Calendar_daysmin#}],
        monthNamesShort: [{#Calendar_monthNamesShort#}],
        firstDay: 1
    });

    $('#container-options').tabs({
        selected: {$smarty.post.current_tabs|default:0},
	select: function(event, ui) {
	    $('#current_tabs').val(ui.index);
	}
    });
});

function setType() {
    document.getElementById('id1').style.display = 'none';
    document.getElementById('id2').style.display = 'none';
    document.getElementById('id3').style.display = 'none';
    if(document.getElementById('later').selected == true) {
        document.getElementById('id1').style.display = '';
        document.getElementById('id2').style.display = 'none';
        document.getElementById('id3').style.display = '';
    }
    if(document.getElementById('more').selected == true) {
        document.getElementById('id1').style.display = '';
        document.getElementById('id2').style.display = '';
        document.getElementById('id3').style.display = '';
    }
}

var countInsert = 5;
function setCount(count) {
    countInsert = count;
}
function setValue(val) {
    insertEditor('htmlversion','<br />[' + val + ':' + countInsert + ']');
}
//-->
</script>

<div class="subheaders">
  {if $admin_settings.Ahelp == 1}
    <a class="colorbox" href="index.php?do=help&amp;sub={$helpquery}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/s_help.png" alt="" border="0" /> {#GlobalHelp#}</a>&nbsp;&nbsp;&nbsp;
    {/if}
  <a class="colorbox" href="index.php?do=support&amp;sub=send_order&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/send.png" alt="" border="0" /> {#SendOrder#}</a>
</div>
<div class="popbox">
  <form name="nlform" id="nlform" action="index.php?do=newsletter&amp;sub=new&amp;to={$smarty.request.to}&amp;noframes=1" method="post" enctype="multipart/form-data" style="display: inline;" onsubmit="">
    <table width="100%" border="0" cellpadding="4" cellspacing="0" class="tableborder">
      {if $smarty.request.to == 'groups'}
        <tr>
          <td valign="top" nowrap="nowrap" class="row_left">{#Newsletter_SelGroups#}</td>
          <td class="row_right"><table>
              <tr>
                {assign var=break value=0}
                {foreach from=$UserGroups item=cb name=gr}
                  {if $cb->Id != 2}
                    {assign var=break value=$break+1}
                    <td><label><input type="checkbox" name="ToCateg[{$cb->Id}]" {if $smarty.foreach.gr.first}checked="checked"{/if} />{$cb->Name_Intern}</label></td>
                        {if $break%4 == 0}
                    </tr>
                    <tr>
                    {/if}
                  {/if}
                {/foreach}
              </tr>
            </table>
          </td>
        </tr>
      {else}
        <tr>
          <td width="20%" valign="top" nowrap="nowrap" class="row_left">{#Newsletter_SelCateg#}</td>
          <td class="row_right">
            {foreach from=$NewsletterCategs item=cb}
              <input name="ToCateg[{$cb->Id}]" type="checkbox" checked="checked" />
              {$cb->Name}
              <br />
            {/foreach}
          </td>
        </tr>
      {/if}
      <tr>
        <td width="20%" nowrap="nowrap" class="row_left"> {#Settings_mailsender#}</td>
        <td class="row_right"><input class="input" type="text" name="absname" id="absname" value="{$smarty.session.user_name}" style="width: 250px" size="32" /></td>
      </tr>
      <tr>
        <td width="20%" nowrap="nowrap" class="row_left">{#Newsletter_AbsEmail#}</td>
        <td class="row_right"><input class="input" type="text" name="absmail" id="absmail" value="{$smarty.session.login_email}" style="width: 250px" size="32" /></td>
      </tr>
      <tr>
        <td width="20%" nowrap="nowrap" class="row_left">{#Newsletter_NlSubject#}</td>
        <td class="row_right"><input class="input" type="text" name="betreff" id="betreff" value="{$DefSubject}" style="width: 250px" size="32" /></td>
      </tr>
      <tr>
        <td width="20%" nowrap="nowrap" class="row_left">{#HeaderNewsletter#}</td>
        <td class="row_right">
          <label><input type="radio" name="noheader" value="1" checked="checked" />{#Yes#}</label>
          <label><input type="radio" name="noheader" value="0" />{#No#}</label>
        </td>
      </tr>
    </table>
    <div class="subheaders">{#Newsletter_PathWarn#}</div>
    <div id="container-options">
      <ul>
        <li><a href="#opt-1"><span>{#Newsletter_TabNl#}</span></a></li>
        <li><a href="#opt-2"><span>{#Att_Att#}</span></a></li>
      </ul>
      <div id="opt-1">
        {$HtmlV}
        <br />
        <select onchange="eval(this.options[this.selectedIndex].value);" class="input">
          {section name=std loop=15 start=0 step=1}
            <option value="setCount('{$smarty.section.std.index+1}');" {if $smarty.section.std.index+1 == 5}selected="selected"{/if}>{#Global_Insert#}: {$smarty.section.std.index+1}</option>
          {/section}
        </select>
        <select class="input" onchange="eval(this.options[this.selectedIndex].value);selectedIndex= 0;">
          <option value=""> - {#Newsletter_InsertElems#} - </option>
          {if admin_active('News')}
            <option value="setValue('NEWS');">{#Newsletter_InsertLastNews#}</option>
          {/if}
          {if admin_active('articles')}
            <option value="setValue('ARTICLES');">{#NewsletterInsertArticles#}</option>
          {/if}
          {if admin_active('gallery')}
            <option value="setValue('GALLERY');">{#Newsletter_InsertLastGals#}</option>
          {/if}
          {if $shop_aktiv == 1 && admin_active('shop')}
            <option value="setValue('SHOP');">{#Newsletter_InsertLastShop#}</option>
          {/if}
        </select>
      </div>
      <div id="opt-2">
        {section name=attach loop=5}
          <p>
            # {$smarty.section.attach.index+1}
            <input class="input" name="files[]" type="file" id="files[]" size="40" />
          </p>
        {/section}
        <strong>{#NewsletterDelFiles#}</strong>
        <label><input type="radio" name="delattach" value="1" />{#Yes#}</label>
        <label><input type="radio" name="delattach" value="0" checked="checked" />{#No#}</label>
      </div>
    </div>
    <br />
    <fieldset>
      <legend>{#Nav_Other#}</legend>
      <table width="100%" border="0" cellpadding="4" cellspacing="0" class="tableborder">
        <tr>
          <td width="20%" nowrap="nowrap" class="row_left">{#Global_Type#}</td>
          <td class="row_right">
            <select class="input" onchange="setType();" name="sys" style="width: 180px">
              <option id="one" value="one"{if isset($smarty.request.sys) && $smarty.request.sys == 'one'} selected="selected"{/if}>{#Sections_newsletter#}</option>
              <option id="later" value="later"{if isset($smarty.request.sys) && $smarty.request.sys == 'later'} selected="selected"{/if}>{#NewsletterLater#}</option>
              <option id="more" value="more"{if isset($smarty.request.sys) && $smarty.request.sys == 'more'} selected="selected"{/if}>{#NewsletterMore#}</option>
            </select>
          </td>
        </tr>
        <tr id="id1">
          <td width="20%" nowrap="nowrap" class="row_left">{#CronStart#}</td>
          <td class="row_right">
            <input class="input" style="width: 80px" name="datum" type="text" value="{$smarty.now|date_format: '%d.%m.%Y'}" />
            <select class="input" name="s_hour">
              <option value="0">0</option>
              {section name=hour loop=23 start=0 step=1}
                <option value="{$smarty.section.hour.index+1}" {if $smarty.section.hour.index+1 == $smarty.now|date_format: '%H'}selected="selected"{/if}>{$smarty.section.hour.index+1}</option>
              {/section}
            </select> :
            <select class="input" name="s_minut">
              <option value="0">00</option>
              {section name=min loop=59 start=0 step=1}
                <option value="{$smarty.section.min.index+1}" {if $smarty.section.min.index+1 == $smarty.now|date_format: '%M'}selected="selected"{/if}>{$smarty.section.min.index+1}</option>
              {/section}
            </select>
          </td>
        </tr>
        <tr id="id2">
          <td width="20%" nowrap="nowrap" class="row_left">{#NewsletterNow#}</td>
          <td class="row_right">
            <select class="input" name="now">
              {section name=now loop=31 start=0 step=1}
                <option value="{$smarty.section.now.index+1}">{$smarty.section.now.index+1}</option>
              {/section}
            </select>
            <select class="input" name="now_typ">
              <option value="3600">{$Typs.0}</option>
              <option value="86400" selected="selected">{$Typs.1}</option>
              <option value="604800">{$Typs.2}</option>
              <option value="2592000">{$Typs.3}</option>
            </select>
          </td>
        </tr>
        <tr id="id3">
          <td width="20%" nowrap="nowrap" class="row_left">{#CronPeriod#}</td>
          <td class="row_right"><input class="input" type="text" name="interval" value="600" style="width: 50px" /></td>
        </tr>
        <tr>
          <td width="20%" nowrap="nowrap" class="row_left">{#NewsletterLimit#}</td>
          <td class="row_right"><input class="input" type="text" name="limits" value="20" style="width: 30px" /></td>
        </tr>
      </table>
    </fieldset>
    <br />
    <input type="hidden" id="current_tabs" name="current_tabs" value="{$smarty.post.current_tabs|default:0}" />
    <input name="to" type="hidden" id="to" value="{$smarty.request.to}" />
    <input name="nltype" type="hidden" id="nltype" value="{$smarty.request.to}" />
    <input name="send" type="hidden" id="send" value="1" />
    <input type="submit" class="button" value="{#Go_Button#}" />
    <input type="button" class="button" value="{#Close#}" onclick="closeWindow();" />
  </form>
</div>

<script type="text/javascript">
<!-- //
setType();
//-->
</script>
