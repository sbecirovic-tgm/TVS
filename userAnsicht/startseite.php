<?php
/**
 * Created by PhpStorm.
 * User: Mir
 * Date: 19.11.2018
 * Time: 11:06
 */
session_start();


$db = mysqli_connect('localhost', 'root', '2017lewiS661451', 'tvs_datenbank.sql');

$userName = $_SESSION['userName'];


function printAwardDropDown ()
{
    global $db;
    $sqlC = 'select name from award';
    $award = mysqli_query($db, $sqlC);
    for (; $award_array = mysqli_fetch_assoc($award); ) {
        $name = $award_array['name'];
        echo '<a class="dropdown-item" onclick="setAwardButton(' . $name . ')">' . $name . '</a>';
    }
}

function printHighscoreTop3Overall()
{
    include ("../php/getHighscore.php");
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
    include ("../php/getHighscore.php");
    $highscore = getAwardHighscoreThisSaisonLimit(3);

    $i = 0;
    foreach ($highscore as $name => $anzahl )
    {
        echo '<tr><th scope="row">'.$i.'</th><td>'.$name.'</td><td>'.$anzahl.'</td></tr>';
        $i++;
    }
}


if(isset($_GET['requestToken']))
{
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
    header("Refresh:0; url=../Login.html");
    if(session_status() == PHP_SESSION_ACTIVE) {
        session_destroy();
    }
}

?>