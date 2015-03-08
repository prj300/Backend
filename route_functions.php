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
        // Insert procedure on Routes Table
        $route = mysqli_query($link, "INSERT INTO routes(user_id, grade, terrain, distance, date_created)
              VALUES ('$user_id', '$grade', '$terrain', '$distance', NOW())");

        return mysqli_insert_id($link);
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
        mysqli_query($link, "INSERT INTO results(user_id, route_id,
                            distance, max_speed, average_speed, total_time, date_created)
                            VALUES('$user_id', '$route_id', '$distance',
                            '$max_speed', '$avg_speed', '$time', NOW())");

        $result_id = mysqli_insert_id($link);

        if(!$result_id) {
            return null;
        }
        return $result_id;
    }

    /**
     * Save all lat longs into lat longs table
     */
    public function saveLatLngs($link, $route_id, $latitudes, $longitudes)
    {
        // convert lats and longs back to associative array
        $lats = json_decode($latitudes);
        $longs = json_decode($longitudes);

        // Iterating through $lats and $longs arrays, inserting each value into the table
        // Along with the ID of the relative Route from the route table
        for($i=0;$i < sizeof($lats) && $i < sizeof($longs);$i++) {
            // SQL query string
            $query = "INSERT INTO lats_longs (route_id, latitude, longitude)
                      VALUES ('$route_id', '$lats[$i]', $longs[$i])";

            // execute query
            mysqli_query($link, $query);
        }
        return true;
    }

    public function insertDiscoveryPoint($link, $location_id, $name, $county)
    {
        $insert = mysqli_query($link, "INSERT INTO discovery_points (name, location_id, county)
                    VALUES ('$name', '$location_id', '$county')");

        if(mysqli_affected_rows($link)) {
            return true;
        } else {
            return false;
        }

    }

    public function getDiscoveryPoints($link, $county)
    {
        $response = array();
        $query = mysqli_query($link, "SELECT * FROM discovery_points WHERE county='$county'");

        if($query->num_rows > 0) {
            while($row = $query->fetch_assoc()) {
                $location = $this->getLatLong($link, $row["location_id"]);
                $result = array("id"=>$row["id"], "name"=>$row["name"],
                    "location_id"=>$row["location_id"], "county"=>$row["county"],
                    "lat"=>$location["lat"], "long"=>$location["long"]);

                array_push($response, $result);
            }
        }
        return $response;
    }

    private function getLatLong($link, $id)
    {
        // find row where id equals to passed id
        $query = mysqli_query($link, "SELECT latitude, longitude FROM wild_atlantic_way WHERE id='$id'");
        $row = mysqli_fetch_array($query);

        // return lat long
        return array("lat"=>$row["latitude"], "long"=>$row["longitude"]);
    }
}