<?php
/**
 * Created by PhpStorm.
 * User: Mir
 * Date: 19.11.2018
 * Time: 11:06
 */
session_start();

$db = mysqli_connect('localhost', 'root', '2017lewiS661451', 'tvs_datenbank');
$_SESSION['finished'] = False;
$userName = $_SESSION['userName'];


function printAwardDropDown ()
{
    global $db;
    $sqlC = 'select name from award';
    $award = mysqli_query($db, $sqlC);
    echo '<select id=awardTyp name=awardTyp>';
    for (; $award_array = mysqli_fetch_assoc($award); ) {
        echo '<option value=' . $award_array['name'] . '>' . $award_array['name'] . '</option>';
    }
    echo '</select>';
}

function printSucc()
{
    if ( $_SESSION['finished'])
    {
        if ( $_SESSION['result'] )
        {
            echo 'Success!';
        }
        else
        {
            echo 'Error';
        }
    }
}


if(isset($_GET['requestToken']))
{
    //insert into anfrage( datum, zeit, aName, superkuerzel, lehrerKuerzel, eName, eDatum, untName, skuerzel, tokenAnzahl, beschreibung, betreff, wirdBewilligt, kommentar ) values (CURDATE(), CURTIME(), NULL, NULL, NULL, NULL, NULL, NULL, 'swahl', '1', 'Test ist das', 'Test', NULL, NULL);
    $awardTyp = $_POST['awardTyp'];
    $tokenAnzahl = $_POST['tokenanzahl'];
    $betreff = $_POST['betreff'];
    $beschreibung = $_POST['beschreibung'];
    $userName = $_SESSION['userName'];
    // inserten, alles andere auf NULL setzten --> bei der normalen abfrage nicht notwendig
    $result = requestTokenBasic($awardTyp, $tokenAnzahl, $betreff, $beschreibung, $userName);

    if ($result)
    {
        $_SESSION['result'] = True;
    }
    else
    {
        $_SESSION['result'] = False;
    }
    $_SESSION['finished'] = True;
}

?>