<?php
/**
 * Created by PhpStorm.
 * User: Mir
 * Date: 11.01.2019
 * Time: 15:48
 */

session_start();

$userName = $_SESSION['userName'];


function printAllReqeusts()
{
    include_once ("../php/anfragenVerwalten.php");
    include_once ("../php/userCheck.php");
    global $userName;
    $requests = listAllRequestToLehrerErlaubnis($userName);
    $anfrageVerwalten = $_SESSION['anfrageVerwaltung'];

    if ( count($requests) == 0 )
    {
        echo '<tr>
                <td>
                </td>
                <td></td>
                <td>
                    <div class="media">
                        <div class="media-body">
                            <span class="media-meta pull-right"></span>
                            <h4 class="title"> Noch keine Anfragen
                            </h4>
                        </div>
                    </div>
                </td>
            </tr>';
    }

    $i = 0;
    foreach ($requests as $anfrage )
    {
        /**
        $out[$i]['id'] = $anfragen_array['id'];
        $out[$i]['datum'] = $anfragen_array['datum'];
        $out[$i]['zeit'] = $anfragen_array['zeit'];
        $out[$i]['aName'] = $anfragen_array['aName'];
        $out[$i]['eName'] = $anfragen_array['eName'];
        $out[$i]['eDatum'] = $anfragen_array['eDatum'];
        $out[$i]['untName'] = $anfragen_array['untName'];
        $out[$i]['tokenAnzahl'] = $anfragen_array['tokenAnzahl'];
        $out[$i]['tokenAnzahlNeu'] = $anfragen_array['tokenAnzahlNeu'];
        $out[$i]['beschreibung'] = $anfragen_array['beschreibung'];
        $out[$i]['betreff'] = $anfragen_array['betreff'];
        $out[$i]['wirdBewilligt'] = $anfragen_array['wirdBewilligt'];
        $out[$i]['kommentar'] = $anfragen_array['kommentar'];
        $out[$i]['superkuerzel'] = $anfragen_array['superkuerzel'];
        $out[$i]['lehrerKuerzel'] = $anfragen_array['lehrerKuerzel'];
         */

        $userName = $anfrage['skuerzel'];
        $datumString = $anfrage['datum'];
        $datum = strtotime($datumString);
        $day = date("d", $datum);
        $mon = date("M", $datum);
        $year = date("Y", $datum);

        $zeit = $anfrage['zeit'];
        $betreff = $anfrage['betreff'];
        $beschreibung = $anfrage['beschreibung'];
        $aName = $anfrage['aName'];

        $tokenAnzahl = $anfrage['tokenAnzahl'];

        $eName = $anfrage['eName'];
        if ( $eName != '' )
        {
            $eventDatumString = $anfrage['eDatum'];
            $eDatum = strtotime($eventDatumString);
            $eday = date("d", $eDatum);
            $emon = date("M", $eDatum);
            $eyear = date("Y", $eDatum);

            $event = '<div class="row smallFont abstand1"><div class="col-sm-12"><strong>Event</strong><hr class="nullAbstand">' . $eName . ', ' . $eday . ' ' . $emon . ', ' . $eyear;

            $uKat = $anfrage['untName'];
            if ( $uKat != '' )
            {
                $event = $event . '<br>Anfrage in der Unterkategorie "' . $uKat . '" gestellt';
            }
            $event = $event . '</div></div>';
        }
        else
        {
            $event = '';
        }


        $lehrer = $anfrage['lehrerKuerzel'];
        $super = $anfrage['superkuerzel'];

        $temp = $anfrage['wirdBewilligt'];
        if ( $temp == '' )
        {
            $status = "Zu Bearbeitung";
            $statusBackEnde = "zubearbeiten";
            $statusBackEnde2 = "inbearbeitung";
            $komm = '';
            $kommentar = '';
        }
        else if ( $temp == true )
        {
            $status = "Best&auml;tigt";
            $statusBackEnde = "best&auml;tigt";
            $statusBackEnde2 = $statusBackEnde;

            if ( $lehrer == "" )
            {
                $bearbeitetVon = getNameFormSuper($super);
            }
            else if ( $super == "" )
            {
                $bearbeitetVon = getNameToLehrerKuerzel($lehrer);
            }
            else
            {
                $bearbeitetVon = "Gott?";
            }
            $komm = '<p class="summary smallFont"><strong>Bewertet von:</strong> ' . $bearbeitetVon . '</p>';
            $tokenNeu = $anfrage['tokenAnzahlNeu'];
            $kommentar = '<div class="row smallFont abstand1"><div class="col-sm-12"><strong>Kommentar des Lehrers</strong><hr class="noAbstand">' . $anfrage['kommentar'];

            if ( $tokenNeu != '' )
            {
                $kommentar = $kommentar . '<br><strong>Vom Lehrer ge&auml;nderte Tokenanzahl:</strong> ' . $tokenNeu;
            }
            $kommentar = $kommentar . '</div></div>';
        }
        else if ( $temp == false )
        {
            $status = "Abgelehnt";
            $statusBackEnde = "abgelehnt";
            $statusBackEnde2 = $statusBackEnde;
            $komm = '';
            $kommentar = '';
        }

        $id = $anfrage['id'];

        $checkBox = "";
        if ( $anfrageVerwalten != null )
        {
            $idVerwalten = $anfrageVerwalten['id'];
            if ( $id == $idVerwalten )
            {
                $checkBox = "checked";
                $_SESSION['foundAnfrage'] = $id;
            }
        }
        // zum abfragen einer checkbox: isset($_POST['formWheelchair'] schauen ob vorhanden und dann value handeln
        echo '<tr data-status="' . $statusBackEnde . '" data-name="'. getNameFromKuerzel($userName) . '" data-kuerzel="' . $userName . '" ' . $checkBox . '>
                <td>
                    <div class="form-check">
                        <input type="checkbox" id="adAddCheckBox' . $i . '" name="adAddCheckBox" onclick="addToChangeList(' . $i . ')" value="' . $id . '">
                    </div>
                </td>
                <td class="clickable" data-toggle="collapse" data-target="#anfrage' . $i . '" aria-expanded="false" aria-controls="anfrage' . $i . '"></td>
                <td class="clickable" data-toggle="collapse" data-target="#anfrage' . $i . '" aria-expanded="false" aria-controls="anfrage' . $i . '">
                    <div class="media">
                        <div class="media-body">
                            <span class="media-meta pull-right">'. $day . ' ' . $mon .', ' . $year . ' ' . $zeit . '</span>
                            <h4 class="title"> Anfrage von ' . getNameFromKuerzel($userName) . ' ('.$userName.')<br>Betreff: ' . $betreff . '
                                <span class="pull-right ' . $statusBackEnde2 . '">(' . $status . ')</span>
                            </h4>' . $komm . '
                        </div>
                    </div>
                    <div id="anfrage' . $i . '" class="collapse">
                        <div class="row smallFont">
                                <div class="col-sm-6">
                                    <strong>Geforderte Token: </strong> ' . $tokenAnzahl . '
                                </div>
                                <div class="col-sm-6">
                                    <strong>Award:</strong> ' . $aName . '
                                </div>
                        </div>
                        <div class="row smallFont abstand1">
                            <div class="col-sm-12">
                                <strong>Beschreibung</strong>
                                <hr class="nullAbstand">
                                ' . $beschreibung . '
                            </div>
                        </div>
                            ' . $event . '
                            ' . $kommentar . '
                    </div>
                </td>
            </tr>';
        $i++;
    }
}

function printVor()
{
    if ( key_exists('foundAnfrage', $_SESSION))
    {
        $id = $_SESSION['foundAnfrage'];
        echo '<input type="text" id="antrag0" name="antrag0" class="hiddenMeldung" value="' . $id . '">';
        unset($_SESSION['foundAnfrage']);
    }
}

function printVorSelected()
{
    if ( key_exists('foundAnfrage', $_SESSION))
    {
        echo '<input type="number" id="anzahlSelected" name="anzahlSelected" class="hiddenMeldung" value="1">';
    }
    else
    {
        echo '<input type="number" id="anzahlSelected" name="anzahlSelected" class="hiddenMeldung" value="0">';

    }
}


if ( isset($_GET['changeAntraege']))
{
    include_once ("../php/anfragenVerwalten.php");
    $selected = $_POST['anzahlSelected'];
    $toDo = $_POST['toDo'];
    $tokenNew = $_POST['tokenAnzahlNeu'];
    $kommentar = $_POST['kommentar'];

    if ( $tokenNew == '' )
    {
        $tokenNew = null;
    }
    $toChange = array();
    for ( $i = 0; $i < $selected; $i++ )
    {
        $toChange[$i] = $_POST['antrag'.$i];
    }

    if ( $toDo == 1 )
    {
        // bestätigen
        $wirdBewilligt = true;
    }
    else
    {
        // ablehnen
        $wirdBewilligt = false;
    }

    foreach ($toChange as $id )
    {
        bewilligeToken($id, $userName, $tokenNew, $kommentar, $wirdBewilligt);
    }
}

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