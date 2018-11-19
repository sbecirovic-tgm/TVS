<?php
/**
 * Created by PhpStorm.
 * User: Mir
 * Date: 19.11.2018
 * Time: 10:09
 */

//session_start();

$db = mysqli_connect('localhost', 'root', '2017lewiS661451', 'tvs_datenbank');

function requestTokenBasic ( $awardTyp, $tokenAnzahl, $betreff, $beschreibung, $userKuerzel )
{
    global $db;
    // inserten, alles andere auf NULL setzten --> bei der normalen abfrage nicht notwendig
    $sqlC = "insert into anfrage( datum, zeit, aName, superkuerzel, lehrerKuerzel, eName, eDatum, untName, skuerzel, tokenAnzahl, beschreibung, betreff, wirdBewilligt, kommentar ) values (CURDATE(), CURTIME(), '$awardTyp', NULL, NULL, NULL, NULL, NULL, '$userKuerzel', '$tokenAnzahl', '$beschreibung', '$betreff', NULL, NULL)";

    $result = mysqli_query($db, $sqlC);
    return $result;
}

function requestTokenExt ( $awardTyp, $eventName, $eventDate, $unterKatName, $userKuerzel, $tokenAnzahl, $beschreibung, $betreff )
{
    global $db;
    // inserten, alles andere auf NULL setzten --> bei der normalen abfrage nicht notwendig
    $sqlC = "insert into anfrage( datum, zeit, aName, superkuerzel, lehrerKuerzel, eName, eDatum, untName, skuerzel, tokenAnzahl, beschreibung, betreff, wirdBewilligt, kommentar ) values (CURDATE(), CURTIME(), '$awardTyp', NULL, NULL, '$eventName', '$eventDate', $unterKatName, '$userKuerzel', '$tokenAnzahl', '$beschreibung', '$betreff', NULL, NULL)";

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
        $sqlC = "update anfrage set datum = CURDATE(), zeit = CURTIME(), aName = '$awardTypNeu', eName =  '$eventNameNeu', eDate = '$eventDateNeu', untName = '$unterKatNameNeu', tokenAnzahl = '$tokenAnzahlBenutzerNeu', beschreibung = '$beschreibungNeu', betreff = '$betreffNeu' where id = '$id' and datum = '$datum' and zeit = '$zeit' and skuerzel = $userKeurzel";
        return mysqli_query($db, $sqlC);
    }
    else
    {
        return false;
    }
}

function deleteTokenRequest ( $id, $userKeurzel, $datum, $zeit )
{
    global $db;

    $sqlCeck = "select wirdBewilligt from anfrage where id = '$id' and datum = '$datum' and zeit = '$zeit' and skuerzel = '$userKeurzel'";
    $request = mysqli_query($db, $sqlCeck);
    $requestArray = mysqli_fetch_assoc($db, $request);
    if ( $requestArray['wirdBewilligt'] == NULL )
    {
        $sqlC = "delete from anfrage where id = '$id' and datum = '$datum' and zeit = '$zeit' and skuerzel = '$userKeurzel'";
        return mysqli_query($db, $sqlC);
    }
    else
    {
        return false;
    }
}

function bewilligeToken ( $id, $datum, $zeit , $schuelerKuerzel, $userName, $tokenNeu, $kommentar, $wirdBewilligt)
{
    global $db;
    include ("userCheck.php");
    if ( checkIfUserIsSuperUser($userName) )
    {
        $sqlC = "update anfrage set tokenAnzahlNeu = '$tokenNeu', wirdBewilligt = '$wirdBewilligt', kommentar = '$kommentar', superkuerzel = '$userName' where id = '$id' and datum = '$datum' and zeit = '$zeit' and skuerzel = '$schuelerKuerzel'";
    }
    else
    {
        $sqlC = "update anfrage set tokenAnzahlNeu = '$tokenNeu', wirdBewilligt = '$wirdBewilligt', kommentar = '$kommentar', lehrerKuerzel = '$userName' where id = '$id' and datum = '$datum' and zeit = '$zeit' and skuerzel = '$schuelerKuerzel'";
    }
    $results[0] = mysqli_query($db, $sqlC);

    $sqlC2 = "select aName from anfrage where id = '$id' and datum = '$datum' and zeit = '$zeit' and skuerzel = '$schuelerKuerzel'";
    $name = mysqli_query($db, $sqlC2);
    $nameArray = mysqli_fetch_assoc($name);

    $results[1] = addTokenToLeistung($schuelerKuerzel, $nameArray['aName'] , $tokenNeu );
    return $results;
}

function addTokenToLeistung ( $schuelerKuerzel, $aName, $token)
{
    global $db;
    $sqlC = "update leistung set tokenAnzahl = '$token' where aName = '$aName' and sKuerzel = '$schuelerKuerzel'";
    return mysqli_query($db, $sqlC);
}

function listAllReqests()
{
    global $db;
    $out = array();

    $sqlC = "select * from anfrage order by datum desc, zeit desc";
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


?>
