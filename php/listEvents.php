<?php
/**
 * Created by PhpStorm.
 * User: Mir
 * Date: 19.11.2018
 * Time: 11:08
 */
$db = mysqli_connect('localhost', 'root', '2017lewiS661451', 'tvs_datenbank');

function listAllEvents()
{
    global $db;
    $sqlC = "select * from event order by data desc";
}

?>