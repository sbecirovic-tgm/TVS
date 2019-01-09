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
    $sqlC = 'select name from award';
    $award = mysqli_query($db, $sqlC);
    for (; $award_array = mysqli_fetch_assoc($award); ) {
        $name = $award_array['name'];
        echo '<a class="dropdown-item" href="#" onclick="setAwardButton(\'' . $name . '\')">' . $name . '</a>';
    }
}

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
            $dateStringFind = $use['eDate'];


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
                    <button id="eventVerwalten'.$i.'" type="button" class="btn btn-outline-primary adminEventButton abstand1 center-block">Event verwalten</button>
                </div>';
        echo '</div>';
        $i++;
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

function printVerwalten()
{
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
                                    <ol id="kategorienListe" class="list-group">
                                        <?php
                                                printKatList();
                                            ?>
                                    </ol>
                                    <?php
                                            printKatError();
                                        ?>
                                </div>
                                <div class="col-sm-6">
                                    <button type="button" class="btn btn-outline-primary dashboardButtons abstand1" data-toggle="modal" data-target="#popUpKategorie">Neue Kategorie hinzufügen</button>
                                </div>
                                <div class="col-sm-6">
                                    <button type="button" class="btn btn-outline-primary dashboardButtons abstand1" onclick="submitForm(\'katForm\')" >Ausgewähle Kategorien löschen</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <form id="schuelerForm" action="?deleteKuerzel=1" method="post">
                                <input type="number" id="anzahlSelectedSch" name="anzahlSelected" class="hiddenMeldung" value="0">
                                <div id="schuelerListBackend">
                                </div>
                            </form>
                            <?php
                                    printWildcard();
                                    printError();
                                ?>
                            <div class="col-sm-12 text-right">
                                <button type="button" class="btn btn-outline-primary dashboardButtons abstand1" onclick="submitForm(\'schuelerForm\')" >Ausgewähle Schüler löschen</button>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="card-body">
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
                                    <div class="btn-group">
                                        <script type="application/javascript">
                                            function setAwardButton(value) {
                                                document.getElementById("awardType").innerHTML = value;
                                                document.getElementById("awardTypeBackend").value = value;
                                            }
                                        </script>
                                        <button type="button" name="awardType" id="awardType" class="btn dropdown-toggle btn-primary"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" onclick="">Award auswählen</button>
                                        <input type="text" min="0" id="awardTypeBackend" name="awardTypeBackend" class="hiddenMeldung" value="">

                                        <div class="dropdown-menu">
                                            <?php
                                                printAwardDropDown();
                                            ?>
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
                            <button type="button" class="btn btn-outline-primary dashboardButtons abstand1" onclick="submitForm(\'addEvent\')">Event hinzufügen</button>
                        </div>

                        <!-- Jetzt kommen die popovers -->
                        <div class="modal fade" id="popUpKategorie" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <form id="addKat" action="?addKat=1" method="post">
                                        <div class="modal-content">
                                        <div class="modal-header">
                                            <div class="col-sm-6">
                                                <h5 class="modal-title">Kategorie zum Event hinzufügen</h5>
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
                                            <button type="button" class="btn btn-outline-primary" onclick="submitForm(\'addKat\')">Hinzufügen</button>
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
                                    <ol id="kategorienListe" class="list-group">
                                        <?php
                                                printKatList();
                                            ?>
                                    </ol>
                                    <?php
                                            printKatError();
                                        ?>
                                </div>
                                <div class="col-sm-6">
                                    <button type="button" class="btn btn-outline-primary dashboardButtons abstand1" data-toggle="modal" data-target="#popUpKategorie">Neue Kategorie hinzufügen</button>
                                </div>
                                <div class="col-sm-6">
                                    <button type="button" class="btn btn-outline-primary dashboardButtons abstand1" onclick="submitForm(\'katForm\')" >Ausgewähle Kategorien löschen</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <form id="schuelerForm" action="?deleteKuerzel=1" method="post">
                                <input type="number" id="anzahlSelectedSch" name="anzahlSelected" class="hiddenMeldung" value="0">
                                <div id="schuelerListBackend">
                                </div>
                            </form>
                            <?php
                                    printWildcard();
                                    printError();
                                ?>
                            <div class="col-sm-12 text-right">
                                <button type="button" class="btn btn-outline-primary dashboardButtons abstand1" onclick="submitForm(\'schuelerForm\')" >Ausgewähle Schüler löschen</button>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="card-body">
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
                                    <div class="btn-group">
                                        <script type="application/javascript">
                                            function setAwardButton(value) {
                                                document.getElementById("awardType").innerHTML = value;
                                                document.getElementById("awardTypeBackend").value = value;
                                            }
                                        </script>
                                        <button type="button" name="awardType" id="awardType" class="btn dropdown-toggle btn-primary"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" onclick="">Award auswählen</button>
                                        <input type="text" min="0" id="awardTypeBackend" name="awardTypeBackend" class="hiddenMeldung" value="">

                                        <div class="dropdown-menu">
                                            <?php
                                                printAwardDropDown();
                                            ?>
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
                        <div class="col-sm-12 text-center">
                            <button type="button" class="btn btn-outline-primary dashboardButtons abstand1" onclick="submitForm(\'addEvent\')">Event hinzufügen</button>
                        </div>

                        <!-- Jetzt kommen die popovers -->
                        <div class="modal fade" id="popUpKategorie" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <form id="addKat" action="?addKat=1" method="post">
                                        <div class="modal-content">
                                        <div class="modal-header">
                                            <div class="col-sm-6">
                                                <h5 class="modal-title">Kategorie zum Event hinzufügen</h5>
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
                                            <button type="button" class="btn btn-outline-primary" onclick="submitForm(\'addKat\')">Hinzufügen</button>
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
    if ( key_exists('kategorienListe', $_SESSION))
    {
        $list = $_SESSION['kategorienListe'];
        foreach ( $list as $elm )
        {
            $name = $elm['name'];
            $tokenAnzahl = $elm['tokenAnzahl'];
            $beschreibung = $elm['beschreibung'];
            echo '<li id="'.$name.'" class="list-group-item" style="cursor: pointer;" onclick="markElm(\''.$name.'\', '.$tokenAnzahl.', \''.$beschreibung.'\')"><strong>'.$name.'</strong> ('.$tokenAnzahl.' Token)</li>';
        }
    }
    else
    {
        echo '<li id="startValueKat" class="list-group-item"><strong>Noch keine Eintr&auml;ge</strong></li>';
    }
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
            echo '<div class="row">
                <div class="col-sm-8">
                    <h4>Zug&auml;nglichkeit</h4>
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
            </div>';
        }
        else
        {
            echo '<div class="row">
                <div class="col-sm-8">
                    <h4>Zug&auml;nglichkeit</h4>
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
            </div>';
        }
    }
    else
    {
        $_SESSION['wildcardStatus'] = false;
        echo '<div class="row">
                <div class="col-sm-8">
                    <h4>Zug&auml;nglichkeit</h4>
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
}

if (isset($_GET['addKat']))
{
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
    }

}

if ( isset($_GET['deleteKat']))
{
    if ( key_exists('kategorienListe', $_SESSION))
    {
        $selected = $_POST['anzahlSelected'];

        for ( $i = 0; $i < $selected; $i++ )
        {
            $search['name'] = $_POST['toDeleteName'.$i];
            $search['tokenAnzahl'] = $_POST['toDeleteToken'.$i];
            $search['beschreibung'] = $_POST['toDeleteBesch'.$i];

            $pos = array_search($search, $_SESSION['kategorienListe']);
            unset($_SESSION['kategorienListe'][$pos]);
        }
        if ( count($_SESSION['kategorienListe']) == 0 )
        {
            unset($_SESSION['kategorienListe']);
        }
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
            echo '<script>function f() {var form = document.getElementById("alertKatDismiss"); form.submit();}</script><div class="alert alert-danger alert-dismissible fade show abstand1" role="alert"><form id="alertKatDismiss" action="?alertKatDismiss=1" method="post"><button type="button" class="close" data-dismiss="alert" aria-label="Close" onclick="f()"><span aria-hidden="true">&times;</span></button>Bitte alle Felder entsprechend ausf&uuml;llen!</form></div>';
        }
    }
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
            echo '<script>function f() {var form = document.getElementById("alertForm"); form.submit();}</script><div class="alert alert-danger alert-dismissible fade show abstand1" role="alert"><form id="alertForm" action="?alertDismiss=1" method="post"><button type="button" class="close" data-dismiss="alert" aria-label="Close" onclick="f()"><span aria-hidden="true">&times;</span></button>Dieser Sch&uuml;ler konnte nicht hinzugef&uuml;gt werden, da er noch nicht in der Datenbank eingetragen ist.</form></div>';
        }
    }
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
    if ( key_exists('eventWildTemp', $_SESSION))
    {
        $kuerzel = $_POST['schuelerKuerzel'];
        echo $kuerzel;
        if ( checkIfUserInDatabase($kuerzel, 0) )
        {
            $temp = $_SESSION['eventWildTemp'];
            $next = count($temp);

            $temp[$next] = $kuerzel;
            $_SESSION['eventWildTemp'] = $temp;

        }
        else
        {
            $_SESSION['wildCardError'] = true;
        }

    }
    else
    {
        $kuerzel = $_POST['schuelerKuerzel'];
        $temp[0] = $kuerzel;
        $_SESSION['eventWildTemp'] = $temp;
    }
}


if ( isset($_GET['deleteKuerzel']))
{
    if ( key_exists('eventWildTemp', $_SESSION))
    {
        $selected = $_POST['anzahlSelected'];

        for ( $i = 0; $i < $selected; $i++ )
        {
            $kurz = $_POST['toDeleteName'.$i];

            $pos = array_search($kurz, $_SESSION['eventWildTemp']);
            unset($_SESSION['eventWildTemp'][$pos]);
        }
        if ( count($_SESSION['eventWildTemp']) == 0 )
        {
            unset($_SESSION['eventWildTemp']);
        }
    }
}

if ( isset($_GET['addEvent']))
{
    include_once ("../php/eventsVerwalten.php");
    $name = $_POST['name'];
    $datum = $_POST['date'];
    $aName = $_POST['awardTypeBackend'];
    $beschreibung = $_POST['beschreibung'];

    if ( strlen($name) == 0 && strlen($datum) == 0 && strlen($aName) == 0 && strlen($beschreibung) == 0 )
    {
        $_SESSION['addEventError'] = true;
    }
    else {
        if ( key_exists('eventWildTemp', $_SESSION))
        {
            $result = addWildcard($_SESSION['eventWildTemp']);
        }
        addEvent($name, $datum, $userName, $aName, $beschreibung, $result[1]);

        echo '<br>';

        if ( key_exists('kategorienListe', $_SESSION))
        {
            $unterKatArr = $_SESSION['kategorienListe'];

            foreach ($unterKatArr as $unterKat) {
                /*
                 ['name']
                 ['tokenAnzahl']
                 ['beschreibung']
                 */
                $temp = addUnterkateogrie($unterKat['name'], $name, $aName, $datum, $unterKat['tokenAnzahl'], $unterKat['beschreibung']);
                echo 'Kat';
                if ( $temp )
                {
                    echo 1;
                }
                else
                {
                    echo 0;
                }
            }
        }


        // dinger löschen
        unset($_SESSION['eventWildTemp']);
        unset($_SESSION['kategorienListe']);
    }
}

// error popup
function printEventError()
{
    if ( key_exists('addEventError', $_SESSION))
    {
        $error = $_SESSION['addEventError'];
        if ( $error )
        {
            echo '<script>function f() {var form = document.getElementById("alertEventForm"); form.submit();}</script><div class="alert alert-danger alert-dismissible fade show abstand1" role="alert"><form id="alertEventForm" action="?alertEventDismiss=1" method="post"><button type="button" class="close" data-dismiss="alert" aria-label="Close" onclick="f()"><span aria-hidden="true">&times;</span></button>Bitte alle Felder ausfüllen!</form></div>';
        }
    }
}
// popup dismiss
if (isset($_GET['alertEventDismiss']))
{
    unset($_SESSION['addEventError']);
}

if ( isset($_GET['deleteEvent']))
{
    include_once ("../php/awardsVerwalten.php");
    $temp = $_SESSION['eventVerwaltung'];
    deleteEvent($temp['eName'], $temp['eDatum'], $temp['aName']);
}

if ( isset($_GET['updateEvent']))
{
    include_once ("../php/awardsVerwalten.php");
    $temp = $_SESSION['eventVerwaltung'];
    $name = $_POST['name'];
    $datum = $_POST['date'];
    $aName = $_POST['awardTypeBackend'];
    $beschreibung = $_POST['beschreibung'];

    if ( strlen($name) == 0 && strlen($datum) == 0 && strlen($aName) == 0 && strlen($beschreibung) == 0 )
    {
        $_SESSION['addEventError'] = true;
    }
    else {

        if ( $_SESSION['wildcardStatus'])
        {
            // alte mit neuer Wildcard vergleichen und dann die die nicht mehr dabei sind löschen und die neuen hinzufügen
            $result = addWildcard($_SESSION['eventWildTemp']);
        }
        else
        {
            $wID = Null;
        }
        changeEvent($temp['eName'], $temp['eDatum'], $temp['aName'], $userName, $name, $datum, $aName, $beschreibung, $wID );

        /*
        if ( key_exists('kategorienListe', $_SESSION))
        {
            $unterKatArr = $_SESSION['kategorienListe'];

            foreach ($unterKatArr as $unterKat) {
                /*
                 ['name']
                 ['tokenAnzahl']
                 ['beschreibung']
                 */
                /*addUnterkateogrie($unterKat['name'], $name, $aName, $datum, $unterKat['tokenAnzahl'], $unterKat['beschreibung']);
            }
        }
        */

        // dinger löschen
        unset($_SESSION['eventWildTemp']);
        unset($_SESSION['kategorienListe']);
    }

}