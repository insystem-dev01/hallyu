var xmlHttp;
function img(obj){	
	obj.src='/si_admin/_images/0000000.GIF'
}
function createXMLHttpRequest() {
    if (window.ActiveXObject) {
        xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
    } 
    else if (window.XMLHttpRequest) {
        xmlHttp = new XMLHttpRequest();
    }
}
var Sno;
function doRequestUsingGET(targetUrl,val,val2,val3,type) {
    createXMLHttpRequest();
    Sno=type;
    var queryString = targetUrl+"?";
    queryString = queryString + "val="+val+"&val2="+val2+"&val3="+val3+"&timeStamp=" + new Date().getTime();
    xmlHttp.onreadystatechange = handleStateChange;
    xmlHttp.open("GET", queryString, true);
    xmlHttp.send(null);
}
function doRequestUsingGET2(targetUrl,no,value) {
    createXMLHttpRequest();
    
    var queryString = targetUrl+"?";
    queryString = queryString + "idx="+no+"&val="+value; 
        + "&timeStamp=" + new Date().getTime();
    xmlHttp.onreadystatechange = handleStateChange;
    xmlHttp.open("GET", queryString, true);
    xmlHttp.send(null);
}
function doRequestUsingPOST(targetUrl) {
    createXMLHttpRequest();
    
    var url = targetUrl+"?timeStamp=" + new Date().getTime();
    var queryString = createQueryString();
    
    xmlHttp.open("POST", url, true);
    xmlHttp.onreadystatechange = handleStateChange;
    xmlHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");    
    xmlHttp.send(queryString);
}
    function doRequestUsingPOST2(targetUrl) {
    createXMLHttpRequest();
    
    var url = targetUrl+"?timeStamp=" + new Date().getTime();
    var queryString = createQueryString();
    
    xmlHttp.open("POST", url, true);
    xmlHttp.onreadystatechange = handleStateChange2;
    xmlHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");    
    xmlHttp.send(queryString);
}
function handleStateChange() {
    if(xmlHttp.readyState == 4) {
        if(xmlHttp.status == 200) {
            parseResults(Sno);
        }
    }
}
function handleStateChange2() {
    if(xmlHttp.readyState == 4) {
        if(xmlHttp.status == 200) {
            parseResults2();
        }
    }
}
