<?php
/**
 * Created by PhpStorm.
 * User: Mir
 * Date: 19.11.2018
 * Time: 10:47
 */
//session_start();

$db = mysqli_connect('localhost', 'root', '2017lewiS661451', 'tvs_datenbank');
$userName = $_SESSION['userName'];

function bewilligeToken ( $id, $datum, $zeit , $schuelerKuerzel, $userName, $tokenNeu, $kommentar, $wirdBewilligt, $isSuper )
{
    global $db;
    if ( $isSuper )
    {
        $sqlC = "update anfrage set tokenAnzahlNeu = '$tokenNeu', wirdBewilligt = '$wirdBewilligt', kommentar = '$kommentar', superkuerzel = '$userName' where id = '$id' and datum = '$datum' and zeit = '$zeit' and skuerzel = '$schuelerKuerzel'";
    }
    else
    {
        $sqlC = "update anfrage set tokenAnzahlNeu = '$tokenNeu', wirdBewilligt = '$wirdBewilligt', kommentar = '$kommentar', lehrerKuerzel = '$userName' where id = '$id' and datum = '$datum' and zeit = '$zeit' and skuerzel = '$schuelerKuerzel'";
    }

    return mysqli_query($db, $sqlC);
}

function listAllReqests()
{
    global $db;
    $out = array();

    $slqC = "select * from anfrage order by datum desc, zeit desc";
    $anfragen = mysqli_query($db, $slqC);

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