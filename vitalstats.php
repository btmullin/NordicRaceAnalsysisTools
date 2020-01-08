<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Vital statistics based on the data in nordic race analysis.">
	<meta name="author" content="Ben Mullin">
    <title>Nordic Race Analysis Tools</title>
    
    <link rel="stylesheet" href="https://unpkg.com/purecss@1.0.0/build/pure-min.css" integrity="sha384-" crossorigin="anonymous">
	<link rel="stylesheet" href="css/layouts/side-menu.css">
	<link rel="stylesheet" href="css/nratstyle.css">

</head>

<body id="nrat-vitalstats">

<div id="layout">
	<!-- /* Menu here */ -->
	<?php include "nratnav.php"; ?>

	<div id="main">
		<div class="header">
			<h1>Nordic Race Analysis Tools</h1>
			<h2>Vital Stats</h2>
		</div>

		<div class="container">
			<div class="content">
				<?php
				// include the utilities functions
				require_once 'php/raceresultsutilities.php';

				LogActivity();

				?>
				<h2>Vital Statistics</h2>
				<?php
				// # of races
				$query = 'SELECT 
							COUNT(EventID) as NumRaces
						FROM Event';

				$result = RaceResultsQuery($query);
				$data = $result->fetch_assoc();
				$NumRaces = $data["NumRaces"];
				
				echo "<p>Races: ".number_format($NumRaces)."</p>";
				
				$query = 'SELECT
							COUNT(RacerID) as NumRacers
						FROM Racer WHERE EXISTS
						(SELECT * FROM Result WHERE Result.RacerID=Racer.RacerID)';
				$result = RaceResultsQuery($query);
				$data = $result->fetch_assoc();
				$NumRacers = $data["NumRacers"];

				echo "<p>Racers: ".number_format($NumRacers)."</p>";
				
				$query = 'SELECT COUNT(id) as NumResults FROM Result';
				
				$result = RaceResultsQuery($query);
				$data = $result->fetch_assoc();
				$NumResults = $data["NumResults"];

				echo "<p>Results: ".number_format($NumResults)."</p>";
								
				$query = 'SELECT
							SUM(Event.DistanceInKM) as KMSkied
						FROM Result, Event WHERE Result.EventID=Event.EventID';
				
				$result = RaceResultsQuery($query);
				$data = $result->fetch_assoc();
				$KMSkied = $data["KMSkied"];

				echo "<p>Total KM Skied: ".number_format($KMSkied)."</p>";
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
