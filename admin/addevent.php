<?php
// Either display the form to enter event data or add the data that was entered
// from the previous form

include '../php/raceresultsutilities.php';
$mysqli = OpenRaceResultsDatabase();

if (!isset($_POST['submit']))
{
	// Display the form to enter data
?>
<html>
<body>

<form action="addevent.php" method="post">
Event Name: <input type="text" name="Name"><br>
Date: <input type="date" name="Date"><br>
Distance: <input type="number" name="Distance"><br>
Location: <input type="text" name="Location"><br>
Race Type: <select name="RaceType"><br>
<?php
	$result = $mysqli->query('SELECT * FROM RaceType');
	while ($row = $result->fetch_array())
	{
		$id = $row["RaceTypeID"];
		$name = $row["Name"];
		echo "<option value=".$id.">".$name."</option>";
	}
?>
</select><br>
Technique: <select name="Technique">
<?php
	$result = $mysqli->query('SELECT * FROM Technique');
	while ($row = $result->fetch_array())
	{
		$id = $row["TechniqueID"];
		$name = $row["Name"];
		echo "<option value=".$id.">".$name."</option>";
	}
?>
</select><br>
Hilly (1 - flat, 5 - very hilly): <input type="number" name="Hilly"><br>
Snow Firmness (1 - soft, 5 - hard): <input type="number" name="Firmness"><br>
Temp (F): <input type="number" name="Temp"><br>
<input type="submit" name="submit">
</form>

</body>
</html>
<?php
}
else
{
	// get the form data and clean input
	// define variables and set to empty values
	$EventName = $Date = $Location = "";
	$Distance = $Temp = $Firmness = $Hilly = $RaceType = 0;

	if ($_SERVER["REQUEST_METHOD"] == "POST") {
	  $EventName = test_input($_POST["Name"]);
	  $Date = test_input($_POST["Date"]);
	  $Location = test_input($_POST["Location"]);
	  $Distance = test_input($_POST["Distance"]);
	  $Temp = test_input($_POST["Temp"]);
	  $Firmness = test_input($_POST["Firmness"]);
	  $Hilly = test_input($_POST["Hilly"]);
	  $RaceType = test_input($_POST["RaceType"]);
	  $Technique = test_input($_POST["Technique"]);
	}

	// Test by outputting form data
	echo $EventName." ".$Location." ".$Date." ".$Distance." ".$Temp."F ".$Firmness." ".$Hilly." ".$RaceType." ".$Technique."<br/>";
	
	// Add the data to the event table
	$q = "INSERT INTO Event (Name, EventDate, DistanceInKM, Location, RaceType, Technique) VALUES
		  (\"$EventName\", \"$Date\", $Distance, \"$Location\", $RaceType, $Technique)";

	$result = $mysqli->query($q);
	$EventID = $mysqli->insert_id;
	
	if ($Hilly !== "")
	{
		$q = 'UPDATE Event SET Hilly="'.$Hilly.'" WHERE EventID='.$EventID;
		$result = $mysqli->query($q);
	}
	if ($Firmness !== "")
	{
		$q = 'UPDATE Event SET SnowFirmness="'.$Firmness.'" WHERE EventID='.$EventID;
		$result = $mysqli->query($q);
	}
	if ($Temp !== "")
	{
		$q = 'UPDATE Event SET TempInDegreeF="'.$Temp.'" WHERE EventID='.$EventID;
		$result = $mysqli->query($q);
	}



	if($result)
	{
		echo "Event Added<br/>";
	}
	else
	{
		echo "Failed insert<br/>";
	}
	
	echo "<a href=\"../viewevents.php\">View All Events</a><br><br>";
	echo "<a href=\"uploadfullresults1.php?eid=$EventID\">Upload Results</a><br><br>";
}

function test_input($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}
?>