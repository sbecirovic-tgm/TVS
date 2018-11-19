<?php
/**
 * Created by PhpStorm.
 * User: Mir
 * Date: 19.11.2018
 * Time: 16:40
 */
$db = mysqli_connect('localhost', 'root', '2017lewiS661451', 'tvs_datenbank');

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
        $sqlC = 'select count(kuerzel) as anzahl from schueler where kuerzel = "' . $kuerzel . '"';
    }
    elseif ( $isSchueler == 1 )
    {
        $sqlC = 'select count(kuerzel) as anzahl from lehrer where kuerzel = "' . $kuerzel . '"';
    }
    elseif ( $isSchueler == 2 )
    {
        $sqlC = 'select count(kuerzel) as anzahl from superuser where kuerzel = "' . $kuerzel . '"';
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
    return is_array($kuerzel, $headadminArray);
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
    $sqlAward = "select name from award";
    $awards = mysqli_query($db, $sqlAward);
    $out = true;
    for ( ;$awardArray = mysqli_fetch_assoc($awards); )
    {
        $aName = $awardArray['name'];
        $sqlC = "insert into leistung ( aName, sKuerzel, tokenAnzahl ) values ( '$aName' , '$kuerzel', 0)";
        $result = mysqli_query($db, $sqlC);
        if ( !$result )
        {
            $out = false;
        }
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

// Lehrer
function insertLehrer ( $kuerzel, $skuerzel )
{
    global $db;
    $sqlC = "insert into lehrer ( kuerzel, skuerzel ) values ( '$kuerzel', '$skuerzel')";
    return mysqli_query($db, $sqlC);
}

function updateLehrer ( $kuerzel, $skuerzel )
{
    global $db;
    $sqlC = "update lehrer set kuerzel = '$kuerzel', skuerzel = '$skuerzel' where kuerzel = $kuerzel";
    return mysqli_query($db, $sqlC);
}

function deleteLehrer ( $kuerzel )
{
    global  $db;
    $sqlC = "delete from lehrer where kuerzel = '$kuerzel'";
    return mysqli_query($db, $sqlC);
}

// Superuser
function insertSuper ( $kuerzel )
{
    global $db;
    $sqlC = "insert into superuser ( kuerzel ) values ( '$kuerzel')";
    return mysqli_query($db, $sqlC);
}

function updateSuper ( $kuerzel, $kuerzelNeu )
{
    global $db;
    $sqlC = "update superuser set kuerzel = '$kuerzelNeu' where kuerzel = $kuerzel";
    return mysqli_query($db, $sqlC);
}

function deleteSuper ( $kuerzel )
{
    global  $db;
    $sqlC = "delete from superuser where kuerzel = '$kuerzel'";
    return mysqli_query($db, $sqlC);
}

?>