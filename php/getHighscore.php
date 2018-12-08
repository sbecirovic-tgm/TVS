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
function getTokenHighscoreProSasison( $saisonNummer )
{
    global $db;
    $tokenProSchueler = array();

    $nameToKurzel = getAllSchuelerNamesToKurzel();

    $sqlC = "select sKuerzel, tokenAnzahl from leistung where saisonNummer = '$saisonNummer' order by sKuerzel";
    $leistungen = mysqli_query($db, $sqlC);
    for (; $leistungenArray = mysqli_fetch_assoc($leistungen);)
    {
        $sKuerzel = $leistungenArray['sKuerzel'];
        $name = $nameToKurzel[$sKuerzel];
        if ( !array_key_exists ( $name , $tokenProSchueler ) )
        {
            $tokenProSchueler[$name] = $leistungenArray['tokenAnzahl'];
        }
        else
        {
            $tokenProSchueler[$name] += $leistungenArray['tokenAnzahl'];
        }
        $sqlSch = "select skuerzel, awardName from auszeichnung where skuerzel = '$sKuerzel' and saisonNummer = '$saisonNummer'";
        $awardTokenSchuler = mysqli_query($db, $sqlSch);
        for ( ; $awardTokenSchArray = mysqli_fetch_assoc($awardTokenSchuler); )
        {
            $awardName = $awardTokenSchArray['awardName'];
            $sqlAward = "select tokenLimit from award where name = '$awardName'";
            $awardToken = mysqli_query($db,$sqlAward);
            $awardTokenArray = mysqli_fetch_assoc($awardToken);
            $tokenProSchueler[$name] += $awardTokenArray['tokenLimit'];
        }
    }
    arsort($tokenProSchueler);

    return $tokenProSchueler;
}

function getTokenHighscoreThisSaison()
{
    include_once ("saisonVerwalten.php");
    $saisonNummer = getSaisonNumb();
    return getTokenHighscoreProSasison($saisonNummer);
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
    $tokenProSchueler = getTokenHighscoreProSasison($saisonNummer);
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

    $nameToKurzel = getAllSchuelerNamesToKurzel();

    $sqlC = "select sKuerzel, tokenAnzahl from leistung order by sKuerzel";
    $leistungen = mysqli_query($db, $sqlC);
    for (; $leistungenArray = mysqli_fetch_assoc($leistungen);)
    {
        $sKuerzel = $leistungenArray['sKuerzel'];
        $name = $nameToKurzel[$sKuerzel];
        if ( !array_key_exists ( $name , $tokenProSchueler ) )
        {
            $tokenProSchueler[$name] = $leistungenArray['tokenAnzahl'];
        }
        else
        {
            $tokenProSchueler[$name] += $leistungenArray['tokenAnzahl'];
        }
        $sqlSch = "select skuerzel, awardName from auszeichnung where skuerzel = '$sKuerzel'";
        $awardTokenSchuler = mysqli_query($db, $sqlSch);
        for ( ; $awardTokenSchArray = mysqli_fetch_assoc($awardTokenSchuler); )
        {
            $awardName = $awardTokenSchArray['awardName'];
            $sqlAward = "select tokenLimit from award where name = '$awardName'";
            $awardToken = mysqli_query($db, $sqlAward);
            $awardTokenArray = mysqli_fetch_assoc($awardToken);
            $tokenProSchueler[$name] += $awardTokenArray['tokenLimit'];
        }
    }
    arsort($tokenProSchueler);

    return $tokenProSchueler;
}

function getTokenHighscoreProAwardPerSaison( $aName, $saisonNummer )
{
    global $db;
    $tokenProSchueler = array();

    $nameToKurzel = getAllSchuelerNamesToKurzel();

    $sqlC = "select sKuerzel, tokenAnzahl from leistung where saisonNummer = '$saisonNummer' and aName = '$aName'order by skuerzel";
    $leistungen = mysqli_query($db, $sqlC);
    for (; $leistungenArray = mysqli_fetch_assoc($leistungen);)
    {
        $sKuerzel = $leistungenArray['skuerzel'];
        $name = $nameToKurzel[$sKuerzel];
        if ( !array_key_exists ( $name , $tokenProSchueler ) )
        {
            $tokenProSchueler[$name] = $leistungenArray['tokenAnzahl'];
        }
        else
        {
            $tokenProSchueler[$name] += $leistungenArray['tokenAnzahl'];
        }
        $sqlSch = "select skuerzel, awardName from auszeichnung where skuerzel = '$sKuerzel' and saisonNummer = '$saisonNummer'";
        $awardTokenSchuler = mysqli_query($db, $sqlSch);
        for ( ; $awardTokenSchArray = mysqli_fetch_assoc($awardTokenSchuler); )
        {
            $awardName = $awardTokenSchArray['awardName'];
            $sqlAward = "select tokenLimit from award where name = '$awardName'";
            $awardToken = mysqli_query($db, $sqlAward);
            $awardTokenArray = mysqli_fetch_assoc($awardToken);
            $tokenProSchueler[$name] += $awardTokenArray['tokenLimit'];
        }
    }
    arsort($tokenProSchueler);

    return $tokenProSchueler;
}

function getTokenHighscoreProAwardAllTime( $aName )
{
    global $db;
    $tokenProSchueler = array();

    $nameToKurzel = getAllSchuelerNamesToKurzel();


    $sqlC = "select sKuerzel, tokenAnzahl from leistung where aName = '$aName'order by skuerzel ";
    $leistungen = mysqli_query($db, $sqlC);
    for (; $leistungenArray = mysqli_fetch_assoc($leistungen);)
    {
        $sKuerzel = $leistungenArray['skuerzel'];
        $name = $nameToKurzel[$sKuerzel];
        if ( !array_key_exists ( $name , $tokenProSchueler ) )
        {
            $tokenProSchueler[$name] = $leistungenArray['tokenAnzahl'];
        }
        else
        {
            $tokenProSchueler[$name] += $leistungenArray['tokenAnzahl'];
        }
        $sqlSch = "select skuerzel, awardName from auszeichnung where skuerzel = '$sKuerzel'";
        $awardTokenSchuler = mysqli_query($db, $sqlSch);
        for ( ; $awardTokenSchArray = mysqli_fetch_assoc($awardTokenSchuler); )
        {
            $awardName = $awardTokenSchArray['awardName'];
            $sqlAward = "select tokenLimit from award where name = '$awardName'";
            $awardToken = mysqli_query($db, $sqlAward);
            $awardTokenArray = mysqli_fetch_assoc($awardToken);
            $tokenProSchueler[$name] += $awardTokenArray['tokenLimit'];
        }
    }
    arsort($tokenProSchueler);

    return $tokenProSchueler;
}


/*
 * Alle funktionen für die Award highscores
 */

function getAwardHighscoreProSasison( $saisonNummer )
{
    global $db;

    $nameToKurzel = getAllSchuelerNamesToKurzel();

    $sqlC = "select skuerzel from auszeichnung where saisonNummer = '$saisonNummer' order by skuerzel ";
    $awards = mysqli_query($db, $sqlC);
    $out = array();
    for ( ; $awardsArray = mysqli_fetch_assoc($awards); )
    {
        $sKuerzel = $awardsArray['skuerzel'];
        $name = $nameToKurzel[$sKuerzel];
        if ( !array_key_exists ( $name , $out ) )
        {
            $out[$name] = 1;
        }
        else
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

    $nameToKurzel = getAllSchuelerNamesToKurzel();

    $sqlC = "select skuerzel from auszeichnung order by skuerzel";
    $awards = mysqli_query($db, $sqlC);
    $out = array();
    for ( ; $awardsArray = mysqli_fetch_assoc($awards); )
    {
        $sKuerzel = $awardsArray['skuerzel'];
        $name = $nameToKurzel[$sKuerzel];
        if ( !array_key_exists ( $name , $out ) )
        {
            $out[$name] = 1;
        }
        else
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

    $nameToKurzel = getAllSchuelerNamesToKurzel();

    $sqlC = "select skuerzel from auszeichnung where saisonNummer = '$saisonNummer' and awardName = '$aName' order by skuerzel";
    $awards = mysqli_query($db, $sqlC);
    $out = array();
    for ( ; $awardsArray = mysqli_fetch_assoc($awards); )
    {
        $sKuerzel = $awardsArray['skuerzel'];
        $name = $nameToKurzel[$sKuerzel];
        if ( !array_key_exists ( $name , $out ) )
        {
            $out[$name] = 1;
        }
        else
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

    $nameToKurzel = getAllSchuelerNamesToKurzel();

    $sqlC = "select skuerzel from auszeichnung where awardName = '$aName' order by skuerzel";
    $awards = mysqli_query($db, $sqlC);
    $out = array();
    for ( ; $awardsArray = mysqli_fetch_assoc($awards); )
    {
        $sKuerzel = $awardsArray['skuerzel'];
        $name = $nameToKurzel[$sKuerzel];
        if ( !array_key_exists ( $name , $out ) )
        {
            $out[$name] = 1;
        }
        else
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
    $tawardProSchueler = getAwardHighscoreProSasison($saisonNummer);
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