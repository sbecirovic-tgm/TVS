<?php
session_start();
include("php\conLdap.php");
include("php\userCheck.php");
$db = mysqli_connect('localhost', 'tokenverwaltung', '1234', 'tvs_datenbank');

if(isset($_GET['login']))
{
	$userName = $_POST['inputBenutzername'];
	$password = $_POST['inputPassword'];
	$regularLehrer = "/^[a-zA-Z0-9_.+-]+@[tgm]+.[ac]+.[at]+$/";
	$regularSchueler = "/^[a-zA-Z0-9_.+-]+@[student]+.[tgm]+.[ac]+.[at]+$/";
	$ergSchueler = preg_match($regularSchueler , $userName);
	$ergLehrer = preg_match($regularLehrer, $userName);

	if ( $ergLehrer == 1 || $ergSchueler == 1 )
	{
		// mit ldap �berpr�fen ob 
		// wenn ja dann�berpr�fen ob benutzer schon angelegt (wenn nicht anlegen)
		if ( checkUserPass($userName, $password))
		{
            $kuerzel = substr($userName, 0, strpos($userName, "@"));
            if ( $ergSchueler == 1 )
            {
                if (!checkIfUserInDatabase($kuerzel, 0)) {
                    insertSchueler($kuerzel, NULL);
                }
            }
            elseif ( $ergLehrer == 1 )
            {
                if (!checkIfUserInDatabase($kuerzel, 1)) {
                    insertLehrer($kuerzel, NULL);
                }
            }

			
			$_SESSION['userName'] = $kuerzel;
			// weiterleiten auf die unterseite 
			if ( $ergSchueler == 1  && $ergLehrer != 1 )
			{
				header("Refresh:0; url=userAnsicht\startseite.html");
			}
			else
			{
			    if ( checkIfUserIsSuperUser($kuerzel))
                {
                    header("Refresh:0; url=headAnsicht\startseite.html");
                }
                else
                {
                    header("Refresh:0; url=adminAnsicht\startseite.html");
                }
			}
			
		}
		// error ausgeben
		else
		{
			echo "<div class='falsch'>Email oder Passwort sind falsch!</div>";
			// javascript ausgeben
			echo '<script type="text/javascript"> document.getElementById("navbarHeader").classList.add("show"); </script>';
		}
	}

	// error ausgeben
	else
	{
		echo "<div class='falsch'>Email ist keine g&uumlltige TGM Email Adresse!</div>";
		// javascript ausgeben
		echo '<script type="text/javascript"> document.getElementById("navbarHeader").classList.add("show"); </script>';
	}
}
?>