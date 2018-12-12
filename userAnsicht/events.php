<?php

session_start();
$userName = $_SESSION['userName'];

function printEvents()
{
    include_once ("../php/eventsVerwalten.php");
    $events = listAllEvents();
    /*
     * $out[$i]['name'] = $event_array['name'];
        $out[$i]['datum'] = $event_array['datum'];
        $out[$i]['superKuerzel'] = $event_array['superKuerzel'];
        $out[$i]['lKuerzel'] = $event_array['lKuerzel'];
        $out[$i]['aName'] = $event_array['aName'];
        $out[$i]['beschreibung'] = $event_array['beschreibung'];
     */
    foreach ($events as $event)
    {
        // als datum YYYY-MM-DD
        $date = strtotime($event['datum']);
        $day = date("d", $date);
        $mon = date("M", $date);

        $name = $event['name'];
        $aName = $event['aName'];

        if ( $date > time() )
        {
            $badge = '<span class="badge badge-primary">'.$day.'</span>';
        }
        else
        {
            $badge = '<span class="badge badge-secondary">'.$day.'</span>';
        }
        echo '<div class="row row-striped"><div class="col-2 text-right"><h1 class="display-4">'.$badge.'</h1>
                    <h2>'.$mon.'</h2>
                </div>
                <div class="col-10">
                    <h3 class="text-uppercase"><strong>'.$name.'</strong></h3>
                    <ul class="list-inline">
                        <li class="list-inline-item"><i class="fa fa-clock-o" aria-hidden="true"></i> Awardtyp: '.$aName.'</li>
                    </ul>
                    <p>'.$aName.'</p>
                </div>
            </div>';
    }
}


if (isset($_GET['eventAnzeigen']))
{
    $_SESSION['eventEintragung'] = NULL;
    header("Refresh:0; url=events.html");
}
if (isset($_GET['highscoreAnzeigen']))
{
    $_SESSION['highscore'] = NULL;
    header("Refresh:0; url=highscore.html");
}

if(isset($_GET['logout']))
{
    include_once("../php/userCheck.php");
    logout();
}
?>