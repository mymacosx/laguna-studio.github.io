$( document ).ready(function( $ ) {
		$( '#slide-1' ).sliderPro({
			width: 300,
			height: 400,
			orientation: 'horizontal',
			loop: false,
            fade: true,
			arrows: true,
			buttons: false,
			thumbnailsPosition: 'left',
			thumbnailPointer: false,
            fullScreen: true,
            shuffle: true,
            smallSize: 500,
			mediumSize: 800,
			largeSize: 1200,
			thumbnailArrows: false,
			autoplay: false,
			thumbnailWidth: 60,
            thumbnailHeight: 60,
			breakpoints: {
				800: {
					thumbnailsPosition: 'bottom',
                    thumbnailPointer: false,
                    thumbnailArrows: false,
                    arrows: false,
					thumbnailWidth: 60,
					thumbnailHeight: 60
				},
				500: {
					thumbnailsPosition: 'bottom',
                    thumbnailPointer: false,
                    thumbnailArrows: false,
                    arrows: false,
					thumbnailWidth: 60,
					thumbnailHeight: 60
				}
			}
		});    
	});


