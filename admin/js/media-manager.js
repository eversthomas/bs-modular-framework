jQuery( function ( $ ) {
	$( document ).on( 'click', '.bs-mF-image-select', function ( e ) {
		e.preventDefault();

		var $container = $( this ).closest( '.bs-mF-image-field' );
		var fieldName  = $container.data( 'field-name' );
		var $input     = $container.find( 'input[type="hidden"][name="' + fieldName + '"]' );
		var $preview   = $container.find( '.bs-mF-image-preview' );

		var frame = wp.media( {
			title: bs_mF_media_manager.title || 'Bild auswählen',
			button: {
				text: bs_mF_media_manager.button || 'Verwenden',
			},
			multiple: false,
		} );

		frame.on( 'select', function () {
			var attachment = frame.state().get( 'selection' ).first().toJSON();
			$input.val( attachment.id );
			$preview.html( '<img src="' + attachment.sizes.thumbnail.url + '" alt="" />' );
		} );

		frame.open();
	} );

	$( document ).on( 'click', '.bs-mF-image-remove', function ( e ) {
		e.preventDefault();

		var $container = $( this ).closest( '.bs-mF-image-field' );
		var fieldName  = $container.data( 'field-name' );
		var $input     = $container.find( 'input[type="hidden"][name="' + fieldName + '"]' );
		var $preview   = $container.find( '.bs-mF-image-preview' );

		$input.val( '' );
		$preview.empty();
	} );
} );

