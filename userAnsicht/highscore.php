<?php
/**
 * Created by PhpStorm.
 * User: Mir
 * Date: 07.12.2018
 * Time: 19:30
 */
$db = mysqli_connect('localhost', 'tokenverwaltung', '1234', 'tvs_datenbank');
$userName = $_SESSION['userName'];


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