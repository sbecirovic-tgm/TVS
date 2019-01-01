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

        echo '<div class="card-body"><form action="?eventVerwalten'.$i.'=1" method="post"><div class="row"><div class="col-sm-9"><a name="event'.$i.'"> '.$name.' ('.$aName.') - '.$datum.'</a><input type="hidden" name="event'.$i.'" value="'.$name.' ('.$aName.') - '.$datum.'"></div><div class="col-sm-3"><input class="btn btn-outline-primary" type="submit" name="eintragenEvent" value="Verwalten"></div></div></form></div>';
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

if (isset($_GET['eventVerwalten0']))
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

if (isset($_GET['eventVerwalten1']))
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

if (isset($_GET['eventVerwalten2']))
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

if (isset($_GET['eventVerwalten3']))
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
    header("Refresh:0; url=adminEvents.html");
}

if(isset($_GET['logout']))
{
    include_once("../php/userCheck.php");
    logout();
}
