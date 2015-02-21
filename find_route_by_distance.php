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

if(isset($_POST['distance'])) {

    // Pass incoming data into variables
    $distance = mysqli_real_escape_string($link, $_POST['distance']);

    // set the range at which the query searches for a route
    $lower = (float)$distance-3;
    $higher = (float)$distance+3;

    // search for routes between the range
    $query = mysqli_query($link, "SELECT * FROM routes WHERE distance BETWEEN '$lower' AND '$higher'
    ORDER BY distance ASC LIMIT 10");

    // Routes available
    if(mysqli_num_rows($query)) {
        $response["success"] = 1;
        $response["message"] = "Routes Available";

        // create an array to store routes
        $response["routes"] = array();
        while($row = mysqli_fetch_array($query)) {
            $route = array();
            $route["route_id"] = $row["route_id"];
            $route["user_id"] = $row["user_id"];
            $route["grade"] = $row["grade"];
            $route["terrain"] = $row["terrain"];
            $route["distance"] = $row["distance"];
            $route["date_created"] = $row["date_created"];

            // add the route to array
            array_push($response["routes"], $route);
        }
        echo json_encode($response);
    } else {
        $response["success"] = 0;
        $response["message"] = "No Routes available";
        echo json_encode($response);
    }
} else {
    $response["success"] = -1;
    $response["message"] = "Missing field";
    echo json_encode($response);
}