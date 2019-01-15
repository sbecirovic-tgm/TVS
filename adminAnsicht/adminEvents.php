<?php
/**
 * Created by PhpStorm.
 * User: Mir
 * Date: 03.01.2019
 * Time: 14:26
 */
session_start();
$db = mysqli_connect('localhost', 'tokenverwaltung', '1234', 'tvs_datenbank');

$userName = $_SESSION['userName'];

function printAwardDropDown ()
{
    global $db;
    $out = '';
    $sqlC = 'select name from award';
    $award = mysqli_query($db, $sqlC);
    for (; $award_array = mysqli_fetch_assoc($award); ) {
        $name = $award_array['name'];
        $out = $out . '<a class="dropdown-item" style="cursor: pointer;" onclick="setAwardButton(\'' . $name . '\')">' . $name . '</a>';
    }
    return $out;
}

function printEvents()
{
    global $userName;
    include_once ("../php/eventsVerwalten.php");
    include_once ("../php/userCheck.php");

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
    echo '<script>
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
    $use = $_SESSION['eventVerwaltung'];

    $found = -1;

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
        $_SESSION['eventVerwalten'.$i] = $event;

        if ( $use != NULL )
        {
            $nameFind = $use['eName'];
            $aNameFind = $use['aName'];
            $dateStringFind = $use['eDatum'];


            if ( $name == $nameFind and $aNameFind == $aName and $dateStringFind == $dateString )
            {
                $found = $i;
            }
        }

        if ( $date > time() )
        {
            $badge = '<span class="badge badge-primary">'.$day.'</span>';
        }
        else
        {
            $badge = '<span class="badge badge-secondary">'.$day.'</span>';
        }

        if ( isLehrerBerechtigt($aName, $userName) || checkIfUserIsSuperUser($userName) )
        {
            $button = 'onclick="setEventInForm( \''.$name.'\', \''.$dateString.'\', \''.$aName.'\' )';
        }
        else
        {
            $button = 'disabled data-toggle="tooltip" data-placement="bottom"  data-html="true" title="Sie haben keine Berechtigung dieses Event zu verwalten."';
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
                    <button id="eventVerwalten'.$i.'" type="button" class="btn btn-outline-primary adminEventButton abstand1 center-block" ' . $button . '">Event verwalten</button>
                </div>';
        echo '</div>';
        $i++;
    }
}


if ( isset($_GET['setSessionToEvent']))
{
    if ( $_SESSION['eventVerwaltung'] != null )
    {
        stop();
    }
    $temp['eName'] = $_POST['name'];
    $temp['eDatum'] = $_POST['datum'];
    $temp['aName'] = $_POST['aName'];
    $_SESSION['eventVerwaltung'] = $temp;
    unset($temp);
}

function printVerwalten()
{
    include_once ("../php/eventsVerwalten.php");
    $verw = $_SESSION['eventVerwaltung'];
    if ( $verw == NULL )
    {
        echo '<div class="row">
            <div class="col-sm-12">
                <h1>Events</h1>
                <hr>
            </div>
            <div class="col-sm-12 tokenAnfragen">
                <div class="card">
                    <div class="input-group abstand1">
                        <div class="col-sm-6">
                            <div class="row">
                                <div class="col-sm-12">
                                    <h4>Kategorien</h4>
                                </div>
                            </div>
                            <hr class="col-xs-12">
                            <div class="row">
                                <form id="katForm" action="?deleteKat=1" method="post">
                                    <input type="number" id="anzahlSelected" name="anzahlSelected" class="hiddenMeldung" value="0">
                                    <div id="katListBackend">
                                    </div>
                                </form>
                                <div class="col-sm-12">
                                    <ol id="kategorienListe" class="list-group">' . printKatList() . '
                                    </ol>' . printKatError() . '
                                </div>
                                <div class="col-sm-6">
                                    <button type="button" class="btn btn-outline-primary dashboardButtons abstand1" data-toggle="modal" data-target="#popUpKategorie">Neue Kategorie hinzuf&uuml;gen</button>
                                </div>
                                <div class="col-sm-6">
                                    <button type="button" class="btn btn-outline-primary dashboardButtons abstand1" onclick="submitForm(\'katForm\')" >Ausgew&auml;hle Kategorien l&ouml;schen</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <form id="schuelerForm" action="?deleteKuerzel=1" method="post">
                                <input type="number" id="anzahlSelectedSch" name="anzahlSelected" class="hiddenMeldung" value="0">
                                <div id="schuelerListBackend">
                                </div>
                            </form>' . printWildcard() . printError() . '
                        </div>
                    </div>
                    <hr>
                    <div class="card-body">
                        <div class="row ">
                            <div class="col-sm-12">
                                <h5>Event Daten</h5>
                            </div>
                        </div>
                        <form id="addEvent" action="?addEvent=1" method="post">
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-primary text-light adminEventText" id="inputGroup-sizing-default">Eventname</span>
                                        </div>
                                        <input type="text" id="eName" name="name" class="form-control" minlength="1"
                                            aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default" required>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-primary text-light adminEventText">Datum</span>
                                        </div>
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar">
                                            </i>
                                        </div>
                                        <input class="form-control" id="date" name="date" placeholder="DD-MM-YYYY" type="text" required>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div>
                                        <script>
                                            function setAwardButton(value) {
                                                document.getElementById("awardType").innerHTML = value;
                                                document.getElementById("awardTypeBackend").value = value;
                                            }
                                        </script>
                                        <button type="button" name="awardType" id="awardType" class="btn dropdown-toggle btn-primary"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" onclick="">Award ausw&auml;hlen</button>
                                        <input type="text" min="0" id="awardTypeBackend" name="awardTypeBackend" class="hiddenMeldung" value="">

                                        <div class="dropdown-menu">' . printAwardDropDown() . '
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-primary text-light adminEventText">Beschreibung</span>
                                </div>
                                <textarea class="form-control" name="beschreibung" id="beschreibung" aria-label="With textarea" required></textarea>
                            </div>
                        </form>
                        <hr>
                        <div class="input-group">
                            <div class="row">

                            </div>
                        </div>
                        <div class="col-sm-12 text-center">
                            <button type="button" class="btn btn-outline-primary dashboardButtons abstand1" onclick="submitForm(\'addEvent\')">Event hinzuf&uuml;gen</button>
                        </div>

                        <!-- Jetzt kommen die popovers -->
                        <div class="modal fade" id="popUpKategorie" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <form id="addKat" action="?addKat=1" method="post">
                                        <div class="modal-content">
                                        <div class="modal-header">
                                            <div class="col-sm-6">
                                                <h5 class="modal-title">Kategorie zum Event hinzuf&uuml;gen</h5>
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
                                                <div class="col-sm-12">
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text bg-primary text-light">Name</span>
                                                        </div>
                                                        <input type="text" id="name" name="name" class="form-control" minlength="1"
                                                               aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default" required>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-sm-8">
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text bg-primary text-light">Vorgegebene Tokenanzahl</span>
                                                        </div>
                                                        <input type="number" min="1" id="tokenAnzahlUnterKat" name="tokenAnzahlUnterKat" class="form-control tokens"
                                                               aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default" value="" required>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <div class="form-group">
                                                        <label>Kategroiebeschreibung:</label>
                                                        <textarea class="form-control" rows="3" minlength="1" id="katBeschreibung" name="katBeschreibung" required></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Schlie&szlig;en</button>
                                            <button type="button" class="btn btn-outline-primary" onclick="submitForm(\'addKat\')">Hinzuf&uuml;gen</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <!-- ende der popups -->
                    </div>
                </div>
            </div>
        </div>';
    }
    else
    {
        $wID = getAttrFromEvent('wID', $verw['eName'], $verw['eDatum'], $verw['aName']);
        if ( !key_exists('eventWildTempBackUp', $_SESSION) && $wID != null)
        {
            $_SESSION['wildcardStatus'] = true;
        }
        if ( $_SESSION['wildcardStatus'])
        {
            if ($wID != null) {
                if ( !key_exists('eventWildTemp', $_SESSION))
                {
                    $_SESSION['eventWildTemp'] = getAllZuordnungToID($wID);
                }
                $_SESSION['eventWildTempBackUp'] = getAllZuordnungToID($wID);
            }
            else
            {
                $_SESSION['eventWildTempBackUp'] = array();
            }
        }
        else {
            if ($wID != null) {
                if ( !key_exists('eventWildTemp', $_SESSION))
                {
                    $_SESSION['eventWildTemp'] = getAllZuordnungToID($wID);
                }
                $_SESSION['eventWildTempBackUp'] = getAllZuordnungToID($wID);
            } else {
                $_SESSION['eventWildTempBackUp'] = array();
                unset($_SESSION['wildcardStatus']); //$_SESSION['wildcardStatus'] = false;
                unset($_SESSION['eventWildTemp']);
            }
        }
        $kats = getAllUnterkatToEvent($verw['eName'], $verw['eDatum'], $verw['aName']);
        if ( $kats != null )
        {
            if ( !key_exists('kategorienListe', $_SESSION))
            {
                $_SESSION['kategorienListe'] = $kats;
            }
            $_SESSION['kategorienListeBakUp'] = $kats;
        }
        else
        {
            $_SESSION['kategorienListeBakUp'] = array();
        }
        $event = getEvent($verw['eName'], $verw['eDatum'], $verw['aName']);
        $name = $event['name'];
        $datum = $event['datum'];
        $aName = $event['aName'];
        $beschreibung = $event['beschreibung'];
        /*
        $out['name'] = $event_array['name'];
        $out['datum'] = $event_array['datum'];
        $out['superKuerzel'] = $event_array['superKuerzel'];
        $out['lKuerzel'] = $event_array['lKuerzel'];
        $out['aName'] = $event_array['aName'];
        $out['beschreibung'] = $event_array['beschreibung'];
        */

        echo '<div class="row">
            <div class="col-sm-12">
                <h1>Events</h1>
                <hr>
            </div>
            <div class="col-sm-12 tokenAnfragen">
                <div class="card">
                    <div class="input-group abstand1">
                        <div class="col-sm-6">
                            <div class="row ">
                                <div class="col-sm-12">
                                    <h4>Kategorien</h4>
                                </div>
                            </div>
                            <hr class="col-xs-12">
                            <div class="row">
                                <form id="katForm" action="?deleteKat=1" method="post">
                                    <input type="number" id="anzahlSelected" name="anzahlSelected" class="hiddenMeldung" value="0">
                                    <div id="katListBackend">
                                    </div>
                                </form>
                                <div class="col-sm-12">
                                    <ol id="kategorienListe" class="list-group">' . printKatList() . '
                                    </ol>' . printKatError() . '
                                </div>
                                <div class="col-sm-6">
                                    <button type="button" class="btn btn-outline-primary dashboardButtons abstand1" data-toggle="modal" data-target="#popUpKategorie">Neue Kategorie hinzuf&uuml;gen</button>
                                </div>
                                <div class="col-sm-6">
                                    <button type="button" class="btn btn-outline-primary dashboardButtons abstand1" onclick="submitForm(\'katForm\')" >Ausgew&auml;hle Kategorien l&ouml;schen</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <form id="schuelerForm" action="?deleteKuerzel=1" method="post">
                                <input type="number" id="anzahlSelectedSch" name="anzahlSelected" class="hiddenMeldung" value="0">
                                <div id="schuelerListBackend">
                                </div>
                            </form>' . printWildcard() . '' . printError() . '
                        </div>
                    </div>
                    <hr>
                    <div class="card-body">
                        <div class="row ">
                            <div class="col-sm-12">
                                <h5>Event Daten</h5>
                            </div>
                        </div>
                        <form id="updateEvent" action="?updateEvent=1" method="post">
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-primary text-light adminEventText" id="inputGroup-sizing-default">Eventname</span>
                                        </div>
                                        <input type="text" id="eName" name="name" class="form-control" minlength="1"
                                            aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default" required value="'.$name.'">
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-primary text-light adminEventText">Datum</span>
                                        </div>
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar">
                                            </i>
                                        </div>
                                        <input class="form-control" id="date" name="date" placeholder="DD-MM-YYYY" type="text" required value="'.$datum.'">
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div>
                                        <script>
                                            function setAwardButton(value) {
                                                document.getElementById("awardType").innerHTML = value;
                                                document.getElementById("awardTypeBackend").value = value;
                                            }
                                        </script>
                                        <button type="button" name="awardType" id="awardType" class="btn dropdown-toggle btn-primary"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" onclick="">'.$aName.'</button>
                                        <input type="text" min="0" id="awardTypeBackend" name="awardTypeBackend" class="hiddenMeldung" value="'.$aName.'">

                                        <div class="dropdown-menu">' . printAwardDropDown() . '
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-primary text-light adminEventText">Beschreibung</span>
                                </div>
                                <textarea class="form-control" name="beschreibung" id="beschreibung" aria-label="With textarea" required>'.$beschreibung.'</textarea>
                            </div>
                        </form>
                        <hr>
                        <form id="deleteEvent" action="?deleteEvent=1" method="post"></form>
                        <form id="stop" action="?stop=1" method="post"></form>
                        <div class="col-sm-12 text-center">
                            <button type="button" class="btn btn-outline-primary dashboardButtons abstand1" onclick="submitForm(\'deleteEvent\')">Event l&ouml;schen</button>
                            <button type="button" class="btn btn-outline-secondary dashboardButtons abstand1" onclick="submitForm(\'stop\')">Abbrechen</button>
                            <button type="button" class="btn btn-outline-primary dashboardButtons abstand1" onclick="submitForm(\'updateEvent\')">Event speichern</button>
                        </div>

                        <!-- Jetzt kommen die popovers -->
                        <div class="modal fade" id="popUpKategorie" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <form id="addKat" action="?addKat=1" method="post">
                                        <div class="modal-content">
                                        <div class="modal-header">
                                            <div class="col-sm-6">
                                                <h5 class="modal-title">Kategorie zum Event hinzuf&uuml;gen</h5>
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
                                                <div class="col-sm-12">
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text bg-primary text-light">Name</span>
                                                        </div>
                                                        <input type="text" id="name" name="name" class="form-control" minlength="1"
                                                               aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default" required>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-sm-8">
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text bg-primary text-light">Vorgegebene Tokenanzahl</span>
                                                        </div>
                                                        <input type="number" min="1" id="tokenAnzahlUnterKat" name="tokenAnzahlUnterKat" class="form-control tokens"
                                                               aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default" value="" required>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <div class="form-group">
                                                        <label>Kategroiebeschreibung:</label>
                                                        <textarea class="form-control" rows="3" minlength="1" id="katBeschreibung" name="katBeschreibung" required></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Schlie&szlig;en</button>
                                            <button type="button" class="btn btn-outline-primary" onclick="submitForm(\'addKat\')">Hinzuf&uuml;gen</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <!-- ende der popups -->
                    </div>
                </div>
            </div>
        </div>';
    }
}

function printKatList()
{
    $out = '';
    if ( key_exists('kategorienListe', $_SESSION))
    {
        $list = $_SESSION['kategorienListe'];
        foreach ( $list as $elm )
        {
            $name = $elm['name'];
            $tokenAnzahl = $elm['tokenAnzahl'];
            $beschreibung = $elm['beschreibung'];
            $out = $out . '<li id="'.$name.'" class="list-group-item" style="cursor: pointer;" onclick="markElm(\''.$name.'\', '.$tokenAnzahl.', \''.$beschreibung.'\')"><strong>'.$name.'</strong> ('.$tokenAnzahl.' Token)</li>';
        }
    }
    else
    {
        $out = '<li id="startValueKat" class="list-group-item"><strong>Noch keine Eintr&auml;ge</strong></li>';
    }

    return $out;
}

function printWildcard()
{
    // false: deaktiviert
    // true: aktiviert
    if ( key_exists('wildcardStatus', $_SESSION))
    {
        $status = $_SESSION['wildcardStatus'];
        if ( $status )
        {
            return '<div class="row">
                <div class="col-sm-8">
                    <h4 data-toggle="tooltip"  data-placement="bottom" title="Setzen wer eine Anfrage zu diesem Event stellen kann.">Zug&auml;nglichkeit</h4>
                </div>
                <div class="col-sm-4">
                    <form id="changeWildcard" action="?changeWildcard=1" method="post">
                        <input type="number" id="wildcardActBackend" name="wildcardActBackend" class="hiddenMeldung" value="1">
                    </form>
                    <div class="col-sm-12 text-right">
                        <button id="wildcardButton" type="button" class="btn btn-outline-primary" onclick="submitForm(\'changeWildcard\')">Deaktivieren</button>
                    </div>
                </div>
            </div>
            <hr>
            <div id="wildcardDiv" class="col-sm-12">
                <form id="addKuerzel" class="input-group mb-3" action="?addKuerzel=1" method="post">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-primary text-light">K&uuml;rzel des Sch&uuml;lers</span>
                    </div>

                    <input type="text" id="schuelerKuerzel" name="schuelerKuerzel" class="form-control" minlength="1"
                           aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default">
                    <div class="input-group-append">
                        <button type="button" class="btn btn-outline-primary" onclick="submitForm(\'addKuerzel\')">Hinzuf&uuml;gen</button>
                    </div>
                </form>
                <ol id="schuelerList" class="list-group">' . printSchuelerList() . '
                </ol>
                <div class="col-sm-12 text-right">
                    <button type="button" class="btn btn-outline-primary dashboardButtons abstand1" onclick="submitForm(\'schuelerForm\')" >Ausgew&auml;hle Sch&uuml;ler l&ouml;schen</button>
                </div>
            </div>';
        }
        else
        {
            return '<div class="row">
                <div class="col-sm-8">
                    <h4 data-toggle="tooltip"  data-placement="bottom" title="Setzen wer eine Anfrage zu diesem Event stellen kann.">Zug&auml;nglichkeit</h4>
                </div>
                <div class="col-sm-4">
                    <form id="changeWildcard" action="?changeWildcard=1" method="post">
                        <input type="number" id="wildcardActBackend" name="wildcardActBackend" class="hiddenMeldung" value="0">
                    </form>
                    <div class="col-sm-12 text-right">
                        <button id="wildcardButton" type="button" class="btn btn-outline-primary" onclick="submitForm(\'changeWildcard\')">Aktivieren</button>
                    </div>
                </div>
            </div>
            <hr>
            <div id="wildcardDiv" class="col-sm-12 disabledDiv">
                <form id="addKuerzel" class="input-group mb-3" action="?addKuerzel=1" method="post">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-primary text-light">K&uuml;rzel des Sch&uuml;lers</span>
                    </div>

                    <input type="text" id="schuelerKuerzel" name="schuelerKuerzel" class="form-control" minlength="1"
                           aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default">
                    <div class="input-group-append">
                        <button type="button" class="btn btn-outline-primary" onclick="submitForm(\'addKuerzel\')">Hinzuf&uuml;gen</button>
                    </div>
                </form>
                <ol id="schuelerList" class="list-group">' . printSchuelerList() . '
                </ol>
                <div class="col-sm-12 text-right">
                    <button type="button" class="btn btn-outline-primary dashboardButtons abstand1" onclick="submitForm(\'schuelerForm\')" >Ausgew&auml;hle Sch&uuml;ler l&ouml;schen</button>
                </div>
            </div>';
        }
    }
    else
    {
        $_SESSION['wildcardStatus'] = false;
        return '<div class="row">
                <div class="col-sm-8">
                    <h4 data-toggle="tooltip"  data-placement="bottom" title="Setzen wer eine Anfrage zu diesem Event stellen kann.">Zug&auml;nglichkeit</h4>
                </div>
                <div class="col-sm-4">
                    <form id="changeWildcard" action="?changeWildcard=1" method="post">
                        <input type="number" id="wildcardActBackend" name="wildcardActBackend" class="hiddenMeldung" value="0">
                    </form>
                    <div class="col-sm-12 text-right">
                        <button id="wildcardButton" type="button" class="btn btn-outline-primary" onclick="submitForm(\'changeWildcard\')">Aktivieren</button>
                    </div>
                </div>
            </div>
            <hr>
            <div id="wildcardDiv" class="col-sm-12 disabledDiv">
                <form id="addKuerzel" class="input-group mb-3" action="?addKuerzel=1" method="post">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-primary text-light">K&uuml;rzel des Sch&uuml;lers</span>
                    </div>

                    <input type="text" id="schuelerKuerzel" name="schuelerKuerzel" class="form-control" minlength="1"
                           aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default">
                    <div class="input-group-append">
                        <button type="button" class="btn btn-outline-primary" onclick="submitForm(\'addKuerzel\')">Hinzuf&uuml;gen</button>
                    </div>
                </form>
                <ol id="schuelerList" class="list-group">' . printSchuelerList() . '
                </ol>
                <div class="col-sm-12 text-right">
                    <button type="button" class="btn btn-outline-primary dashboardButtons abstand1" onclick="submitForm(\'schuelerForm\')" >Ausgew&auml;hle Sch&uuml;ler l&ouml;schen</button>
                </div>
            </div>';
    }
}

function printSchuelerList()
{
    include_once ("../php/userCheck.php");
    if ( key_exists('eventWildTemp', $_SESSION))
    {
        $list = $_SESSION['eventWildTemp'];
        $out = '';
        foreach ( $list as $elm )
        {
            $name = getAllSchuelerNamesToKurzel()[$elm];
            $out = $out . '<li id="'.$elm.'" class="list-group-item" style="cursor: pointer;" onclick="markSchueler(\''.$elm.'\')">'.$name.'</li>';
        }
        return $out;
    }
    else
    {
        return '<li id="startValueSch" class="list-group-item">Noch keine Eintr&auml;ge</li>';
    }
}

if (isset($_GET['changeWildcard']))
{
    $status = $_POST['wildcardActBackend'];
    if ( $status == 0 )
    {
        // auf das gegenteil setzten
        $_SESSION['wildcardStatus'] = true;
    }
    else
    {
        $_SESSION['wildcardStatus'] = false;
    }

    unset($status);
}

function stop()
{
    include_once ("../php/eventsVerwalten.php");

    unset($_SESSION['eventWildTemp']);
    unset($_SESSION['kategorienListe']);
    unset($_SESSION['addKatError']);
    unset($_SESSION['wildCardError']);

    // altes wiederherstellen
    $temp = $_SESSION['eventVerwaltung'];

    $kuerzelArray = $_SESSION['eventWildTempBackUp'];
    $wID = getAttrFromEvent("wID", $temp['eName'], $temp['eDatum'], $temp['aName']);

    foreach ($kuerzelArray as $kuerzel) {
        addToWildCard($wID, $kuerzel);
    }

    $katArray = $_SESSION['kategorienListeBakUp'];
    foreach ($katArray as $kat) {
        addUnterkategorie($kat['name'], $temp['eName'], $temp['eDatum'], $temp['aName'], $kat['tokenAnzahl'], $kat['beschreibung']);
    }

    unset($_SESSION['kategorienListeBakUp']);
    unset($_SESSION['eventWildTempBackUp']);
    $_SESSION['eventVerwaltung'] = null;
    unset($temp);
}

if ( isset($_GET['stop']))
{
    stop();
}

if (isset($_GET['addKat']))
{
    include_once ("../php/eventsVerwalten.php");
    $name = $_POST['name'];
    $tokenAnzahl = $_POST['tokenAnzahlUnterKat'];
    $beschreibung = $_POST['katBeschreibung'];
    if ( strlen($name) == 0 || strlen($tokenAnzahl) == 0 || strlen($beschreibung) == 0)
    {
        $_SESSION['addKatError'] = true;
    }
    else
    {
        if ( key_exists('kategorienListe', $_SESSION))
        {
            $list = $_SESSION['kategorienListe'];
            $num = count($list);
            $list[$num]['name'] = $name;
            $list[$num]['tokenAnzahl'] = $tokenAnzahl;
            $list[$num]['beschreibung'] = $beschreibung;
            $_SESSION['kategorienListe'] = $list;
        }
        else
        {
            $list[0]['name'] = $name;
            $list[0]['tokenAnzahl'] = $tokenAnzahl;
            $list[0]['beschreibung'] = $beschreibung;
            $_SESSION['kategorienListe'] = $list;
        }

        $temp = $_SESSION['eventVerwaltung'];
        if ( $temp != null )
        {
            addUnterkategorie($name, $temp['eName'], $temp['aName'], $temp['eDatum'], $tokenAnzahl, $beschreibung);
        }

        unset($temp);
        unset($list);
        unset($num);
    }
    unset($name);
    unset($tokenAnzahl);
    unset($beschreibung);
}

if ( isset($_GET['deleteKat']))
{
    include_once ("../php/eventsVerwalten.php");
    if ( key_exists('kategorienListe', $_SESSION))
    {
        $selected = $_POST['anzahlSelected'];

        for ( $i = 0; $i < $selected; $i++ )
        {
            $search['name'] = $_POST['toDeleteName'.$i];
            $search['tokenAnzahl'] = $_POST['toDeleteToken'.$i];
            $search['beschreibung'] = $_POST['toDeleteBesch'.$i];

            $temp = $_SESSION['eventVerwaltung'];
            if ( $temp != null )
            {
                deleteUnterkategorie($search['name'], $temp['eName'], $temp['aName'], $temp['eDatum']);
            }
            $pos = array_search($search, $_SESSION['kategorienListe']);
            unset($_SESSION['kategorienListe'][$pos]);
        }
        if ( count($_SESSION['kategorienListe']) == 0 )
        {
            unset($_SESSION['kategorienListe']);
        }
        unset($temp);
        unset($search);
        unset($selected);
        unset($pos);
    }
}

// error popup
function printKatError()
{
    if ( key_exists('addKatError', $_SESSION))
    {
        $error = $_SESSION['addKatError'];
        if ( $error )
        {
            return '<script>function f() {var form = document.getElementById("alertKatDismiss"); form.submit();}</script><div class="alert alert-danger alert-dismissible fade show abstand1" role="alert"><form id="alertKatDismiss" action="?alertKatDismiss=1" method="post"><button type="button" class="close" data-dismiss="alert" aria-label="Close" onclick="f()"><span aria-hidden="true">&times;</span></button>Bitte alle Felder entsprechend ausf&uuml;llen!</form></div>';
        }
    }
    return '';
}
// popup dismiss
if (isset($_GET['alertKatDismiss']))
{
    unset($_SESSION['addKatError']);
}

// error popup
function printError()
{
    if ( key_exists('wildCardError', $_SESSION))
    {
        $error = $_SESSION['wildCardError'];
        if ( $error )
        {
            return '<script>function f() {var form = document.getElementById("alertForm"); form.submit();}</script><div class="alert alert-danger alert-dismissible fade show abstand1" role="alert"><form id="alertForm" action="?alertDismiss=1" method="post"><button type="button" class="close" data-dismiss="alert" aria-label="Close" onclick="f()"><span aria-hidden="true">&times;</span></button>Dieser Sch&uuml;ler konnte nicht hinzugef&uuml;gt werden, da er noch nicht in der Datenbank eingetragen ist.</form></div>';
        }
    }
    return '';
}
// popup dismiss
if (isset($_GET['alertDismiss']))
{
    unset($_SESSION['wildCardError']);
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
    header("Refresh:0; url=adminEvents.html");
}

if(isset($_GET['logout']))
{
    include_once("../php/userCheck.php");
    logout();
}

if (isset($_GET['addKuerzel']))
{
    include_once ("../php/userCheck.php");
    include_once ("../php/eventsVerwalten.php");

    $temp = $_SESSION['eventVerwaltung'];
    $kuerzel = $_POST['schuelerKuerzel'];
    if ( key_exists('eventWildTemp', $_SESSION))
    {
        if ( checkIfUserInDatabase($kuerzel, 0) )
        {
            $wildCard = $_SESSION['eventWildTemp'];
            $next = count($wildCard);
            $wildCard[$next] = $kuerzel;
            $_SESSION['eventWildTemp'] = $wildCard;
        }
        else
        {
            $_SESSION['wildCardError'] = true;
        }
    }
    else
    {
        if ( checkIfUserInDatabase($kuerzel, 0) )
        {
            $wildCard[0] = $kuerzel;
            $_SESSION['eventWildTemp'] = $wildCard;
        }
        else
        {
            $_SESSION['wildCardError'] = true;
        }

    }

    if ( $temp != null )
    {
        $wID = getAttrFromEvent("wID", $temp['eName'], $temp['eDatum'], $temp['aName']);
        addToWildCard($wID, $kuerzel);
        unset($wID);
    }

    unset($temp);
    unset($kuerzel);
    unset($wildCard);

}


if ( isset($_GET['deleteKuerzel']))
{
    include_once ("../php/eventsVerwalten.php");
    if ( key_exists('eventWildTemp', $_SESSION))
    {
        $temp = $_SESSION['eventVerwaltung'];

        $selected = $_POST['anzahlSelected'];

        $kurz = '';
        $pos = '';
        $wID = '';

        for ( $i = 0; $i < $selected; $i++ )
        {
            $kurz = $_POST['toDeleteName'.$i];

            $pos = array_search($kurz, $_SESSION['eventWildTemp']);
            unset($_SESSION['eventWildTemp'][$pos]);

            if ( $temp != null )
            {
                $wID = getAttrFromEvent("wID", $temp['eName'], $temp['eDatum'], $temp['aName']);
                removeFromWildCard($wID, $kurz);
            }
        }
        if ( count($_SESSION['eventWildTemp']) == 0 )
        {
            unset($_SESSION['eventWildTemp']);
        }
        unset($temp);
        unset($selected);
        unset($kurz);
        unset($pos);
        unset($wID);
    }
}

if ( isset($_GET['addEvent']))
{
    include_once ("../php/eventsVerwalten.php");
    include_once ("../php/userCheck.php");
    $name = $_POST['name'];
    $datum = $_POST['date'];
    $aName = $_POST['awardTypeBackend'];
    $beschreibung = $_POST['beschreibung'];
    $berechtigtArray = getBerechtigteAwardsProLehrer($userName);

    if ( in_array($aName, $berechtigtArray))
    {
        if ( strlen($name) == 0 && strlen($datum) == 0 && strlen($aName) == 0 )
        {
            $_SESSION['addEventError'] = true;
        }
        else {
            if ( key_exists('eventWildTemp', $_SESSION))
            {
                $result = addWildcard($_SESSION['eventWildTemp']);
            }
            else
            {
                $result[0] = Null;
            }
            addEvent($name, $datum, $userName, $aName, $beschreibung, $result[0]);


            if ( key_exists('kategorienListe', $_SESSION))
            {
                $unterKatArr = $_SESSION['kategorienListe'];

                foreach ($unterKatArr as $unterKat) {
                    /*
                     ['name']
                     ['tokenAnzahl']
                     ['beschreibung']
                     */
                    addUnterkategorie($unterKat['name'], $name, $aName, $datum, $unterKat['tokenAnzahl'], $unterKat['beschreibung']);
                }

                unset($unterKatArr);
                unset($unterKat);
            }


            // dinger löschen
            unset($_SESSION['eventWildTemp']);
            unset($_SESSION['kategorienListe']);

            unset($result);

        }

    }
    else
    {
        $_SESSION['berechtigungError'] = true;
    }

    unset($name);
    unset($datum);
    unset($aName);
    unset($beschreibung);
}

// error popup
function printEventError()
{
    if ( key_exists('addEventError', $_SESSION))
    {
        $error = $_SESSION['addEventError'];
        if ( $error )
        {
            echo '<script>function f() {var form = document.getElementById("alertEventForm"); form.submit();}</script><div class="bottomAlert"><div class="alert alert-danger alert-dismissible fade show abstand1" role="alert"><form id="alertEventForm" action="?alertEventDismiss=1" method="post"><button type="button" class="close" data-dismiss="alert" aria-label="Close" onclick="f()"><span aria-hidden="true">&times;</span></button>Bitte alle blau markierten Felder ausf&uuml;llen!</form></div></div>';
        }
    }
}
// popup dismiss
if (isset($_GET['alertEventDismiss']))
{
    unset($_SESSION['addEventError']);
}

// error popup
function printEventBerechtigungError()
{
    if ( key_exists('berechtigungError', $_SESSION))
    {
        $error = $_SESSION['berechtigungError'];
        if ( $error )
        {
            echo '<script>function f() {var form = document.getElementById("alertEventBerechtigungForm"); form.submit();}</script><div class="bottomAlert"><div class="alert alert-danger alert-dismissible fade show abstand1" role="alert"><form id="alertEventBerechtigungForm" action="?alertEventBerechtigungDismiss=1" method="post"><button type="button" class="close" data-dismiss="alert" aria-label="Close" onclick="f()"><span aria-hidden="true">&times;</span></button>Sie haben nicht die n&ouml;tige Berechtigung um ein Event zu diesem Award hinzuzuf&uuml;gen.</form></div></div>';
        }
    }
}
// popup dismiss
if (isset($_GET['alertEventBerechtigungDismiss']))
{
    unset($_SESSION['berechtigungError']);
}

if ( isset($_GET['deleteEvent']))
{
    include_once ("../php/eventsVerwalten.php");
    $temp = $_SESSION['eventVerwaltung'];
    deleteEvent($temp['eName'], $temp['eDatum'], $temp['aName']);
    unset($temp);
}

if ( isset($_GET['updateEvent']))
{
    include_once ("../php/eventsVerwalten.php");
    $temp = $_SESSION['eventVerwaltung'];
    $name = $_POST['name'];
    $datum = $_POST['date'];
    $aName = $_POST['awardTypeBackend'];
    $beschreibung = $_POST['beschreibung'];

    if ( strlen($name) == 0 && strlen($datum) == 0 && strlen($aName) == 0 )
    {
        $_SESSION['addEventError'] = true;
    }
    else {
        $wID = getAttrFromEvent('wID', $temp['eName'], $temp['eDatum'], $temp['aName'] );
        if ( $_SESSION['wildcardStatus'])
        {
            // alte mit neuer Wildcard vergleichen und dann die die nicht mehr dabei sind löschen und die neuen hinzufügen
            if ( key_exists('eventWildTemp', $_SESSION))
            {
                if ( $wID == NULL )
                {
                    $wID = addWildcard(array())[0];
                }
                $schNames = getAllZuordnungToID($wID);
                $schNamesNew = $_SESSION['eventWildTemp'];
                $toAdd = buildMix($schNames, $schNamesNew);
                $result = addArrayToWildcard($wID, $toAdd);
            }
        }
        else
        {
            if ( $wID != Null )
            {
                deleteWildcard($wID);
            }
            $wID = Null;
        }
        $katRightNow = getUnterKatProEvent($temp['eName'], $temp['eDatum'], $temp['aName']);
        changeEvent($temp['eName'], $temp['eDatum'], $temp['aName'], $userName, $name, $datum, $aName, $beschreibung, $wID );


        if ( key_exists('kategorienListe', $_SESSION))
        {
            $unterKatArr = $_SESSION['kategorienListe'];
            $katToAdd = buildMix($katRightNow, $unterKatArr);
            foreach ($katToAdd as $unterKat) {
                /*
                 ['name']
                 ['tokenAnzahl']
                 ['beschreibung']
                 */
                addUnterkategorie($unterKat['name'], $name, $aName, $datum, $unterKat['tokenAnzahl'], $unterKat['beschreibung']);
            }
        }
        else
        {
            // remove unterkat
            deleteAllUnterkategorieToEvent($name, $datum, $aName);
        }


        // dinger löschen
        unset($_SESSION['eventWildTemp']);
        unset($_SESSION['kategorienListe']);
        $_SESSION['eventVerwaltung'] = NULL;
    }


    unset($temp);
    unset($name);
    unset($datum);
    unset($aName);
    unset($beschreibung);
}

// arrNeu ist das "starke array"
function buildMix( $arr, $arrNeu )
{
    foreach ( $arr as $items )
    {
        if ( in_array($items, $arrNeu))
        {
            $ind = array_search($items, $arrNeu);
            unset($arrNeu[$ind]);
        }
    }

    return $arrNeu;
}