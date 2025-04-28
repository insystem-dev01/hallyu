document.write(
    "<div id=alt_div style=\"" +
    "padding:2;" +
    "border:10px solid #999999;" +
    "background-color:#F7F7F7;color:#000;" +
    "position:absolute;" +
    "left:0px;" +
    "top:0px;" +
    "visibility:hidden;" +
    "overflow:hidden;" +
    "z-index:1;" +
    "width:auto;" +
    "height:auto;" +
    "filter:alpha(opacity=100);" +
    "\"></div>"
);

var imageSize;

function alt(msg){
	var html;
	checkIE();

	if( msg != null ){
		html = '<table>';
		html = html + '<tr>';
		html = html + '<td>';
		html = html + "<img src='" + msg + "'>";
		html = html + '</tr>';
		html = html + '</td>';
		html = html + '</table>';

		_div = document.getElementById("alt_div");
		_div.innerHTML = html;
		document.getElementById("alt_div").style.visibility = "hidden";
		imageSize = getImageSize(msg);

		if(isIE == true){
			document.onmousemove = layrermove;
		} else {
			document.body.addEventListener("mousemove", layrermove, false);
		}

		document.getElementById("alt_div").style.visibility = "visible";
	}else{

		_div = document.getElementById("alt_div");
		_div.innerHTML = html;
		document.getElementById("alt_div").style.visibility = "hidden";
		document.getElementById("alt_div").style.left = "20px";
		document.getElementById("alt_div").style.top = "2000px";

		if(isIE == true){
			document.onmousemove = '';
		} else {
			document.body.removeEventListener("mousemove", layrermove, false);
		}

	}
}

function checkIE(){
	if(navigator.appName == "Microsoft Internet Explorer"){
		isIE = true;
	} else {
		isIE = false;
	}
}

function getImageSize(src) {
	testImg = new Image();
	testImg.src = src;
	return {width:testImg.width, height:testImg.height};
}

function layrermove(e)
{
	if (window.event)
	{
		var pixelLeft			= document.all.alt_div.style.pixelLeft;
		var pixelTop			= document.all.alt_div.style.pixelTop;
		var clientX			= window.event.clientX;
		var clientY			= window.event.clientY;
		var scrollLeft		= (document.documentElement.scrollLeft) ? document.documentElement.scrollLeft : document.body.scrollLeft;
		var scrollTop			= (document.documentElement.scrollTop) ? document.documentElement.scrollTop : document.body.scrollTop;
		var screenWidth	= window.screen.width;
		var screenHeight	= window.screen.height;

		if ((pixelLeft != clientX + scrollLeft) || (pixelTop != clientY + scrollTop)){
			if(screenWidth + scrollLeft <= clientX + scrollLeft + imageSize.width)
			{
				document.all.alt_div.style.pixelLeft = clientX + scrollLeft - imageSize.width - 20; 
			}
			else
			{
				document.all.alt_div.style.pixelLeft = clientX + scrollLeft + 20;  
			}

			if(screenHeight + scrollTop <= clientY + scrollTop + imageSize.height + 100)
			{
				document.all.alt_div.style.pixelTop = clientY + scrollTop - imageSize.height - 20;
			}
			else
			{
				document.all.alt_div.style.pixelTop = clientY + scrollTop;
			}
		}
	}
	else
	{
		var itemsObj	= document.getElementById('alt_div');;
		img_w			= imageSize.width;
		img_h			= imageSize.height;

		var mouseW		= e.clientX;
		var mouseH		= e.clientY;
		var scrollW		= document.documentElement.scrollLeft;
		var scrollH		= document.documentElement.scrollTop;
		var bodyW		= document.body.offsetWidth;
		var bodyH		= document.body.offsetHeight;
		var posx		= (bodyW+scrollW <= mouseW+scrollW+img_w) ? (mouseW+scrollW-img_w-20) : (mouseW+20+scrollW);
		var posy		= (bodyH+scrollH <= mouseH+scrollH+img_h + 100) ? (scrollH+mouseH-img_h-20) : mouseH+scrollH;

		itemsObj.style.left		= posx + "px";
		itemsObj.style.top		= posy + "px";
	}
}
