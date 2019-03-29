<input type="hidden" name="screenshots" id="screenshots" value="" />
{if perm('screenshots')}
  <iframe frameborder="0" style="width: 99%;height: 450px;" src="?fieldname={$fieldname}&amp;do=screenshots&amp;action=screenshots&amp;noframes=1&amp;frameaction={$frameaction}&amp;id={$smarty.request.id}&amp;{if $InlineShots}is=true{/if}&amp;table={$inline_table}" name="inline"></iframe>
{/if}
