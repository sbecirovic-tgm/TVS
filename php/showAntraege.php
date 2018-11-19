<?php
/**
 * Created by PhpStorm.
 * User: Mir
 * Date: 19.11.2018
 * Time: 10:57
 */
session_start();

$db = mysqli_connect('localhost', 'root', '2017lewiS661451', 'tvs_datenbank');
$_SESSION['userName'] = 'swahl'; // muss dann nacher noch angepasst werden, nach dem Login
$userName =$_SESSION['userName'];
function printEvents ()
{
	global $db, $userName;

	$sqlC = 'select * from anfrage where skuerzel = "' . $userName . '" order by datum desc';
	$antraege = mysqli_query($db, $sqlC);

	// Daten holen und in variablen speicher
	$antraegeInfo = array();


	echo '<table id=antraegeListe name=antraegeListe>';
	echo '<tr>';
		echo '<th>Betreff</th>';
		echo '<th>Award-Type</th>';
		echo '<th>Geforderte Token</th>';
		echo '<th>Status</th>';
		echo '<th>Datum</th>';
	echo '</tr>';

		for( $i = 0; $antraege_array = mysqli_fetch_assoc($antraege); $i++)
		{
			echo '<tr>';
			$antraegeInfo[$i] = array();
			$antraegeInfo[$i]['datum'] =  $antraege_array['datum'];
			$antraegeInfo[$i]['zeit'] = $antraege_array['zeit'];
			$antraegeInfo[$i]['aName'] =  $antraege_array['aName'];
			$antraegeInfo[$i]['eName'] = $antraege_array['eName'];
			$antraegeInfo[$i]['eDatum'] = $antraege_array['eDatum'];
			$antraegeInfo[$i]['untName'] = $antraege_array['untName'];
			$antraegeInfo[$i]['tokenAnzahl'] = $antraege_array['tokenAnzahl'];
			$antraegeInfo[$i]['beschreibung'] = $antraege_array['beschreibung'];
			$antraegeInfo[$i]['betreff'] = $antraege_array['betreff'];
			$antraegeInfo[$i]['wirdBewilligt'] = $antraege_array['wirdBewilligt'];
			$antraegeInfo[$i]['kommentar'] = $antraege_array['kommentar'];

			echo '<td>' . $antraegeInfo[$i]['betreff'] . '</td>';
			echo '<td>' . $antraegeInfo[$i]['aName'] . '</td>';
			echo '<td>' . $antraegeInfo[$i]['tokenAnzahl'] . '</td>';
			if ( $antraegeInfo[$i]['wirdBewilligt'] )
			{
				echo '<td>Bewilligt</td>';
			}
			else
			{
				if ( $antraegeInfo[$i]['wirdBewilligt'] == NULL )
				{
					echo '<td>In Bearbeitung</td>';
				}
				else
				{
					echo '<td>Abgelehnt</td';
				}
			}
			echo '<td>' . $antraegeInfo[$i]['datum'] . '</td>';

			echo '</tr>';
		}


	echo '</table>';
}

if(isset($_GET['requestToken']))
{
	//insert into anfrage( datum, zeit, aName, superkuerzel, lehrerKuerzel, eName, eDatum, untName, skuerzel, tokenAnzahl, beschreibung, betreff, wirdBewilligt, kommentar ) values (CURDATE(), CURTIME(), NULL, NULL, NULL, NULL, NULL, NULL, 'swahl', '1', 'Test ist das', 'Test', NULL, NULL);
	$awardTyp = $_POST['awardTyp'];
	$tokenAnzahl = $_POST['tokenanzahl'];
	$betreff = $_POST['betreff'];
	$beschreibung = $_POST['beschreibung'];
	// inserten, alles andere auf NULL setzten --> bei der normalen abfrage nicht notwendig
	$sqlC = "insert into anfrage( datum, zeit, aName, superkuerzel, lehrerKuerzel, eName, eDatum, untName, skuerzel, tokenAnzahl, beschreibung, betreff, wirdBewilligt, kommentar ) values (CURDATE(), CURTIME(), '$awardTyp', NULL, NULL, NULL, NULL, NULL, 'swahl', '$tokenAnzahl', '$beschreibung', '$betreff', NULL, NULL)";

    $result = mysqli_query($db, $sqlC);
}

?>
