<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="2018/2019 Elm Creek Series Scoring.">
    <title>Nordic Race Analysis Tools</title>
    
    <link rel="stylesheet" href="https://unpkg.com/purecss@1.0.0/build/pure-min.css" integrity="sha384-" crossorigin="anonymous">
	<link rel="stylesheet" href="css/layouts/side-menu.css">
	<link rel="stylesheet" href="css/nratstyle.css">

</head>

<?php
// sort by total points in descending order
$POINTLIMIT = 5;

function pointsort($a, $b)
{
	global $POINTLIMIT;
	
	$a_tot = 0;
	rsort($a);
	$b_tot = 0;
	rsort($b);
	$count = 0;
	foreach ($a as $p)
	{
		$a_tot += $p;
		$count++;
		if ($count >= $POINTLIMIT)
			break;
	}
	$count = 0;
	foreach ($b as $p)
	{
		$b_tot += $p;
		$count++;
		if ($count >= $POINTLIMIT)
			break;
	}
	if ($a_tot == $b_tot)
	{
		return 0;
	}
	else
	{
		return ($a_tot > $b_tot) ? -1 : 1;
	}
}
?>

<?php
// Setting the navigation flag for the year passed as an argument

// include the utilities functions
require_once 'php/raceresultsutilities.php';

{
	if (isset($_REQUEST["year"]))
	{
		$year = $_REQUEST["year"];
	}
	else
	{
		die("Must specify year");
	}
	echo "<body id=\"nrat-elmcreekseries-$year\">";
}
?>


<div id="layout">
	<!-- /* Menu here */ -->
	<?php include "nratnav.php"; ?>

	<div id="main">
		
		<div class="header">
			<h1>Nordic Race Analysis Tools</h1>
			<?php
			// include the utilities functions
			require_once 'php/raceresultsutilities.php';

			{
				if (isset($_REQUEST["year"]))
				{
					$year = $_REQUEST["year"];
				}
				else
				{
					die("Must specify year");
				}
				echo "<h2>Elm Creek Race Series $year</h2>";
			}
			?>
		</div>

		<div class="container">
			<div class="content">
				These are the current series standings.  The standings are updated
				when new results are added.  The list of races and their associated
				scoring are listed below the standings table.
				<h3>Current Standings</h3>
				<?php
				// include the utilities functions
				require_once 'php/raceresultsutilities.php';

				{
					// For each race
					//   For each racer, add the points to their array
					if (isset($_REQUEST["style"]))
					{
						$style = $_REQUEST["style"];
					}
					else
					{
						die("Must specify style");
					}
					if (isset($_REQUEST["gender"]))
					{
						$gender = $_REQUEST["gender"];
					}
					else
					{
						die("Must specify gender");
					}
					if (isset($_REQUEST["year"]))
					{
						$year = $_REQUEST["year"];
					}
					else
					{
						die("Must specify year");
					}
					
					
					// Select just the elm creek freestyle races
					$start_year = $year - 1;
					$q = "SELECT EventView.*,
								EventView.EventID AS eid
								FROM EventView
								WHERE FullName LIKE '%Elm Creek%' AND
								EventDate >= \"$start_year-9-1\" AND
								EventDate < \"$year-9-1\" AND
								Technique=\"$style\"";
					$result = RaceResultsQuery($q);

					// Now for each race we want to do a bunch of things
					$race_number = 0;
					$results_display = "";
					$racer_points = [];
					while ($race = $result->fetch_assoc())
					{
						// get the id of the next race
						$raceid = $race["EventID"];
						
						// get the results for the race
						$race_results = RaceResultsQuery("SELECT
							Racer.RacerID,
							Racer.Gender,
							CONCAT(Racer.FirstName,' ',Racer.LastName) AS \"Skier Name\",
							TIME_FORMAT(SEC_TO_TIME(Result.TimeInSec),'%H:%i:%s') AS Time
							FROM Racer INNER JOIN Result ON
							Racer.RacerID = Result.RacerId WHERE
							Result.EventID=$raceid AND
							Racer.Gender=\"$gender\" ORDER BY Result.Overall");

						// We are going to ouptut a table of the results for this
						// race and also add up the points
						$racename = $race["FullName"];
						$results_display .= "<b>$racename</b><br>";
						$results_display .= "<table>";
						//header
						$results_display .= "<tr>";
						$header = $race_results->fetch_fields();
						$results_display .= "<th><b>OAP</b></th><th><b>GP</b></th><th><b>Points</b></th>";
						foreach ($header as $col)
						{
							if (($col->name != "EventID") && ($col->name != "RacerID"))
							{
								$results_display .= "<th><b>$col->name</b></th>";
							}
						}
						$results_display .= "</tr>";
						// body
						$lastdate = null;
						$oap = 0;
						$mp = 0;
						$fp = 0;
						while ($row = $race_results->fetch_assoc())
						{
							$results_display .= "<tr>";
							$oap += 1;
							$results_display .= "<td>$oap</td>";
							
							if ($row["Gender"] == "M")
							{
								$mp += 1;
								$results_display .= "<td>$mp</td>";
							}
							else
							{
								$fp += 1;
								$results_display .= "<td>$fp</td>";
							}
							
							if ($race["EventDate"] == "2019-12-18")
							{
								// This is the Fulton Team Night, everyone gets 100 points
								$points = 100;
							}
							else
							{
								if ($row["Gender"] == "M")
								{
									$points = 100 - ($mp-1);
								}
								else if ($row["Gender"] == "F")
								{
									$points = 100 - ($fp-1);
								}
							}
							$results_display .= "<td>$points</td>";

							// Sum the points... first make sure we have that
							// racer.  If we don't add them, then add the points
							if (!array_key_exists($row["Skier Name"], $racer_points))
							{
								// skier wasn't in the list
								$racer_points[$row["Skier Name"]] = [];
							}
							$racer_points[$row["Skier Name"]][] = $points;

							// fill the rest of the table
							foreach ($row as $key => $field)
							{
								if ($key != "RacerID")
								{
									$results_display .= "<td>$field</td>";
								}
							}
							$results_display .= "</tr>";
							
						}
						$results_display .= "</table><br><br>";
						
						$race_number += 1;
					}
					
					// Display a table of the points and total
					echo "<table>";
					echo "<tr>";
					echo "<th><b>Skier</b></th>";
					echo "<th><b>Points</b></th>";
					echo "<th><b>Scored Points</b></th>";
					echo "<th><b>Total Points</b></th>";
					echo "</tr>";
					uasort($racer_points,'pointsort');
					foreach ($racer_points as $name => $point)
					{
						echo "<tr>";
						echo "<td>$name</td>";
						rsort($point);
						$count = 0;
						echo "<td>";
						foreach ($point as $p)
						{
							if ($count < $POINTLIMIT)
							{
								echo "<b>";
							}
							echo "$p ";
							if ($count < $POINTLIMIT)
							{
								echo "</b>";
							}
							$count++;
						}
						echo "</td>";
						$scored = 0;
						$score_count = count($point);
						for ($i = 0; ($i < $POINTLIMIT) && $i < $score_count; $i++)
						{
							$scored += $point[$i];
						}
						echo "<td>$scored</td>";
						$total = array_sum($point);
						echo "<td>$total</td>";
						echo "</tr>";
					}
					echo "</table>";
					
					echo "<br><br>";
					
					echo "<h3>Individual Race Results</h3>";

					echo $results_display;
				}
				?>
			</div>
			
			<!-- /* Sidebar here */ -->
			<?php include "nratsidebar.php"; ?>
		</div>
	</div>
</div>

<script src="ui.js"></script>

</body>
</html>
