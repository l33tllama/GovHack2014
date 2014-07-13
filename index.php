

<!DOCTYPE html>
<html>
  <head>
    <title>Tasmanian Crash Database</title>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
    <meta charset="utf-8">
    <link href='http://fonts.googleapis.com/css?family=Ubuntu:400,700' rel='stylesheet' type='text/css'>
    <link href='css/bootstrap.css' rel='stylesheet' type='text/css'>

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
        var all_results = [];
        var index = 1;
        var cancel_timer = false;
        var playing = false;
        var showing_all = false;
        
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
                '1': '#0077FF',
                '2': '#0077FF',
                '3': '#FF6666',
                '4': '#FF6666',
                '5': '#FF0000',
                '6': '#000000'
            }[severity] ;
        }
    
        Date.prototype.getDOY = function() { var onejan = new Date(this.getFullYear(),0,1); return Math.ceil((this - onejan) / 86400000); }

      

        function loadYear(){
            var year = $('#year').val();
            console.log('loadYear called with ' + year);

            var data = {};
            
            $.ajax({
                //url: 'http://data.gov.au/api/action/datastore_search_sql',
                url: 'http://localhost/GovHack2014/'+year+'.json',
                data: data,
                dataType: 'json',
                success: function(data) {
                    //console.log('successful ajax');

                    //frequencies = {};
                    all_results = data.result.records;

                    //console.log(all_results.length + ' records found for this year');
                    index = 1;
                    //console.log('Slider moved to ' + index);

                    $("#slider").slider("value", index); 
                    loadDay(index);
                    
                }
            });   
            
        }

        function loadDay(day_of_year) {

            console.log('loadDay called with ' + day_of_year);

            var results = [];

            
            if (day_of_year == 367) {
                showAllData();
                showing_all = true;
                return;
            } else {
                if (showing_all == true) {
                    showing_all = false;
                    hideAllData();
                }
            }

            //console.time('selectingDay');
            for (var i = 0; i < all_results.length; i++) {
                var t = all_results[i].dt.split("-");
                var d = new Date(t[0], t[1]-1, t[2]);
                var result_doy = d.getDOY();
                if (result_doy == day_of_year){ 
                    results.push(all_results[i]); 
                }
            }
            //console.timeEnd('selectingDay');

                //console.log(results.length + ' matching records found');

                for (var i = 0; i < markers.length; i++) {
                    markers[i].fadeOut({duration: 3000, complete: function() {
						markers[i].setMap(null);
                    }}); 
                }

                for (var i = 0; i < results.length; i++) {
                    var x = results[i].x;
                    var y = results[i].y;
                    //console.log(x + ", " + y);
                    var fromProj = "+proj=utm +zone=55 +south +ellps=GRS80 +towgs84=0,0,0,0,0,0,0 +units=m +no_defs"; 
                    var toProj = "+proj=longlat +ellps=WGS84 +datum=WGS84 +no_defs";

                    var coords = new proj4(fromProj, toProj, [x,y]);   //any object will do as long as it has 'x' and 'y' properties
                    //console.log(coords[1] + ", " + coords[0]);

                    var latLng = new google.maps.LatLng(coords[1],coords[0]);

                    colour = getSeverity(results[i].sev);
                    //colour = parseInt(results[i].sev);
                    size = (parseInt(results[i].ct) + 4);

					// var testImage = "./img/crash.png";
					// var testCarOverlay = new carOverlay(colour, latLng, [size, size, size], testImage, map);
                    var marker = new google.maps.Marker({
                        position: latLng,
                        map: map,
                        //title: 'Description: ' + results[i].dca + '\n' + 'Severity: ' + results[i].severity + '\n' + 'No of Vehicles: ' + results[i].count,
                        icon: getCircle(colour, size)
                    });
					marker.bindCircle({
						map: map,
						radius: 1000 * size,
						strokeColor: "white",
						strokeWeight: 1,
                        //title: 'Description: ' + results[i].dca + '\n' + 'Severity: ' + results[i].severity + '\n' + 'No of Vehicles: ' + results[i].count,
						fillColor: colour,
						fillOpacity: 1.0
					});
                    marker.setZIndex(-1);
                    //marker.setMap(null);
                    markers.push(marker);
                    marker.fadeIn(map, {duration : 500});
            }
        }

         $(function() {
            $( "#slider" ).slider({
                value:1,
                min: 1,
                max: 367,
                step: 1,
                slide: function( event, ui ) {
                    //console.log(ui.value);
                    //var padded = ("0" + ui.value).slice (-2);
                    //console.log('Slider moved to ' + ui.value);
                    index = ui.value;
                    loadDay(ui.value);
                }
            });

             $( "#severity-range" ).slider({
                range: true,
                min: 0,
                max: 6,
                values: [ 0, 6 ],
                slide: function( event, ui ) {
                    //$( "#amount" ).val( "$" + ui.values[ 0 ] + " - $" + ui.values[ 1 ] );
                    console.log(ui.values[0], ui.values[1]);
                }
            });

          
          var mapOptions = {
            zoom: 7,
            mapTypeId: google.maps.MapTypeId.SATELLITE,
            center: new google.maps.LatLng(-42, 147)
          };
          map = new google.maps.Map(document.getElementById('map-canvas'),
              mapOptions);


            loadYear('2013');

            //var markers = [];

            //loadData("01");
            loadDay(1);

            moveSlider(index);

        });

        function showAllData(){
            console.log('showAllData');
            for (var i = 0; i < all_results.length; i++) {
                var x = all_results[i].x;
                var y = all_results[i].y;
                var fromProj = "+proj=utm +zone=55 +south +ellps=GRS80 +towgs84=0,0,0,0,0,0,0 +units=m +no_defs"; 
                var toProj = "+proj=longlat +ellps=WGS84 +datum=WGS84 +no_defs";

                var coords = new proj4(fromProj, toProj, [x,y]);
                var latLng = new google.maps.LatLng(coords[1],coords[0]);

                colour = getSeverity(all_results[i].sev);
                size = (parseInt(all_results[i].ct) + 4);

                var marker = new google.maps.Marker({
                    position: latLng,
                    map: map,
                    icon: getCircle(colour, size)
                });
                marker.bindCircle({
                    map: map,
                    radius: 1000 * size,
                    strokeColor: "white",
                    strokeWeight: 1,
                    fillColor: colour,
                    fillOpacity: 0.8
                });
                marker.setZIndex(-1);
                markers.push(marker);
                marker.setMap(map);
                //marker.fadeIn(map, {duration : 500});
            }
        }   

        function hideAllData() {
            for (var i = 0; i < markers.length; i++) {
                markers[i].setMap(null);
            }
        }

        function setMonthDayLabel(){
            
        }   

        function moveSlider(i){
            if (!playing){
                return;
            }
            
            $("#slider").slider("value", i); 
            setMonthDayLabel();
            loadDay(i+1);
            //console.log('Slider moved to ' + i+1);

            if (i > 366) {
                i = 1;
                index = 1;
                return
            }

            setTimeout(function() {
                index = i;
                moveSlider(i+1);
            }, 200);
        }
        //google.maps.event.addDomListener(window, 'load', initialize);

    var toolbar_hidden = false;

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

    </script>
  </head>
  <body>

    <div id="toolbar" 
        style="background-color:#FFFFFF; float:left; width:200px; height:100%" 
        onclick="toggle_toolbar();">

        <div style="margin:20px 30px 10px 20px;">Severity</div>
        <div id="severity-range" style="margin:10px 30px 10px 30px"></div> 
        
        <i class="icon-backward"
            style="position:absolute; top:50%;"></i>

    </div>

    <div id="collapsed-toolbar"
        onclick="toggle_toolbar();"
        style="width:20px; display:none; height:100%; float:left;">

        <i class="icon-forward"
            style="position:absolute; top:50%;"></i>
    </div>

<div id="not-toolbar" style="float:left; height:100%; width:80%">
    <div style="background-color:#FFFFFF; border-bottom:single #CCCCCC">
        <img src="img/hackerspace.jpg" height="55" width="27" style="float:left; padding:15px;" title="Hobart Hackerspace" alt="Hobart Hackerspace Logo"/>
        <h1 style="display: inline; float:left; font-weight:700; font-size: 30px; padding:10px 10px">Tasmanian Crash Database</h1>
        <select class="form-control" style="margin: 26px 10px; width:150px" id="year" name="year" onchange="loadYear(this.selectedText);">
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
        <div id="month-day"></div>
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


