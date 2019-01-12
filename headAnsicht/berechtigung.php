<?php
/**
 * Created by PhpStorm.
 * User: Mir
 * Date: 12.01.2019
 * Time: 11:10
 */
session_start();

$userName = $_SESSION['userName'];

function printAwardDropDown ()
{
    global $db;
    $sqlC = 'select name from award';
    $award = mysqli_query($db, $sqlC);
    $out = '';
    for (; $award_array = mysqli_fetch_assoc($award); ) {
        $name = $award_array['name'];
        $out = $out . '<a class="dropdown-item" style="cursor: pointer;" onclick="setAwardButton(\'' . $name . '\')">' . $name . '</a>';
    }
    return $out;
}

function printAward()
{
    if (  key_exists('berechtigungAward', $_SESSION) )
    {
        $aName = $_SESSION['berechtigungAward'];
        if ( $aName != null )
        {
            echo '<button type="button" name="awardType" id="awardType" class="btn btn-primary dropdown-toggle btn-outline-primary"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">'.$aName.'</button>
                        <div class="dropdown-menu">' . printAwardDropDown() . '
                        </div>
                        <form id="awardForm" action="?setAward=1" method="post">
                            <input type="text" min="0" id="awardTypeBackend" name="awardTypeBackend" class="hiddenMeldung" value="'.$aName.'">
                        </form>';
        }
        else
        {
            echo '<button type="button" name="awardType" id="awardType" class="btn btn-primary dropdown-toggle btn-outline-primary"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Award ausw&auml;hlen</button>
                        <div class="dropdown-menu">' . printAwardDropDown() . '
                        </div>
                        <form id="awardForm" action="?setAward=1" method="post">
                            <input type="text" min="0" id="awardTypeBackend" name="awardTypeBackend" class="hiddenMeldung" value="'.$aName.'">
                        </form>';
        }
    }
    else
    {
        $_SESSION['berechtigungAward'] = null;
        echo '<button type="button" name="awardType" id="awardType" class="btn btn-primary dropdown-toggle btn-outline-primary"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Award ausw&auml;hlen</button>
                        <form id="awardForm" action="?setAward=1" method="post">
                            <input type="text" min="0" id="awardTypeBackend" name="awardTypeBackend" class="hiddenMeldung" value="">
                        </form>

                        <div class="dropdown-menu">' . printAwardDropDown() . '
                        </div>';
    }
}

function printLehrerToAward()
{
    include_once ("../php/userCheck.php");
    $aName = $_SESSION['berechtigungAward'];
    if ( $aName != null )
    {
        $lehrerArray = getAllLehrerNotBerechtigt($aName);
    }
    else
    {
        $lehrerArray = getAllLehrer();

    }
    foreach ( $lehrerArray as $lehrer )
    {
        $name = getNameToLehrerKuerzel($lehrer);
        echo '<option id="'.$lehrer.'" value="'.$name.'">'.$name.'</option>';
    }
}

function printLehrerBerechtigtToAward()
{
    include_once ("../php/userCheck.php");
    $aName = $_SESSION['berechtigungAward'];
    if ( $aName != null )
    {
        $lehrerArray = getBerechtigteLehrerToAward($aName);
        foreach ( $lehrerArray as $lehrer )
        {
            $name = getNameToLehrerKuerzel($lehrer);
            echo '<option id="'.$lehrer.'" value="'.$name.'">'.$name.'</option>';
        }
    }
}


if (isset($_GET['setAward']))
{
    $aName = $_POST['awardTypeBackend'];
    if ( $aName == "" )
    {
        $aName = null;
    }
    $_SESSION['berechtigungAward'] = $aName;
}








// menü
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
?>