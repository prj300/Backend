<?php
/**
 * Created by PhpStorm.
 * User: Enda
 * Date: 24/02/2015
 * Time: 18:16
 */

if(isset($_POST['tag']) && $_POST['tag'] != '') {

    include "db/config.php";
    require_once 'route_functions.php';

    $tag = $_POST['tag'];
    $functions = new route_functions();

    if($tag == 'dist') {
        $latitude = mysqli_real_escape_string($link, $_POST['latitude']);
        $longitude = mysqli_real_escape_string($link, $_POST['longitude']);
        $distance = mysqli_real_escape_string($link, $_POST['distance']);
        $dist = $distance * 1000;

        $id = $functions->findNearestPoint($link, $latitude, $longitude);
        $route = $functions->createRoute($link, $dist, $id);
        if($route) {
            $response["success"] = true;
            $response["distance"] = $route["distance"];
            $response["route"] = $route;
            echo json_encode($response);
        } else {
            $response["success"] = false;
            $response["message"] = "No route";
            echo json_encode($response);
        }
    } else if($tag == 'new') {
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

        $route_id = $functions->saveNewRoute($link, $user_id, $grade, $terrain, $distance);

        if($route_id != null) {
            $route = $functions->saveLatLngs($link, $route_id, $latitudes, $longitudes);
            if($route) {
                $response["success"] = true;
                $response["message"] = "Route saved!";
                echo json_encode($response);
            } else {
                $response["success"] = false;
            }

        }

    }
} else {
    $response["success"] = false;
    $response["message"] = "No tag";
    echo json_encode($response);
}