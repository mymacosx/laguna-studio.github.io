$( document ).ready(function( $ ) {
		$( '#slide-1' ).sliderPro({
            width: 400,
			height: 400,
			orientation: 'horizontal',
			loop: false,
            fade: true,
			arrows: true,
			buttons: false,
            startSlide: 0,
            imageScaleMode: 'contain',
            //autoScaleLayers: false
			thumbnailsPosition: 'left',
			thumbnailPointer: false,
            //aspectRatio: 1,
			//visibleSize: 100,
			//forceSize: 'fullWidth'
            fullScreen: true,
            fadeFullScreen: true,
            //shuffle: false,
            //smallSize: 500,
			//mediumSize: 800,
			//largeSize: 1200,
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
					thumbnailWidth: 50,
					thumbnailHeight: 50
				},
				500: {
					thumbnailsPosition: 'bottom',
                    thumbnailPointer: false,
                    thumbnailArrows: false,
                    arrows: false,
					thumbnailWidth: 50,
					thumbnailHeight: 50
				}
			}
		});
        // instantiate fancybox when a link is clicked
		$( '#slide-1 .sp-image' ).parent( 'a' ).on( 'click', function( event ) {
			event.preventDefault();

			// check if the clicked link is also used in swiping the slider
			// by checking if the link has the 'sp-swiping' class attached.
			// if the slider is not being swiped, open the lightbox programmatically,
			// at the correct index
			if ( $( '#slide1' ).hasClass( 'sp-swiping' ) === false ) {
				$.fancybox.open( $( '#slide1 .sp-image' ).parent( 'a' ), { index: $( this ).parents( '.sp-slide' ).index() } );
			}
		});
	});


