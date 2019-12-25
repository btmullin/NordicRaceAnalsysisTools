<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Detailed view of the results of an individual skier.">
	<meta name="author" content="Ben Mullin">
    <title>Nordic Race Analysis Tools</title>
    
    <link rel="stylesheet" href="https://unpkg.com/purecss@1.0.0/build/pure-min.css" integrity="sha384-" crossorigin="anonymous">
	<link rel="stylesheet" href="css/layouts/side-menu.css">
	<link rel="stylesheet" href="css/nratstyle.css">

</head>


<body id="nrat-viewskier">

<div id="layout">
	<!-- /* Menu here */ -->
	<?php include "nratnav.php"; ?>

	<div id="main">
		<div class="header">
			<h1>Nordic Race Analysis Tools</h1>
			<h2>View Skier Results</h2>
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
						Select a skier to view all of the results that have been
						logged for them. Either use the drop down or type the
						first and last name. If you type the name, you will have
						to get it exactly right.
					</p>
					<form class=" pure-form pure-form-aligned" action="viewskier.php" method="post">
					<div class="pure-control-group">
						<label for="racer">Racer</label>
						<select id="racer" name='id'>
							<?php
							$mysqli = OpenRaceResultsDatabase();
							$result = $mysqli->query('SELECT * FROM Racer Where RacerID=PrimaryRacerID ORDER BY FirstName, LastName');
							echo "<option value=null>Select Racer</option>";
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
					<div>Or</div>
					<br>
					<div class="pure-control-group">
						<label for="racer">Name</label>
						<input id="racer" type="text" name="FN" placeholder="First Name"> <input type="text" name="LN" placeholder="Last Name">
					</div>
					
					<div class="pure-controls">
						<input type="submit" value="View Racer" name="submit">
					</div>
					</form>
				<?php
				}
				else
				{
					$racertable="Racer";
					$mysqli = OpenRaceResultsDatabase();

					if ($_SERVER["REQUEST_METHOD"] == "POST") {
						if ($_REQUEST["id"] != "null")
						{
							$RacerID = $_POST["id"];
							echo "ID ".$RacerID;
						}
						else
						{
							$FN = $_REQUEST["FN"];
							$LN = $_REQUEST["LN"];
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
							}
							else
							{
								die("Sorry, your entry needs to match a racer exactly. I'll try to make this work better eventually...");
							}
						}
					}
					else if (isset($_REQUEST["rid"]))
					{
						$RacerID = $_REQUEST["rid"];
					}
					else
					{
						die("No Racer Selected");
					}
					
					if (isset($_REQUEST["filter"]))
					{
						$filter = $_REQUEST["filter"];
					}
					else
					{
						$filter = "";
					}
					
					LogActivity([$RacerID]);

					?>
					<h2>Racer Details</h2>
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
							FROM '.$racertable.' WHERE RacerID = '.$RacerID;

					$result = $mysqli->query($query);

					if ($result)
					{
						ResultToTable($result);
					}
					else
					{
						echo "Query did not work<br/>";
					}

					echo "<br>";
					echo "Racer ID: $RacerID<br>";

					echo "<a href=\"birkiepredictor.php?rid=$RacerID\">Birkie Predictor</a>";

					?>
					<h2>Race Results</h2>
					<?php

					// Display the selected racer's results
					$q = "SELECT EventView.EventDate as \"Event Date\",
								EventView.FullName as \"Event Name\",
								EventView.EventID,
								SEC_TO_TIME(Result.TimeInSec) as Time,
								FORMAT(((TimeInSec - (SELECT MIN(TimeInSec) FROM Result as R1 WHERE R1.EventID=Event.EventID))/
											  (SELECT MIN(TimeInSec) FROM Result as R2 WHERE R2.EventID=Event.EventID)*100),1) as \"Percent Back\",
								((SELECT COUNT(*) FROM Result as R3 WHERE R3.EventID=Event.EventID AND TimeInSec<Result.TimeInSec)+1) as \"Overall Place\"
								FROM Event, Racer, Result, EventView
								WHERE Racer.RacerID=Result.RacerID AND
									Result.EventID=Event.EventID AND
									Racer.RacerID=$RacerID AND
									EventView.EventID=Event.EventID AND
									EventView.FullName LIKE '%$filter%'
								ORDER BY Event.EventDate";
					$result = $mysqli->query($q);
					ResultToTable($result);

					echo "<br><br>";
					echo "<img src=\"php/racerpercentbacktrendgraph.php?rid=$RacerID\"><br><br>";
					echo "<img src=\"php/graphracertechniquedistpie.php?rid=$RacerID\"><br><br>";
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
