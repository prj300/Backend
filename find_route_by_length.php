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

$lat = 51.760587483;
$long = -8.500319597;
$distance = 5;


$length = 0;
$earthRadius = 6371000;

$query = mysqli_query($link, "SELECT id, latitude, longitude,
                ( '$earthRadius' * acos(cos(radians('$lat'))
                * cos(radians(latitude)) * cos(radians(longitude)
                - radians('$long')) + sin(radians('$lat'))
                * sin(radians(latitude)))) AS distance
                FROM wild_atlantic_way
                ORDER BY distance LIMIT 1;");

if(mysqli_num_rows($query)) {
    $row = mysqli_fetch_array($query);
    echo $row["id"];
}



/*
while($distance < 10) {
    $query = mysqli_query($link, "SELECT latitude, longitude FROM wild_atlantic_way");
    $row = mysqli_fetch_array($query);
}*/