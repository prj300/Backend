<?php
/**
 * Created by PhpStorm.
 * User: Enda
 * Date: 22/02/2015
 * Time: 19:44
 */

// this will hold the JSON response that will be sent back to the client
$response = array();

// database configuration path
include "db/config.php";

$latitude = 54.0810356;
$longitude = -7.277504712;
$latFrom = deg2rad($latitude);
$lonFrom = deg2rad($longitude);


$distance = 0;
$earthRadius = 6371000;

$sql = mysqli_query($link, "SELECT id, (
          6371 * acos (
          cos (radians('$latitude'))
          * cos(radians(latitude))
          * cos(radians(longitude) - radians('$longitude'))
          + sin(radians('$longitude'))
          * sin(radians(latitude))
          )
        ) as distance
        FROM wild_atlantic_way
        having distance < 100");

if($sql) {
    if(mysqli_num_rows($sql) > 0) {
        echo "jj";
    } else {
        echo "hmm";
    }
}


/*
while($distance < 10) {
    $query = mysqli_query($link, "SELECT latitude, longitude FROM wild_atlantic_way");
    $row = mysqli_fetch_array($query);
}*/