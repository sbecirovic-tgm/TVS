<?php
/**
 * Created by PhpStorm.
 * User: Mir
 * Date: 19.11.2018
 * Time: 11:06
 */
session_start();


$db = mysqli_connect('localhost', 'tokenverwaltung', '1234', 'tvs_datenbank');

$userName = $_SESSION['userName'];
$_SESSION['requestResult'] = Null;

function printAwardDropDown ()
{
    global $db;
    $sqlC = 'select name from award';
    $award = mysqli_query($db, $sqlC);
    for (; $award_array = mysqli_fetch_assoc($award); ) {
        $name = $award_array['name'];
        echo '<a class="dropdown-item" style="cursor: pointer;" onclick="setAwardButton(\'' . $name . '\')">' . $name . '</a>';
    }
}

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

        echo '<div class="card-body"><form action="?eventEintragen'.$i.'=1" method="post"><div class="row"><div class="col-sm-9"><a name="event'.$i.'"> '.$name.' ('.$aName.') - '.$datum.'</a><input type="hidden" name="eventName'.$i.'" value="'.$name.'"><input type="hidden" name="eventAName'.$i.'" value="'.$aName.'"><input type="hidden" name="eventDatum'.$i.'" value="'.$datum.'"></div><div class="col-sm-3"><input class="btn btn-outline-primary" type="submit" value="Eintragen"></div></div></form></div>';
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
    $_SESSION['eventEintragung'] = NULL;
    header("Refresh:0; url=events.html");
}

if (isset($_GET['eventEintragen0']))
{
    $out = array();
    $out['eName'] = $_POST['eventName0'];
    $out['aName'] = $_POST['eventAName0'];
    $out['eDate'] = $_POST['eventDatum0'];

    $_SESSION['eventEintragung'] = $out;
    header("Refresh:0; url=events.html");
}

if (isset($_GET['eventEintragen1']))
{
    $out = array();
    $out['eName'] = $_POST['eventName1'];
    $out['aName'] = $_POST['eventAName1'];
    $out['eDate'] = $_POST['eventDatum1'];

    $_SESSION['eventEintragung'] = $out;
    header("Refresh:0; url=events.html");
}

if (isset($_GET['eventEintragen2']))
{
    $out = array();
    $out['eName'] = $_POST['eventName2'];
    $out['aName'] = $_POST['eventAName2'];
    $out['eDate'] = $_POST['eventDatum2'];

    $_SESSION['eventEintragung'] = $out;
    header("Refresh:0; url=events.html");
}

if (isset($_GET['eventEintragen3']))
{
    $out = array();
    $out['eName'] = $_POST['eventName3'];
    $out['aName'] = $_POST['eventAName3'];
    $out['eDate'] = $_POST['eventDatum3'];

    $_SESSION['eventEintragung'] = $out;
    header("Refresh:0; url=events.html");
}


if(isset($_GET['requestToken'])) {
    include_once ("../php/anfragenVerwalten.php");
    //insert into anfrage( datum, zeit, aName, superkuerzel, lehrerKuerzel, eName, eDatum, untName, skuerzel, tokenAnzahl, beschreibung, betreff, wirdBewilligt, kommentar ) values (CURDATE(), CURTIME(), NULL, NULL, NULL, NULL, NULL, NULL, 'swahl', '1', 'Test ist das', 'Test', NULL, NULL);
    $awardTyp = $_POST['awardTypeBackend'];
    $tokenAnzahl = $_POST['tokenAnzahl'];
    $betreff = $_POST['betreff'];
    $beschreibung = $_POST['beschreibung'];
    // inserten, alles andere auf NULL setzten --> bei der normalen abfrage nicht notwendig
    $result = requestTokenBasic($awardTyp, $tokenAnzahl, $betreff, $beschreibung, $userName);
    $_SESSION['requestResult'] = $result;
}

function printMeldung()
{
    $result = $_SESSION['requestResult'];
    if ($result)
    {
        echo '<script language="JavaScript" type="text/javascript">errorMsg = document.getElementById("antragErrorMsg");errorMsg.innerHTML = \'<div class="alert alert-success alert-dismissible fade show abstand1" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>Ihr Antrag wurde erfolgreich gestellt!</div>\';</script>';
    }
}
//setTimeout(makeDiss, 5000);
if(isset($_GET['logout']))
{
    include_once("../php/userCheck.php");
    logout();
}

?>