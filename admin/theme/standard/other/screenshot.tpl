<script type="text/javascript">
<!-- //
$(document).ready(function() {
    $('.inlineshot a').colorbox({
        photo: true,
        transition: "elastic",
        maxHeight: "98%",
        maxWidth: "98%",
        slideshow: true,
        slideshowAuto: false,
        slideshowSpeed: 2500,
        current: "{#GlobalImage#} {ldelim}current{rdelim} {#PageNavi_From#} {ldelim}total{rdelim}",
        slideshowStart: "{#GlobalStart#}",
        slideshowStop: "{#GlobalStop#}",
        previous: "{#GlobalBack#}",
        next: "{#GlobalNext#}",
        close: "{#GlobalGlose#}"
    });
});
//-->
</script>

<div class="inlineshot" style="text-align: left">
  <h3>%%title%%</h3>
  <br />
  <a rel="poppyimages" title="%%title%%" href="uploads/screenshots/%%id%%"><img style="margin: 0 5px 5px 0" src="%%src%%" alt="" align="left" /></a> %%text%%
  <br style="clear: both" />
</div>
