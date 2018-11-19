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

function changeTokenRequestToken ( $id, $userKeurzel, $datum, $zeit, $awardTyp, $eventName, $eventDate, $unterKatName, $userKuerzel, $tokenAnzahl, $beschreibung, $betreff )
{
    global $db;

    $sqlC = "";
}

function deleteTokenRequest ( $id, $userKeurzel, $datum, $zeit )
{
    global $db;

    $sqlC = "delete from anfrage where id = '$id' and datum = '$datum' and zeit = '$zeit' and skuerzel = '$userKeurzel'";
    return mysqli_query($db, $sqlC);
}


?>
