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

function bewilligeToken ( $id, $userName, $tokenNeu, $kommentar, $wirdBewilligt)
{
    global $db;
    include_once ("saisonVerwalten.php");
    include_once ("userCheck.php");
    if ( $wirdBewilligt )
    {
        $wirdBewilligtString = 'true';
    }
    else
    {
        $wirdBewilligtString = 'false';
    }
    // wenn schon bewilligt war und geändert wird..
    $sqlC = "select wirdBewilligt, skuerzel, tokenAnzahl, datum, zeit, aName from anfrage where id = $id";
    $res = mysqli_query($db, $sqlC);
    $resArray = mysqli_fetch_assoc($res);
    $wirdBewilligtOld = $resArray['wirdBewilligt'];
    $tokenAnzahlOld = $resArray['tokenAnzahl'];
    $sKuerzel = $resArray['skuerzel'];
    $datum = $resArray['datum'];
    $zeit = $resArray['zeit'];
    $aName = $resArray['aName'];
    if ( $wirdBewilligtOld && !$wirdBewilligt )
    {
        // leistung muss abgezogen werden
        $saisonNummer = getSaisonNumbFromDate($datum);
        $sqlC = "select tokenAnzahl from leistung where saisonNummer = $saisonNummer and sKuerzel = '$sKuerzel' and aName = '$aName'";
        $res = mysqli_query($db, $sqlC);
        $resArray = mysqli_fetch_assoc($res);
        if ( $tokenAnzahlOld < $resArray['tokenAnzahl'])
        {
            $tokenNeu = $resArray['tokenAnzahl'] - $tokenAnzahlOld;
        }
        else
        {
            // award wegnehmen
            $sqlC = "select tokenLimit from award where name = '$aName";
            $res = mysqli_query($db, $sqlC);
            $tokenAward = mysqli_fetch_assoc($res)['tokenLimit'];

            $tokenNeu = $tokenAward - $tokenAnzahlOld;

            // auszeichnung löschen
            $sqlC = "delete from auszeichnung where skuerzel = '$sKuerzel' and saisonNummer = $saisonNummer order by datum desc limit 1";
            mysqli_query($db, $sqlC);
        }

    }
    if ( $tokenNeu == null )
    {
        if ( checkIfUserIsSuperUser($userName) )
        {
            $sqlC = "update anfrage set wirdBewilligt = $wirdBewilligtString, kommentar = '$kommentar', superkuerzel = '$userName' where id = $id";
        }
        else
        {
            $sqlC = "update anfrage set wirdBewilligt = $wirdBewilligtString, kommentar = '$kommentar', lehrerKuerzel = '$userName' where id = $id";
        }

        $sqlC2 = "select tokenAnzahl from anfrage where id = $id";
        $temp = mysqli_query($db, $sqlC2);
        $tokenNeu = mysqli_fetch_assoc($temp)['tokenAnzahl'];
    }
    else
    {
        if ( checkIfUserIsSuperUser($userName) )
        {
            $sqlC = "update anfrage set tokenAnzahlNeu = $tokenNeu, wirdBewilligt = $wirdBewilligtString, kommentar = '$kommentar', superkuerzel = '$userName' where id = $id";
        }
        else
        {
            $sqlC = "update anfrage set tokenAnzahlNeu = $tokenNeu, wirdBewilligt = $wirdBewilligtString, kommentar = '$kommentar', lehrerKuerzel = '$userName' where id = $id";
        }
    }

    $results[0] = mysqli_query($db, $sqlC);

    if ( $wirdBewilligt )
    {
        $sqlC2 = "select aName, datum, skuerzel from anfrage where id = $id";
        $name = mysqli_query($db, $sqlC2);
        $nameArray = mysqli_fetch_assoc($name);
        $results[1] = addTokenToLeistung($nameArray['skuerzel'], $nameArray['aName'] , $tokenNeu, getSaisonNumbFromDate($nameArray['datum']) );
    }
    return $results;
}

function addTokenToLeistung ( $schuelerKuerzel, $aName, $token, $saisonNumb)
{
    global $db;
    include_once ("saisonVerwalten.php");
    include_once ("userCheck.php");
    // schauen ob der Schüler schon genug token für einen Award hat
    $sqlC = "select tokenAnzahl from leistung where aName = '$aName' and sKuerzel = '$schuelerKuerzel' and saisonNummer = $saisonNumb";
    $tokenRes = mysqli_query($db, $sqlC);
    $tokenArray = mysqli_fetch_assoc($tokenRes);
    $anzahlToken = $tokenArray['tokenAnzahl'];
    $anzahlToken += $token;

    $sqlC2 = "select tokenLimit from award where name = '$aName'";
    $tokenA = mysqli_query($db, $sqlC2);
    $tokenAArray = mysqli_fetch_assoc($tokenA);
    $tokenLimit = $tokenAArray['tokenLimit'];
    if ( $anzahlToken >= $tokenLimit )
    {
        $token = $anzahlToken - $tokenLimit;
        //auszeichnung erstellen wenn
        $sqlA = "insert into auszeichnung ( datum, zeit, skuerzel, awardName, saisonNummer ) values ( CURDATE(), CURTIME(), '$schuelerKuerzel', '$aName', $saisonNumb )";
        mysqli_query($db, $sqlA);

        $sqlA = "select awardName from auszeichnung where saisonNummer = $saisonNumb and skuerzel = '$schuelerKuerzel'";
        $awards = mysqli_query($db, $sqlA);
        $aNameArray = array();
        for ( $i = 0; $array = mysqli_fetch_assoc($awards); $i++ )
        {
            $aNameArray[$i] = $array['awardName'];
        }

        if ( in_array('Pulitzer', $aNameArray) && in_array('Editor', $aNameArray) && in_array('Favorite', $aNameArray) && in_array('Architect', $aNameArray) && !in_array('Spirit of HIT', $aNameArray) )
        {
            $sqlC = "insert into auszeichnung ( datum, zeit, skuerzel, awardName, saisonNummer ) values ( CURDATE(), CURTIME(), '$schuelerKuerzel', 'Spirit of HIT', $saisonNumb )";
            mysqli_query($db, $sqlC);
        }

    }
    else
    {
        $token = $anzahlToken;
    }

    $sqlC = "select count(tokenAnzahl) as anzahl from leistung where saisonNummer = $saisonNumb";
    $temp = mysqli_query($db, $sqlC);
    $anzahl = mysqli_fetch_assoc($temp)['anzahl'];

    if ( $anzahl == 0 )
    {
        $sqlC2 = "insert into leistung (aName, sKuerzel, tokenAnzahl, saisonNummer) values('$aName', '$schuelerKuerzel', $token, $saisonNumb )";
    }
    else
    {
        $sqlC2 = "update leistung set tokenAnzahl = $token where aName = '$aName' and sKuerzel = '$schuelerKuerzel' and saisonNummer = $saisonNumb";
    }

    $result = mysqli_query($db, $sqlC2);
    return $result;
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
        $out[$i]['skuerzel'] = $anfragen_array['skuerzel'];
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

function listAllRequestToLehrerErlaubnis ( $lKuerzel )
{
    global $db;
    // requests holen
    $sqlC = "select * from anfrage where aName in (select aName from erlaubnis where lKuerzel = '$lKuerzel') order by datum desc, zeit desc";
    $temp = mysqli_query($db, $sqlC);
    $out = array();
    for ($i = 0; $anfrage = mysqli_fetch_assoc($temp); $i++ )
    {
        $out[$i]['id'] = $anfrage['id'];
        $out[$i]['datum'] = $anfrage['datum'];
        $out[$i]['zeit'] = $anfrage['zeit'];
        $out[$i]['aName'] = $anfrage['aName'];
        $out[$i]['eName'] = $anfrage['eName'];
        $out[$i]['eDatum'] = $anfrage['eDatum'];
        $out[$i]['untName'] = $anfrage['untName'];
        $out[$i]['tokenAnzahl'] = $anfrage['tokenAnzahl'];
        $out[$i]['tokenAnzahlNeu'] = $anfrage['tokenAnzahlNeu'];
        $out[$i]['beschreibung'] = $anfrage['beschreibung'];
        $out[$i]['betreff'] = $anfrage['betreff'];
        $out[$i]['wirdBewilligt'] = $anfrage['wirdBewilligt'];
        $out[$i]['kommentar'] = $anfrage['kommentar'];
        $out[$i]['skuerzel'] = $anfrage['skuerzel'];
        $out[$i]['superkuerzel'] = $anfrage['superkuerzel'];
        $out[$i]['lehrerKuerzel'] = $anfrage['lehrerKuerzel'];
    }
    return $out;
}

function listAllRequestToLehrerErlaubnisLimit ( $limit, $lKuerzel )
{
    global $db;
    $sqlC = "select * from anfrage where aName in (select aName from erlaubnis where lKuerzel = '$lKuerzel') order by datum desc, zeit desc limit $limit";
    $temp = mysqli_query($db, $sqlC);
    $out = array();
    for ($i = 0; $anfrage = mysqli_fetch_assoc($temp); $i++ )
    {
        $out[$i]['id'] = $anfrage['id'];
        $out[$i]['datum'] = $anfrage['datum'];
        $out[$i]['zeit'] = $anfrage['zeit'];
        $out[$i]['aName'] = $anfrage['aName'];
        $out[$i]['eName'] = $anfrage['eName'];
        $out[$i]['eDatum'] = $anfrage['eDatum'];
        $out[$i]['untName'] = $anfrage['untName'];
        $out[$i]['tokenAnzahl'] = $anfrage['tokenAnzahl'];
        $out[$i]['tokenAnzahlNeu'] = $anfrage['tokenAnzahlNeu'];
        $out[$i]['beschreibung'] = $anfrage['beschreibung'];
        $out[$i]['betreff'] = $anfrage['betreff'];
        $out[$i]['wirdBewilligt'] = $anfrage['wirdBewilligt'];
        $out[$i]['kommentar'] = $anfrage['kommentar'];
        $out[$i]['skuerzel'] = $anfrage['skuerzel'];
        $out[$i]['superkuerzel'] = $anfrage['superkuerzel'];
        $out[$i]['lehrerKuerzel'] = $anfrage['lehrerKuerzel'];
    }
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
