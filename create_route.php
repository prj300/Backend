<?php
/**
 * Created by PhpStorm.
 * User: Enda
 * Date: 24/01/2015
 * Time: 15:48
 */

// Connection path to database
include 'db/config.php';

// Array to hold JSON response
$response = array();

/**
 * Check if required incoming data is there
 */

// If all values are set continue with procedure
if(isset($_POST['user_id']) && isset($_POST['grade'])
    && isset($_POST['terrain']) && isset($_POST['latitudes'])
    && isset($_POST['longitudes']) && isset($_POST['distance'])
    && isset($_POST['max_speed']) && isset($_POST['avg_speed'])
    && isset($_POST['time'])) {

    /**
     * Set variables to POST data
     * Trim to prevent SQL injection
     */
    $user_id = mysqli_real_escape_string($link, $_POST['user_id']);
    $grade = mysqli_real_escape_string($link, $_POST['grade']);
    $terrain = mysqli_real_escape_string($link, $_POST['terrain']);
    $latitudes = mysqli_real_escape_string($link, $_POST['latitudes']);
    $longitudes = mysqli_real_escape_string($link, $_POST['longitudes']);
    $distance = mysqli_real_escape_string($link, $_POST['distance']);
    $max_speed = mysqli_real_escape_string($link, $_POST['max_speed']);
    $avg_speed = mysqli_real_escape_string($link, $_POST['avg_speed']);
    $time = mysqli_real_escape_string($link, $_POST['time']);

    // Decode latitude and longitude strings back to arrays
    $lats = json_decode($latitudes, true);
    $longs = json_decode($longitudes, true);


    /**
     * Insert Queries
     */
    // Insert procedure on Routes Table
    $route = mysqli_query($link, "INSERT INTO routes(user_id, grade, terrain, distance, date_created)
              VALUES ('$user_id', '$grade', '$terrain', '$distance', NOW())");

    // ID of last inserted route
    $route_id = mysqli_insert_id($link);

    // Insert results into Results table
    $results = mysqli_query($link, "INSERT INTO results(user_id, route_id,
    distance, max_speed, average_speed, total_time, date_created)
                            VALUES('$user_id', '$route_id', '$distance',
                            '$max_speed', '$avg_speed', '$time', NOW())");

    $result_id = mysqli_insert_id($link);

    // If route was successfully saved
    if($route) {

        // Iterating through $lats and $longs arrays, inserting each value into the table
        // Along with the ID of the relative Route from the route table
        for ($i = 0; $i < count($lats) && $i < count($longs); $i++) {
            $lat = $lats[$i];
            $long = $longs[$i];

            $lats_longs = mysqli_query($link, "INSERT INTO lats_longs
                        (route_id, latitude, longitude)
                        VALUES ('$route_id', '$lat', '$long')");
        }

        $response["success"] = 1;
        $response["message"] = "Insert successful.";
        $response["route_id"] = $route_id;
        $response["result_id"] = $result_id;
        echo json_encode($response);

    } else {
    $response["success"] = 0;
    $response["message"] = "Insert failed.";
    echo json_encode($response);
}

// Fields missing
} else {
    $response["success"] = -1;
    $response["message"] = "Field(s) missing.";
    echo json_encode($response);
}
