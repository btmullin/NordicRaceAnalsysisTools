<?php
// The first page of uploading results
//
// Page 3 we check the selected fields for validity and then import the data

include '../php/raceresultsutilities.php';
include_once 'uploadresultsfields.php';

session_start();

$ResultFile = $_SESSION["ResultFile"];
$EventID = $_SESSION["EventID"];
$HeaderRow = $_POST["headers"];
					  
?>
					  
<html>
<body>

<?php

// Validate the fields selected
if (!in_array("FirstName", $_POST))
{
	die("Must have a First Name field");
}
if (!in_array("LastName", $_POST))
{
	die("Must have a Last Name field");
}
if (!in_array("Time", $_POST) AND !in_array("TimeInSec", $_POST))
{
	die("Must have a Time field");
}
					  
// Be sure a file was uploaded
if (file_exists($ResultFile))
{
	//Import uploaded file to Database

	// Open the file
	$handle = fopen($ResultFile, "r");

	if ($handle)
	{
		// Get the first row so we can know how many columsn were in the data
		// if we had a header row, we are going to throw this one a way,
		// otherewise move on and process it
		$data = fgetcsv($handle, 10000, ",");
		
		// Determine which columsn go with which fields
		$field_count = count($data);
		for ($i = 0; $i < $field_count; $i++)
		{
			$field_name = "field$i";
			$fv = $_POST[$field_name];
			foreach ($RacerFields as $field => &$value)
			{
				if ($field == $_POST[$field_name])
				{
					echo "$field_name - $fv - $i<br>";
					$value[1] = $i;
				}
			}
			foreach ($ResultFields as $field => &$value)
			{
				if ($field == $_POST[$field_name])
				{
					echo "$field_name - $fv - $i<br>";
					$value[1] = $i;
				}
			}
			foreach ($CalculatedFields as $field => &$value)
			{
				if ($field == $_POST[$field_name])
				{
					echo "$field_name - $fv - $i<br>";
					$value[1] = $i;
				}
			}
		}
		
		// Open the database
		$mysqli = OpenRaceResultsDatabase();
		
		if ($HeaderRow)
		{
			// the first row is a header so skip it
			$data = fgetcsv($handle, 1000, ",");
		}
		
		$racers_added = 0;
		$results_added = 0;

		// Go through the remaining rows of data
		do
		{
			// Get the racer information
			$FN = trim($data[$RacerFields["FirstName"][1]]);
			$LN = trim($data[$RacerFields["LastName"][1]]);

			$q = "SELECT * FROM Racer WHERE Racer.FirstName=\"$FN\" AND Racer.LastName=\"$LN\"";
			$in_db = $mysqli->query($q);

			$racer_id = 0;
			if ($in_db->num_rows > 0)
			{
				// The racer was found, grab the primary ID
				$racer = $in_db->fetch_array();
				$racer_id = $racer["PrimaryRacerID"];
				
				// If we know the gender, and the information in the DB is null
				// update it
				$gender = null;
				foreach ($RacerFields as $field_name => $field)
				{
					if ($field[1] !== NULL)
					{
						if ($field_name == "Gender")
						{
							$gender = substr($data[$field[1]],0,1);
						}
					}
				}
				// Get the primary racer info
				$q = "SELECT * FROM Racer WHERE RacerID=".$racer_id;
				$result = $mysqli->query($q);
				$racer = $result->fetch_array();
				
				if ($racer["Gender"] == null && $gender != null)
				{
					$q = "UPDATE Racer SET Gender='".$gender."' WHERE RacerID=".$racer_id;
					$mysqli->query($q);
				}
				
			}
			else
			{
				// Racer does not exist, insert them into the database
				$cols = "";
				$vals = "";
				foreach ($RacerFields as $field_name => $field)
				{
					if ($field[1] !== NULL)
					{
						if ($cols != "")
						{
							$cols .= ",";
							$vals .= ",";
						}
						$cols .= $field_name;
						if ($field_name == "Gender")
						{
							$data[$field[1]] = substr($data[$field[1]],0,1);
						}
						// before inserting anything into the values, be sure to
						// escape it, in case it had an appostrophe in it
						$vals .= "'".$mysqli->real_escape_string(trim($data[$field[1]]))."'";
					}
				}
				if ($CalculatedFields["Age"][1] !== NULL)
				{
					$cols .= ",BirthYear";
					// get date of event for use in birth year calculation
					$eventdata = $mysqli->query("SELECT YEAR(EventDate) FROM Event WHERE EventID=$EventID");
					$eventyear = $eventdata->fetch_array()[0] - 1;
					$by = $eventyear - $data[$CalculatedFields["Age"][1]];
					$vals .= ",'".$by."'";
				}
				$q = "INSERT INTO Racer ($cols) VALUES ($vals)";
				$success = $mysqli->query($q);
				if ($success)
				{
					$racer_id = $mysqli->insert_id;
					$q = "UPDATE Racer SET Racer.PrimaryRacerID=$racer_id WHERE Racer.RacerID=$racer_id";
					$mysqli->query($q);
					
					echo "Added $FN $LN<br>";
					$racers_added = $racers_added+1;
				}
				else
				{
					echo "Failed to insert a racer ($cols) ($vals)<br>";
				}
			}

			// Get the result information
			$cols = "";
			$vals = "";
			foreach ($ResultFields as $field_name => $field)
			{
				if ($field[1] !== NULL)
				{
					if ($cols != "")
					{
						$cols .= ",";
						$vals .= ",";
					}
					$cols .= $field_name;
					$vals .= "'".$data[$field[1]]."'";
				}
			}
			if ($CalculatedFields["Time"][1] !== NULL)
			{
				if ($cols != "")
				{
					$cols .= ",";
					$vals .= ",";
				}
				$cols .= "TimeInSec";
				// Calculate the time in seconds from HH:MM:SS or MM:SS
				$time_str = $data[$CalculatedFields["Time"][1]];
				sscanf($time_str, "%d:%d:%d", $hours, $minutes, $seconds);
				$time_seconds = isset($seconds) ? $hours * 3600 + $minutes * 60 + $seconds : $hours * 60 + $minutes;
				$vals .= "'".$time_seconds."'";
			}
			// add racer id
			$cols .= ",RacerID";
			$vals .= ",'".$racer_id."'";
			// add event id
			$cols .= ",EventID";
			$vals .= ",'".$EventID."'";
			
			// build result insert query
			$q = "INSERT INTO Result ($cols) VALUES ($vals)";
			// Enter the result
			$success = $mysqli->query($q);
			if (! $success)
			{
				echo "Failed to insert a racer ($cols) ($vals)<br>";
			}
			$results_added = $results_added+1;
		} while (($data = fgetcsv($handle, 1000, ",")) !== FALSE);
		
		echo "Done!<br><br>";
		echo "$results_added Results Added<br>";
		echo "<a href=\"../viewresults.php?eid=$EventID\">Results Available Here</a>";
	}
}
else
{
	echo "You must select a file<br>";
	var_dump($Files);
}

// foreach ($array as $key => $value)
?>

</body>
</html>

<?php

function buildfieldoptions($racerfields, $resultfields, $calculatedfields)
{
	// First is the "no data" option
	$options = "<option value=\"Nothing\"></option>";
	foreach ($racerfields as $racerkey => $racerfield)
	{
		$options .= "<option value=$racerkey>$racerfield[0]</option>";
	}
	foreach ($resultfields as $resultkey => $resultfield)
	{
		$options .= "<option value=$resultkey>$resultfield[0]</option>";
	}
	foreach ($calculatedfields as $calculatedkey => $calculatedfield)
	{
		$options .= "<option value=$calculatedkey>$calculatedfield[0]</option>";
	}
	return $options;
}

?>