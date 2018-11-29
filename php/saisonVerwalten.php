<?php
/**
 * Created by PhpStorm.
 * User: Mir
 * Date: 21.11.2018
 * Time: 10:08
 */
$db = mysqli_connect('localhost', 'root', '2017lewiS661451', 'tvs_datenbank.sql');

function getSaisonNumbFromDate($date)
{
    global $db;
    $sqlC = "select startJahr, DATEPART(month, startDatumWSem) as startWSemMon , DATEPART(day, startDatumWSem) as startWSemDay, DATEPART(month, endDatumWSem) as endWSemMon , DATEPART(day, endDatumWSem) as endWSemDay, DATEPART(month, startDatumsSem) as startSSemMon , DATEPART(day, startDatumSSem) as startSSemDay, DATEPART(month, endDatumSSem) as endSSemMon , DATEPART(day, endDatumSSem) as endSSemDay  from saisonEinstellung";
    $result = mysqli_query($db, $sqlC);
    $resArray = mysqli_fetch_assoc($result);

    $dateNow = $date;
    $tempMon = date_format($dateNow, "M");

    if ( ($tempMon > 8 && $tempMon < 13) || ($tempMon < 3 && $tempMon > 0))
    {
        $year = date_format($dateNow, "Y");
    }
    elseif( ($tempMon > 2 && $tempMon < 8) )
    {
        $year = date_format($dateNow, "Y")+1;
    }
    else
    {
        return -1;
    }

    $monTemp = $resArray['startWSemMon'];
    $dayTemp = $resArray['startWSemDay'];

    $startWSem =date_create("'$year'-'$monTemp'-'$dayTemp'");

    $monTemp = $resArray['endWSemMon'];
    $dayTemp = $resArray['endWSemDay'];
    $endWSem =date_create("'$year'-'$monTemp'-'$dayTemp'");


    $monTemp = $resArray['startSSemMon'];
    $dayTemp = $resArray['startSSemDay'];
    $startSSem =date_create("'$year'-'$monTemp'-'$dayTemp'");

    $monTemp = $resArray['endSSemMon'];
    $dayTemp = $resArray['endSSemDay'];
    $endSSem =date_create("'$year'-'$monTemp'-'$dayTemp'");

    $startYear = $resArray['startJahr'];

    $res = date_format($dateNow, "Y") - $startYear;

    if ( $startWSem < $dateNow || $dateNow < $endWSem )
    {
        $out = 1 * $res;
    }
    elseif ( $startSSem < $dateNow || $dateNow < $endSSem )
    {
        $out = 2 * $res;
    }
    else
    {
        $out = -1;
    }

    return $out;
}

/**
 * jetztiges Datum
 */
function getSaisonNumb()
{
    return getSaisonNumbFromDate(date("Y-m-d"));
}

?>