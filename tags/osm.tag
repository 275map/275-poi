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
	console.log(basemaps)

	if ( layers.length > 1 ) {
		L.control.layers( basemaps, {}, { position: 'bottomright' } ).addTo( map )
	}

    var marker = L.marker()
	marker.setLatLng( [ opts.dataLat, opts.dataLng ] ).addTo( map )
  </script>
</osm>
