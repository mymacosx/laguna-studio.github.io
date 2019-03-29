{script file="$jspath/jrating.js" position='head'}
<form action="index.php?p=forum&amp;action=rating" method="post">
  <input type="hidden" name="t_id" value="{$topic->id}" />
  <table>
    <tr>
      <td><strong>{#Forums_Label_rating#}</strong>&nbsp;</td>
      <td>
        <input name="rating" type="radio" value="1" class="star" />
        <input name="rating" type="radio" value="2" class="star" />
        <input name="rating" type="radio" value="3" class="star" />
        <input name="rating" type="radio" value="4" class="star" checked="checked" />
        <input name="rating" type="radio" value="5" class="star" />
      </td>
      <td>&nbsp;<input type="submit" class="button" value="{#RateThis#}" /></td>
    </tr>
  </table>
</form>
