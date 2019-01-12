<?php
session_start();
include_once("php\conLdap.php");
include_once("php\userCheck.php");
$db = mysqli_connect('localhost', 'tokenverwaltung', '1234', 'tvs_datenbank');

if(isset($_GET['login']))
{
	$userName = $_POST['inputBenutzername'];
	$password = $_POST['inputPassword'];

    // mit ldap �berpr�fen ob
    // wenn ja dann�berpr�fen ob benutzer schon angelegt (wenn nicht anlegen)
    $result = checkUserPass($userName, $password);
    if ( $result[0] )
    {
        $kuerzel = $userName;
        if ( !$result[1] )
        {
            if (!checkIfUserInDatabase($kuerzel, 0)) {
                insertSchueler($kuerzel, $result[2]);
            }
        }
        else
        {
            if (!checkIfUserInDatabase($kuerzel, 1)) {
                insertLehrerShort($kuerzel, $result[2]);
            }
        }


        $_SESSION['userName'] = $kuerzel;
        //$_SESSION['schueler']
        // 0: Normaler Lehrer
        // 1: Schüler
        // -1: Headadmin
        // weiterleiten auf die unterseite

        if ( !$result[1] )
        {
            header("Refresh:0; url=userAnsicht\startseite.html");
            $_SESSION['schueler'] = 1;
        }
        else
        {
            if ( checkIfUserIsSuperUser($kuerzel))
            {
                header("Refresh:0; url=headAnsicht\startseite.html");
                $_SESSION['schueler'] = -1;
            }
            else
            {
                header("Refresh:0; url=adminAnsicht\startseite.html");
                $_SESSION['schueler'] = 0;
            }
        }

    }
    // error ausgeben
    else
    {
        echo "<div class='falsch'>Ungültige Anmeldedaten. Versuchen Sie es noch einmal!</div>";
        // javascript ausgeben
        echo '<script type="text/javascript"> document.getElementById("navbarHeader").classList.add("show"); </script>';
    }
}
?>