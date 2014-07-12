

<!DOCTYPE html>
<html>
  <head>
    <title>Simple Map</title>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
    <meta charset="utf-8">
    <style>
    * {
        font-family: san-serif;
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
    #slider {
        height: 10%;
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

    </style>

    <link rel="stylesheet" href="//code.jquery.com/ui/1.11.0/themes/smoothness/jquery-ui.css">
    <script src="https://maps.googleapis.com/maps/api/js?libraries=visualization"></script>
    <script src="http://code.jquery.com/jquery-1.11.0.min.js"></script>
    <script src="http://cdnjs.cloudflare.com/ajax/libs/proj4js/2.2.1/proj4-src.js"></script>
    <script src="http://code.jquery.com/ui/1.11.0/jquery-ui.js"></script>
    <script src="http://d3js.org/d3.v3.min.js" charset="utf-8"></script>
    <!--Not needed atm, maybe save for later if I cab be bothered getting it working <script src="js/map_overlay.js"></script>-->
    <script src="js/vectorMarkerAnimate.js"></script>

    <script>
        var map;
        var markers = [];
        //var frequencies;
    
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

        function getSeverity(severity){
            return {
                'Not known': '#FFBBBB',
                'Unknown': '#FFBBBB',
                'Property Damage Only': '#FF9999',
                'First Aid': '#FF7777',
                'Minor': '#FF5555',
                'Serious': '#FF3333',
                'Fatal': '#000000'
            }[severity] ;
        }

        function getDays(year){
            
        }
        

        function loadData(month) {
            var year = $('#year').val();

            console.log('loadData called with ' + year + ', ' + month);

            var data = {
               sql: 'SELECT \"ID\",count(*),\"CRASH_DATE\",\"CRASH_TIME\",max(\"SEVERITY\") AS severity,max(\"DCA\")AS dca,max(\"X\")AS x,max(\"Y\")AS y from \"e73ea42f-30ee-4a02-a2cb-d3e426c1f0b3\" WHERE \"CRASH_DATE\" LIKE \'' + year + '-' + month + '%\' GROUP BY \"ID\",\"CRASH_DATE\",\"CRASH_TIME\" ORDER BY \"CRASH_TIME\"'
            };
            
            $.ajax({
                url: 'http://data.gov.au/api/action/datastore_search_sql',
                data: data,
                dataType: 'jsonp',
                success: function(data) {
                console.log('successfull ajax');
                var fromProj = "+proj=utm +zone=55 +south +ellps=GRS80 +towgs84=0,0,0,0,0,0,0 +units=m +no_defs"; 
                var toProj = "+proj=longlat +ellps=WGS84 +datum=WGS84 +no_defs";

                //frequencies = {};
                var results = data.result.records;
                
                //for (var i = 0; i < results.length; i++) {
                //    id = results[i].ID;
                //    if (id in frequencies) {
                //        frequencies[id] += 1;
                //    } else {
                //        frequencies[id] = 1;
                //    }
                //}
                
                //console.log(frequencies);
				
                console.log(results.length + ' records found');

                for (var i = 0; i < markers.length; i++) {
                    markers[i].fadeOut({duration: 3000, complete: function() {
						markers[i].setMap(null);
                    }});
                    
                    
                }

                for (var i = 0; i < results.length; i++) {
                    var x = results[i].x;
                    var y = results[i].y;
                    //alert(x + ", " + y);

                    var coords = new proj4(fromProj, toProj, [x,y]);   //any object will do as long as it has 'x' and 'y' properties
                    //alert(coords[1] + ", " + coords[0]);

                    var latLng = new google.maps.LatLng(coords[1],coords[0]);

                    colour = getSeverity(results[i].severity);
                    size = (parseInt(results[i].count) + 4);

					// var testImage = "./img/crash.png";
					// var testCarOverlay = new carOverlay(colour, latLng, [size, size, size], testImage, map);
                    var marker = new google.maps.Marker({
                        position: latLng,
                        map: map,
                        title: 'Description: ' + results[i].dca + '\n' + 'Severity: ' + results[i].severity + '\n' + 'No of Vehicles: ' + results[i].count,
                        icon: getCircle(colour, size)
                    });
					marker.bindCircle({
						map: map,
						radius: 800 * size,
						strokeColor: "white",
						strokeWeight: 1,
                        //title: 'Description: ' + results[i].dca + '\n' + 'Severity: ' + results[i].severity + '\n' + 'No of Vehicles: ' + results[i].count,
						fillColor: colour,
						fillOpacity: 0.8
					});
                    //marker.setMap(null);
                    markers.push(marker);
                    marker.fadeIn(map, {duration : 3000});
                    /*

                    function nextMarker(mkNum){
                        if (mkNum <= markers.length) {;
                            markers[mkNum].setMap(map);
                            setTimeout(function() {
                                //markers[mkNum].setMap(null);
                                nextMarker(mkNum+1);
                            }, 100);
                        }
                    }
                    nextMarker(0);*/

                }
            }
          });
            
        }


         $(function() {
            $( "#slider" ).slider({
                value:1,
                min: 1,
                max: 12,
                step: 1,
                slide: function( event, ui ) {
                    console.log(ui.value);
                    var padded = ("0" + ui.value).slice (-2);
                    console.log(padded);
                    loadData(padded);
                }
            });
          
          var mapOptions = {
            zoom: 7,
            center: new google.maps.LatLng(-42, 147)
          };
          map = new google.maps.Map(document.getElementById('map-canvas'),
              mapOptions);

            //var markers = [];

            loadData("01");
        });


        //google.maps.event.addDomListener(window, 'load', initialize);

    </script>
  </head>
  <body>
    <div id="toolbar" style="padding:5px; background-color:#cccccc">
        <label for="year" style="font-family: sans-serif">Select a year to view</label>
        <select id="year" name="year" onchange="loadData('01');">
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
    </div>
    <div id="map-canvas"></div>
	<div id="slider"></div>
  </body>
</html>


