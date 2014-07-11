

<!DOCTYPE html>
<html>
  <head>
    <title>Simple Map</title>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
    <meta charset="utf-8">
    <style>
      html, body, #map-canvas {
        margin: 0px;
        padding: 0px
      }
    html, body {
        height: 100%;
    }
    #map-canvas {
        height: 90%;
    }
    #slider {
        height: 10%;
    }

    </style>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.11.0/themes/smoothness/jquery-ui.css">
    <script src="https://maps.googleapis.com/maps/api/js?libraries=visualization"></script>
    <script src="http://code.jquery.com/jquery-1.11.0.min.js"></script>
    <script src="http://cdnjs.cloudflare.com/ajax/libs/proj4js/2.2.1/proj4-src.js"></script>
    <script src="http://code.jquery.com/ui/1.11.0/jquery-ui.js"></script>


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

        function getFrequency(id){
            //return frequencies[id];
        }

        

        function loadData(month) {
            console.log('loadData called with ' + month);

            var data = {
               sql: 'SELECT \"ID\",count(*),\"CRASH_DATE\",\"CRASH_TIME\",max(\"SEVERITY\") AS severity,max(\"DCA\")AS dca,max(\"X\")AS x,max(\"Y\")AS y from \"e73ea42f-30ee-4a02-a2cb-d3e426c1f0b3\" WHERE \"CRASH_DATE\" LIKE \'2013-' + month + '%\' GROUP BY \"ID\",\"CRASH_DATE\",\"CRASH_TIME\" ORDER BY \"CRASH_TIME\"'
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
                    markers[i].setMap(null);
                }

                for (var i = 0; i < results.length; i++) {
                    var x = results[i].x;
                    var y = results[i].y;
                    //alert(x + ", " + y);

                    var coords = new proj4(fromProj, toProj, [x,y]);   //any object will do as long as it has 'x' and 'y' properties
                    //alert(coords[1] + ", " + coords[0]);

                    var latLng = new google.maps.LatLng(coords[1],coords[0]);
                    
                    //alert(map);
//
                    colour = getSeverity(results[i].severity);
                    size = parseInt(results[i].count) + 4;

                    var marker = new google.maps.Marker({
                        position: latLng,
                        map: map,
                        title: 'Description: ' + results[i].dca + '\n' + 'Severity: ' + results[i].severity + '\n' + 'No of Vehicles: ' + results[i].count,
                        icon: getCircle(colour, size)
                    });
                    //marker.setMap(null);
                    markers.push(marker);
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
                value:0,
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
    <div id="map-canvas"></div>
<div id="slider"></div>
  </body>
</html>


