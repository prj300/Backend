<?php
/**
 * Created by PhpStorm.
 * User: Enda
 * Date: 03/01/2015
 * Time: 17:21
 */

// connection to database
include 'db/config.php';

$response = array();

if(isset($_POST['email']) && isset($_POST['password'])) {

    //$email = "endaphelan1993@gmail.com";
    //$password = "12345";
    $email = mysqli_real_escape_string($link, $_POST['email']);
    $password = mysqli_real_escape_string($link, $_POST['password']);

    // Check if email address is valid
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response["success"] = -3;
        $response["message"] = "Invalid email format.";
        echo json_encode($response);
    } else {

        // check if user already exists
        $check = mysqli_query($link, "SELECT * FROM users WHERE email = '$email'");

        // get row
        $row = mysqli_fetch_array($check);

        // check row count value
        if ($row) {
            // user exists
            $response["success"] = -1;
            $response["message"] = "A user with this e-mail already exists";

            // return response to client
            echo json_encode($response);
        } else {

            // hash password
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);

            $insert = mysqli_query($link, "INSERT INTO users(email, hashed_password, date_created)
                                   VALUES('$email', '$hashed_password', NOW())");

            // check if insert successful
            $check = mysqli_query($link, "SELECT * FROM users WHERE email = '$email'");
            // row count
            $row = mysqli_fetch_array($check);

            // if row exists
            if ($row) {
                $response["success"] = 1;
                $response["message"] = "Successfully registered";

                // respond with all user's details
                $response["id"] = $row["user_id"];
                $response["grade"] = $row["grade"];
                $response["totaldistance"] = $row["total_distance_km"];
                $response["totaltime"] = $row["total_time"];
                $response["maxspeed"] = $row["max_speed"];
                $response["avgspeed"] = $row["avg_speed"];



                /**
                 * Send confirmation email after registering
                 */
                // send email to email address
                $to = $email;

                // subject
                $subject = "Welcome to Apex.";

                // $message
                $message = "Thank you for signing up to Project Apex!";

                // send email
                $sendemail = mail($to, $subject, $message);

                if($sendemail) {
                    $response["message"] = "Confirmation e-mail sent.";
                }

                // send json back to client
                echo json_encode($response);


            } else {
                $response["success"] = 0;
                $response["message"] = "Registration failed";

                echo json_encode($response);
            }
        }
    }
} else {
    $response["success"] = -2;
    $response["message"] = "Required field(s) missing";
    echo json_encode($response);
}
