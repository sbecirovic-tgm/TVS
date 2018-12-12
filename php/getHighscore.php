<?php
/**
 * Created by PhpStorm.
 * User: Mir
 * Date: 22.11.2018
 * Time: 13:28
 */

$db = mysqli_connect('localhost', 'tokenverwaltung', '1234', 'tvs_datenbank');


/*
 * Alle funktionen für die Token highscores
 */
include_once ("userCheck.php");

function getTokenHighscoreProSaison ( $saisonNummer )
{
    global $db;
    $tokenProSchueler = array();


    $sqlC = "select kuerzel, sName from schueler order by order by kuerzel";
    $schueler = mysqli_query($db, $sqlC);
    for (; $schuelerArray = mysqli_fetch_assoc($schueler); )
    {
        $sKuerzel = $schuelerArray['kuerzel'];
        $sqlC2 = "select tokenAnzahl from leistung where saisonNummer = '$saisonNummer' and sKuerzel = '$sKuerzel'";
        $leistungen = mysqli_query($db, $sqlC2);
        for (; $leistungenArray = mysqli_fetch_assoc($leistungen); )
        {
            $tokenProSchueler[$schuelerArray['sName']] = $leistungenArray['tokenAnzahl'];
        }
        if ( count($leistungenArray) == 0 )
        {
            $tokenProSchueler[$schuelerArray['sName']] = 0;
        }
        $sqlSch = "select awardName from auszeichnung where skuerzel = '$sKuerzel' and saisonNummer = '$saisonNummer'";
        $awardTokenSchuler = mysqli_query($db, $sqlSch);
        for ( ; $awardTokenSchArray = mysqli_fetch_assoc($awardTokenSchuler); )
        {
            $awardName = $awardTokenSchArray['awardName'];
            $sqlAward = "select tokenLimit from award where name = '$awardName'";
            $awardToken = mysqli_query($db,$sqlAward);
            $awardTokenArray = mysqli_fetch_assoc($awardToken);

            $tokenProSchueler[$schuelerArray['sName']] += $awardTokenArray['tokenLimit'];
        }
    }
    arsort($tokenProSchueler);

    return $tokenProSchueler;
}


function getTokenHighscoreThisSaison()
{
    include_once ("saisonVerwalten.php");
    $saisonNummer = getSaisonNumb();
    return getTokenHighscoreProSaison($saisonNummer);
}

function getTokenHighscoreThisSaisonLimit($limit)
{
    global $db;
    include_once ("saisonVerwalten.php");
    $saisonNummer = getSaisonNumb();
    return getTokenHighscoreProSaisionLimit($saisonNummer, $limit);
}

function getTokenHighscoreProSaisionLimit($saisonNummer, $limit)
{
    $tokenProSchueler = getTokenHighscoreProSaison($saisonNummer);
    $out = array();
    $i = 0;
    foreach ($tokenProSchueler as $temp => $temp_value)
    {
        $out[$temp] = $temp_value;
        if ( $i == $limit-1 )
        {
            break;
        }
        $i++;
    }

    return $out;
}

function getTokenHighscoreAllTime()
{
    global $db;
    $tokenProSchueler = array();


    $sqlC = "select kuerzel, sName from schueler order by order by kuerzel";
    $schueler = mysqli_query($db, $sqlC);
    for (; $schuelerArray = mysqli_fetch_assoc($schueler); )
    {
        $sKuerzel = $schuelerArray['kuerzel'];
        $sqlC2 = "select tokenAnzahl from leistung sKuerzel = '$sKuerzel'";
        $leistungen = mysqli_query($db, $sqlC2);
        for (; $leistungenArray = mysqli_fetch_assoc($leistungen); )
        {
            $tokenProSchueler[$schuelerArray['sName']] = $leistungenArray['tokenAnzahl'];
        }
        if ( count($leistungenArray) == 0 )
        {
            $tokenProSchueler[$schuelerArray['sName']] = 0;
        }
        $sqlSch = "select awardName from auszeichnung where skuerzel = '$sKuerzel'";
        $awardTokenSchuler = mysqli_query($db, $sqlSch);
        for ( ; $awardTokenSchArray = mysqli_fetch_assoc($awardTokenSchuler); )
        {
            $awardName = $awardTokenSchArray['awardName'];
            $sqlAward = "select tokenLimit from award where name = '$awardName'";
            $awardToken = mysqli_query($db,$sqlAward);
            $awardTokenArray = mysqli_fetch_assoc($awardToken);

            $tokenProSchueler[$schuelerArray['sName']] += $awardTokenArray['tokenLimit'];
        }
    }
    arsort($tokenProSchueler);

    return $tokenProSchueler;
}

function getTokenHighscoreProAwardPerSaison ( $aName, $saisonNummer )
{
    global $db;
    $tokenProSchueler = array();

    $sqlC = "select kuerzel, sName from schueler order by order by kuerzel";
    $schueler = mysqli_query($db, $sqlC);
    for (; $schuelerArray = mysqli_fetch_assoc($schueler); )
    {
        $sKuerzel = $schuelerArray['kuerzel'];
        $sqlC2 = "select tokenAnzahl from leistung where saisonNummer = '$saisonNummer' and sKuerzel = '$sKuerzel' and aName = '$aName'";
        $leistungen = mysqli_query($db, $sqlC2);
        for (; $leistungenArray = mysqli_fetch_assoc($leistungen); )
        {
            $tokenProSchueler[$schuelerArray['sName']] = $leistungenArray['tokenAnzahl'];
        }
        if ( count($leistungenArray) == 0 )
        {
            $tokenProSchueler[$schuelerArray['sName']] = 0;
        }
        $sqlSch = "select awardName from auszeichnung where skuerzel = '$sKuerzel' and saisonNummer = '$saisonNummer' and awardName = '$aName'";
        $awardTokenSchuler = mysqli_query($db, $sqlSch);
        for ( ; $awardTokenSchArray = mysqli_fetch_assoc($awardTokenSchuler); )
        {
            $awardName = $awardTokenSchArray['awardName'];
            $sqlAward = "select tokenLimit from award where name = '$awardName'";
            $awardToken = mysqli_query($db,$sqlAward);
            $awardTokenArray = mysqli_fetch_assoc($awardToken);

            $tokenProSchueler[$schuelerArray['sName']] += $awardTokenArray['tokenLimit'];
        }
    }
    arsort($tokenProSchueler);

    return $tokenProSchueler;
}


function getTokenHighscoreProAwardAllTime ( $aName )
{
    global $db;
    $tokenProSchueler = array();

    $sqlC = "select kuerzel, sName from schueler order by order by kuerzel";
    $schueler = mysqli_query($db, $sqlC);
    for (; $schuelerArray = mysqli_fetch_assoc($schueler); )
    {
        $sKuerzel = $schuelerArray['kuerzel'];
        $sqlC2 = "select tokenAnzahl from leistung where sKuerzel = '$sKuerzel' and aName = '$aName'";
        $leistungen = mysqli_query($db, $sqlC2);
        for (; $leistungenArray = mysqli_fetch_assoc($leistungen); )
        {
            $tokenProSchueler[$schuelerArray['sName']] = $leistungenArray['tokenAnzahl'];
        }
        if ( count($leistungenArray) == 0 )
        {
            $tokenProSchueler[$schuelerArray['sName']] = 0;
        }
        $sqlSch = "select awardName from auszeichnung where skuerzel = '$sKuerzel' and awardName = '$aName'";
        $awardTokenSchuler = mysqli_query($db, $sqlSch);
        for ( ; $awardTokenSchArray = mysqli_fetch_assoc($awardTokenSchuler); )
        {
            $awardName = $awardTokenSchArray['awardName'];
            $sqlAward = "select tokenLimit from award where name = '$awardName'";
            $awardToken = mysqli_query($db,$sqlAward);
            $awardTokenArray = mysqli_fetch_assoc($awardToken);

            $tokenProSchueler[$schuelerArray['sName']] += $awardTokenArray['tokenLimit'];
        }
    }
    arsort($tokenProSchueler);

    return $tokenProSchueler;
}



/*
 * Alle funktionen für die Award highscores
 */

function getAwardHighscoreProSaison( $saisonNummer )
{
    global $db;

    $out = array();

    $sqlC = "select kuerzel, sName from schueler order by order by kuerzel";
    $schueler = mysqli_query($db, $sqlC);
    for (; $schuelerArray = mysqli_fetch_assoc($schueler); )
    {
        $name = $schuelerArray['sName'];
        $out[$name] = 0;
        $sKuerzel = $schuelerArray['kuerzel'];
        $sqlC2 = "select skuerzel from auszeichnung where saisonNummer = '$saisonNummer' and skuerzel = '$sKuerzel'";
        $awards = mysqli_query($db, $sqlC2);
        for ( ; $awardsArray = mysqli_fetch_assoc($awards); )
        {
            $out[$name] += 1;
        }
    }

    arsort($out);
    return $out;
}

function getAwardHighscoreAllTime()
{
    global $db;

    $out = array();

    $sqlC = "select kuerzel, sName from schueler order by order by kuerzel";
    $schueler = mysqli_query($db, $sqlC);
    for (; $schuelerArray = mysqli_fetch_assoc($schueler); )
    {
        $name = $schuelerArray['sName'];
        $out[$name] = 0;
        $sKuerzel = $schuelerArray['kuerzel'];
        $sqlC2 = "select skuerzel from auszeichnung where skuerzel = '$sKuerzel'";
        $awards = mysqli_query($db, $sqlC2);
        for ( ; $awardsArray = mysqli_fetch_assoc($awards); )
        {
            $out[$name] += 1;
        }
    }

    arsort($out);
    return $out;
}

function getAwardHighscoreProAwardSaision( $saisonNummer, $aName )
{
    global $db;

    $out = array();

    $sqlC = "select kuerzel, sName from schueler order by order by kuerzel";
    $schueler = mysqli_query($db, $sqlC);
    for (; $schuelerArray = mysqli_fetch_assoc($schueler); )
    {
        $name = $schuelerArray['sName'];
        $out[$name] = 0;
        $sKuerzel = $schuelerArray['kuerzel'];
        $sqlC2 = "select skuerzel from auszeichnung where saisonNummer = '$saisonNummer' and skuerzel = '$sKuerzel' and awardName = '$aName'";
        $awards = mysqli_query($db, $sqlC2);
        for ( ; $awardsArray = mysqli_fetch_assoc($awards); )
        {
            $out[$name] += 1;
        }
    }

    arsort($out);
    return $out;
}

function getAwardHighscoreProAwardAllTime( $aName )
{
    global $db;

    $out = array();

    $sqlC = "select kuerzel, sName from schueler order by order by kuerzel";
    $schueler = mysqli_query($db, $sqlC);
    for (; $schuelerArray = mysqli_fetch_assoc($schueler); )
    {
        $name = $schuelerArray['sName'];
        $out[$name] = 0;
        $sKuerzel = $schuelerArray['kuerzel'];
        $sqlC2 = "select skuerzel from auszeichnung where skuerzel = '$sKuerzel' and awardName = '$aName'";
        $awards = mysqli_query($db, $sqlC2);
        for ( ; $awardsArray = mysqli_fetch_assoc($awards); )
        {
            $out[$name] += 1;
        }
    }

    arsort($out);
    return $out;
}

function getAwardHighscoreThisSaisonLimit($limit)
{
    include_once ("saisonVerwalten.php");
    $saisonNummer = getSaisonNumb();
    return getAwardHighscoreProSaisionLimit($saisonNummer, $limit);
}

function getAwardHighscoreProSaisionLimit($saisonNummer, $limit)
{
    $tawardProSchueler = getAwardHighscoreProSaison($saisonNummer);
    $out = array();
    $i = 0;
    foreach ($tawardProSchueler as $temp => $temp_value)
    {
        $out[$temp] = $temp_value;
        if ( $i == $limit-1 )
        {
            break;
        }
        $i++;
    }

    return $out;
}

?>