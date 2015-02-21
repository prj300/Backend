<?php
/**
 * Created by PhpStorm.
 * User: Enda
 * Date: 16/02/2015
 * Time: 21:29
 */

include 'db/config.php';

$gpx = simplexml_load_file("waw.gpx");

foreach($gpx->wpt as $pt) {
    $lat = (string) $pt['lat'];
    $lon = (string) $pt['lon'];

    $insert = mysqli_query($link, "INSERT INTO wild_atlantic_way(latitude, longitude) VALUES('$lat', '$lon')");

    if($insert) {
        echo "Nope.";
    } else {
        echo "nanah";
    }

}
unset($gpx);