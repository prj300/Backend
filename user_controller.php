<?php
/**
 * Created by PhpStorm.
 * User: Enda
 * Date: 04/03/2015
 * Time: 22:42
 */

if(isset($_POST['tag']) && $_POST['tag'] != '') {

    include "db/config.php";
    require_once "user_functions.php";


    $tag = $_POST['tag'];
    $functions = new user_functions();
    $response = array();

    /**
     * Call login functions
     */
    if($tag == 'login') {
        $email = mysqli_real_escape_string($link, $_POST['email']);
        $password = mysqli_real_escape_string($link, $_POST['password']);

        // check if user exists
        $val = $functions->validateEmail($email);

        if($val) {
            $check = $functions->checkUser($link, $email);

            if($check) {
                $confirmPassword = $functions->confirmPassword($link, $email, $password);
                if($confirmPassword) {
                    $response["success"] = true;
                    $response["message"] = "Login successful";
                    $response["user"] = $functions->getUser($link, $email);
                    echo json_encode($response);
                } else {
                    $response["success"] = false;
                    // actually means password, but we don't want
                    // potential hackers knowing which is right/wrong
                    $response["message"] = "Incorrect email/password";
                    echo json_encode($response);
                }
            } else {
                $response["success"] = false;
                $response["message"] = "There is no user with this email";
                echo json_encode($response);
            }
        } else {
            $response["success"] = false;
            $response["message"] = "Invalid email";
            echo json_encode($response);
        }


        /**
         * Call register functions
         */
    } else if($tag == 'register') {
        $email = mysqli_real_escape_string($link, $_POST['email']);
        $password = mysqli_real_escape_string($link, $_POST['password']);

        // validate email address
        $val = $functions->validateEmail($email);

        // email provided is not in a valid format
        if(!$val) {
            $response["success"] = $val;
            $response["message"] = "Invalid email address";
            echo json_encode($response);
        } else {
            // check if a user with this email already exists
            if($functions->checkUser($link, $email)) {
                $response["success"] = false;
                $response["message"] = "A user with this email already exists";
                echo json_encode($response);
            } else {
                // create user
                $createUser = $functions->createUser($link, $email, $password);
                // if successful
                if($createUser) {
                    $response["success"] = true;
                    $response["user"] = $functions->getUser($link, $email);
                    $response["message"] = "Registration successful";
                    echo json_encode($response);
                } else {
                    $response["success"] = false;
                    $response["message"] = "Whoops, something went wrong!";
                    echo json_encode($response);
                }
            }
        }
        /**
         * Call functions to download user's results
         */
    } else if ($tag == 'results') {
        $user_id = mysqli_real_escape_string($link, $_POST['user_id']);

        $results = $functions->getResults($link, $user_id);

        if(!$results) {
            $response["success"] = false;
            $response["message"] = "You do not have any results yet!";
            echo json_encode($response);
        } else {
            $response["success"] = true;
            $response["results"] = $results;
            echo json_encode($response);
        }
    } else if($tag == 'update') {
        $user_id = mysqli_real_escape_string($link, $_POST['user_id']);
        $distance = mysqli_real_escape_string($link, $_POST['distance']);
        $time = mysqli_real_escape_string($link, $_POST['time']);
        $max_speed = mysqli_real_escape_string($link, $_POST['max_speed']);
        $average_speed = mysqli_real_escape_string($link, $_POST['average_speed']);

        // $update = $functions->updateUser($link, $user_id, $distance, $time, $max_speed, $average_speed);

        $response["success"] = true;
        $response["message"] = "Update successful";
        echo json_encode($response);

        /*
        if($update) {
            $response["success"] = true;
            $response["message"] = "Update successful";
            echo json_encode($response);
        } else {
            $response["success"] = false;
            $response["message"] = "Update failed";
            echo json_encode($response);
        }*/
    }

} else {
    $response["success"] = false;
    $response["message"] = "Tag Missing.";
    echo json_encode($response);
}