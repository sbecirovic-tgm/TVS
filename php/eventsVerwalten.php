<?php
/**
 * Created by PhpStorm.
 * User: Mir
 * Date: 19.11.2018
 * Time: 11:08
 */
$db = mysqli_connect('localhost', 'tokenverwaltung', '1234', 'tvs_datenbank');
include_once("userCheck.php");
function listAllEvents()
{
    global $db;
    $sqlC = "select * from event order by datum desc";
    $events = mysqli_query($db, $sqlC);
    $out = array();
    for ($i = 0; $event_array = mysqli_fetch_assoc($events); $i++) {
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

function listAllEventsLimit($limit)
{
    $events = listAllEvents();
    $out = array();

    $i = 0;
    foreach ($events as $event)
    {
        $out[$i] = array();
        $out[$i]['name'] = $event['name'];
        $out[$i]['datum'] = $event['datum'];
        $out[$i]['superKuerzel'] = $event['superKuerzel'];
        $out[$i]['lKuerzel'] = $event['lKuerzel'];
        $out[$i]['aName'] = $event['aName'];
        $out[$i]['beschreibung'] = $event['beschreibung'];

        if ( $i == $limit-1 )
        {
            break;
        }
        $i++;
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

function getUnterKatProEvent( $name, $datum, $aName )
{
    global $db;
    $sqlC = "select name, beschreibung, tokenAnzahl from unterkategorie where eName = '$name' and eDatum = '$datum' and aName = '$aName'";
    $unter = mysqli_query($db, $sqlC);
    $i = 0;
    for(; $event_array = mysqli_fetch_assoc($unter);)
    {
        $out[$i]['name'] = $event_array['name'];
        $out[$i]['beschreibung'] = $event_array['beschreibung'];
        $out[$i]['tokenAnzahl'] = $event_array['tokenAnzahl'];
        $i++;
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

function countNewEvents()
{
    global $db;

    $sqlC = "select count(name) as result from event where datum >= CURDATE()";
    $temp = mysqli_query($db,$sqlC);
    $out = mysqli_fetch_assoc($temp);
    return $out['result'];
}

?>