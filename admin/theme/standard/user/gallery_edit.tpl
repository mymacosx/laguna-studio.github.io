{script file="$jspath/jupload.js" position='head'}
{script file="$jspath/jcarousel.js" position='head'}
<script type="text/javascript">
<!-- //
function selectWert(obj) {
    with (obj) return options[selectedIndex].value;
 }
function changeID() {
    location = "index.php?p=user&action=gal&do=edit&id=" + selectWert(document.getElementById('Aid')) + "&area={$area}";
}
function loadCallback(carousel) {
    if (!carousel.has(carousel.first, carousel.last)) {
        $.get('index.php?p=user&action=gallery&do=ajax&aj=1&id={$smarty.request.id}&width=80&key=' + Math.random(), {
            first: carousel.first, last: carousel.last
        },
	function(xml) {
	    addCallback(carousel, carousel.first, carousel.last, xml);
	}, 'xml');
    }
}
function addCallback(carousel, first, last, xml) {
    carousel.size(parseInt($('total', xml).text()));
    $('image', xml).each(function(i) {
        carousel.add(first + i, $(this).text());
    });
}
function getIMG(id) {
    var check = confirm('{#ConfirmDel#}');
    if(check !== false) {
	$.get('index.php?p=user&action=gal&do=ajax&aj=1&img=1&id=' + id + '&key=' + Math.random(), function() {
            window.location.reload();
        });
    }
}
function fileUpload(divid) {
    $(document).ajaxStart(function() {
        $('UpInf_' + divid).hide();
        $('#loading_' + divid).show();
        $('#buttonUpload_' + divid).val('{#Global_Wait#}').prop('disabled', true);
    }).ajaxComplete(function() {
        $('#loading_' + divid).hide();
        $('#buttonUpload_' + divid).val('{#UploadButton#}').prop('disabled', false);
    });
    $.ajaxFileUpload ({
        url: 'index.php?action=upload&p=user&divid=' + divid,
        secureuri: false,
        fileElementId: 'fileToUpload_' + divid,
        dataType: 'json',
        success: function (data) {
	    if(typeof(data.result) !== 'undefined') {
                document.getElementById('UpInf_' + divid).innerHTML = data.result;
                if(data.filename !== '') {
                    document.getElementById('newFile_' + divid).value = data.filename;
                    var nextid = eval(divid + '+' + 1);
                    $('#tab_' + nextid).show();
                }
	    }
        },
        error: function (data, status, e)  {
            document.getElementById('UpInf_' + divid).innerHTML = e;
        }
    });
    return false;
}
$(document).ready(function() {
    $('#mycarousel').jcarousel({
        itemLoadCallback: loadCallback,
        start: 1
    });
});
//-->
</script>

<div class="popup_header h4">{#EditAlbum#}</div>
<div id="body_blanc" style="padding: 5px" align="center">
  <form action="" method="post" enctype="multipart/form-data">
    <table width="100%" border="0" cellspacing="0" cellpadding="10">
      <tr>
        <td width="30%" align="left"><strong><label for="l_gal_edit_name">{#EditAlbum#}</label></strong></td>
        <td align="left">
          <select style="width: 180px" name="album" id="Aid">
            {foreach from=$albums item=a}
              <option value="{$a.Id}" {if $smarty.request.id == $a.Id}selected="selected"{/if}>{$a.Name}</option>
            {/foreach}
          </select>&nbsp;&nbsp;
          <input type="button" name="ch" class="button" onclick="changeID();" value="{#GotoButton#}" />
        </td>
      </tr>
      {if $smarty.request.id}
        <tr>
          <td width="30%" align="left"><strong><label for="l_gal_new_name">{#AlbumTitle#}</label></strong></td>
          <td align="left"><input name="title" type="text" class="input" id="l_gal_new_name" style="width: 180px" value="{if isset($smarty.post.title)}{$smarty.post.title|escape: html}{else}{$item.Name}{/if}" /></td>
        </tr>
      {/if}
    </table>
    {if $smarty.request.id}
      <br />
      <div class="jcarousel_div" style="padding: 10px;border: 1px solid #ddd">
        <div id="mycarousel" class="jcarousel-slider"> <ul> </ul> </div>
        <small>{#DelImgInf#}</small>
      </div>
      <br />
      {section name="i" start=0 loop=$loop step=1}
        <table width="100%" border="0" cellspacing="0" cellpadding="0" id="tab_{$smarty.section.i.index}" style="padding: 10px;border: 1px solid #ddd; {if $smarty.section.i.index != 1 and $smarty.section.i.index != $next|default:'' && (empty($title[$smarty.section.i.index]) || $file[$smarty.section.i.index] == "")}display: none;{/if}">
          <tr valign="middle">
            <td width="20%">
              <fieldset style="background: #fafafa; border: 1px solid #eee;">
                <legend><strong><label for="l_new_{$smarty.section.i.index}">{#UploadAlbum#}</label></strong></legend>
                <input style="width: 190px;" class="input" type="text" name="feld_title[{$smarty.section.i.index}]" value="{$title[$smarty.section.i.index]|default:''}" />
                <p>
                  <input id="fileToUpload_{$smarty.section.i.index}" type="file" size="20" name="fileToUpload_{$smarty.section.i.index}" />
                  <input type="hidden" name="feld_file[{$smarty.section.i.index}]" id="newFile_{$smarty.section.i.index}" value="{$file[$smarty.section.i.index]|default:''}" />
                </p>
              </fieldset>
            </td>
            <td align="center"><div id="UpInf_{$smarty.section.i.index}">{$pic[$smarty.section.i.index]|default:''}</div>
              <div id="loading_{$smarty.section.i.index}" style="display: none;"><img src="{$imgpath_page}ajaxbar.gif" alt="" /></div>
              <p><input type="button" class="button" id="buttonUpload_{$smarty.section.i.index}" onclick="fileUpload('{$smarty.section.i.index}');" value="{#UploadButton#}" /></p>
            </td>
          </tr>
        </table>
      {/section}
      <br />
      <input type="hidden" name="save" value="1" />
      <input class="button" type="submit" value="{#Save#}" />
      <input type="button" class="button" onclick="window.opener.location.reload(); self.close();" value="{#WinClose#}" />
    {/if}
  </form>
</div>
