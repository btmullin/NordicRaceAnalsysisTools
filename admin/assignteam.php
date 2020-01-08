<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Detailed view of the results of an individual skier.">
	<meta name="author" content="Ben Mullin">
    <title>Nordic Race Analysis Tools</title>
    
    <link rel="stylesheet" href="https://unpkg.com/purecss@1.0.0/build/pure-min.css" integrity="sha384-" crossorigin="anonymous">
	<link rel="stylesheet" href="../css/layouts/side-menu.css">
	<link rel="stylesheet" href="../css/nratstyle.css">

</head>
<?php
include '../php/raceresultsutilities.php';
// If this isn't a post, then we should display the form first
if (!isset($_POST['submit']))
{
	// This isn't a the initial submit or confirm so we should display the form
	// to select the racers
?>
	<form action="assignteam.php" method="post">
	Racer: <select name='racer_id'>
		<?php
			$result = RaceResultsQuery('SELECT * FROM Racer WHERE RacerID=PrimaryRacerID ORDER BY FirstName, LastName');
			while ($row = $result->fetch_array())
			{
				$id = $row["RacerID"];
				$fn = $row["FirstName"];
				$ln = $row["LastName"];
				echo "<option value=".$id.">".$fn." ".$ln."</option>";
			}
		?>
	</select><br>
	Team: <select name='team_id'>
		<?php
			$result = RaceResultsQuery('SELECT * FROM Team ORDER BY Name');
			while ($row = $result->fetch_array())
			{
				$id = $row["TeamID"];
				$name = $row["Name"];
				echo "<option value=".$id.">".$name."</option>";
			}
		?>
	</select><br>
	<input type="submit" value="Assign Team" name="submit">
	</form>
<?php
}
else
{
	// The user has assigned a team
	$racer_id = $_POST["racer_id"];
	$team_id = $_POST["team_id"];
	
	// Assign
	$result = RaceResultsQuery("INSERT INTO Affiliation (RacerID, TeamID) VALUES ($racer_id, $team_id)");
	
	// Show Team
	$result = RaceResultsQuery("SELECT Name FROM Team WHERE TeamID=$team_id");
	$row = $result->fetch_array();
	$team_name = $row["Name"];
	echo "<b>Team: $team_name</b><br>";
	
	$result = RaceResultsQuery("SELECT FirstName, LastName FROM Racer, Affiliation WHERE Racer.RacerID=Affiliation.RacerID AND Affiliation.TeamID=$team_id");
	ResultToTable($result);
}
?>