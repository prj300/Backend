<?php
/**
 * Created by PhpStorm.
 * User: Enda
 * Date: 03/01/2015
 * Time: 17:13
 */

/**
 * Database config variables
 */
$link = mysqli_connect("localhost", "root", "");
mysqli_select_db($link, "apex.db");

if(!$link) {
    $error = "Could not connect to database";
    echo $error;
    exit();
}

if(!mysqli_select_db($link, 'apex.db')) {
    $error = "Unable to locate events";
    echo $error;
    exit();
}

if(!mysqli_set_charset($link, "utf8")) {
    $error = "Unable to set database encoding";
    echo $error;
    exit();
}

