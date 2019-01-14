<?php
/**
 * Created by PhpStorm.
 * User: Mir
 * Date: 19.11.2018
 * Time: 10:09
 */

//session_start();

$db = mysqli_connect('localhost', 'tokenverwaltung', '1234', 'tvs_datenbank');

function requestTokenBasic ( $awardTyp, $tokenAnzahl, $betreff, $beschreibung, $userKuerzel )
{
    global $db;
    // inserten, alles andere auf NULL setzten --> bei der normalen abfrage nicht notwendig
    $sqlC = "insert into anfrage( datum, zeit, aName, superkuerzel, lehrerKuerzel, eName, eDatum, untName, skuerzel, tokenAnzahl, tokenAnzahlNeu, beschreibung, betreff, wirdBewilligt, kommentar ) values (CURDATE(), CURTIME(), '$awardTyp', NULL, NULL, NULL, NULL, NULL, '$userKuerzel', $tokenAnzahl, NULL, '$beschreibung', '$betreff', NULL, NULL)";

    $result = mysqli_query($db, $sqlC);
    return $result;
}

function requestTokenExt ( $awardTyp, $eventName, $eventDate, $unterKatName, $userKuerzel, $tokenAnzahl, $beschreibung, $betreff )
{
    global $db;
    // inserten, alles andere auf NULL setzten --> bei der normalen abfrage nicht notwendig
    $sqlC = "insert into anfrage( datum, zeit, aName, superkuerzel, lehrerKuerzel, eName, eDatum, untName, skuerzel, tokenAnzahl, tokenAnzahlNeu, beschreibung, betreff, wirdBewilligt, kommentar ) values (CURDATE(), CURTIME(), '$awardTyp', NULL, NULL, '$eventName', '$eventDate', '$unterKatName', '$userKuerzel', $tokenAnzahl, NULL, '$beschreibung', '$betreff', NULL, NULL)";

    $result = mysqli_query($db, $sqlC);
    return $result;
}

function requestTokenExtOhneKat ( $awardTyp, $eventName, $eventDate, $userKuerzel, $tokenAnzahl, $beschreibung, $betreff )
{
    global $db;
    // inserten, alles andere auf NULL setzten --> bei der normalen abfrage nicht notwendig
    $sqlC = "insert into anfrage( datum, zeit, aName, superkuerzel, lehrerKuerzel, eName, eDatum, untName, skuerzel, tokenAnzahl, tokenAnzahlNeu, beschreibung, betreff, wirdBewilligt, kommentar ) values (CURDATE(), CURTIME(), '$awardTyp', NULL, NULL, '$eventName', '$eventDate', NULL, '$userKuerzel', $tokenAnzahl, NULL, '$beschreibung', '$betreff', NULL, NULL)";

    $result = mysqli_query($db, $sqlC);
    return $result;
}

function changeTokenRequestToken ( $id, $userKeurzel, $datum, $zeit, $awardTypNeu, $eventNameNeu, $eventDateNeu, $unterKatNameNeu, $tokenAnzahlBenutzerNeu, $beschreibungNeu, $betreffNeu )
{
    global $db;

    $sqlCeck = "select wirdBewilligt from anfrage where id = '$id' and datum = '$datum' and zeit = '$zeit' and skuerzel = '$userKeurzel'";
    $request = mysqli_query($db, $sqlCeck);
    $requestArray = mysqli_fetch_assoc($request);
    if ( $requestArray['wirdBewilligt'] == NULL )
    {
        $sqlC = "update anfrage set datum = CURDATE(), zeit = CURTIME(), aName = '$awardTypNeu', eName =  '$eventNameNeu', eDate = '$eventDateNeu', untName = '$unterKatNameNeu', tokenAnzahl = '$tokenAnzahlBenutzerNeu', beschreibung = '$beschreibungNeu', betreff = '$betreffNeu' where id = '$id' and datum = '$datum' and zeit = '$zeit' and skuerzel = '$userKeurzel'";
        return mysqli_query($db, $sqlC);
    }
    else
    {
        return false;
    }
}

function deleteTokenRequest ( $id, $userKeurzel )
{
    global $db;

    $sqlCeck = "select wirdBewilligt from anfrage where id = '$id'and skuerzel = '$userKeurzel'";
    $request = mysqli_query($db, $sqlCeck);
    $requestArray = mysqli_fetch_assoc($request);
    if ( $requestArray['wirdBewilligt'] == '' )
    {
        $sqlC = "delete from anfrage where id = '$id' and skuerzel = '$userKeurzel'";
        return mysqli_query($db, $sqlC);
    }
    else
    {
        return false;
    }
}

function bewilligeToken ( $id, $schuelerKuerzel, $userName, $tokenNeu, $kommentar, $wirdBewilligt)
{
    global $db;
    include_once ("userCheck.php");
    if ( checkIfUserIsSuperUser($userName) )
    {
        $sqlC = "update anfrage set tokenAnzahlNeu = '$tokenNeu', wirdBewilligt = '$wirdBewilligt', kommentar = '$kommentar', superkuerzel = '$userName' where id = '$id' and skuerzel = '$schuelerKuerzel'";
    }
    else
    {
        $sqlC = "update anfrage set tokenAnzahlNeu = '$tokenNeu', wirdBewilligt = '$wirdBewilligt', kommentar = '$kommentar', lehrerKuerzel = '$userName' where id = '$id' and skuerzel = '$schuelerKuerzel'";
    }
    $results[0] = mysqli_query($db, $sqlC);

    $sqlC2 = "select aName, datum from anfrage where id = '$id' and skuerzel = '$schuelerKuerzel'";
    $name = mysqli_query($db, $sqlC2);
    $nameArray = mysqli_fetch_assoc($name);

    $results[1] = addTokenToLeistung($schuelerKuerzel, $nameArray['aName'] , $tokenNeu, getSaisonNumbFromDate($nameArray['datum']) );
    return $results;
}

function addTokenToLeistung ( $schuelerKuerzel, $aName, $token, $saisonNumb)
{
    global $db;
    // schauen ob der Schüler schon genug token für einen Award hat
    $sqlC = "select tokenAnzahl from leistung where aName = '$aName' and sKuerzel = '$schuelerKuerzel' and saisonNummer = '$saisonNumb'";
    $tokenRes = mysqli_query($db, $sqlC);
    $tokenArray = mysqli_fetch_assoc($tokenRes);
    $anzahlToken = $tokenArray['tokenAnzahl'];
    $anzahlToken += $token;

    $sqlC2 = "select tokenLimit from award where name = '$aName'";
    $tokenA = mysqli_query($db, $sqlC);
    $tokenAArray = mysqli_fetch_assoc($tokenA);
    $tokenLimit = $tokenAArray['tokenLimit'];
    if ( $anzahlToken >= $tokenLimit )
    {
        $token = $anzahlToken - $tokenLimit;
        //auszeichnung erstellen wenn
        $sqlA = "insert into auszeichnung ( datum, zeit, skuerzel, awardName, saisonNummer ) values ( CURDATE(), CURTIME(), '$schuelerKuerzel', '$aName', $saisonNumb )";
        mysqli_query($db, $sqlA);
    }


    $sqlC2 = "update leistung set tokenAnzahl = '$token' where aName = '$aName' and sKuerzel = '$schuelerKuerzel' and saisonNummer = '$saisonNumb'";
    return mysqli_query($db, $sqlC2);
}


/**
datum DATE,
zeit TIME,
skuerzel VARCHAR(255),

-- FOREIGN KEYs
aName VARCHAR(255), -- award name
superkuerzel VARCHAR(255),
lehrerKuerzel VARCHAR(255),

eName VARCHAR(255),
eDatum DATE,
untName VARCHAR(255),

tokenAnzahl INTEGER NOT NULL,
tokenAnzahlNeu INTEGER, -- Wird nur vom Admin gesetzt, also ist am Anfang null
beschreibung TEXT,
betreff VARCHAR(255),
wirdBewilligt BOOLEAN, -- NULL wenn noch nichts eingetragen wurde
kommentar TEXT, -- Admin schreibt ein Kommentar, anfangs NULL
 */
function listAllRequests()
{
    global $db;
    $out = array();

    $sqlC = "select * from anfrage order by datum desc, zeit desc";
    $anfragen = mysqli_query($db, $sqlC);

    for( $i = 0; $anfragen_array = mysqli_fetch_assoc($anfragen); $i++)
    {
        $out[$i] = array();
        $out[$i]['id'] = $anfragen_array['id'];
        $out[$i]['datum'] = $anfragen_array['datum'];
        $out[$i]['zeit'] = $anfragen_array['zeit'];
        $out[$i]['aName'] = $anfragen_array['aName'];
        $out[$i]['eName'] = $anfragen_array['eName'];
        $out[$i]['eDatum'] = $anfragen_array['eDatum'];
        $out[$i]['untName'] = $anfragen_array['untName'];
        $out[$i]['tokenAnzahl'] = $anfragen_array['tokenAnzahl'];
        $out[$i]['tokenAnzahlNeu'] = $anfragen_array['tokenAnzahlNeu'];
        $out[$i]['beschreibung'] = $anfragen_array['beschreibung'];
        $out[$i]['betreff'] = $anfragen_array['betreff'];
        $out[$i]['wirdBewilligt'] = $anfragen_array['wirdBewilligt'];
        $out[$i]['kommentar'] = $anfragen_array['kommentar'];
        $out[$i]['skuerzel'] = $anfragen_array['skuerzel'];
        $out[$i]['superkuerzel'] = $anfragen_array['superkuerzel'];
        $out[$i]['lehrerKuerzel'] = $anfragen_array['lehrerKuerzel'];
    }

    return $out;
}

function listAllRequestsLimit($limit)
{
    global $db;
    $out = array();

    $sqlC = "select * from anfrage order by datum desc, zeit desc limit $limit";
    $anfragen = mysqli_query($db, $sqlC);

    for( $i = 0; $anfragen_array = mysqli_fetch_assoc($anfragen); $i++)
    {
        $out[$i] = array();
        $out[$i]['id'] = $anfragen_array['id'];
        $out[$i]['datum'] = $anfragen_array['datum'];
        $out[$i]['zeit'] = $anfragen_array['zeit'];
        $out[$i]['aName'] = $anfragen_array['aName'];
        $out[$i]['eName'] = $anfragen_array['eName'];
        $out[$i]['eDatum'] = $anfragen_array['eDatum'];
        $out[$i]['untName'] = $anfragen_array['untName'];
        $out[$i]['tokenAnzahl'] = $anfragen_array['tokenAnzahl'];
        $out[$i]['tokenAnzahlNeu'] = $anfragen_array['tokenAnzahlNeu'];
        $out[$i]['beschreibung'] = $anfragen_array['beschreibung'];
        $out[$i]['betreff'] = $anfragen_array['betreff'];
        $out[$i]['wirdBewilligt'] = $anfragen_array['wirdBewilligt'];
        $out[$i]['kommentar'] = $anfragen_array['kommentar'];
        $out[$i]['superkuerzel'] = $anfragen_array['superkuerzel'];
        $out[$i]['lehrerKuerzel'] = $anfragen_array['lehrerKuerzel'];
    }

    return $out;
}

function listAllRequestsProSchueler( $kuerzel )
{
    global $db;
    $out = array();

    $sqlC = "select * from anfrage where skuerzel = '$kuerzel' order by datum desc, zeit desc";
    $anfragen = mysqli_query($db, $sqlC);

    for( $i = 0; $anfragen_array = mysqli_fetch_assoc($anfragen); $i++)
    {
        $out[$i] = array();
        $out[$i]['id'] = $anfragen_array['id'];
        $out[$i]['datum'] = $anfragen_array['datum'];
        $out[$i]['zeit'] = $anfragen_array['zeit'];
        $out[$i]['aName'] = $anfragen_array['aName'];
        $out[$i]['eName'] = $anfragen_array['eName'];
        $out[$i]['eDatum'] = $anfragen_array['eDatum'];
        $out[$i]['untName'] = $anfragen_array['untName'];
        $out[$i]['tokenAnzahl'] = $anfragen_array['tokenAnzahl'];
        $out[$i]['tokenAnzahlNeu'] = $anfragen_array['tokenAnzahlNeu'];
        $out[$i]['beschreibung'] = $anfragen_array['beschreibung'];
        $out[$i]['betreff'] = $anfragen_array['betreff'];
        $out[$i]['wirdBewilligt'] = $anfragen_array['wirdBewilligt'];
        $out[$i]['kommentar'] = $anfragen_array['kommentar'];
        $out[$i]['superkuerzel'] = $anfragen_array['superkuerzel'];
        $out[$i]['lehrerKuerzel'] = $anfragen_array['lehrerKuerzel'];
    }

    return $out;
}

function listAllRequestsProAward( $aName )
{
    global $db;
    $out = array();

    $sqlC = "select * from anfrage where aName = '$aName' order by datum desc, zeit desc";
    $anfragen = mysqli_query($db, $sqlC);

    for( $i = 0; $anfragen_array = mysqli_fetch_assoc($anfragen); $i++)
    {
        $out[$i] = array();
        $out[$i]['id'] = $anfragen_array['id'];
        $out[$i]['datum'] = $anfragen_array['datum'];
        $out[$i]['zeit'] = $anfragen_array['zeit'];
        $out[$i]['aName'] = $anfragen_array['aName'];
        $out[$i]['eName'] = $anfragen_array['eName'];
        $out[$i]['eDatum'] = $anfragen_array['eDatum'];
        $out[$i]['untName'] = $anfragen_array['untName'];
        $out[$i]['tokenAnzahl'] = $anfragen_array['tokenAnzahl'];
        $out[$i]['beschreibung'] = $anfragen_array['beschreibung'];
        $out[$i]['betreff'] = $anfragen_array['betreff'];
        $out[$i]['wirdBewilligt'] = $anfragen_array['wirdBewilligt'];
        $out[$i]['kommentar'] = $anfragen_array['kommentar'];
    }

    return $out;
}

function getAnfrageFromId( $id )
{
    global $db;

    $sqlC = "select * from anfrage where id = '$id'";
    $anfragen = mysqli_query($db, $sqlC);
    $anfragen_array = mysqli_fetch_assoc($anfragen);

    $out = array();
    $out['id'] = $anfragen_array['id'];
    $out['datum'] = $anfragen_array['datum'];
    $out['zeit'] = $anfragen_array['zeit'];
    $out['aName'] = $anfragen_array['aName'];
    $out['eName'] = $anfragen_array['eName'];
    $out['eDatum'] = $anfragen_array['eDatum'];
    $out['untName'] = $anfragen_array['untName'];
    $out['tokenAnzahl'] = $anfragen_array['tokenAnzahl'];
    $out['beschreibung'] = $anfragen_array['beschreibung'];
    $out['betreff'] = $anfragen_array['betreff'];
    $out['wirdBewilligt'] = $anfragen_array['wirdBewilligt'];
    $out['kommentar'] = $anfragen_array['kommentar'];

    return $out;
}

function listAllOpenReqests()
{
    return listAllRequestsToStatus("NULL");
}

function listAllRequestsToStatus ( $status )
{
    global $db;
    $out = array();

    $sqlC = "select * from anfrage where wirdBewilligt = '$status' order by datum desc, zeit desc";
    $anfragen = mysqli_query($db, $sqlC);

    for( $i = 0; $anfragen_array = mysqli_fetch_assoc($anfragen); $i++)
    {
        $out[$i] = array();
        $out[$i]['datum'] = $anfragen_array['datum'];
        $out[$i]['zeit'] = $anfragen_array['zeit'];
        $out[$i]['aName'] = $anfragen_array['aName'];
        $out[$i]['eName'] = $anfragen_array['eName'];
        $out[$i]['eDatum'] = $anfragen_array['eDatum'];
        $out[$i]['untName'] = $anfragen_array['untName'];
        $out[$i]['tokenAnzahl'] = $anfragen_array['tokenAnzahl'];
        $out[$i]['beschreibung'] = $anfragen_array['beschreibung'];
        $out[$i]['betreff'] = $anfragen_array['betreff'];
        $out[$i]['wirdBewilligt'] = $anfragen_array['wirdBewilligt'];
        $out[$i]['kommentar'] = $anfragen_array['kommentar'];
    }

    return $out;
}

function listAllDeniedRequests()
{
    return listAllRequestsToStatus("False");
}

function listAllAcceptedRequests()
{
    return listAllRequestsToStatus("True");
}


?>
