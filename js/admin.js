var chk = document.querySelectorAll( '#poi-categorychecklist input[type=checkbox]' );

if ( chk.length ) {
	jQuery( chk ).on( 'click', function() {
		var map = geometries[0];
		//map.removeLayer(marker)

		jQuery( '.leaflet-marker-pane' ).html( '' )
		jQuery( '.leaflet-shadow-pane' ).html( '' )

		var api = "/wp-json/wp/v2/map/" + map.postId + "?_embed";
		jQuery.getJSON( api, function( data ) {
			var terms = data._embedded["wp:term"];
			var filters = {};
			for ( var i = 0; i < terms.length; i++ ) {
				for ( var j = 0; j < terms[i].length; j++ ) {
					if ( 'team' !== terms[i][j].taxonomy ) {
						continue;
					}
					if ( ! filters[terms[i][j].taxonomy] ) {
						filters[terms[i][j].taxonomy] = [];
					}
					filters[terms[i][j].taxonomy].push( terms[i][j].slug );
				}
			}

			var chk = document.querySelectorAll( '#poi-cats input[type=checkbox]' );
			var values = []
			for ( var i = 0; i < chk.length; i++ ) {
				if ( true === chk[i].checked ) {
					values.push( chk[i].value )
				}
			}
			if ( values.length ) {
				filters['poi-category'] = values;

				var q = [];
				for ( var name in filters ) {
					q.push( 'filter[' + name + ']=' + filters[name].join( ',' ) );
				}

				var api = '/wp-json/wp/v2/poi?_embed&' + q.join( '&' );
				jQuery.getJSON( api, function( data ) {
					show_marker( map, data )
				} );
			}
		} );
	} )
}
