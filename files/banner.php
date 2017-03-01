<?php

$ar2=array('index','price','specification','location','floorplan','siteplan','contact','privacy','thankyou');
$url='http://krrishicon.com/advertisement/advertisement.php';
$project['image']=array();


	foreach ($ar2 as $key => $value) {
		$project['image']["$value"]=array();
$banner=json_decode(file_get_contents($url."?width=$width1&height=$height1&page=$value&id=$projectid&domain=$domain"),true);
//echo "---".$url."?width=$width1&height=$height1&page=$value&id=$projectid&domain=$domain"."--";
if(sizeof($banner)>=2)
$project['image']["$value"][1]='<a rel="nofollow" href="'.$banner['href'].'"  target="_blank"><img  src="'.$banner['src'].'" /></a>';
else 
$project['image']["$value"][1]='<a rel="nofollow" href="#"><img  src="images/banner.gif" /></a>';

$banner=json_decode(file_get_contents($url."?width=$width2&height=$height2&page=$value&id=$projectid&domain=$domain"),true);
if(sizeof($banner)>=2)
$project['image']["$value"][2]='<a rel="nofollow" href="'.$banner['href'].'"  target="_blank"><img  src="'.$banner['src'].'" /></a>';
else
$project['image']["$value"][2]='<a rel="nofollow" href="#"><img  src="images/banner.gif" /></a>';
//break;
	}


?>