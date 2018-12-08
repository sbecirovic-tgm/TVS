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


function printAwardDropDown ()
{
    global $db;
    $sqlC = 'select name from award';
    $award = mysqli_query($db, $sqlC);
    for (; $award_array = mysqli_fetch_assoc($award); ) {
        $name = $award_array['name'];
        echo '<a class="dropdown-item" onclick="setAwardButton(\'' . $name . '\')">' . $name . '</a>';
    }
}

function printHighscoreTop3Overall()
{
    include_once ("../php/getHighscore.php");
    $highscore = getTokenHighscoreThisSaisonLimit(3);

    $i = 0;
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

    $i = 0;
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

        echo '<div class="card-body"><form action="?eventEintragen'.$i.'=1" method="post"><div class="row"><div class="col-sm-9"><a name="event'.$i.'"> '.$name.' ('.$aName.') - '.$datum.'</a><input type="hidden" name="event'.$i.'" value="'.$name.' ('.$aName.') - '.$datum.'"></div><div class="col-sm-3"><input class="btn btn-outline-primary" type="submit" name="eintragenEvent" value="Eintragen"></div></div></form></div>';
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
    $_SESSION['highscore'] = "overall";
}

if (isset($_GET['awardHighscoreAnzeigen']))
{
    $_SESSION['highscore'] = "award";
}

if (isset($_GET['highscoreAnzeigen']))
{
    $_SESSION['highscore'] = NULL;
}

if (isset($_GET['eventAnzeigen']))
{
    $_SESSION['eventEintragung'] = NULL;
    header("Refresh:0; url=events.html");
}

if (isset($_GET['eventEintragen0']))
{
    $temp = $_POST['event0'];
    $out = array();
    $out['eName'] = substr($temp,0, strpos($temp, "(")-1);
    $out['aName'] = substr($temp, strpos($temp, "(")+1, strpos($temp, ")")-strpos($temp, "(")-1);
    $jahr = substr($temp, strpos($temp, "-")+2, strlen($temp)-(strpos($temp, "-")+2));
    $out['eDateTag']= substr($jahr, 0, strpos($jahr, "."));
    $rest = substr($jahr, strpos($jahr, ".")+1, strlen($jahr));
    $out['eDateMon'] = substr($rest, 0, strpos($rest, '.'));
    $out['eDateJahr'] = substr($rest, strpos($rest, ".")+1, strlen($rest));

    $_SESSION['eventEintragung'] = $out;
    header("Refresh:0; url=events.html");
}

if (isset($_GET['eventEintragen1']))
{
    $temp = $_POST['event1'];
    $out = array();
    $out['eName'] = substr($temp,0, strpos($temp, "(")-1);
    $out['aName'] = substr($temp, strpos($temp, "(")+1, strpos($temp, ")")-strpos($temp, "(")-1);
    $jahr = substr($temp, strpos($temp, "-")+2, strlen($temp)-(strpos($temp, "-")+2));
    $out['eDateTag']= substr($jahr, 0, strpos($jahr, "."));
    $rest = substr($jahr, strpos($jahr, ".")+1, strlen($jahr));
    $out['eDateMon'] = substr($rest, 0, strpos($rest, '.'));
    $out['eDateJahr'] = substr($rest, strpos($rest, ".")+1, strlen($rest));

    $_SESSION['eventEintragung'] = $out;
    header("Refresh:0; url=events.html");
}

if (isset($_GET['eventEintragen2']))
{
    $temp = $_POST['event2'];
    $out = array();
    $out['eName'] = substr($temp,0, strpos($temp, "(")-1);
    $out['aName'] = substr($temp, strpos($temp, "(")+1, strpos($temp, ")")-strpos($temp, "(")-1);
    $jahr = substr($temp, strpos($temp, "-")+2, strlen($temp)-(strpos($temp, "-")+2));
    $out['eDateTag']= substr($jahr, 0, strpos($jahr, "."));
    $rest = substr($jahr, strpos($jahr, ".")+1, strlen($jahr));
    $out['eDateMon'] = substr($rest, 0, strpos($rest, '.'));
    $out['eDateJahr'] = substr($rest, strpos($rest, ".")+1, strlen($rest));

    $_SESSION['eventEintragung'] = $out;
    header("Refresh:0; url=events.html");
}

if (isset($_GET['eventEintragen3']))
{
    $temp = $_POST['event3'];
    $out = array();
    $out['eName'] = substr($temp,0, strpos($temp, "(")-1);
    $out['aName'] = substr($temp, strpos($temp, "(")+1, strpos($temp, ")")-strpos($temp, "(")-1);
    $jahr = substr($temp, strpos($temp, "-")+2, strlen($temp)-(strpos($temp, "-")+2));
    $out['eDateTag']= substr($jahr, 0, strpos($jahr, "."));
    $rest = substr($jahr, strpos($jahr, ".")+1, strlen($jahr));
    $out['eDateMon'] = substr($rest, 0, strpos($rest, '.'));
    $out['eDateJahr'] = substr($rest, strpos($rest, ".")+1, strlen($rest));

    $_SESSION['eventEintragung'] = $out;
    header("Refresh:0; url=events.html");
}


if(isset($_GET['requestToken'])) {
    include_once ("../php/anfragenVerwalten.php");
    //insert into anfrage( datum, zeit, aName, superkuerzel, lehrerKuerzel, eName, eDatum, untName, skuerzel, tokenAnzahl, beschreibung, betreff, wirdBewilligt, kommentar ) values (CURDATE(), CURTIME(), NULL, NULL, NULL, NULL, NULL, NULL, 'swahl', '1', 'Test ist das', 'Test', NULL, NULL);

    $awardTyp = $_POST['awardType'];
    $tokenAnzahl = $_POST['tokenAnzahl'];
    $betreff = $_POST['betreff'];
    $beschreibung = $_POST['beschreibung'];
    // inserten, alles andere auf NULL setzten --> bei der normalen abfrage nicht notwendig
    $result = requestTokenBasic($awardTyp, $tokenAnzahl, $betreff, $beschreibung, $userName);

}
if(isset($_GET['logout']))
{
    include_once("../php/userCheck.php");
    logout();
}

?>