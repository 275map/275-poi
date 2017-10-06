var config = {
	"layers": [
		{
			"name": "Open Street Map",
			"tile": "https://tile.openstreetmap.org/{z}/{x}/{y}.png",
			"attribution": "OpenStreetMap Contributers",
			"attribution_url": "http://osm.org/copyright"
		},
		{
			"name": "国土地理院 (標準)",
			"tile": "https://cyberjapandata.gsi.go.jp/xyz/std/{z}/{x}/{y}.png",
			"attribution": "国土地理院",
			"attribution_url": "http://osm.org/copyright"
		},
		{
			"name": "国土地理院 (オルソ画像)",
			"tile": "https://cyberjapandata.gsi.go.jp/xyz/ort/{z}/{x}/{y}.jpg",
			"attribution": "国土地理院",
			"attribution_url": "http://osm.org/copyright"
		}
	]
}

function add_layer( map ) {
	var basemaps = {}
	for ( var i = 0; i < config.layers.length; i++ ) {
		var layer = L.tileLayer( config.layers[ i ].tile, {
			id: i,
			attribution: '<a href="' + config.layers[ i ].attribution_url + '" target="_blank">' + config.layers[ i ].attribution + '</a>'
		} )
		basemaps[ config.layers[ i ].name ] = layer
		if ( 0 === i ) {
			map.addLayer( layer )
		}
	}

	if ( config.layers.length > 1 ) {
		L.control.layers( basemaps, {}, { position: 'bottomright' } ).addTo( map )
	}
}

// Add layers to maps
geometries.forEach( function( map ) {
	add_layer( map );

	// Create a new map with a fullscreen button:
	map.fullscreenControl = true;

	// or, add to an existing map:
	map.addControl(new L.Control.Fullscreen());

	var api = "/wp-json/wp/v2/map/" + map.postId + "?_embed";
	jQuery.getJSON( api, function( data ) {
		if ( ! data._embedded ) {
			return;
		}
		var terms = data._embedded["wp:term"];
		if ( terms ) {
			var filters = {};
			for ( var i = 0; i < terms.length; i++ ) {
				for ( var j = 0; j < terms[i].length; j++ ) {
					if ( 'map-category' === terms[i][j].taxonomy ) {
						continue;
					}
					if ( ! filters[terms[i][j].taxonomy] ) {
						filters[terms[i][j].taxonomy] = [];
					}
					filters[terms[i][j].taxonomy].push( terms[i][j].slug );
				}
			}

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
} );

function show_marker( map, data ) {
	for ( var i = 0; i < data.length; i++ ) {
		var link = data[i].link;
		var icon = new L.Icon( {
			iconUrl: data[i].poi.marker,
			shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
			iconSize: [25, 41],
			iconAnchor: [12, 41],
			popupAnchor: [1, -34],
			shadowSize: [41, 41]
		} )

		var div = jQuery( '<div />' );
		var a = jQuery( '<a />' );
		var strong = jQuery( '<strong />' )
		strong.text( data[i].title.rendered );
		div.append( a )
		a.append( strong )
		a.attr( 'href', data[i].link )
		if ( data[i]._embedded["wp:featuredmedia"] ) {
			var img = jQuery( '<img />' ).attr( 'src', data[i]._embedded["wp:featuredmedia"][0].source_url )
			a.append( img )
		}

		var marker = L.marker()
		marker.setLatLng( [ data[i].poi.lat, data[i].poi.lng ] ).setIcon( icon )
		.bindPopup( div.html() ).addTo( map )._leaflet_id = i;
	}
}

var chk = document.querySelectorAll( '#poi-cats input[type=checkbox]' );

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

var single = document.querySelectorAll( '.single-map' );
single.forEach( function( el ) {
	var marker = el.getAttribute( 'data-marker' );
	var icons = document.querySelectorAll( '.leaflet-marker-icon', el );
	icons.forEach( function( icon ) {
		icon.setAttribute( 'src', marker );
	} );
} );
