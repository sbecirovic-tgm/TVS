<?php
/**
 * Created by PhpStorm.
 * User: Mir
 * Date: 21.11.2018
 * Time: 10:08
 */
$db = mysqli_connect('localhost', 'tokenverwaltung', '1234', 'tvs_datenbank');

function getSaisonNumbFromDate($date)
{
    // 2019-02-26
    global $db;
    $sqlC = "select startJahr, MONTH(startDatumWSem) as startWSemMon , DAY(startDatumWSem) as startWSemDay, MONTH(endDatumWSem) as endWSemMon , DAY(endDatumWSem) as endWSemDay, MONTH(startDatumsSem) as startSSemMon , DAY(startDatumSSem) as startSSemDay, MONTH(endDatumSSem) as endSSemMon , DAY(endDatumSSem) as endSSemDay  from saisonEinstellung";
    $result = mysqli_query($db, $sqlC);
    $resArray = mysqli_fetch_assoc($result);

    $dateNow = $date;
    $time = strtotime($dateNow);
    $dateNow = date('Y-m-d', $time);
    $dateNow = date_create($dateNow);
    $tempMon = date_format($dateNow, "m");

    if ( ($tempMon > 8 && $tempMon <= 12) || ($tempMon < 2 && $tempMon >= 1))
    {
        $year = date_format($dateNow, "Y");
        if ($tempMon < 2 && $tempMon >= 1)
        {
            $year = $year - 1;
        }
    }
    elseif( ($tempMon >= 2 && $tempMon < 9) )
    {
        $year = date_format($dateNow, "Y");
    }
    else
    {
        return -1;
    }




    $monTemp = $resArray['startWSemMon'];
    $dayTemp = $resArray['startWSemDay'];

    $startWSem =date_create("$year-$monTemp-$dayTemp");

    $monTemp = $resArray['endWSemMon'];
    $dayTemp = $resArray['endWSemDay'];
    $endWSem =date_create("$year-$monTemp-$dayTemp");

    $monTemp = $resArray['startSSemMon'];
    $dayTemp = $resArray['startSSemDay'];
    $startSSem =date_create("$year-$monTemp-$dayTemp");

    $monTemp = $resArray['endSSemMon'];
    $dayTemp = $resArray['endSSemDay'];
    $endSSem =date_create("$year-$monTemp-$dayTemp");

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
    $date = date("Y-m-d");
    return getSaisonNumbFromDate($date);
}

?>