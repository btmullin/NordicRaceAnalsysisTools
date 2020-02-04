<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Prediction of an individual skier at the Birkie.">
	<meta name="author" content="Ben Mullin">
    <title>Nordic Race Analysis Tools</title>
    
    <link rel="stylesheet" href="https://unpkg.com/purecss@1.0.0/build/pure-min.css" integrity="sha384-" crossorigin="anonymous">
	<link rel="stylesheet" href="css/layouts/side-menu.css">
	<link rel="stylesheet" href="css/nratstyle.css">

</head>

<?php
/*
 * Here are some defines for the predictor tool so they can be changed here
 * rather than buried in the code.
 */

// Set the date limit when going back for predictions 
//$event_date_limit = "2019-07-01";  // '19 and later
$event_date_limit = "2018-07-01";  // '18/'19 and later
//$event_date_limit = "2017-07-01";  // '17/'18 season and later
//$event_date_limit = "2016-07-01";  // '16/'17 and '17/'17 seasons
//$event_date_limit = "2000-01-01";  // essentially forever

$birkie_id = 174; // '18 freestyle
$birkie_year = 2019;
$waves = array(		array("time"=>11495, "name"=>"Wave 1"), // 3:11:35
					array("time"=>12609, "name"=>"Wave 2"), // 3:30:09
					array("time"=>13643, "name"=>"Wave 3"), // 3:47:23
					array("time"=>14677, "name"=>"Wave 4"), // 4:04:37
					array("time"=>16109, "name"=>"Wave 5"), // 4:28:29
					array("time"=>18256, "name"=>"Wave 6"), // 5:04:16
					array("time"=>99999, "name"=>"Wave 7"), // per 2020 stds
					array("time"=>99999, "name"=>"Wave 8")); // new skiers
//$birkie_id = 126; // '18 freestyle
//$birkie_year = 2018;
//$waves = array(		array("time"=>10657, "name"=>"Wave 1"), // 2:57:37
//					array("time"=>11612, "name"=>"Wave 2"), // 3:13:32
//					array("time"=>12568, "name"=>"Wave 3"), // 3:29:28
//					array("time"=>13523, "name"=>"Wave 4"), // 3:45:23
//					array("time"=>14846, "name"=>"Wave 5"), // 4:07:26
//					array("time"=>16978, "name"=>"Wave 6"), // 4:42:58
//					array("time"=>20211, "name"=>"Wave 7"), // 5:36:51
//					array("time"=>99999, "name"=>"Wave 8")); // per 2019 stds
//$birkie_id = 52; // '16 freestyle
//$birkie_year = 2016;
//$waves = array(		array("time"=>11116, "name"=>"Wave 1"),
//					array("time"=>12396, "name"=>"Wave 2"),
//					array("time"=>14059, "name"=>"Wave 3"),
//					array("time"=>14585, "name"=>"Wave 4"),
//					array("time"=>16390, "name"=>"Wave 5"),
//					array("time"=>19770, "name"=>"Wave 6"),
//					array("time"=>99999, "name"=>"Wave 7"));

// Percent back limit
$pb_limit = 40;

// Wave qualifiers
$elite = array("M"=>200, "F"=>60);

function GetEliteWaveCutoff($event_id, $gender)
{
	global $elite;
	
	$query = "SELECT 
				Result.*,
				Racer.*
			FROM
				Result,
				Racer
			WHERE 
				Result.EventID=$event_id AND
				Racer.RacerID=Result.RacerID AND
				Racer.Gender=\"$gender\"
			ORDER BY
				Result.TimeInSec
			LIMIT
				$elite[$gender]";
	$result = RaceResultsQuery($query);
	$time = 0;
	while ($row = $result->fetch_assoc())
	{
		$time = $row["TimeInSec"];
	}
	return $time;
}

?>


<body id="nrat-birkiepredictor">

<div id="layout">
	<!-- /* Menu here */ -->
	<?php include "nratnav.php"; ?>

	<div id="main">
		<div class="header">
			<h1>Nordic Race Analysis Tools</h1>
			<h2>Birkie Predictor</h2>
		</div>

		<div class="container">
			<div class="content">
				<?php
				// include the utilities functions
				require_once 'php/raceresultsutilities.php';
				// If this isn't a post, then we should display the form first
				if (!isset($_POST['submit']) AND !isset($_REQUEST['rid']))
				{
					// This isn't a post so we should display the form
					
					LogActivity(["Selection"]);
				?>
					<p>
					This tool finds all of the selected skier's results from the
					2018/2019 or 2019/2020 racing seasons and uses those results
					to predict an equivalent performance at the 2019 50k Birkie
					Freestyle race.
					</p>
					<p>
					This is done using the Compare Races tool found on this site.
					Essentially all racers who did both races are used to build
					a mathematical model to translate results from one race to
					another.
					</p>
					<p>
					Of course this is pretty tricky for many reasons.  Who
					missed the wax, who was sick, the races are approaching two
					years apart now, one race might be a 5 kilometer classic race
					and the 2019 Birkie Freestlye was clearly not.
					</p>
					<p>
					That isn't to say it is all complete crap, but take it with a
					grain of salt.  Does the prediction match how you feel your
					training has been going?  How about a subjective feel of your
					results lately?
					</p>
					<p>
					As they say, the best predictor is to actually go out and race,
					but until February 22rd 2020, all you can do is train, race,
					and... try to predict.
					</p>
					<form class=" pure-form pure-form-aligned" action="birkiepredictor.php" method="post">
					<div class="pure-control-group">
						<label for="racer">Racer</label>
						<select id="racer" name='id'>
							<?php
							$result = RaceResultsQuery('SELECT * FROM Racer Where RacerID=PrimaryRacerID ORDER BY FirstName, LastName');
							while ($row = $result->fetch_array())
							{
								$id = $row["RacerID"];
								$fn = $row["FirstName"];
								$ln = $row["LastName"];
								echo "<option value=".$id.">".$fn." ".$ln."</option>";
							}
							?>
						</select><br>
					</div>
					<div class="pure-control-group">
						<label for="strength">Stregth of Prediction Limit (Optional)</label>
						<input id="strength" type="number" name="Strength"> Minimum strength (0-100)<br>
					</div>
					<div class="pure-controls">
						<input type="submit" value="Birkie Prediction" name="submit">
					</div>
					</form>
				<?php
				}
				else
				{
					$strength_limit = 0;

					if ($_SERVER["REQUEST_METHOD"] == "POST") {
					  $RacerID = $_POST["id"];
					  if ($_POST["Strength"] == "")
					  {
						  $strength_limit = 0;
					  }
					  else
					  {
						  $strength_limit = $_POST["Strength"];
					  }
					}
					else if (isset($_REQUEST["rid"]))
					{
						$RacerID = $_REQUEST["rid"];
						
						if (isset($_REQUEST["strength"]))
						{
							$strength_limit = $_REQUEST["strength"];
						}
					}
					else
					{
						die("No Racer Selected");
					}
					
					LogActivity([$RacerID]);

					?>
					<h2>Skier Detail</h2>
					<?php
					// get the racers
					$query = 'SELECT 
								FirstName AS "First Name",
								LastName AS "Last Name",
								BirthYear AS "Birth Year",
								HomeTown AS "Home Town",
								HomeState AS "Home State",
								Gender AS "Gender",
								(SELECT COUNT(*) FROM Result WHERE Result.RacerID='.$RacerID.') as Results
							FROM Racer WHERE RacerID = '.$RacerID;

					$result = RaceResultsQuery($query);
					$data = $result->fetch_assoc();
					$gender = $data["Gender"];
					$FN = $data["First Name"];
					$LN = $data["Last Name"];

					$result = RaceResultsQuery($query);

					if ($result)
					{
						ResultToTable($result);
					}
					else
					{
						echo "Query did not work<br/>";
					}

					echo "<br>";

					$cutoff["M"] = GetEliteWaveCutoff($birkie_id, "M");
					$cutoff["F"] = GetEliteWaveCutoff($birkie_id, "F");
					?>
					<h2>Predictions</h2>
					<?php

					$q = "SELECT EventView.EventDate as \"Event Date\",
								EventView.FullName as \"Event Name\",
								EventView.EventID,
								Event.DistanceInKM,
								SEC_TO_TIME(Result.TimeInSec) as Time,
								Result.TimeInSec as TimeInSec
							FROM Event, Racer, Result, EventView
							WHERE Racer.RacerID=Result.RacerID AND
								Result.EventID=Event.EventID AND
								Racer.RacerID=$RacerID AND
								EventView.EventID=Event.EventID AND
								EventView.EventDate >= \"$event_date_limit\"
							ORDER BY Event.EventDate DESC";
					$result = RaceResultsQuery($q);
					echo "<table>";
					echo "<th><b>Event</b></th><th><b>Event Place</b></th><th><b>Predicted ".$birkie_year." Birkie Time</b></th><th><b>Predicted Overall Place</b></th><th><b>Predicted Gender Place</b></th><th><b>Wave</b></th><th><b>Prediction Strength (0-100)</b></th><th><b>Elite Wave Beaten</b></th>";
					while ($row = $result->fetch_assoc())
					{
						$raceid = $row["EventID"];
						$pred = LinearRegressionRaceCompare($raceid, $birkie_id, $pb_limit, $RacerID);
						
						$strength = $pred["compared"];
						if ($row["DistanceInKM"] < 10)
						{
							// under 10k is half strength
							$strength /= 2;
						}
						else if ($row["DistanceInKM"] < 25)
						{
							// no modifier for 10-25k
						}
						else if ($row["DistanceInKM"] < 42)
						{
							// between 25 and 42k 1.5x
							$strength *= 1.5;
						}
						else
						{
							// marathon distance 2x
							$strength *= 2;
						}
						if ($strength > 100)
						{
							$strength = 100;
						}
						
						if ($strength >= $strength_limit)
						{
							echo "<tr>";
							
							echo "<td><a href=\"viewresults.php?eid=".$raceid."\">".$row["Event Name"]."</a></td>";
							
							// column for original race place
							$original_time = $row["TimeInSec"];
							$original_pq = "SELECT * FROM Result, Racer WHERE EventID=$raceid AND Racer.RacerID=Result.RacerID AND TimeInSec<$original_time";
							$original_r = RaceResultsQuery($original_pq);
							$original_p = $original_r->num_rows+1;
							echo "<td>$original_p</td>";
							
							$time = gmdate("H:i:s",$pred["prediction"]);
							echo "<td><a href=\"compareraces.php?eid1=$raceid&eid2=$birkie_id&FN=$FN&LN=$LN\">$time</a></td>";
							
							$timeinsec = $pred["prediction"];
							$oapq = "SELECT * FROM Result, Racer WHERE EventID=$birkie_id AND Racer.RacerID=Result.RacerID AND TimeInSec<$timeinsec";
							$oapr = RaceResultsQuery($oapq);
							$oap = $oapr->num_rows+1;
							echo "<td>$oap</td>";
							
							$gpq = "SELECT * FROM Result, Racer WHERE EventID=$birkie_id AND Racer.RacerID=Result.RacerID AND Gender=\"$gender\" AND TimeInSec<$timeinsec";
							$gpr = RaceResultsQuery($gpq);
							$gp = $gpr->num_rows+1;
							echo "<td>$gp</td>";
							
							echo "<td>";
							if ($gp <= $elite[$gender])
							{
								echo "Elite Wave";
							}
							else
							{
								$i = 0;
								while ($pred["prediction"] > $waves[$i]["time"])
								{
									$i++;
								}
								echo $waves[$i]["name"];
							}
							echo "</td>";

							echo "<td>".number_format($strength)."</td>";
							
							// insert number of elite wave beaten here
							$racer_time = $row["TimeInSec"];
							$beaten_q = "SELECT Racer.*, br.*, er.*
								FROM
									Racer, Result br, Result er 
								WHERE
									Racer.RacerID=br.RacerID AND
									br.RacerID=er.RacerID AND
									br.EventID=$birkie_id AND
									er.EventID=$raceid AND
									br.TimeInSec <= $cutoff[$gender] AND
									er.TimeInSec > $racer_time AND
									Racer.Gender = \"$gender\"";
							$beaten_result = RaceResultsQuery($beaten_q);
							$beaten_count = $beaten_result->num_rows;
							echo "<td>".$beaten_count."</td>";
			
							echo "</tr>";
						}
					}
					echo "</table>";
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
