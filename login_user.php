<?php
/**
 * Created by PhpStorm.
 * User: Enda
 * Date: 03/01/2015
 * Time: 21:27
 */

// connection path
include 'db/config.php';

if(isset($_POST['email']) && isset($_POST['password'])) {

    $response = array();

    $email = mysqli_real_escape_string($link, $_POST['email']);
    $password = mysqli_real_escape_string($link, $_POST['password']);

    // get user from email address
    $check = mysqli_query($link, "SELECT * FROM users WHERE email = '$email'");

    // row count
    $row = mysqli_fetch_array($check);

    // if user exists
    if ($row) {
        $hashed_password = $row['hashed_password'];

        if (password_verify($password, $hashed_password)) {
            $result = mysqli_query($link, "SELECT * FROM users WHERE email = '$email' AND hashed_password = '$hashed_password'");

            if ($result) {
                // login successful
                $response["success"] = 1;
                $response["message"] = "Login successful";

                // respond with all user's details
                $response["id"] = $row["user_id"];
                $response["grade"] = $row["grade"];
                $response["experience"] = $row["experience"];
                $response["totaldistance"] = $row["total_distance_km"];
                $response["totaltime"] = $row["total_time"];
                $response["maxspeed"] = $row["max_speed"];
                $response["avgspeed"] = $row["avg_speed"];

                echo json_encode($response);
            } else {
                // login unsuccessful
                $response["success"] = -1;
                $response["message"] = "Login unsuccessful";
                echo json_encode($response);
            }
        } else {
            // login successful
            $response["success"] = -2;
            $response["message"] = "Password incorrect";
            echo json_encode($response);
        }
    } else {
        $response["success"] = -3;
        $response["message"] = "User does not exist";
        echo json_encode($response);
    }
} else {
    $response["success"] = 0;
    $response["message"] = "Missing fields";
    echo json_encode($response);
}