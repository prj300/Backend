<?php
/**
 * Created by PhpStorm.
 * User: Enda
 * Date: 21/02/2015
 * Time: 17:42
 */

// this will hold the JSON response that will be sent back to the client
$response = array();

// database configuration path
include "db/config.php";

if(isset($_POST['distance']) && isset($_POST['latitude'])
    && isset($_POST['longitude'])) {

    // Pass incoming data into variables
    $distance = mysqli_real_escape_string($link, $_POST['distance']);
    $latitude = mysqli_real_escape_string($link, $_POST['latitude']);
    $longitude = mysqli_real_escape_string($link, $_POST['longitude']);

    $earthRadius = 6371;

    // http://gis.stackexchange.com/a/31629
    $location = mysqli_query($link, "SELECT
          id, latitude, longitude (
          '$earthRadius' * acos (
          cos (radians('$latitude'))
          * cos(radians(latitude))
          * cos(radians(longitude)
          - radians('$longitude'))
          + sin(radians('$longitude'))
          * sin(radians(latitude))
          )
        ) as distance
        FROM wild_atlantic_way
        having distance < '$distance'");

    // Routes available
    if(mysqli_num_rows($location)) {
        $row = mysqli_fetch_array($location);
        $route_length = 0;

        $route = array();

        for($id = $row["id"]; $route_length < $distance; $id++) {
            $latitude = $row["latitude"];
            $longitude = $row["longitude"];
            $route["id"] = $id;
            $route["latitude"] = $row["latitude"];
            $route["longitude"] = $row["longitude"];

            array_push($response, $route);

            $q = mysqli_query($link, "SELECT *
                                      FROM wild_atlantic_way
                                      WHERE id = '$id'");
        }

        echo json_encode($response);
    }

} else {
    $response["success"] = -1;
    $response["message"] = "Missing field";
    echo json_encode($response);
}