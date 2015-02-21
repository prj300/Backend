<?php
/**
 * Created by PhpStorm.
 * User: Enda
 * Date: 17/02/2015
 * Time: 13:40
 */

// Database connection path
include 'db/config.php';

// Override default time limit
// The default timeout is 30 seconds
set_time_limit(0);
ignore_user_abort(true);
ini_set('max_execution_time', 0);

// check if file path exists
if(file_exists('C:\Users\Enda\Documents\wildpoints.xml')) {

    // store it in a variable
    $gpx = simplexml_load_file('C:\Users\Enda\Documents\wildpoints.xml');

    // get the nested node <trkpt>
    foreach ($gpx->trk->trkseg->trkpt as $pt) {
        // store the trkpt attributes in a variable at position
        $lat = (double) $pt['lat'];
        $lon = (double) $pt['lon'];

        // insert into database
        $insert = mysqli_query($link, "INSERT INTO wild_atlantic_way(latitude, longitude) VALUES ('$lat', '$lon')");

    }

    if($insert) {
        echo "Success";
    }

}