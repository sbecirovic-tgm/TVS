<?php
/**
 * Created by PhpStorm.
 * User: Mir
 * Date: 19.11.2018
 * Time: 16:40
 */
$db = mysqli_connect('localhost', 'tokenverwaltung', '1234', 'tvs_datenbank');

/*
 * LOGOUT!
 */
function logout()
{
    header("Refresh:0; url=../index.html");
    if(session_status() == PHP_SESSION_ACTIVE) {
        session_destroy();
    }
}



/*
 * $isSchueler: Wenn 0, dann schueler
 *              Wenn 1, dann Leher
 *              Wenn 2, dann Headadmin
 */
function checkIfUserInDatabase( $kuerzel, $isSchueler )
{
    global $db;
    if ( $isSchueler == 0 )
    {
        $sqlC = "select count(kuerzel) as anzahl from schueler where kuerzel = '$kuerzel'";
    }
    elseif ( $isSchueler == 1 )
    {
        $sqlC = "select count(kuerzel) as anzahl from lehrer where kuerzel = '$kuerzel'";
    }
    elseif ( $isSchueler == 2 )
    {
        $sqlC = "select count(kuerzel) as anzahl from superuser where kuerzel = '$kuerzel'";
    }
    $users = mysqli_query($db, $sqlC);
    $userArray = mysqli_fetch_assoc($users);
    if ( $userArray['anzahl'] < 1 )
    {
        return False;
    }
    else
    {
        return True;
    }
}

function checkIfUserIsSuperUser ( $kuerzel )
{
    global $db;
    $sqlC = 'select kuerzel from superuser';
    $headadmin = mysqli_query($db, $sqlC);
    $headadminArray = mysqli_fetch_assoc($headadmin);
    return in_array($kuerzel, $headadminArray);
}

// Schüler
function insertSchueler ( $kuerzel, $sName )
{
    global $db;
    $sqlC = "insert into schueler ( kuerzel, sName ) values ( '$kuerzel' , '$sName')";
    $result[0] = mysqli_query($db, $sqlC);
    $result[1] = makeLeistungProSchueler($kuerzel);
    return $result;
}

function makeLeistungProSchueler($kuerzel)
{
    global $db;
    include_once ("saisonVerwalten.php");
    $sqlAward = "select name from award";
    $awards = mysqli_query($db, $sqlAward);
    $out = true;
    for ( ;$awardArray = mysqli_fetch_assoc($awards); )
    {
        $aName = $awardArray['name'];
        $saisonNumb = getSaisonNumb();
        $sqlC = "insert into leistung ( aName, sKuerzel, tokenAnzahl, saisonNummer ) values ( '$aName' , '$kuerzel', 0, '$saisonNumb')";
        $result = mysqli_query($db, $sqlC);
        if ( !$result )
        {
            $out = false;
        }
    }
    return $out;
}

function getGesamteTokenProSchueler ( $kuerzel, $saisonNummer )
{
    global $db;
    $out = 0;
    $sqlC = "select tokenAnzahl from leistung where sKuerzel = '$kuerzel'";
    $token = mysqli_query($db, $sqlC);
    for ( ;$tokenArray = mysqli_fetch_assoc($token); )
    {
        $out += $tokenArray['tokenAnzahl'];
    }
    $sqlC = "select datum from auszeichnung where skuerzel = '$kuerzel' and saisonNummer = '$saisonNummer'";
    $auszeichnungen = mysqli_query($db, $sqlC);
    for ( ;$auszeichnungenArray = mysqli_fetch_assoc($auszeichnungen); )
    {
        $out += 15;
    }

    return $out;

}

function getAllSchuelerNamesToKurzel()
{
    global $db;
    $out = array();
    $sqlC = "select sName, kuerzel from schueler";
    $schueler = mysqli_query($db, $sqlC);

    for ( ;$tokenArray = mysqli_fetch_assoc($schueler); )
    {
        $out[$tokenArray['kuerzel']] = $tokenArray['sName'];
    }

    return $out;
}

function getGesmateTokenProKat ( $kuerzel, $aName, $saisonNummer )
{
    global $db;
    $out = 0;
    $sqlC = "select tokenAnzahl from leistung where sKuerzel = '$kuerzel' and aName = '$aName'";
    $token = mysqli_query($db, $sqlC);
    for ( ;$tokenArray = mysqli_fetch_assoc($token); )
    {
        $out += $tokenArray['tokenAnzahl'];
    }
    $sqlC = "select datum from auszeichnung where skuerzel = '$kuerzel' and aName = '$aName' and saisonNummer = '$saisonNummer'";
    $auszeichnungen = mysqli_query($db, $sqlC);
    for ( ;$auszeichnungenArray = mysqli_fetch_assoc($auszeichnungen); )
    {
        $out += 15;
    }

    return $out;
}

function getGesamteAwards ( $kuerzel )
{
    global $db;
    $sqlC = "select name from award";
    $awards = mysqli_query($sqlC, $db);

    $out = array();
    for (; $awardsArray = mysqli_fetch_assoc($awards); )
    {
        array_push($out ,$awardsArray['name']);
    }

    return $out;
}

function updateSchueler ( $kuerzel, $kuerzelNeu ,$sName )
{
    global $db;
    $sqlC = "update schueler set kuerzel = '$kuerzelNeu', sName = '$sName' where kuerzel = $kuerzel";
    return mysqli_query($db, $sqlC);
}

function deleteSchueler ( $kuerzel )
{
    global  $db;
    $sqlC = "delete from schueler where kuerzel = '$kuerzel'";
    return mysqli_query($db, $sqlC);
}

function getNameFromKuerzel ( $kuerzel )
{
    global $db;
    $sqlC = "select sName from schueler where kuerzel = '$kuerzel'";
    $out = mysqli_fetch_assoc(mysqli_query($db, $sqlC));
    return $out['sName'];
}


// Lehrer
function insertLehrer ( $kuerzel, $skuerzel, $lName )
{
    global $db;
    $sqlC = "insert into lehrer ( kuerzel, lName, skuerzel ) values ( '$kuerzel', '$lName', '$skuerzel')";
    return mysqli_query($db, $sqlC);
}
function insertLehrerShort ( $kuerzel, $lName )
{
    global $db;
    $sqlC = "insert into lehrer ( kuerzel, lName ) values ( '$kuerzel', '$lName')";
    return mysqli_query($db, $sqlC);
}

function updateLehrer ( $kuerzel, $kuerzelNeu, $skuerzel, $lName )
{
    global $db;
    $sqlC = "update lehrer set kuerzel = '$kuerzelNeu', skuerzel = '$skuerzel', lName = '$lName' where kuerzel = $kuerzel";
    return mysqli_query($db, $sqlC);
}

function deleteLehrer ( $kuerzel )
{
    global  $db;
    $sqlC = "delete from lehrer where kuerzel = '$kuerzel'";
    return mysqli_query($db, $sqlC);
}

function getBerechtigteLehrerToAward( $aName )
{
    global $db;
    $sqlC = "select * from erlaubnis where aName = '$aName'";
    $temp = mysqli_query($db, $sqlC);
    $out = array();
    for ( $i = 0; $erlaubnisArray = mysqli_fetch_assoc($temp); $i++ )
    {
        $out[$i] = $erlaubnisArray['lKuerzel'];
    }
    return $out;
}

function deleteBerechtigungLehrerToAward ( $kuerzel, $aName )
{
    global $db;
    $sqlC = "delete from erlaubnis where aName = '$aName' and lKuerzel = '$kuerzel'";
    $result = mysqli_query($db, $sqlC);
    return $result;
}

function deleteBerechtigungLehrerArrayToAward ( $lehrerArray, $aName )
{
    $out = true;
    foreach ($lehrerArray as $lehrer )
    {
        if ( deleteBerechtigungLehrerToAward($lehrer, $aName) == false )
        {
            $out = false;
        }
    }
    return $out;
}

function berechtigeLehrerToAward ( $kuerzel, $aName )
{
    global $db;
    $sqlC = "insert into erlaubnis (aName, lKuerzel) values('$aName', '$kuerzel')";
    $result = mysqli_query($db, $sqlC);
    return $result;
}

function berechtigeLehrerArrayToAward ( $lehrerArray, $aName )
{
    $out = true;
    foreach ($lehrerArray as $lehrer )
    {
        if ( berechtigeLehrerToAward($lehrer, $aName) == false )
        {
            $out = false;
        }
    }
    return $out;
}

function getAllLehrerNotBerechtigt( $aName )
{
    global $db;
    $sqlC = "select kuerzel from lehrer where kuerzel not in (select lKuerzel from erlaubnis where aName = '$aName')";
    $out = array();
    $temp = mysqli_query($db, $sqlC);
    for ( $i = 0; $lehrerArray = mysqli_fetch_assoc($temp); $i++ )
    {
        $out[$i] = $lehrerArray['kuerzel'];
    }
    return $out;
}

function getAllLehrer()
{
    global $db;
    $sqlC = "select kuerzel from lehrer";
    $temp = mysqli_query($db, $sqlC);
    $out = array();
    for ( $i = 0; $array = mysqli_fetch_assoc($temp); $i++ )
    {
        $out[$i] = $array['kuerzel'];
    }

    return $out;
}

function isLehrerBerechtigt( $aName, $kuerzel )
{
    global $db;
    $sqlC = "select count(lKuerzel) as anz from erlaubnis where lKuerzel = '$kuerzel' and aName = '$aName'";
    $temp = mysqli_query($db, $sqlC);
    $result = mysqli_fetch_assoc($temp);
    if ( $result['anz'] == 0 )
    {
        return false;
    }
    else
    {
        return true;
    }
}

function getNameToLehrerKuerzel( $kuerzel )
{
    global $db;
    $sqlC = "select lName from lehrer where kuerzel = '$kuerzel'";
    $temp = mysqli_query($db, $sqlC);
    return mysqli_fetch_assoc($temp)['lName'];
}

// Superuser
function insertSuper ( $kuerzel, $lName )
{
    global $db;
    $sqlC = "insert into superuser ( kuerzel, lName ) values ( '$kuerzel', '$lName')";
    return mysqli_query($db, $sqlC);
}

function updateSuper ( $kuerzel, $kuerzelNeu, $lName )
{
    global $db;
    $sqlC = "update superuser set kuerzel = '$kuerzelNeu', lName = '$lName' where kuerzel = '$kuerzel'";
    return mysqli_query($db, $sqlC);
}

function deleteSuper ( $kuerzel )
{
    global  $db;
    $sqlC = "delete from superuser where kuerzel = '$kuerzel'";
    return mysqli_query($db, $sqlC);
}

function getNameFormSuper( $kuerzel )
{
    global $db;
    $sqlC = "select lName from superuser where kuerzel = '$kuerzel'";
    $temp = mysqli_query($db, $sqlC);
    return mysqli_fetch_assoc($temp)['lName'];
}

?>