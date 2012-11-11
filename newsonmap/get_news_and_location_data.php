<?php
	
	$countryName=$_POST['countryName'];
	
	$countryName=ereg_replace("[^A-Za-z]"," ",$countryName);
	
	$countryName=str_replace("-","%20",$countryName);
	$countryName=str_replace("_","%20",$countryName);
	$countryName=str_replace(",","%20",$countryName);
	$countryName=str_replace(".","%20",$countryName);
	$countryName=str_replace(" ","%20",$countryName);
	
	//echo $countryName;
	
	$server   = 'localhost'; // MySQL hostname
	$username = 'tettrain'; // MySQL username
	$password = 'seU49T9c49'; // MySQL password
	$dbname   = 'tettrain_newsonmap'; // MySQL db name
	$db = mysql_connect($server, $username, $password) or die(mysql_error());
	    //  mysql_select_db($dbname) or die(mysql_error());
	


	
	
//news 
$url = "https://ajax.googleapis.com/ajax/services/search/news?"."v=1.0&key=ABQIAAAAq6AjbvcgTwJGnSaeJEMrhBQs76AG7O2n92rHgKLP4CNVCExmaBQFRp3ylWx0yFRhthB7BZ1LIhk0vw&userip=".$ipaddress."&rsz=8"."&q=".$countryName;//."&geo=".$countryName;

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
//var_dump($json);



$key = 'f4mqH67V34H5BcCVyPdYY1Xixp7S1gyH2YKGH46ZHt676pj1kTk_phgB0z01hxyr3z_O';
$apiendpoint = 'http://wherein.yahooapis.com/v1/document';
$inputType = 'text/plain';
$outputType = 'json';
	
	$title_array=array();
	$content_array=array();
	$link_array=array();
	
	//$db = mysql_connect($server, $username, $password) or die(mysql_error());
$dbname   = 'tettrain_mapmyfriend'; // MySQL db name
mysql_select_db($dbname) or die(mysql_error());
$latlng_all_array = array ();	
$placename_all_array=array();
$thumbnail_url_array=array();
$news_obtained_length=sizeof($json[responseData][results]);
if($news_obtained_length==0){
echo "-1";
}
else{
foreach($json[responseData][results] as $p){
$my_array = array ();
$placename_array=array();
//echo '<br>----------------------------------<br><br>content = '.$p[content];
//echo '<br>url = '.$p[unescapedUrl];

//make the yahoo placemaker query

$title=$p[title];
$content=$p[content];
$title_edited=str_replace("<b>"," ",$title);
$title_edited=str_replace("</b>"," ",$title_edited);

$content_edited=str_replace("<b>"," ",$content);
$content_edited=str_replace("</b>"," ",$content_edited);

$link=$p[unescapedUrl];
$thumbnail_url=$p[image][tbUrl];


$post = 'appid='.$key.'&documentContent='.$title_edited.','.$content_edited.'&documentType='.$inputType.'&outputType='.$outputType;
$ch = curl_init($apiendpoint);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$results = curl_exec($ch);

//echo $results;
$json_placemark = json_decode($results);
//var_dump($json_placemark);

	//echo "<br>first  = ".$json_placemark->document->referenceList->reference->text."<br>";

	$count=count($json_placemark->document->referenceList);
	//echo "<br>count=".$count;
	if($count>=1){
		array_push($title_array,$title);
		array_push($content_array,$content);
		array_push($link_array,$link);
		array_push($thumbnail_url_array,$thumbnail_url);
	}
	if ($count>1)
	{
	foreach($json_placemark->document->referenceList as $k){
		$placeName=$k->reference->text;
		//echo '<br>*****************'.$placeName;
		$latlng=searchInDataBase($placeName);
		array_push($my_array,$latlng);
		array_push($placename_array,$placeName);	
	}
	}elseif ($count==1) {
		$placeName=$json_placemark->document->referenceList->reference->text;
	//	echo '<br>*****************'.$placeName; 
			$latlng=searchInDataBase($placeName);
			array_push($my_array,$latlng);
			array_push($placename_array,$placeName);
	}
	if($count>=1){
		array_push($latlng_all_array,$my_array);
		array_push($placename_all_array,$placename_array);
	}
//print_r($my_array);
}

//print_r($content_array);
//print_r($link_array);
//print_r($latlng_all_array);

//data to be send back through ajax
$data=array($content_array,$link_array,$latlng_all_array,$title_array,$placename_all_array,$thumbnail_url_array);
$json_data=json_encode($data);
echo $json_data;

}


	function getGeoCodeJSON($placeName){
	
			$placeName1 = str_replace(" ", "%20", $placeName);
			$jsonurl = "http://where.yahooapis.com/geocode?location=".$placeName1."&flags=J&appid=dj0yJmk9aFVuOEptNWhlR0VsJmQ9WVdrOVVrVjNhbloyTkdjbWNHbzlNakV6TkRBeE9UVTJNZy0tJnM9Y29uc3VtZXJzZWNyZXQmeD1lNg--";
			
			//echo '<br/>'.'yahoo geocoding used for '.$placeName.'<br/>';
			$json = file_get_contents($jsonurl);
			$json_output = json_decode($json);
			return $json_output;
	}

	function getLat($json_output){	
		$lat=$json_output->ResultSet->Results[0]->latitude;	
		return $lat;
	} 

	function getLon($json_output){
		$lng=$json_output->ResultSet->Results[0]->longitude;
		return $lng;
	}
	function insertIntoDB_geocode_table($placeName,$lat,$lng){
		$insertSql="Insert Into geocode (location,lat,lng) Values ('$placeName',$lat,$lng) ;";
	//	echo 'insertSql = '.$insertSql.'<br/>';		
		$isInserted = mysql_query($insertSql);
		return $isInserted;
	}
	

	function searchInDataBase($placeName){
		$searchQuery="Select lat,lng from geocode where location='$placeName';";
	//	echo 'seachQuery='.$searchQuery.'<br/>';
		$resultSet=mysql_query($searchQuery);		
		$row=mysql_fetch_row($resultSet);
		
	
		
		if(mysql_num_rows($resultSet)==0){
			//echo 'No result Found for location='.$placeName.'<br/>';
			//echo 'going to web to geocode anf then will store in db for future'.'<br/>';
			$json_output=getGeoCodeJSON($placeName);
			$lat=getLat($json_output);
			$lng=getLon($json_output);
			$isInserted=insertIntoDB_geocode_table($placeName,$lat,$lng);
		//	echo 'inserted = '.$isInserted;
		//	echo "placeName=".$placeName."=> lat=".$lat.",lng=".$lng.'<br/>';
		}
		else{
		//	echo 'found '.$placeName.' in DB lat='.$row[0].", lng=".$row[1];
			$lat=$row[0];
			$lng=$row[1];
		}
		
		mysql_free_result($resultSet);
		$latlng=array($lat,$lng);
		return $latlng;
	
		
		
	}
?>