<?php
/**
 * Created by PhpStorm.
 * User: Mir
 * Date: 07.12.2018
 * Time: 19:30
 */
$db = mysqli_connect('localhost', 'tokenverwaltung', '1234', 'tvs_datenbank');
session_start();
$userName = $_SESSION['userName'];



function setHighscoreType()
{
    if ( !array_key_exists('highscoreTypeTemp', $_SESSION))
    {
        // 0: Award
        // 1: Token
        $_SESSION['highscoreTypeTemp'] = 1;
        echo '<button type="button" id="highscoreType" class="btn btn-primary dropdown-toggle btn-outline-primary" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" >Award Highscore</button><input id="highscoreTypeTemp" name="highscoreTypeTemp" class="hiddenMeldung" value="Award Highscore">';
    }
    else {
        $temp = $_SESSION['highscoreTypeTemp'];
        if ($temp == 0) {
            echo '<button type="button" id="highscoreType" class="btn btn-primary dropdown-toggle btn-outline-primary" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Token Highscore</button><input id="highscoreTypeTemp" name="highscoreTypeTemp" class="hiddenMeldung" value="Token Highscore">';
        } else {
            echo '<button type="button" id="highscoreType" class="btn btn-primary dropdown-toggle btn-outline-primary" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Award Highscore</button><input id="highscoreTypeTemp" name="highscoreTypeTemp" class="hiddenMeldung" value="Award Highscore">';
        }
    }
}

function setAwardType()
{
    if ( !array_key_exists('highscoreAwardType', $_SESSION))
    {
        // -1: alle Awards
        $_SESSION['highscoreAwardType'] = -1;
        echo '<button type="button" id="AwardType" class="btn btn-primary dropdown-toggle btn-outline-primary" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">All Awards</button><input id="AwardTypeTemp" name="AwardTypeTemp" class="hiddenMeldung" value="All Awards">';
    }
    else {
        if ($_SESSION['highscoreAwardType'] == -1) {
            echo '<button type="button" id="AwardType" class="btn btn-primary dropdown-toggle btn-outline-primary" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">All Awards</button><input id="AwardTypeTemp" name="AwardTypeTemp" class="hiddenMeldung" value="All Awards">';
        } else {
            include_once("../php/awardsVerwalten.php");
            $awards = getAllAwardsOrderedByName();
            $name = $awards[$_SESSION['highscoreAwardType']]['name'];
            echo '<button type="button" id="AwardType" class="btn btn-primary dropdown-toggle btn-outline-primary" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">' . $name . '</button><input id="AwardTypeTemp" name="AwardTypeTemp" class="hiddenMeldung" value="' . $name . '">';
        }
    }
}


function setSaisonNum()
{
    if ( !array_key_exists('highscoreSaisonNum', $_SESSION))
    {
        // 0: jetztige Saison
        $_SESSION['highscoreSaisonNum'] = 0;
        echo '<form id="saisonNumForm" action="?saisonNumForm=1" method="post"><div class="input-group-prepend"><span class="input-group-text btn btn-primary btn-outline-primary">Saison Nr.</span></div><input type="number" min="-1" id="saisonNum" name="saisonNum" class="form-control tokens" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default" value="0"></form>';
    }
    else
    {
        $num = $_SESSION['highscoreSaisonNum'];
        echo '<form id="saisonNumForm" action="?saisonNumForm=1" method="post"><div class="input-group-prepend"><span class="input-group-text btn btn-primary btn-outline-primary">Saison Nr.</span></div><input type="number" min="-1" id="saisonNum" name="saisonNum" class="form-control tokens" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default" value="'.$num.'"></form>';
    }
}

function setSortieren()
{
    if ( !array_key_exists('highscoreSortiert', $_SESSION))
    {
        // 0: Aufsteigend
        // 1: Absteigend
        $_SESSION['highscoreSaisonNum'] = 0;
        echo '<form id="highscoreSortiert" action="?highscoreSortiert=1" method="post"><button type="button" id="SortType" class="btn btn-primary dropdown-toggle btn-outline-primary"data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Aufsteigend</button><input type="number" min="-1" id="saisonNum" name="saisonNum" class="form-control tokens" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default" value="Aufsteigend"></form>';
    }
    else
    {
        if ( $_SESSION['highscoreSortiert'] = 0)
        {
            echo '<form id="highscoreSortiert" action="?highscoreSortiert=1" method="post"><button type="button" id="SortType" class="btn btn-primary dropdown-toggle btn-outline-primary"data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Aufsteigend</button><input type="number" min="-1" id="saisonNum" name="saisonNum" class="form-control tokens" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default" value="Aufsteigend"></form>';
        }
        else
        {
            echo '<form id="highscoreSortiert" action="?highscoreSortiert=1" method="post"><button type="button" id="SortType" class="btn btn-primary dropdown-toggle btn-outline-primary"data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Absteigend</button><input type="number" min="-1" id="saisonNum" name="saisonNum" class="form-control tokens" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default" value="Absteigend"></form>';
        }
    }
}

function printAwardDropDown ()
{
    global $db;
    $sqlC = 'select name from award';
    $award = mysqli_query($db, $sqlC);
    for (; $award_array = mysqli_fetch_assoc($award); ) {
        $name = $award_array['name'];
        echo '<a class="dropdown-item" onclick="setAwardButton(\'' . $name . '\')" href="#">' . $name . '</a>';
    }
}

function printHighscoreEnd( $highscore )
{
    $i = 1;
    foreach ($highscore as $name => $anzahl)
    {
        echo '<tr><td scope="row">'.$i.'</td><td>'.$name.'</td><td>'.$anzahl.'</td></tr>';
        $i++;
    }
}

function printHighscore()
{
    $typ = $_GET['highscoreTypeTemp'];
    $award = $_GET['highscoreAwardType'];
    $saison = $_GET['highscoreSaisonNum'];
    $sortiert = $_GET['highscoreSortiert'];

    if ($typ == "Token Highscore") {
        printTokenHighscore($award, $saison, $sortiert);
    } else {
        printAwardHighscore($award, $saison, $sortiert);
    }
}

function printTokenHighscore($award, $saison, $sortiert)
{
    if ( $award == -1)
    {
        if ( $saison == -1 )
        {
            $out = getTokenHighscoreAllTime();
        }
        elseif ( $saison == 0 )
        {
            $out = getTokenHighscoreThisSaison();
        }
        else
        {
            $out = getTokenHighscoreProSaison($saison);
        }
        if ( $sortiert == 'Absteigend' )
        {
            asort($out);
        }
    }
    else
    {
        if ( $saison == -1 )
        {
            $out = getTokenHighscoreProAwardAllTime($award);
        }
        elseif ( $saison == 0 )
        {
            include_once ("../php/saisonVerwalten.php");
            $out = getTokenHighscoreProAwardPerSaison($award, getSaisonNumb());
        }
        else
        {
            $out = getTokenHighscoreProAwardPerSaison($award, $saison);
        }
        if ( $sortiert == 'Absteigend' )
        {
            asort($out);
        }
    }
}

function printAwardHighscore($award, $saison, $sortiert)
{
    if ( $award == -1)
    {
        if ( $saison == -1 )
        {
            $out = getAwardHighscoreAllTime();
        }
        elseif ( $saison == 0 )
        {
            $out = getAwardHighscoreThisSaison();
        }
        else
        {
            $out = getAwardHighscoreProSaison($saison);
        }
        if ( $sortiert == 'Absteigend' )
        {
            asort($out);
        }
    }
    else
    {
        if ( $saison == -1 )
        {
            $out = getAwardHighscoreProAwardAllTime($award);
        }
        elseif ( $saison == 0 )
        {
            include_once ("../php/saisonVerwalten.php");
            $out = getAwardHighscoreProAwardPerSaison($award, getSaisonNumb());
        }
        else
        {
            $out = getAwardHighscoreProAwardPerSaison($award, $saison);
        }
        if ( $sortiert == 'Absteigend' )
        {
            asort($out);
        }
    }
}


function printHighscoreThisSaision()
{
    // 0: Award
    // 1: Token
    include_once ("../php/getHighscore.php");
    if ($_SESSION['highscoreTypeTemp'] == 0)
    {
        $highscore = getAwardHighscoreThisSaison();
    }
    else {
        $highscore = getTokenHighscoreThisSaison();
    }

    //printHighscore($highscore);

}

function printAwardHighscoreThisSaision()
{

}


if (isset($_GET['eventAnzeigen']))
{
    $_SESSION['eventEintragung'] = NULL;
    header("Refresh:0; url=events.html");
}
if (isset($_GET['highscoreAnzeigen']))
{
    $_SESSION['highscore'] = NULL;
    header("Refresh:0; url=highscore.html");
}

if (isset($_GET['highscoreForm']))
{
    $type = $_POST['highscoreTypeTemp'];
    if ($type == "Award Highscore")
    {
        // 0: Award
        // 1: Token
        $_SESSION['highscoreTypeTemp'] = 0;
    }
    elseif( $type == "Token Highscore")
    {
        $_SESSION['highscoreTypeTemp'] = 1;
    }
}

if (isset($_GET['awardForm']))
{
    $award = $_GET['AwardTypeTemp'];
    $awards = getAllAwardsOrderedByName();

    if ( $award == 'All Awards' )
    {
        $_SESSION['highscoreAwardType'] = -1;
    }
    else
    {
        $_SESSION['highscoreAwardType'] = array_search($_GET['AwardTypeTemp'], $awards);
    }
}

if (isset($_GET['saisonNumForm']))
{
    $num = $_GET['saisonNum'];
    $_SESSION['highscoreSaisonNum'] = $num;
}

if (isset($_GET['highscoreSortiert']))
{
    $sort = $_GET['saisonNum'];
    if ( $sort == 'Aufsteigend')
    {
       $sort = 0;
    }
    else
    {
        $sort = 1;
    }
}

if(isset($_GET['logout']))
{
    include_once("../php/userCheck.php");
    logout();
}