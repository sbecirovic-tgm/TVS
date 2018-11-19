<?php
/**
 * Created by PhpStorm.
 * User: Mir
 * Date: 19.11.2018
 * Time: 17:53
 */
$db = mysqli_connect('localhost', 'root', '2017lewiS661451', 'tvs_datenbank');

function insertAward( $name, $tokenLimit )
{
    global $db;
    $sqlC = "insert into award ( name, tokenLimit ) values ( '$name' , '$tokenLimit')";
    $result[0] = mysqli_query($db, $sqlC);
    $result[1] = makeLeistungProAward($name);
    return $result;
}

function makeLeistungProAward($aName)
{
    global $db;
    $sqlUser = "select kuerzel from schueler";
    $users = mysqli_query($db, $sqlUser);
    $out = true;
    for ( ;$userArray = mysqli_fetch_assoc($users); )
    {
        $kuerzel = $userArray['kuerzel'];
        $sqlC = "insert into leistung ( aName, sKuerzel, tokenAnzahl ) values ( '$aName' , '$kuerzel', 0)";
        $result = mysqli_query($db, $sqlC);
        if ( !$result )
        {
            $out = false;
        }
    }
    return $out;
}

function updateAward( $name, $nameNeu, $tokenLimit )
{
    global $db;
    $sqlC = "update award set name = '$nameNeu', tokenLimit = '$tokenLimit' where name = $name";
    return mysqli_query($db, $sqlC);
}

function deleteAward ($name)
{
    global  $db;
    $sqlC = "delete from award where name = '$name'";
    return mysqli_query($db, $sqlC);
}

?>