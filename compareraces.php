<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="A layout example with a side menu that hides on mobile, just like the Pure website.">
    <title>Nordic Race Analysis Tools</title>
    
    <link rel="stylesheet" href="https://unpkg.com/purecss@1.0.0/build/pure-min.css" integrity="sha384-" crossorigin="anonymous">
	<link rel="stylesheet" href="css/layouts/side-menu.css">
	<link rel="stylesheet" href="css/nratstyle.css">

</head>


<body id="nrat-compareraces">

<div id="layout">
	<!-- /* Menu here */ -->
	<?php include "nratnav.php"; ?>

	<div id="main">
		<div class="header">
			<h1>Nordic Race Analysis Tools</h1>
			<h2>Compare Races</h2>
		</div>

		<div class="container">
			<div class="content">
				<?php
				include 'php/raceresultsutilities.php';
				// If this isn't a post, then we should display the form first
				if (!isset($_POST['submit'])AND !(isset($_REQUEST['eid1']) AND isset($_REQUEST['eid2'])))
				{
					// This isn't a post so we should display the form
					
					LogActivity(["Selection"]);
				?>
					<h2>Instructions</h2>
					<p>
						This tool will compare the first race against the second race.
						It uses common racers between the races to come up with a
						conversion between the races.
					</p>
					<p>
						Optionally enter the first and last name of a racer who did the
						first race and the tool will predict their finish time of the
						second race.  This can be used to either predict a time for a race
						that wasn't skied or it could be used to determine which performance
						was "better".
					</p>
					<p>
						The final parameter is the percent back limit.  This optional parameter
						will limit the tool to comparing common racers who were within the
						specified perecent back of the winner.  The general idea is that the
						closer to the pointy end of the race the more consistent the results.
					</p>
					
					<h2>Race Selection</h2>

					<form class=" pure-form pure-form-aligned" action="compareraces.php" method="post">
					
					<div class="pure-control-group">
						<label for="race1">Event 1</label>
						<select id="race1" name='id1'>
							<?php
								$mysqli = OpenRaceResultsDatabase();
								$result = $mysqli->query('SELECT * FROM EventView');
								while ($row = $result->fetch_array())
								{
									$id = $row["EventID"];
									$EventName = $row["FullName"];
									echo "<option value=".$id.">".$EventName."</option>";
								}
							?>
						</select>
					</div>
					
					<div class="pure-control-group">
						<label for="race2">Event 2</label>
						<select id="race2" name='id2'>
							<?php
								$result = $mysqli->query('SELECT * FROM EventView');
								while ($row = $result->fetch_array())
								{
									$id = $row["EventID"];
									$EventName = $row["FullName"];
									echo "<option value=".$id.">".$EventName."</option>";
								}
							?>
						</select>
					</div>
					
					<div class="pure-control-group">
						<label for="racer">Racer To Predict (Optional)</label>
						<input id="racer" type="text" name="FN" placeholder="First Name"> <input type="text" name="LN" placeholder="Last Name">
					</div>
					
					<div class="pure-control-group">
						<label for="back">Percent Back Limit (Optional)</label>
						<input id="back" type="number" name="PBLimit"> 25-50 typical (40 default)<br>
					</div>
					
					<div class="pure-controls">
						<input type="submit" value="Graph Common Results" name="submit">
					</div>
					</form>
				<?php
				ini_set('max_execution_time', 0);
				}
				else
				{
					// This is a post so we should display the selected results
					if (isset($_POST['submit']))
					{
						$EventID1 = $_POST["id1"];
						$EventID2 = $_POST["id2"];
						if ($_POST["PBLimit"] == "")
						{
							$PBLimit = 40;
						}
						else
						{
							$PBLimit = $_POST["PBLimit"];
						}
						
						$FN = $_POST["FN"];
						$LN = $_POST["LN"];
					}
					else
					{
						$EventID1 = $_REQUEST["eid1"];
						$EventID2 = $_REQUEST["eid2"];
						
						if (!isset($_POST["PBLimit"]) ||
						    $_REQUEST["PBLimit"] == "")
						{
							$PBLimit = 40;
						}
						else
						{
							$PBLimit = $_REQUEST["PBLimit"];
						}
						
						$FN = $_REQUEST["FN"];
						$LN = $_REQUEST["LN"];
					}
					
					$mysqli = OpenRaceResultsDatabase();

					// Get the event names
					$result = $mysqli->query("SELECT FullName FROM EventView WHERE EventID=$EventID1");
					$row = $result->fetch_array();
					$EventName1 = $row["FullName"];
					$result = $mysqli->query("SELECT FullName FROM EventView WHERE EventID=$EventID2");
					$row = $result->fetch_array();
					$EventName2 = $row["FullName"];

					// Get the winning times to use in calculating the percent back
					$result = $mysqli->query("SELECT MIN(TimeInSec) AS WinningTime From Result WHERE Result.EventID=$EventID1");
					$min_time1 = $result->fetch_assoc()["WinningTime"];
					$result = $mysqli->query("SELECT MIN(TimeInSec) AS WinningTime From Result WHERE Result.EventID=$EventID2");
					$min_time2 = $result->fetch_assoc()["WinningTime"];
					$min_time1_string = gmdate("H:i:s",$min_time1);
					$min_time2_string = gmdate("H:i:s",$min_time2);

					// Calculate the time limit based on percent back
					$limit1 = $min_time1*(1+$PBLimit/100);
					$limit2 = $min_time2*(1+$PBLimit/100);
					$limit1_string = gmdate("H:i:s",$limit1);
					$limit2_string = gmdate("H:i:s",$limit2);
					
					// Search for the racer specified
					$FirstRaceTime = 0;
					$FirstRaceTimeString = "No Result";
					$SecondRaceTimeString = "No Result";
					$RacerID = null;
					if ($FN !== "" OR $LN !== "")
					{
						$q = "SELECT FirstName, LastName, RacerID FROM Racer WHERE ";
						if ($FN !== "")
						{
							$q .= "FirstName=\"$FN\" ";
						}
						if (($FN !== "") AND ($LN !== ""))
						{
							$q .= "AND ";
						}
						if ($LN !== "")
						{
							$q .= "LastName=\"$LN\" ";
						}
						$result = $mysqli->query($q);
						$RacerMatches = $result->num_rows;
						if ($RacerMatches == 1)
						{
							$row = $result->fetch_assoc();
							$RacerID = $row["RacerID"];
							
							// Find the racers time in the events
							$q = "SELECT TimeInSec FROM Result WHERE EventID=$EventID1 AND RacerID=$RacerID";
							$result = $mysqli->query($q);
							if ($result->num_rows == 1)
							{
								$row = $result->fetch_assoc();
								$FirstRaceTime = $row["TimeInSec"];
								$FirstRaceTimeString = gmdate("H:i:s",$FirstRaceTime);
							}
							
							$q = "SELECT TimeInSec FROM Result WHERE EventID=$EventID2 AND RacerID=$RacerID";
							$result = $mysqli->query($q);
							if ($result->num_rows == 1)
							{
								$row = $result->fetch_assoc();
								$SecondRaceTime = $row["TimeInSec"];
								$SecondRaceTimeString = gmdate("H:i:s",$SecondRaceTime);
							}
						}
					}
					
					LogActivity([$EventID1, $EventID2, $PBLimit, $FN, $LN, $RacerID]);
					
					// btm - having some trouble with the following query losing connection to the database
					// going to try closing the connection and reopening it here
					$mysqli->close();
					$mysqli = OpenRaceResultsDatabase();

					// Do the linear regression on the common racers that are within the limit
					$q = "SELECT Racer.FirstName, Racer.LastName, r1.TimeInSec as \"Race 1 Time\", r2.TimeInSec as \"Race 2 Time\"
							FROM Racer, Result r1, Result r2
							WHERE r1.EventID=$EventID1 AND r2.EventID=$EventID2 AND r1.RacerID=r2.RacerID AND Racer.RacerID=r1.RacerID
							LIMIT 1000";
					
					// Find the results within the limit
					$result = $mysqli->query($q);
					if ($result === FALSE)
					{
						echo $mysqli->error;
						exit();
					}
					$x_inc = array();
					$y_inc = array();
					while ($row = $result->fetch_array())
					{
						if (($row["Race 1 Time"] <= $limit1) AND ($row["Race 2 Time"] <= $limit2))
						{
							$x_inc[] = $row["Race 1 Time"];
							$y_inc[] = $row["Race 2 Time"];
						}
					}	
					require_once( 'php/jpgraph/src/jpgraph_utils.inc.php');
					$linreg = new LinearRegression($x_inc, $y_inc);
					$ab = $linreg->GetAB();
					$m = $ab[1];
					$b = $ab[0];
					if ($FirstRaceTime != 0)
					{
						$PredTime = $m*$FirstRaceTime + $b;
						$Prediction = gmdate("H:i:s",$PredTime);
					}
					else
					{
						$Prediction = "Did Not Do First Race";
					}

					// Display relevant information about the races
					echo "<table border=\"1\"><tr><td></td>";
					echo "<td><b>$EventName1</td><td><b>$EventName2</td></tr>";
					echo "<tr><td>Winning Times</td><td align=\"center\">$min_time1_string</td><td align=\"center\">$min_time2_string</td></tr>";
					echo "<tr><td>Time Back Limits ($PBLimit%)</td><td align=\"center\">$limit1_string</td><td align=\"center\">$limit2_string</td></tr>";
					echo "<tr><td>$FN $LN</td><td align=\"center\">$FirstRaceTimeString</td><td align=\"center\">$SecondRaceTimeString</td></tr>";
					echo "<tr><td>Prediction</td><td></td><td align=\"center\">$Prediction</td>";
					echo "</table><br>";

					// Display the correlation graph
					echo "<img src=\"php/commonresultsgraph.php?e1=$EventID1&e2=$EventID2&pb=$PBLimit&rid=$RacerID\"><br><br>";

					// Make a table of the common racers
					// Highlight the areas where the result is > PBLimit
					$q = "SELECT Racer.FirstName, Racer.LastName, r1.TimeInSec as \"Race 1 Time\", r2.TimeInSec as \"Race 2 Time\"
							FROM Racer, Result r1, Result r2
							WHERE r1.EventID=$EventID1 AND r2.EventID=$EventID2 AND r1.RacerID=r2.RacerID AND Racer.RacerID=r1.RacerID
							ORDER BY r1.TimeInSec
							LIMIT 1000";
					$result = $mysqli->query($q);
					
					echo "<table border=\"1\">";
					//header
					echo "<tr>";
					echo "<td><b>First Name</b></td><td><b>Last Name</b></td>
							<td><b>$EventName1</b></td><td><b>$EventName2</b></td>";
					echo "</tr>";
					// body
					while ($field = $result->fetch_array())
					{
						echo "<tr>";
						// name
						echo "<td>$field[0]</td><td>$field[1]</td>";
						// times but colored
						for ($i = 2; $i < 4; $i++)
						{
							echo "<td align=\"center\" ";
							$over = ((($i==2) AND ($field[$i] > $limit1)) OR
									 (($i==3) AND ($field[$i] > $limit2)));
							if ($over)
							{
								echo "bgcolor=\"#FF7777\"";
							}
							$time = gmdate("H:i:s",$field[$i]);
							echo ">$time</td>";
						}
						echo "</tr>";
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
