<?php
/**
 * Created by PhpStorm.
 * User: Enda
 * Date: 24/02/2015
 * Time: 18:11
 */

class route_functions {

    /**
     * Saving a route to the database
     */
    public function saveNewRoute($link, $user_id, $grade, $terrain, $distance)
    {
        $route_id = 0;
        // Insert procedure on Routes Table
        $route = mysqli_query($link, "INSERT INTO routes(user_id, grade, terrain, distance, date_created)
              VALUES ('$user_id', '$grade', '$terrain', '$distance', NOW())");

        if(mysqli_num_rows($route)) {
            // ID of last inserted route
            $route_id = mysqli_insert_id($link);

        }
        return $route_id;
    }

    /**
     * Finds nearest point on the Wild Atlantic Way to the user's location
     */
    public function findNearestPoint($link, $lat, $long)
    {
        $earthRadius = 6371000;

        $query = mysqli_query($link, "SELECT id, ( '$earthRadius'
                              * acos( cos( radians('$lat') ) * cos( radians( latitude ) )
                              * cos( radians( longitude ) - radians('$long') )
                              + sin( radians('$lat') ) * sin(radians(latitude)) ) ) AS distance
                              FROM wild_atlantic_way
                              ORDER BY distance
                              LIMIT 1;");


        if (mysqli_num_rows($query)) {
            $row = mysqli_fetch_array($query);
            return $row["id"];
        } else {
            return false;
        }
    }

    /**
     * Build a route to return to the phone based on distance
     */
    function createRoute($link, $distance, $id)
    {
        $data = array();
        $data["route"] = array();

        $lats = array();
        $longs = array();

        $length = 0;

        // Increment through the Wild Atlantic Way Table and add each point to an array
        // Continue to increment through the table until the distance has reached the
        // requested inputted distance by the user
        while($length < ($distance)) {

            $query = mysqli_query($link, "SELECT id, latitude, longitude
                                          FROM wild_atlantic_way
                                          WHERE id='$id'");

            $row = mysqli_fetch_array($query);
            $lat = $row["latitude"];
            $lng = $row["longitude"];
            array_push($lats, $lat);
            array_push($longs, $lng);

            /*
            while(count($lats) > 0) {
                $latA = (end($lats) - 1);
                $longA = (end($longs) - 1);
                $longB = (end($longs));
                $latB = (end($lats));
                $length += ($this->totalDistance($latA, $longA, $latB, $longB));
            }*/
            $latA = (end($lats) - 1);
            $longA = (end($longs) - 1);
            $longB = (end($longs));
            $latB = (end($lats));
            $length += ($this->totalDistance($latA, $longA, $latB, $longB));

            $i = array("id" => $row["id"], "lat" => $row["latitude"], "lng"=> $row["longitude"]);
            array_push($data["route"], $i);

            $id++;

        }
        $data["distance"] = $length;
        return $data;
    }

    /**
     * Haversine's formula
     * Tracks distance between two locations
     */
    // http://stackoverflow.com/a/10054282
    function totalDistance($latA, $longA, $latB, $longB)
    {
        $earthRadius = 6371000;

        // convert from degrees
        $latFrom = deg2rad($latA);
        $lonFrom = deg2rad($longA);
        $latTo = deg2rad($latB);
        $lonTo = deg2rad($longB);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta/2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));

        return ($angle * $earthRadius) / 250;
    }

    public function saveResults($link, $user_id, $route_id, $distance, $max_speed, $avg_speed, $time)
    {
        // Insert results into Results table
        $results = mysqli_query($link, "INSERT INTO results(user_id, route_id,
                            distance, max_speed, average_speed, total_time, date_created)
                            VALUES('$user_id', '$route_id', '$distance',
                            '$max_speed', '$avg_speed', '$time', NOW())");

        $result_id = mysqli_insert_id($link);

        if(!$result_id) {
            return null;
        }
        return $result_id;
    }

    public function saveLatLngs($link, $route_id, $lats, $longs)
    {
        // Iterating through $lats and $longs arrays, inserting each value into the table
        // Along with the ID of the relative Route from the route table
        for ($i = 0; $i < count($lats) && $i < count($longs); $i++) {
            $lat = $lats[$i];
            $long = $longs[$i];

            $query = mysqli_query($link, "INSERT INTO lats_longs
                        (route_id, latitude, longitude)
                        VALUES ('$route_id', '$lat', '$long')");

        }
    }
}