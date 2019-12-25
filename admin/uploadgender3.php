<?php
// The third page of updating genders
//
// Page 3 we check the selected fields for validity and then import the data
//
// This was copied straight from upload results and then the acutal storing
// of results was deleted because this already had updating genders.
// basically I created this because I had not included gender when uploading
// the 2018 Birkie results originally and that throws off the predictor
// because it can't do the elite wave stuff

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
if (!in_array("Gender", $_POST))
{
	die("Must have a gender field");
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
			echo "$field_name - $fv<br>";
			foreach ($RacerFields as $field => &$value)
			{
				if ($field == $_POST[$field_name])
				{
					$value[1] = $i;
				}
			}
			foreach ($ResultFields as $field => &$value)
			{
				if ($field == $_POST[$field_name])
				{
					$value[1] = $i;
				}
			}
			foreach ($CalculatedFields as $field => &$value)
			{
				if ($field == $_POST[$field_name])
				{
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
				
			if (($racer["Gender"] == null || $racer["Gender"] == " ") && $gender != null)
				{
					$q = "UPDATE Racer SET Gender='".$gender."' WHERE RacerID=".$racer_id;
					$mysqli->query($q);
				}
				
			}
		} while (($data = fgetcsv($handle, 1000, ",")) !== FALSE);
		
		echo "Done!<br><br>";
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