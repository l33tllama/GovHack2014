<!DOCTYPE html>
<html>
  <head>
    <title>Tasmanian Crash Database: FAQ</title>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
    <meta charset="utf-8">
    <link href='http://fonts.googleapis.com/css?family=Ubuntu:400,700' rel='stylesheet' type='text/css'>
    <script src="https://maps.googleapis.com/maps/api/js?libraries=visualization"></script>
    <script src="http://code.jquery.com/jquery-1.11.0.min.js"></script>
    <script src="http://code.jquery.com/ui/1.11.0/jquery-ui.js"></script>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.11.0/themes/smoothness/jquery-ui.css">
	<link href='css/bootstrap.css' rel='stylesheet' type='text/css'>
    <script src="http://cdnjs.cloudflare.com/ajax/libs/proj4js/2.2.1/proj4-src.js"></script>
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
    
  </head>
  <body>
	

    <div style="background-color:#FFFFFF; border-bottom:single #CCCCCC">
        <img src="img/hackerspace.jpg" height="55" width="27" style="float:left; padding:15px;" title="Hobart Hackerspace" alt="Hobart Hackerspace Logo"/>
        <h1 style="display: inline; float:left; font-weight:700; font-size: 30px; padding:10px 10px">Tasmanian Crash Database</h1>

        <a style="margin: 30px; float:left" href="index.php">Back to application</a>
    </div>

    <div style="clear:both; padding:30px;">
        <h4>Frequently Asked Questions</h4>

        <p><b>Q: Where do you get your Data?</b></p>

        <p>A: We pull data from the Tasmanian Crash Data API, which can be found at  http://data.gov.au/dataset/tasmanian.</p> 

        <p><b>Q: Does the number of fatal crashes equal the number of deaths?</b></p>

        <p>A: No, the dataset available to us can only provide the number of Fatal crashes, not the number of deaths. </p>

        <!--p><b>Q: when was this data updated last? </b></p>

        <p>A: We draw the data directly from the Government API, so the data is as up to date as they keep it. (Any new additions will be shown when you refresh the page)</p-->

        <p><b>Q: Help! It’s not working! I see blank/broken objects! It keeps crashing!</b></p>

        <p>A: Ensure you have a modern Browser, such as Chrome Or firefox, and that it is the latest version of it. Additionally, Javascript must be enabled, and for the very best experience ensure you use an updated version of chrome. </p>



    </div>
    
  </body>
</html>


