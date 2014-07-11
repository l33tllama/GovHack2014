

<!DOCTYPE html>
<html>
  <head>
    <title>Simple Map</title>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
    <meta charset="utf-8">
    <style>
      html, body, #map-canvas {
        height: 100%;
        margin: 0px;
        padding: 0px
      }
    </style>
    <script src="https://maps.googleapis.com/maps/api/js?libraries=visualization"></script>
    <script src="http://code.jquery.com/jquery-1.11.0.min.js"></script>
    <script src="http://cdnjs.cloudflare.com/ajax/libs/proj4js/2.2.1/proj4-src.js"></script>


    <script>
        var map;
    
        function getCircle(hexColour) {
          return {
            path: google.maps.SymbolPath.CIRCLE,
            fillColor: hexColour,
            fillOpacity: .8,
            scale: 7,
            strokeColor: 'white',
            strokeWeight: .5
          };
        }

        function getSeverity(severity){
            return {
                'Unknown': '#ffffff',
                'Property Damage Only': '#ff3333',
                'First Aid': '#ff6666',
                'Minor': '#ff3333',
                'Serious': '#ff0000',
                'Fatal': '#000000'
            }[severity] ;
        }



        function initialize() {
          var mapOptions = {
            zoom: 7,
            center: new google.maps.LatLng(-42, 147)
          };
          map = new google.maps.Map(document.getElementById('map-canvas'),
              mapOptions);

        var data = {
            //resource_id: 'e73ea42f-30ee-4a02-a2cb-d3e426c1f0b3', // the resource id
            //limit: 999, // get 5 results
            //q: 'traffic signals'
            //sql: 'SELECT MAX(X), MAX(Y), MAX(SEVERITY), COUNT(ID) AS coincidence from \"e73ea42f-30ee-4a02-a2cb-d3e426c1f0b3\" WHERE \"CRASH_DATE\" = \'2013-03-30T00:00:00\' GROUP BY coincidence'

            sql: 'SELECT * FROM \"e73ea42f-30ee-4a02-a2cb-d3e426c1f0b3\" WHERE \"CRASH_DATE\" = \'2013-03-30T00:00:00\''

        };

            $.ajax({
            url: 'http://data.gov.au/api/action/datastore_search_sql',
            data: data,
            dataType: 'jsonp',
            success: function(data) {

                var fromProj = "+proj=utm +zone=55 +south +ellps=GRS80 +towgs84=0,0,0,0,0,0,0 +units=m +no_defs"; 
                var toProj = "+proj=longlat +ellps=WGS84 +datum=WGS84 +no_defs";

                var results = data.result.records;

                console.log(results.length + ' records found');

                for (var i = 0; i < results.length; i++) {
                    var x = results[i].X;
                    var y = results[i].Y;
                    //alert(x + ", " + y);

                    var coords = new proj4(fromProj, toProj, [x,y]);   //any object will do as long as it has 'x' and 'y' properties
                    //alert(coords[1] + ", " + coords[0]);

                    var latLng = new google.maps.LatLng(coords[1],coords[0]);
                    
                    //alert(map);

                    var marker = new google.maps.Marker({
                        position: latLng,
                        map: map,
                        title: results[i].DCA,
                        icon: getCircle(getSeverity(results[i].SEVERITY)) 
                    });
                }

            }
          });


        }

        google.maps.event.addDomListener(window, 'load', initialize);

    </script>
  </head>
  <body>
    <div id="map-canvas"></div>
  </body>
</html>


