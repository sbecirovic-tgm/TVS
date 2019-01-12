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
        $temp = $anfrage['wirdBewilligt'];

        $kommentar = $anfrage['kommentar'];
        if ( $temp == true )
        {
            $status = "Bestätigt";
            $statusBackEnde = "bestätigt";
            $komm = '<p class="summary">Bewertet von: </p>';
        }
        else if ( $temp == false )
        {
            $status = "Abgelehnt";
            $statusBackEnde = "abgelehnt";
        }
        else
        {
            $status = "In Bearbeitung";
            $statusBackEnde = "inbearbeitung";
        }
        echo '<tr data-status="' . $statusBackEnde . '">
                                        <td>
                                            <div class="form-check">
                                                <input type="checkbox" id="editCheckBox">
                                            </div>
                                        </td>
                                        <td class="clickable" data-toggle="collapse" data-target="#anfrage1" aria-expanded="false" aria-controls="anfrage1"></td>
                                        <td class="clickable" data-toggle="collapse" data-target="#anfrage1" aria-expanded="false" aria-controls="anfrage1">
                                            <div class="media">
                                                <div class="media-body">
                                                    <span class="media-meta pull-right">'. $day . ' ' . $mon .', ' . $year . '</span>
                                                    <h4 class="title"> Betreff: ' . $betreff . '
                                                        <span class="pull-right ' . $statusBackEnde . '">(' . $status . ')</span>
                                                    </h4>
                                                    <p class="summary">Kommentar des Lehreres: </p>
                                                </div>
                                            </div>
                                            <div id="anfrage1" class="collapse">
                                                Restliche info
                                            </div>
                                        </td>
                                    </tr>';

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