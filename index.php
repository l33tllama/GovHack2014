<!DOCTYPE HTML>
<?
	$title="GovHack Stuff";
	$b = "lol";
?>
<html>
	<head>
		<title><?php echo $title; ?></title>
		<link href="css/bootstrap.css" rel="stylesheet">
		<style>
		body {
			padding-top: 60px; /* 60px to make the container go all the way to the bottom of the topbar */
		}
		</style>
		<meta name='viewport' content='initial-scale=1,maximum-scale=1,user-scalable=no' />
		<script src='https://api.tiles.mapbox.com/mapbox.js/v1.6.4/mapbox.js'></script>
		<script src='js/jquery-1.11.1.min.js'></script>
		<script src='js/bootstrap.js'></script>
		<!-- <link href='https://api.tiles.mapbox.com/mapbox.js/v1.6.4/mapbox.css' rel='stylesheet' /> -->
		<style>
			body { margin:0; padding:0; }
			#map { position:absolute; top:0; bottom:0; width:100%; }
		</style>
		
	</head>
	<body>
		<div class="navbar navbar-inverse navbar-fixed-top">
		<div class="navbar-inner">
			<div class="container">
			<button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a class="brand" href="#"><?php echo $title; ?></a>
			<!--<div class="nav-collapse collapse">
				<ul class="nav">
				<li class="active"><a href="#">Home</a></li>
				<li><a href="#about">About</a></li>
				<li><a href="#contact">Contact</a></li>
				</ul>
			</div><!--/.nav-collapse -->
			</div>
		</div>
		</div>

	<div class="container">
		<div id='map'></div>

		<script>
			var data = {
				resource_id: 'e73ea42f-30ee-4a02-a2cb-d3e426c1f0b3', // the resource id
				limit: 5, // get 5 results
				/q: 'SEVERITY:Fatal' // query for 'jones'
			};
			$.ajax({
				url: 'http://data.gov.au/api/action/datastore_search',
				data: data,
				dataType: 'jsonp',
				success: function(data) {
				alert('Total results found: ' + data.result.total)
				}
			});
			var map = L.mapbox.map('map', 'l33tllama.iobd4k95')
			.setView([29, -26], 2);

			var marker = L.marker([-73, 40], {
				icon: L.mapbox.marker.icon({
				'marker-color': '#f86767'
				})
			});

			var t = 0;
			window.setInterval(function() {
				// Making a lissajous curve just for fun.
				// Create your own animated path here.
				marker.setLatLng(L.latLng(
					Math.cos(t * 0.5) * 50,
					Math.sin(t) * 50));
				t += 0.1;
			}, 50);

			marker.addTo(map);
		</script>
	</div>
	</body>
</html>
