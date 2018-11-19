<?php
session_start();
include("php\conLdap.php");

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


if(isset($_GET['login']))
{
	$userName = $_POST['inputBenutzername'];
	$password = $_POST['inputPassword'];
	$regularLehrer = "/^[a-zA-Z0-9_.+-]+@[tgm]+.[ac]+.[at]+$/";
	$regularSchueler = "/^[a-zA-Z0-9_.+-]+@[student]+.[tgm]+.[ac]+.[at]+$/";
	$ergSchueler = preg_match($regularSchueler , $userName);
	$ergLehrer = preg_match($regularLehrer, $userName);

	echo $ergLehrer;
	echo $ergSchueler;
	if ( $ergLehrer == 1 || $ergSchueler == 1 )
	{
		// mit ldap überprüfen ob 
		// wenn ja dannüberprüfen ob benutzer schon angelegt (wenn nicht anlegen)
		if ( checkUserPass($userName, $password))
		{
            $kuerzel = substr($userName, 0, strpos($userName, "@"));
            if ( $ergSchueler == 1 )
            {
                if (!checkIfUserInDatabase($kuerzel, 0)) {
                    $sqlC2 = 'insert into schueler ( kuerzel, sName, gesToken ) values ( "' . $kuerzel . '" , NULL, 0)'; // NULL beim vollen Namen weil ich noch nicht genau weiß wie ichs rausbekomme (glaube nicht mit dem ldap)
                    $result = mysqli_query($db, $sqlC2);
                }
            }
            elseif ( $ergLehrer == 1 )
            {
                if (!checkIfUserInDatabase($kuerzel, 1)) {
                    $sqlC2 = 'insert into lehrer ( kuerzel, sName, gesToken ) values ( "' . $kuerzel . '" , NULL, 0)'; // NULL beim vollen Namen weil ich noch nicht genau weiß wie ichs rausbekomme (glaube nicht mit dem ldap)
                    $result = mysqli_query($db, $sqlC2);
                }
            }

			
			$_SESSION['userName'] = $kuerzel;
			// weiterleiten auf die unterseite 
			if ( $ergSchueler == 1)
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
			echo "Email oder Passwort sind falsch!";
			// javascript ausgeben
			echo '<script type="text/javascript"> document.getElementById("navbarHeader").classList.add("show"); </script>';
		}
	}

	// error ausgeben
	else
	{
		echo "Email ist keine g&uumlltige TGM Email Adresse!";
		// javascript ausgeben
		echo '<script type="text/javascript"> document.getElementById("navbarHeader").classList.add("show"); </script>';
	}
}
?>