<?php
/**
 * Created by PhpStorm.
 * User: Enda
 * Date: 12/02/2015
 * Time: 18:57
 */

// this will hold the JSON response that will be sent back to the client
$response = array();

// database configuration path
include "db/config.php";

if(isset($_POST['user_id'])) {

    // read incoming data into variable
    $user_id = mysqli_real_escape_string($link, $_POST['user_id']);

    $get = mysqli_query($link, "SELECT * FROM routes WHERE user_id = '$user_id'");

    // if there are no routes created by requested user
    if(mysqli_num_rows($get) = 0) {
        $response["success"] = 0;
        $response["message"] = "No routes";
        echo json_encode($response);
    } else {

        $response["routes"] = array();

        while($row = mysqli_fetch_array($get)) {
            $route = array();
            $route["route_id"] = $row["route_id"];
            $route["grade"] = $row["grade"];
            $route["terrain"] = $row["terrain"];
            $route["distance"] = $row["distance"];
            $route["date_created"] = $row["date_created"];

            array_push($response["routes"], $route);
        }

        $response["success"] = 1;
        $response["message"] = "Route(s) available";
        echo json_encode($response);
    }

} else {
    $response["success"] = -1;
    $response["message"] = "Missing field(s)";
    echo json_encode($response);
}