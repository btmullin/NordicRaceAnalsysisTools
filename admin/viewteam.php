<?php
include '../php/raceresultsutilities.php';
// If this isn't a post, then we should display the form first
if (!isset($_POST['submit']) AND !isset($_REQUEST['team_id']))
{
	// This isn't a the initial submit or confirm so we should display the form
	// select the team
?>
	<form action="viewteam.php" method="post">
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
	<input type="submit" value="View Team" name="submit">
	</form><br>
	
	<?php
	// Make a table of teams and number of members
	echo "<table>";
	//header
	echo "<tr>";
	$data = RaceResultsQuery("select Team.TeamID, Name, count(Affiliation.TeamID) as \"Members\" FROM Team, Affiliation WHERE Team.TeamID=Affiliation.TeamID GROUP By Affiliation.TeamID");
	$header = $data->fetch_fields();
	foreach ($header as $col)
	{
		if ($col->name != "TeamID")
		{
			echo "<th><b>$col->name</b></th>";
		}
	}
	echo "</tr>";
	// body
	$lastdate = null;
	while ($row = $data->fetch_assoc())
	{
		echo "<tr>";
		// if it exists, grab the event and racer ids
		$team_id = $row["TeamID"];
		$team_name = $row["Name"];
		$count = $row["Members"];
		echo "<td><a href=\"viewteam.php?team_id=".$team_id."\">$team_name</a></td>";
		echo "<td>$count</td>";
		echo "</tr>";
	}
	echo "</table>";
	?>
<?php
}
else
{
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		// The user has assigned a team
		$team_id = $_POST["team_id"];
	}
	else
	{
		$team_id = $_REQUEST["team_id"];
	}
	
	// Show Team
	$result = RaceResultsQuery("SELECT Name FROM Team WHERE TeamID=$team_id");
	$row = $result->fetch_array();
	$team_name = $row["Name"];
	echo "<b>Team: $team_name</b><br>";
	
	$result = RaceResultsQuery("SELECT FirstName, LastName FROM Racer, Affiliation WHERE Racer.RacerID=Affiliation.RacerID AND Affiliation.TeamID=$team_id");
	ResultToTable($result);
}
?>