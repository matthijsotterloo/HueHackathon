<html>
<head>
<script>
function compare() {
console.log(resemble('watson.jpg').compareTo('hitler.jpg').onComplete(function(){
	return data;
	/*
	{
	  misMatchPercentage : 100, // %
	  isSameDimensions: true, // or false
	  getImageDataUrl: function(){}
	}
	*/
}));
}
</script>
</head>
<body>

</body>
</html>