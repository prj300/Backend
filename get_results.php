<?php
/**
 * Created by PhpStorm.
 * User: Enda
 * Date: 16/02/2015
 * Time: 18:02
 */

// this will hold the JSON response that will be sent back to the client
$response = array();

// database configuration path
include "db/config.php";

if(isset($_POST['user_id'])) {

    // read incoming data into variable
    $user_id = mysqli_real_escape_string($link, $_POST['user_id']);

    $get = mysqli_query($link, "SELECT * FROM results WHERE user_id = '$user_id'");

    // if there are no routes created by requested user
    if(!$get) {
        $response["success"] = 0;
        $response["message"] = "No results";
        echo json_encode($response);
    } else {

        // array tol hold results
        $response["results"] = array();


        while($row = mysqli_fetch_array($get)) {
            $result = array();
            $result["result_id"] = $row["result_id"];
            $result["route_id"] = $row["route_id"];
            $result["distance"] = $row["distance"];
            $result["max_speed"] = $row["max_speed"];
            $result["avg_speed"] = $row["average_speed"];
            $result["time"] = $row["total_time"];
            $result["date_created"] = $row["date_created"];

            array_push($response["results"], $result);
        }

        $response["success"] = 1;
        $response["message"] = "Results available";
        echo json_encode($response);
    }

} else {
    $response["success"] = -1;
    $response["message"] = "Missing field(s)";
    echo json_encode($response);
}