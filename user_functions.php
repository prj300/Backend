<?php
/**
 * Created by PhpStorm.
 * User: Enda
 * Date: 04/03/2015
 * Time: 22:48
 */


class user_functions {

    /**
     * Check if password is correct
     */
    public function confirmPassword($link, $email, $password)
    {
        $query = mysqli_query($link, "SELECT * FROM users WHERE email='$email'");
        $row = mysqli_fetch_array($query);

        $hash = $row["hashed_password"];

        // compare inputted password against the actual password
        if(!password_verify($password, $hash)) {
            return false;
        } else {
            return true;
        }

    }

    /**
     * Check if user exists
     */
    public function checkUser($link, $email)
    {
        $user = mysqli_query($link, "SELECT * FROM users WHERE email = '$email'");
        $row = mysqli_fetch_array($user);

        if($row) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Add user to database
     */
    public function createUser($link, $email, $password)
    {
        // hash password using BCrypt
        $hash = password_hash($password, PASSWORD_BCRYPT);

        // create ane execute insert query
        $query = "INSERT INTO users(email, hashed_password) VALUES ('$email', '$hash')";
        mysqli_query($link, $query);

    }

    /**
     * Validate email regex
     */
    public function validateEmail($email)
    {
        // built in validator
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Retrieve user from database
     */
    public function getUser($link, $email)
    {
        $query = mysqli_query($link, "SELECT * FROM users WHERE email = '$email'");

        $row = mysqli_fetch_array($query);

        $user["id"] = $row["user_id"];
        $user["email"] = $row["email"];
        $user["grade"] = $row["grade"];
        $user["total_distance"] = $row["total_distance_km"];
        $user["total_time"] = $row["total_time"];
        $user["max_speed"] = $row["max_speed"];
        $user["avg_speed"] = $row["avg_speed"];

        return $user;

    }

    /**
     * Retrieve user's results
     */
    public function getResults($link, $user_id)
    {
        // select all results from table
        $query = mysqli_query($link, "SELECT * FROM results WHERE user_id='$user_id'");

        // array to hold results
        $result = array();
        // if there are results available
        if(!mysqli_num_rows($query)) {
            return null;
        } else {
            // loop through rows adding them to an array
            while($row = mysqli_fetch_array($query)) {
                $result["result_id"] = $row["result_id"];
                $result["route_id"] = $row["route_id"];
                $result["distance"] = $row["distance"];
                $result["max_speed"] = $row["max_speed"];
                $result["avg_speed"] = $row["average_speed"];
                $result["time"] = $row["total_time"];
                $result["date_created"] = $row["date_created"];
            }
            return $result;
        }
    }

    /**
     * Update query
     */
    public function updateUser($link, $user_id, $distance, $time, $max_speed, $average_speed)
    {
        $update = mysqli_query($link, "UPDATE users SET total_distance_km='$distance',
                  total_time='$time', max_speed='$max_speed', avg_speed='$average_speed',
                  date_updated=NOW() WHERE user_id='$user_id'");

        // bool confirms if update worked
        if(!mysqli_affected_rows($link)) {
            return false;
        } else {
            return true;
        }

    }

    /**
     * Send password reset link to user
     */
    public function sendPasswordReset($link, $email)
    {
        // get previously hashed password from table
        $query = mysqli_query($link, "SELECT hashed_password FROM users WHERE email='$email'");
        $row = mysqli_fetch_array($query);
        $hash = $row["hashed_password"];

        // build email
        $subject = "Password Reset";
        $headers = 'From: webmaster@example.com' . "\r\n" .
            'Reply-To: webmaster@example.com' . "\r\n" .
            'X-Mailer: PHP/' . phpversion();
        $message = "<a href='http://localhost/apex/views/reset_password.php?email=$email&code=$hash>";

        mail($email, $subject, $message, $headers);

    }

    public function updateUserGrade($link, $grade)
    {
        $query = mysqli_query($link, "UPDATE users SET grade='$grade' WHERE user_id='$id'");

        if(!mysqli_insert_id($link)) {
            return false;
        } else {
            return true;
        }
    }

}