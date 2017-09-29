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

riot.mount( "street-view", config )
riot.mount( "osm", config )
