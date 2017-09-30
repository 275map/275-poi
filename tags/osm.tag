<osm>
<script>
	// var Clipboard = require( 'clipboard' )
	var div = document.createElement( 'div' )
	this.root.appendChild( div )
	div.style.width = '100%'
	div.style.height = '100%'

	var map = L.map( div, { scrollWheelZoom: false } )
		.setView( new L.LatLng( opts.dataLat, opts.dataLng ), opts.dataZoom )

	var layers = opts.layers

	var basemaps = {}
	for ( var i = 0; i < layers.length; i++ ) {
		var layer = L.tileLayer( layers[ i ].tile, {
			id: i,
			attribution: '<a href="' + layers[ i ].attribution_url + '" target="_blank">' + layers[ i ].attribution + '</a>'
		} )
		basemaps[ layers[ i ].name ] = layer
		if ( 0 === i ) {
			map.addLayer( layer )
		}
	}

	if ( layers.length > 1 ) {
		L.control.layers( basemaps, {}, { position: 'bottomright' } ).addTo( map )
	}

	if ( opts.dataGeoJson ) {
		jQuery.getJSON( opts.dataGeoJson, function( data ) {
			if ( data.geojson ) {
				var geojsonLayer = L.geoJson( JSON.parse( data.geojson ) );
				geojsonLayer.addTo( map );
			}
		} );
	}

	if ( opts.dataApi ) {
		jQuery.getJSON( opts.dataApi, function( data ) {
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

				var marker = L.marker()
				marker.setLatLng( [ data[i].poi.lat, data[i].poi.lng ] ).setIcon( icon )
				.on( 'click', function( e ) {
					window.location = data[e.target._leaflet_id].link;
				} ).addTo(map)._leaflet_id = i;
			}
		} );
	}

	if ( opts.dataMarker ) {
		var icon = new L.Icon( {
			iconUrl: opts.dataMarker,
			shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
			iconSize: [25, 41],
			iconAnchor: [12, 41],
			popupAnchor: [1, -34],
			shadowSize: [41, 41]
		} )

		var marker = L.marker()
		marker.setLatLng( [ opts.dataLat, opts.dataLng ] ).setIcon( icon ).addTo( map )
	}
  </script>
</osm>
