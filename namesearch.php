<?php
	// include the utilities functions
	require_once 'php/raceresultsutilities.php';

	$mysqli = OpenRaceResultsDatabase();
	
	if (isset($_GET['term']))
	{
		$return_arr = array();
	
		$term = $_GET['term'];
		$query = "SELECT * FROM Racer WHERE concat_ws(' ',FirstName,LastName) LIKE '%$term%' AND RacerID=PrimaryRacerID ORDER BY FirstName, LastName";
		$result = $mysqli->query($query);
		
		while($row = $result->fetch_assoc())
		{
			$return_arr[] = $row["FirstName"] . " " . $row["LastName"];
		}
	
		echo json_encode($return_arr);
	}
	else
	{
		echo "Term not set";
	}
?>