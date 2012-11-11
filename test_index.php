<?php
// Turn off all error reporting
//error_reporting(0); 
//ini_set('display_errors', 'Off');

echo "<font size='4px'><b>newsonmap.in : </b>click a location on map to get related news <b>OR</b> hover mouse over news to see related locations on map.</font><br><br>";

	$server   = 'localhost'; // MySQL hostname
	$username = 'tettrain'; // MySQL username
	$password = 'seU49T9c49'; // MySQL password
$dbname   = 'tettrain_newsonmap'; // MySQL db name
	
	$ipaddress=$_SERVER['REMOTE_ADDR'];
	
	//echo "ipaddress = ".$ipaddress;
	$db = mysql_connect($server, $username, $password) or die(mysql_error());
	      mysql_select_db($dbname) or die(mysql_error());
	
	$sql = 'SELECT 
	            c.country 
	        FROM 
	            ip2nationCountries c,
	            ip2nation i 
	        WHERE 
	            i.ip < INET_ATON("'.$ipaddress.'") 
	            AND 
	            c.code = i.country 
	        ORDER BY 
	            i.ip DESC 
	        LIMIT 0,1';
	
	list($countryName) = mysql_fetch_row(mysql_query($sql));
	
	// Output full country name
	//echo $countryName;

//////////////
/*
//news 
$url = "https://ajax.googleapis.com/ajax/services/search/news?"."v=1.0&key=ABQIAAAAq6AjbvcgTwJGnSaeJEMrhBQs76AG7O2n92rHgKLP4CNVCExmaBQFRp3ylWx0yFRhthB7BZ1LIhk0vw&userip=".$ipaddress."&geo=USA"."&q=BEL";

// sendRequest
// note how referer is set manually
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_REFERER, 'http://tettra.in/newsonmap');
$body = curl_exec($ch);
curl_close($ch);

// now, process the JSON string
$json = json_decode($body,true);
// now have some fun with the results...
//echo "json response = <br>";
var_dump($json);
*/
////////////////
  ?>

<html>
<head>
<title>newsonmap.in : see news on map</title>

<script type="text/javascript" src="http://newsonmap.in/newsonmap/Contact-Pop/js/jquery-1.3.2.min.js"> </script>
<script type="text/javascript" src="http://newsonmap.in/newsonmap/Contact-Pop/js/contact-pop.js"></script>
<link rel="stylesheet" type="text/css" href="http://newsonmap.in/newsonmap/Contact-Pop/css/contact-pop.css" />

<script type="text/javascript" src="//maps.googleapis.com/maps/api/js?sensor=false"></script>
<script type="text/javascript">
 var geocoder;
  var map;
  function initialize() {
    geocoder = new google.maps.Geocoder();
    var latlng = new google.maps.LatLng(-1.397, 105.644);
    var myOptions = {
      zoom: 2,
      center: latlng,
	  disableDefaultUI: false,
      mapTypeId: google.maps.MapTypeId.ROADMAP
    }
    map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
		google.maps.event.addListener(map, 'click', function(event) {
			//alert('map clicked at '+event.latLng.lat()+","+event.latLng.lng());
			
			get_address_from_lat_lng(event.latLng.lat(),event.latLng.lng());
		});

	get_news_for_this_address("<?=$countryName?>");
  }

  function get_news_for_this_address(address) {
	//var address="<?=$countryName?>"; 
	//alert(address);	
   // geocoder.geocode( { 'address': address}, function(results, status) {
     // if (status == google.maps.GeocoderStatus.OK) {
      //  map.setCenter(results[0].geometry.location);
        //var marker = new google.maps.Marker({
       //     map: map,
        //    position: results[0].geometry.location
       // });
	   
	   xmlhttpPost('get_news_and_location_data.php',address);
	   
    //  } else {
    //    alert("Geocode was not successful for the following reason: " + status);
    //  }
  //  });
  }
  
  function xmlhttpPost(strURL,address) {
		document.getElementById('status').innerHTML="<font color='green' size='4'><b>Loading...</b></font>";
		var xmlHttpReq = false;
		var self = this;
		// Mozilla/Safari
		if (window.XMLHttpRequest) {
			self.xmlHttpReq = new XMLHttpRequest();
		}
		// IE
		else if (window.ActiveXObject) {
			self.xmlHttpReq = new ActiveXObject("Microsoft.XMLHTTP");
		}
		self.xmlHttpReq.open('POST', strURL, true);
		self.xmlHttpReq.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
		self.xmlHttpReq.onreadystatechange = function() {
			if (self.xmlHttpReq.readyState == 4) {
				if(self.xmlHttpReq.responseText=="-1"){
					document.getElementById('status').innerHTML="<font color='red' size='4'>could not get news for "+address+", try some other location.</font>";
				}else{
					//alert(self.xmlHttpReq.responseText);
					//alert(self.xmlHttpReq.responseText.length);
					update_News_and_Map(self.xmlHttpReq.responseText);
					document.getElementById('status').innerHTML="";	
					document.getElementById('news').scrollTop = 99999999;
					
				}
			}
		}
		self.xmlHttpReq.send("countryName="+address);
		
	}
	
	
		
		var description= new Array();
		var link=new Array();		
		var latlng_in_one_news=new Array();
		var placename_in_one_news=new Array();
		var title=new Array();
		var markerArray=new Array();
		var thumbnail_img=new Array();
		
		//var array_index=0;
		var prev_array_index=0;
		
	function update_News_and_Map(receivedData){
		var response=new Array();
		response=eval(receivedData);
		
		
		var index=0;
	var array_index=prev_array_index;
	
		if(typeof response!="undefined"){
			for(index in response[0]){
				//alert("index = "+index+"="+response[0][index]);
				description[array_index]=response[0][index];
				array_index++;
			}
			index=0;
			array_index=prev_array_index;
			
			for(index in response[1]){
				//alert("index="+index+"="+response[1][index]);
				link[array_index]=response[1][index];
				array_index++;
			}
			index=0;
			array_index=prev_array_index;
			for(index in response[2]){
				//alert("index="+index+"="+response[2][index]);
				latlng_in_one_news[array_index]=response[2][index];
				array_index++;
			}	
			index=0;
			array_index=prev_array_index;
			
			for(index in response[3]){
				title[array_index]=response[3][index];
				
				array_index++;
			}
			index=0;
			array_index=prev_array_index;			
			for(index in response[4]){
				placename_in_one_news[array_index]=response[4][index];				
				array_index++;
			}
			index=0;
			array_index=prev_array_index;		
			
			for(index in response[5]){
				thumbnail_img[array_index]=response[5][index];	
				//alert(thumbnail_img[array_index]);
				add_new_row_in_newsTable(title[array_index],link[array_index],thumbnail_img[array_index]);
				array_index++;
			}
			
			
			prev_array_index=array_index;
		}
	}

	var rIndex=0;
	var imgHTML;
	
	function add_new_row_in_newsTable(title,link,thumbnail){
		var table=document.getElementById('newsTable');
		
		
		
		if (window.ActiveXObject) {// for IE
		
		
			var row=table.insertRow(-1);
			row.id=rIndex;
			row.onclick=rowClicked;
			row.onmouseover=rowClicked;
			row.onmouseout=row_onmouseout;
			
			var cell=row.insertCell(0);
			//alert(row.setAttribute("id",rIndex));
			
			imgHTML='';
			if(thumbnail!=null)
				imgHTML="<img src='"+thumbnail+"' style='float:left;margin:2px;'/>";
			
			
			cell.innerHTML="  <a href='"+link+"' target='_blank' style='text-decoration:none;'>"+imgHTML+"<b>"+title +"</b>"+"</a>";
			changeColorForSomeTime(rIndex);
			//row.attachEvent('onclick',rowClicked);
		}
		else		
		{
			var row=document.createElement("TR");
			var cell=document.createElement("TD");
			
			imgHTML='';
			if(thumbnail!=null)
				imgHTML="<img src='"+thumbnail+"' style='float:left;margin:2px;'/>";
				
			cell.innerHTML="  <a href='"+link+"' target='_blank' style='text-decoration:none;'>"+imgHTML+"<b>"+title+"</b>"+"</a>";
			table.appendChild(row);
			row.appendChild(cell);
			row.setAttribute("id",rIndex );
		
			row.addEventListener('click',rowClicked,false);
			row.addEventListener('mouseover',rowClicked,false);
			row.addEventListener('mouseout',row_onmouseout,false);
			changeColorForSomeTime(rIndex);
		}
		
		rIndex++;
	
		
	}
	var e;
	function changeColorForSomeTime(n){
		document.getElementById(n).style.backgroundColor="#D0FA58";
		setTimeout("resetColor('"+n+"')",3000);
	}
	function resetColor(n){
		document.getElementById(n).style.backgroundColor="";	
	}
	
	function row_onmouseover() {		
		this.style.backgroundColor='#CCFFFF';	
	}
	function row_onmouseout(){
		this.style.backgroundColor='';	
	}
	
	function rowClicked(){
		//alert(this.innerHTML);
		//alert(this.id);
		//alert(this.rowIndex);
		this.style.backgroundColor='#CCFFFF';
	//	map.setZoom(2);
		load_markers_for_selected_row(this.id);
	} 
	var bounds;
	
	function load_markers_for_selected_row(index){
		removeAllMarker();
		 bounds=new google.maps.LatLngBounds();
		
		var i=0;
		for(i in latlng_in_one_news[index]){
		
			var lat=latlng_in_one_news[index][i][0];
			var lng=latlng_in_one_news[index][i][1];
			loadMarker(lat,lng,placename_in_one_news[index][i]);
		
		}
	//	alert(i);
		if(i==1){ 
			map.fitBounds(bounds);
			map.setZoom(4);
			
		}
		if(i>1)
			map.fitBounds(bounds);
	} 
	
	function loadMarker(lat,lng,titleName){
		//var myLatlng = new google.maps.LatLng(-25.363882,131.044922);
		var latlng=new google.maps.LatLng(lat,lng);
		var marker = new google.maps.Marker({
            map: map,
			title: titleName,
           position: latlng
        });
		markerArray.push(marker);
		//map.setCenter(latlng);
		
		bounds.extend(latlng);
	
		
	}
	
	function removeAllMarker(){
		var i=0;
		if(markerArray){
			for(i in markerArray){
				markerArray[i].setMap(null);
			}
		}		
	}
	
	function get_address_from_lat_lng(latStr,lngStr) {
    var latFloat = parseFloat(latStr);
    var lngFloat = parseFloat(lngStr);
    var latlng = new google.maps.LatLng(latFloat, lngFloat);
    geocoder.geocode({'latLng': latlng}, function(results, status) {
      if (status == google.maps.GeocoderStatus.OK) {
        if (results[1]) {
       //   map.setZoom(11);
         /* marker = new google.maps.Marker({
              position: latlng,
              map: map
          });*/
		  //alert(latStr+","+lngStr+" belongs to address= "+results[1].formatted_address);
		  alert(results[1].formatted_address);
		  get_news_for_this_address(results[1].formatted_address);
          //infowindow.setContent(results[1].formatted_address);
          //infowindow.open(map, marker);
        }
      } else {
        //alert("Geocoder failed due to: " + status);
		alert("Not a valid location.");
      }
    });
  }
  
  function searchLocation(){
	var location=document.getElementById('locationBox').value;
	
	if(trim(location).length>0){
		xmlhttpPost('get_news_and_location_data.php',location);
	}
  }
  
  function trim(s)
{
	return rtrim(ltrim(s));
}

function ltrim(s)
{
	var l=0;
	while(l < s.length && s[l] == ' ')
	{	l++; }
	return s.substring(l, s.length);
}

function rtrim(s)
{
	var r=s.length -1;
	while(r > 0 && s[r] == ' ')
	{	r-=1;	}
	return s.substring(0, r+1);
}
  function textBoxKeydown(event){
  var code=event.keyCode||event.charCode;
		if(code==13){
			searchLocation();
		}
  }
</script>


<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-27400985-1']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
	
	
</head>

<body onload="initialize()">
 
 <div id="map_canvas" style="width: 72%; height: 95%;float:left;"></div>
<div id="textbox"><label>Location:</label> <input type='text' id='locationBox' onkeypress="textBoxKeydown(event);"/><input type='button' value='search' onClick="searchLocation()"/></div>
 <div id="status" style="position:relative;"></div>
 <div id="news" style="height:480px;overflow:scroll;position:relative;top:10px;margin:2px;padding:2px;border:1px solid black">
  <table id="newsTable" border="1" style="width:100%;height:100%;">
  
  </table>
  
 
  </div>
   <iframe src="http://www.facebook.com/plugins/like.php?href=http://www.newsonmap.in"
        scrolling="no" frameborder="0"
        style="position:relative;top:20px;left:10px;border:none; width:180px; height:100px"></iframe>
     <iframe allowtransparency="true" frameborder="0" scrolling="no"

            src="//platform.twitter.com/widgets/tweet_button.html"

            style="position:relative;left:20px;width:60px; height:100px;top:20px;"></iframe>
			<div id="contact" style="position:relative;float:left;"><a href='/newsonmap/Contact-Pop/contact.php'>Give You Feedback</a></div>
</body>
</html>