<?php
/**
 * Created by PhpStorm.
 * User: Mir
 * Date: 22.12.2018
 * Time: 18:20
 */
session_start();

$userName = $_SESSION['userName'];

function printHighscoreTop3Overall()
{
    include_once ("../php/getHighscore.php");
    $highscore = getTokenHighscoreThisSaisonLimit(3);

    $i = 1;
    foreach ($highscore as $name => $anzahl )
    {
        echo '<tr><th scope="row">'.$i.'</th><td>'.$name.'</td><td>'.$anzahl.'</td></tr>';
        $i++;
    }
}

function printHighscoreTop3Award()
{
    include_once ("../php/getHighscore.php");
    $highscore = getAwardHighscoreThisSaisonLimit(3);

    $i = 1;
    foreach ($highscore as $name => $anzahl )
    {
        echo '<tr><th scope="row">'.$i.'</th><td>'.$name.'</td><td>'.$anzahl.'</td></tr>';
        $i++;
    }
}

function printEventLimit4()
{
    include_once ("../php/eventsVerwalten.php");
    $events = listAllEventsLimit(4);
    $i = 0;
    foreach ($events as $event )
    {
        $datum = $event['datum'];
        $name = $event['name'];
        $aName = $event['aName'];

        echo '<div class="card-body"><form action="?eventVerwalten'.$i.'=1" method="post"><div class="row"><div class="col-sm-9"><a name="event'.$i.'"> '.$name.' ('.$aName.') - '.$datum.'</a><input type="hidden" name="eventName'.$i.'" value="'.$name.'"><input type="hidden" name="eventAName'.$i.'" value="'.$aName.'"><input type="hidden" name="eventDatum'.$i.'" value="'.$datum.'"></div><div class="col-sm-3"><input class="btn btn-outline-primary" type="submit" name="eintragenEvent" value="Verwalten"></div></div></form></div>';
        /*
        $out['name'] = $event_array['name'];
        $out['datum'] = $event_array['datum'];
        $out['superKuerzel'] = $event_array['superKuerzel'];
        $out['lKuerzel'] = $event_array['lKuerzel'];
        $out['aName'] = $event_array['aName'];
        $out['beschreibung'] = $event_array['beschreibung'];
        */
        $i++;
    }
}

function printAnfragenLimit4()
{
    include_once ("../php/anfragenVerwalten.php");
    include_once ("../php/userCheck.php");
    $anfragen = listAllRequestsLimit(4);
    $i = 0;
    foreach ($anfragen as $anfrage )
    {
        $datum = $anfrage['eDatum'];
        $name = getNameFromKuerzel($anfrage['skuerzel']);
        $tokenAnzahl = $anfrage['tokenAnzahl'];
        $betreff = $anfrage['betreff'];
        $id = $anfrage['id'];

        echo '<form action="?anfrageVerwalten'.$i.'=1" method="post"><input type="number" name="idBackend" class="hiddenMeldung" value="'.$id.'"><tr><th scope="row">'.$name.'</th><td>'.$tokenAnzahl.'</td><td>'.$betreff.'</td><td>'.$datum.'</td><td><input class="btn btn-outline-primary" type="submit" value="Verwalten"></td></tr></form>';
        $i++;
    }
    /*
        $out[$i]['id'] = $anfragen_array['id'];
        $out[$i]['datum'] = $anfragen_array['datum'];
        $out[$i]['zeit'] = $anfragen_array['zeit'];
        $out[$i]['skuerzel'] = $anfragen_array['skuerzel'];
        $out[$i]['aName'] = $anfragen_array['aName'];
        $out[$i]['eName'] = $anfragen_array['eName'];
        $out[$i]['eDatum'] = $anfragen_array['eDatum'];
        $out[$i]['untName'] = $anfragen_array['untName'];
        $out[$i]['tokenAnzahl'] = $anfragen_array['tokenAnzahl'];
        $out[$i]['beschreibung'] = $anfragen_array['beschreibung'];
        $out[$i]['betreff'] = $anfragen_array['betreff'];
        $out[$i]['wirdBewilligt'] = $anfragen_array['wirdBewilligt'];
        $out[$i]['kommentar'] = $anfragen_array['kommentar'];
    */
}

if (isset($_GET['anfrageVerwalten']))
{
    $_SESSION['anfrageVerwaltung'] = NULL;
    header("Refresh:0; url=anfragen.html");
}

if (isset($_GET['anfrageVerwalten0']))
{
    $id = $_POST['idBackend'];
    $anfrage = getAnfrageFromId($id);

    $_SESSION['anfrageVerwaltung'] = $anfrage;
    header("Refresh:0; url=anfragen.html");
}

if (isset($_GET['anfrageVerwalten1']))
{
    $id = $_POST['idBackend'];
    $anfrage = getAnfrageFromId($id);

    $_SESSION['anfrageVerwaltung'] = $anfrage;
    header("Refresh:0; url=anfragen.html");
}

if (isset($_GET['anfrageVerwalten2']))
{
    $id = $_POST['idBackend'];
    $anfrage = getAnfrageFromId($id);

    $_SESSION['anfrageVerwaltung'] = $anfrage;
    header("Refresh:0; url=anfragen.html");
}

if (isset($_GET['anfrageVerwalten3']))
{
    $id = $_POST['idBackend'];
    $anfrage = getAnfrageFromId($id);

    $_SESSION['anfrageVerwaltung'] = $anfrage;
    header("Refresh:0; url=anfragen.html");
}

if (isset($_GET['eventVerwalten0']))
{
    $out = array();
    $out['eName'] = $_POST['eventName0'];
    $out['aName'] = $_POST['eventAName0'];
    $out['eDatum'] = $_POST['eventDatum0'];

    $_SESSION['eventVerwaltung'] = $out;
    header("Refresh:0; url=adminEvents.html");
}

if (isset($_GET['eventVerwalten1']))
{
    $out = array();
    $out['eName'] = $_POST['eventName1'];
    $out['aName'] = $_POST['eventAName1'];
    $out['eDatum'] = $_POST['eventDatum1'];

    $_SESSION['eventVerwaltung'] = $out;
    header("Refresh:0; url=adminEvents.html");
}

if (isset($_GET['eventVerwalten2']))
{
    $out = array();
    $out['eName'] = $_POST['eventName2'];
    $out['aName'] = $_POST['eventAName2'];
    $out['eDatum'] = $_POST['eventDatum2'];

    $_SESSION['eventVerwaltung'] = $out;
    header("Refresh:0; url=adminEvents.html");
}

if (isset($_GET['eventVerwalten3']))
{
    $out = array();
    $out['eName'] = $_POST['eventName3'];
    $out['aName'] = $_POST['eventAName3'];
    $out['eDatum'] = $_POST['eventDatum3'];

    $_SESSION['eventVerwaltung'] = $out;
    header("Refresh:0; url=adminEvents.html");
}

if (isset($_GET['overallHighscoreAnzeigen']))
{
    $_SESSION['highscore'] = "overallToken";
    header("Refresh:0; url=highscore.html");
}

if (isset($_GET['awardHighscoreAnzeigen']))
{
    $_SESSION['highscore'] = "overallAward";
    header("Refresh:0; url=highscore.html");
}

if (isset($_GET['highscoreAnzeigen']))
{
    header("Refresh:0; url=highscore.html");
}

if (isset($_GET['eventAnzeigen']))
{
    $_SESSION['eventVerwaltung'] = NULL;
    header("Refresh:0; url=adminEvents.html");
}

if(isset($_GET['logout']))
{
    include_once("../php/userCheck.php");
    logout();
}
