<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Compare two skiers.">
	<meta name="author" content="Ben Mullin">
    <title>Nordic Race Analysis Tools</title>
    
    <link rel="stylesheet" href="https://unpkg.com/purecss@1.0.0/build/pure-min.css" integrity="sha384-" crossorigin="anonymous">
	<link rel="stylesheet" href="css/layouts/side-menu.css">
	<link rel="stylesheet" href="css/nratstyle.css">

</head>


<body id="nrat-compareskiers">

<div id="layout">
	<!-- /* Menu here */ -->
	<?php include "nratnav.php"; ?>

	<div id="main">
		<div class="header">
			<h1>Nordic Race Analysis Tools</h1>
			<h2>Compare Skiers</h2>
		</div>

		<div class="container">
			<div class="content">
				<?php
				// include the utilities functions
				require_once 'php/raceresultsutilities.php';
				// If this isn't a post, then we should display the form first
				if (!isset($_POST['submit']) AND !(isset($_REQUEST['rid1']) and isset($_REQUEST['rid2'])))
				{
					// This isn't a post so we should display the form
					
					LogActivity(["Selection"]);
				?>
					<h2>Instructions</h2>
					<p>
						This tool will compare two skiers results.  This is useful to see
						how you have been skiing relative to your buddies previously.  Perhaps
						you can use this tool to identify folks you might want to try to stick
						with or maybe people you want to be sure you don't stick with at an
						upcoming race.
					</p>
					
					<h2>Skier Selection</h2>
					<form action="compareskiers.php" method="post">

					<div class="pure-control-group">
						<label for="racer1">Racer 1</label>
						<select id="racer1" name='id1'>
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
						</select>
					</div>

					<div class="pure-control-group">
						<label for="racer2">Racer 2</label>
						<select id="racer2" name='id2'>
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
						</select>
					</div>

					<div class="pure-controls">
						<input type="submit" value="Compare Skiers" name="submit">
					</div>

					</form>
				<?php
				}
				else
				{
					if ($_SERVER["REQUEST_METHOD"] == "POST") {
					  $RacerID1 = $_POST["id1"];
					  $RacerID2 = $_POST["id2"];
					}
					else if (isset($_REQUEST["rid1"]) AND isset($_REQUEST["rid2"]))
					{
						$RacerID1 = $_REQUEST["rid1"];
						$RacerID2 = $_REQUEST["rid2"];
					}
					else
					{
						die("No Racer Selected");
					}
					
					LogActivity([$RacerID1, $RacerID2]);

					?>
					<h2>Skier Details</h2>
					<?php
					// get the racers
					$query = 'SELECT 
								RacerID,
								FirstName AS "First Name",
								LastName AS "Last Name",
								BirthYear AS "Birth Year",
								HomeTown AS "Home Town",
								HomeState AS "Home State",
								Gender AS "Gender"
							FROM Racer WHERE RacerID = '.$RacerID1.' OR RacerID = '.$RacerID2;

					$result = RaceResultsQuery($query);

					if ($result)
					{
						ResultToTable($result);
					}
					else
					{
						echo "Query did not work<br/>";
					}
					
					// this time grab the skier names for titles in the next table
					$result = RaceResultsQuery($query);
					while ($data = $result->fetch_assoc())
					{
						$name = $data["First Name"]." ".$data["Last Name"];
						if ($data["RacerID"] == $RacerID1)
						{
							$RacerName1 = $name;
						}
						else
						{
							$RacerName2 = $name;
						}
					}

					echo "<br>";
					?>
					<h2>Skier Comparisons </h2>
					<?php

					$q = "SELECT 	EventView.FullName as \"Event Name\",
									EventView.EventID,
									Result1.TimeInSec as \"$RacerName1\",
									Result2.TimeInSec as \"$RacerName2\"
								FROM EventView, Result as Result1, Result as Result2
								WHERE 	Result1.EventID=Result2.EventID AND
										EventView.EventID=Result1.EventID AND
										Result1.RacerID=$RacerID1 AND
										Result2.RacerID=$RacerID2";
										
					$result = RaceResultsQuery($q);
					if ($result)
					{
						$header = $result->fetch_fields();
						// hacking a new column into the result so copy one header to modify it
						$header[] = clone $header[0];
						$header[count($header)-1]->name = "Percent Back";
						for ($set = array (); $row = $result->fetch_assoc(); $set[] = $row);
						
						// Add in percent back
						foreach ($set as &$s)
						{
							$pb = 0;
							if ($s["$RacerName1"] < $s["$RacerName2"])
							{
								$pb = -1* ($s["$RacerName2"]-$s["$RacerName1"])/($s["$RacerName1"]) * 100;
							}
							else
							{
								$pb = ($s["$RacerName1"]-$s["$RacerName2"])/($s["$RacerName2"]) * 100;
							}
							$s["Percent Back"] = number_format($pb,1)."%";
							
							$s["$RacerName1"] = gmdate("H:i:s", $s["$RacerName1"]);
							$s["$RacerName2"] = gmdate("H:i:s", $s["$RacerName2"]);
					   }

						ArrayToTable($header,$set);
					}
					else
					{
						die("Combined results query failed");
					}
					echo "<br><img src=\"php/racercomparisongraph.php?rid1=$RacerID1&rid2=$RacerID2\"><br><br>";
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
