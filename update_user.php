<?php
/**
 * Created by PhpStorm.
 * User: Enda
 * Date: 14/02/2015
 * Time: 21:19
 */

// Connection path to database
include 'db/config.php';

// Array to hold JSON response
$response = array();

/**
 * Check if required incoming data is there
 */

if(isset($_POST['user_id']) && isset($_POST['distance']) &&
    isset($_POST['time']) && isset($_POST['max_speed']) &&
    isset($_POST['average_speed'])) {

    /**
     * Set variables to POST data
     * Trim to prevent SQL injection
     */
    $user_id = mysqli_real_escape_string($link, $_POST['user_id']);
    $distance = mysqli_real_escape_string($link, $_POST['distance']);
    $time = mysqli_real_escape_string($link, $_POST['time']);
    $max_speed = mysqli_real_escape_string($link, $_POST['max_speed']);
    $average_speed = mysqli_real_escape_string($link, $_POST['average_speed']);

    /**
     * Update query
     */
    $update = mysqli_query($link, "UPDATE users SET total_distance_km='$distance',
    max_speed='$max_speed', avg_speed='$average_speed', date_updated=NOW()
    WHERE user_id='$user_id'");

    if($update) {
        $response["success"] = 1;
        $response["message"] = "Update successful";
        echo json_encode($response);
    } else {
        $response["success"] = 0;
        $response["message"] = "Update unsuccessful";
        echo json_encode($response);
    }
} else {
    $response["success"] = -1;
    $response["message"] = "Missing field(s)";
    echo json_encode($response);
}