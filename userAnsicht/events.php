<?php

session_start();
$userName = $_SESSION['userName'];

function printEvents()
{
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
    foreach ($events as $event)
    {
        // als datum YYYY-MM-DD
        $date = strtotime($event['datum']);
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
                    <button type="button" class="btn btn-outline-primary adminEventButton abstand1 center-block" data-toggle="modal" data-target="#exampleModal">Einschreiben</button>
                </div>
                <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <form id="eventAntrag" action="?requestTokenEvent=1" method="post">
                        <!-- inputs für die values -->
                        <input type="text" id="awardTypeBackend" name="awardTypeBackend" class="hiddenMeldung" value="'.$aName.'">
                        <input type="text" id="eventNameBackend" name="eventNameBackend" class="hiddenMeldung" value="'.$name.'">
                        <input type="text" id="eventDateBackend" name="eventDateBackend" class="hiddenMeldung" value="'.$date.'">
                            <div class="modal-content">
                                <script type="application/javascript">
                                    function changeValues(name, beschreibung, tokenAnzahl) {
                                        var flag;
                                        if ( tokenAnzahl == -1)
                                        {
                                            document.getElementById("tokenAnzahlUnterKat").readOnly = false;
                                            document.getElementById("tokenAnzahlUnterKat").disabled = false;
                                            flag = true;
                                        }
                                        else
                                        {
                                            document.getElementById("tokenAnzahlUnterKat").readOnly = true;
                                            document.getElementById("tokenAnzahlUnterKat").disabled = true;
                                            flag = false;
                                        }
                                        // button oben
                                        document.getElementById("katTyp").innerHTML = name;
                                        document.getElementById("katTypBackend").value = name;
                                        // tokenAnzahl
                                        if ( flag == false)
                                        {
                                            document.getElementById("tokenAnzahlUnterKat").value = tokenAnzahl;
                                        }
                                        else 
                                        {
                                            document.getElementById("tokenAnzahlUnterKat").value = "";  
                                        }
                                        // beschreibung
                                        document.getElementById("katBeschreibung").innerHTML = beschreibung;
                                    }
                                    function submitEventForm() {
                                        var form = document.getElementById("eventAntrag");
                                        form.submit();
                                    }
                                </script>
                                <div class="modal-header">
                                    <div class="col-sm-6">
                                        <h5 class="modal-title" id="exampleModalLabel">Event-Antrag stellen</h5>
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
                                                <button type="button" id="katTyp" class="btn btn-primary dropdown-toggle btn-outline-primary"
                                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Kategorie ausw&auml;hlen</button>
                                                <input type="text" id="katTypBackend" name="katTypBackend" class="hiddenMeldung" value="">
                                                <div class="dropdown-menu">
                                                    <a class="dropdown-item" href="#" onclick="changeValues(\'Andere Kategorie\', \'Bitte genau beschreiben\', -1)">Andere Kategorie</a>
                                                    '.printUnter($event).'
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="input-group mb-3">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text bg-primary text-light">Token</span>
                                                </div>
                                                <input type="number" min="1" id="tokenAnzahlUnterKat" name="tokenAnzahlUnterKat" class="form-control tokens"
                                                    aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default" disabled readonly value="">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label>Kategroiebeschreibung:</label>
                                                <textarea class="form-control" rows="3" minlength="1" id="katBeschreibung" disabled readonly></textarea>
                                            </div>
                                        </div>  
                                    </div>
                                    <hr class="col-xs-12">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="input-group mb-3">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text bg-primary text-light" id="inputGroup-sizing-default">Betreff</span>
                                                </div>
                                                <input type="text" id="betreff" name="betreff" class="form-control" minlength="1"
                                                    aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label>Beschreibung:</label>
                                                <textarea class="form-control" rows="3" minlength="1" id="beschreibung" name="beschreibung" required></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <div class="row">
                                        <div id="antragErrorMsg"></div>
                                        ' . printMeldung() . '
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Schlie&szlig;en</button>
                                            <button type="button" class="btn btn-outline-primary" onclick="submitEventForm()">Antrag stellen</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>';
    }
}

function printUnter( $event )
{
    $out = '';
    $unterKat = getUnterKatProEvent($event['name'], $event['datum'],$event['aName']);
    foreach ($unterKat as $kat )
    {
        $name = $kat['name'];
        $beschreibung = $kat['beschreibung'];
        $tokenAnzahl = $kat['tokenAnzahl'];
        $out = $out . '<a class="dropdown-item" href="#" onclick="changeValues(\''.$name.'\', \''.$beschreibung.'\', '.$tokenAnzahl.')">' . $name . '</a>';
    }

    return $out;
}

function printMeldung()
{
    $result = $_SESSION['eventRequestResult'];
    if ($result)
    {
        return '<script language="JavaScript" type="text/javascript">errorMsg = document.getElementById("antragErrorMsg");errorMsg.innerHTML = \'<div class="alert alert-success alert-dismissible fade show abstand1" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>Ihr Antrag wurde erfolgreich gestellt!</div>\';</script>';
    }
    else
    {
        return '<script language="JavaScript" type="text/javascript">errorMsg = document.getElementById("antragErrorMsg");errorMsg.innerHTML = \'<div class="alert alert-danger alert-dismissible fade show abstand1" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>Ein Fehler ist aufgetreten! Bitte versuchen Sie es erneut.</div>\';</script>';
    }
}

if (isset($_GET['requestTokenEvent']))
{
    include_once ("../php/anfragenVerwalten.php");
    $awardTyp = $_POST['awardTypeBackend'];
    $eventName = $_POST['eventNameBackend'];
    $eventDate = $_POST['eventDateBackend'];

    $tokenAnzahl = $_POST['tokenAnzahlUnterKat'];
    $betreff = $_POST['betreff'];
    $beschreibung = $_POST['beschreibung'];
    $unterKatName = $_POST['katTypBackend'];

    $result = requestTokenExt($awardTyp, $eventName, $eventDate, $unterKatName, $userName, $tokenAnzahl, $beschreibung, $betreff);
    $_SESSION['eventRequestResult'] = $result;
    echo $result;
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