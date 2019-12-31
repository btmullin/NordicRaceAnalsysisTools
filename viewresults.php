<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="View race results.">
    <title>Nordic Race Analysis Tools</title>
    
    <link rel="stylesheet" href="https://unpkg.com/purecss@1.0.0/build/pure-min.css" integrity="sha384-" crossorigin="anonymous">
	<link rel="stylesheet" href="css/layouts/side-menu.css">
	<link rel="stylesheet" href="css/nratstyle.css">

</head>


<body id="nrat-viewresults">

<div id="layout">
	<!-- /* Menu here */ -->
	<?php include "nratnav.php"; ?>

	<div id="main">
		
		<div class="header">
			<h1>Nordic Race Analysis Tools</h1>
			<h2>View Event Results</h2>
		</div>

		<div class="container">
			<div class="content">
				<?php
				// include the utilities functions
				require_once 'php/raceresultsutilities.php';
				// If this isn't a post, then we should display the form first
				if (!isset($_POST['submit']) AND !isset($_REQUEST['eid']))
				{
					// This isn't a post so we should display the form
					LogActivity(["Selection"]);
				?>
					<p>
						Select an event to view the results.
					</p>
					<form class=" pure-form pure-form-aligned" action="viewresults.php" method="post">
					
					<div class="pure-control-group">
						<label for="race">Event</label>
						<select id="race" name='id'>
							<?php
								$result = RaceResultsQuery('SELECT * FROM EventView');
								while ($row = $result->fetch_array())
								{
									$id = $row["EventID"];
									$EventName = $row["FullName"];
									echo "<option value=".$id.">".$EventName."</option>";
								}
							?>
						</select><br>
					</div>
					<div class="pure-controls">
						<input type="submit" value="View Results" name="submit">
					</div>
					</form>
				<?php
				}
				else
				{
					// This is a post or a link with an argument so we should display the
					// selected results
					if (isset($_POST['submit']))
					{
						$EventID = $_POST["id"];
					}
					else
					{
						$EventID = $_REQUEST["eid"];
					}
					
					LogActivity([$EventID]);
					
					$result = RaceResultsQuery("SELECT FullName FROM EventView WHERE EventID=$EventID");
					$row = $result->fetch_array();
					$EventName = $row["FullName"];
					echo "<h2>$EventName</h2>";
					$result = RaceResultsQuery("SELECT EventDate, Location, Source FROM EventView WHERE EventID=$EventID");
					ResultToTable($result);
					echo "Event ID: $EventID<br>";
					echo "<a href=\"#graphs\">Jump To Graphs</a><br><br>";
					raceresultstable($EventID);
					
					echo "<br><br>";
					echo "<a name=\"graphs\">";
					echo "<img class=\"pure-img\" src=\"php/timevsplacebarchart.php?eid=$EventID\"><br><br>";
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
