// Global variable
var img = null,
	needle = null,
	ctx = null;

function clearCanvas() {
	 // clear canvas
	ctx.clearRect(0, 0, 200, 200);
}

function drawCompass(degrees) {

	clearCanvas();

	// Draw the compass onto the canvas
	ctx.drawImage(img, 0, 0);

	// Save the current drawing state
	ctx.save();

	// Now move across and down half the 
	ctx.translate(100, 100);

	// Rotate around this point
	ctx.rotate(degrees * (Math.PI / 180));

	// Draw the image back and up
	ctx.drawImage(needle, -100, -100);

	// Restore the previous drawing state
	ctx.restore();
}

function imgLoaded() {
	// Image loaded event complete.  Start the timer
	setInterval(drawCompass(0), 100);
}

function init() {
	// Grab the compass element
	var canvas = document.getElementById('compass');

	// Canvas supported?
	if (canvas.getContext('2d')) {
		ctx = canvas.getContext('2d');

		// Load the needle image
		needle = new Image();
		needle.src = 'png/needle.png';

		// Load the compass image
		img = new Image();
		img.src = 'png/compass.png';
		img.onload = imgLoaded;
	} else {
		alert("Canvas not supported!");
	}
}