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
        echo '<tr><th scope="row">'.$i.'</th><td>'.$name.'</td><td>'.$anzahl.'</td></tr>';
        $i++;
    }
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

    }
    elseif( $type == "Token Highscore")
    {

    }
}