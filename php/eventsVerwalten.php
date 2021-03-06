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

function getAttrFromEvent ( $attr, $name, $date, $aName )
{
    global $db;
    $sqlC = "select $attr from event where name = '$name' and datum = '$date' and aName = '$aName'";
    $temp = mysqli_query($db, $sqlC);
    $out = mysqli_fetch_assoc($temp)[$attr];
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

function listAllEventsBerechtigtLimit ($limit, $lKuerzel )
{
    global $db;
    $sqlC = "select * from event where aName in (select aName from erlaubnis where lKuerzel = '$lKuerzel') order by datum desc limit $limit";
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

function getEvent( $name, $datum, $aName )
{
    global $db;
    $sqlC = "select * from event where name = '$name' and datum = '$datum' and aName = '$aName'";
    $events = mysqli_query($db, $sqlC);
    $out = null;
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
    $out = array();
    for(; $event_array = mysqli_fetch_assoc($unter);)
    {
        $out[$i]['name'] = $event_array['name'];
        $out[$i]['beschreibung'] = $event_array['beschreibung'];
        $out[$i]['tokenAnzahl'] = $event_array['tokenAnzahl'];
        $i++;
    }
    return $out;
}
function addEvent ( $name, $datum, $kuerzel, $aName, $beschreibung, $wID )
{
    global $db;
    if ( $wID != null )
    {
        if (checkIfUserIsSuperUser($kuerzel))
        {
            $sqlC = "insert into event(name, datum, superKuerzel, lKuerzel, aName, beschreibung, wID) values('$name', '$datum', '$kuerzel', NULL, '$aName','$beschreibung', '$wID')";
        }
        else
        {
            $sqlC = "insert into event(name, datum, superKuerzel, lKuerzel, aName, beschreibung, wID) values('$name', '$datum', NULL, '$kuerzel', '$aName', '$beschreibung', '$wID')";
        }
    }
    else
    {
        if (checkIfUserIsSuperUser($kuerzel))
        {
            $sqlC = "insert into event(name, datum, superKuerzel, lKuerzel, aName, beschreibung, wID) values('$name', '$datum', '$kuerzel', NULL, '$aName','$beschreibung', NULL )";
        }
        else
        {
            $sqlC = "insert into event(name, datum, superKuerzel, lKuerzel, aName, beschreibung, wID) values('$name', '$datum', NULL, '$kuerzel', '$aName', '$beschreibung', NULL )";
        }
    }
    $out = mysqli_query($db, $sqlC);
    return $out;
}

function changeEvent ( $name, $datum, $aName, $kuerzel, $nameNeu, $datumNeu, $aNameNeu, $beschreibungNeu, $wIDNeu)
{
    global $db;

    if ( checkIfUserIsSuperUser($kuerzel))
    {
        if ( $wIDNeu != null )
        {
            $sqlC = "update event set name = '$nameNeu', datum = '$datumNeu',superKuerzel = '$kuerzel', lKuerzel = NULL, aName = '$aNameNeu', beschreibung = '$beschreibungNeu', wID = '$wIDNeu' where name = '$name' and datum = '$datum' and aName = '$aName'";
        }
        else
        {
            $sqlC = "update event set name = '$nameNeu', datum = '$datumNeu',superKuerzel = '$kuerzel', lKuerzel = NULL, aName = '$aNameNeu', beschreibung = '$beschreibungNeu', wID = NULL where name = '$name' and datum = '$datum' and aName = '$aName'";
        }
    }
    else
    {
        if ( $wIDNeu != null )
        {
            $sqlC = "update event set name = '$nameNeu', datum = '$datumNeu',superKuerzel = NULL, lKuerzel = '$kuerzel', aName = '$aNameNeu', beschreibung = '$beschreibungNeu', wID = '$wIDNeu' where name = '$name' and datum = '$datum' and aName = '$aName'";
        }
        else
        {
            $sqlC = "update event set name = '$nameNeu', datum = '$datumNeu',superKuerzel = NULL, lKuerzel = '$kuerzel', aName = '$aNameNeu', beschreibung = '$beschreibungNeu', wID = NULL where name = '$name' and datum = '$datum' and aName = '$aName'";
        }
    }

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

function isSchuelerInWildcard( $name, $datum, $aName, $userName )
{
    global $db;
    $sqlC = "select wID from event where name = '$name' and datum = '$datum' and aName = '$aName'";
    $temp = mysqli_query($db, $sqlC);
    $result = mysqli_fetch_assoc($temp);
    if ( $result['wID'] == NULL )
    {
        return true;
    }
    else
    {
        $res = $result['wID'];
        $sqlC2 = "select skuerzel from zuordnung where wID = '$res' order by skuerzel";
        $temp = mysqli_query($db, $sqlC2);
        for ( ; $zuordnung = mysqli_fetch_assoc($temp) ; )
        {
            if ( $zuordnung['skuerzel'] == $userName )
            {
                return true;
            }
        }
        return false;
    }
}


function addWildcard( $schuelerArray )
{
    global $db;
    $sqlC = "insert into wildcard() values()";
    $result = mysqli_query($db, $sqlC);
    $out = true;

    $sqlC = "select id from wildcard order by id desc limit 1";
    $temp = mysqli_query($db, $sqlC);
    $id = mysqli_fetch_assoc($temp)['id'];
    /*
        CREATE TABLE wildcard (
		id INTEGER NOT NULL AUTO_INCREMENT,
		PRIMARY KEY ( id )
        ) ENGINE = INNODB;
     */

    foreach ($schuelerArray as $kuerzel )
    {
        $sqlC2 = "Insert into zuordnung (wID, skuerzel) values('$id', '$kuerzel')";
        /*
        CREATE TABLE zuordnung (
        wID INTEGER,
		skuerzel VARCHAR(255),

		PRIMARY KEY (wID, skuerzel),

		FOREIGN KEY ( wID )
		REFERENCES wildcard(id)
		ON UPDATE CASCADE
		On DELETE CASCADE,
		FOREIGN KEY ( skuerzel )
		REFERENCES schueler(kuerzel)
		ON UPDATE CASCADE
		On DELETE CASCADE
        ) ENGINE = INNODB;
        */
        $result2 = mysqli_query($db, $sqlC2);
        if ( $result2 == false )
        {
            $out = false;
        }
    }

    if ( $result && $out )
    {
        return array($id, true);
    }
    else
    {
        return array($id, false);
    }

}

function deleteWildcard($wID)
{
    global $db;
    $sqlC = "delete from wild where id = '$wID'";
    $result = mysqli_query($db, $sqlC);
    return $result;
}

function getAllZuordnungToID ( $wID )
{
    global $db;
    $sqlC = "select * from zuordnung where wID = '$wID'";
    $res = mysqli_query($db, $sqlC);
    $out = array();
    for ($i = 0; $arr = mysqli_fetch_assoc($res); $i++ )
    {
        $out[$i]= $arr['skuerzel'];
    }
    return $out;
}


function addArrayToWildcard( $wID, $kuerzelArr )
{
    $out = true;
    foreach ($kuerzelArr as $kuerzel )
    {
        $result = addToWildCard($wID, $kuerzel);
        if ( $result != true )
        {
            $out = false;
        }
    }
    return $out;
}

function addToWildCard( $wID, $kuerzel)
{
    global $db;
    $sqlC = "Insert into zuordnung (wID, skuerzel) values('$wID', '$kuerzel')";
    $result = mysqli_query($db, $sqlC);
    return $result;
}

function removeFromWildCard ( $wID, $kuerzel )
{
    global $db;
    $sqlC = "delete from zuordnung where wID = '$wID' and kuerzel = '$kuerzel'";
    $result = mysqli_query($db, $sqlC);
    return $result;
}

function addUnterkategorie( $name, $eName, $aName, $eDatum, $tokenAnzahl, $beschreibung )
{
    global $db;
    $sqlC = "Insert into unterkategorie ( name, eName, aName, eDatum, tokenAnzahl, beschreibung ) values ('$name', '$eName', '$aName', '$eDatum', '$tokenAnzahl', '$beschreibung' )";
    $result = mysqli_query($db, $sqlC);
    return $result;
    /*CREATE TABLE unterkategorie (
		name VARCHAR(255),

		eName VARCHAR(255),
		aName VARCHAR(255),
		eDatum DATE,

		tokenAnzahl INTEGER,
		beschreibung TEXT,*/
}

function deleteAllUnterkategorieToEvent ( $eName, $aName, $eDatum )
{
    global $db;
    $sqlC = "delete from unterkategorie where eName = '$eName' and eDatum = '$eDatum' and aName = '$aName'";
    $result = mysqli_query($db, $sqlC);
    return $result;
}

function deleteUnterkategorie( $name, $eName, $aName, $eDatum )
{
    global $db;
    $sqlC = "delete from unterkategorie where name = '$name' and eName = '$eName' and eDatum = '$eDatum' and aName = '$aName'";
    $result = mysqli_query($db, $sqlC);
    return $result;
}

function getAllUnterkatToEvent( $eName, $eDate, $aName )
{
    global $db;
    $sqlC = "select * from unterkategorie where eName = '$eName' and eDatum = '$eDate' and aName = '$aName'";
    $temp = mysqli_query($db, $sqlC);
    $out = null;
    for ( $i = 0; $array = mysqli_fetch_assoc($temp); $i++ )
    {
        /*
         ['name']
         ['tokenAnzahl']
         ['beschreibung']
         */
        $out[$i]['name'] = $array['name'];
        $out[$i]['tokenAnzahl'] = $array['tokenAnzahl'];
        $out[$i]['beschreibung'] = $array['beschreibung'];
    }

    return $out;
}

?>