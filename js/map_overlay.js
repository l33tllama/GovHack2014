carOverlay.prototype = new google.maps.OverlayView();
function carOverlay(colour, latLon, image_dims, image, map){
	this.color_ = colour;
	this.image_dims_ = image_dims;
	this.map_ = map;
	this.latLon_ = latLon;

	// a div to store the image's div
	this.div_ = null;
	this.crashInfo = null;

	this.setMap(map)
}

carOverlay.prototype.onAdd = function() {
	var div = document.createElement('div');
	div.style.borderStyle = "none";
	div.style.position = 'absolute';
	//div.style.backgroundColor = this.color_;
	//div.innerHTML = "le crash";
	
	var d3_div = d3.select(div).style({	
		"opacity" : 0.75
	});
	
	this.crashInfo = d3_div.append("div").attr("class", "crashInfo").html("A crash! oh noes");
	
	var svg = d3_div.append('svg').attr({
		"width": this.image_dims_[0],
		"height" : this.image_dims_[1],
		"class" : "crashDot"
	});
	
	var gradient = svg.append('defs').append('radialGradient')
					.attr("id", "circle_gradient");
	
					gradient.append('stop').attr({ "offset" : "0%", 
									"stop-color" : this.color_,
									"stop-opacity" : 1
					});
					gradient.append('stop').attr({ "offset" : "10%", 
									"stop-color" : this.color_,
									"stop-opacity" : .9
					});
					gradient.append('stop').attr({ "offset" : "65%", 
									"stop-color" : this.color_,
									"stop-opacity" : .8
					});
					gradient.append('stop').attr({ "offset" : "100%",
									"stop-color" : this.color_,
									"stop-opacity" : 0 });
	
	svg.append('circle').attr({
		"fill": "url(#circle_gradient)",
		"cx" : this.image_dims_[0]/2,
		"cy" : this.image_dims_[1]/2,
		"r" : this.image_dims_[2]/2
	});
	

	this.div_ = div;

	// Add the element to the "overlayLayer" pane.
	var panes = this.getPanes();
	
	panes.overlayLayer.appendChild(div);
	
};

carOverlay.prototype.draw = function() {
	// We use the south-west and north-east
	// coordinates of the overlay to peg it to the correct position and size.
	// To do this, we need to retrieve the projection from the overlay.
	var overlayProjection = this.getProjection();

	// Retrieve the south-west and north-east coordinates of this overlay
	// in LatLngs and convert them to pixel coordinates.
	// We'll use these coordinates to resize the div.
	//var sw = overlayProjection.fromLatLngToDivPixel(this.bounds_.getSouthWest());
	//var ne = overlayProjection.fromLatLngToDivPixel(this.bounds_.getNorthEast());
	var overlay_dims = overlayProjection.fromLatLngToDivPixel(this.latLon_);
	var x = overlay_dims.x;
	var y = overlay_dims.y;

	var width = this.image_dims_[0];
	var height = this.image_dims_[1];
	//console.log("Added an overlay marker at: " +x+ ", " + y)
	
	// Resize the image's div to fit the indicated dimensions.
	var div = this.div_;
	div.style.left = x -width / 2 + 'px';
	div.style.top = y -height / 2 + 'px';
	div.style.width = width + 'px';
	div.style.height = height + 'px';
	
	var thatCrashInfo = this.crashInfo;
	google.maps.event.addDomListener(this.div_, 'mouseover', function() {
		console.log("Mouse over!");
		thatCrashInfo.style("visibility", "visible");
		thatCrashInfo.attr("class", "visible");
	});
	
	google.maps.event.addDomListener(this.div_, 'mouseout', function() {
		console.log("Mouse out!");
		thatCrashInfo.style("visibility", "hidden");
		thatCrashInfo.attr("class", "hidden");
	});
};

carOverlay.prototype.onRemove = function() {
	this.div_.parentNode.removeChild(this.div_);
	this.div_ = null;
};
carOverlay.prototype.hide = function() {
	if (this.div_) {
		// The visibility property must be a string enclosed in quotes.
		this.div_.className = "hidden"; 
		//this.div_.style.animationName = "hideshow 10s ease infinite";
		//this.div_.style.visibility = 'hidden';
	}
};
carOverlay.prototype.show = function() {
	if (this.div_) {
		//this.div_.className = "visible";
		this.div_.style.visibility = 'visible';
	}
};
carOverlay.prototype.toggle = function() {
	if (this.div_) {
		if (this.div_.style.visibility == 'hidden') {
			this.show();
		} else {
			this.hide();
		}
	}
};
carOverlay.prototype.toggleDOM = function() {
	if (this.getMap()) {
		// Note: setMap(null) calls OverlayView.onRemove()
		this.setMap(null);
	} else {
		this.setMap(this.map_);
	}
};




