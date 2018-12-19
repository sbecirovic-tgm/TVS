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
        if ($temp = 0) {
            echo '<button type="button" id="highscoreType" class="btn btn-primary dropdown-toggle btn-outline-primary" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Token Highscore</button><input id="highscoreTypeTemp" name="highscoreTypeTemp" class="hiddenMeldung" value="Token Highscore">';
        } else {
            echo '<button type="button" id="highscoreType" class="btn btn-primary dropdown-toggle btn-outline-primary" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Award Highscore</button><input id="highscoreTypeTemp" name="highscoreTypeTemp" class="hiddenMeldung" value="Award Highscore">';
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

function printHighscore( $highscore )
{
    $i = 1;
    foreach ($highscore as $name => $anzahl)
    {
        echo '<tr><td scope="row">'.$i.'</td><td>'.$name.'</td><td>'.$anzahl.'</td></tr>';
        $i++;
    }
}


function printHighscoreThisSaision()
{
    // 0: Award
    // 1: Token
    include_once ("../php/getHighscore.php");
    if ($_SESSION['highscoreTypeTemp'] == 0)
    {
        $highscore = getAwardHighscoreThisSaision();
    }
    else {
        $highscore = getTokenHighscoreThisSaison();
    }

    printHighscore($highscore);

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

}

if(isset($_GET['logout']))
{
    include_once("../php/userCheck.php");
    logout();
}