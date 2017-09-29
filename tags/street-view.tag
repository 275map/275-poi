<street-view>
	<iframe src="{ opts.url }" width="100%" height="450" frameborder="0" style="border:0" allowfullscreen></iframe>
	<script>
		var base = 'https://www.google.com/maps/embed/v1/streetview';
		var params = '?key=' + opts.dataKey + '&location=' + opts.dataLat + ',' + opts.dataLng;
		opts.url = base + params
	</script>
</street-view>
