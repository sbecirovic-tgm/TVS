<?php
/**
 * Created by PhpStorm.
 * User: Mir
 * Date: 12.01.2019
 * Time: 14:36
 */
session_start();

$userName = $_SESSION['userName'];

function printAntraege()
{
    global $userName;
    include_once ("../php/anfragenVerwalten.php");
    $anfragenArray = listAllRequestsProSchueler($userName);
    $i = 0;
    foreach ($anfragenArray as $anfrage )
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
            $div = "";
            $status = "In Bearbeitung";
            $statusBackEnde = "inbearbeitung";
            $komm = '';
            $kommentar = '';
            $onclickCheck = 'onclick="addToDelete(' . $i . ')"';
        }
        else if ( $temp == true )
        {
            $div = " disabledDiv";
            $status = "Best&auml;tigt";
            $statusBackEnde = "best&auml;tigt";

            if ( $lehrer == "" )
            {
                $bearbeitetVon = "";
            }
            else if ( $super == "" )
            {
                $bearbeitetVon = "";
            }
            else
            {
                $bearbeitetVon = "Gott?";
            }
            $komm = '<p class="summary smallFont"><strong>Bewertet von:</strong>' . $bearbeitetVon . '</p>';
            $tokenNeu = $anfrage['tokenAnzahlNeu'];
            $kommentar = '<div class="row smallFont"><div class="col-sm-12"><strong>Kommentar des Lehrers</strong><hr>' . $anfrage['kommentar'];

            if ( $tokenNeu != '' )
            {
                $kommentar = $kommentar . '<br><strong>Vom Lehrer ge&auml;nderte Tokenanzahl:</strong> ' . $tokenNeu;
            }
            $kommentar = $kommentar . '</div></div>';
            $onclickCheck = '';
        }
        else if ( $temp == false )
        {
            $div = " disabledDiv";
            $status = "Abgelehnt";
            $statusBackEnde = "abgelehnt";
            $komm = '';
            $kommentar = '';
            $onclickCheck = '';
        }

        $id = $anfrage['id'];
        // zum abfragen einer checkbox: isset($_POST['formWheelchair'] schauen ob vorhanden und dann value handeln
        echo '<tr data-status="' . $statusBackEnde . '">
                <td>
                    <div class="form-check'.$div.'">
                        <input type="checkbox" id="deleteCheckBox' . $i . '" name="deleteCheckBox" ' . $onclickCheck . ' value="' . $id . '">
                    </div>
                </td>
                <td class="clickable" data-toggle="collapse" data-target="#anfrage' . $i . '" aria-expanded="false" aria-controls="anfrage' . $i . '"></td>
                <td class="clickable" data-toggle="collapse" data-target="#anfrage' . $i . '" aria-expanded="false" aria-controls="anfrage' . $i . '">
                    <div class="media">
                        <div class="media-body">
                            <span class="media-meta pull-right">'. $day . ' ' . $mon .', ' . $year . ' ' . $zeit . '</span>
                            <h4 class="title"> Betreff: ' . $betreff . '
                                <span class="pull-right ' . $statusBackEnde . '">(' . $status . ')</span>
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

if ( isset($_GET['deleteAntraege']))
{
    include_once ("../php/anfragenVerwalten.php");
    $selected = $_POST['anzahlSelected'];
    $toDelete = array();
    for ( $i = 0; $i < $selected; $i++ )
    {
        $toDelete[$i] = $_POST['antrag' . $i];
    }

    foreach ($toDelete as $id )
    {
        deleteTokenRequest($id, $userName);
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
    $_SESSION['eventEintragung'] = NULL;
    header("Refresh:0; url=events.html");
}

if(isset($_GET['logout']))
{
    include_once("../php/userCheck.php");
    logout();
}
?>