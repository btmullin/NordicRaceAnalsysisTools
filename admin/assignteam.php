<?php
include '../php/raceresultsutilities.php';
// If this isn't a post, then we should display the form first
if (!isset($_POST['confirm']) AND
	!isset($_POST['submit']))
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
else if (isset($_POST['submit']))
{
	// The user selected the racers to duplicate, pull up the changes being
	// proposed and ask for confirmation before making the changes
?>
	<form action="duplicateracer.php" method="post">
	<?php
		$dup_id = $_POST["dup_id"];
		$prim_id = $_POST["prim_id"];
		echo "<input type=\"hidden\" name=\"dup_id\" value=\"$dup_id\">";
		echo "<input type=\"hidden\" name=\"prim_id\" value=\"$prim_id\">";
		$mysqli = OpenRaceResultsDatabase();
		$result = $mysqli->query("SELECT * FROM Racer WHERE RacerID=$dup_id");
		$row = $result->fetch_array();
		$dup_name = $row["FirstName"]." ".$row["LastName"];
		$result = $mysqli->query("SELECT * FROM Racer WHERE RacerID=$prim_id");
		$row = $result->fetch_array();
		$prim_name = $row["FirstName"]." ".$row["LastName"];
		echo "Replacing $dup_name with $prim_name<br><br>";
		echo "These results:<br>";
		$result = $mysqli->query("SELECT * FROM Result WHERE RacerID=$dup_id");
		ResultToTable($result);
		echo "<br>";
		echo "Will be added to these results:<br>";
		$result = $mysqli->query("SELECT * FROM Result WHERE RacerID=$prim_id");
		ResultToTable($result);
		echo "<br>";
	?>
	<input type="submit" value="Are You Sure?" name="confirm">
	</form>
<?php
}
else
{
	// The user has confirmed the changes
	$dup_id = $_POST["dup_id"];
	$prim_id = $_POST["prim_id"];
	echo "Duplicate ID: $dup_id<br>Primary ID: $prim_id<br><br>";
	
	$mysqli = OpenRaceResultsDatabase();
	$result = $mysqli->query("SELECT Result.*, EventView.FullName FROM Result, EventView WHERE Result.RacerID=$dup_id AND Result.EventID=EventView.EventID");
	echo "Updating results for the following events:<br>";
	ResultToTable($result);
	
	// Make the updates
	$result = $mysqli->query("UPDATE Racer SET PrimaryRacerID=$prim_id WHERE RacerID=$dup_id");
	$result = $mysqli->query("UPDATE Result SET RacerID=$prim_id WHERE RacerID=$dup_id");
	
	echo "<a href=\"../viewskier.php?rid=$prim_id\">See the updated results</a>";
	
	/*
	
	UPDATE Racer SET PrimaryRacerID=$prim_id WHERE RacerID=$dup_id;
	UPDATE Result SET RacerID=$prim_id WHERE RacerID=$dup_id;
	
	*/
}
?>