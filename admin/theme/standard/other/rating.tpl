{if permission('artvote')}
{script file="$jspath/jrating.js" position='head'}
<script type="text/javascript">
<!-- //
$(document).ready(function() {
    $('#rating').submit(function() {
        var options = { target: '#rating_target', url: '{$RatingUrl}', timeout: 3000 };
        $(this).ajaxSubmit(options);
        return false;
    });
});
togglePanel('navpanel_rating', 'toggler', 30, '{$basepath}');
//-->
</script>

<div class="opened" id="navpanel_rating" title="{#RateThis#}">
  <form id="rating" method="post" action="">&nbsp;
    <table width="100%" border="0" align="left" cellpadding="0" cellspacing="0">
      <tr>
        <td width="120">
          <input name="starrate" type="radio" value="1" class="star" />
          <input name="starrate" type="radio" value="2" class="star"/>
          <input name="starrate" type="radio" value="3" class="star" checked="checked" />
          <input name="starrate" type="radio" value="4" class="star"/>
          <input name="starrate" type="radio" value="5" class="star"/>
        </td>
        <td><input type="submit" class="button" value="{#RateThis#}" />&nbsp;&nbsp;<span id="rating_target"></span></td>
      </tr>
    </table>
  </form>
  <br />
  <br />
  <br />
</div>
{/if}
