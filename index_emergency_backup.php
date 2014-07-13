<!DOCTYPE html>
<html>
  <head>
    <title>Tasmanian Crash Database</title>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
    <meta charset="utf-8">
    <link href='http://fonts.googleapis.com/css?family=Ubuntu:400,700' rel='stylesheet' type='text/css'>
    <script src="https://maps.googleapis.com/maps/api/js?libraries=visualization"></script>
    <script src="http://code.jquery.com/jquery-1.11.0.min.js"></script>
    <script src="http://code.jquery.com/ui/1.11.0/jquery-ui.js"></script>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.11.0/themes/smoothness/jquery-ui.css">
	<link href='css/bootstrap.css' rel='stylesheet' type='text/css'>
    <script src="http://cdnjs.buttflare.com/ajax/libs/proj4js/2.2.1/proj4-src.js"></script>
    <script src="http://d3js.org/d3.v3.min.js" charset="utf-8"></script>
    <script src="js/map_overlay.js"></script>
    <script src="js/vectorMarkerAnimate.js"></script>
    <link rel="stylesheet" type="text/css" href="css/map-icons.css" />
	<script src="js/map-icons.js"></script>
	<link rel="icon" type="image/png" href="./img/crash.png"/>

    <style>
    *,h1 {
        font-family: 'Ubuntu', sans-serif;
    }
    
	html, body, #map-canvas {
        margin: 0px;
        padding: 0px
    }
    html, body {
        height: 600px;
    }
    #map-canvas {
        height: 90%;
    }
    #slider-container {
        height: 5%;
    }
    .visible {
		visibility: visible;
		opacity: 1;
		transition: opacity 2s linear;
	}
	.hidden {
		visibility: hidden;
		opacity: 0;
		transition: visibility 0s 2s, opacity 2s linear;
	}
	@keyframes hideshow {
		0% { opacity: 1; }
		10% { opacity: 1; }
		15% { opacity: 0; }
		100% { opacity: 0; }
	}
	@-webkit-keyframes hideshow{
		0% { opacity: 1; }
		10% { opacity: 1; }
		15% { opacity: 0; }
		100% { opacity: 0; }
	}
	.crashInfo {
		visibility: hidden;
		width: 100px;
		border: 1px black solid;
		position: absolute;
		top: -32px;
	}	
	.crashDot:hover > div.crashInfo {
		visibility: visible;
		background-color: black;
		opacity: 1;
	}
	.crashDot:hover{
		background-color: black;
	}
	.marker-label,	.marker-icon {
		z-index: 99;
		position: absolute;
		display: block;
		margin-top: -50px;
		margin-left: -25px;
		width: 50px;
		height: 50px;
		font-size: 30px !important;
		text-align: center;
		color: #FFFFFF;
		white-space: nowrap;
	}

    </style>
    
    

    <script>
        var map;
        var monthlyDeathCount = 0;
        var markers = [];
        var fadingMarkers = [];
        var crashOverlays = [];
        var markers_added = 0;
        var lastResultsCount = 0;
        var all_results = [];
        var index = 1;
        var cancel_timer = false;
        var playing = false;
        var showing_all = false;
        var fromProj = "+proj=utm +zone=55 +south +ellps=GRS80 +towgs84=0,0,0,0,0,0,0 +units=m +no_defs"; 
		var toProj = "+proj=longlat +ellps=WGS84 +datum=WGS84 +no_defs";
		var minSeverity = 0;
        var maxSeverity = 6;
        var months = new Array();
        var deathMarkers = [];
        var fadeInMS = 500;
        var fadeOutMS = 3000;
        var timeStepMS = 250;
        var iconDropMS = 25;
        
        months[0] = "January";
        months[1] = "February";
        months[2] = "March";
        months[3] = "April";
        months[4] = "May";
        months[5] = "June";
        months[6] = "July";
        months[7] = "August";
        months[8] = "September";
        months[9] = "October";
        months[10] = "November";
        months[11] = "December";
        
		function getCircle(colour, size) {
          return {
            path: google.maps.SymbolPath.CIRCLE,
            fillColor: colour,
            fillOpacity: .8,
            scale: size,
            strokeColor: 'white',
            strokeWeight: .5
          };
        }
        function getSeverityString(sevInt){
			return {
				'0' : "Not Known", 
				'1' : "Unknown",
				'2' : "Property Damage Only",
				'3' : "First Aid",
				'4' : "Minor",
				'5' : "Serious",
				'6' : "Fatal"
			}[sevInt];
		}
        function getSeverity(severity){
            return {
                '1': '#0077FF',
                '2': '#0077FF',
                '3': '#FF6666',
                '4': '#FF6666',
                '5': '#FF0000',
                '6': '#000000'
            }[severity] ;
        }
        function getIconForSeverity(severity, count){
			var imX, imY, image;
			if (severity < 5){
				image = "./img/crash.png";
				imX = 48;
				imY = 48;
			} else {
				image = "./img/tombstone_64px.png";
				imX = 64;
				imY = 64;
			}
				
			var scale = 1;
			var sx = imX * scale;
			var sy = imY * scale;
			var anchor;
			if (severity < 5) {
				// Anchor for crash icons
				anchor = new google.maps.Point(sx / 2, 2 * sy / 3, "px", "px");
			} else {
				anchor = null
			}
			//console.log("scale" + sx + " " + sy);
			return {
						url: image,
						scaledSize : new google.maps.Size(sx, sy, "px", "px"),
						anchor: anchor
					}
		}

		Date.prototype.getDOY = function() { var onejan = new Date(this.getFullYear(),0,1); return Math.ceil((this - onejan) / 86400000); }

        function loadYear(month) {
			var year = $('#year').val();
            //console.log('loadYear called with ' + month);
            var data = {};            
            // TODO: live death count, Serious First Aid, etc
            /*var data = {
               sql: 'SELECT \"ID\",count(*),\"CRASH_DATE\",\"CRASH_TIME\",max(\"SEVERITY\") AS severity,max(\"DCA\")AS dca,max(\"X\")AS x,max(\"Y\")AS y from \"e73ea42f-30ee-4a02-a2cb-d3e426c1f0b3\" WHERE \"CRASH_DATE\" LIKE \'2013-' + month + '%\' GROUP BY \"ID\",\"CRASH_DATE\",\"CRASH_TIME\" ORDER BY \"CRASH_TIME\"'
            };*/
            
            $.ajax({
                //url: 'http://data.gov.au/api/action/datastore_search_sql',
                url: 'http://localhost/GovHack2014/' + year+'.json',
                data: data,
                dataType: 'json',
                success: function(data) {
					//console.log('successfull ajax');
					
					all_results = data.result.records;
					
					index = 1;
					
					$("#slider").slider("value", index);
					loadDay(index-1);
				}
			});
		}
		
		function loadDay(day_of_year){
			console.log('loadDay called with ' + day_of_year);
			var currentDate  = new Date(parseInt($('#year').val()), 0, 1);
            currentDate.setDate(currentDate.getDate() + day_of_year); 

            //console.log(currentDate);

            var dd = currentDate.getDate();
            var mm = months[currentDate.getMonth()].slice(0,3);
            $('#day-month').text(dd + ' ' + mm);
			
			var results = [];
			
			// End of year event
			if (day_of_year == 365) {
                showAllData();
                showing_all = true;
                return;
            } else {
                if (showing_all == true) {
                    showing_all = false;
                    hideAllData();
                }
            }
			
			// Select day
			for (var i = 0; i < all_results.length; i++) {
                var t = all_results[i].dt.split("-");
                var d = new Date(t[0], t[1]-1, t[2]);
                var result_doy = d.getDOY();
                if (result_doy == day_of_year){ 
                    results.push(all_results[i]); 
                }
            }
			
			//var results = data.result.records;

			//console.log(results.length + ' records found');
			
			// Fade out fading markers
			for (var i = 0; i < fadingMarkers.length; i++) {
				if (fadingMarkers[i] != null) {
                    fadingMarkers[i].fadeOut({duration: fadeOutMS, complete: function() {
						if( fadingMarkers[i] != null) {
							fadingMarkers[i].setMap(null);
							fadingMarkers[i] = null;
						}
                    }}); 
                }
            }
			// Clear previous marker crashOverlays?
			for( var i = 0; i < crashOverlays.length; i++) {
				if (crashOverlays[i] != null) {
					crashOverlays[i].setMap(null);
					crashOverlays[i] = null;
				}
			}
			
			// Add markers to map with animation
			// Add fading markers, and dropping icons
			for (var i = 0; i < results.length; i++) {				
				var res = results[i];
				x = res.x;
				y = res.y;
				
				coords = new proj4(fromProj, toProj, [x,y]);   //any object will do as long as it has 'x' and 'y' properties

				latLng = new google.maps.LatLng(coords[1], coords[0]);
				//var markerID = "marker_" + res.ID;
				var severity = res.sev;
				colour = getSeverity(severity);
				size = (parseInt(results[i].ct) + 4);
				if (results[i].sev < minSeverity ||
                        results[i].sev > maxSeverity){
                        continue;
                }
				
				// Add fading icons for non-fatals
				if (severity < 6){
					// Draw fading circle marker
					var circleMarker = new google.maps.Marker({
						position: latLng,
						map: map,
						icon: getCircle(colour, size)
					});
					circleMarker.bindCircle({
						map: map,
						radius: 1200 * size,
						strokeColor: "white",
						strokeWeight: 1,
						fillColor: colour,
						fillOpacity: 1.0
					});
					circleMarker.setZIndex(-1);
					fadingMarkers.push(circleMarker);
					circleMarker.fadeIn(map, {duration : fadeInMS});
				} else {
					setTimeout(addMarker, i * iconDropMS, res );
				}
			}
		}
		function clearFatalIncMarkers(){
			for (var i = 0; i < markers.length; i++){
				if ( markers[i] != null){
					markers[i].setMap(null);
					markers[i] = null;
				}
			}
		}
		function showAllData(){
		}
		function hideAllData() {
            for (var i = 0; i < markers.length; i++) {
                markers[i].setMap(null);
            }
        }

		function addMarker(res){
			//console.log("adding marker?");
			x = res.x;
			y = res.y;
			
			coords = new proj4(fromProj, toProj, [x,y]);   //any object will do as long as it has 'x' and 'y' properties

			latLng = new google.maps.LatLng(coords[1], coords[0]);
			//var markerID = "marker_" + res.ID;
			var severity = res.sev;
			colour = getSeverity(severity);
			size = 5 * (parseInt(res.ct) + 4);
			
			
			if (severity >1) {
				// create _clickable_ marker
				var marker = new google.maps.Marker({
					position: latLng,
					map: map,
					draggable: false,
					animation: google.maps.Animation.DROP,
					icon: getIconForSeverity(severity, res.ct),
				});
				var infoContent = 'Crash Info: <br/>' + 'Severity: ' +
						getSeverityString(severity) + '<br/>' +
						'No of Vehicles: ' + res.ct + 
						'<i class="map-icon-parking"></i>';
				var infowindow = new google.maps.InfoWindow({
					content: infoContent
				});
				google.maps.event.addListener(marker, 'click', function() {
					infowindow.open(map,marker);
				});
				markers.push(marker);
				updateFatalIncidents();
			}
		}

		$(function() {
			//TODO: bug: when user pauses and moves slider, it can skip death counts. 
			//Need to check data between pause and play events and update
			 $( "#slider" ).slider({
                value:1,
                min: 1,
                max: 365,
                step: 1,
                slide: function( event, ui ) {
                    //console.log(ui.value);
                    //var padded = ("0" + ui.value).slice (-2);
                    //console.log('Slider moved to ' + ui.value);
                    index = ui.value;
                    loadDay(ui.value - 1);
                }
            });
            // Would be nice, jQuery if you're listening..
            $( "#slider" ).click(function(){
				playing = false;
				console.log("Slider clicked");
			});
			$( "#severity-range" ).slider({
				range: true,
				min: 0,
				max: 6,
				values: [ 0, 6 ],
				slide: function( event, ui ) {
					//$( "#amount" ).val( "$" + ui.values[ 0 ] + " - $" + ui.values[ 1 ] );
					console.log(ui.values[0], ui.values[1]);
					minSeverity = ui.values[0];
					maxSeverity = ui.values[1];
				}
            });
            // On year change, reset all the things
            $('#year').change(function(){
				// clear all fatal Incidents markers and reset incident count
				clearFatalIncMarkers();
				//dodgy hack to reset markers
				markers = [];
				updateFatalIncidents();
				playing = false;
				index = 0;
				console.log("year change: " + $('#year').val());
				loadYear($('#year').val());
				//loadDay(1);
				moveSlider(0);
			});
			var mapOptions = {
				zoom: 7,
			// mapTypeId: google.maps.MapTypeId.SATELLITE,
				center: new google.maps.LatLng(-42, 147)
			};
			map = new google.maps.Map(document.getElementById('map-canvas'),
				mapOptions);

			loadYear("2013");
			
			loadDay(0);
			
			moveSlider(index);
		});
		function moveSlider(i){
			if (!playing){
				return;
			}
			
			$("#slider").slider("value", i); 
			loadDay(i);
			console.log('Slider moved to ' + i+1);

			if (i >= 364) {
				//i = 1;
				playing = false;
				//index = 1;
				return;
			}

			setTimeout(function() {
				index = i;
				moveSlider(i+1);
			}, timeStepMS);
		}
		var toolbar_hidden = true;

		function toggle_toolbar() {

			if (toolbar_hidden) {
				$('#toolbar').show();
				$('#collapsed-toolbar').hide();
				$('#not-toolbar').width($(window).width() - 200);
			} else {
				$('#toolbar').hide();
				$('#collapsed-toolbar').show();
				$('#not-toolbar').width($(window).width() - 20);
			}
			toolbar_hidden = !toolbar_hidden;
		}

		function play(){
			console.log('play');
			playing = true;
			moveSlider(index);
		}

		function pause(){
			console.log('pause');
			playing = false;    
		}  
        function updateFatalIncidents(){
			console.log("Updating fatals : " + markers.length);
			$("#fatal_incidents").text(markers.length);
        }
    </script>
  </head>
  <body>
	
    <div id="toolbar" 
        style="background-color:#FFFFFF; float:left; width:200px; height:100%; display:none" 
        onclick="toggle_toolbar();">

        <div style="margin:20px 30px 10px 20px;">Severity</div>
        <div id="severity-range" style="margin:10px 30px 10px 30px"></div> 
        
        <i class="icon-backward"
            style="position:absolute; top:50%;"></i>

    </div>

    <div id="collapsed-toolbar"
        onclick="toggle_toolbar();"
        style="width:20px; height:100%; float:left;">

        <i class="icon-forward"
            style="position:absolute; top:50%;"></i>
    </div>

<div id="not-toolbar" style="float:left; height:100%; width:95%">
    <div style="background-color:#FFFFFF; border-bottom:single #CCCCCC">
        <img src="img/hackerspace.jpg" height="55" width="27" style="float:left; padding:15px;" title="Hobart Hackerspace" alt="Hobart Hackerspace Logo"/>
        <h1 style="display: inline; float:left; font-weight:700; font-size: 30px; padding:10px 10px">Tasmanian Crash Database</h1>
        <label class="form-control" style="display:inline; font-size:1.5em; margin:30px 15px 20px 25px; float: left; padding-left: 10px; width: 80px;" id="day-month"></label>
        <select class="form-control" style="margin: 26px 0px; width:150px; float: left;" id="year" name="year" "/>
            <option>2004</option>
            <option>2005</option>
            <option>2006</option>
            <option>2007</option>
            <option>2008</option>
            <option>2009</option>
            <option>2010</option>
            <option>2011</option>
            <option>2012</option>
            <option selected="selected">2013</option>
        </select>
        <img src="./img/tombstone_64px.png" style="float: left; padding-left: 55px; height: 32; width: 32px; margin-top: 22px"/>
        <h1 id="fatal_incidents" style="display: inline; float:left; font-weight:700; font-size: 30px; padding:10px 10px ">0</h1>
    </div>
    <div id="map-canvas" style="clear: both"></div>
    <div id="slider-container">
        <i class="icon-play" onclick="play();"></i>
        <i class="icon-pause" onclick="pause();"></i>
    	<div id="slider"></div>
    </div>
</div>

  </body>
</html>


