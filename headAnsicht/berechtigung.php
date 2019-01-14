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
    $out = '';
    $lehrerArray = getAllLehrerNotBerechtigt($aName);
    foreach ( $lehrerArray as $lehrer )
    {
        $name = getNameToLehrerKuerzel($lehrer);
        $out = $out . '<option id="'.$lehrer.'" value="'.$name.'">'.$name.'</option>';
    }
    return $out;
}

function printLehrerBerechtigtToAward()
{
    include_once ("../php/userCheck.php");
    $aName = $_SESSION['berechtigungAward'];
    $out = '';
    if ( $aName != null )
    {
        $lehrerArray = getBerechtigteLehrerToAward($aName);
        foreach ( $lehrerArray as $lehrer )
        {
            $name = getNameToLehrerKuerzel($lehrer);
            $out = $out . '<option id="'.$lehrer.'" value="'.$name.'">'.$name.'</option>';
        }
    }
    return $out;
}

function printBody()
{
    $aName = $_SESSION['berechtigungAward'];
    if ( $aName != null )
    {
        echo '<div class="row">
            <!-- Left side select -->
            <div class="col-xs-5 col-md-5 col-sm-5">
                <h3>Verf&uuml;gbare Lehrer</h3>
                <div class="abstand05TopBot" data-toggle="tooltip"  data-placement="bottom" title="Einfach und schnell nach einem Lehrernamen suchen">
                    <input type="text" id="nameFilterLeft" class="form-control" onkeyup="filterFunction(\'mySideToSideSelect\', \'nameFilterLeft\')" placeholder="Nach Namen oder K&uuml;rzel filtern" aria-label="Sizing example input" 
                    aria-describedby="inputGroup-sizing-default" >
                </div>
                <select name="from[]" id="mySideToSideSelect" class="form-control" style="height:70%;" multiple="multiple">' . printLehrerToAward() . '
                </select>
            </div>

            <!-- Action buttons -->
            <div class="col-xs-2 col-md-2 col-sm-2 linksrechts">
                <button type="button" id="mySideToSideSelect_rightSelected" class="btn btn-block btn-primary">
                    <span class="oi oi-chevron-right"></span>
                </button>
                <button type="button" id="mySideToSideSelect_leftSelected" class="btn btn-block btn-primary">
                    <span class="oi oi-chevron-left"></span>
                </button>
            </div>

            <!-- Right side select -->
            <div class="col-xs-5 col-md-5 col-sm-5">
                <h3>Hinzugef&uuml;gte Lehrer</h3>
                <div class="abstand05TopBot" data-toggle="tooltip"  data-placement="bottom" title="Einfach und schnell nach einem Lehrernamen suchen">
                    <input type="text" id="nameFilterRight" class="form-control" onkeyup="filterFunction(\'mySideToSideSelect_to\', \'nameFilterRight\')" placeholder="Nach Namen oder K&uuml;rzel filtern" aria-label="Sizing example input" 
                    aria-describedby="inputGroup-sizing-default" >
                </div>
                <select name="to[]" id="mySideToSideSelect_to" class="form-control" style="height:70%;" multiple="multiple">' . printLehrerBerechtigtToAward() . '
                </select>
            </div>
        </div>
        <div class="row abstand1">
            <div class="col-sm-12">
                <div class="text-right">
                    <button type="button" class="btn btn-primary" onclick="addToFrom()">Speichern</button>
                </div>
                <form id="berechtigen" action="?berechtigen=1" method="post">
                    <div id="lehrerListBackend">
                    </div>
                </form>
            </div>
        </div>';
    }
    else
    {
        echo '<div class="row disabledDiv">
            <!-- Left side select -->
            <div class="col-xs-5 col-md-5 col-sm-5">
                <h3>Verf&uuml;gbare Lehrer</h3>
                <div class="abstand05TopBot" data-toggle="tooltip"  data-placement="bottom" title="Einfach und schnell nach einem Lehrernamen suchen">
                    <input type="text" id="nameFilterLeft" class="form-control" onkeyup="filterFunction(\'mySideToSideSelect\', \'nameFilterLeft\')" placeholder="Nach Namen oder K&uuml;rzel filtern" aria-label="Sizing example input" 
                    aria-describedby="inputGroup-sizing-default" >
                </div>
                <select name="from[]" id="mySideToSideSelect" class="form-control" style="height:70%;" multiple="multiple">' . printLehrerToAward() . '
                </select>
            </div>

            <!-- Action buttons -->
            <div class="col-xs-2 col-md-2 col-sm-2 linksrechts">
                <button type="button" id="mySideToSideSelect_rightSelected" class="btn btn-block btn-primary">
                    <span class="oi oi-chevron-right"></span>
                </button>
                <button type="button" id="mySideToSideSelect_leftSelected" class="btn btn-block btn-primary">
                    <span class="oi oi-chevron-left"></span>
                </button>
            </div>

            <!-- Right side select -->
            <div class="col-xs-5 col-md-5 col-sm-5">
                <h3>Hinzugef&uuml;gte Lehrer</h3>
                <div class="abstand05TopBot" data-toggle="tooltip"  data-placement="bottom" title="Einfach und schnell nach einem Lehrernamen suchen">
                    <input type="text" id="nameFilterRight" class="form-control" onkeyup="filterFunction(\'mySideToSideSelect_to\', \'nameFilterRight\')" placeholder="Nach Namen oder K&uuml;rzel filtern" aria-label="Sizing example input" 
                    aria-describedby="inputGroup-sizing-default" >
                </div>
                <select name="to[]" id="mySideToSideSelect_to" class="form-control" style="height:70%;" multiple="multiple">' . printLehrerBerechtigtToAward() . '
                </select>
            </div>
        </div>
        <div class="row abstand1">
            <div class="col-sm-12">
                <div class="text-right">
                    <button type="button" class="btn btn-primary" onclick="addToFrom()">Speichern</button>
                </div>
                <form id="berechtigen" action="?berechtigen=1" method="post">
                    <div id="lehrerListBackend">
                    </div>
                </form>
            </div>
        </div>';
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

if (isset($_GET['berechtigen']))
{
    include_once ("../php/userCheck.php");
    $seleced = $_POST['anzahlSelected'];
    $aName = $_SESSION['berechtigungAward'];
    $lehrerArray = array();
    for ( $i = 0; $i < $seleced; $i++ )
    {
        $lehrerArray[$i] = $_POST['lehrer'.$i];
    }

    $lehrerArrayVorhanden = getBerechtigteLehrerToAward($aName);

    $toAdd = makeDifference($lehrerArray, $lehrerArrayVorhanden);
    $toDelete = makeDifference($lehrerArrayVorhanden, $lehrerArray);

    berechtigeLehrerArrayToAward($toAdd, $aName);
    deleteBerechtigungLehrerArrayToAward($toDelete, $aName);
}

function makeDifference( $strong, $weak )
{
    $out = array();
    $j = 0;
    for ($i = 0; $i < count($strong); $i++ )
    {
        $elm = $strong[$i];
        if ( !in_array($elm, $weak))
        {
            $out[$j] = $elm;
            $j++;
        }
    }
    return $out;
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