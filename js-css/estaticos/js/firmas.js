let ctx, color = "#000";
// function to setup a new canvas for drawing
const newCanvas = () => {
	//define and resize canvas
	let canvas = '<canvas id="canvas" width="330px" height="100px"></canvas>';
	document.getElementById("content_firmas").innerHTML = canvas;
	// setup canvas
	ctx = document.getElementById("canvas").getContext("2d");
	ctx.strokeStyle = color;
	ctx.lineWidth = 1;
	// setup to trigger drawing on mouse or touch
	drawTouch();
	//   drawPointer();
	drawMouse();
}

const selectColor = el => {
	ctx.beginPath();
	ctx.strokeStyle = "#000";
}

// prototype to	start drawing on touch using canvas moveTo and lineTo
const drawTouch = () => {
	let start = e => {
		ctx.beginPath();
		let divcanvas = document.getElementById("canvas");
		x = e.changedTouches[0].pageX - getDimensions(divcanvas).x;
		y = e.changedTouches[0].pageY - getDimensions(divcanvas).y;
		ctx.moveTo(x, y);
	};
	let move = e => {
		e.preventDefault();
		let divcanvas = document.getElementById("canvas");
		x = e.changedTouches[0].pageX - getDimensions(divcanvas).x;
		y = e.changedTouches[0].pageY - getDimensions(divcanvas).y;
		ctx.lineTo(x, y);
		ctx.stroke();
	};
	document.getElementById("canvas").addEventListener("touchstart", start, false);
	document.getElementById("canvas").addEventListener("touchmove", move, false);
};

//prototype to start drawing on pointer(microsoft ie) using canvas moveTo and lineTo
const drawPointer = () => {
	let start = e => {
		e = e.originalEvent;
		ctx.beginPath();
		x = e.pageX;
		y = e.pageY;
		ctx.moveTo(x, y);
	};
	let move = e => {
		e.preventDefault();
		e = e.originalEvent;
		x = e.pageX;
		y = e.pageY;
		ctx.lineTo(x, y);
		ctx.stroke();
	};
	document.getElementById("canvas").addEventListener("MSPointerDown", start, false);
	document.getElementById("canvas").addEventListener("MSPointerMove", move, false);
};

// prototype to	start drawing on mouse using canvas moveTo and lineTo
const drawMouse = () => {
	let clicked = 0;

	let start = (e) => {
		clicked = 1;
		ctx.beginPath();
		let divcanvas = document.getElementById("canvas");
		x = e.pageX - getDimensions(divcanvas).x;
		y = e.pageY - getDimensions(divcanvas).y;
		ctx.moveTo(x, y);
	};

	let move = (e) => {
		if (clicked) {
			let divcanvas = document.getElementById("content_firmas");
			x = e.pageX - getDimensions(divcanvas).x;
			y = e.pageY - getDimensions(divcanvas).y;
			ctx.lineTo(x, y);
			ctx.stroke();
		}
	};

	let stop = (e) => {
		clicked = 0;
	};

	document.getElementById("canvas").addEventListener("mousedown", start, false);
	document.getElementById("canvas").addEventListener("mousemove", move, false);
	document.addEventListener("mouseup", stop, false);
};

getDimensions = oElement => {
	let x, y, w, h;
	x = y = w = h = 0;
	if (document.getBoxObjectFor) { // Mozilla
		let oBox = document.getBoxObjectFor(oElement);
		x = oBox.x - 1;
		w = oBox.width;
		y = oBox.y - 1;
		h = oBox.height;
	} else if (oElement.getBoundingClientRect) { // IE
		let oRect = oElement.getBoundingClientRect();
		x = oRect.left - 2;
		w = oElement.clientWidth;
		y = oRect.top - 2;
		h = oElement.clientHeight;
	}
	return {
		x: x,
		y: y,
		w: w,
		h: h
	};
}

const send = (url, callback) => {
	let dataURL = document.getElementById("canvas").toDataURL();
	// Sending the image data to Server
	$.ajax({
		type: 'POST',
		url,
		data: {
			image: dataURL
		},
		dataType: 'json',
		success: function (res) {
			callback(res);
		}
	});
}
