<?php
session_start();
include("php\conLdap.php");
$db = mysqli_connect('localhost', 'root', '2017lewiS661451', 'tvs_datenbank');

function checkIfUserInDatabase( $userName )
{
	global $db;
	$sqlC = 'select count(kuerzel) as anzahl from schueler where kuerzel = "' . $userName . '"';
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


if(isset($_GET['login']))
{
	$userName = $_POST['inputBenutzername'];
	$password = $_POST['inputPassword'];
	$regularLehrer = "/^[a-zA-Z0-9_.+-]+@[tgm]+.[ac]+.[at]+$/";
	$regularSchueler = "/^[a-zA-Z0-9_.+-]+@[student]+.[tgm]+.[ac]+.[at]+$/";
	$ergSchueler = preg_match($regularSchueler , $userName);
	$ergLehrer = preg_match($regularLehrer, $userName);

	if ( !($ergLehrer || $ergSchueler) )
	{
		// mit ldap überprüfen ob 
		// wenn ja dannüberprüfen ob benutzer schon angelegt (wenn nicht anlegen)
		if ( checkUserPass($userName, $password))
		{
			$kuerzel = substr($userName, 0, strpos($userName, "@")-1);
			if ( !checkIfUserInDatabase($userName))
			{
				$sqlC2 = 'insert into schueler ( kuerzel, sName, gesToken ) values ( "' . $kuerzel . '" , NULL, 0);'; // NULL beim vollen Namen weil ich noch nicht genau weiß wie ichs rausbekomme (glaube nicht mit dem ldap)
				$result = mysqli_query($db, $sqlC2);
			}

			
			$_SESSION['userName'] = $kuerzel;
			// weiterleiten auf die unterseite 
			if ( $ergSchueler )
			{
				header("Refresh:0; url=userAnsicht\startseite.html");
			}
			else
				//header("Refresh:0; url=userAnsicht\startseite.html");
			}
			
		}
		// error ausgeben
		else
		{
			echo "Email oder Passwort sind falsch!";
			// javascript ausgeben
			echo '<script type="text/javascript"> document.getElementById("navbarHeader").classList.add("show"); </script>';
		}
	}

	// error ausgeben
	else
	{
		echo "Email ist keine gültige TGM Email Adresse!";
		// javascript ausgeben
		echo '<script type="text/javascript"> document.getElementById("navbarHeader").classList.add("show"); </script>';
	}
}
?>