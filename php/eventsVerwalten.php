<?php
/**
 * Created by PhpStorm.
 * User: Mir
 * Date: 19.11.2018
 * Time: 11:08
 */
$db = mysqli_connect('localhost', 'root', '2017lewiS661451', 'tvs_datenbank');
include("userCheck.php");
function listAllEvents()
{
    global $db;
    $sqlC = "select * from event order by data desc";
    $events = mysqli_query($db, $sqlC);
    $out = array();
    for( $i = 0; $event_array = mysqli_fetch_assoc($events); $i++)
    {
        $out[$i] = array();
        $out[$i]['name'] = $event_array['name'];
        $out[$i]['datum'] = $event_array['datum'];
        $out[$i]['superKuerzel'] = $event_array['superKuerzel'];
        $out[$i]['lKuerzel'] = $event_array['lKuerzel'];
        $out[$i]['aName'] = $event_array['aName'];
        $out[$i]['beschreibung'] = $event_array['beschreibung'];
    }
    return $out;
}

function getEvent( $name, $datum, $aName )
{
    global $db;
    $sqlC = "select * from event where name = '$name' and datum = '$datum' and aName = '$aName'";
    $events = mysqli_query($db, $sqlC);
    for(; $event_array = mysqli_fetch_assoc($events);)
    {
        $out['name'] = $event_array['name'];
        $out['datum'] = $event_array['datum'];
        $out['superKuerzel'] = $event_array['superKuerzel'];
        $out['lKuerzel'] = $event_array['lKuerzel'];
        $out['aName'] = $event_array['aName'];
        $out['beschreibung'] = $event_array['beschreibung'];
    }
    return $out;
}

function addEvent ( $name, $datum, $kuerzel, $aName, $beschreibung)
{
    global $db;
    if (checkIfUserIsSuperUser($kuerzel))
    {
        $sqlC = "insert into event(name, datum, superKuerzel, lKuerzel, aName, beschreibung) values('$name', '$datum', '$kuerzel', NULL, '$aName','$beschreibung')";
    }
    else
    {
        $sqlC = "insert into event(name, datum, superKuerzel, lKuerzel, aName, beschreibung) values('$name', '$datum', NULL, '$kuerzel', '$aName', '$beschreibung')";
    }
    return mysqli_query($db, $sqlC);
}

function changeEvent ( $name, $datum, $aName, $kuerzel, $nameNeu, $datumNeu, $aNameNeu, $beschreibungNeu)
{
    global $db;

    if ( checkIfUserIsSuperUser($kuerzel))
    {
        $sKuerzel = $kuerzel;
        $lKuerzel = Null;
    }
    else
    {
        $sKuerzel = Null;
        $lKuerzel = $kuerzel;
    }
    $sqlC = "update event set name = '$aNameNeu', datum = '$datumNeu',superKuerzel = '$sKuerzel', lKuerzel = '$lKuerzel', aName = '$aNameNeu', bescheibung = '$beschreibungNeu' where name = '$name' and datum = '$datum' and aName = '$aName'";
    return mysqli_query($db,$sqlC);
}

function deleteEvent ( $name, $datum, $aName )
{
    global $db;

    $sqlC = "delete from event where name = '$name' and datum = '$datum' and aName = '$aName'";
    return mysqli_query($db,$sqlC);
}

?>