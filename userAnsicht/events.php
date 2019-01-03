<?php

session_start();
$userName = $_SESSION['userName'];

function printEvents()
{
    global $userName;
    include_once ("../php/eventsVerwalten.php");
    $events = listAllEvents();
    /*
     * $out[$i]['name'] = $event_array['name'];
        $out[$i]['datum'] = $event_array['datum'];
        $out[$i]['superKuerzel'] = $event_array['superKuerzel'];
        $out[$i]['lKuerzel'] = $event_array['lKuerzel'];
        $out[$i]['aName'] = $event_array['aName'];
        $out[$i]['beschreibung'] = $event_array['beschreibung'];
     */
    $i = 0;
    // script ausgeben
    echo '<script type="application/javascript">
            function changeValues(name, beschreibung, tokenAnzahl, id) {
                var flag;
                if ( tokenAnzahl == -1)
                {
                    document.getElementById(("tokenAnzahlUnterKat"+id)).readOnly = false;
                    document.getElementById(("tokenAnzahlUnterKat"+id)).disabled = false;
                    flag = true;
                }
                else
                {
                    document.getElementById(("tokenAnzahlUnterKat"+id)).readOnly = true;
                    document.getElementById(("tokenAnzahlUnterKat"+id)).disabled = true;
                    flag = false;
                }
                // button oben
                document.getElementById(("katTyp"+id)).innerHTML = name;
                document.getElementById(("katTypBackend"+id)).value = name;
                // tokenAnzahl
                if ( flag == false)
                {
                    document.getElementById(("tokenAnzahlUnterKat"+id)).value = tokenAnzahl;
                    document.getElementById(("tokenAnzahlUnterKatBackend"+id)).value = tokenAnzahl;
                }
                else
                {
                    document.getElementById(("tokenAnzahlUnterKat"+id)).value = "";
                }
                // beschreibung
                document.getElementById(("katBeschreibung"+id)).innerHTML = beschreibung;
            }
            function submitEventForm(id) {
                var form = document.getElementById(("eventAntrag"+id));
                document.getElementById("tokenAnzahlUnterKatBackend").value = document.getElementById(("tokenAnzahlUnterKat"+id)).value;
                form.submit();
            }
        </script>';
    foreach ($events as $event)
    {
        // als datum YYYY-MM-DD
        $dateString = $event['datum'];
        $date = strtotime($dateString);
        $day = date("d", $date);
        $mon = date("M", $date);

        $name = $event['name'];
        $aName = $event['aName'];
        $bescheibung = $event['beschreibung'];

        if ( $date > time() )
        {
            $badge = '<span class="badge badge-primary">'.$day.'</span>';
        }
        else
        {
            $badge = '<span class="badge badge-secondary">'.$day.'</span>';
        }
        if ( isSchuelerInWildcard($name, $dateString, $aName, $userName))
        {
            $lock = "";
            $popover = 'data-toggle="modal" data-target="#popUp'.$i.'"';
        }
        else
        {
            $lock = "disabled";
            $popover = 'data-toggle="tooltip" data-placement="bottom"  data-html="true" title="Dieses Event ist mit einer Wildcard gesch&uuml;tzt. Sie k&ouml;nnen sich nicht einschreiben."';
        }
        echo '<div class="row row-striped"><div class="col-2 text-right"><h1 class="display-4">'.$badge.'</h1>
                    <h2>'.$mon.'</h2>
                </div>
                <div class="col-10 eventMedia">
                    <h4 class="text-uppercase"><strong>'.$name.'</strong></h4>
                    <ul class="list-inline">
                        <li class="list-inline-item"><i class="fa fa-clock-o" aria-hidden="true"></i> Awardtyp: '.$aName.'</li>
                    </ul>
                    <p>'.$bescheibung.'</p>
                </div>
                <div class="col-12 text-center">
                    <button type="button" class="btn btn-outline-primary adminEventButton abstand1 center-block" '.$popover.' '.$lock.'>Token beantragen</button>
                </div>';
        if ( $lock != 'disabled')
        {
            echo '<div class="modal fade" id="popUp'.$i.'" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <form id="eventAntrag'.$i.'" action="?requestTokenEvent=1" method="post">
                            <!-- inputs für die values -->
                            <input type="text" id="awardTypeBackend'.$i.'" name="awardTypeBackend" class="hiddenMeldung" value="'.$aName.'">
                            <input type="text" id="eventNameBackend'.$i.'" name="eventNameBackend" class="hiddenMeldung" value="'.$name.'">
                            <input type="text" id="eventDateBackend'.$i.'" name="eventDateBackend" class="hiddenMeldung" value="'.$dateString.'">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <div class="col-sm-6">
                                        <h5 class="modal-title">Antrag zu dem Event stellen: '.$name.'</h5>
                                    </div>
                                    <div class="col-sm-6">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <hr class="col-xs-12">
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-sm-8">
                                            <div class="btn-group">
                                                <button type="button" id="katTyp'.$i.'" class="btn btn-primary dropdown-toggle btn-outline-primary"
                                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Kategorie ausw&auml;hlen</button>
                                                <input type="text" id="katTypBackend'.$i.'" name="katTypBackend" class="hiddenMeldung" value="">
                                                <div class="dropdown-menu">
                                                    <a class="dropdown-item" href="#" onclick="changeValues(\'Andere Kategorie\', \'Bitte genau beschreiben\', -1, '.$i.')">Andere Kategorie</a>
                                                    '.printUnter($event, $i).'
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="input-group mb-3">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text bg-primary text-light">Token</span>
                                                </div>
                                                <input type="number" min="1" id="tokenAnzahlUnterKat'.$i.'" name="tokenAnzahlUnterKat" class="form-control tokens"
                                                    aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default" disabled readonly value="">
                                                <input type="number" id="tokenAnzahlUnterKatBackend'.$i.'" name="tokenAnzahlUnterKatBackend" class="hiddenMeldung" value="">

                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label>Kategroiebeschreibung:</label>
                                                <textarea class="form-control" rows="3" minlength="1" id="katBeschreibung'.$i.'" disabled readonly></textarea>
                                            </div>
                                        </div>  
                                    </div>
                                    <hr class="col-xs-12">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="input-group mb-3">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text bg-primary text-light">Betreff</span>
                                                </div>
                                                <input type="text" id="betreff'.$i.'" name="betreff" class="form-control" minlength="1"
                                                    aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label>Beschreibung:</label>
                                                <textarea class="form-control" rows="3" minlength="1" id="beschreibung'.$i.'" name="beschreibung" required></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Schlie&szlig;en</button>
                                    <button type="button" class="btn btn-outline-primary" onclick="submitEventForm('.$i.')">Antrag stellen</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>';
        }
        echo '</div>';
        $i++;
    }
    if ( $use = $_SESSION['eventEintragung'] != NULL )
    {

    }
}

function printUnter( $event, $i )
{
    $out = '';
    $unterKat = getUnterKatProEvent($event['name'], $event['datum'],$event['aName']);
    foreach ($unterKat as $kat )
    {
        $name = $kat['name'];
        $beschreibung = $kat['beschreibung'];
        $tokenAnzahl = $kat['tokenAnzahl'];
        $out = $out . '<a class="dropdown-item" href="#" onclick="changeValues(\''.$name.'\', \''.$beschreibung.'\', '.$tokenAnzahl.', '.$i.')">' . $name . '</a>';
    }

    return $out;
}

function printMeldung()
{
    if ( key_exists('eventRequestResult', $_SESSION)) {
        $result = $_SESSION['eventRequestResult'];
        echo '<div id="antragErrorMsg" class="bottomAlert">';
        if ($result) {
            //echo '<script language="JavaScript" type="text/javascript">errorMsg = document.getElementById("antragErrorMsg");errorMsg.innerHTML = \'<div class="alert alert-success alert-dismissible fade show abstand1" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>Ihr Antrag wurde erfolgreich gestellt!</div>\';</script>';
            echo '<div class="alert alert-success alert-dismissible fade show abstand1" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>Ihr Antrag wurde erfolgreich gestellt!</div>';
        } else {
            echo '<div class="alert alert-danger alert-dismissible fade show abstand1" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>Ein Fehler ist aufgetreten! Bitte versuchen Sie es erneut.</div>';
        }
        echo '</div>';
    }
}

if (isset($_GET['requestTokenEvent']))
{
    include_once ("../php/anfragenVerwalten.php");
    $awardTyp = $_POST['awardTypeBackend'];
    $eventName = $_POST['eventNameBackend'];
    $eventDate = $_POST['eventDateBackend'];
    $tokenAnzahl = $_POST['tokenAnzahlUnterKatBackend'];
    $betreff = $_POST['betreff'];
    $beschreibung = $_POST['beschreibung'];
    $unterKatName = $_POST['katTypBackend'];
    $result = requestTokenExt($awardTyp, $eventName, $eventDate, $unterKatName, $userName, $tokenAnzahl, $beschreibung, $betreff);
    $_SESSION['eventRequestResult'] = $result;
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

if(isset($_GET['logout']))
{
    include_once("../php/userCheck.php");
    logout();
}
?>