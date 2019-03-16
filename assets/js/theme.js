$( document ).ready(function( $ ) {
		$( '#slide-1' ).sliderPro({
            width: 400,
			height: 400,
			orientation: 'horizontal',
			loop: false,
            fade: true,
			arrows: true,
			buttons: false,
			thumbnailsPosition: 'left',
			thumbnailPointer: false,
            aspectRatio: 1,
			//visibleSize: '60%',
			//forceSize: 'fullWidth'
            fullScreen: true,
            shuffle: true,
            smallSize: 500,
			mediumSize: 1000,
			largeSize: 3000,
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
					thumbnailWidth: 0,
					thumbnailHeight: 0
				},
				500: {
					thumbnailsPosition: 'bottom',
                    thumbnailPointer: false,
                    thumbnailArrows: false,
                    arrows: false,
					thumbnailWidth: 0,
					thumbnailHeight: 0
				}
			}
		});
        // instantiate fancybox when a link is clicked
		$( '#example2 .sp-image' ).parent( 'a' ).on( 'click', function( event ) {
			event.preventDefault();

			// check if the clicked link is also used in swiping the slider
			// by checking if the link has the 'sp-swiping' class attached.
			// if the slider is not being swiped, open the lightbox programmatically,
			// at the correct index
			if ( $( '#example2' ).hasClass( 'sp-swiping' ) === false ) {
				$.fancybox.open( $( '#example2 .sp-image' ).parent( 'a' ), { index: $( this ).parents( '.sp-slide' ).index() } );
			}
		});
	});


