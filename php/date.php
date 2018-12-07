<?php
    function getKuerzelProMon( $mon )
    {
        $out = "";
        switch ($mon)
        {
            case 1:
                $out = "Jän";
                break;
            case 2:
                $out = "Feb";
                break;
            case 3:
                $out = "Mär";
                break;
            case 4:
                $out = "Apr";
                break;
            case 5:
                $out = "Mai";
                break;
            case 6:
                $out = "Jun";
                break;
            case 7:
                $out = "Jul";
                break;
            case 8:
                $out = "Aug";
                break;
            case 9:
                $out = "Sep";
                break;
            case 10:
                $out = "Okt";
                break;
            case 11:
                $out = "Nov";
                break;
            case 12:
                $out = "Dez";
                break;
        }
        return $out;
    }
?>