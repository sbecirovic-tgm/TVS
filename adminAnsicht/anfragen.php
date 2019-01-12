<?php
/**
 * Created by PhpStorm.
 * User: Mir
 * Date: 05.01.2019
 * Time: 17:06
 */
session_start();

$userName = $_SESSION['userName'];

if (isset($_GET['anfrageVerwalten']))
{
    $_SESSION['anfrageVerwaltung'] = NULL;
    header("Refresh:0; url=anfragen.html");
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