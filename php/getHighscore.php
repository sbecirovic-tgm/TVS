<?php
/**
 * Created by PhpStorm.
 * User: Mir
 * Date: 22.11.2018
 * Time: 13:28
 */

$db = mysqli_connect('localhost', 'root', '2017lewiS661451', 'tvs_datenbank.sql');


/*
 * Alle funktionen für die Token highscores
 */

function getTokenHighscoreProSasison( $saisonNummer )
{
    global $db;
    $tokenProSchueler = array();
    $sqlC = "select sKuerzel, tokenAnzahl from leistung where saisonNummer = '$saisonNummer' order by skuerzel ";
    $leistungen = mysqli_query($sqlC, $db);
    for (; $leistungenArray = mysqli_fetch_assoc($leistungen);)
    {
        $sKuerzel = $leistungenArray['skuerzel'];
        if ( array_key_exists ( $sKuerzel , $tokenProSchueler ) )
        {
            $tokenProSchueler[$sKuerzel] = $leistungenArray['tokenAnzahl'];
        }
        else
        {
            $tokenProSchueler[$sKuerzel] += $leistungenArray['tokenAnzahl'];
        }
        $sqlSch = "select skuerzel, awardName from auszeichnung where skuerzel = '$sKuerzel' and saisonNummer = '$saisonNummer'";
        $awardTokenSchuler = mysqli_query($sqlSch, $db);
        for ( ; $awardTokenSchArray = mysqli_fetch_assoc($awardTokenSchuler); )
        {
            $awardName = $awardTokenSchArray['awardName'];
            $sqlAward = "select tokenLimit from award where name = '$awardName'";
            $awardToken = mysqli_query($sqlAward, $db);
            $awardTokenArray = mysqli_fetch_assoc($awardToken);
            $tokenProSchueler[$sKuerzel] += $awardTokenArray['tokenLimit'];
        }
    }
    arsort($tokenProSchueler);

    return $tokenProSchueler;
}

function getTokenHighscoreThisSaison()
{
    include ("saisonVerwalten.php");
    $saisonNummer = getSaisonNumb();
    return getTokenHighscoreProSasison($saisonNummer);
}

function getTokenHighscoreThisSaisonLimit($limit)
{
    global $db;
    include ("saisonVerwalten.php");
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
        if ( $i == $limit )
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
    $sqlC = "select sKuerzel, tokenAnzahl from leistung order by sKuerzel ";
    $leistungen = mysqli_query($sqlC, $db);
    for (; $leistungenArray = mysqli_fetch_assoc($leistungen);)
    {
        $sKuerzel = $leistungenArray['sKuerzel'];
        if ( array_key_exists ( $sKuerzel , $tokenProSchueler ) )
        {
            $tokenProSchueler[$sKuerzel] = $leistungenArray['tokenAnzahl'];
        }
        else
        {
            $tokenProSchueler[$sKuerzel] += $leistungenArray['tokenAnzahl'];
        }
        $sqlSch = "select skuerzel, awardName from auszeichnung where skuerzel = '$sKuerzel'";
        $awardTokenSchuler = mysqli_query($sqlSch, $db);
        for ( ; $awardTokenSchArray = mysqli_fetch_assoc($awardTokenSchuler); )
        {
            $awardName = $awardTokenSchArray['awardName'];
            $sqlAward = "select tokenLimit from award where name = '$awardName'";
            $awardToken = mysqli_query($sqlAward, $db);
            $awardTokenArray = mysqli_fetch_assoc($awardToken);
            $tokenProSchueler[$sKuerzel] += $awardTokenArray['tokenLimit'];
        }
    }
    arsort($tokenProSchueler);

    return $tokenProSchueler;
}

function getTokenHighscoreProAwardPerSaison( $aName, $saisonNummer )
{
    global $db;
    $tokenProSchueler = array();
    $sqlC = "select sKuerzel, tokenAnzahl from leistung where saisonNummer = '$saisonNummer' and aName = '$aName'order by skuerzel ";
    $leistungen = mysqli_query($sqlC, $db);
    for (; $leistungenArray = mysqli_fetch_assoc($leistungen);)
    {
        $sKuerzel = $leistungenArray['skuerzel'];
        if ( array_key_exists ( $sKuerzel , $tokenProSchueler ) )
        {
            $tokenProSchueler[$sKuerzel] = $leistungenArray['tokenAnzahl'];
        }
        else
        {
            $tokenProSchueler[$sKuerzel] += $leistungenArray['tokenAnzahl'];
        }
        $sqlSch = "select skuerzel, awardName from auszeichnung where skuerzel = '$sKuerzel' and saisonNummer = '$saisonNummer'";
        $awardTokenSchuler = mysqli_query($sqlSch, $db);
        for ( ; $awardTokenSchArray = mysqli_fetch_assoc($awardTokenSchuler); )
        {
            $awardName = $awardTokenSchArray['awardName'];
            $sqlAward = "select tokenLimit from award where name = '$awardName'";
            $awardToken = mysqli_query($sqlAward, $db);
            $awardTokenArray = mysqli_fetch_assoc($awardToken);
            $tokenProSchueler[$sKuerzel] += $awardTokenArray['tokenLimit'];
        }
    }
    arsort($tokenProSchueler);

    return $tokenProSchueler;
}

function getTokenHighscoreProAwardAllTime( $aName )
{
    global $db;
    $tokenProSchueler = array();
    $sqlC = "select sKuerzel, tokenAnzahl from leistung where aName = '$aName'order by skuerzel ";
    $leistungen = mysqli_query($sqlC, $db);
    for (; $leistungenArray = mysqli_fetch_assoc($leistungen);)
    {
        $sKuerzel = $leistungenArray['skuerzel'];
        if ( array_key_exists ( $sKuerzel , $tokenProSchueler ) )
        {
            $tokenProSchueler[$sKuerzel] = $leistungenArray['tokenAnzahl'];
        }
        else
        {
            $tokenProSchueler[$sKuerzel] += $leistungenArray['tokenAnzahl'];
        }
        $sqlSch = "select skuerzel, awardName from auszeichnung where skuerzel = '$sKuerzel'";
        $awardTokenSchuler = mysqli_query($sqlSch, $db);
        for ( ; $awardTokenSchArray = mysqli_fetch_assoc($awardTokenSchuler); )
        {
            $awardName = $awardTokenSchArray['awardName'];
            $sqlAward = "select tokenLimit from award where name = '$awardName'";
            $awardToken = mysqli_query($sqlAward, $db);
            $awardTokenArray = mysqli_fetch_assoc($awardToken);
            $tokenProSchueler[$sKuerzel] += $awardTokenArray['tokenLimit'];
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
    $tokenProSchueler = array();
    $sqlC = "select skuerzel from auszeichnung where saisonNummer = '$saisonNummer' order by skuerzel ";
    $awards = mysqli_query($sqlC, $db);
    $out = array();
    for ( ; $awardsArray = mysqli_fetch_assoc($awards); )
    {
        $sKuerzel = $awardsArray['skuerzel'];
        if ( array_key_exists ( $sKuerzel , $out ) )
        {
            $out[$sKuerzel] = 1;
        }
        else
        {
            $out[$sKuerzel] += 1;
        }
    }

    arsort($out);
    return $out;
}

function getAwardHighscoreAllTime()
{
    global $db;
    $tokenProSchueler = array();
    $sqlC = "select skuerzel from auszeichnung order by skuerzel ";
    $awards = mysqli_query($sqlC, $db);
    $out = array();
    for ( ; $awardsArray = mysqli_fetch_assoc($awards); )
    {
        $sKuerzel = $awardsArray['skuerzel'];
        if ( array_key_exists ( $sKuerzel , $out ) )
        {
            $out[$sKuerzel] = 1;
        }
        else
        {
            $out[$sKuerzel] += 1;
        }
    }

    arsort($out);
    return $out;
}

function getAwardHighscoreProAwardSaision( $saisonNummer, $aName )
{
    global $db;
    $tokenProSchueler = array();
    $sqlC = "select skuerzel from auszeichnung where saisonNummer = '$saisonNummer' and awardName = '$aName' order by skuerzel";
    $awards = mysqli_query($sqlC, $db);
    $out = array();
    for ( ; $awardsArray = mysqli_fetch_assoc($awards); )
    {
        $sKuerzel = $awardsArray['skuerzel'];
        if ( array_key_exists ( $sKuerzel , $out ) )
        {
            $out[$sKuerzel] = 1;
        }
        else
        {
            $out[$sKuerzel] += 1;
        }
    }

    arsort($out);
    return $out;
}

function getAwardHighscoreProAwardAllTime( $aName )
{
    global $db;
    $tokenProSchueler = array();
    $sqlC = "select skuerzel from auszeichnung where awardName = '$aName' order by skuerzel";
    $awards = mysqli_query($sqlC, $db);
    $out = array();
    for ( ; $awardsArray = mysqli_fetch_assoc($awards); )
    {
        $sKuerzel = $awardsArray['skuerzel'];
        if ( array_key_exists ( $sKuerzel , $out ) )
        {
            $out[$sKuerzel] = 1;
        }
        else
        {
            $out[$sKuerzel] += 1;
        }
    }

    arsort($out);
    return $out;
}

?>