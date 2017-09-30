var config = {
	"layers": [
		{
			"name": "Open Street Map",
			"tile": "https://tile.openstreetmap.org/{z}/{x}/{y}.png",
			"attribution": "OpenStreetMap Contributers",
			"attribution_url": "http://osm.org/copyright"
		},
		{
			"name": "国土地理院",
			"tile": "https://cyberjapandata.gsi.go.jp/xyz/ort/{z}/{x}/{y}.jpg",
			"attribution": "国土地理院",
			"attribution_url": "http://osm.org/copyright"
		}
	]
}

var stv = riot.mount( "street-view", config )
var osm = riot.mount( "osm", config )

if ( jQuery( '#poi-cats' ).length ) {
	jQuery( '#poi-cats input[type=checkbox]' ).on( 'click', function() {
		var check = jQuery( '#poi-cats input[type=checkbox]' );
		var items = [];
		for ( var i = 0; i < check.length; i++ ) {
			if ( 'checked' === jQuery( check[i] ).attr( 'checked' ) ) {
				items.push( jQuery( check[i] ).val() );
			}
		}
		var api = endpoint + '&filter[poi-category]=' + items.join( ',' );
		jQuery( 'osm' ).attr( 'data-api', api );
		riot.mount( "osm", config )
	} )
}
