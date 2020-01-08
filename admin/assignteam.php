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
			$result = RaceResultsQuery('SELECT * FROM Racer ORDER BY FirstName, LastName');
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
	echo "Duplicate ID: $dup_id<br>Primary ID: $prim_id<br><br>";
	
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