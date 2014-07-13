<!DOCTYPE html>
<html>
  <head>
    <title>CrashUp: Credits</title>
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
        <h1 style="display: inline; float:left; font-weight:700; font-size: 30px; padding:10px 10px">CrashUp</h1>

        <a style="margin: 30px; float:left" href="index.php">Back to application</a>
    </div>

    <div style="clear:both; padding:30px;">
        <h4>CrashUp Team</h4>

        <ul>
            <li>Leo Febey</li>
            <li>Toby Roat</li>
            <li>Shane Dalgleish</li>
            <li>Craig Marshall</li>
            <li>Michael Emery</li>
            <li>Susan Smith</li>
            <li>Tanya McLachlan-Troup</li>
            <li>Mary Wright</li>
            <li>Brian Diamond</li>
        </ul>

        <h4>Credits</h4>
        
        <ul>
            <li>http://freemusicarchive.org/music/Illl/ <div xmlns:cc="http://creativecommons.org/ns#" xmlns:dct="http://purl.org/dc/terms/" about="http://freemusicarchive.org/music/Section_27_Netlabel/Sectioned_v30/"><span property="dct:title">Sectioned v3.0</span> (<a rel="cc:attributionURL" property="cc:attributionName" href="http://section27netlabel.blogspot.com">Section 27 Netlabel</a>) / <a rel="license" href="http://creativecommons.org/licenses/by-nc-nd/3.0/">CC BY-NC-ND 3.0</a></div></li>
        </ul>

    </div>
    
  </body>
</html>


