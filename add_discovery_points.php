<?php
/**
 * Created by PhpStorm.
 * User: Enda
 * Date: 07/03/2015
 * Time: 14:31
 */

include "db/config.php";
require_once 'route_functions.php';

if(isset($_GET['lat']) || isset($_GET['long'])) {
    $lat = $_GET['lat'];
    $long = $_GET['long'];
    $name = mysqli_real_escape_string($link, $_GET['name']);
    $county = mysqli_real_escape_string($link, $_GET['county']);

    $functions = new route_functions();

    $location_id = $functions->findNearestPoint($link, $lat, $long);

    $success = $functions->insertDiscoveryPoint($link, $location_id, $name, $county);

    echo json_encode($success);


}
?>

<html>
    <body>
        <form action="<?php $_PHP_SELF ?>" method="GET">
            Name: <input type="text" name="name"/></p>
            County: <input type="text" name="county"><p/>
            Lat: <input type="text" name="lat"/></p>
            Long: <input type="text" name="long"/><p/>
            <input type="submit"/>
        </form>
    </body>
</html>